<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Assessment;
use App\Models\Question;

class TestQuestionView extends Command
{
    protected $signature = 'test:question-view';
    protected $description = 'Test the question view functionality';

    public function handle()
    {
        $this->info('Testing Question View Functionality...');
        
        try {
            // Get first assessment
            $assessment = Assessment::first();
            if (!$assessment) {
                $this->error('No assessments found');
                return 1;
            }
            
            $this->info("Testing with assessment: {$assessment->name} (Category: {$assessment->category})");
            
            // Test getting questions for assessment
            $questions = $assessment->questions()->orderBy('created_at', 'desc')->get();
            $this->info("Questions in assessment: {$questions->count()}");
            
            // Test getting available questions
            $categoryId = $assessment->category === 'Aptitude' ? 1 : 2;
            $availableQuestions = Question::whereDoesntHave('assessments', function($query) use ($assessment) {
                $query->where('assessment_id', $assessment->id);
            })->where('category_id', $categoryId)
              ->where('is_active', true)
              ->get();
              
            $this->info("Available questions for assignment: {$availableQuestions->count()}");
            
            // Test question properties
            if ($questions->count() > 0) {
                $firstQuestion = $questions->first();
                $this->info("First question details:");
                $this->info("  ID: {$firstQuestion->id}");
                $this->info("  Text: " . substr($firstQuestion->question_text, 0, 50) . '...');
                $this->info("  Category: {$firstQuestion->category}");
                $this->info("  Difficulty: {$firstQuestion->difficulty}");
                $this->info("  Options count: " . count($firstQuestion->options ?? []));
                $this->info("  Correct option: {$firstQuestion->correct_option_letter}");
            }
            
            if ($availableQuestions->count() > 0) {
                $availableQuestion = $availableQuestions->first();
                $this->info("\nFirst available question:");
                $this->info("  ID: {$availableQuestion->id}");
                $this->info("  Text: " . substr($availableQuestion->question_text, 0, 50) . '...');
                $this->info("  Category: {$availableQuestion->category}");
                $this->info("  Can be added: " . ($assessment->canAddQuestion($availableQuestion) ? 'Yes' : 'No'));
            }
            
            $this->info('\n✅ Question view test completed successfully!');
            
        } catch (\Exception $e) {
            $this->error("❌ Error during test:");
            $this->error($e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
