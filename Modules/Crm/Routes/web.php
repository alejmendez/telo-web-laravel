<?php

use Illuminate\Support\Facades\Route;
use Modules\Crm\Http\Controllers\CategoriesController;
use Modules\Crm\Http\Controllers\CustomersController;
use Modules\Crm\Http\Controllers\CustomerTypesController;
use Modules\Crm\Http\Controllers\LocationsController;
use Modules\Crm\Http\Controllers\ProfessionalsController;
use Modules\Crm\Http\Controllers\ProfessionalTypesController;
use Modules\Crm\Http\Controllers\RequestsController;
use Modules\Crm\Http\Controllers\ServicesController;
use Modules\Crm\Http\Controllers\UrgencyTypesController;

Route::middleware('throttle:web')->prefix('backoffice')->group(function () {
    Route::middleware(['auth', 'check.permission'])->group(function () {
        Route::resources([
            'categories' => CategoriesController::class,
            'customers' => CustomersController::class,
            'customertypes' => CustomerTypesController::class,
            'locations' => LocationsController::class,
            'professionals' => ProfessionalsController::class,
            'professionaltypes' => ProfessionalTypesController::class,
            'requests' => RequestsController::class,
            'services' => ServicesController::class,
            'urgencytypes' => UrgencyTypesController::class,
        ]);
    });
});
