<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class PromoBanner extends Model
{
    protected $fillable = [
        'promo_category_id',
        'title',
        'disclaimer',
        'condition',
        'certified',
        'author',
        'status',
        'start_date',
        'end_date',
        'link_url',
        'desktop_image_url',
        'mobile_image_url',
        'srp_desktop_banner_url',
        'srp_mobile_banner_url',
        'primary_color',
        'secondary_color',
        'content',
        'sort_order',
    ];

    public function category()
    {
        return $this->belongsTo(PromoCategory::class, 'promo_category_id');
    }
}
