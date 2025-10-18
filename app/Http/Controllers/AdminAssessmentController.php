<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminAssessmentController extends Controller
{
    public function index(Request $request): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $query = Assessment::select('id', 'title', 'description', 'category', 'difficulty_level', 'total_time', 'total_marks', 'pass_percentage', 'status', 'is_active', 'created_at', 'created_by')
            ->with(['creator:id,name'])
            ->withCount(['questions', 'studentResults'])
            ->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });
        }
        
        $assessments = $query->paginate(10)->withQueryString();

        return view('admin.assessments.index', compact('assessments'))
            ->with('errors', session()->get('errors', new \Illuminate\Support\MessageBag()));
    }

    public function create(): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        return view('admin.assessments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Convert checkbox values to boolean before validation
        $request->merge([
            'allow_multiple_attempts' => $request->has('allow_multiple_attempts'),
            'show_results_immediately' => $request->has('show_results_immediately'),
            'show_correct_answers' => $request->has('show_correct_answers'),
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1|max:300',
            'total_marks' => 'required|integer|min:1',
            'pass_percentage' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,inactive,draft',
            'category' => 'required|string|max:100',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'allow_multiple_attempts' => 'boolean',
            'show_results_immediately' => 'boolean',
            'show_correct_answers' => 'boolean',
        ]);

        // Map duration to total_time
        $validated['total_time'] = $validated['duration'];
        unset($validated['duration']);
        
        // Set created_by to current user
        $validated['created_by'] = Auth::id();
        
        // Set is_active based on status
        $validated['is_active'] = ($validated['status'] === 'active');

        $assessment = Assessment::create($validated);

        return redirect()->route('admin.assessments.index')
            ->with('status', 'Assessment created successfully');
    }

    public function duplicate(Assessment $assessment): RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Prepare data for the new assessment
            $newData = $assessment->toArray();
            unset($newData['id']);
            unset($newData['created_at']);
            unset($newData['updated_at']);
            
            // Update title to indicate it's a copy
            $newData['title'] = $assessment->title . ' (Copy)';
            
            // Set to draft status for the copy
            $newData['status'] = 'draft';
            $newData['is_active'] = false;
            
            // Set deleted_at to null to ensure it's not soft deleted
            $newData['deleted_at'] = null;
            
            // Set created_by to current user
            $newData['created_by'] = Auth::id();
            
            // Create the new assessment
            $newAssessment = Assessment::create($newData);
            
            \Log::info('New assessment created', ['new_id' => $newAssessment->id]);

            // Duplicate the questions associated with this assessment
            $questionIds = $assessment->questions()->pluck('questions.id');
            if ($questionIds->count() > 0) {
                \Log::info('Attaching questions', ['count' => $questionIds->count()]);
                $newAssessment->questions()->attach($questionIds);
            }
            
            // Commit the transaction
            DB::commit();
            
            \Log::info('Assessment duplicated successfully', ['new_id' => $newAssessment->id]);
            
            return redirect()->route('admin.assessments.index')
                ->with('status', 'Assessment duplicated successfully as "' . $newAssessment->title . '". The copy has been set to draft status.');
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollback();
            
            \Log::error('Failed to duplicate assessment', [
                'assessment_id' => $assessment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to duplicate assessment: ' . $e->getMessage());
        }
    }

    public function edit($id, Request $request): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        // Fetch fresh assessment without any relationships to avoid Closure issues
        $assessment = Assessment::select([
            'id',
            'title',
            'description',
            'total_time',
            'total_marks',
            'pass_percentage',
            'start_date',
            'end_date',
            'status',
            'category',
            'difficulty_level',
            'allow_multiple_attempts',
            'show_results_immediately',
            'show_correct_answers',
            'is_active'
        ])->findOrFail($id);
        
        // Ensure no relationships are loaded
        $assessment->unsetRelations();
        
        // If this is a duplicate action, prepare the assessment data for duplication
        if ($request->query('action') === 'duplicate') {
            // Modify the title to indicate it's a copy
            $assessment->title = $assessment->title . ' (Copy)';
            // Reset the ID so it's treated as a new assessment
            $assessment->id = null;
        }
        
        return view('admin.assessments.edit', compact('assessment'));
    }

    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        // Convert checkbox values to boolean before validation
        $request->merge([
            'allow_multiple_attempts' => $request->has('allow_multiple_attempts'),
            'show_results_immediately' => $request->has('show_results_immediately'),
            'show_correct_answers' => $request->has('show_correct_answers'),
        ]);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1|max:300',
            'total_marks' => 'required|integer|min:1',
            'pass_percentage' => 'required|integer|min:1|max:100',
            'status' => 'required|in:active,inactive,draft',
            'category' => 'required|string|max:100',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'allow_multiple_attempts' => 'boolean',
            'show_results_immediately' => 'boolean',
            'show_correct_answers' => 'boolean',
        ]);
        
        // Map duration to total_time if needed
        if (isset($validated['duration'])) {
            $validated['total_time'] = $validated['duration'];
            unset($validated['duration']); // Remove duration from the array
        }
        
        $assessment->update($validated);

        return redirect()->route('admin.assessments.index')
            ->with('status', 'Assessment updated successfully');
    }

    public function destroy(Assessment $assessment): RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $assessment->delete();
        return redirect()->route('admin.assessments.index')
            ->with('status', 'Assessment deleted successfully');
    }

    public function toggleStatus(Request $request, Assessment $assessment)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:active,inactive,draft'
        ]);

        $newStatus = $request->status;
        $isActive = ($newStatus === 'active');
        
        // If trying to activate, ensure assessment has questions
        if ($isActive && $assessment->questions()->where('is_active', true)->count() === 0) {
            return back()->with('error', 'Cannot activate assessment: No active questions found. Please add questions first.');
        }

        // Update both status and is_active fields
        $assessment->update([
            'status' => $newStatus,
            'is_active' => $isActive
        ]);
        
        // Clear any caches
        \Cache::flush();

        $statusText = $isActive ? 'activated' : 'deactivated';
        $message = "Assessment {$statusText} successfully";
        
        if ($isActive) {
            $questionCount = $assessment->questions()->where('is_active', true)->count();
            $message .= " with {$questionCount} question(s)";
        }

        return redirect()->route('admin.assessments.index')
            ->with('status', $message);
    }

    public function questions(Assessment $assessment): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        // Get all questions for statistics (unpaginated)
        $allQuestions = $assessment->questions()->get();
        
        // Get paginated questions for display
        $questions = $assessment->questions()
            ->orderBy('order')
            ->paginate(20);
        
        return view('admin.assessments.questions', [
            'assessment' => $assessment,
            'questions' => $questions,
            'allQuestions' => $allQuestions
        ]);
    }
    
    public function removeAllQuestions(Assessment $assessment): RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        // Remove all questions from this assessment
        $assessment->questions()->detach();
        
        return redirect()->route('admin.assessments.questions', $assessment)
            ->with('status', 'All questions have been removed from this assessment.');
    }
    
    public function addQuestion(Assessment $assessment): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        return view('admin.assessments.add-question', compact('assessment'));
    }

    public function storeQuestion(Request $request, Assessment $assessment): RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D,0,1,2,3',
            'marks' => 'nullable|integer|min:1',
            'difficulty_level' => 'nullable|in:easy,medium,hard',
        ]);

        // Convert letter to number if needed
        $correctAnswer = $validated['correct_answer'];
        if (in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
            $letterToNumber = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
            $correctAnswer = $letterToNumber[$correctAnswer];
        } else {
            $correctAnswer = (int)$correctAnswer;
        }

        DB::beginTransaction();
        try {
            // Find or create category
            $category = \App\Models\Category::firstOrCreate(
                ['name' => $assessment->category],
                [
                    'description' => ucfirst($assessment->category) . ' category questions',
                    'is_active' => true
                ]
            );

            // Create the question - only using fields that exist in database
            // Convert options array to JSON string for database storage
            $optionsArray = [
                $validated['option_a'],
                $validated['option_b'],
                $validated['option_c'],
                $validated['option_d'],
            ];
            
            $question = Question::create([
                'question' => $validated['question_text'],
                'question_type' => 'mcq',
                'options' => json_encode($optionsArray),
                'correct_option' => $correctAnswer,
                'correct_answer' => ['A', 'B', 'C', 'D'][$correctAnswer],
                'marks' => $validated['marks'] ?? 1,
                'difficulty_level' => $validated['difficulty_level'] ?? 'medium',
                'category_id' => $category->id,
                'category' => $assessment->category, // Add category field
                'time_per_question' => 60,
                'is_active' => true,
                'order' => 0,
            ]);

            // Link question to assessment with order
            $order = $assessment->questions()->count() + 1;
            $assessment->questions()->attach($question->id, ['order' => $order]);
            
            DB::commit();
            
            return redirect()->route('admin.assessments.questions', $assessment)
                ->with('status', 'Question added to assessment successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create question: ' . $e->getMessage()])
                ->withInput();
        }
    }


    public function editQuestion(Assessment $assessment, Question $question): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        // Ensure question belongs to assessment
        if (!$assessment->questions()->where('questions.id', $question->id)->exists()) {
            abort(404);
        }
        
        return view('admin.assessments.edit-question', compact('assessment', 'question'));
    }

    public function updateQuestion(Request $request, Assessment $assessment, Question $question): RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D,0,1,2,3',
        ]);

        // Convert letter to number if needed
        $correctAnswer = $validated['correct_answer'];
        if (in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
            $letterToNumber = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
            $correctAnswer = $letterToNumber[$correctAnswer];
        } else {
            $correctAnswer = (int)$correctAnswer;
        }

        // Update the question
        $question->update([
            'question' => $validated['question_text'],
            'options' => json_encode([
                $validated['option_a'],
                $validated['option_b'],
                $validated['option_c'],
                $validated['option_d'],
            ]),
            'correct_option' => $correctAnswer,
            'correct_answer' => ['A', 'B', 'C', 'D'][$correctAnswer],
        ]);
        
        return redirect()->route('admin.assessments.questions', $assessment)
            ->with('status', 'Question updated successfully');
    }

    public function deleteQuestion(Assessment $assessment, Question $question): RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        // Remove question from assessment
        $assessment->questions()->detach($question->id);
        
        // Optionally delete the question entirely if not used in other assessments
        if ($question->assessments()->count() === 0) {
            $question->delete();
            $message = 'Question removed from assessment and deleted (not used elsewhere)';
        } else {
            $message = 'Question removed from assessment';
        }
        
        return redirect()->route('admin.assessments.questions', $assessment)
            ->with('status', $message);
    }

    public function show(Assessment $assessment): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        // Load relationships and count questions
        $assessment->loadCount(['questions', 'studentAssessments']);
        $assessment->load('questions');
        $assessment->load('studentResults', 'studentResults.student');
        
        // Calculate additional statistics if there are attempts
        if ($assessment->student_assessments_count > 0) {
            $assessment->average_score = $assessment->studentAssessments()
                ->where('status', 'completed')
                ->avg('percentage') ?? 0;
            
            $totalCompleted = $assessment->studentAssessments()
                ->where('status', 'completed')
                ->count();
            
            $totalPassed = $assessment->studentAssessments()
                ->where('status', 'completed')
                ->where('pass_status', 'pass')
                ->count();
            
            $assessment->pass_rate = $totalCompleted > 0 ? ($totalPassed / $totalCompleted) * 100 : 0;
        }
        
        return view('admin.assessments.show', compact('assessment'));
    }
}
