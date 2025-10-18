<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Security Headers Middleware
 * Adds security headers to all responses without breaking functionality
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Only add headers if response supports it
        if (method_exists($response, 'header')) {
            // Prevent clickjacking attacks
            $response->header('X-Frame-Options', 'SAMEORIGIN');
            
            // Prevent MIME type sniffing
            $response->header('X-Content-Type-Options', 'nosniff');
            
            // Enable XSS protection in older browsers
            $response->header('X-XSS-Protection', '1; mode=block');
            
            // Control referrer information
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
            
            // Restrict browser features
            $response->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
            
            // Force HTTPS (only in production)
            if ($request->secure() || config('app.env') === 'production') {
                $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            }
            
            // Content Security Policy - Permissive to avoid breaking existing functionality
            $csp = $this->buildContentSecurityPolicy($request);
            $response->header('Content-Security-Policy', $csp);
            
            // Remove server header if possible
            $response->headers->remove('X-Powered-By');
            $response->headers->remove('Server');
        }
        
        return $response;
    }
    
    /**
     * Build Content Security Policy based on environment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function buildContentSecurityPolicy(Request $request): string
    {
        $policies = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net",
            "img-src 'self' data: https: blob:",
            "connect-src 'self' http://localhost:8001 http://localhost:8000 ws: wss:",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
        ];
        
        // Add nonce for inline scripts if needed
        if (config('app.debug')) {
            $policies[] = "report-uri /csp-report";
        }
        
        return implode('; ', $policies);
    }
}
