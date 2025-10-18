<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SampleAssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Get admin user (or create one if not exists)
            $admin = User::where('role', 'admin')->first();
            if (!$admin) {
                $admin = User::create([
                    'name' => 'Admin',
                    'email' => 'admin@example.com',
                    'password' => bcrypt('password'),
                    'role' => 'admin',
                    'department' => 'Administration'
                ]);
            }
            
            // Create Technical Assessment
            $technicalAssessment = Assessment::create([
                'name' => 'Technical Assessment - Programming Fundamentals',
                'description' => 'This assessment tests your knowledge of programming concepts, data structures, and algorithms.',
                'category' => 'Technical',
                'time_limit' => 20, // 20 minutes
                'total_time' => 20,
                'duration' => 20,
                'is_active' => true,
                'allow_multiple_attempts' => false,
                'pass_percentage' => 50,
                'status' => 'active',
                'created_by' => $admin->id,
                'show_results_immediately' => true,
                'show_correct_answers' => true
            ]);
            
            // Technical Questions
            $techQuestions = [
                [
                    'question_text' => 'Which data structure uses LIFO (Last In First Out) principle?',
                    'option_a' => 'Queue',
                    'option_b' => 'Stack',
                    'option_c' => 'Array',
                    'option_d' => 'Linked List',
                    'correct_answer' => 'B',
                    'marks' => 5,
                    'time_limit' => 60,
                    'difficulty' => 'Easy',
                    'explanation' => 'Stack follows LIFO principle where the last element added is the first one to be removed.'
                ],
                [
                    'question_text' => 'What is the time complexity of binary search in a sorted array?',
                    'option_a' => 'O(n)',
                    'option_b' => 'O(n²)',
                    'option_c' => 'O(log n)',
                    'option_d' => 'O(1)',
                    'correct_answer' => 'C',
                    'marks' => 5,
                    'time_limit' => 60,
                    'difficulty' => 'Medium',
                    'explanation' => 'Binary search divides the search space in half at each step, resulting in O(log n) time complexity.'
                ],
                [
                    'question_text' => 'Which programming paradigm does JavaScript primarily support?',
                    'option_a' => 'Object-Oriented Programming only',
                    'option_b' => 'Functional Programming only',
                    'option_c' => 'Procedural Programming only',
                    'option_d' => 'Multi-paradigm (OOP, Functional, and Procedural)',
                    'correct_answer' => 'D',
                    'marks' => 5,
                    'time_limit' => 60,
                    'difficulty' => 'Medium',
                    'explanation' => 'JavaScript is a multi-paradigm language supporting object-oriented, functional, and procedural programming styles.'
                ],
                [
                    'question_text' => 'What does SQL stand for?',
                    'option_a' => 'Structured Query Language',
                    'option_b' => 'Simple Question Language',
                    'option_c' => 'Sequential Query Language',
                    'option_d' => 'System Query Language',
                    'correct_answer' => 'A',
                    'marks' => 5,
                    'time_limit' => 60,
                    'difficulty' => 'Easy',
                    'explanation' => 'SQL stands for Structured Query Language, used for managing relational databases.'
                ]
            ];
            
            // Get Technical category ID
            $technicalCategoryId = DB::table('categories')->where('name', 'Technical')->value('id');
            
            // Add technical questions to database and link to assessment
            foreach ($techQuestions as $index => $q) {
                // Convert letter to index (A=0, B=1, C=2, D=3)
                $correctIndex = ord($q['correct_answer']) - ord('A');
                
                $options = [
                    0 => $q['option_a'],
                    1 => $q['option_b'],
                    2 => $q['option_c'],
                    3 => $q['option_d']
                ];
                
                $question = Question::create([
                    'question' => $q['question_text'],
                    'category_id' => $technicalCategoryId,
                    'options' => json_encode($options),
                    'option_a' => $q['option_a'],
                    'option_b' => $q['option_b'],
                    'option_c' => $q['option_c'],
                    'option_d' => $q['option_d'],
                    'correct_answer' => $q['correct_answer'],
                    'correct_option' => $correctIndex,
                    'marks' => $q['marks'],
                    'time_per_question' => $q['time_limit'],
                    'difficulty_level' => strtolower($q['difficulty']),
                    'explanation' => $q['explanation'],
                    'is_active' => true,
                    'question_type' => 'mcq',
                    'order' => $index + 1
                ]);
                
                // Link question to assessment
                DB::table('assessment_questions')->insert([
                    'assessment_id' => $technicalAssessment->id,
                    'question_id' => $question->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            // Create Aptitude Assessment
            $aptitudeAssessment = Assessment::create([
                'name' => 'Aptitude Assessment - Logical Reasoning',
                'description' => 'This assessment evaluates your logical reasoning, analytical thinking, and problem-solving abilities.',
                'category' => 'Aptitude',
                'time_limit' => 15, // 15 minutes
                'total_time' => 15,
                'duration' => 15,
                'is_active' => true,
                'allow_multiple_attempts' => false,
                'pass_percentage' => 50,
                'status' => 'active',
                'created_by' => $admin->id,
                'show_results_immediately' => true,
                'show_correct_answers' => true
            ]);
            
            // Aptitude Questions
            $aptitudeQuestions = [
                [
                    'question_text' => 'If all roses are flowers and some flowers fade quickly, which statement must be true?',
                    'option_a' => 'All roses fade quickly',
                    'option_b' => 'Some roses fade quickly',
                    'option_c' => 'No roses fade quickly',
                    'option_d' => 'Cannot be determined from given information',
                    'correct_answer' => 'D',
                    'marks' => 5,
                    'time_limit' => 90,
                    'difficulty' => 'Medium',
                    'explanation' => 'We know roses are flowers, but we only know SOME flowers fade quickly, not which ones. So we cannot determine if roses fade quickly.'
                ],
                [
                    'question_text' => 'What is the next number in the sequence: 2, 6, 12, 20, 30, ?',
                    'option_a' => '40',
                    'option_b' => '42',
                    'option_c' => '44',
                    'option_d' => '36',
                    'correct_answer' => 'B',
                    'marks' => 5,
                    'time_limit' => 90,
                    'difficulty' => 'Medium',
                    'explanation' => 'The pattern is n×(n+1): 1×2=2, 2×3=6, 3×4=12, 4×5=20, 5×6=30, 6×7=42'
                ],
                [
                    'question_text' => 'If A is coded as 1, B as 2, C as 3, what is the code for FACE?',
                    'option_a' => '6134',
                    'option_b' => '6135',
                    'option_c' => '6133',
                    'option_d' => '6132',
                    'correct_answer' => 'B',
                    'marks' => 5,
                    'time_limit' => 60,
                    'difficulty' => 'Easy',
                    'explanation' => 'F=6, A=1, C=3, E=5. So FACE = 6135'
                ],
                [
                    'question_text' => 'A train traveling at 60 km/h crosses a platform in 30 seconds. If the platform is 200 meters long and the train is 100 meters long, what is the total distance covered?',
                    'option_a' => '300 meters',
                    'option_b' => '500 meters',
                    'option_c' => '200 meters',
                    'option_d' => '100 meters',
                    'correct_answer' => 'A',
                    'marks' => 5,
                    'time_limit' => 120,
                    'difficulty' => 'Hard',
                    'explanation' => 'To completely cross the platform, the train travels its own length (100m) plus the platform length (200m) = 300 meters total.'
                ]
            ];
            
            // Get Aptitude category ID
            $aptitudeCategoryId = DB::table('categories')->where('name', 'Aptitude')->value('id');
            
            // Add aptitude questions to database and link to assessment
            foreach ($aptitudeQuestions as $index => $q) {
                // Convert letter to index (A=0, B=1, C=2, D=3)
                $correctIndex = ord($q['correct_answer']) - ord('A');
                
                $options = [
                    0 => $q['option_a'],
                    1 => $q['option_b'],
                    2 => $q['option_c'],
                    3 => $q['option_d']
                ];
                
                $question = Question::create([
                    'question' => $q['question_text'],
                    'category_id' => $aptitudeCategoryId,
                    'options' => json_encode($options),
                    'option_a' => $q['option_a'],
                    'option_b' => $q['option_b'],
                    'option_c' => $q['option_c'],
                    'option_d' => $q['option_d'],
                    'correct_answer' => $q['correct_answer'],
                    'correct_option' => $correctIndex,
                    'marks' => $q['marks'],
                    'time_per_question' => $q['time_limit'],
                    'difficulty_level' => strtolower($q['difficulty']),
                    'explanation' => $q['explanation'],
                    'is_active' => true,
                    'question_type' => 'mcq',
                    'order' => $index + 1
                ]);
                
                // Link question to assessment
                DB::table('assessment_questions')->insert([
                    'assessment_id' => $aptitudeAssessment->id,
                    'question_id' => $question->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            DB::commit();
            
            echo "\n✅ Successfully created sample assessments!\n\n";
            echo "Created Assessments:\n";
            echo "1. Technical Assessment - Programming Fundamentals (ID: {$technicalAssessment->id})\n";
            echo "   - 4 questions on programming concepts\n";
            echo "   - Duration: 20 minutes\n";
            echo "   - Category: Technical\n\n";
            echo "2. Aptitude Assessment - Logical Reasoning (ID: {$aptitudeAssessment->id})\n";
            echo "   - 4 questions on logical reasoning\n";
            echo "   - Duration: 15 minutes\n";
            echo "   - Category: Aptitude\n\n";
            echo "Total Questions Created: 8\n";
            
        } catch (\Exception $e) {
            DB::rollBack();
            echo "\n❌ Error creating assessments: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine() . "\n";
            echo "Full error: " . $e->getTraceAsString() . "\n";
            throw $e;
        }
    }
}
