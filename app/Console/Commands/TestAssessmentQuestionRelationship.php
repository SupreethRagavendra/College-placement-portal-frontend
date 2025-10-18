<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Assessment;
use App\Models\Question;

class TestAssessmentQuestionRelationship extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:assessment-questions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test assessment-question relationship and validation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Assessment-Question Relationship...');
        
        try {
            // Test 1: Get assessments with question counts
            $assessments = Assessment::withCount('questions')->get();
            $this->info("Total assessments: {$assessments->count()}");
            
            foreach ($assessments as $assessment) {
                $questionsCount = $assessment->questions_count;
                $activeQuestionsCount = $assessment->active_questions_count;
                $isReady = $assessment->isReadyForStudents();
                
                $this->info("  {$assessment->name}: {$questionsCount} questions ({$activeQuestionsCount} active) - Ready: " . ($isReady ? 'Yes' : 'No'));
            }
            
            // Test 2: Test question assignment validation
            $assessment = Assessment::first();
            if ($assessment) {
                $this->info("\nTesting with assessment: {$assessment->name} (Category: {$assessment->category})");
                
                // Get a question from the same category
                $assessmentCategoryId = $assessment->category === 'Aptitude' ? 1 : 2;
                $sameCategory = Question::where('category_id', $assessmentCategoryId)
                    ->where('is_active', true)
                    ->whereDoesntHave('assessments', function($query) use ($assessment) {
                        $query->where('assessment_id', $assessment->id);
                    })
                    ->first();
                
                if ($sameCategory) {
                    $canAdd = $assessment->canAddQuestion($sameCategory);
                    $this->info("✓ Can add same-category question: " . ($canAdd ? 'Yes' : 'No'));
                } else {
                    $this->info("No available same-category questions found");
                }
                
                // Get a question from different category
                $differentCategory = Question::where('category_id', '!=', $assessmentCategoryId)
                    ->where('is_active', true)
                    ->first();
                
                if ($differentCategory) {
                    $canAdd = $assessment->canAddQuestion($differentCategory);
                    $this->info("✓ Can add different-category question: " . ($canAdd ? 'Yes' : 'No') . " (Expected: No)");
                } else {
                    $this->info("No different-category questions found");
                }
            }
            
            // Test 3: Test scopes
            $withQuestions = Assessment::withQuestions()->count();
            $withoutQuestions = Assessment::withoutQuestions()->count();
            $active = Assessment::active()->count();
            
            $this->info("\nScope tests:");
            $this->info("  Assessments with questions: {$withQuestions}");
            $this->info("  Assessments without questions: {$withoutQuestions}");
            $this->info("  Active assessments: {$active}");
            
            // Test 4: Check ready assessments
            $readyAssessments = Assessment::active()->get()->filter(function($assessment) {
                return $assessment->isReadyForStudents();
            });
            
            $this->info("  Ready for students: {$readyAssessments->count()}");
            
            foreach ($readyAssessments as $ready) {
                $this->info("    - {$ready->name} ({$ready->active_questions_count} active questions)");
            }
            
            $this->info('\n✅ Assessment-Question relationship test completed successfully!');
            
        } catch (\Exception $e) {
            $this->error("❌ Error during test:");
            $this->error($e->getMessage());
            $this->error("Stack trace:");
            $this->error($e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}
