<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Lead extends Model
{
    use HasFactory, HasUuids;

    // ---------------------------------------------------------------------------
    // Fillable / Casts
    // ---------------------------------------------------------------------------

    protected $fillable = [
        'name',
        'whatsapp',
        'instagram',
        'source',
        'interest',
        'destination',
        'date_desired',
        'people_count',
        'estimated_ticket',
        'status',
        'notes',        // legacy text field — kept for backward-compatibility during migration
        'last_contact',
        'next_follow_up',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'date_desired'     => 'date',
            'last_contact'     => 'date',
            'next_follow_up'   => 'date',
            'estimated_ticket' => 'float',
            'people_count'     => 'integer',
            'tags'             => 'array',
        ];
    }

    // ---------------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------------

    public function leadNotes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest();
    }

    // ---------------------------------------------------------------------------
    // [CRÍTICO 2 / MÉDIO 7] scopeUpcoming — upcoming follow-ups
    // ---------------------------------------------------------------------------

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('next_follow_up', '<=', now()->addDays(7))
                     ->whereDate('next_follow_up', '>=', now());
    }

    // ---------------------------------------------------------------------------
    // [MÉDIO 7] scopeSearch — driver-aware full-text search
    //
    // MySQL:      Uses MATCH ... AGAINST with FULLTEXT index for fast lookups.
    //             Requires migration 2026_05_11_000003_add_search_indexes_to_leads.php
    //
    // PostgreSQL: Uses the pg_trgm GIN index with ILIKE for substring matching.
    //             Requires the same migration (PostgreSQL variant).
    //
    // SQLite:     Falls back to LIKE for test environments.
    //
    // Usage (controller):
    //   $query->search($request->search);
    // ---------------------------------------------------------------------------

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $driver = DB::getDriverName();

        return match ($driver) {
            'mysql'  => $this->applyMysqlFulltext($query, $term),
            'pgsql'  => $this->applyPostgresTrigramSearch($query, $term),
            default  => $this->applyLikeFallback($query, $term),
        };
    }

    // ---- MySQL: FULLTEXT BOOLEAN MODE (requires FULLTEXT index) ----
    private function applyMysqlFulltext(Builder $query, string $term): Builder
    {
        // Sanitise input: keep alphanumeric and spaces, strip FULLTEXT operators
        $safe = preg_replace('/[+\-><()*~"@]+/', ' ', $term);
        $safe = trim($safe);

        if ($safe === '') {
            return $query;
        }

        // Boolean mode: each space-separated word must appear (+word1 +word2)
        $booleanTerm = collect(explode(' ', $safe))
            ->filter()
            ->map(fn (string $w) => "+{$w}*")
            ->implode(' ');

        return $query->whereRaw(
            'MATCH(name, whatsapp, destination) AGAINST(? IN BOOLEAN MODE)',
            [$booleanTerm]
        );
    }

    // ---- PostgreSQL: trigram similarity via pg_trgm + GIN index ----
    private function applyPostgresTrigramSearch(Builder $query, string $term): Builder
    {
        $safe = addcslashes($term, '%_\\');

        return $query->where(function (Builder $q) use ($safe) {
            $q->whereRaw('name ILIKE ?', ["%{$safe}%"])
              ->orWhereRaw('whatsapp ILIKE ?', ["%{$safe}%"])
              ->orWhereRaw('destination ILIKE ?', ["%{$safe}%"]);
        });
    }

    // ---- Fallback (SQLite / test environments) ----
    private function applyLikeFallback(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('whatsapp', 'like', "%{$term}%")
              ->orWhere('destination', 'like', "%{$term}%");
        });
    }
}
