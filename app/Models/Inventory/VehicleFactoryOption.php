<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleFactoryOption extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'category_id',
        'group_id',
        'option_key',
        'label',
        'is_starred',
        'is_selected',
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

    public function category()
    {
        return $this->belongsTo(\App\Models\Catalog\FactoryOptionCategory::class);
    }

    public function group()
    {
        return $this->belongsTo(\App\Models\Catalog\FactoryOptionGroup::class);
    }
}
