<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | RAG Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for OpenRouter AI RAG service integration
    |
    */
    
    'service_url' => env('RAG_SERVICE_URL', 'http://localhost:8001'),
    
    'timeout' => env('RAG_SERVICE_TIMEOUT', 30),
    
    'enabled' => env('RAG_ENABLED', true),
    
    'auto_sync' => env('RAG_AUTO_SYNC', true),
    
    /*
    |--------------------------------------------------------------------------
    | OpenRouter API Configuration
    |--------------------------------------------------------------------------
    */
    
    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
        'api_url' => env('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions'),
        'primary_model' => env('OPENROUTER_PRIMARY_MODEL', 'qwen/qwen-2.5-72b-instruct:free'),
        'fallback_model' => env('OPENROUTER_FALLBACK_MODEL', 'deepseek/deepseek-v3.1:free'),
        'temperature' => env('OPENROUTER_TEMPERATURE', 0.7),
        'max_tokens' => env('OPENROUTER_MAX_TOKENS', 1024),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Supabase Configuration
    |--------------------------------------------------------------------------
    */
    
    'supabase' => [
        'db_host' => env('SUPABASE_DB_HOST'),
        'db_port' => env('SUPABASE_DB_PORT', 5432),
        'db_name' => env('SUPABASE_DB_NAME'),
        'db_user' => env('SUPABASE_DB_USER'),
        'db_password' => env('SUPABASE_DB_PASSWORD'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    
    'cache' => [
        'enabled' => env('RAG_CACHE_ENABLED', true),
        'ttl' => env('RAG_CACHE_TTL', 300), // 5 minutes
    ],
    
];