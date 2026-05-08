<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'url',
        'type',
        'expedition_id',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
        'created_at' => 'datetime',
    ];

    public function expedition()
    {
        return $this->belongsTo(Expedition::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByExpedition($query, $expeditionId)
    {
        return $query->where('expedition_id', $expeditionId);
    }
}
