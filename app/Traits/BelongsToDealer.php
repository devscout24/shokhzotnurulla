<?php

namespace App\Traits;

use App\Models\Scopes\DealerScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

/**
 * This trait automatically applies the DealerScope to any model that uses it.
 * It also handles setting the dealer_id when creating a new record.
 */
trait BelongsToDealer
{
    public static function bootBelongsToDealer()
    {
        static::addGlobalScope(new DealerScope);

        static::creating(function ($model) {
            if (!$model->dealer_id && app()->has('currentDealer')) {
                $model->dealer_id = app('currentDealer')->id;
            }
        });
    }

    public function dealer()
    {
        return $this->belongsTo(\App\Models\Dealership\Dealer::class);
    }
}
