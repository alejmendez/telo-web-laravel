<?php

namespace Modules\Dashboard\Providers;

use Illuminate\Support\Facades\Route;
use Modules\Core\Providers\CoreServiceProvider;
use Modules\Dashboard\Services\ShowDashboard;

class DashboardServiceProvider extends CoreServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ShowDashboard::class, function ($app) {
            return new ShowDashboard;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadTranslationsFrom(__DIR__.'/../Lang');
        $this->loadJsonTranslationsFrom(__DIR__.'/../Lang');
        Route::middleware('web')->group(base_path('Modules/Dashboard/Routes/web.php'));
    }
}
