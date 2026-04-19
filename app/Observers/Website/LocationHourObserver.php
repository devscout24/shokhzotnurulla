<?php

namespace App\Observers\Website;

use App\Models\Website\Location;
use App\Models\Website\LocationHour;
use Illuminate\Support\Facades\Cache;

class LocationHourObserver
{
    public function created(LocationHour $hour): void
    {
        $this->bustCache($hour);
    }

    public function updated(LocationHour $hour): void
    {
        $this->bustCache($hour);
    }

    public function deleted(LocationHour $hour): void
    {
        $this->bustCache($hour);
    }

    public function restored(LocationHour $hour): void
    {
        $this->bustCache($hour);
    }

    public function forceDeleted(LocationHour $hour): void
    {
        $this->bustCache($hour);
    }

    private function bustCache(LocationHour $hour): void
    {
        $hourly = $hour->hourly;

        if (!$hourly instanceof Location) {
            return;
        }

        Cache::forget("dealer_{$hourly->dealer_id}_frontend_settings");
        Cache::forget("dealer_{$hourly->dealer_id}_location_menu");
    }
}
