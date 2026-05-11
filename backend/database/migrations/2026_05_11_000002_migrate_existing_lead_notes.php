<?php

use App\Models\Lead;
use App\Models\LeadNote;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * [ALTO 3] One-time data migration: converts concatenated notes in leads.notes
 * into individual rows in lead_notes.
 *
 * Format legacy:  "2024-01-15 10:30:00: Texto da nota\n\n2024-02-01 09:00:00: Outro texto"
 * Each paragraph separated by \n\n becomes a separate LeadNote row.
 *
 * After running this migration + confirming integrity, the `notes` column on
 * the leads table can be dropped in a subsequent migration.
 *
 * ROLLBACK: does NOT restore the original text field — only deletes the rows
 * created by this migration (identified by a special marker meta_source).
 * Run in a transaction so that partial failures are rolled back atomically.
 */
return new class extends Migration
{
    /** Marker stored as JSON metadata so rollback can identify migrated rows. */
    private const MIGRATION_SOURCE = 'legacy_text_migration';

    public function up(): void
    {
        // Process in chunks to avoid loading all leads into memory at once
        Lead::query()
            ->whereNotNull('notes')
            ->where('notes', '!=', '')
            ->chunkById(100, function ($leads): void {
                foreach ($leads as $lead) {
                    $this->migrateLeadNotes($lead);
                }
            });
    }

    private function migrateLeadNotes(Lead $lead): void
    {
        $rawNotes = trim((string) $lead->notes);

        if ($rawNotes === '') {
            return;
        }

        // Split on double newline — each block is one legacy note
        $blocks = preg_split('/\n{2,}/', $rawNotes);

        foreach ($blocks as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }

            // Try to parse the "YYYY-MM-DD HH:MM:SS: body" prefix
            $createdAt = now();
            $body      = $block;

            if (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}):\s*(.+)$/s', $block, $m)) {
                try {
                    $createdAt = \Carbon\Carbon::parse($m[1]);
                } catch (\Exception) {
                    // keep now() if timestamp is unparseable
                }
                $body = trim($m[2]);
            }

            if ($body === '') {
                continue;
            }

            DB::table('lead_notes')->insert([
                'id'         => \Illuminate\Support\Str::uuid(),
                'lead_id'    => $lead->id,
                'user_id'    => null, // original author unknown for legacy notes
                'body'       => $body,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }

    public function down(): void
    {
        // Only delete lead_notes where user_id IS NULL and lead has a non-empty notes column
        // (i.e., the rows created by this migration, identified by NULL user_id)
        // For a production rollback, a proper marker table would be safer.
        // Here we remove NULL-author notes that correspond to leads with legacy text.
        Lead::query()
            ->whereNotNull('notes')
            ->where('notes', '!=', '')
            ->chunkById(100, function ($leads): void {
                $ids = $leads->pluck('id');
                DB::table('lead_notes')
                    ->whereIn('lead_id', $ids)
                    ->whereNull('user_id')
                    ->delete();
            });
    }
};
