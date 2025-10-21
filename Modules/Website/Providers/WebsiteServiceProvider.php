<?php

namespace Modules\Website\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WebsiteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'website');
        Route::middleware('throttle:web')->group(base_path('Modules/Website/Routes/web.php'));
    }
}
