<?php

use Illuminate\Support\Facades\Route;
use Modules\Crm\Http\Controllers\LocationsController;

Route::middleware('throttle:web')->prefix('backoffice')->group(function () {
    Route::middleware(['auth', 'check.permission'])->group(function () {
        Route::resources([
            'locations' => LocationsController::class,
        ]);
    });
});
