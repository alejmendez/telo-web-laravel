<?php

use Illuminate\Support\Facades\Route;
use Modules\Crm\Http\Controllers\CitiesController;
use Modules\Crm\Http\Controllers\CountriesController;

Route::middleware('throttle:web')->prefix('backoffice')->group(function () {
    Route::middleware(['auth', 'check.permission'])->group(function () {
        Route::resources([
            'cities' => CitiesController::class,
            'countries' => CountriesController::class,
        ]);
    });
});
