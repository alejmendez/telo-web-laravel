<?php

namespace Modules\Core\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Http\Middleware\CheckPermission;
use Modules\Core\Services\CacheService;
use Modules\Core\Services\Contracts\CacheServiceContract;
use Modules\Core\Services\Contracts\ListNotificationContract;
use Modules\Core\Services\Contracts\MenuServiceContract;
use Modules\Core\Services\ListNotification;
use Modules\Core\Services\MenuService;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CacheServiceContract::class, CacheService::class);
        $this->app->singleton(ListNotificationContract::class, ListNotification::class);
        $this->app->singleton(MenuServiceContract::class, MenuService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'core');
        $this->loadTranslationsFrom(__DIR__.'/../Lang');
        $this->loadJsonTranslationsFrom(__DIR__.'/../Lang');
        Route::middleware('web')->group(base_path('Modules/Core/Routes/web.php'));

        // Registrar middleware personalizado
        $this->app['router']->aliasMiddleware('check.permission', CheckPermission::class);

        if ($this->app->runningInConsole()) {
            Factory::guessFactoryNamesUsing(function (string $modelName) {
                if (strpos($modelName, 'Modules\\') === 0) {
                    $parts = explode('\\', $modelName);
                    $moduleName = $parts[1];

                    return "Modules\\{$moduleName}\\Database\\Factories\\".class_basename($modelName).'Factory';
                }

                return 'Database\\Factories\\'.class_basename($modelName).'Factory';
            });
        }
    }
}
