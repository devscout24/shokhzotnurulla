<?php

namespace App\Traits;

use App\Models\Scopes\LocationScope;

/**
 * This trait automatically applies the LocationScope to any model that uses it.
 * It also handles setting the location_id when creating a new record.
 */
trait BelongsToLocation
{
    public static function bootBelongsToLocation()
    {
        static::addGlobalScope(new LocationScope);

        static::creating(function ($model) {
            if (!$model->location_id && app()->bound('currentLocation')) {
                $model->location_id = app('currentLocation')->id;
            }
        });
    }

    public function location()
    {
        return $this->belongsTo(\App\Models\Website\Location::class);
    }
}
