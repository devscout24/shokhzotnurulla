<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'all.active', 'isAdmin', \App\Http\Middleware\EnsureTwoFactorAuthenticated::class])
    ->group(function () {

        // ─── 2FA ──────────────────────────────────────────────────────────────────
        Route::prefix('2fa')->name('2fa.')->group(function () {
            Route::get('/verify', [\App\Http\Controllers\Admin\TwoFactorController::class, 'showVerifyForm'])->name('verify');
            Route::post('/verify', [\App\Http\Controllers\Admin\TwoFactorController::class, 'verifyLogin'])->name('verify.post');
            Route::post('/enable', [\App\Http\Controllers\Admin\TwoFactorController::class, 'enable'])->name('enable');
            Route::post('/disable', [\App\Http\Controllers\Admin\TwoFactorController::class, 'disable'])->name('disable');
        });

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Dealers Export
        Route::get('dealers/export/csv', [\App\Http\Controllers\Admin\DealerExportController::class, 'exportCsv'])
            ->name('dealers.export.csv');
        Route::get('dealers/export/cargurus', [\App\Http\Controllers\Admin\DealerExportController::class, 'exportCarGurusCsv'])
            ->name('dealers.export.cargurus');
        Route::get('dealers/export/truecars', [\App\Http\Controllers\Admin\DealerExportController::class, 'exportTrueCarsCsv'])
            ->name('dealers.export.truecars');
        Route::get('dealers/export/carsdotcom', [\App\Http\Controllers\Admin\DealerExportController::class, 'exportCarsDotComCsv'])
            ->name('dealers.export.carsdotcom');
        Route::get('dealers/export/carfax', [\App\Http\Controllers\Admin\DealerExportController::class, 'exportCarFaxXml'])
            ->name('dealers.export.carfax');


        Route::get('dealers/{dealer}/export-vehicles', [\App\Http\Controllers\Admin\DealerExportController::class, 'exportDealerVehiclesCsv'])
            ->name('dealers.export.vehicles');
        Route::get('dealers/{dealer}/export-vehicles-carsforsale', [\App\Http\Controllers\Admin\DealerExportController::class, 'exportDealerVehiclesCarsForSales'])
            ->name('dealers.export.vehicles.carsforsale');

        Route::get('dealers/{dealer}/export-vehicles-carfax', [\App\Http\Controllers\Admin\DealerExportController::class, 'exportDealerVehiclesCarFax'])
            ->name('dealers.export.vehicles.carfax');
        Route::get('dealers/{dealer}/export-vehicles-truecars', [\App\Http\Controllers\Admin\DealerExportController::class, 'exportDealerVehiclesTrueCars'])
            ->name('dealers.export.vehicles.truecars');
        Route::get('dealers/{dealer}/export-vehicles-carsdotcom', [\App\Http\Controllers\Admin\DealerExportController::class, 'exportDealerVehiclesCarsDotCom'])
            ->name('dealers.export.vehicles.carsdotcom');

        // Dealers CRUD
        Route::resource('dealers', \App\Http\Controllers\Admin\DealerController::class);
        Route::patch('dealers/{dealer}/toggle-status', [\App\Http\Controllers\Admin\DealerController::class, 'toggleStatus'])
            ->name('dealers.toggle-status');
        Route::post('dealers/{dealer}/notify', [\App\Http\Controllers\Admin\DealerController::class, 'notify'])
            ->name('dealers.notify');



        // Profile
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])
            ->name('profile.update');

        // Integrations
        Route::get('dealers/{dealer}/integrations', [\App\Http\Controllers\Admin\IntegrationController::class, 'index'])
            ->name('dealers.integrations.index');
        Route::post('dealers/{dealer}/integrations', [\App\Http\Controllers\Admin\IntegrationController::class, 'save'])
            ->name('dealers.integrations.save');
        Route::delete('dealers/{dealer}/integrations/{provider}', [\App\Http\Controllers\Admin\IntegrationController::class, 'destroy'])
            ->name('dealers.integrations.destroy');
        Route::post('integrations/{integration}/test', [\App\Http\Controllers\Admin\IntegrationController::class, 'test'])
            ->name('dealers.integrations.test');

        // Integration Approval Workflow
        Route::get('integrations/pending', [\App\Http\Controllers\Admin\IntegrationController::class, 'pending'])
            ->name('integrations.pending');
        Route::patch('integrations/{integration}/approve', [\App\Http\Controllers\Admin\IntegrationController::class, 'approve'])
            ->name('integrations.approve');
        Route::patch('integrations/{integration}/reject', [\App\Http\Controllers\Admin\IntegrationController::class, 'reject'])
            ->name('integrations.reject');
        Route::patch('integrations/{integration}/revoke', [\App\Http\Controllers\Admin\IntegrationController::class, 'revoke'])
            ->name('integrations.revoke');

        // Admin Restricted Sites
        Route::get('restricted-sites', [\App\Http\Controllers\Admin\AdminRestrictedSiteController::class, 'index'])
            ->name('restricted-sites.index');
        Route::post('restricted-sites/setting', [\App\Http\Controllers\Admin\AdminRestrictedSiteController::class, 'updateSetting'])
            ->name('restricted-sites.setting');
        Route::post('restricted-sites', [\App\Http\Controllers\Admin\AdminRestrictedSiteController::class, 'store'])
            ->name('restricted-sites.store');
        Route::delete('restricted-sites/{id}', [\App\Http\Controllers\Admin\AdminRestrictedSiteController::class, 'destroy'])
            ->name('restricted-sites.destroy');

    });
