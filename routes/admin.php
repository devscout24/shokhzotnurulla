<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'all.active', 'isAdmin'])
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Dealers CRUD
        Route::resource('dealers', \App\Http\Controllers\Admin\DealerController::class);
        Route::patch('dealers/{dealer}/toggle-status', [\App\Http\Controllers\Admin\DealerController::class, 'toggleStatus'])
            ->name('dealers.toggle-status');

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
        Route::post('integrations/{integration}/test', [\App\Http\Controllers\Admin\IntegrationController::class, 'test'])
            ->name('dealers.integrations.test');

    });