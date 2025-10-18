<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions for an assessment
     */
    public function index(Assessment $assessment, Request $request): View
    {
        $query = $assessment->questions()->orderBy('order');
        
        // Apply filters
        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }
        
        if ($request->filled('search')) {
            $query->where('question_text', 'like', '%' . $request->search . '%');
        }
        
        $questions = $query->paginate(10);
        
        return view('admin.questions.index', compact('assessment', 'questions'));
    }

    /**
     * Show the form for creating a new question
     */
    public function create(Assessment $assessment): View
    {
        return view('admin.questions.create', compact('assessment'));
    }

    /**
     * Store a newly created question
     */
    public function store(Request $request, Assessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false,short_answer',
            'correct_answer' => 'required|string',
            'marks' => 'required|integer|min:1',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'order' => 'required|integer|min:0',
        ]);
        
        // Validate options for MCQ questions
        if ($validated['question_type'] === 'mcq') {
            $validated['options'] = $request->validate([
                'options' => 'required|array|min:2',
                'options.*' => 'required|string',
            ])['options'];
        }
        
        $validated['assessment_id'] = $assessment->id;
        
        Question::create($validated);
        
        // Trigger RAG sync
        $this->syncRagKnowledge();
        
        return redirect()->route('admin.assessments.questions', $assessment)
            ->with('status', 'Question added successfully');
    }

    /**
     * Show the form for editing the specified question
     */
    public function edit(Assessment $assessment, Question $question): View
    {
        return view('admin.questions.edit', compact('assessment', 'question'));
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, Assessment $assessment, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,true_false,short_answer',
            'correct_answer' => 'required|string',
            'marks' => 'required|integer|min:1',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'order' => 'required|integer|min:0',
        ]);
        
        // Validate options for MCQ questions
        if ($validated['question_type'] === 'mcq') {
            $validated['options'] = $request->validate([
                'options' => 'required|array|min:2',
                'options.*' => 'required|string',
            ])['options'];
        }
        
        $question->update($validated);
        
        // Trigger RAG sync
        $this->syncRagKnowledge();
        
        return redirect()->route('admin.assessments.questions', $assessment)
            ->with('status', 'Question updated successfully');
    }

    /**
     * Remove the specified question
     */
    public function destroy(Assessment $assessment, Question $question): RedirectResponse
    {
        $question->delete();
        
        // Trigger RAG sync
        $this->syncRagKnowledge();
        
        return redirect()->route('admin.assessments.questions', $assessment)
            ->with('status', 'Question deleted successfully');
    }

    /**
     * Import questions from CSV
     */
    public function import(Request $request, Assessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);
        
        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($csvData); // Remove header row
        
        $imported = 0;
        $errors = [];
        
        DB::beginTransaction();
        try {
            foreach ($csvData as $index => $row) {
                // Expected CSV format:
                // question_text, question_type, option_a, option_b, option_c, option_d, correct_answer, marks, difficulty_level, order
                if (count($row) < 10) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid format";
                    continue;
                }
                
                try {
                    $options = null;
                    if ($row[1] === 'mcq') {
                        $options = [
                            $row[2], // option_a
                            $row[3], // option_b
                            $row[4], // option_c
                            $row[5], // option_d
                        ];
                    }
                    
                    Question::create([
                        'assessment_id' => $assessment->id,
                        'question_text' => $row[0],
                        'question_type' => $row[1],
                        'options' => $options,
                        'correct_answer' => $row[6],
                        'marks' => (int)$row[7],
                        'difficulty_level' => $row[8],
                        'order' => (int)$row[9],
                    ]);
                    
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            // Trigger RAG sync after importing questions
            $this->syncRagKnowledge();
            
            $message = "Imported {$imported} questions successfully.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', array_slice($errors, 0, 5));
            }
            
            return redirect()->route('admin.assessments.questions', $assessment)
                ->with('status', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to import questions: ' . $e->getMessage()]);
        }
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