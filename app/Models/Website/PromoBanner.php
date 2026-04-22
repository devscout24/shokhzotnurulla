<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class PromoBanner extends Model
{
    protected $fillable = [
        'promo_category_id',
        'title',
        'author',
        'status',
        'start_date',
        'end_date',
        'link_url',
        'content',
        'sort_order',
    ];

    public function category()
    {
        return $this->belongsTo(PromoCategory::class, 'promo_category_id');
    }
}
