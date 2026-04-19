<?php

namespace App\Models\Website;

use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use App\Observers\Website\LocationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([LocationObserver::class])]

class Location extends Model
{
    protected $fillable = [
        'dealer_id', 'name', 'street1', 'street2', 'city', 'state', 'postalcode',
        'country', 'map_override', 'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function phones(): MorphMany
    {
        return $this->morphMany(LocationPhone::class, 'phoneable');
    }

    public function emails(): MorphMany
    {
        return $this->morphMany(LocationEmail::class, 'emailable');
    }

    public function hours(): MorphMany
    {
        return $this->morphMany(LocationHour::class, 'hourly');
    }

    public function specialHours(): HasMany
    {
        return $this->hasMany(LocationSpecialHour::class);
    }

    // Helper methods to get specific phone/email by type
    public function getPhoneByType(string $type): ?string
    {
        return $this->phones->where('type', $type)->first()?->number;
    }

    public function getEmailByType(string $type): ?string
    {
        return $this->emails->where('type', $type)->first()?->email;
    }
}