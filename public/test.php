<?php
/**
 * Simple test endpoint to verify PHP is working
 * This bypasses Laravel completely
 */

// Set headers
header('Content-Type: application/json');
header('X-Powered-By: PHP/' . PHP_VERSION);

// Basic response
$response = [
    'status' => 'success',
    'message' => 'PHP is working!',
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
    'script_filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'Unknown',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
    'timestamp' => date('Y-m-d H:i:s'),
    'timezone' => date_default_timezone_get(),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'environment_check' => [
        'APP_ENV' => getenv('APP_ENV') ?: 'not set',
        'APP_DEBUG' => getenv('APP_DEBUG') ?: 'not set',
        'APP_KEY' => getenv('APP_KEY') ? 'set (hidden)' : 'NOT SET - THIS IS THE PROBLEM!',
        'DB_CONNECTION' => getenv('DB_CONNECTION') ?: 'not set',
        'DB_HOST' => getenv('DB_HOST') ? 'set (hidden)' : 'NOT SET',
        'PORT' => getenv('PORT') ?: 'not set'
    ]
];

// Output
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
