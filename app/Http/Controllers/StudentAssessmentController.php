<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Question;
use App\Models\StudentResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentAssessmentController extends Controller
{
    public function index(): View
    {
        if (!Auth::check() || !Auth::user()->isStudent()) {
            abort(403);
        }

        // Optimize with eager loading and selective columns
        $assessments = Assessment::active()
            ->select('id', 'name', 'description', 'category', 'difficulty_level', 'total_time', 'total_marks', 'pass_percentage', 'is_active', 'created_at')
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get user's results for each assessment with optimized query
        $assessmentIds = $assessments->pluck('id');
        $userResults = StudentResult::where('student_id', Auth::id())
            ->whereIn('assessment_id', $assessmentIds)
            ->select('id', 'assessment_id', 'score', 'total_questions', 'submitted_at')
            ->get()
            ->keyBy('assessment_id');

        return view('student.assessments.index', compact('assessments', 'userResults'));
    }

    public function show(Assessment $assessment): View|RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->isStudent()) {
            abort(403);
        }

        if (!$assessment->is_active) {
            abort(404, 'Assessment not found or not active');
        }

        // Check if user has already taken this assessment - optimized query
        $existingResult = StudentResult::where('student_id', Auth::id())
            ->where('assessment_id', $assessment->id)
            ->exists();

        // Only redirect if they've taken it AND multiple attempts are NOT allowed
        if ($existingResult && !$assessment->allow_multiple_attempts) {
            return redirect()->route('student.assessments.result', $assessment)
                ->with('info', 'You have already taken this assessment. Multiple attempts are not allowed.');
        }

        // Get questions for this assessment with selective columns
        $questions = $assessment->questions()
            ->where('is_active', true)
            ->select('questions.id', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'correct_option', 'time_per_question')
            ->get();
        
        if ($questions->isEmpty()) {
            return redirect()->route('student.assessments.index')
                ->with('error', 'This assessment has no questions.');
        }

        // Shuffle questions for randomization
        $questions = $questions->shuffle();
        
        return view('student.assessments.show', compact('assessment', 'questions'));
    }

    public function start(Assessment $assessment): View|RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->isStudent()) {
            abort(403);
        }

        if (!$assessment->is_active) {
            abort(404, 'Assessment not found or not active');
        }

        // Check if user has already taken this assessment
        $existingResult = StudentResult::where('student_id', Auth::id())
            ->where('assessment_id', $assessment->id)
            ->first();

        // Only redirect if they've taken it AND multiple attempts are NOT allowed
        if ($existingResult && !$assessment->allow_multiple_attempts) {
            return redirect()->route('student.assessments.result', $assessment)
                ->with('info', 'You have already taken this assessment. Multiple attempts are not allowed.');
        }

        // Get questions for this assessment
        $questions = $assessment->questions()->where('is_active', true)->get();
        
        if ($questions->isEmpty()) {
            return redirect()->route('student.assessments.index')
                ->with('error', 'This assessment has no questions.');
        }

        // Shuffle questions for randomization
        $questions = $questions->shuffle();
        
        // Initialize empty variables for the view
        $existingAnswers = [];
        $studentAssessment = null;
        
        return view('student.assessments.take', compact('assessment', 'questions', 'existingAnswers', 'studentAssessment'));
    }

    public function submit(Request $request, Assessment $assessment): RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->isStudent()) {
            abort(403);
        }

        if (!$assessment->is_active) {
            abort(404, 'Assessment not found or not active');
        }

        // Check if user has already taken this assessment
        $existingResult = StudentResult::where('student_id', Auth::id())
            ->where('assessment_id', $assessment->id)
            ->first();

        // Only block submission if they've taken it AND multiple attempts are NOT allowed
        if ($existingResult && !$assessment->allow_multiple_attempts) {
            return redirect()->route('student.assessments.result', $assessment)
                ->with('info', 'You have already taken this assessment. Multiple attempts are not allowed.');
        }
        
        $validated = $request->validate([
            'answers' => 'required|array',
            'time_taken' => 'required|integer|min:1',
        ]);

        // Get all questions for this assessment
        $questions = $assessment->questions()->where('is_active', true)->get()->keyBy('id');
        $totalQuestions = $questions->count();
        $correctAnswers = 0;
        $userAnswers = [];

        // Calculate score
        foreach ($validated['answers'] as $questionId => $answer) {
            $question = $questions->get($questionId);
            if ($question) {
                // Store the answer as-is (letter A, B, C, D)
                $userAnswers[$questionId] = $answer;
                // Check if correct (the isCorrectAnswer method handles letter comparison)
                if ($question->isCorrectAnswer($answer)) {
                    $correctAnswers++;
                }
            }
        }

        // Store result
        $result = StudentResult::create([
            'student_id' => Auth::id(),
            'assessment_id' => $assessment->id,
            'score' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'time_taken' => $validated['time_taken'],
            'answers' => $userAnswers,
            'submitted_at' => now(),
        ]);

        return redirect()->route('student.assessments.result', $assessment)
            ->with('status', 'Assessment submitted successfully!');
    }

    public function result(Assessment $assessment): View|RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->isStudent()) {
            abort(403);
        }

        $result = StudentResult::where('student_id', Auth::id())
            ->where('assessment_id', $assessment->id)
            ->with(['assessment', 'student'])
            ->orderBy('id', 'desc')
            ->first();

        if (!$result) {
            return redirect()->route('student.assessments.index')
                ->with('error', 'No result found for this assessment.');
        }

        // Get questions with user answers for detailed review
        $questions = $assessment->questions()->get()->keyBy('id');
        $detailedResults = [];
        
        // Ensure answers is an array
        $answers = is_array($result->answers) ? $result->answers : json_decode($result->answers, true) ?? [];

        foreach ($answers as $questionId => $userAnswer) {
            // Cast questionId to int to ensure proper matching
            $questionId = (int)$questionId;
            $question = $questions->get($questionId);
            if ($question) {
                // Get the correct answer as a letter
                $correctAnswerLetter = $question->correct_answer;
                if (!$correctAnswerLetter && isset($question->correct_option)) {
                    // Convert numeric index to letter if needed
                    $letters = ['A', 'B', 'C', 'D'];
                    $correctAnswerLetter = $letters[$question->correct_option] ?? 'A';
                }
                
                $detailedResults[] = [
                    'question' => $question,
                    'user_answer' => $userAnswer,  // This is already a letter (A, B, C, D)
                    'is_correct' => $question->isCorrectAnswer($userAnswer),
                    'correct_answer' => $correctAnswerLetter,  // Now this is also a letter
                ];
            }
        }

        return view('student.assessments.result', compact('result', 'assessment', 'detailedResults'));
    }

    public function history(): View
    {
        if (!Auth::check() || !Auth::user()->isStudent()) {
            abort(403);
        }

        $studentId = Auth::id();
        
        // Eager load assessment with selective columns
        $results = StudentResult::where('student_id', $studentId)
            ->with(['assessment:id,name,category,difficulty_level'])
            ->select('id', 'assessment_id', 'score', 'total_questions', 'time_taken', 'submitted_at')
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        // Calculate overall statistics with a single query
        $statsQuery = StudentResult::where('student_id', $studentId)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('AVG((score::float / NULLIF(total_questions, 0)) * 100) as avg_percentage')
            ->selectRaw('MAX((score::float / NULLIF(total_questions, 0)) * 100) as max_percentage')
            ->selectRaw('SUM(time_taken) as total_time')
            ->first();
        
        $stats = [
            'total_assessments' => $statsQuery->total_count ?? 0,
            'average_score' => round($statsQuery->avg_percentage ?? 0, 2),
            'highest_score' => round($statsQuery->max_percentage ?? 0, 2),
            'total_time_spent' => $statsQuery->total_time ?? 0,
        ];

        return view('student.assessments.history', compact('results', 'stats'));
    }

    public function analytics(): View
    {
        if (!Auth::check() || !Auth::user()->isStudent()) {
            abort(403);
        }

        $studentId = Auth::id();

        // Get performance by category
        $categoryPerformance = DB::table('student_results')
            ->join('assessments', 'student_results.assessment_id', '=', 'assessments.id')
            ->where('student_results.student_id', $studentId)
            ->select('assessments.category')
            ->selectRaw('AVG((score / total_questions) * 100) as avg_percentage')
            ->selectRaw('COUNT(*) as attempts')
            ->groupBy('assessments.category')
            ->get();

        // Get monthly performance trend (PostgreSQL compatible)
        $monthlyPerformance = StudentResult::where('student_id', $studentId)
            ->selectRaw('EXTRACT(YEAR FROM submitted_at) as year, EXTRACT(MONTH FROM submitted_at) as month')
            ->selectRaw('AVG((score / total_questions) * 100) as avg_percentage')
            ->selectRaw('COUNT(*) as attempts')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();

        return view('student.assessments.analytics', compact(
            'categoryPerformance', 
            'monthlyPerformance'
        ));
    }
}