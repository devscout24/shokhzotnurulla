<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use App\Observers\Website\LocationEmailObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([LocationEmailObserver::class])]

class LocationEmail extends Model
{
    protected $fillable = ['emailable_type', 'emailable_id', 'type', 'email'];

    public function emailable(): MorphTo
    {
        return $this->morphTo();
    }
}