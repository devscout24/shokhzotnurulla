<?php

namespace App\Observers\Website;

use App\Models\Website\Location;
use App\Models\Website\LocationEmail;
use Illuminate\Support\Facades\Cache;

class LocationEmailObserver
{
    public function created(LocationEmail $email): void
    {
        $this->bustCache($email);
    }

    public function updated(LocationEmail $email): void
    {
        $this->bustCache($email);
    }

    public function deleted(LocationEmail $email): void
    {
        $this->bustCache($email);
    }

    public function restored(LocationEmail $email): void
    {
        $this->bustCache($email);
    }

    public function forceDeleted(LocationEmail $email): void
    {
        $this->bustCache($email);
    }

    private function bustCache(LocationEmail $email): void
    {
        $emailable = $email->emailable;

        if (!$emailable instanceof Location) {
            return;
        }

        Cache::forget("dealer_{$emailable->dealer_id}_frontend_settings");
        Cache::forget("dealer_{$emailable->dealer_id}_location_menu");
    }
}
