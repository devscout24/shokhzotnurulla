<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiclePremiumOption extends Model
{
    protected $fillable = [
        'vehicle_id',
        'factory_code',
        'category',
        'name',
        'description',
        'msrp',
    ];

    protected $casts = [
        'msrp' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}