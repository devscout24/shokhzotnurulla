<?php

namespace App\Http\View\Composers;

use App\Models\Catalog\BodyStyle;
use App\Models\Catalog\BodyType;
use App\Models\Catalog\Color;
use App\Models\Catalog\DrivetrainType;
use App\Models\Catalog\Make;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Provides catalog filter data to the main search modal and trade-in offcanvas.
 *
 * Bound to:
 *   - frontend.modals.main-search
 *   - frontend.offcanvas.get-trade-in
 *
 * Static $cache property ensures ONE cache round-trip per request regardless
 * of how many views this composer is bound to — once() won't work here because
 * Laravel resolves a fresh composer instance per view render.
 *
 * Cache:
 *   Key:  frontend_catalog_all
 *   TTL:  1 hour
 *   Bust: artisan cache:clear on catalog seeder runs
 */
class FrontendCatalogComposer
{
    /** Memoized across all instances within the same request lifecycle. */
    private static ?array $cache = null;

    public function compose(View $view): void
    {
        $view->with($this->getCatalogData());
    }

    public function getCatalogData(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        self::$cache = Cache::remember('frontend_catalog_all', 3600, fn () => [
            'globalMakes'           => Make::orderBy('name')->get(['id', 'name']),
            'globalColors'          => Color::orderBy('name')->get(['id', 'name']),
            'globalBodyTypes'       => BodyType::orderBy('name')->get(['id', 'name']),
            'globalBodyStyles'      => BodyStyle::orderBy('name')->get(['id', 'name']),
            'globalDrivetrainTypes' => DrivetrainType::orderBy('name')->get(['id', 'name']),
        ]);

        return self::$cache;
    }
}