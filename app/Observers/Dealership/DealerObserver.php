<?php

namespace App\Observers\Dealership;

use App\Models\Dealership\Dealer;
use Illuminate\Support\Facades\Cache;

class DealerObserver
{
    /**
     * Bust dealer frontend settings cache when dealer record is updated.
     * Covers: name, legal_name, social_links changes.
     */
    public function created(Dealer $dealer): void
    {
        Cache::forget("dealer_{$dealer->id}_frontend_settings");
    }

    public function updated(Dealer $dealer): void
    {
        Cache::forget("dealer_{$dealer->id}_frontend_settings");
    }

    public function deleted(Dealer $dealer): void
    {
        Cache::forget("dealer_{$dealer->id}_frontend_settings");
    }

    public function restored(Dealer $dealer): void
    {
        Cache::forget("dealer_{$dealer->id}_frontend_settings");
    }

    public function forceDeleted(Dealer $dealer): void
    {
        Cache::forget("dealer_{$dealer->id}_frontend_settings");
    }
}
