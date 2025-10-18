<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\StudentAssessment;
use App\Models\StudentAnswer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Optimized version of StudentAssessmentController with performance improvements
 */
class OptimizedStudentAssessmentController extends Controller
{
    /**
     * Cache TTL constants
     */
    private const CACHE_TTL_SHORT = 300; // 5 minutes
    private const CACHE_TTL_MEDIUM = 600; // 10 minutes
    private const CACHE_TTL_LONG = 3600; // 1 hour

    /**
     * Display available assessments with optimized queries
     */
    public function index()
    {
        $studentId = auth()->id();
        $cacheKey = "student_{$studentId}_dashboard";
        
        $data = Cache::remember($cacheKey, self::CACHE_TTL_MEDIUM, function () use ($studentId) {
            // Single optimized query with all necessary data
            $availableAssessments = Assessment::query()
                ->select('id', 'title', 'description', 'category', 'total_time', 'total_marks', 'start_date', 'end_date')
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->withCount('questions')
                ->with(['studentAssessments' => function ($query) use ($studentId) {
                    $query->where('student_id', $studentId)
                          ->select('id', 'assessment_id', 'status', 'percentage', 'created_at');
                }])
                ->get()
                ->map(function ($assessment) {
                    // Calculate attempts and best score in memory
                    $attempts = $assessment->studentAssessments;
                    $assessment->attempts_count = $attempts->count();
                    $assessment->best_score = $attempts->max('percentage');
                    $assessment->can_attempt = $assessment->allow_multiple_attempts || $assessment->attempts_count === 0;
                    unset($assessment->studentAssessments); // Remove raw data
                    return $assessment;
                });
            
            // Get recent completed assessments
            $completedAssessments = StudentAssessment::query()
                ->select('id', 'assessment_id', 'percentage', 'pass_status', 'created_at')
                ->where('student_id', $studentId)
                ->where('status', 'completed')
                ->with('assessment:id,title,category')
                ->latest()
                ->take(5)
                ->get();
            
            return compact('availableAssessments', 'completedAssessments');
        });
        
        return view('student.assessments.index', $data);
    }

    /**
     * Show assessment details with optimized question loading
     */
    public function show($id)
    {
        $studentId = auth()->id();
        $cacheKey = "assessment_{$id}_student_{$studentId}";
        
        $data = Cache::remember($cacheKey, self::CACHE_TTL_SHORT, function () use ($id, $studentId) {
            $assessment = Assessment::query()
                ->select('id', 'title', 'description', 'total_time', 'total_marks', 'pass_percentage', 'allow_multiple_attempts')
                ->withCount('questions')
                ->findOrFail($id);
            
            // Check previous attempts efficiently
            $previousAttempts = StudentAssessment::query()
                ->where('student_id', $studentId)
                ->where('assessment_id', $id)
                ->select('id', 'status', 'percentage', 'created_at')
                ->get();
            
            $canStart = $assessment->allow_multiple_attempts || $previousAttempts->isEmpty();
            
            return compact('assessment', 'previousAttempts', 'canStart');
        });
        
        return view('student.assessments.show', $data);
    }

    /**
     * Start assessment with optimized session management
     */
    public function start(Request $request, $id)
    {
        $studentId = auth()->id();
        
        // Use database transaction for consistency
        DB::beginTransaction();
        
        try {
            $assessment = Assessment::select('id', 'title', 'total_time', 'allow_multiple_attempts')
                ->findOrFail($id);
            
            // Check if can start
            if (!$assessment->allow_multiple_attempts) {
                $existingAttempt = StudentAssessment::where('student_id', $studentId)
                    ->where('assessment_id', $id)
                    ->exists();
                
                if ($existingAttempt) {
                    return redirect()->route('student.assessments.show', $id)
                        ->with('error', 'You have already attempted this assessment.');
                }
            }
            
            // Create new attempt
            $studentAssessment = StudentAssessment::create([
                'student_id' => $studentId,
                'assessment_id' => $id,
                'start_time' => now(),
                'status' => 'in_progress',
            ]);
            
            // Store minimal data in session
            session([
                'assessment_id' => $id,
                'student_assessment_id' => $studentAssessment->id,
                'start_time' => now()->timestamp,
                'duration' => $assessment->total_time * 60, // Convert to seconds
            ]);
            
            // Clear relevant caches
            Cache::forget("student_{$studentId}_dashboard");
            Cache::forget("assessment_{$id}_student_{$studentId}");
            
            DB::commit();
            
            return redirect()->route('student.assessments.take', $id);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to start assessment', [
                'assessment_id' => $id,
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to start assessment. Please try again.');
        }
    }

    /**
     * Display assessment questions with pagination
     */
    public function take($id)
    {
        $studentAssessmentId = session('student_assessment_id');
        
        if (!$studentAssessmentId) {
            return redirect()->route('student.assessments.show', $id)
                ->with('error', 'Please start the assessment first.');
        }
        
        // Load questions with existing answers
        $assessment = Assessment::select('id', 'title', 'total_time')
            ->with(['questions' => function ($query) use ($studentAssessmentId) {
                $query->select('questions.id', 'question_text', 'options', 'category')
                      ->where('is_active', true)
                      ->orderBy('assessment_questions.order')
                      ->with(['studentAnswers' => function ($q) use ($studentAssessmentId) {
                          $q->where('student_assessment_id', $studentAssessmentId)
                            ->select('question_id', 'student_answer');
                      }]);
            }])
            ->findOrFail($id);
        
        // Calculate remaining time
        $startTime = session('start_time');
        $duration = session('duration');
        $elapsed = now()->timestamp - $startTime;
        $remainingTime = max(0, $duration - $elapsed);
        
        return view('student.assessments.take', compact('assessment', 'remainingTime'));
    }

    /**
     * Save answer with batching support
     */
    public function saveAnswer(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|string|max:1',
        ]);
        
        $studentAssessmentId = session('student_assessment_id');
        
        if (!$studentAssessmentId) {
            return response()->json(['error' => 'Session expired'], 401);
        }
        
        // Use upsert for efficient insert/update
        StudentAnswer::upsert(
            [
                'student_assessment_id' => $studentAssessmentId,
                'question_id' => $request->question_id,
                'student_answer' => $request->answer,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            ['student_assessment_id', 'question_id'], // Unique keys
            ['student_answer', 'updated_at'] // Update columns
        );
        
        return response()->json(['status' => 'saved']);
    }

    /**
     * Batch save multiple answers
     */
    public function batchSaveAnswers(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required|string|max:1',
        ]);
        
        $studentAssessmentId = session('student_assessment_id');
        
        if (!$studentAssessmentId) {
            return response()->json(['error' => 'Session expired'], 401);
        }
        
        $answers = collect($request->answers)->map(function ($answer) use ($studentAssessmentId) {
            return [
                'student_assessment_id' => $studentAssessmentId,
                'question_id' => $answer['question_id'],
                'student_answer' => $answer['answer'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();
        
        // Batch upsert for efficiency
        StudentAnswer::upsert(
            $answers,
            ['student_assessment_id', 'question_id'],
            ['student_answer', 'updated_at']
        );
        
        return response()->json(['status' => 'saved', 'count' => count($answers)]);
    }

    /**
     * Submit assessment with optimized scoring
     */
    public function submit(Request $request, $id)
    {
        $studentAssessmentId = session('student_assessment_id');
        
        if (!$studentAssessmentId) {
            return redirect()->route('student.assessments.show', $id)
                ->with('error', 'Assessment session expired.');
        }
        
        DB::beginTransaction();
        
        try {
            // Get all questions with correct answers in one query
            $questions = Question::whereHas('assessments', function ($query) use ($id) {
                    $query->where('assessment_id', $id);
                })
                ->where('is_active', true)
                ->select('id', 'correct_option')
                ->get()
                ->keyBy('id');
            
            // Get all student answers
            $studentAnswers = StudentAnswer::where('student_assessment_id', $studentAssessmentId)
                ->select('question_id', 'student_answer')
                ->get();
            
            // Calculate score in memory
            $correctCount = 0;
            $totalQuestions = $questions->count();
            
            // Batch update answer correctness
            $updates = [];
            foreach ($studentAnswers as $answer) {
                $question = $questions->get($answer->question_id);
                if ($question) {
                    $isCorrect = $answer->student_answer === $question->correct_option;
                    if ($isCorrect) {
                        $correctCount++;
                    }
                    
                    $updates[] = [
                        'id' => $answer->id,
                        'is_correct' => $isCorrect,
                        'marks_obtained' => $isCorrect ? 1 : 0,
                    ];
                }
            }
            
            // Batch update answers
            if (!empty($updates)) {
                $cases = [];
                $ids = [];
                
                foreach ($updates as $update) {
                    $cases['is_correct'][] = "WHEN {$update['id']} THEN " . ($update['is_correct'] ? 'true' : 'false');
                    $cases['marks_obtained'][] = "WHEN {$update['id']} THEN {$update['marks_obtained']}";
                    $ids[] = $update['id'];
                }
                
                $idList = implode(',', $ids);
                $isCorrectCase = implode(' ', $cases['is_correct']);
                $marksCase = implode(' ', $cases['marks_obtained']);
                
                DB::statement("
                    UPDATE student_answers 
                    SET is_correct = CASE id {$isCorrectCase} END,
                        marks_obtained = CASE id {$marksCase} END
                    WHERE id IN ({$idList})
                ");
            }
            
            // Calculate final score
            $percentage = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
            $passPercentage = Assessment::where('id', $id)->value('pass_percentage') ?? 60;
            
            // Update student assessment
            StudentAssessment::where('id', $studentAssessmentId)->update([
                'end_time' => now(),
                'submit_time' => now(),
                'status' => 'completed',
                'total_marks' => $totalQuestions,
                'obtained_marks' => $correctCount,
                'percentage' => $percentage,
                'pass_status' => $percentage >= $passPercentage ? 'pass' : 'fail',
                'time_taken' => now()->timestamp - session('start_time'),
            ]);
            
            // Clear session and cache
            session()->forget(['assessment_id', 'student_assessment_id', 'start_time', 'duration']);
            Cache::forget("student_" . auth()->id() . "_dashboard");
            
            DB::commit();
            
            return redirect()->route('student.assessments.result', $studentAssessmentId);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to submit assessment', [
                'assessment_id' => $id,
                'student_assessment_id' => $studentAssessmentId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to submit assessment. Please contact support.');
        }
    }

    /**
     * Display assessment result with caching
     */
    public function result($studentAssessmentId)
    {
        $studentId = auth()->id();
        $cacheKey = "result_{$studentAssessmentId}";
        
        $result = Cache::remember($cacheKey, self::CACHE_TTL_LONG, function () use ($studentAssessmentId, $studentId) {
            return StudentAssessment::where('id', $studentAssessmentId)
                ->where('student_id', $studentId)
                ->with([
                    'assessment:id,title,total_marks,pass_percentage',
                    'answers' => function ($query) {
                        $query->select('student_assessment_id', 'question_id', 'student_answer', 'is_correct')
                              ->with('question:id,question_text,options,correct_option');
                    }
                ])
                ->firstOrFail();
        });
        
        return view('student.assessments.result', compact('result'));
    }

    /**
     * Get assessment history with pagination
     */
    public function history(Request $request)
    {
        $studentId = auth()->id();
        $page = $request->get('page', 1);
        $cacheKey = "student_{$studentId}_history_page_{$page}";
        
        $history = Cache::remember($cacheKey, self::CACHE_TTL_MEDIUM, function () use ($studentId) {
            return StudentAssessment::where('student_id', $studentId)
                ->where('status', 'completed')
                ->with('assessment:id,title,category')
                ->select('id', 'assessment_id', 'percentage', 'pass_status', 'created_at')
                ->latest()
                ->paginate(10);
        });
        
        return view('student.assessments.history', compact('history'));
    }
}
