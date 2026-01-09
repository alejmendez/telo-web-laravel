<?php

namespace Modules\Crm\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Crm\Services\CityService;
use Modules\Crm\Services\CountryService;

class CrmServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'Crm');
        $this->loadTranslationsFrom(__DIR__.'/../Lang');
        $this->loadJsonTranslationsFrom(__DIR__.'/../Lang');

        Route::middleware('web')->group(base_path('Modules/Crm/Routes/web.php'));
        Route::middleware('api')->group(base_path('Modules/Crm/Routes/api.php'));

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

        $this->app->singleton(CountryService::class, function (Application $app) {
            return new CountryService();
        });

        $this->app->singleton(CityService::class, function (Application $app) {
            return new CityService();
        });
    }
}
