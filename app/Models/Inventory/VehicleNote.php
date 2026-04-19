<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'dealer_notes',
        'ai_description',
        'internal_notes',
        'key_highlights',
        'highlights',
        'lock_highlights',
        'warranty_dealer',
        'warranty_non_dealer',
        'warranty_labor',
        'warranty_parts',
        'warranty_systems',
        'warranty_duration',
        'service_contract',
    ];

    protected $casts = [
        'key_highlights'  => 'array',
        'highlights'      => 'array',
        'lock_highlights' => 'boolean',
        'service_contract'=> 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
