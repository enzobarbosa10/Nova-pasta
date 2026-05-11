<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'expedition_id',
        'title',
        'description',
        'status',
        'due_date',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    // ---------------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------------

    public function expedition(): BelongsTo
    {
        return $this->belongsTo(Expedition::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ---------------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------------

    public function toggleStatus(): void
    {
        $this->status = $this->status === 'DONE' ? 'PENDING' : 'DONE';
        $this->save();
    }
}
