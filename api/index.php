<?php
// Laravel serverless entry point for Vercel
// This file handles all requests and routes them through Laravel

// Set the document root to the Laravel public directory
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../public';
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Set up the request URI
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Handle static files first
if (file_exists(__DIR__ . '/../public' . $requestUri) && !is_dir(__DIR__ . '/../public' . $requestUri)) {
    return false; // Let Vercel handle static files
}

// Set up Laravel environment
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
$_SERVER['SERVER_PORT'] = $_SERVER['SERVER_PORT'] ?? '80';
$_SERVER['HTTPS'] = $_SERVER['HTTPS'] ?? 'off';

// Include the Laravel bootstrap file
require __DIR__.'/../public/index.php';
