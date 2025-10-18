<?php

use App\Http\Controllers\SupabaseAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Registration routes
    Route::get('register', [SupabaseAuthController::class, 'showRegister'])
        ->name('register');

    Route::post('register', [SupabaseAuthController::class, 'register']);

    // Login routes
    Route::get('login', [SupabaseAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('login', [SupabaseAuthController::class, 'login']);

    // Password reset routes
    Route::get('forgot-password', [SupabaseAuthController::class, 'showResetPassword'])
        ->name('password.request');

    Route::post('forgot-password', [SupabaseAuthController::class, 'resetPassword'])
        ->name('password.email');
});

Route::middleware('auth')->group(function () {
    // Email verification routes
    Route::get('verify-email', [SupabaseAuthController::class, 'showVerificationNotice'])
        ->name('verification.notice');

    Route::get('verify-email', [SupabaseAuthController::class, 'verifyEmail'])
        ->name('verification.verify');

    // Logout route
    Route::post('logout', [SupabaseAuthController::class, 'logout'])
        ->name('logout');
});
