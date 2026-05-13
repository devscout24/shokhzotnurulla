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
        if ($dealer->domain) {
            \App\Services\Website\WebResolver::clearCache($dealer->domain);
        }

        // Seed default menus for newly registered dealers
        \Database\Seeders\MenuSeeder::seedForDealer($dealer);
    }

    public function updated(Dealer $dealer): void
    {
        Cache::forget("dealer_{$dealer->id}_frontend_settings");
        if ($dealer->domain) {
            \App\Services\Website\WebResolver::clearCache($dealer->domain);
        }
        if ($dealer->isDirty('domain')) {
            \App\Services\Website\WebResolver::clearCache($dealer->getOriginal('domain'));
        }
    }

    public function deleted(Dealer $dealer): void
    {
        Cache::forget("dealer_{$dealer->id}_frontend_settings");
        if ($dealer->domain) {
            \App\Services\Website\WebResolver::clearCache($dealer->domain);
        }
    }

    public function restored(Dealer $dealer): void
    {
        Cache::forget("dealer_{$dealer->id}_frontend_settings");
        if ($dealer->domain) {
            \App\Services\Website\WebResolver::clearCache($dealer->domain);
        }
    }

    public function forceDeleted(Dealer $dealer): void
    {
        Cache::forget("dealer_{$dealer->id}_frontend_settings");
        if ($dealer->domain) {
            \App\Services\Website\WebResolver::clearCache($dealer->domain);
        }
    }
}
