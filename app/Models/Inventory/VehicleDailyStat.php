<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleDailyStat extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'dealer_id',
        'date',
        'views',
        'leads',
    ];

    /*
    |--------------
    | Relationships
    |--------------
    */

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function dealer()
    {
        return $this->belongsTo(\App\Models\Dealership\Dealer::class);
    }
}
