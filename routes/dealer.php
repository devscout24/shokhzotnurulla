<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dealer\WebsiteDashboardController;
use App\Http\Controllers\Dealer\WebsiteFormController;
use App\Http\Controllers\Dealer\WebsiteMediaController;
use App\Http\Controllers\Dealer\WebsiteMenuController;
use App\Http\Controllers\Dealer\WebsiteSettingController;
use App\Http\Controllers\Dealer\InventoryController;
use App\Http\Controllers\Dealer\InventorySettingController;
use App\Http\Controllers\Dealer\ConnectionController;
use App\Http\Controllers\Dealer\SettingController;
use App\Http\Controllers\Dealer\IncentiveController;
use App\Http\Controllers\Dealer\PrintableController;
use App\Http\Controllers\Dealer\PricingSpecialController;
use App\Http\Controllers\Dealer\WebsitePageController;
use App\Http\Controllers\Dealer\WebsiteFaqController;
use App\Http\Controllers\Dealer\WebsiteSrpContentController;
use App\Http\Controllers\Dealer\WebsiteStaticPageContentController;
use App\Http\Controllers\Dealer\WebsitePromoBannerController;
use App\Http\Controllers\Dealer\CustomerReviewController;

use App\Http\Controllers\Dealer\WebsiteStaffMemberController;
use App\Http\Controllers\Dealer\WebsiteJobPostController;
use App\Http\Controllers\Dealer\WebsiteServiceOfferController;
use App\Http\Controllers\Dealer\WebsiteEventController;

Route::prefix('dealer')->name('dealer.')
    ->middleware(['auth', 'verified', 'all.active', 'isDealer'])
    ->group(function () {

    // ─── Website ──────────────────────────────────────────────────────────────

    Route::prefix('website')->name('website.')->group(function () {

        Route::get('/dashboard', [WebsiteDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/media',     [WebsiteMediaController::class, 'index'])->name('media');

        // ── Media Library ──────────────────────────────────────────────────────
        Route::prefix('media')->name('media.')->group(function () {
            Route::get('/list',       [WebsiteMediaController::class, 'list'])->name('list');
            Route::post('/upload',    [WebsiteMediaController::class, 'upload'])->name('upload');
            Route::patch('/{media}',  [WebsiteMediaController::class, 'update']) ->name('update');
            Route::delete('/{media}', [WebsiteMediaController::class, 'destroy'])->name('destroy');
        });


        // ── Menus ──────────────────────────────────────────────────────
        Route::get('/menus',           [WebsiteMenuController::class, 'menus'])->name('menus');
        Route::get('/menus/data',      [WebsiteMenuController::class, 'data'])->name('menus.data');
        Route::post('/menus',          [WebsiteMenuController::class, 'store'])->name('menus.store');
        Route::patch('/menus/{menu}',  [WebsiteMenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [WebsiteMenuController::class, 'destroy'])->name('menus.destroy');
        Route::post('/menus/reorder',  [WebsiteMenuController::class, 'reorder'])->name('menus.reorder');

        // ── Pages (CMS) ────────────────────────────────────────────────────────
        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('/', [WebsitePageController::class, 'index'])->name('index');
            Route::get('/create', [WebsitePageController::class, 'create'])->name('create');
            Route::post('/', [WebsitePageController::class, 'store'])->name('store');
            Route::get('/{page}/edit', [WebsitePageController::class, 'edit'])->name('edit');
            Route::patch('/{page}', [WebsitePageController::class, 'update'])->name('update');
            Route::delete('/{page}', [WebsitePageController::class, 'destroy'])->name('destroy');
            Route::get('/by-tag/{tag}', [WebsitePageController::class, 'getByTag'])->name('by-tag');
        });

        // ── FAQs (Reusable Content) ──────────────────────────────────────────────────
        Route::prefix('faqs')->name('faqs.')->group(function () {
            Route::get('/',                          [WebsiteFaqController::class, 'index'])->name('index');
            Route::post('/',                         [WebsiteFaqController::class, 'storeFaq'])->name('store');
            Route::patch('/{faq}',                   [WebsiteFaqController::class, 'updateFaq'])->name('update');
            Route::delete('/{faq}',                  [WebsiteFaqController::class, 'destroyFaq'])->name('destroy');
            Route::post('/categories',               [WebsiteFaqController::class, 'storeCategory'])->name('categories.store');
            Route::patch('/categories/{faqCategory}', [WebsiteFaqController::class, 'updateCategory'])->name('categories.update');
            Route::delete('/categories/{faqCategory}',[WebsiteFaqController::class, 'destroyCategory'])->name('categories.destroy');
            Route::post('/bulk-update',               [WebsiteFaqController::class, 'bulkUpdate'])->name('bulk-update');
        });

        // ── SRP Content (Reusable Content) ───────────────────────────────────────────
        Route::prefix('srp-content')->name('srp-content.')->group(function () {
            Route::get('/',              [WebsiteSrpContentController::class, 'index'])->name('index');
            Route::post('/',             [WebsiteSrpContentController::class, 'store'])->name('store');
            Route::patch('/{srpContent}', [WebsiteSrpContentController::class, 'update'])->name('update');
            Route::delete('/{srpContent}', [WebsiteSrpContentController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-update',   [WebsiteSrpContentController::class, 'bulkUpdate'])->name('bulk-update');
        });

        // ── Static Page Content (Reusable Content) ───────────────────────────────────
        Route::prefix('static-page-content')->name('static-page-content.')->group(function () {
            Route::get('/',                                 [WebsiteStaticPageContentController::class, 'index'])->name('index');
            Route::post('/',                                [WebsiteStaticPageContentController::class, 'store'])->name('store');
            Route::patch('/{staticPageContent}',            [WebsiteStaticPageContentController::class, 'update'])->name('update');
            Route::delete('/{staticPageContent}',           [WebsiteStaticPageContentController::class, 'destroy'])->name('destroy');
            Route::post('/categories',                      [WebsiteStaticPageContentController::class, 'storeCategory'])->name('categories.store');
            Route::patch('/categories/{staticPageCategory}', [WebsiteStaticPageContentController::class, 'updateCategory'])->name('categories.update');
            Route::delete('/categories/{staticPageCategory}',[WebsiteStaticPageContentController::class, 'destroyCategory'])->name('categories.destroy');
            Route::post('/bulk-update',                      [WebsiteStaticPageContentController::class, 'bulkUpdate'])->name('bulk-update');
        });

        // ── OEM Promo Banners (Reusable Content) ─────────────────────────────────────
        Route::prefix('promo-banners')->name('promo-banners.')->group(function () {
            Route::get('/', [WebsitePromoBannerController::class, 'index'])->name('index');
            Route::post('/', [WebsitePromoBannerController::class, 'store'])->name('store');
            Route::get('/download-template', [WebsitePromoBannerController::class, 'downloadTemplate'])->name('download-template');
            Route::post('/upload-csv', [WebsitePromoBannerController::class, 'uploadCsv'])->name('upload-csv');
            Route::patch('/{promoBanner}', [WebsitePromoBannerController::class, 'update'])->name('update');
            Route::delete('/{promoBanner}', [WebsitePromoBannerController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-update', [WebsitePromoBannerController::class, 'bulkUpdate'])->name('bulk-update');
            
            Route::post('/categories', [WebsitePromoBannerController::class, 'storeCategory'])->name('categories.store');
            Route::patch('/categories/{promoCategory}', [WebsitePromoBannerController::class, 'updateCategory'])->name('categories.update');
            Route::delete('/categories/{promoCategory}', [WebsitePromoBannerController::class, 'destroyCategory'])->name('categories.destroy');
        });

        // ── Customer Reviews (Reusable Content) ──────────────────────────────────────
        Route::prefix('customer-reviews')->name('customer-reviews.')->group(function () {
            Route::get('/',                          [CustomerReviewController::class, 'index'])->name('index');
            Route::post('/',                         [CustomerReviewController::class, 'store'])->name('store');
            Route::patch('/{customerReview}',        [CustomerReviewController::class, 'update'])->name('update');
            Route::delete('/{customerReview}',       [CustomerReviewController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-update',              [CustomerReviewController::class, 'bulkUpdate'])->name('bulk-update');
            Route::post('/categories',               [CustomerReviewController::class, 'storeCategory'])->name('categories.store');
            Route::patch('/categories/{customerReviewCategory}', [CustomerReviewController::class, 'updateCategory'])->name('categories.update');
            Route::delete('/categories/{customerReviewCategory}',[CustomerReviewController::class, 'destroyCategory'])->name('categories.destroy');
        });

        // ── Staff Members (Reusable Content) ──────────────────────────────────────
        Route::prefix('staff-members')->name('staff-members.')->group(function () {
            Route::get('/',                          [WebsiteStaffMemberController::class, 'index'])->name('index');
            Route::post('/',                         [WebsiteStaffMemberController::class, 'store'])->name('store');
            Route::patch('/{staffMember}',           [WebsiteStaffMemberController::class, 'update'])->name('update');
            Route::delete('/{staffMember}',          [WebsiteStaffMemberController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-update',              [WebsiteStaffMemberController::class, 'bulkUpdate'])->name('bulk-update');
            Route::post('/categories',               [WebsiteStaffMemberController::class, 'storeCategory'])->name('categories.store');
            Route::patch('/categories/{staffMemberCategory}', [WebsiteStaffMemberController::class, 'updateCategory'])->name('categories.update');
            Route::delete('/categories/{staffMemberCategory}',[WebsiteStaffMemberController::class, 'destroyCategory'])->name('categories.destroy');
        });

        // ── Job Posts (Reusable Content) ──────────────────────────────────────────
        Route::prefix('job-posts')->name('job-posts.')->group(function () {
            Route::get('/',                          [WebsiteJobPostController::class, 'index'])->name('index');
            Route::post('/',                         [WebsiteJobPostController::class, 'store'])->name('store');
            Route::patch('/{jobPost}',               [WebsiteJobPostController::class, 'update'])->name('update');
            Route::delete('/{jobPost}',              [WebsiteJobPostController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-update',              [WebsiteJobPostController::class, 'bulkUpdate'])->name('bulk-update');
            Route::post('/categories',               [WebsiteJobPostController::class, 'storeCategory'])->name('categories.store');
            Route::patch('/categories/{jobPostCategory}', [WebsiteJobPostController::class, 'updateCategory'])->name('categories.update');
            Route::delete('/categories/{jobPostCategory}',[WebsiteJobPostController::class, 'destroyCategory'])->name('categories.destroy');
        });

        // ── Service Offers (Reusable Content) ──────────────────────────────────────────
        Route::prefix('service-offers')->name('service-offers.')->group(function () {
            Route::get('/',                          [WebsiteServiceOfferController::class, 'index'])->name('index');
            Route::post('/',                         [WebsiteServiceOfferController::class, 'store'])->name('store');
            Route::patch('/{serviceOffer}',          [WebsiteServiceOfferController::class, 'update'])->name('update');
            Route::delete('/{serviceOffer}',         [WebsiteServiceOfferController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-update',              [WebsiteServiceOfferController::class, 'bulkUpdate'])->name('bulk-update');
            Route::post('/categories',               [WebsiteServiceOfferController::class, 'storeCategory'])->name('categories.store');
            Route::patch('/categories/{serviceOfferCategory}', [WebsiteServiceOfferController::class, 'updateCategory'])->name('categories.update');
            Route::delete('/categories/{serviceOfferCategory}',[WebsiteServiceOfferController::class, 'destroyCategory'])->name('categories.destroy');
        });

        // ── Events (Reusable Content) ──────────────────────────────────────────
        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/',                          [WebsiteEventController::class, 'index'])->name('index');
            Route::post('/',                         [WebsiteEventController::class, 'store'])->name('store');
            Route::patch('/{event}',                 [WebsiteEventController::class, 'update'])->name('update');
            Route::delete('/{event}',                [WebsiteEventController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-update',              [WebsiteEventController::class, 'bulkUpdate'])->name('bulk-update');
            Route::post('/categories',               [WebsiteEventController::class, 'storeCategory'])->name('categories.store');
            Route::patch('/categories/{eventCategory}', [WebsiteEventController::class, 'updateCategory'])->name('categories.update');
            Route::delete('/categories/{eventCategory}',[WebsiteEventController::class, 'destroyCategory'])->name('categories.destroy');
        });

        // ── Form Entries ──────────────────────────────────────────────────────────────
        Route::prefix('form-entries')->name('form-entries.')->group(function () {
            Route::get('/',              [WebsiteFormController::class, 'index'])->name('index');
            Route::get('/{formEntry}',   [WebsiteFormController::class, 'show'])->name('show');
            Route::delete('/',           [WebsiteFormController::class, 'bulkDestroy'])->name('bulk-destroy');
            Route::delete('/{formEntry}',[WebsiteFormController::class, 'destroy'])->name('destroy');
            Route::patch('/{formEntry}/read',   [WebsiteFormController::class, 'markAsRead'])->name('read');
            Route::patch('/{formEntry}/unread', [WebsiteFormController::class, 'markAsUnread'])->name('unread');
            Route::patch('/bulk-read',   [WebsiteFormController::class, 'bulkMarkAsRead'])->name('bulk-read');
            Route::get('/export',        [WebsiteFormController::class, 'export'])->name('export');
        });

        // ── Settings ──────────────────────────────────────────────────────────────
        Route::prefix('settings')->name('settings.')->group(function () {
            // General
            Route::get('/',           [WebsiteSettingController::class, 'general'])->name('general');
            Route::patch('/general',  [WebsiteSettingController::class, 'updateGeneral'])->name('general.update');
            Route::patch('/disclaimers', [WebsiteSettingController::class, 'updateDisclaimers'])->name('disclaimers.update');
            Route::patch('/social',   [WebsiteSettingController::class, 'updateSocial'])->name('social.update');

            // Locations & Hours
            Route::get('/locations',  [WebsiteSettingController::class, 'locations'])->name('locations');

            Route::prefix('locations')->name('locations.')->group(function () {
                Route::post('/', [WebsiteSettingController::class, 'storeLocation'])->name('store');
                Route::patch('/{location}', [WebsiteSettingController::class, 'updateLocation'])->name('update');
                Route::delete('/{location}', [WebsiteSettingController::class, 'destroyLocation'])->name('destroy');
                Route::post('/reorder', [WebsiteSettingController::class, 'reorderLocations'])->name('reorder');
                Route::get('/{location}/edit', [WebsiteSettingController::class, 'editLocation'])->name('edit');
            });

            // Banners & Announcements
            Route::get('/banners',    [WebsiteSettingController::class, 'banners'])->name('banners');
            Route::patch('/banners',  [WebsiteSettingController::class, 'updateBanners'])->name('banners.update');

            // Inerest Rates (Finance)
            Route::get('/finance',    [WebsiteSettingController::class, 'finance'])->name('finance');

            // Digital Retails
            Route::get('/retail',     [WebsiteSettingController::class, 'retail'])->name('retail');
            Route::patch('/retail',   [WebsiteSettingController::class, 'updateDigitalRetail'])->name('retail.update');

            // Redirects
            Route::get('/redirects',  [WebsiteSettingController::class, 'redirects'])->name('redirects');
            Route::post('/redirects', [WebsiteSettingController::class, 'storeRedirect'])->name('redirects.store');
            Route::patch('/redirects/{redirect}', [WebsiteSettingController::class, 'updateRedirect'])->name('redirects.update');
            Route::delete('/redirects/{redirect}', [WebsiteSettingController::class, 'destroyRedirect'])->name('redirects.destroy');
            Route::post('/redirects/import', [WebsiteSettingController::class, 'importRedirects'])->name('redirects.import');
            Route::get('/redirects/sample', [WebsiteSettingController::class, 'downloadSampleCsv'])->name('redirects.sample');

            // Dealer IPs
            Route::prefix('ips')->name('ips.')->group(function () {
                Route::get('/', [WebsiteSettingController::class, 'ips'])->name('index');
                Route::post('/', [WebsiteSettingController::class, 'storeDealerIp'])->name('store');
                Route::patch('/{dealerIp}', [WebsiteSettingController::class, 'updateDealerIp'])->name('update');
                Route::delete('/{dealerIp}', [WebsiteSettingController::class, 'destroyDealerIp'])->name('destroy');
            });

            // Domains
            // Route::get('/domains',    [WebsiteSettingController::class, 'domains'])->name('domains');
        });

    });

    // ─── Inventory ────────────────────────────────────────────────────────────

    Route::prefix('inventory')->name('inventory.')->group(function () {

        // Listing page
        Route::get('/',          [InventoryController::class, 'index'])->name('index');

        // Dashboard / Reports
        Route::get('/dashboard', [InventoryController::class, 'dashboard'])->name('dashboard');

        // Incentives
        Route::prefix('incentives')->name('incentives.')->group(function () {
            Route::get('/',               [IncentiveController::class, 'index'])->name('index');
            Route::post('/',              [IncentiveController::class, 'store'])->name('store');
            Route::patch('/{incentive}',  [IncentiveController::class, 'update'])->name('update');
            Route::delete('/{incentive}', [IncentiveController::class, 'destroy'])->name('destroy');
        });

        // Pricing Specials
        Route::prefix('pricing-specials')->name('pricing-specials.')->group(function () {
            Route::get('/match-count',             [PricingSpecialController::class, 'matchCount'])->name('match-count');
            Route::get('/',                        [PricingSpecialController::class, 'index'])->name('index');
            Route::post('/',                       [PricingSpecialController::class, 'store'])->name('store');
            Route::patch('/{pricingSpecial}',      [PricingSpecialController::class, 'update'])->name('update');
            Route::patch('/{pricingSpecial}/toggle', [PricingSpecialController::class, 'toggle'])->name('toggle');
            Route::post('/{pricingSpecial}/duplicate', [PricingSpecialController::class, 'duplicate'])->name('duplicate');
            Route::delete('/{pricingSpecial}',     [PricingSpecialController::class, 'destroy'])->name('destroy');
        });

        // VIN decode — AJAX only
        Route::post('/vin-decode', [InventoryController::class, 'vinDecode'])->name('vin-decode');
        Route::get('/models',      [InventoryController::class, 'getModels'])->name('models');
        // Create new vehicle (VIN modal submit)
        Route::post('/', [InventoryController::class, 'store'])->name('store');

        // ── VDP (Vehicle Detail Page) ─────────────────────────────────────────
        Route::prefix('/vdp/{vehicle}')->name('vdp.')->group(function () {

            // Main VDP page (default tab: Pricing)
            Route::get('/', [InventoryController::class, 'show'])->name('show');

            // Tab updates
            Route::patch('/pricing', [InventoryController::class, 'updatePricing'])->name('pricing');
            Route::patch('/details', [InventoryController::class, 'updateDetails'])->name('details');

            // Photos / Gallery
            Route::prefix('/gallery')->name('gallery.')->group(function () {
                Route::get('/',              [InventoryController::class, 'gallery'])->name('show');
                Route::post('/upload',       [InventoryController::class, 'uploadPhotos'])->name('upload');
                Route::patch('/{photo}/status', [InventoryController::class, 'updatePhotoStatus'])->name('photo.status');
                Route::post('/reorder',      [InventoryController::class, 'reorderPhotos'])->name('reorder');
                Route::delete('/{photo}',    [InventoryController::class, 'deletePhoto'])->name('photo.destroy');
                Route::delete('/',           [InventoryController::class, 'bulkDeletePhotos'])->name('bulk.destroy');
                Route::patch('/{photo}/primary', [InventoryController::class, 'setPhotoAsPrimary'])->name('photo.primary');
                Route::get('/download', [InventoryController::class, 'downloadPhotos'])->name('download');
            });

            Route::patch('/video', [InventoryController::class, 'updateVideo'])->name('video');
            Route::patch('/tags',    [InventoryController::class, 'updateTags'])->name('tags');
            Route::patch('/notes',   [InventoryController::class, 'updateNotes'])->name('notes');
            Route::patch('/factory-options', [InventoryController::class, 'updateFactoryOptions'])->name('factory-options');

            // Premium build options
            Route::prefix('/premium-options')->name('premium-options.')->group(function () {
                Route::post('/',           [InventoryController::class, 'storePremiumOption'])->name('store');
                Route::patch('/{option}',  [InventoryController::class, 'updatePremiumOption'])->name('update');
                Route::delete('/{option}', [InventoryController::class, 'destroyPremiumOption'])->name('destroy');
            });

            // Right sidebar (status, toggles, feed setting)
            Route::patch('/status',  [InventoryController::class, 'updateStatus'])->name('status');

            // Delete vehicle
            Route::delete('/', [InventoryController::class, 'destroy'])->name('destroy');

            // ── VDP Incentives (hide/unhide per vehicle) ──
            Route::post('/incentives/{incentive}/hide',   [InventoryController::class, 'hideIncentive'])->name('incentive.hide');
            Route::delete('/incentives/{incentive}/hide', [InventoryController::class, 'unhideIncentive'])->name('incentive.unhide');

            // ── Printables ──────────────────────────────────────────────────────
            Route::prefix('/printables')->name('printables.')->group(function () {
                Route::get('/',                            [PrintableController::class, 'index'])  ->name('index');
                Route::post('/',                           [PrintableController::class, 'store'])  ->name('store');
                Route::patch('/{printable}',               [PrintableController::class, 'update']) ->name('update');
                Route::delete('/{printable}',              [PrintableController::class, 'destroy'])->name('destroy');
                Route::get('/{printable}/render',          [PrintableController::class, 'render']) ->name('render');
            });

        });

        // ── Inventory Settings ────────────────────────────────────────────────
        Route::prefix('settings')->name('settings.')->group(function () {
            // ── Interest Rates ──
            Route::prefix('rates')->name('rates.')->group(function () {
                Route::get('/',              [InventorySettingController::class, 'rates'])->name('index');
                Route::post('/sync',         [InventorySettingController::class, 'syncRates'])->name('sync');
                Route::post('/',             [InventorySettingController::class, 'storeRate'])->name('store');
                Route::patch('/bulk',        [InventorySettingController::class, 'bulkUpdateRates'])->name('bulk-update');
                Route::post('/{rate}/clone', [InventorySettingController::class, 'cloneRate'])->name('clone');
                Route::delete('/{rate}',     [InventorySettingController::class, 'destroyRate'])->name('destroy');
            });

            // ── Fees ──
            Route::prefix('fees')->name('fees.')->group(function () {
                Route::get('/',              [InventorySettingController::class, 'fees'])->name('index');
                Route::post('/',             [InventorySettingController::class, 'storeFee'])->name('store');
                Route::patch('/{fee}',       [InventorySettingController::class, 'updateFee'])->name('update');
                Route::delete('/{fee}',      [InventorySettingController::class, 'destroyFee'])->name('destroy');
                Route::post('/reorder',      [InventorySettingController::class, 'reorderFees'])->name('reorder');
            });

            // ── Other Settings ──
            Route::get('/syndication', [InventorySettingController::class, 'syndication'])->name('syndication');
        });

    });

    // ─── Connections ──────────────────────────────────────────────────────────

    Route::prefix('connections')->name('connections.')->group(function () {
        Route::get('/apps',  [ConnectionController::class, 'apps'])->name('apps');
        Route::get('/links', [ConnectionController::class, 'links'])->name('links');
    });

    // ─── Settings ─────────────────────────────────────────────────────────────

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/',                  [SettingController::class, 'profile'])->name('profile');
        Route::patch('/',         [SettingController::class, 'updateProfile'])->name('profile.update');
        Route::patch('/password',        [SettingController::class, 'updatePassword'])->name('password.update');
        Route::get('/authentication',    [SettingController::class, 'authentication'])->name('authentication');
        Route::get('/security',          [SettingController::class, 'security'])->name('security');
        Route::patch('/security',        [SettingController::class, 'updateSecurity'])->name('security.update');
    });

});