<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Secure Session Middleware
 * Handles session security without breaking existing functionality
 */
class SecureSession
{
    /**
     * Session timeout in seconds (30 minutes)
     */
    protected $timeout = 1800;
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Regenerate session ID on login to prevent session fixation
        if ($request->isMethod('post') && $request->is('login')) {
            $response = $next($request);
            
            if (Auth::check()) {
                $request->session()->regenerate();
                
                // Store login time and IP
                $request->session()->put('login_time', time());
                $request->session()->put('login_ip', $request->ip());
                
                // Clear any previous failed attempts
                $request->session()->forget('failed_attempts');
            }
            
            return $response;
        }
        
        // Check for session timeout (only for authenticated users)
        if (Auth::check()) {
            $lastActivity = $request->session()->get('last_activity');
            
            // Check if session has expired due to inactivity
            if ($lastActivity && (time() - $lastActivity) > $this->timeout) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect('/login')
                    ->with('message', 'Your session has expired due to inactivity. Please login again.')
                    ->with('alert-type', 'warning');
            }
            
            // Check for IP address change (session hijacking prevention)
            $loginIp = $request->session()->get('login_ip');
            if ($loginIp && $loginIp !== $request->ip()) {
                // Log potential session hijacking attempt
                \Log::channel('security')->warning('Session IP mismatch detected', [
                    'user_id' => Auth::id(),
                    'original_ip' => $loginIp,
                    'current_ip' => $request->ip(),
                ]);
                
                // Optional: Invalidate session on IP change
                if (config('session.invalidate_on_ip_change', false)) {
                    Auth::logout();
                    $request->session()->invalidate();
                    
                    return redirect('/login')
                        ->with('error', 'Session security check failed. Please login again.');
                }
            }
            
            // Update last activity time
            $request->session()->put('last_activity', time());
            
            // Regenerate session ID periodically (every 15 minutes)
            $lastRegeneration = $request->session()->get('last_regeneration', 0);
            if (time() - $lastRegeneration > 900) {
                $request->session()->regenerate();
                $request->session()->put('last_regeneration', time());
            }
        }
        
        // Check for concurrent sessions (optional)
        if (Auth::check() && config('session.single_session', false)) {
            $this->checkConcurrentSessions($request);
        }
        
        $response = $next($request);
        
        // Set secure cookie attributes
        if (method_exists($response, 'withCookie')) {
            foreach ($response->headers->getCookies() as $cookie) {
                if ($cookie->getName() === config('session.cookie')) {
                    // Ensure session cookie has secure attributes
                    $response->headers->setCookie(
                        cookie(
                            $cookie->getName(),
                            $cookie->getValue(),
                            $cookie->getExpiresTime(),
                            $cookie->getPath(),
                            $cookie->getDomain(),
                            $request->secure(), // Secure flag
                            true, // HttpOnly
                            false, // Raw
                            $cookie->getSameSite() ?: 'lax' // SameSite
                        )
                    );
                }
            }
        }
        
        return $response;
    }
    
    /**
     * Check for concurrent sessions and invalidate old ones
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function checkConcurrentSessions(Request $request)
    {
        $userId = Auth::id();
        $currentSessionId = $request->session()->getId();
        
        // Store current session ID for user
        $userSessionKey = 'user_session_' . $userId;
        $storedSessionId = cache()->get($userSessionKey);
        
        if ($storedSessionId && $storedSessionId !== $currentSessionId) {
            // Different session detected, invalidate it
            \Log::channel('security')->info('Concurrent session detected', [
                'user_id' => $userId,
                'current_session' => $currentSessionId,
                'stored_session' => $storedSessionId,
            ]);
        }
        
        // Update stored session ID
        cache()->put($userSessionKey, $currentSessionId, now()->addMinutes(30));
    }
}
