<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class PromoCategory extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function banners()
    {
        return $this->hasMany(PromoBanner::class);
    }
}
