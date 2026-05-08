<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class DealerScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // 1. Get the current dealer from the service container
        $dealer = app('currentDealer');

        // 2. If a dealer is identified for the current session/domain, restrict the query
        if ($dealer) {
            $builder->where('dealer_id', $dealer->id);
        }
    }
}
