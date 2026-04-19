<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class VehiclePhoto extends Model
{
    protected $fillable = [
        'vehicle_id',
        'path',
        'disk',
        'url',
        'sort_order',
        'is_primary',
        'status',
        'width',
        'height',
    ];

    protected $casts = [
        'is_primary'  => 'boolean',
        'sort_order'  => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeLive(Builder $query): void
    {
        $query->where('status', 'live');
    }
}