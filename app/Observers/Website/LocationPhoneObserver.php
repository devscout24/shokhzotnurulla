<?php

namespace App\Observers\Website;

use App\Models\Website\Location;
use App\Models\Website\LocationPhone;
use Illuminate\Support\Facades\Cache;

class LocationPhoneObserver
{
    public function created(LocationPhone $phone): void
    {
        $this->bustCache($phone);
    }

    public function updated(LocationPhone $phone): void
    {
        $this->bustCache($phone);
    }

    public function deleted(LocationPhone $phone): void
    {
        $this->bustCache($phone);
    }

    public function restored(LocationPhone $phone): void
    {
        $this->bustCache($phone);
    }

    public function forceDeleted(LocationPhone $phone): void
    {
        $this->bustCache($phone);
    }

    private function bustCache(LocationPhone $phone): void
    {
        $phoneable = $phone->phoneable;

        if (!$phoneable instanceof Location) {
            return;
        }

        Cache::forget("dealer_{$phoneable->dealer_id}_frontend_settings");
        Cache::forget("dealer_{$phoneable->dealer_id}_location_menu");
    }
}
