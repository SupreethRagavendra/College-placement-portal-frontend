<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentAssessment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    /**
     * Display a listing of the student's assessment results
     */
    public function index(): View
    {
        $studentAssessments = StudentAssessment::where('student_id', Auth::id())
            ->with('assessment')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('student.results.index', compact('studentAssessments'));
    }

    /**
     * Display the specified result
     */
    public function show(StudentAssessment $studentAssessment): View
    {
        // Ensure the student can only view their own results
        if ($studentAssessment->student_id !== Auth::id()) {
            abort(403);
        }
        
        $studentAssessment->load(['assessment', 'studentAnswers.question']);
        
        return view('student.results.show', compact('studentAssessment'));
    }

    /**
     * Display student's performance dashboard
     */
    public function dashboard(): View
    {
        $studentId = Auth::id();
        
        // Get all completed assessments
        $completedAssessments = StudentAssessment::where('student_id', $studentId)
            ->where('status', 'completed')
            ->with('assessment')
            ->get();
        
        // Calculate overall statistics
        $totalAssessments = $completedAssessments->count();
        $passedAssessments = $completedAssessments->where('pass_status', 'pass')->count();
        $failedAssessments = $completedAssessments->where('pass_status', 'fail')->count();
        
        $averagePercentage = $totalAssessments > 0 ? 
            round($completedAssessments->avg('percentage'), 2) : 0;
        
        $passRate = $totalAssessments > 0 ? 
            round(($passedAssessments / $totalAssessments) * 100, 2) : 0;
        
        // Group by category for performance analysis
        $categoryPerformance = $completedAssessments->groupBy(function($attempt) {
            return $attempt->assessment->category;
        })->map(function($attempts) {
            return [
                'average_percentage' => round($attempts->avg('percentage'), 2),
                'total_attempts' => $attempts->count(),
                'passed' => $attempts->where('pass_status', 'pass')->count(),
            ];
        });
        
        // Get recent assessments
        $recentAssessments = $completedAssessments->sortByDesc('created_at')->take(5);
        
        return view('student.reports.dashboard', compact(
            'totalAssessments',
            'passedAssessments',
            'failedAssessments',
            'averagePercentage',
            'passRate',
            'categoryPerformance',
            'recentAssessments'
        ));
    }
}