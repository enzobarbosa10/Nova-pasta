<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

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
        'notes',
        'last_contact',
        'next_follow_up',
        'tags',
    ];

    protected $casts = [
        'date_desired' => 'date',
        'last_contact' => 'date',
        'next_follow_up' => 'date',
        'tags' => 'array',
        'estimated_ticket' => 'decimal:2',
        'people_count' => 'integer',
    ];

    protected $hidden = [];

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('next_follow_up', '<=', now()->addDays(7));
    }
}
