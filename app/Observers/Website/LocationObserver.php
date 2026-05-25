<?php

namespace App\Observers\Website;

use App\Models\Website\Location;
use Illuminate\Support\Facades\Cache;

class LocationObserver
{
    public function created(Location $location): void
    {
        $this->bustCache($location->dealer_id);

        // If this is the dealer's first location, associate all legacy (NULL location) vehicles and form entries with it
        $locationCount = Location::where('dealer_id', $location->dealer_id)->count();
        if ($locationCount === 1) {
            \App\Models\Inventory\Vehicle::where('dealer_id', $location->dealer_id)
                ->whereNull('location_id')
                ->update(['location_id' => $location->id]);

            \App\Models\Website\FormEntry::where('dealer_id', $location->dealer_id)
                ->whereNull('location_id')
                ->update(['location_id' => $location->id]);
        }
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
        Cache::forget("dealer_locations:{$dealerId}");
        
        try {
            app(\App\Services\Inventory\InventoryListingService::class)->invalidateFilterCache($dealerId);
        } catch (\Exception $e) {
            // Ignore if service is not bound
        }
    }
}
