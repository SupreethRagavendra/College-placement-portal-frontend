<?php
/**
 * Health check endpoint for Render
 * This endpoint doesn't require database connection
 */

// Basic health check - just verify PHP is working
http_response_code(200);
header('Content-Type: application/json');

$health = [
    'status' => 'healthy',
    'timestamp' => date('c'),
    'service' => 'college-placement-portal',
    'php_version' => PHP_VERSION
];

// Try to check database connection (non-blocking)
if (isset($_ENV['DB_HOST']) && isset($_ENV['DB_PORT'])) {
    $health['database'] = [
        'host' => $_ENV['DB_HOST'],
        'port' => $_ENV['DB_PORT'],
        'configured' => true
    ];
    
    // Attempt quick connection test
    try {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s',
            $_ENV['DB_HOST'] ?? 'localhost',
            $_ENV['DB_PORT'] ?? '5432',
            $_ENV['DB_DATABASE'] ?? 'postgres',
            $_ENV['DB_SSLMODE'] ?? 'require'
        );
        
        $options = [
            PDO::ATTR_TIMEOUT => 2,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];
        
        $pdo = new PDO($dsn, $_ENV['DB_USERNAME'] ?? '', $_ENV['DB_PASSWORD'] ?? '', $options);
        $health['database']['connected'] = true;
    } catch (Exception $e) {
        $health['database']['connected'] = false;
        $health['database']['error'] = 'Connection failed';
    }
}

echo json_encode($health, JSON_PRETTY_PRINT);
