<?php

namespace App\Providers;

use App\Http\View\Composers\DealerFrontendComposer;
use App\Http\View\Composers\FrontendCatalogComposer;
use App\Models\TrackingScript;
use App\Models\Website\Menu;
use App\Services\Website\DealerResolverService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    private static array $schemaCache = [];

    public function register(): void {}

    public function boot(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $this->registerTrackingComposer();
        $this->registerMenuComposers();
        $this->registerDealerFrontendComposer();
        $this->registerFrontendCatalogComposer();
    }

    // ── Tracking ──────────────────────────────────────────────────────────────

    private function registerTrackingComposer(): void
    {
        View::composer(
            ['partials.tracking-head', 'partials.tracking-body-start', 'partials.tracking-body-end'],
            function ($view): void {
                if (! class_exists(TrackingScript::class) || ! $this->tableExists('tracking_scripts')) {
                    $view->with('scripts', collect());
                    return;
                }

                $view->with('scripts', Cache::remember(
                    'tracking_scripts_global',
                    config('seo.tracking_cache_ttl', 3600),
                    fn () => TrackingScript::where('is_active', true)
                        ->get()
                        ->groupBy('position'),
                ));
            }
        );
    }

    // ── Navigation Menus ──────────────────────────────────────────────────────

    private function registerMenuComposers(): void
    {
        if (! $this->tableExists('menus')) {
            View::composer(
                ['frontend.partials.header', 'frontend.partials.footer'],
                fn ($view) => $view->with([
                    'mainMenu'   => collect(),
                    'footerMenu' => collect(),
                ])
            );
            return;
        }

        View::composer('frontend.partials.header', function ($view): void {
            $dealerId = $this->resolveDealerId();

            $view->with('mainMenu', $dealerId
                ? Cache::remember(
                    "dealer_{$dealerId}_main_menu",
                    3600,
                    fn () => Menu::forDealer($dealerId)
                        ->forLocation('main')
                        ->topLevel()
                        ->with('children')
                        ->orderBy('sort_order')
                        ->get()
                )
                : collect()
            );
        });

        View::composer('frontend.partials.footer', function ($view): void {
            $dealerId = $this->resolveDealerId();

            $view->with('footerMenu', $dealerId
                ? Cache::remember(
                    "dealer_{$dealerId}_footer_menu",
                    3600,
                    fn () => Menu::forDealer($dealerId)
                        ->forLocation('footer')
                        ->topLevel()
                        ->orderBy('sort_order')
                        ->get()
                )
                : collect()
            );
        });
    }

    // ── Dealer Frontend Settings ───────────────────────────────────────────────

    private function registerDealerFrontendComposer(): void
    {
        View::composer(
            [
                // 'layouts.frontend.app',
                // 'frontend.partials.dealership-info',
                // 'frontend.pages.contact-us',
                'frontend.*',
            ],
            DealerFrontendComposer::class
        );
    }

    // ── Frontend Catalog Data ────────────────────────────

    private function registerFrontendCatalogComposer(): void
    {
        View::composer(
            [
                'frontend.modals.main-search',
                'frontend.offcanvas.get-trade-in',
            ],
            FrontendCatalogComposer::class
        );
    }

    /** Memoized dealer ID — shared across all composer instances in one request. */
    private static bool $dealerIdResolved = false;
    private static ?int $dealerIdCache = null;

    private function resolveDealerId(): ?int
    {
        if (self::$dealerIdResolved) {
            return self::$dealerIdCache;
        }

        try {
            self::$dealerIdCache = app(DealerResolverService::class)->resolve(request());
        } catch (\Throwable) {
            self::$dealerIdCache = null;
        }

        self::$dealerIdResolved = true;
        return self::$dealerIdCache;
    }

    private function tableExists(string $table): bool
    {
        if (! array_key_exists($table, self::$schemaCache)) {
            self::$schemaCache[$table] = Schema::hasTable($table);
        }

        return self::$schemaCache[$table];
    }
}