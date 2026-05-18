<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class SrpContent extends Model
{
    use SoftDeletes;
    use \App\Models\Traits\HasLocationScope;

    protected $fillable = [
        'dealer_id',
        'location_id',
        'nickname',
        'slug',
        'h1_override',
        'meta_title',
        'meta_description',
        'placement',
        'content',
        'author',
        'status',
        'sort_order'
    ];

    protected static function booted()
    {
        static::addGlobalScope('dealer', function (Builder $builder) {
            if (auth()->check() && auth()->user()->current_dealer_id) {
                $builder->where('dealer_id', auth()->user()->current_dealer_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->current_dealer_id) {
                $model->dealer_id = auth()->user()->current_dealer_id;
            }
        });
    }
}
