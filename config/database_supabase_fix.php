<?php

// Temporary Supabase Connection Fix
// Copy this configuration to your config/database.php if DNS issues persist

return [
    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            // Try using IP directly if DNS fails
            // You can get the current IP by running: nslookup db.wkqbukidxmzbgwauncrl.supabase.co 8.8.8.8
            'host' => env('DB_HOST', 'db.wkqbukidxmzbgwauncrl.supabase.co'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'postgres'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'require', // Supabase requires SSL
            'options' => [
                PDO::ATTR_PERSISTENT => false,
                // Add SSL options for Supabase
                PDO::PGSQL_ATTR_DISABLE_PREPARES => false,
            ],
        ],
        
        // Alternative connection using direct IP (update IP as needed)
        'pgsql_ip' => [
            'driver' => 'pgsql',
            'host' => '34.151.225.195', // Example IP - replace with actual Supabase IP
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'postgres'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'require',
            'options' => [
                PDO::ATTR_PERSISTENT => false,
            ],
        ],
    ],
];

// To use this fix:
// 1. Get the current Supabase IP: nslookup db.wkqbukidxmzbgwauncrl.supabase.co 8.8.8.8
// 2. Update the 'host' in pgsql_ip connection with the actual IP
// 3. In your .env, temporarily change: DB_CONNECTION=pgsql_ip
// 4. Or merge this config into your existing config/database.php
