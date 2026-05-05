<?php

use App\Http\Controllers\Frontend\FormEntryController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Models\Dealership\Dealer;
use App\Models\Website\Domain;
use App\Services\Website\DealerResolverService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

// ── Auth Routes ───────────────────────────────────────────────────────────────
Auth::routes(['verify' => true]);

// ── Panel Routes ──────────────────────────────────────────────────────────────
require __DIR__ . '/dealer.php';
require __DIR__ . '/admin.php';

Route::middleware([\App\Http\Middleware\LogWebsiteVisit::class])->name('frontend.')->group(function () {

    // Pages
    Route::get('/', [FrontendController::class, 'home'])->name('home');
    Route::get('/inventory', [FrontendController::class, 'inventory'])->name('inventory');

    // ── AJAX filter endpoint — MUST be before /inventory/{slug} ──────────────
    Route::get('/inventory/filter', [FrontendController::class, 'inventoryFilter'])->name('inventory.filter');

    Route::get('/inventory/{slug}', [FrontendController::class, 'inventoryDetail'])->name('inventory.show');

    Route::get('/inventory/printable/{vehicle}/{printable}', [FrontendController::class, 'printable'])->name('inventory.vehicle.printable');

    // Vehicle type pages + their AJAX filter endpoints
    Route::get('/{type}', [FrontendController::class, 'inventoryByType'])
        ->name('inventory.type')
        ->where('type', implode('|', array_keys(config('vehicle_types'))));

    Route::get('/{type}/filter', [FrontendController::class, 'inventoryByTypeFilter'])
        ->name('inventory.type.filter')
        ->where('type', implode('|', array_keys(config('vehicle_types'))));

    // Static pages
    Route::view('/get-approved', 'frontend.pages.get-approved')->name('get-approved');
    Route::view('/schedule-service', 'frontend.pages.service')->name('service');
    Route::get('/about-us', [FrontendController::class, 'aboutUs'])->name('about');
    Route::get('/contact-us', [FrontendController::class, 'contactUs'])->name('contact');
    Route::view('/privacy-policy', 'frontend.pages.privacy-policy')->name('privacy');
    Route::view('/terms-of-service', 'frontend.pages.terms-of-service')->name('terms');
    Route::view('/direction', 'frontend.pages.direction')->name('direction');

    // Data endpoints
    Route::get('/data/makes/{make}/models', [FrontendController::class, 'makeModels'])->name('data.make-models');
    Route::post('/data/vin-decode', [FrontendController::class, 'vinDecode'])->name('data.vin-decode');

    // Debug: verify dealer resolution on frontend
    Route::get('/test/dealer/frontend-data', function (DealerResolverService $dealerResolver) {
        $host = strtolower(request()->getHost());
        if (request()->boolean('refresh')) {
            Cache::forget("dealer_id_by_domain:{$host}");
        }
        $dealerId = $dealerResolver->resolve();

        $dealer       = Dealer::find($dealerId, ['id', 'name', 'slug', 'domain', 'staging_domain', 'is_active']);
        $domainRecord = Domain::where('domain', $host)
            ->first(['id', 'dealer_id', 'domain', 'is_primary', 'is_verified']);

        return response()->json([
            'request_host'       => $host,
            'cache_refreshed'    => request()->boolean('refresh'),
            'resolved_dealer_id' => $dealerId,
            'dealer'             => $dealer,
            'domain_record'      => $domainRecord,
        ]);
    })->name('frontend.test.dealer-frontend-data');

    // Form submissions
    Route::prefix('forms')->name('forms.')->group(function () {
        Route::post('/trade-in', [FormEntryController::class, 'tradeIn'])->name('trade-in');
        Route::post('/get-approved', [FormEntryController::class, 'getApproved'])->name('get-approved');
        Route::post('/managers-special', [FormEntryController::class, 'managersSpecial'])->name('managers-special');
        Route::post('/ask-question', [FormEntryController::class, 'askQuestion'])->name('ask-question');
        Route::post('/schedule-test-drive', [FormEntryController::class, 'scheduleTestDrive'])->name('schedule-test-drive');
        Route::post('/contact-us', [FormEntryController::class, 'contactUs'])->name('contact-us');
        Route::post('/trade-in/photos', [FormEntryController::class, 'uploadTradeInPhotos'])->name('trade-in.photos');
        Route::patch('/{formEntry}/nps', [FormEntryController::class, 'updateNps'])->name('nps');
        Route::post('/unlock-price', [FormEntryController::class, 'unlockPrice'])->name('unlock-price');
    });

    // Dynamic Pages (Catch-all for slugs)
    Route::get('/{slug}', [FrontendController::class, 'showPage'])->name('page.show');
});

// ── Test Routes (local only) ──────────────────────────────────────────────────
if (app()->isLocal()) {
    Route::get('/test-404', fn() => abort(404));
    Route::get('/test-403', fn() => abort(403));
    Route::get('/test-500', fn() => abort(500));
}
