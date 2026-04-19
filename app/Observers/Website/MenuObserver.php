<?php

namespace App\Observers\Website;

use App\Models\Website\Menu;
use Illuminate\Support\Facades\Cache;

class MenuObserver
{
    public function created(Menu $menu): void
    {
        $this->bustCache($menu->dealer_id);
    }

    public function updated(Menu $menu): void
    {
        $this->bustCache($menu->dealer_id);
    }

    public function deleted(Menu $menu): void
    {
        $this->bustCache($menu->dealer_id);
    }

    public function restored(Menu $menu): void
    {
        $this->bustCache($menu->dealer_id);
    }

    public function forceDeleted(Menu $menu): void
    {
        $this->bustCache($menu->dealer_id);
    }

    private function bustCache(int $dealerId): void
    {
        Cache::forget("dealer_{$dealerId}_main_menu");
        Cache::forget("dealer_{$dealerId}_footer_menu");
    }
}