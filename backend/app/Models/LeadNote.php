<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * [ALTO 3] LeadNote — replaces the legacy "notes" text field in leads.
 *
 * Each row is one atomic note entry linked to a lead and authored by a user.
 * Provides a proper audit trail with per-note edit/delete capabilities.
 *
 * @property string      $id
 * @property string      $lead_id
 * @property string      $user_id
 * @property string      $body
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class LeadNote extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'lead_id',
        'user_id',
        'body',
    ];

    // ---------------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------------

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
