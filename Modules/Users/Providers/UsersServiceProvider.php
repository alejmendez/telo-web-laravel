<?php

namespace Modules\Users\Providers;

use Illuminate\Support\Facades\Route;
use Modules\Core\Providers\CoreServiceProvider;
use Modules\Core\Services\Contracts\CacheServiceContract;
use Modules\Users\Services\Contracts\RoleServiceContract;
use Modules\Users\Services\Contracts\UserServiceContract;
use Modules\Users\Services\RoleService;
use Modules\Users\Services\UserService;

class UsersServiceProvider extends CoreServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(UserServiceContract::class, function ($app) {
            return new UserService($app->make(CacheServiceContract::class));
        });

        $this->app->singleton(RoleServiceContract::class, function ($app) {
            return new RoleService;
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
        Route::middleware('web')->group(base_path('Modules/Users/Routes/web.php'));
    }
}
