<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Question;
use App\Models\StudentResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index(): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Get assessment statistics
        $assessmentStats = Assessment::withCount(['studentResults', 'questions'])
            ->get()
            ->map(function($assessment) {
                $results = $assessment->studentResults()->select('assessment_id', 'score', 'total_questions', 'time_taken')->get();
                return [
                    'id' => $assessment->id,
                    'title' => $assessment->title ?? 'Untitled',
                    'category' => $assessment->category,
                    'total_questions' => $assessment->questions_count,
                    'total_attempts' => $assessment->student_results_count,
                    'avg_score' => $results->avg('score') ?? 0,
                    'avg_percentage' => $results->count() > 0 
                        ? round($results->avg(function($result) {
                            return $result->total_questions > 0 ? ($result->score / $result->total_questions) * 100 : 0;
                        }), 2) : 0,
                    'avg_time' => round($results->avg('time_taken') ?? 0),
                    'highest_score' => $results->max('score') ?? 0,
                    'lowest_score' => $results->min('score') ?? 0,
                    'pass_rate' => $results->count() > 0 
                        ? round($results->filter(function($result) {
                            return $result->total_questions > 0 && (($result->score / $result->total_questions) * 100) >= 50;
                        })->count() / $results->count() * 100, 2) : 0,
                ];
            });

        // Overall statistics
        $overallStats = [
            'total_assessments' => Assessment::count(),
            'total_questions' => Question::count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_attempts' => StudentResult::count(),
            'average_score' => StudentResult::selectRaw('AVG((score / total_questions) * 100) as avg')
                ->value('avg') ?? 0,
        ];

        return view('admin.reports.index', compact('assessmentStats', 'overallStats'));
    }

    public function assessmentDetails(Request $request, Assessment $assessment): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $query = StudentResult::where('assessment_id', $assessment->id)
            ->with(['student:id,name,email', 'assessment:id,title,category']);

        // Apply filters
        if ($request->filled('search')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('email', 'LIKE', '%' . $request->search . '%');
            });
        }

        if ($request->filled('grade')) {
            switch ($request->grade) {
                case 'A':
                    $query->whereRaw('(score / total_questions) * 100 >= 90');
                    break;
                case 'B':
                    $query->whereRaw('(score / total_questions) * 100 >= 80 AND (score / total_questions) * 100 < 90');
                    break;
                case 'C':
                    $query->whereRaw('(score / total_questions) * 100 >= 70 AND (score / total_questions) * 100 < 80');
                    break;
                case 'D':
                    $query->whereRaw('(score / total_questions) * 100 >= 60 AND (score / total_questions) * 100 < 70');
                    break;
                case 'F':
                    $query->whereRaw('(score / total_questions) * 100 < 60');
                    break;
            }
        }

        $results = $query->orderBy('submitted_at', 'desc')
            ->paginate(20);

        // Assessment statistics
        $stats = [
            'total_attempts' => StudentResult::where('assessment_id', $assessment->id)->count(),
            'avg_score' => StudentResult::where('assessment_id', $assessment->id)
                ->selectRaw('AVG((score / total_questions) * 100) as avg')
                ->value('avg') ?? 0,
            'pass_rate' => StudentResult::where('assessment_id', $assessment->id)
                ->whereRaw('(score / total_questions) * 100 >= 50')
                ->count() / max(StudentResult::where('assessment_id', $assessment->id)->count(), 1) * 100,
            'avg_time' => StudentResult::where('assessment_id', $assessment->id)
                ->avg('time_taken') ?? 0,
        ];

        // Category Analysis - Analyze performance by question categories
        $categoryAnalysis = [];
        if ($assessment->questions()->exists()) {
            // Get all questions with their categories
            $questions = $assessment->questions()->get();
            
            // Group by category - the accessor in Question model handles JSON parsing
            $questionCategories = $questions->groupBy('category');
            
            // Get all student answers for this assessment
            $studentAnswers = DB::table('student_answers')
                ->join('student_assessments', 'student_answers.student_assessment_id', '=', 'student_assessments.id')
                ->where('student_assessments.assessment_id', $assessment->id)
                ->select('student_answers.question_id', 'student_answers.is_correct')
                ->get();
            
            // Group answers by question
            $answersByQuestion = $studentAnswers->groupBy('question_id');
            
            foreach ($questionCategories as $category => $categoryQuestions) {
                $totalAttempts = 0;
                $correctAnswers = 0;
                
                foreach ($categoryQuestions as $question) {
                    if (isset($answersByQuestion[$question->id])) {
                        $questionAnswers = $answersByQuestion[$question->id];
                        $totalAttempts += $questionAnswers->count();
                        $correctAnswers += $questionAnswers->where('is_correct', true)->count();
                    }
                }
                
                $accuracy = $totalAttempts > 0 ? round(($correctAnswers / $totalAttempts) * 100, 1) : 0;
                
                $categoryAnalysis[] = [
                    'category' => $category,
                    'question_count' => $categoryQuestions->count(),
                    'total_attempts' => $totalAttempts,
                    'correct_answers' => $correctAnswers,
                    'accuracy' => $accuracy,
                    'is_struggling' => $accuracy < 60 && $totalAttempts > 0, // Below 60% is considered struggling
                ];
            }
            
            // Sort by accuracy (ascending) to show struggling categories first
            usort($categoryAnalysis, function($a, $b) {
                return $a['accuracy'] <=> $b['accuracy'];
            });
        }

        return view('admin.reports.assessment-details', compact('assessment', 'results', 'stats', 'categoryAnalysis'));
    }

    public function studentPerformance(Request $request): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $query = StudentResult::select('student_id')
            ->selectRaw('COUNT(*) as total_attempts')
            ->selectRaw('AVG((score / total_questions) * 100) as avg_percentage')
            ->selectRaw('MAX((score / total_questions) * 100) as best_percentage')
            ->selectRaw('MIN((score / total_questions) * 100) as worst_percentage')
            ->selectRaw('SUM(time_taken) as total_time')
            ->with('student:id,name,email')
            ->groupBy('student_id');

        // Apply filters
        if ($request->filled('search')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('email', 'LIKE', '%' . $request->search . '%');
            });
        }

        if ($request->filled('min_attempts')) {
            $query->having('total_attempts', '>=', $request->min_attempts);
        }

        $studentPerformance = $query->orderBy('avg_percentage', 'desc')
            ->paginate(20);

        return view('admin.reports.student-performance', compact('studentPerformance'));
    }

    public function studentDetails(User $student): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Ensure the user is a student
        if ($student->role !== 'student') {
            abort(404, 'Student not found');
        }

        // Get all assessment attempts for this student
        $assessmentHistory = StudentResult::where('student_id', $student->id)
            ->with(['assessment:id,title,category,total_marks,pass_percentage,difficulty_level'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        // Calculate overall statistics
        $overallStats = [
            'total_assessments' => $assessmentHistory->count(),
            'unique_assessments' => $assessmentHistory->pluck('assessment_id')->unique()->count(),
            'average_score' => $assessmentHistory->avg('score_percentage') ?? 0,
            'best_score' => $assessmentHistory->max('score_percentage') ?? 0,
            'worst_score' => $assessmentHistory->min('score_percentage') ?? 0,
            'total_time_spent' => $assessmentHistory->sum('time_taken'),
            'average_time' => $assessmentHistory->avg('time_taken') ?? 0,
            'pass_rate' => $assessmentHistory->count() > 0 
                ? ($assessmentHistory->where('score_percentage', '>=', 50)->count() / $assessmentHistory->count()) * 100 
                : 0,
        ];

        // Category-wise performance
        $categoryPerformance = $assessmentHistory->groupBy('assessment.category')
            ->map(function ($items, $category) {
                return [
                    'category' => $category,
                    'attempts' => $items->count(),
                    'average_score' => $items->avg('score_percentage'),
                    'best_score' => $items->max('score_percentage'),
                    'worst_score' => $items->min('score_percentage'),
                    'pass_rate' => $items->count() > 0 
                        ? ($items->where('score_percentage', '>=', 50)->count() / $items->count()) * 100 
                        : 0,
                ];
            })->values();

        // Difficulty-wise performance
        $difficultyPerformance = $assessmentHistory->groupBy('assessment.difficulty_level')
            ->map(function ($items, $difficulty) {
                return [
                    'difficulty' => ucfirst($difficulty),
                    'attempts' => $items->count(),
                    'average_score' => round($items->avg('score_percentage'), 2),
                    'pass_rate' => $items->count() > 0 
                        ? round(($items->where('score_percentage', '>=', 50)->count() / $items->count()) * 100, 2) 
                        : 0,
                ];
            })->values();

        // Recent performance trend (last 10 assessments)
        $recentTrend = $assessmentHistory->take(10)->reverse()->map(function ($result) {
            return [
                'assessment_name' => $result->assessment->title ?? 'N/A',
                'score' => $result->score_percentage,
                'date' => $result->submitted_at ? $result->submitted_at->format('M d') : 'N/A',
            ];
        })->values();

        // Time analysis
        $timeAnalysis = [
            'fastest_completion' => $assessmentHistory->min('time_taken'),
            'slowest_completion' => $assessmentHistory->max('time_taken'),
            'average_time' => round($assessmentHistory->avg('time_taken'), 2),
            'total_time' => $assessmentHistory->sum('time_taken'),
        ];

        // Grade distribution
        $gradeDistribution = [
            'A' => $assessmentHistory->filter(fn($r) => $r->score_percentage >= 90)->count(),
            'B' => $assessmentHistory->filter(fn($r) => $r->score_percentage >= 80 && $r->score_percentage < 90)->count(),
            'C' => $assessmentHistory->filter(fn($r) => $r->score_percentage >= 70 && $r->score_percentage < 80)->count(),
            'D' => $assessmentHistory->filter(fn($r) => $r->score_percentage >= 60 && $r->score_percentage < 70)->count(),
            'F' => $assessmentHistory->filter(fn($r) => $r->score_percentage < 60)->count(),
        ];

        // Get unique assessments taken
        $uniqueAssessments = Assessment::whereIn('id', $assessmentHistory->pluck('assessment_id')->unique())
            ->get(['id', 'title', 'category']);

        return view('admin.reports.student-details', compact(
            'student', 
            'assessmentHistory', 
            'overallStats', 
            'categoryPerformance',
            'difficultyPerformance',
            'recentTrend',
            'timeAnalysis',
            'gradeDistribution',
            'uniqueAssessments'
        ));
    }

    public function categoryAnalysis(): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $categoryStats = DB::table('student_results')
            ->join('assessments', 'student_results.assessment_id', '=', 'assessments.id')
            ->select('assessments.category')
            ->selectRaw('COUNT(student_results.id) as total_attempts')
            ->selectRaw('AVG((score / total_questions) * 100) as avg_percentage')
            ->selectRaw('COUNT(DISTINCT student_results.student_id) as unique_students')
            ->selectRaw('COUNT(DISTINCT assessments.id) as total_assessments')
            ->groupBy('assessments.category')
            ->get();

        return view('admin.reports.category-analysis', compact('categoryStats'));
    }

    public function exportCsv(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $type = $request->get('type', 'all'); // all, assessment, student
        $assessmentId = $request->get('assessment_id');

        // For student-focused export, we'll group by student
        if ($type === 'student') {
            return $this->exportStudentSummary();
        }
        // For assessment-focused export
        else if ($type === 'assessment' && $assessmentId) {
            return $this->exportAssessmentDetails($assessmentId);
        }
        // Default: export all results
        else {
            return $this->exportAllResults();
        }
    }

    private function formatTimeForExport($seconds)
    {
        if ($seconds < 60) {
            return $seconds . ' seconds';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;
            if ($remainingSeconds > 0) {
                return $minutes . 'm ' . $remainingSeconds . 's';
            }
            return $minutes . ' minutes';
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $remainingSeconds = $seconds % 60;
            if ($minutes > 0 && $remainingSeconds > 0) {
                return $hours . 'h ' . $minutes . 'm ' . $remainingSeconds . 's';
            } elseif ($minutes > 0) {
                return $hours . 'h ' . $minutes . 'm';
            } else {
                return $hours . 'h';
            }
        }
    }

    private function exportAllResults()
    {
        $results = StudentResult::with(['student:id,name,email', 'assessment:id,title,category'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $csvData = [];
        $csvData[] = [
            'Student Name',
            'Student Email', 
            'Assessment Title',
            'Category',
            'Score',
            'Total Questions',
            'Percentage',
            'Grade',
            'Time Taken',
            'Submitted At'
        ];

        foreach ($results as $result) {
            $percentage = $result->score_percentage;
            $grade = $result->grade;
            $timeFormatted = $this->formatTimeForExport($result->time_taken);

            $csvData[] = [
                $result->student->name ?? 'N/A',
                $result->student->email ?? 'N/A',
                $result->assessment->title ?? 'N/A',
                $result->assessment->category ?? 'N/A',
                $result->score,
                $result->total_questions,
                $percentage . '%',
                $grade,
                $timeFormatted,
                $result->submitted_at ? $result->submitted_at->format('d-m-Y H:i') : 'N/A'
            ];
        }

        $filename = 'all_assessment_results_' . date('Y-m-d_H-i-s') . '.csv';
        
        return $this->generateCsvResponse($csvData, $filename);
    }

    private function exportAssessmentDetails($assessmentId)
    {
        $results = StudentResult::with(['student:id,name,email', 'assessment:id,title,category'])
            ->where('assessment_id', $assessmentId)
            ->orderBy('submitted_at', 'desc')
            ->get();

        if ($results->isEmpty()) {
            $assessment = Assessment::find($assessmentId);
            $assessmentName = $assessment ? $assessment->title : 'Unknown Assessment';
        } else {
            $assessmentName = $results->first()->assessment->title;
        }

        $csvData = [];
        $csvData[] = [
            'Student Name',
            'Student Email', 
            'Assessment Title',
            'Category',
            'Score',
            'Total Questions',
            'Percentage',
            'Grade',
            'Time Taken',
            'Submitted At'
        ];

        foreach ($results as $result) {
            $percentage = $result->score_percentage;
            $grade = $result->grade;
            $timeFormatted = $this->formatTimeForExport($result->time_taken);

            $csvData[] = [
                $result->student->name ?? 'N/A',
                $result->student->email ?? 'N/A',
                $result->assessment->title ?? 'N/A',
                $result->assessment->category ?? 'N/A',
                $result->score,
                $result->total_questions,
                $percentage . '%',
                $grade,
                $timeFormatted,
                $result->submitted_at ? $result->submitted_at->format('d-m-Y H:i') : 'N/A'
            ];
        }

        $filename = 'assessment_' . str_replace(' ', '_', $assessmentName) . '_results_' . date('Y-m-d_H-i-s') . '.csv';
        
        return $this->generateCsvResponse($csvData, $filename);
    }

    private function exportStudentSummary()
    {
        // Get student performance data grouped by student
        $studentData = StudentResult::select('student_id')
            ->selectRaw('COUNT(*) as total_attempts')
            ->selectRaw('AVG((score / total_questions) * 100) as avg_percentage')
            ->selectRaw('MAX((score / total_questions) * 100) as best_percentage')
            ->selectRaw('MIN((score / total_questions) * 100) as worst_percentage')
            ->selectRaw('SUM(time_taken) as total_time')
            ->with('student:id,name,email')
            ->groupBy('student_id')
            ->orderBy('avg_percentage', 'desc')
            ->get();

        $csvData = [];
        $csvData[] = [
            'Student Name',
            'Student Email',
            'Total Assessments Taken',
            'Average Percentage',
            'Best Percentage',
            'Worst Percentage',
            'Total Time Spent'
        ];

        foreach ($studentData as $data) {
            $totalTimeFormatted = $this->formatTimeForExport($data->total_time);
            
            $csvData[] = [
                $data->student->name ?? 'N/A',
                $data->student->email ?? 'N/A',
                $data->total_attempts,
                round($data->avg_percentage, 2) . '%',
                round($data->best_percentage, 2) . '%',
                round($data->worst_percentage, 2) . '%',
                $totalTimeFormatted
            ];
        }

        $filename = 'student_summary_' . date('Y-m-d_H-i-s') . '.csv';
        
        return $this->generateCsvResponse($csvData, $filename);
    }

    private function generateCsvResponse($csvData, $filename)
    {
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function resultDetails(StudentResult $result)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Load relationships
        $result->load(['student', 'assessment.questions']);

        // Get question-wise breakdown
        $questionBreakdown = [];
        $answers = $result->answers ?? [];

        foreach ($result->assessment->questions as $question) {
            $questionId = $question->id;
            $userAnswer = isset($answers[$questionId]) ? $answers[$questionId] : null;
            $isCorrect = false;
            $options = $question->options ?? [];
            
            // Determine correct answer (letter and index)
            $correctAnswerLetter = null;
            $correctAnswerIndex = null;
            
            // First, try to get correct_answer (should be a letter A, B, C, D)
            if (!empty($question->correct_answer)) {
                $correctAnswerLetter = strtoupper(trim($question->correct_answer));
                if (in_array($correctAnswerLetter, ['A', 'B', 'C', 'D'])) {
                    $correctAnswerIndex = ord($correctAnswerLetter) - ord('A');
                }
            }
            
            // Fallback: use correct_option (numeric index)
            if ($correctAnswerIndex === null && isset($question->correct_option) && is_numeric($question->correct_option)) {
                $correctAnswerIndex = (int)$question->correct_option;
                $letters = ['A', 'B', 'C', 'D'];
                $correctAnswerLetter = $letters[$correctAnswerIndex] ?? null;
            }
            
            // Normalize user answer to both letter and index
            $userAnswerLetter = null;
            $userAnswerIndex = null;
            
            if ($userAnswer !== null) {
                // Check if user answer is a letter (A, B, C, D)
                if (is_string($userAnswer) && in_array(strtoupper($userAnswer), ['A', 'B', 'C', 'D'])) {
                    $userAnswerLetter = strtoupper($userAnswer);
                    $userAnswerIndex = ord($userAnswerLetter) - ord('A');
                }
                // Check if user answer is a numeric index (0, 1, 2, 3)
                elseif (is_numeric($userAnswer) && $userAnswer >= 0 && $userAnswer <= 3) {
                    $userAnswerIndex = (int)$userAnswer;
                    $letters = ['A', 'B', 'C', 'D'];
                    $userAnswerLetter = $letters[$userAnswerIndex];
                }
            }
            
            // Check if user's answer is correct (compare both letter and index)
            if ($userAnswerLetter !== null && $correctAnswerLetter !== null) {
                $isCorrect = $userAnswerLetter === $correctAnswerLetter;
            } elseif ($userAnswerIndex !== null && $correctAnswerIndex !== null) {
                $isCorrect = $userAnswerIndex === $correctAnswerIndex;
            }
            
            // Get answer texts
            $userAnswerText = 'Not Answered';
            if ($userAnswerIndex !== null && isset($options[$userAnswerIndex])) {
                $userAnswerText = $options[$userAnswerIndex];
            }
            
            $correctAnswerText = 'N/A';
            if ($correctAnswerIndex !== null && isset($options[$correctAnswerIndex])) {
                $correctAnswerText = $options[$correctAnswerIndex];
            }
            
            $questionBreakdown[] = [
                'question' => [
                    'id' => $question->id,
                    'question_text' => $question->question_text ?? $question->question ?? 'N/A',
                    'options' => $options
                ],
                'user_answer' => $userAnswerLetter ?? $userAnswerIndex,
                'user_answer_letter' => $userAnswerLetter,
                'user_answer_index' => $userAnswerIndex,
                'correct_answer_letter' => $correctAnswerLetter,
                'correct_answer_index' => $correctAnswerIndex,
                'is_correct' => $isCorrect,
                'user_answer_text' => $userAnswerText,
                'correct_answer_text' => $correctAnswerText
            ];
        }

        // Calculate statistics
        $totalQuestions = count($result->assessment->questions);
        $answeredQuestions = count(array_filter($answers, fn($a) => $a !== null));
        $correctAnswers = collect($questionBreakdown)->where('is_correct', true)->count();
        $unansweredQuestions = collect($questionBreakdown)->where('user_answer', null)->count();
        $wrongAnswers = $answeredQuestions - $correctAnswers;
        $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;

        // Prepare response data
        $data = [
            'student_name' => $result->student->name,
            'student_email' => $result->student->email,
            'assessment_name' => $result->assessment->title,
            'category' => $result->assessment->category,
            'submitted_at' => $result->submitted_at ? $result->submitted_at->format('M d, Y H:i:s') : 'N/A',
            'time_taken' => round($result->time_taken / 60, 1) . ' minutes',
            'score' => $result->score,
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $wrongAnswers,
            'unanswered_questions' => $unansweredQuestions,
            'percentage' => $percentage,
            'grade' => $percentage >= 90 ? 'A' : 
                      ($percentage >= 80 ? 'B' : 
                      ($percentage >= 70 ? 'C' : 
                      ($percentage >= 60 ? 'D' : 'F'))),
            'pass_status' => $percentage >= ($result->assessment->pass_percentage ?? 50) ? 'Passed' : 'Failed',
            'question_breakdown' => $questionBreakdown
        ];

        // Return JSON for AJAX requests
        if (request()->ajax()) {
            return response()->json($data);
        }

        // Return view for direct access
        return view('admin.reports.result-details', compact('result', 'data'));
    }

    public function questionAnalysis(Assessment $assessment): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $questions = $assessment->questions()->get();
        $questionStats = [];

        foreach ($questions as $question) {
            // Count correct and incorrect answers for this question
            $results = StudentResult::where('assessment_id', $assessment->id)
                ->whereNotNull('answers')
                ->get();

            $totalAttempts = 0;
            $correctAttempts = 0;
            $optionCounts = [0, 0, 0, 0]; // For options A, B, C, D

            foreach ($results as $result) {
                $answers = $result->answers;
                if (isset($answers[$question->id])) {
                    $totalAttempts++;
                    $userAnswer = $answers[$question->id];

                    // Count option selection
                    if ($userAnswer >= 0 && $userAnswer < 4) {
                        $optionCounts[$userAnswer]++;
                    }

                    // Check if correct
                    if ($question->isCorrectAnswer($userAnswer)) {
                        $correctAttempts++;
                    }
                }
            }

            $questionStats[] = [
                'question' => $question,
                'total_attempts' => $totalAttempts,
                'correct_attempts' => $correctAttempts,
                'accuracy_rate' => $totalAttempts > 0 ? round(($correctAttempts / $totalAttempts) * 100, 2) : 0,
                'option_counts' => $optionCounts,
                'option_percentages' => $totalAttempts > 0 
                    ? array_map(function($count) use ($totalAttempts) {
                        return round(($count / $totalAttempts) * 100, 2);
                    }, $optionCounts)
                    : [0, 0, 0, 0]
            ];
        }

        return view('admin.reports.question-analysis', compact('assessment', 'questionStats'));
    }
}