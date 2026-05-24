<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'all.active', 'isAdmin'])
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Dealers Export
        Route::get('dealers/export/csv', [\App\Http\Controllers\Admin\DealerExportController::class, 'exportCsv'])
            ->name('dealers.export.csv');

        // Dealers CRUD
        Route::resource('dealers', \App\Http\Controllers\Admin\DealerController::class);
        Route::patch('dealers/{dealer}/toggle-status', [\App\Http\Controllers\Admin\DealerController::class, 'toggleStatus'])
            ->name('dealers.toggle-status');
        Route::post('dealers/{dealer}/notify', [\App\Http\Controllers\Admin\DealerController::class, 'notify'])
            ->name('dealers.notify');
        Route::get('dealers/{dealer}/export-vehicles', [\App\Http\Controllers\Admin\DealerExportController::class, 'exportDealerVehiclesCsv'])
            ->name('dealers.export.vehicles');

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

    });