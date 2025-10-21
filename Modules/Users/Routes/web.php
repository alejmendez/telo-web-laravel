<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\ProfileController;
use Modules\Users\Http\Controllers\UsersController;

Route::middleware('throttle:web')->prefix('backoffice')->group(function () {
    Route::middleware(['auth', 'check.permission'])->group(function () {
        Route::resources([
            'users' => UsersController::class,
        ]);

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});
