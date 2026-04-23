<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class ServiceOfferCategory extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function serviceOffers()
    {
        return $this->hasMany(ServiceOffer::class, 'service_offer_category_id');
    }
}
