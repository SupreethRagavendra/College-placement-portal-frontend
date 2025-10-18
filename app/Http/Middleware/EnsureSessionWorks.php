<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensure Session Works Middleware
 * 
 * This middleware ensures sessions are working properly before
 * processing requests that require session functionality.
 * It provides better error messages than generic 500 errors.
 */
class EnsureSessionWorks
{
    /**
     * Routes that require session functionality
     */
    protected $sessionRoutes = [
        'login',
        'register',
        'logout',
        'password/reset',
        'verification/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only test session for routes that need it
        if (!$this->requiresSession($request)) {
            return $next($request);
        }

        try {
            // Test if session is working
            if (!$request->session()->isStarted()) {
                $request->session()->start();
            }

            // Try to write and read from session
            $testKey = '_session_test_' . time();
            $testValue = 'working';
            
            $request->session()->put($testKey, $testValue);
            $retrieved = $request->session()->get($testKey);
            $request->session()->forget($testKey);

            // Verify session read/write works
            if ($retrieved !== $testValue) {
                throw new \RuntimeException('Session write/read verification failed');
            }

            // Session is working, proceed with request
            return $next($request);

        } catch (\Exception $e) {
            Log::error('Session middleware detected session failure', [
                'error' => $e->getMessage(),
                'url' => $request->url(),
                'method' => $request->method(),
                'driver' => config('session.driver'),
                'ip' => $request->ip(),
            ]);

            // Return user-friendly error
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Session initialization failed',
                    'message' => 'Unable to start session. Please try clearing your browser cookies.',
                    'details' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            // For web requests, redirect back with error
            return back()->withErrors([
                'session' => 'Session initialization failed. Please clear your browser cookies and try again. If the problem persists, contact support.',
            ])->withInput($request->except(['password', 'password_confirmation', '_token']));
        }
    }

    /**
     * Check if the request requires session functionality
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function requiresSession(Request $request): bool
    {
        // Check if current route matches any session-required routes
        foreach ($this->sessionRoutes as $route) {
            if ($request->is($route) || $request->routeIs($route)) {
                return true;
            }
        }

        // POST requests to authenticated routes need sessions
        if ($request->isMethod('POST') && !$request->is('api/*')) {
            return true;
        }

        return false;
    }
}

