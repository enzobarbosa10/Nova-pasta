<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * [ALTO 4] Expedition model — with SoftDeletes and dependency guards.
 */
class Expedition extends Model
{
    use HasFactory, HasUuids, SoftDeletes; // [ALTO 4] soft delete como camada de segurança

    // ---------------------------------------------------------------------------
    // Fillable / Casts
    // ---------------------------------------------------------------------------

    protected $fillable = [
        'name',
        'cover_image',
        'destination',
        'dates',
        'start_date',
        'end_date',
        'capacity',
        'remaining_spots',
        'guide_id',
        'accommodation',
        'transport',
        'trail_level',
        'status',
        'costs',
        'margin_predicted',
        'margin_real',
        'participants',
    ];

    protected function casts(): array
    {
        return [
            'start_date'       => 'date',
            'end_date'         => 'date',
            'capacity'         => 'integer',
            'remaining_spots'  => 'integer',
            'costs'            => 'float',
            'margin_predicted' => 'float',
            'margin_real'      => 'float',
            'participants'     => 'array',
        ];
    }

    // ---------------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------------

    public function checklistItems(): HasMany
    {
        return $this->hasMany(ChecklistItem::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    // ---------------------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------------------

    /** Active expeditions (open for enrollment or in progress). */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['OPEN', 'GUARANTEED', 'IN_PROGRESS']);
    }

    /**
     * [ALTO 4] withoutActiveDependencies — only expeditions that CAN be deleted.
     *
     * Blocking conditions:
     *   1. Has at least one enrolled participant (participants JSON array not empty).
     *   2. Has checklist items with status other than DONE or CANCELLED.
     *
     * Reusable in any query needing safe-to-delete candidates:
     *   Expedition::withoutActiveDependencies()->where(...)->get();
     */
    public function scopeWithoutActiveDependencies(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $q) {
                $q->whereNull('participants')
                  ->orWhereJsonLength('participants', 0);
            })
            ->whereDoesntHave('checklistItems', function (Builder $q) {
                $q->whereNotIn('status', ['DONE', 'CANCELLED']);
            });
    }

    // ---------------------------------------------------------------------------
    // Business methods (participants management)
    // ---------------------------------------------------------------------------

    public function addParticipant(string $travelerId): void
    {
        $current   = $this->participants ?? [];
        $current[] = $travelerId;

        $this->update([
            'participants'    => array_values(array_unique($current)),
            'remaining_spots' => max(0, $this->remaining_spots - 1),
        ]);
    }

    public function removeParticipant(string $travelerId): void
    {
        $current = array_values(
            array_filter($this->participants ?? [], fn ($id) => $id !== $travelerId)
        );

        $this->update([
            'participants'    => $current,
            'remaining_spots' => $this->remaining_spots + 1,
        ]);
    }

    /**
     * [ALTO 4] Returns an array describing any active dependencies that block
     * permanent deletion. Empty array means the expedition can be deleted.
     *
     * @return array<int, array{type: string, count: int, message: string}>
     */
    public function getActiveDependencies(): array
    {
        $blockers = [];

        $participants = $this->participants ?? [];
        if (! empty($participants)) {
            $blockers[] = [
                'type'    => 'participants',
                'count'   => count($participants),
                'message' => count($participants) . ' viajante(s) inscrito(s) nesta expedição.',
            ];
        }

        $pendingTasks = $this->checklistItems()
            ->whereNotIn('status', ['DONE', 'CANCELLED'])
            ->count();

        if ($pendingTasks > 0) {
            $blockers[] = [
                'type'    => 'checklist_items',
                'count'   => $pendingTasks,
                'message' => $pendingTasks . ' tarefa(s) do checklist ainda não concluída(s).',
            ];
        }

        return $blockers;
    }
}
