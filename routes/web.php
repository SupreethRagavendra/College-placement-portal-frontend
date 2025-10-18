<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminAssessmentController;
use App\Http\Controllers\StudentAssessmentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Note: assessment.php is loaded via bootstrap/app.php

// Test database connection
Route::get('/test-db', function () {
    try {
        $pdo = new PDO(
            "pgsql:host=db.wkqbukidxmzbgwauncrl.supabase.co;port=5432;dbname=postgres;sslmode=require",
            "postgres",
            "Supreeeth24#"
        );
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->query("SELECT version()");
        $version = $stmt->fetchColumn();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Connected to Supabase PostgreSQL successfully!',
            'version' => $version
        ]);
        
    } catch (PDOException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Connection failed: ' . $e->getMessage()
        ]);
    }
});

// Test Supabase configuration
Route::get('/test-supabase', function () {
    $config = [
        'supabase_url' => config('supabase.url'),
        'supabase_anon_key' => config('supabase.anon_key'),
        'supabase_service_role_key' => config('supabase.service_role_key'),
    ];
    
    $issues = [];
    
    if (str_contains($config['supabase_url'], 'your-project')) {
        $issues[] = 'SUPABASE_URL is not configured (still using placeholder)';
    }
    
    if (str_contains($config['supabase_anon_key'], 'your_anon_key_here')) {
        $issues[] = 'SUPABASE_ANON_KEY is not configured (still using placeholder)';
    }
    
    if (str_contains($config['supabase_service_role_key'], 'your_service_role_key_here')) {
        $issues[] = 'SUPABASE_SERVICE_ROLE_KEY is not configured (still using placeholder)';
    }
    
    return response()->json([
        'status' => empty($issues) ? 'success' : 'error',
        'config' => $config,
        'issues' => $issues,
        'message' => empty($issues) ? 'Supabase configuration looks good!' : 'Please configure your Supabase API keys in .env file'
    ]);
});

// Health check endpoint for Render
Route::get('/healthz', function () {
    try {
        // Check database connection
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'service' => 'college-placement-portal',
            'database' => 'connected',
            'app_env' => config('app.env')
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'service' => 'college-placement-portal',
            'database' => 'disconnected',
            'error' => $e->getMessage()
        ], 503);
    }
})->name('healthz');

// Landing page
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Public RAG health check endpoint (no authentication required)
Route::get('/rag-health', [\App\Http\Controllers\Student\OpenRouterChatbotController::class, 'health'])->name('rag.health');

// Authentication routes (no email verification required)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Redirect authenticated users based on role
Route::get('/dashboard', function () {
    if (Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('student.dashboard');
    }
})->middleware(['auth'])->name('dashboard');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/dashboard/stats', [\App\Http\Controllers\Admin\DashboardDataController::class, 'getStats'])->name('admin.dashboard.stats');

    // Student verification
    Route::get('/students/pending', [AdminStudentController::class, 'pending'])->name('admin.pending-students');
    Route::post('/students/{id}/approve', [AdminStudentController::class, 'approve'])->name('admin.approve-student');
    Route::post('/students/{id}/reject', [AdminStudentController::class, 'reject'])->name('admin.reject-student');
    
    // Bulk student operations
    Route::post('/students/bulk-approve', [AdminController::class, 'bulkApprove'])->name('admin.bulk-approve');
    Route::post('/students/bulk-reject', [AdminController::class, 'bulkReject'])->name('admin.bulk-reject');
    
    // Student details and management
    Route::get('/students/{id}/details', [AdminController::class, 'getStudentDetails'])->name('admin.student-details');

    // Student lists (for dashboard links)
    Route::get('/students/approved', [AdminController::class, 'approvedStudents'])->name('admin.approved-students');
    Route::get('/students/rejected', [AdminController::class, 'rejectedStudents'])->name('admin.rejected-students');
    
    // Restore rejected student
    Route::post('/students/{id}/restore', [AdminController::class, 'restoreStudent'])->name('admin.restore-student');
    
    // Revoke approved student access
    Route::delete('/students/{id}/revoke', [AdminController::class, 'revokeStudent'])->name('admin.revoke-student');

    // Note: Assessment routes are now defined in routes/assessment.php

    // Clear caches (for debugging)
    Route::post('/clear-caches', [AdminController::class, 'clearCaches'])->name('admin.clear-caches');
    
    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/assessments/{assessment}', [AdminReportController::class, 'assessmentDetails'])
        ->name('admin.reports.assessment-details');
    Route::get('/reports/students', [AdminReportController::class, 'studentPerformance'])
        ->name('admin.reports.student-performance');
    Route::get('/reports/students/{student}', [AdminReportController::class, 'studentDetails'])
        ->name('admin.reports.student-details');
    Route::get('/reports/categories', [AdminReportController::class, 'categoryAnalysis'])
        ->name('admin.reports.category-analysis');
    Route::get('/reports/questions/{assessment}', [AdminReportController::class, 'questionAnalysis'])
        ->name('admin.reports.question-analysis');
    Route::get('/reports/result/{result}', [AdminReportController::class, 'resultDetails'])
        ->name('admin.reports.result-details');
    Route::get('/reports/export', [AdminReportController::class, 'exportCsv'])->name('admin.reports.export');
    
    // OpenRouter RAG Admin routes
    Route::post('/rag/sync', [\App\Http\Controllers\Student\OpenRouterChatbotController::class, 'syncKnowledge'])->name('admin.rag.sync');
    Route::get('/rag/health', [\App\Http\Controllers\Student\OpenRouterChatbotController::class, 'health'])->name('admin.rag.health');
});

// Student routes
Route::middleware(['auth', 'role:student'])->prefix('student')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\StudentController::class, 'dashboard'])->name('student.dashboard');
    
    // Chatbot routes (original)
    Route::post('/chatbot-ask', [\App\Http\Controllers\Student\ChatbotController::class, 'ask'])->name('student.chatbot.ask');
    Route::get('/chatbot-stats', [\App\Http\Controllers\Student\ChatbotController::class, 'getStats'])->name('student.chatbot.stats');
    
    // Intelligent Chatbot routes (enhanced version)
    Route::post('/intelligent-chat', [\App\Http\Controllers\Student\IntelligentChatbotController::class, 'chat'])->name('student.intelligent.chat');
    Route::get('/conversation-history', [\App\Http\Controllers\Student\IntelligentChatbotController::class, 'getConversationHistory'])->name('student.conversation.history');
    Route::post('/chatbot-feedback', [\App\Http\Controllers\Student\IntelligentChatbotController::class, 'addFeedback'])->name('student.chatbot.feedback');
    Route::get('/performance-insights', [\App\Http\Controllers\Student\IntelligentChatbotController::class, 'getPerformanceInsights'])->name('student.performance.insights');
    
    // OpenRouter RAG Chatbot routes (OpenRouter AI)
    Route::post('/rag-chat', [\App\Http\Controllers\Student\OpenRouterChatbotController::class, 'chat'])->name('student.rag.chat');
    Route::get('/rag-health', [\App\Http\Controllers\Student\OpenRouterChatbotController::class, 'health'])->name('student.rag.health');
    
    // Test route for chatbot mode verification
    Route::get('/chatbot-mode-test', function() {
        $ragServiceUrl = config('rag.service_url', 'http://localhost:8001');
        $ragStatus = 'down';
        $laravelStatus = 'running';
        $currentMode = 'unknown';
        
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)->get($ragServiceUrl . '/health');
            if ($response->successful()) {
                $ragStatus = 'running';
                $currentMode = '🟢 Mode 1: RAG ACTIVE';
            } else {
                $currentMode = '🟡 Mode 2: LIMITED MODE';
            }
        } catch (\Exception $e) {
            $currentMode = '🟡 Mode 2: LIMITED MODE';
        }
        
        return response()->json([
            'current_mode' => $currentMode,
            'services' => [
                'laravel' => $laravelStatus,
                'rag_service' => $ragStatus
            ],
            'rag_url' => $ragServiceUrl,
            'user' => Auth::user() ? Auth::user()->name : 'Not authenticated',
            'timestamp' => now()->toISOString()
        ]);
    })->name('student.chatbot.mode.test');
    
    // Note: Assessment routes are now defined in routes/assessment.php
    
    // Legacy routes (keeping for backward compatibility)
    Route::get('/categories', [\App\Http\Controllers\StudentController::class, 'categories'])->name('student.categories');
    Route::get('/test/{id}', [\App\Http\Controllers\StudentController::class, 'test'])->name('student.test');
    Route::post('/test/{id}/submit', [\App\Http\Controllers\StudentController::class, 'submitTest'])->name('student.test.submit');
    Route::get('/results/{id}', [\App\Http\Controllers\StudentController::class, 'results'])->name('student.results');
    // Profile routes removed - use general /profile routes instead
    Route::get('/history', [\App\Http\Controllers\StudentController::class, 'history'])->name('student.history');
});

// Profile routes (accessible by both roles)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Account deletion confirmation routes (no auth required for email links)
Route::get('/account/delete/confirm/{token}', [ProfileController::class, 'confirmDestroy'])->name('profile.destroy.confirm');
Route::get('/account/delete/cancel/{token}', [ProfileController::class, 'cancelDestroy'])->name('profile.destroy.cancel');