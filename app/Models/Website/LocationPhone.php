<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use App\Observers\Website\LocationPhoneObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([LocationPhoneObserver::class])]

class LocationPhone extends Model
{
    protected $fillable = ['phoneable_type', 'phoneable_id', 'type', 'number'];

    public function phoneable(): MorphTo
    {
        return $this->morphTo();
    }
}