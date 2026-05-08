<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expedition extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

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

    protected $casts = [
        'capacity' => 'integer',
        'remaining_spots' => 'integer',
        'costs' => 'decimal:2',
        'margin_predicted' => 'decimal:2',
        'margin_real' => 'decimal:2',
        'participants' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function checklistItems()
    {
        return $this->hasMany(ChecklistItem::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['OPEN', 'GUARANTEED', 'IN_PROGRESS']);
    }

    public function addParticipant($travelerId)
    {
        $participants = $this->participants ?? [];
        
        if (!in_array($travelerId, $participants)) {
            $participants[] = $travelerId;
            $this->participants = $participants;
            $this->remaining_spots = max(0, $this->capacity - count($participants));
            $this->save();
        }
    }

    public function removeParticipant($travelerId)
    {
        $participants = $this->participants ?? [];
        $participants = array_values(array_filter($participants, fn($id) => $id !== $travelerId));
        
        $this->participants = $participants;
        $this->remaining_spots = max(0, $this->capacity - count($participants));
        $this->save();
    }
}
