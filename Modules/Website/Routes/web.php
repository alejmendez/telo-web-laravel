<?php

use Illuminate\Support\Facades\Route;
use Modules\Website\Http\Controllers\WebsiteController;

Route::get('/', [WebsiteController::class, 'index'])->name('website.index');
