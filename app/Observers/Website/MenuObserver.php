<?php

namespace App\Observers\Website;

use App\Models\Website\Menu;
use Illuminate\Support\Facades\Cache;

class MenuObserver
{
    public function created(Menu $menu): void
    {
        $this->bustCache($menu);
    }

    public function updated(Menu $menu): void
    {
        $this->bustCache($menu);
    }

    public function deleted(Menu $menu): void
    {
        $this->bustCache($menu);
    }

    public function restored(Menu $menu): void
    {
        $this->bustCache($menu);
    }

    public function forceDeleted(Menu $menu): void
    {
        $this->bustCache($menu);
    }

    private function bustCache(Menu $menu): void
    {
        $dealerId = $menu->dealer_id;
        $locationId = $menu->location_id;

        Cache::forget("dealer_{$dealerId}_location_{$locationId}_main_menu");
        Cache::forget("dealer_{$dealerId}_location_{$locationId}_footer_menu");
        Cache::forget("dealer_{$dealerId}_location__main_menu");
        Cache::forget("dealer_{$dealerId}_location__footer_menu");
    }
}