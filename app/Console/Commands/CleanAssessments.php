<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Assessment;
use App\Models\Question;

class CleanAssessments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assessments:clean 
                            {--force : Skip confirmation prompt}
                            {--with-questions : Also delete all questions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all assessments and related data from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== ASSESSMENT CLEANUP UTILITY ===');
        $this->newLine();
        
        // Get current counts
        $assessmentCount = Assessment::withTrashed()->count();
        $studentAssessmentCount = DB::table('student_assessments')->count();
        $studentAnswerCount = DB::table('student_answers')->count();
        $studentResultCount = DB::table('student_results')->count();
        $assessmentQuestionCount = DB::table('assessment_questions')->count();
        $questionCount = Question::count();
        
        $this->info('Current database statistics:');
        $this->table(
            ['Table', 'Count'],
            [
                ['Assessments', $assessmentCount],
                ['Student Assessments', $studentAssessmentCount],
                ['Student Answers', $studentAnswerCount],
                ['Student Results', $studentResultCount],
                ['Assessment-Question Links', $assessmentQuestionCount],
                ['Questions', $questionCount],
            ]
        );
        
        if ($assessmentCount == 0) {
            $this->info('No assessments found in database. Nothing to delete.');
            return Command::SUCCESS;
        }
        
        // Confirm deletion unless --force is used
        if (!$this->option('force')) {
            $this->warn('⚠️  This will permanently delete ALL assessments and related data!');
            
            if ($this->option('with-questions')) {
                $this->error('⚠️  WARNING: --with-questions flag is set. This will also delete ALL questions!');
            }
            
            if (!$this->confirm('Are you sure you want to proceed?')) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }
        
        $this->info('Starting cleanup process...');
        $this->newLine();
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            $progressBar = $this->output->createProgressBar(4);
            $progressBar->start();
            
            // Step 1: Delete all assessments (including soft deleted)
            $this->info(' Deleting assessments...');
            $deletedAssessments = Assessment::withTrashed()->forceDelete();
            $progressBar->advance();
            
            // Step 2: Clean up any orphaned student answers
            $this->info(' Cleaning orphaned student answers...');
            $deletedAnswers = DB::table('student_answers')->whereNotIn('student_assessment_id', function($query) {
                $query->select('id')->from('student_assessments');
            })->delete();
            $progressBar->advance();
            
            // Step 3: Clean up any orphaned student assessments
            $this->info(' Cleaning orphaned student assessments...');
            $deletedStudentAssessments = DB::table('student_assessments')->whereNotIn('assessment_id', function($query) {
                $query->select('id')->from('assessments');
            })->delete();
            $progressBar->advance();
            
            // Step 4: Clean up any orphaned results and links
            $this->info(' Cleaning other orphaned records...');
            DB::table('student_results')->whereNotIn('assessment_id', function($query) {
                $query->select('id')->from('assessments');
            })->delete();
            
            DB::table('assessment_questions')->whereNotIn('assessment_id', function($query) {
                $query->select('id')->from('assessments');
            })->delete();
            $progressBar->advance();
            
            // Optional: Delete all questions if flag is set
            $deletedQuestions = 0;
            if ($this->option('with-questions')) {
                $this->newLine();
                $this->info(' Deleting all questions...');
                $deletedQuestions = Question::query()->delete();
            }
            
            $progressBar->finish();
            $this->newLine(2);
            
            // Commit transaction
            DB::commit();
            
            $this->success('✓ Cleanup completed successfully!');
            $this->newLine();
            
            // Show deletion summary
            $this->info('Deletion Summary:');
            $this->table(
                ['Item', 'Deleted Count'],
                [
                    ['Assessments', $deletedAssessments],
                    ['Student Assessments', $deletedStudentAssessments],
                    ['Student Answers', $deletedAnswers],
                    ['Questions', $deletedQuestions],
                ]
            );
            
            // Show final counts
            $this->newLine();
            $this->info('Final database statistics:');
            $this->table(
                ['Table', 'Count'],
                [
                    ['Assessments', Assessment::withTrashed()->count()],
                    ['Student Assessments', DB::table('student_assessments')->count()],
                    ['Student Answers', DB::table('student_answers')->count()],
                    ['Student Results', DB::table('student_results')->count()],
                    ['Assessment-Question Links', DB::table('assessment_questions')->count()],
                    ['Questions', Question::count()],
                ]
            );
            
            if (!$this->option('with-questions') && Question::count() > 0) {
                $this->newLine();
                $this->warn('Note: Questions were preserved. Use --with-questions flag to also delete questions.');
            }
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->newLine(2);
            $this->error('✗ Error occurred during cleanup: ' . $e->getMessage());
            $this->error('Transaction rolled back. No data was deleted.');
            
            if ($this->option('verbose')) {
                $this->error('Stack trace:');
                $this->error($e->getTraceAsString());
            }
            
            return Command::FAILURE;
        }
    }
    
    /**
     * Output success message with green color
     */
    private function success($message)
    {
        $this->line("<fg=green>$message</>");
    }
}
