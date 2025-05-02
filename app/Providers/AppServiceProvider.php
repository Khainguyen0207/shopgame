<?php

namespace App\Providers;

use App\Helpers\ConfigHelper;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register ConfigHelper alias
        $this->app->bind('config-helper', function () {
            return new ConfigHelper();
        });
        //
        Paginator::defaultView('vendor.pagination.default');

    }
}
