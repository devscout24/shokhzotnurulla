<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleFeature extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'feature_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function feature()
    {
        return $this->belongsTo(\App\Models\Catalog\Feature::class);
    }
}
