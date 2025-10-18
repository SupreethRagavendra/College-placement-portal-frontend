<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AggressiveCaching
{
    /**
     * Cache responses aggressively for maximum speed
     */
    public function handle(Request $request, Closure $next, int $minutes = 5): Response
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Don't cache if user just logged in or performed an action
        if ($request->session()->has('status') || $request->session()->has('error')) {
            return $next($request);
        }

        // Create cache key based on URL and user
        $key = 'page_cache_' . md5($request->url() . (auth()->id() ?? 'guest'));

        // Return cached response or cache new response
        return Cache::remember($key, now()->addMinutes($minutes), function () use ($next, $request) {
            return $next($request);
        });
    }
}

