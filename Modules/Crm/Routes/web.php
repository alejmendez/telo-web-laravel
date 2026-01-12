<?php

use Illuminate\Support\Facades\Route;
use Modules\Crm\Http\Controllers\ProfessionalTypesController;
use Modules\Crm\Http\Controllers\ContactTypesController;
use Modules\Crm\Http\Controllers\LocationsController;

Route::middleware('throttle:web')->prefix('backoffice')->group(function () {
    Route::middleware(['auth', 'check.permission'])->group(function () {
        Route::resources([
            'professionaltypes' => ProfessionalTypesController::class,
            'contacttypes' => ContactTypesController::class,
            'locations' => LocationsController::class,
        ]);
    });
});
