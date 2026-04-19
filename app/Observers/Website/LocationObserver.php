<?php

namespace App\Observers\Website;

use App\Models\Website\Location;
use Illuminate\Support\Facades\Cache;

class LocationObserver
{
    public function created(Location $location): void
    {
        $this->bustCache($location->dealer_id);
    }

    public function updated(Location $location): void
    {
        $this->bustCache($location->dealer_id);
    }

    public function deleted(Location $location): void
    {
        $this->bustCache($location->dealer_id);
    }

    public function restored(Location $location): void
    {
        $this->bustCache($location->dealer_id);
    }

    public function forceDeleted(Location $location): void
    {
        $this->bustCache($location->dealer_id);
    }

    private function bustCache(int $dealerId): void
    {
        Cache::forget("dealer_{$dealerId}_frontend_settings");
        Cache::forget("dealer_{$dealerId}_location_menu");
    }
}
