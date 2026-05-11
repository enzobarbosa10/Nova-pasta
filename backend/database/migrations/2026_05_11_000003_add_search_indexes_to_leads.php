<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * [MÉDIO 7] Adds full-text search indexes to the leads table.
 *
 * MySQL  → FULLTEXT index on (name, whatsapp, destination)
 *          Used with MATCH ... AGAINST in BOOLEAN MODE by Lead::scopeSearch().
 *
 * PostgreSQL → pg_trgm extension + GIN indexes on each column individually.
 *              Used with ILIKE operator by Lead::scopeSearch().
 *              (PostgreSQL does not support composite GIN trigram indexes across
 *               different columns in a single index.)
 *
 * SQLite → no index created (falls back to LIKE in scopeSearch).
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            $this->upMysql();
        } elseif ($driver === 'pgsql') {
            $this->upPostgres();
        }
        // SQLite: no-op — LIKE fallback used in tests
    }

    // -------------------------------------------------------------------------
    // MySQL variant
    // -------------------------------------------------------------------------
    private function upMysql(): void
    {
        // A FULLTEXT index spans multiple columns in one index definition
        DB::statement(
            'ALTER TABLE leads ADD FULLTEXT INDEX idx_leads_search_ft (name, whatsapp, destination)'
        );
    }

    // -------------------------------------------------------------------------
    // PostgreSQL variant
    // -------------------------------------------------------------------------
    private function upPostgres(): void
    {
        // Enable the trigram extension (requires superuser on first use)
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');

        // Only create index if the column exists in the table
        $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_schema='public' AND table_name='leads'");
        $existing = array_column($columns, 'column_name');

        $indexMap = [
            'name'        => 'idx_leads_name_trgm',
            'whatsapp'    => 'idx_leads_whatsapp_trgm',
            'destination' => 'idx_leads_destination_trgm',
        ];

        foreach ($indexMap as $column => $indexName) {
            if (in_array($column, $existing)) {
                DB::statement("CREATE INDEX IF NOT EXISTS {$indexName} ON leads USING gin ({$column} gin_trgm_ops)");
            }
        }
    }

    // -------------------------------------------------------------------------
    // Rollback
    // -------------------------------------------------------------------------
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE leads DROP INDEX idx_leads_search_ft');
        } elseif ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS idx_leads_name_trgm');
            DB::statement('DROP INDEX IF EXISTS idx_leads_whatsapp_trgm');
            DB::statement('DROP INDEX IF EXISTS idx_leads_destination_trgm');
        }
    }
};
