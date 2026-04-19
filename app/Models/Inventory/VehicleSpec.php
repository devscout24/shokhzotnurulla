<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleSpec extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'aspiration',
        'block_type',
        'cylinders',
        'displacement',
        'power_cycle',
        'max_horsepower',
        'max_horsepower_at',
        'max_torque',
        'max_torque_at',
        'transmission_standard',
        'drivetrain_standard',
        'towing_capacity',
        'payload_capacity',
        'gvwr',
        'empty_weight',
        'load_capacity',
        'fuel_tank',
        'mpg_city',
        'mpg_highway',
        'ev_range',
        'ev_battery_capacity',
        'ev_charger_rating',
        'dimension_width',
        'dimension_length',
        'dimension_height',
        'wheelbase',
        'bed_length',
        'axle',
        'axle_ratio',
        'rear_door_gate',
        'front_wheel',
        'rear_wheel',
        'front_tire',
        'rear_tire',
    ];

    protected $casts = [
        'displacement'         => 'decimal:1',
        'mpg_city'             => 'decimal:1',
        'mpg_highway'          => 'decimal:1',
        'ev_battery_capacity'  => 'decimal:1',
        'ev_charger_rating'    => 'decimal:1',
        'axle_ratio'           => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
