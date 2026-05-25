<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class LocationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // 1. Get the current active location from the service container safely
        $activeLocation = app()->bound('currentLocation') ? app('currentLocation') : null;

        // 2. If a location is set in the active context, restrict queries to that location
        if ($activeLocation) {
            $builder->where($model->getTable() . '.location_id', $activeLocation->id);
        }
    }
}
