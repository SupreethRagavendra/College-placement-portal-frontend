<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Input Sanitization Middleware
 * Sanitizes user input without breaking functionality
 */
class SanitizeInput
{
    /**
     * Fields that should not be sanitized
     */
    protected $except = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        '_token',
        'api_key',
        'secret',
    ];
    
    /**
     * Fields that allow HTML content
     */
    protected $allowHtml = [
        'description',
        'content',
        'message',
        'bio',
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip sanitization for file uploads and API routes
        if ($request->is('api/*') || $request->hasFile('file')) {
            return $next($request);
        }
        
        $input = $request->all();
        
        // Recursively sanitize input
        array_walk_recursive($input, function (&$value, $key) {
            if ($this->shouldSanitize($key, $value)) {
                $value = $this->sanitizeValue($value, $key);
            }
        });
        
        // Merge sanitized input back
        $request->merge($input);
        
        // Log suspicious patterns
        $this->detectSuspiciousPatterns($request);
        
        return $next($request);
    }
    
    /**
     * Check if a field should be sanitized
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return bool
     */
    protected function shouldSanitize($key, $value): bool
    {
        // Don't sanitize excluded fields
        if (in_array($key, $this->except)) {
            return false;
        }
        
        // Only sanitize string values
        if (!is_string($value)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Sanitize a value based on its key
     *
     * @param  string  $value
     * @param  string  $key
     * @return string
     */
    protected function sanitizeValue($value, $key): string
    {
        // Remove null bytes
        $value = str_replace(chr(0), '', $value);
        
        // Trim whitespace
        $value = trim($value);
        
        // Handle HTML content fields differently
        if (in_array($key, $this->allowHtml)) {
            // Allow basic HTML but remove scripts
            $value = $this->sanitizeHtml($value);
        } else {
            // Strip all HTML tags for regular fields
            $value = strip_tags($value);
        }
        
        // Remove control characters except newlines and tabs
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
        
        // Normalize line breaks
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        
        // Prevent directory traversal
        if (strpos($key, 'path') !== false || strpos($key, 'file') !== false) {
            $value = str_replace(['../', '..\\', '..'], '', $value);
        }
        
        // Sanitize email fields
        if (strpos($key, 'email') !== false) {
            $value = filter_var($value, FILTER_SANITIZE_EMAIL);
        }
        
        // Sanitize URL fields
        if (strpos($key, 'url') !== false || strpos($key, 'link') !== false) {
            $value = filter_var($value, FILTER_SANITIZE_URL);
        }
        
        return $value;
    }
    
    /**
     * Sanitize HTML content while preserving safe tags
     *
     * @param  string  $html
     * @return string
     */
    protected function sanitizeHtml($html): string
    {
        // Remove script tags and their content
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        
        // Remove iframe tags
        $html = preg_replace('#<iframe(.*?)>(.*?)</iframe>#is', '', $html);
        
        // Remove event handlers
        $html = preg_replace('/on\w+\s*=\s*"[^"]*"/i', '', $html);
        $html = preg_replace("/on\w+\s*=\s*'[^']*'/i", '', $html);
        $html = preg_replace('/on\w+\s*=\s*[^\s>]*/i', '', $html);
        
        // Remove javascript: protocol
        $html = preg_replace('/javascript\s*:/i', '', $html);
        
        // Remove data: protocol (except for images)
        $html = preg_replace('/data:(?!image\/)/i', '', $html);
        
        // Allow only safe HTML tags
        $allowedTags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><blockquote><code><pre><table><thead><tbody><tr><td><th>';
        $html = strip_tags($html, $allowedTags);
        
        return $html;
    }
    
    /**
     * Detect and log suspicious patterns in input
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function detectSuspiciousPatterns(Request $request)
    {
        $suspiciousPatterns = [
            '/(<script|<iframe|javascript:|onerror=|onload=)/i' => 'XSS attempt',
            '/(union\s+select|drop\s+table|insert\s+into|update\s+set)/i' => 'SQL injection attempt',
            '/(\.\.\/|\.\.\\\\|%2e%2e%2f)/i' => 'Directory traversal attempt',
            '/(eval\(|exec\(|system\(|shell_exec\()/i' => 'Code execution attempt',
            '/(\$\{.*\}|{{.*}}|<%.*%>)/i' => 'Template injection attempt',
        ];
        
        $input = json_encode($request->all());
        
        foreach ($suspiciousPatterns as $pattern => $type) {
            if (preg_match($pattern, $input)) {
                \Log::channel('security')->warning('Suspicious input detected', [
                    'type' => $type,
                    'pattern' => $pattern,
                    'ip' => $request->ip(),
                    'user_id' => auth()->id(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ]);
                
                // Optional: Block request if critical pattern detected
                if (in_array($type, ['SQL injection attempt', 'Code execution attempt'])) {
                    // Don't block in development
                    if (config('app.env') === 'production') {
                        abort(403, 'Security violation detected');
                    }
                }
                
                break; // Log only first match to avoid spam
            }
        }
    }
}
