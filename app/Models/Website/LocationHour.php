<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

use App\Observers\Website\LocationHourObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([LocationHourObserver::class])]

class LocationHour extends Model
{
    protected $fillable = ['hourly_type', 'hourly_id', 'department', 'day_of_week', 'open_time', 'close_time', 'is_closed', 'appointment_only'];

    protected $casts = [
        'is_closed' => 'boolean',
        'appointment_only' => 'boolean',
    ];

    public function hourly(): MorphTo
    {
        return $this->morphTo();
    }
}