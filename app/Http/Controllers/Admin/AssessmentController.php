<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    /**
     * Display a listing of assessments
     */
    public function index(Request $request): View
    {
        $query = Assessment::with('creator')
            ->withCount('questions')
            ->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        $assessments = $query->paginate(10);
        
        return view('admin.assessments.index', compact('assessments'));
    }

    /**
     * Show the form for creating a new assessment
     */
    public function create(): View
    {
        return view('admin.assessments.create');
    }

    /**
     * Store a newly created assessment
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1|max:300',
            'total_marks' => 'required|integer|min:1',
            'pass_percentage' => 'required|integer|min:1|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,inactive,draft',
            'category' => 'required|string|max:100',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'allow_multiple_attempts' => 'boolean',
            'show_results_immediately' => 'boolean',
            'show_correct_answers' => 'boolean',
        ]);
        
        $validated['created_by'] = Auth::id();
        $validated['allow_multiple_attempts'] = $request->has('allow_multiple_attempts');
        $validated['show_results_immediately'] = $request->has('show_results_immediately');
        $validated['show_correct_answers'] = $request->has('show_correct_answers');
        
        $assessment = Assessment::create($validated);
        
        // Trigger RAG sync
        $this->syncRagKnowledge();
        
        return redirect()->route('admin.assessments.index')
            ->with('status', 'Assessment created successfully');
    }

    /**
     * Display the specified assessment
     */
    public function show(Assessment $assessment): View
    {
        // Load required relationships
        $assessment->load(['questions', 'creator']);
        $assessment->loadCount('questions');
        
        // Initialize empty collection for student assessments
        $studentAssessments = collect();
        
        // Try to load student assessments if table exists
        try {
            $studentAssessments = $assessment->studentAssessments()
                ->with('student')
                ->latest()
                ->limit(10)
                ->get();
            $assessment->loadCount('studentAssessments');
        } catch (\Exception $e) {
            // Table doesn't exist yet, use empty collection
        }
        
        // Add temporary attribute for studentAssessments collection
        $assessment->setRelation('studentAssessments', $studentAssessments);
        
        return view('admin.assessments.show', compact('assessment'));
    }

    /**
     * Show the form for editing the specified assessment
     */
    public function edit(Assessment $assessment): View
    {
        return view('admin.assessments.edit', compact('assessment'));
    }

    /**
     * Update the specified assessment
     */
    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1|max:300',
            'total_marks' => 'required|integer|min:1',
            'pass_percentage' => 'required|integer|min:1|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,inactive,draft',
            'category' => 'required|string|max:100',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'allow_multiple_attempts' => 'boolean',
            'show_results_immediately' => 'boolean',
            'show_correct_answers' => 'boolean',
        ]);
        
        $validated['allow_multiple_attempts'] = $request->has('allow_multiple_attempts');
        $validated['show_results_immediately'] = $request->has('show_results_immediately');
        $validated['show_correct_answers'] = $request->has('show_correct_answers');
        
        $assessment->update($validated);
        
        // Trigger RAG sync
        $this->syncRagKnowledge();
        
        return redirect()->route('admin.assessments.index')
            ->with('status', 'Assessment updated successfully');
    }

    /**
     * Remove the specified assessment
     */
    public function destroy(Assessment $assessment): RedirectResponse
    {
        // Check if students have attempted this assessment
        try {
            if ($assessment->studentAssessments()->exists()) {
                return back()->withErrors(['error' => 'Cannot delete assessment. Students have already attempted this assessment.']);
            }
        } catch (\Exception $e) {
            // Student assessments table doesn't exist, it's safe to delete
        }
        
        // Detach all questions before deleting
        $assessment->questions()->detach();
        
        $assessment->delete();
        
        // Trigger RAG sync
        $this->syncRagKnowledge();
        
        return redirect()->route('admin.assessments.index')
            ->with('status', 'Assessment deleted successfully');
    }

    /**
     * Toggle assessment status
     */
    public function toggleStatus(Request $request, Assessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,draft'
        ]);
        
        $assessment->update(['status' => $validated['status']]);
        
        // Trigger RAG sync
        $this->syncRagKnowledge();
        
        $statusText = ucfirst($validated['status']);
        return back()->with('status', "Assessment status changed to {$statusText}");
    }

    /**
     * Duplicate an assessment
     */
    public function duplicate(Assessment $assessment): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // Create a copy of the assessment
            $newAssessment = $assessment->replicate();
            $newAssessment->title = ($assessment->title ?? $assessment->name) . ' (Copy)';
            $newAssessment->name = ($assessment->name ?? $assessment->title) . ' (Copy)';
            $newAssessment->status = 'draft';
            $newAssessment->is_active = false;
            $newAssessment->created_by = Auth::id() ?? 1;
            $newAssessment->created_at = now();
            $newAssessment->updated_at = now();
            $newAssessment->save();

            // Copy the questions relationship with order preserved
            $questions = $assessment->questions()->withPivot('order')->get();
            $syncData = [];
            foreach ($questions as $question) {
                $syncData[$question->id] = ['order' => $question->pivot->order ?? 0];
            }
            $newAssessment->questions()->sync($syncData);

            DB::commit();
            
            return redirect()->route('admin.assessments.edit', $newAssessment)
                ->with('status', 'Assessment duplicated successfully. You can now edit the copy.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to duplicate assessment: ' . $e->getMessage()]);
        }
    }

    /**
     * Show questions for an assessment
     */
    public function questions(Assessment $assessment): View
    {
        // Get paginated questions for display
        $questions = $assessment->questions()
            ->withPivot('order')
            ->orderBy('order')
            ->paginate(20);
        
        // Get all questions for statistics (not paginated)
        $allQuestions = $assessment->questions()->get();
        
        // Get ALL available questions that can be assigned (regardless of category)
        $availableQuestions = \App\Models\Question::where('is_active', true)
            ->whereNotIn('id', $assessment->questions()->pluck('questions.id'))
            ->orderBy('difficulty_level')
            ->get();
        
        return view('admin.assessments.questions', [
            'assessment' => $assessment,
            'questions' => $questions,
            'allQuestions' => $allQuestions,
            'availableQuestions' => $availableQuestions
        ]);
    }

    /**
     * Show form to add a question to assessment
     */
    public function addQuestion(Assessment $assessment): View
    {
        $availableQuestions = \App\Models\Question::where('is_active', true)
            ->whereNotIn('id', $assessment->questions()->pluck('questions.id'))
            ->orderBy('category')
            ->orderBy('difficulty')
            ->paginate(20);
            
        return view('admin.assessments.add-question', compact('assessment', 'availableQuestions'));
    }

    /**
     * Store a new question for the assessment
     */
    public function storeQuestion(Request $request, Assessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
            'marks' => 'required|integer|min:1',
            'difficulty_level' => 'required|in:easy,medium,hard',
        ]);

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

            // Create the question
            $question = \App\Models\Question::create([
                'question' => $validated['question_text'],
                'question_type' => 'mcq',
                'option_a' => $validated['option_a'],
                'option_b' => $validated['option_b'],
                'option_c' => $validated['option_c'],
                'option_d' => $validated['option_d'],
                'correct_option' => array_search($validated['correct_answer'], ['A', 'B', 'C', 'D']),
                'options' => [$validated['option_a'], $validated['option_b'], $validated['option_c'], $validated['option_d']],
                'marks' => $validated['marks'],
                'difficulty_level' => $validated['difficulty_level'],
                'category_id' => $category->id,
                'category' => $assessment->category,
                'assessment_id' => $assessment->id,
                'is_active' => true,
                'created_by' => Auth::id() ?? 1,
            ]);

            // Attach to assessment
            $order = $assessment->questions()->count() + 1;
            $assessment->questions()->attach($question->id, ['order' => $order]);

            DB::commit();
            
            return redirect()->route('admin.assessments.questions', $assessment)
                ->with('status', 'Question added successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create question: ' . $e->getMessage()])
                ->withInput();
        }
    }


    /**
     * Show form to edit a question
     */
    public function editQuestion(Assessment $assessment, \App\Models\Question $question): View
    {
        // Verify question belongs to this assessment
        if (!$assessment->questions()->where('questions.id', $question->id)->exists()) {
            abort(404, 'Question not found in this assessment');
        }
        
        return view('admin.assessments.edit-question', compact('assessment', 'question'));
    }

    /**
     * Update a question
     */
    public function updateQuestion(Request $request, Assessment $assessment, \App\Models\Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
            'marks' => 'required|integer|min:1',
            'difficulty_level' => 'required|in:easy,medium,hard',
        ]);

        $question->update([
            'question' => $validated['question_text'],
            'option_a' => $validated['option_a'],
            'option_b' => $validated['option_b'],
            'option_c' => $validated['option_c'],
            'option_d' => $validated['option_d'],
            'correct_option' => array_search($validated['correct_answer'], ['A', 'B', 'C', 'D']),
            'options' => [$validated['option_a'], $validated['option_b'], $validated['option_c'], $validated['option_d']],
            'marks' => $validated['marks'],
            'difficulty_level' => $validated['difficulty_level'],
        ]);

        return redirect()->route('admin.assessments.questions', $assessment)
            ->with('status', 'Question updated successfully');
    }

    /**
     * Delete a question from the assessment
     */
    public function deleteQuestion(Assessment $assessment, \App\Models\Question $question): RedirectResponse
    {
        // Verify question belongs to this assessment
        if (!$assessment->questions()->where('questions.id', $question->id)->exists()) {
            return back()->withErrors(['error' => 'Question not found in this assessment']);
        }

        // Detach from assessment (don't delete the question itself)
        $assessment->questions()->detach($question->id);

        return back()->with('status', 'Question removed from assessment');
    }

    /**
     * Private method to trigger RAG knowledge sync
     */
    private function syncRagKnowledge() {
        try {
            $ragServiceUrl = env('RAG_SERVICE_URL', 'http://localhost:8001');
            Http::timeout(5)->post("{$ragServiceUrl}/sync-knowledge");
        } catch (\Exception $e) {
            Log::warning('RAG sync failed: ' . $e->getMessage());
        }
    }
}