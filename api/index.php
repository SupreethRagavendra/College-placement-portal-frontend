<?php
// Laravel entry point for Vercel
// Set the document root to the Laravel public directory
$_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../public';
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Include the Laravel bootstrap file
require __DIR__.'/../public/index.php';
