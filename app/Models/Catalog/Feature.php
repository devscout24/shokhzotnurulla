<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory\Vehicle;

class Feature extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug'];

    /*
    |--------------
    | Relationships
    |--------------
    */

    public function vehicles()
    {
        return $this->belongsToMany(
            Vehicle::class,
            'vehicle_features',
            'feature_id',
            'vehicle_id'
        );
    }
}
