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

    });