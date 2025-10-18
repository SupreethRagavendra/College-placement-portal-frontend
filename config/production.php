<?php

/**
 * Production Performance Configuration
 * 
 * This file contains production-specific optimizations
 * for the College Placement Portal
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Production Mode
    |--------------------------------------------------------------------------
    | Enable/disable production optimizations
    */
    'enabled' => env('PRODUCTION_MODE', true),
    
    /*
    |--------------------------------------------------------------------------
    | Asset Optimization
    |--------------------------------------------------------------------------
    */
    'assets' => [
        'minify' => true,
        'compress' => true,
        'cdn_enabled' => env('CDN_ENABLED', false),
        'cdn_url' => env('CDN_URL', ''),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Database Connection Pooling
    |--------------------------------------------------------------------------
    */
    'database' => [
        'pool_min' => env('DB_POOL_MIN', 2),
        'pool_max' => env('DB_POOL_MAX', 10),
        'persistent' => env('DB_PERSISTENT', true),
        'statement_cache_size' => env('DB_STATEMENT_CACHE', 250),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'views' => true,
        'routes' => true,
        'config' => true,
        'queries' => true,
        'default_ttl' => env('CACHE_TTL', 3600), // 1 hour
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Response Compression
    |--------------------------------------------------------------------------
    */
    'compression' => [
        'enabled' => true,
        'level' => 6, // 1-9, higher = better compression but slower
        'min_size' => 1024, // Minimum size in bytes to compress
    ],
    
    /*
    |--------------------------------------------------------------------------
    | OPCache Settings
    |--------------------------------------------------------------------------
    */
    'opcache' => [
        'enabled' => true,
        'memory_consumption' => 256, // MB
        'interned_strings_buffer' => 16, // MB
        'max_accelerated_files' => 20000,
        'validate_timestamps' => false, // Disable in production for best performance
    ],
];

