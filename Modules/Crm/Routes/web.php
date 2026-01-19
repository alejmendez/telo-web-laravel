<?php

use Illuminate\Support\Facades\Route;
use Modules\Crm\Http\Controllers\ServicesController;
use Modules\Crm\Http\Controllers\RequestsController;
use Modules\Crm\Http\Controllers\ProfessionalsController;
use Modules\Crm\Http\Controllers\CustomersController;
use Modules\Crm\Http\Controllers\UrgencyTypesController;
use Modules\Crm\Http\Controllers\CategoriesController;
use Modules\Crm\Http\Controllers\CustomerTypesController;
use Modules\Crm\Http\Controllers\ProfessionalTypesController;
use Modules\Crm\Http\Controllers\ContactTypesController;
use Modules\Crm\Http\Controllers\LocationsController;

Route::middleware('throttle:web')->prefix('backoffice')->group(function () {
    Route::middleware(['auth', 'check.permission'])->group(function () {
        Route::resources([
            'services' => ServicesController::class,
            'requests' => RequestsController::class,
            'professionals' => ProfessionalsController::class,
            'customers' => CustomersController::class,
            'urgencytypes' => UrgencyTypesController::class,
            'categories' => CategoriesController::class,
            'customertypes' => CustomerTypesController::class,
            'professionaltypes' => ProfessionalTypesController::class,
            'contacttypes' => ContactTypesController::class,
            'locations' => LocationsController::class,
        ]);
    });
});
