<?php

namespace App\Models\Vin;

use Illuminate\Database\Eloquent\Model;

class VehicleVinData extends Model
{
    protected $table = "vehicle_vin_data";
    protected $guarded = ["id"];

    protected $casts = [
        'default' => 'array',
        'vehicle_databases' => 'array',
        'premium_vehicle_databases' => 'array',
        'data_one' => 'array',
        'custom' => 'array',
    ];
}
