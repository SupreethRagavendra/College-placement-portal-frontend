<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\ChatbotController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Chatbot API endpoints (for frontend compatibility)
Route::middleware(['auth:sanctum'])->group(function () {
    // Primary chatbot endpoint
    Route::post('/chatbot', [ChatbotController::class, 'ask'])->name('api.chatbot');
    Route::post('/chatbot/query', [ChatbotController::class, 'query'])->name('api.chatbot.query');
});

// Public chatbot endpoint (if needed for testing)
Route::post('/chatbot/public', function (Request $request) {
    // For testing without authentication
    if (env('APP_ENV') === 'local') {
        $controller = app(ChatbotController::class);
        return $controller->ask($request);
    }
    
    return response()->json([
        'error' => 'Authentication required'
    ], 401);
});
