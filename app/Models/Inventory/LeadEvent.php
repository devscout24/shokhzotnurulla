<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'dealer_id',
        'type',
        'source',
    ];

    /*
    |------------------------------
    | Relationships
    |------------------------------
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
