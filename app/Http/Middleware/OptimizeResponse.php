<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptimizeResponse
{
    /**
     * Handle an incoming request and optimize the response
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only compress HTML, CSS, JS, JSON responses
        $contentType = $response->headers->get('Content-Type', '');
        $shouldCompress = str_contains($contentType, 'text/') || 
                         str_contains($contentType, 'application/json') ||
                         str_contains($contentType, 'application/javascript');

        if ($shouldCompress && 
            !$response->headers->has('Content-Encoding') &&
            $response->getStatusCode() === 200) {
            
            $content = $response->getContent();
            
            // Only compress if content is larger than 1KB
            if (strlen($content) > 1024) {
                // Check if client supports gzip
                $acceptEncoding = $request->header('Accept-Encoding', '');
                
                if (str_contains($acceptEncoding, 'gzip')) {
                    $compressed = gzencode($content, 6); // Compression level 6
                    
                    if ($compressed !== false) {
                        $response->setContent($compressed);
                        $response->headers->set('Content-Encoding', 'gzip');
                        $response->headers->set('Content-Length', strlen($compressed));
                    }
                }
            }
        }

        // Add caching headers for static assets
        if ($this->isStaticAsset($request)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        }

        // Add security and performance headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Enable browser caching for pages (5 minutes)
        if (!$this->isStaticAsset($request) && $response->getStatusCode() === 200) {
            $response->headers->set('Cache-Control', 'public, max-age=300, must-revalidate');
        }

        return $response;
    }

    /**
     * Check if the request is for a static asset
     */
    private function isStaticAsset(Request $request): bool
    {
        $path = $request->path();
        $staticExtensions = ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'woff', 'woff2', 'ttf', 'eot'];
        
        foreach ($staticExtensions as $ext) {
            if (str_ends_with($path, '.' . $ext)) {
                return true;
            }
        }
        
        return false;
    }
}

