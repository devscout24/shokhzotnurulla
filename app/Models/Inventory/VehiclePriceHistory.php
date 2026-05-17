<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class VehiclePriceHistory extends Model
{
    protected $fillable = [
        'vehicle_id',
        'old_price',
        'new_price',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
