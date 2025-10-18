<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supabase Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Supabase settings. You will need to get
    | these values from your Supabase project dashboard.
    |
    */

    'url' => env('SUPABASE_URL', 'https://wkqbukidxmzbgwauncrl.supabase.co'),
    'anon_key' => env('SUPABASE_ANON_KEY', 'your_anon_key_here'),
    'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY', 'your_service_role_key_here'),
    
    // Email verification redirect URL
    'redirect_url' => env('APP_URL', 'http://localhost:8000') . '/auth/callback',
];
