<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class ServiceOffer extends Model
{
    use \App\Models\Traits\HasLocationScope;

    protected $fillable = [
        'location_id',
        'service_offer_category_id',
        'title',
        'subtitle',
        'description',
        'photo_url',
        'link_offer_to',
        'link_text',
        'disclaimer',
        'status',
        'author',
        'sort_order',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceOfferCategory::class, 'service_offer_category_id');
    }
}
