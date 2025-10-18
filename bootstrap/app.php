<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/assessment.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'optimize' => \App\Http\Middleware\OptimizeResponse::class,
            'ensure.session' => \App\Http\Middleware\EnsureSessionWorks::class,
            'cache.aggressive' => \App\Http\Middleware\AggressiveCaching::class,
        ]);
        
        // Trust proxies for Render deployment
        $middleware->trustProxies(at: '*');
        
        // Use custom CSRF middleware for better debugging
        $middleware->validateCsrfTokens(except: [
            // Add any routes that should be excluded from CSRF verification
        ]);
        
        // Add response optimization to web middleware
        $middleware->web(append: [
            \App\Http\Middleware\OptimizeResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
