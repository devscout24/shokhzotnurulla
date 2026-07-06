<?php

namespace App\Models\Vin;

use Illuminate\Database\Eloquent\Model;

class VehileVinData extends Model
{
    protected $table = "vehile_vin_data";
    protected $guarded = ["id"];

    protected $casts = [
        'default' => 'array',
        'vehile_databases' => 'array',
        'data_one' => 'array',
        'custom' => 'array',
    ];
}
