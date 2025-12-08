<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\ForbiddenController;
use Modules\Core\Http\Controllers\NotificationsController;
use Modules\Core\Http\Controllers\StartController;
use Modules\Core\Services\Contracts\CacheServiceContract;

Route::middleware('throttle:web')->get('health-check', function () {
    return response()->json(['status' => 'ok']);
});

Route::middleware('throttle:web')->prefix('backoffice')->group(function () {
    Route::get('/', function (CacheServiceContract $cacheService) {
        if (Auth::check()) {
            $userPermissions = $cacheService->getUserPermissions(Auth::user());
            if ($userPermissions->contains('dashboard.index')) {
                return redirect()->route('dashboard.index');
            }

            return redirect()->route('start.index');
        }

        return redirect()->route('login');
    });

    Route::middleware(['auth', 'check.permission'])->group(function () {
        Route::get('/start', [StartController::class, 'index'])->name('start.index');

        Route::get('/notifications/{type}/unread', [NotificationsController::class, 'unread'])->name('notifications.unread');

        Route::get('/forbidden', [ForbiddenController::class, 'forbidden'])->name('forbidden');
    });
});
