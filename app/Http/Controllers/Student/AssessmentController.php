<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\StudentAssessment;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    /**
     * Display a listing of available assessments
     */
    public function index(Request $request): View
    {
        $query = Assessment::active()
            ->withCount('questions') // Count questions instead of loading all data - much faster
            ->where(function($q) {
                $q->where('start_date', '<=', now())->orWhereNull('start_date');
            })
            ->where(function($q) {
                $q->where('end_date', '>=', now())->orWhereNull('end_date');
            });
        
        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }
        
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        $assessments = $query->paginate(10);
        
        // Add status information for each assessment
        $assessments->getCollection()->transform(function ($assessment) {
            // Check if student has completed this assessment using StudentResult
            $result = \App\Models\StudentResult::where('student_id', Auth::id())
                ->where('assessment_id', $assessment->id)
                ->orderBy('submitted_at', 'desc')
                ->first();
            
            if ($result) {
                // Create a mock attempt object with the data we need
                $attempt = (object)[
                    'id' => $result->id,
                    'status' => 'completed',
                    'percentage' => $result->total_questions > 0 ? 
                        round(($result->score / $result->total_questions) * 100, 2) : 0,
                    'score' => $result->score,
                    'total_questions' => $result->total_questions,
                ];
                $assessment->student_attempt = $attempt;
            } else {
                $assessment->student_attempt = null;
            }
            
            return $assessment;
        });
        
        return view('student.assessments.index', compact('assessments'));
    }

    /**
     * Display the specified assessment
     */
    public function show(Assessment $assessment): View
    {
        // Check if assessment is available
        if (!$assessment->isCurrentlyActive()) {
            abort(404, 'Assessment is not currently available');
        }
        
        // Check if student has already attempted and multiple attempts are not allowed
        $existingAttempt = $assessment->studentAssessments()
            ->where('student_id', Auth::id())
            ->where('status', 'completed')
            ->first();
        
        if ($existingAttempt && !$assessment->allow_multiple_attempts) {
            return redirect()->route('student.results.show', $existingAttempt)
                ->with('status', 'You have already completed this assessment.');
        }
        
        // Load questions relationship for the view
        $assessment->load('questions');
        
        return view('student.assessments.show', compact('assessment'));
    }

    /**
     * Start taking an assessment
     */
    public function start(Assessment $assessment): RedirectResponse
    {
        // Check if assessment is available
        if (!$assessment->isCurrentlyActive()) {
            return back()->withErrors(['error' => 'Assessment is not currently available']);
        }
        
        // Check if student has already attempted and multiple attempts are not allowed
        $existingAttempt = $assessment->studentAssessments()
            ->where('student_id', Auth::id())
            ->where('status', 'completed')
            ->first();
        
        if ($existingAttempt && !$assessment->allow_multiple_attempts) {
            return redirect()->route('student.results.show', $existingAttempt)
                ->with('status', 'You have already completed this assessment.');
        }
        
        // Create or get existing attempt
        $studentAssessment = $assessment->studentAssessments()
            ->where('student_id', Auth::id())
            ->where('status', 'started')
            ->first();
        
        if (!$studentAssessment) {
            $studentAssessment = StudentAssessment::create([
                'student_id' => Auth::id(),
                'assessment_id' => $assessment->id,
                'start_time' => now(),
                'total_marks' => $assessment->total_marks,
                'status' => 'started'
            ]);
        }
        
        return redirect()->route('student.assessments.take', [$assessment, 'attempt' => $studentAssessment->id]);
    }

    /**
     * Take an assessment
     */
    public function take(Assessment $assessment, Request $request): View
    {
        $attemptId = $request->query('attempt');
        
        $studentAssessment = StudentAssessment::where('id', $attemptId)
            ->where('student_id', Auth::id())
            ->where('assessment_id', $assessment->id)
            ->where('status', 'started')
            ->firstOrFail();
        
        // Load questions with pivot order
        $questions = $assessment->questions()
            ->withPivot('order')
            ->orderBy('assessment_questions.order')
            ->get();
        
        // Load existing answers
        $existingAnswers = $studentAssessment->studentAnswers()
            ->pluck('student_answer', 'question_id')
            ->toArray();
        
        return view('student.assessments.take', compact('assessment', 'studentAssessment', 'questions', 'existingAnswers'));
    }

    /**
     * Save assessment progress
     */
    public function saveProgress(Request $request, Assessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'attempt_id' => 'required|exists:student_assessments,id',
            'answers' => 'array',
            'answers.*' => 'nullable|string',
        ]);
        
        $studentAssessment = StudentAssessment::where('id', $validated['attempt_id'])
            ->where('student_id', Auth::id())
            ->where('assessment_id', $assessment->id)
            ->where('status', 'started')
            ->firstOrFail();
        
        // Save answers
        foreach ($validated['answers'] as $questionId => $answer) {
            if ($answer !== null) {
                StudentAnswer::updateOrCreate(
                    [
                        'student_assessment_id' => $studentAssessment->id,
                        'question_id' => $questionId
                    ],
                    [
                        'student_answer' => $answer
                    ]
                );
            }
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Submit an assessment
     */
    public function submit(Request $request, Assessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'attempt_id' => 'required|exists:student_assessments,id',
        ]);
        
        $studentAssessment = StudentAssessment::where('id', $validated['attempt_id'])
            ->where('student_id', Auth::id())
            ->where('assessment_id', $assessment->id)
            ->where('status', 'started')
            ->firstOrFail();
        
        // Calculate results
        $questions = $assessment->questions;
        $totalObtainedMarks = 0;
        $totalTimeTaken = 0;
        
        DB::beginTransaction();
        try {
            foreach ($questions as $question) {
                $studentAnswer = $studentAssessment->studentAnswers()
                    ->where('question_id', $question->id)
                    ->first();
                
                if ($studentAnswer) {
                    $isCorrect = $question->isCorrectAnswer($studentAnswer->student_answer);
                    $marksObtained = $isCorrect ? $question->marks : 0;
                    
                    $studentAnswer->update([
                        'is_correct' => $isCorrect,
                        'marks_obtained' => $marksObtained
                    ]);
                    
                    $totalObtainedMarks += $marksObtained;
                }
            }
            
            // Calculate percentage
            $percentage = $assessment->total_marks > 0 ? 
                round(($totalObtainedMarks / $assessment->total_marks) * 100, 2) : 0;
            
            // Determine pass/fail status
            $passStatus = $percentage >= $assessment->pass_percentage ? 'pass' : 'fail';
            
            // Calculate time taken
            $timeTaken = $studentAssessment->start_time ? 
                now()->diffInSeconds($studentAssessment->start_time) : 0;
            
            // Update student assessment
            $studentAssessment->update([
                'end_time' => now(),
                'submit_time' => now(),
                'status' => 'completed',
                'obtained_marks' => $totalObtainedMarks,
                'percentage' => $percentage,
                'pass_status' => $passStatus,
                'time_taken' => $timeTaken
            ]);
            
            DB::commit();
            
            return redirect()->route('student.results.show', $studentAssessment)
                ->with('status', 'Assessment submitted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to submit assessment: ' . $e->getMessage()]);
        }
    }
}