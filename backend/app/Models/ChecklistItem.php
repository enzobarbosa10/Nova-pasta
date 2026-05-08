<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'task',
        'category',
        'status',
        'expedition_id',
        'assigned_to',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function expedition()
    {
        return $this->belongsTo(Expedition::class);
    }

    public function toggleStatus()
    {
        $this->status = $this->status === 'PENDING' ? 'DONE' : 'PENDING';
        $this->save();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'DONE');
    }
}
