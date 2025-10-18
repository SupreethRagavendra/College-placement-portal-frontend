<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAssessmentController;
use App\Http\Controllers\StudentAssessmentController;
use App\Http\Controllers\Admin\ResultController as AdminResultController;
use App\Http\Controllers\Student\ResultController as StudentResultController;

// Admin Routes
Route::prefix('admin')->middleware(['web', 'auth', 'role:admin'])->group(function () {
    // Assessment routes
    Route::resource('assessments', AdminAssessmentController::class)->names([
        'index' => 'admin.assessments.index',
        'create' => 'admin.assessments.create',
        'store' => 'admin.assessments.store',
        'show' => 'admin.assessments.show',
        'edit' => 'admin.assessments.edit',
        'update' => 'admin.assessments.update',
        'destroy' => 'admin.assessments.destroy',
    ]);
    
    // Toggle assessment status
    Route::patch('assessments/{assessment}/toggle-status', [AdminAssessmentController::class, 'toggleStatus'])->name('admin.assessments.toggle-status');
    
    // Duplicate assessment
    Route::post('assessments/{assessment}/duplicate', [AdminAssessmentController::class, 'duplicate'])->name('admin.assessments.duplicate');
    
    // Question management routes
    Route::get('assessments/{assessment}/questions', [AdminAssessmentController::class, 'questions'])->name('admin.assessments.questions');
    Route::get('assessments/{assessment}/add-question', [AdminAssessmentController::class, 'addQuestion'])->name('admin.assessments.add-question');
    Route::post('assessments/{assessment}/store-question', [AdminAssessmentController::class, 'storeQuestion'])->name('admin.assessments.store-question');
    Route::delete('assessments/{assessment}/remove-all-questions', [AdminAssessmentController::class, 'removeAllQuestions'])->name('admin.assessments.remove-all-questions');
    Route::get('assessments/{assessment}/questions/{question}/edit', [AdminAssessmentController::class, 'editQuestion'])->name('admin.assessments.edit-question');
    Route::put('assessments/{assessment}/questions/{question}/update', [AdminAssessmentController::class, 'updateQuestion'])->name('admin.assessments.update-question');
    Route::delete('assessments/{assessment}/questions/{question}/delete', [AdminAssessmentController::class, 'deleteQuestion'])->name('admin.assessments.delete-question');
    
    // Result routes
    Route::prefix('assessments/{assessment}')->group(function () {
        Route::get('results', [AdminResultController::class, 'index'])->name('admin.results.index');
        Route::get('export-results', [AdminResultController::class, 'export'])->name('admin.results.export');
        Route::get('export-detailed-results', [AdminResultController::class, 'exportDetailed'])->name('admin.results.export-detailed');
        Route::get('results/{studentAssessment}', [AdminResultController::class, 'show'])->name('admin.results.show');
    });
});

// Student Routes
Route::prefix('student')->middleware(['web', 'auth', 'role:student'])->group(function () {
    // Assessment routes
    Route::get('assessments', [StudentAssessmentController::class, 'index'])->name('student.assessments.index');
    Route::get('assessments/{assessment}', [StudentAssessmentController::class, 'show'])->name('student.assessments.show');
    Route::post('assessments/{assessment}/start', [StudentAssessmentController::class, 'start'])->name('student.assessments.start');
    Route::get('assessments/{assessment}/take', [StudentAssessmentController::class, 'take'])->name('student.assessments.take');
    Route::post('assessments/{assessment}/save-progress', [StudentAssessmentController::class, 'saveProgress'])->name('student.assessments.save-progress');
    Route::post('assessments/{assessment}/submit', [StudentAssessmentController::class, 'submit'])->name('student.assessments.submit');
    Route::get('assessments/{assessment}/result', [StudentAssessmentController::class, 'result'])->name('student.assessments.result');
    Route::get('assessment-history', [StudentAssessmentController::class, 'history'])->name('student.assessment.history');
    Route::get('analytics', [StudentAssessmentController::class, 'analytics'])->name('student.assessments.analytics');
    
    // Result routes
    Route::get('results', [StudentResultController::class, 'index'])->name('student.results.index');
    Route::get('results/{studentAssessment}', [StudentResultController::class, 'show'])->name('student.results.show');
    
    // Report routes
    Route::get('reports/dashboard', [StudentResultController::class, 'dashboard'])->name('student.reports.dashboard');
});