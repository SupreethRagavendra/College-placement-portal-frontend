<?php
/**
 * Health check endpoint for Render deployment
 * This file can be accessed directly without Laravel bootstrapping
 */

header('Content-Type: application/json');

$response = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => []
];

// Check PHP version
$response['checks']['php_version'] = [
    'status' => version_compare(PHP_VERSION, '8.2.0', '>=') ? 'ok' : 'error',
    'value' => PHP_VERSION,
    'required' => '>=8.2.0'
];

// Check required extensions
$requiredExtensions = ['pdo', 'pdo_pgsql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json'];
$missingExtensions = [];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}
$response['checks']['extensions'] = [
    'status' => empty($missingExtensions) ? 'ok' : 'error',
    'missing' => $missingExtensions
];

// Check environment variables
$envVars = ['APP_KEY', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
$missingEnv = [];
foreach ($envVars as $var) {
    if (empty($_ENV[$var]) && empty(getenv($var))) {
        $missingEnv[] = $var;
    }
}
$response['checks']['environment'] = [
    'status' => empty($missingEnv) ? 'ok' : 'warning',
    'missing' => $missingEnv
];

// Check if vendor directory exists
$response['checks']['vendor'] = [
    'status' => is_dir(__DIR__ . '/../vendor') ? 'ok' : 'error',
    'exists' => is_dir(__DIR__ . '/../vendor')
];

// Check if storage directories exist and are writable
$storageDirs = [
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];
$storageIssues = [];
foreach ($storageDirs as $dir) {
    $fullPath = __DIR__ . '/../' . $dir;
    if (!is_dir($fullPath)) {
        $storageIssues[] = "$dir does not exist";
    } elseif (!is_writable($fullPath)) {
        $storageIssues[] = "$dir is not writable";
    }
}
$response['checks']['storage'] = [
    'status' => empty($storageIssues) ? 'ok' : 'error',
    'issues' => $storageIssues
];

// Check if public/build exists (Vite assets)
$response['checks']['assets'] = [
    'status' => is_dir(__DIR__ . '/build') ? 'ok' : 'warning',
    'exists' => is_dir(__DIR__ . '/build')
];

// Overall status
$hasError = false;
foreach ($response['checks'] as $check) {
    if ($check['status'] === 'error') {
        $hasError = true;
        break;
    }
}
$response['status'] = $hasError ? 'error' : 'ok';

// Output response
echo json_encode($response, JSON_PRETTY_PRINT);
