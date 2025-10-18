<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FastAuthService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register our fast authentication service
        $this->app->singleton(FastAuthService::class, function ($app) {
            return new FastAuthService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}