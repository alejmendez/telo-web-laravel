<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\DashboardController;

Route::middleware('throttle:web')->prefix('backoffice')->group(function () {
    Route::middleware(['auth', 'check.permission'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    });
});
