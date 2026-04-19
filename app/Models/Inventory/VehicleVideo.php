<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleVideo extends Model
{
    protected $fillable = [
        'vehicle_id',
        'source',
        'url',
        'autoplay',
        'aspect_ratio',
    ];

    protected $casts = [
        'autoplay' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
