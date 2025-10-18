<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\User;

class AssessmentDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::first(); // Fallback to first user
        }
        
        // Create sample assessments
        $assessments = [
            [
                'name' => 'Java Programming Fundamentals',
                'title' => 'Java Programming Fundamentals',
                'description' => 'Test your knowledge of basic Java programming concepts including variables, data types, control structures, and object-oriented programming.',
                'total_time' => 45,
                'time_limit' => 45,
                'total_marks' => 50,
                'pass_percentage' => 60,
                'start_date' => now()->subDays(7),
                'end_date' => now()->addDays(30),
                'status' => 'active',
                'category' => 'Technical',
                'difficulty_level' => 'medium',
                'is_active' => true,
                'allow_multiple_attempts' => false,
                'show_results_immediately' => true,
                'show_correct_answers' => true,
                'created_by' => $admin->id ?? 1,
            ],
            [
                'name' => 'Quantitative Aptitude Test',
                'title' => 'Quantitative Aptitude Test',
                'description' => 'Assess your mathematical and logical reasoning skills with questions on percentages, ratios, time and work, and number series.',
                'total_time' => 30,
                'time_limit' => 30,
                'total_marks' => 40,
                'pass_percentage' => 50,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(25),
                'status' => 'active',
                'category' => 'Aptitude',
                'difficulty_level' => 'medium',
                'is_active' => true,
                'allow_multiple_attempts' => true,
                'show_results_immediately' => true,
                'show_correct_answers' => true,
                'created_by' => $admin->id ?? 1,
            ],
            [
                'name' => 'Python Programming Basics',
                'title' => 'Python Programming Basics',
                'description' => 'Introduction to Python programming covering syntax, data structures, functions, and basic libraries.',
                'total_time' => 40,
                'time_limit' => 40,
                'total_marks' => 45,
                'pass_percentage' => 55,
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(20),
                'status' => 'active',
                'category' => 'Technical',
                'difficulty_level' => 'easy',
                'is_active' => true,
                'allow_multiple_attempts' => false,
                'show_results_immediately' => true,
                'show_correct_answers' => false,
                'created_by' => $admin->id ?? 1,
            ],
            [
                'name' => 'Logical Reasoning & Puzzles',
                'title' => 'Logical Reasoning & Puzzles',
                'description' => 'Test your logical thinking and problem-solving abilities with various puzzles and reasoning questions.',
                'total_time' => 35,
                'time_limit' => 35,
                'total_marks' => 35,
                'pass_percentage' => 60,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(15),
                'status' => 'active',
                'category' => 'Aptitude',
                'difficulty_level' => 'hard',
                'is_active' => true,
                'allow_multiple_attempts' => false,
                'show_results_immediately' => true,
                'show_correct_answers' => true,
                'created_by' => $admin->id ?? 1,
            ],
            [
                'name' => 'Database Management Systems',
                'title' => 'Database Management Systems',
                'description' => 'Comprehensive test on DBMS concepts including SQL, normalization, transactions, and database design.',
                'total_time' => 50,
                'time_limit' => 50,
                'total_marks' => 60,
                'pass_percentage' => 65,
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(21),
                'status' => 'active',
                'category' => 'Technical',
                'difficulty_level' => 'medium',
                'is_active' => true,
                'allow_multiple_attempts' => true,
                'show_results_immediately' => false,
                'show_correct_answers' => false,
                'created_by' => $admin->id ?? 1,
            ],
            [
                'name' => 'Data Structures & Algorithms',
                'title' => 'Data Structures & Algorithms',
                'description' => 'Advanced assessment covering arrays, linked lists, trees, graphs, sorting, and searching algorithms.',
                'total_time' => 60,
                'time_limit' => 60,
                'total_marks' => 70,
                'pass_percentage' => 70,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'status' => 'active',
                'category' => 'Technical',
                'difficulty_level' => 'hard',
                'is_active' => true,
                'allow_multiple_attempts' => false,
                'show_results_immediately' => true,
                'show_correct_answers' => true,
                'created_by' => $admin->id ?? 1,
            ],
            [
                'name' => 'Communication Skills Test',
                'title' => 'Communication Skills Test',
                'description' => 'Evaluate your English language proficiency, grammar, vocabulary, and comprehension skills.',
                'total_time' => 25,
                'time_limit' => 25,
                'total_marks' => 30,
                'pass_percentage' => 50,
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(10),
                'status' => 'draft',
                'category' => 'Aptitude',
                'difficulty_level' => 'easy',
                'is_active' => false,
                'allow_multiple_attempts' => true,
                'show_results_immediately' => true,
                'show_correct_answers' => true,
                'created_by' => $admin->id ?? 1,
            ],
            [
                'name' => 'Web Development Fundamentals',
                'title' => 'Web Development Fundamentals',
                'description' => 'Test covering HTML, CSS, JavaScript, and basic web development concepts.',
                'total_time' => 45,
                'time_limit' => 45,
                'total_marks' => 50,
                'pass_percentage' => 60,
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(35),
                'status' => 'scheduled',
                'category' => 'Technical',
                'difficulty_level' => 'medium',
                'is_active' => false,
                'allow_multiple_attempts' => false,
                'show_results_immediately' => true,
                'show_correct_answers' => false,
                'created_by' => $admin->id ?? 1,
            ],
        ];
        
        foreach ($assessments as $assessmentData) {
            // Check if assessment already exists
            $existingAssessment = Assessment::where('name', $assessmentData['name'])->first();
            
            if (!$existingAssessment) {
                $assessment = Assessment::create($assessmentData);
                
                // Create sample questions for each assessment
                $this->createSampleQuestions($assessment);
                
                echo "Created assessment: {$assessment->name}\n";
            } else {
                // If assessment exists but has no questions, add them
                if ($existingAssessment->questions()->count() == 0) {
                    $this->createSampleQuestions($existingAssessment);
                    echo "Added questions to existing assessment: {$existingAssessment->name}\n";
                } else {
                    echo "Assessment already exists with questions: {$existingAssessment->name}\n";
                }
            }
        }
    }
    
    /**
     * Create sample questions for an assessment
     */
    private function createSampleQuestions(Assessment $assessment): void
    {
        $questions = [];
        
        // Java Programming Questions
        if (str_contains($assessment->name, 'Java')) {
            $questions = [
                [
                    'question' => 'Which of the following is not a valid access modifier in Java?',
                    'question_type' => 'mcq',
                    'options' => ['public', 'private', 'protected', 'internal'],
                    'correct_option' => 3,
                    'correct_answer' => 'D',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'question' => 'What is the size of int variable in Java?',
                    'question_type' => 'mcq',
                    'options' => ['16 bit', '32 bit', '64 bit', '8 bit'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'question' => 'Which keyword is used to inherit a class in Java?',
                    'question_type' => 'mcq',
                    'options' => ['implements', 'extends', 'inherits', 'super'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 60,
                    'order' => 3,
                    'is_active' => true,
                ],
                [
                    'question' => 'What is method overloading?',
                    'question_type' => 'mcq',
                    'options' => [
                        'Methods with same name but different parameters',
                        'Methods with same name and same parameters',
                        'Methods with different name but same parameters',
                        'None of the above'
                    ],
                    'correct_option' => 0,
                    'correct_answer' => 'A',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 4,
                    'is_active' => true,
                ],
                [
                    'question' => 'Which collection class allows you to access its elements by associating a key with an element value?',
                    'question_type' => 'mcq',
                    'options' => ['ArrayList', 'LinkedList', 'HashMap', 'TreeSet'],
                    'correct_option' => 2,
                    'correct_answer' => 'C',
                    'marks' => 4,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 5,
                    'is_active' => true,
                ],
            ];
        }
        
        // Python Programming Questions
        elseif (str_contains($assessment->name, 'Python')) {
            $questions = [
                [
                    'question' => 'Which of the following is used to define a function in Python?',
                    'question_type' => 'mcq',
                    'options' => ['function', 'def', 'fun', 'define'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'question' => 'What is the correct file extension for Python files?',
                    'question_type' => 'mcq',
                    'options' => ['.pyth', '.pt', '.py', '.python'],
                    'correct_option' => 2,
                    'correct_answer' => 'C',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'question' => 'Which method can be used to remove whitespace from the beginning and end of a string?',
                    'question_type' => 'mcq',
                    'options' => ['strip()', 'trim()', 'clean()', 'remove()'],
                    'correct_option' => 0,
                    'correct_answer' => 'A',
                    'marks' => 3,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 3,
                    'is_active' => true,
                ],
                [
                    'question' => 'How do you create a list in Python?',
                    'question_type' => 'mcq',
                    'options' => ['list = ()', 'list = []', 'list = {}', 'list = <>'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 4,
                    'is_active' => true,
                ],
                [
                    'question' => 'What is the output of print(2 ** 3)?',
                    'question_type' => 'mcq',
                    'options' => ['6', '8', '9', '5'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 5,
                    'is_active' => true,
                ],
            ];
        }
        
        // Database Management Questions
        elseif (str_contains($assessment->name, 'Database')) {
            $questions = [
                [
                    'question' => 'Which SQL statement is used to extract data from a database?',
                    'question_type' => 'mcq',
                    'options' => ['EXTRACT', 'SELECT', 'GET', 'PULL'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'question' => 'What does SQL stand for?',
                    'question_type' => 'mcq',
                    'options' => [
                        'Structured Query Language',
                        'Strong Question Language',
                        'Structured Question Language',
                        'Standard Query Language'
                    ],
                    'correct_option' => 0,
                    'correct_answer' => 'A',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'question' => 'Which of the following is not a type of database key?',
                    'question_type' => 'mcq',
                    'options' => ['Primary Key', 'Foreign Key', 'Candidate Key', 'Secondary Key'],
                    'correct_option' => 3,
                    'correct_answer' => 'D',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 3,
                    'is_active' => true,
                ],
                [
                    'question' => 'What is normalization in databases?',
                    'question_type' => 'mcq',
                    'options' => [
                        'Process of organizing data to minimize redundancy',
                        'Process of adding redundant data',
                        'Process of deleting data',
                        'Process of backing up data'
                    ],
                    'correct_option' => 0,
                    'correct_answer' => 'A',
                    'marks' => 4,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 4,
                    'is_active' => true,
                ],
                [
                    'question' => 'Which JOIN returns all rows from both tables?',
                    'question_type' => 'mcq',
                    'options' => ['INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'FULL OUTER JOIN'],
                    'correct_option' => 3,
                    'correct_answer' => 'D',
                    'marks' => 4,
                    'difficulty_level' => 'hard',
                    'time_per_question' => 120,
                    'order' => 5,
                    'is_active' => true,
                ],
            ];
        }
        
        // Data Structures & Algorithms Questions
        elseif (str_contains($assessment->name, 'Data Structures')) {
            $questions = [
                [
                    'question' => 'What is the time complexity of accessing an element in an array by index?',
                    'question_type' => 'mcq',
                    'options' => ['O(1)', 'O(n)', 'O(log n)', 'O(n²)'],
                    'correct_option' => 0,
                    'correct_answer' => 'A',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'question' => 'Which data structure uses LIFO principle?',
                    'question_type' => 'mcq',
                    'options' => ['Queue', 'Stack', 'Array', 'Linked List'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'question' => 'What is the worst-case time complexity of QuickSort?',
                    'question_type' => 'mcq',
                    'options' => ['O(n log n)', 'O(n)', 'O(n²)', 'O(log n)'],
                    'correct_option' => 2,
                    'correct_answer' => 'C',
                    'marks' => 4,
                    'difficulty_level' => 'hard',
                    'time_per_question' => 120,
                    'order' => 3,
                    'is_active' => true,
                ],
                [
                    'question' => 'Which traversal of a binary search tree gives sorted order?',
                    'question_type' => 'mcq',
                    'options' => ['Preorder', 'Inorder', 'Postorder', 'Level order'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 4,
                    'is_active' => true,
                ],
                [
                    'question' => 'What is the space complexity of merge sort?',
                    'question_type' => 'mcq',
                    'options' => ['O(1)', 'O(log n)', 'O(n)', 'O(n²)'],
                    'correct_option' => 2,
                    'correct_answer' => 'C',
                    'marks' => 4,
                    'difficulty_level' => 'hard',
                    'time_per_question' => 120,
                    'order' => 5,
                    'is_active' => true,
                ],
            ];
        }
        
        // Web Development Questions
        elseif (str_contains($assessment->name, 'Web Development')) {
            $questions = [
                [
                    'question' => 'What does HTML stand for?',
                    'question_type' => 'mcq',
                    'options' => [
                        'Hyper Text Markup Language',
                        'Home Tool Markup Language',
                        'Hyperlinks and Text Markup Language',
                        'Hyper Tool Markup Language'
                    ],
                    'correct_option' => 0,
                    'correct_answer' => 'A',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'question' => 'Which CSS property is used to change the text color?',
                    'question_type' => 'mcq',
                    'options' => ['text-color', 'color', 'font-color', 'text-style'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'question' => 'Which JavaScript method is used to access an HTML element by id?',
                    'question_type' => 'mcq',
                    'options' => [
                        'getElementById()',
                        'getElement(id)',
                        'getElementByID()',
                        'elementById()'
                    ],
                    'correct_option' => 0,
                    'correct_answer' => 'A',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 3,
                    'is_active' => true,
                ],
                [
                    'question' => 'What is the correct HTML tag for inserting a line break?',
                    'question_type' => 'mcq',
                    'options' => ['<break>', '<br>', '<lb>', '<line>'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 4,
                    'is_active' => true,
                ],
                [
                    'question' => 'Which of the following is not a valid HTTP method?',
                    'question_type' => 'mcq',
                    'options' => ['GET', 'POST', 'SEND', 'DELETE'],
                    'correct_option' => 2,
                    'correct_answer' => 'C',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 5,
                    'is_active' => true,
                ],
            ];
        }
        
        // Quantitative Aptitude Questions
        elseif (str_contains($assessment->name, 'Quantitative')) {
            $questions = [
                [
                    'question' => 'If 20% of a number is 120, what is the number?',
                    'question_type' => 'mcq',
                    'options' => ['400', '500', '600', '700'],
                    'correct_option' => 2,
                    'correct_answer' => 'C',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'question' => 'What is the next number in the series: 2, 6, 12, 20, 30, ?',
                    'question_type' => 'mcq',
                    'options' => ['40', '42', '44', '46'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'question' => 'A train travels 60 km in 45 minutes. What is its speed in km/hr?',
                    'question_type' => 'mcq',
                    'options' => ['70', '75', '80', '85'],
                    'correct_option' => 2,
                    'correct_answer' => 'C',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 3,
                    'is_active' => true,
                ],
                [
                    'question' => 'If A:B = 2:3 and B:C = 4:5, then A:B:C is?',
                    'question_type' => 'mcq',
                    'options' => ['8:12:15', '2:3:5', '4:6:5', '8:12:20'],
                    'correct_option' => 0,
                    'correct_answer' => 'A',
                    'marks' => 4,
                    'difficulty_level' => 'hard',
                    'time_per_question' => 120,
                    'order' => 4,
                    'is_active' => true,
                ],
                [
                    'question' => 'The sum of ages of father and son is 56 years. After 4 years, father will be 3 times as old as his son. What is the son\'s present age?',
                    'question_type' => 'mcq',
                    'options' => ['10', '12', '14', '16'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 4,
                    'difficulty_level' => 'hard',
                    'time_per_question' => 120,
                    'order' => 5,
                    'is_active' => true,
                ],
            ];
        }
        
        // Logical Reasoning Questions
        elseif (str_contains($assessment->name, 'Logical')) {
            $questions = [
                [
                    'question' => 'If COMPUTER is coded as RFUVQNPC, how is MEDICINE coded?',
                    'question_type' => 'mcq',
                    'options' => ['MFEDJJOE', 'ECIIDNEM', 'MFEJDJOF', 'EDJDJOFM'],
                    'correct_option' => 2,
                    'correct_answer' => 'C',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'question' => 'Pointing to a man, a woman said, "His mother is the only daughter of my mother." How is the woman related to the man?',
                    'question_type' => 'mcq',
                    'options' => ['Mother', 'Daughter', 'Sister', 'Grandmother'],
                    'correct_option' => 0,
                    'correct_answer' => 'A',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'question' => 'Find the odd one out: 3, 5, 11, 14, 17, 21',
                    'question_type' => 'mcq',
                    'options' => ['3', '14', '17', '21'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 3,
                    'is_active' => true,
                ],
                [
                    'question' => 'If ROSE is coded as 6821, CHAIR is coded as 73456, then what is the code for SEARCH?',
                    'question_type' => 'mcq',
                    'options' => ['214673', '214763', '124673', '214637'],
                    'correct_option' => 0,
                    'correct_answer' => 'A',
                    'marks' => 4,
                    'difficulty_level' => 'hard',
                    'time_per_question' => 120,
                    'order' => 4,
                    'is_active' => true,
                ],
                [
                    'question' => 'Complete the series: 2, 5, 10, 17, 26, ?',
                    'question_type' => 'mcq',
                    'options' => ['35', '37', '39', '41'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 5,
                    'is_active' => true,
                ],
            ];
        }
        
        // Communication Skills Questions
        elseif (str_contains($assessment->name, 'Communication')) {
            $questions = [
                [
                    'question' => 'Choose the correct form: "Neither of the students ___ submitted their assignment."',
                    'question_type' => 'mcq',
                    'options' => ['has', 'have', 'had', 'having'],
                    'correct_option' => 0,
                    'correct_answer' => 'A',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'question' => 'What is the synonym of "Ubiquitous"?',
                    'question_type' => 'mcq',
                    'options' => ['Rare', 'Omnipresent', 'Scarce', 'Limited'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 2,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 60,
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'question' => 'Choose the correct preposition: "He is good ___ mathematics."',
                    'question_type' => 'mcq',
                    'options' => ['in', 'at', 'on', 'for'],
                    'correct_option' => 1,
                    'correct_answer' => 'B',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 3,
                    'is_active' => true,
                ],
                [
                    'question' => 'Identify the antonym of "Benevolent"',
                    'question_type' => 'mcq',
                    'options' => ['Kind', 'Generous', 'Malevolent', 'Charitable'],
                    'correct_option' => 2,
                    'correct_answer' => 'C',
                    'marks' => 3,
                    'difficulty_level' => 'medium',
                    'time_per_question' => 90,
                    'order' => 4,
                    'is_active' => true,
                ],
                [
                    'question' => 'Which of the following sentences is grammatically correct?',
                    'question_type' => 'mcq',
                    'options' => [
                        'She don\'t like coffee',
                        'She doesn\'t likes coffee',
                        'She doesn\'t like coffee',
                        'She don\'t likes coffee'
                    ],
                    'correct_option' => 2,
                    'correct_answer' => 'C',
                    'marks' => 2,
                    'difficulty_level' => 'easy',
                    'time_per_question' => 60,
                    'order' => 5,
                    'is_active' => true,
                ],
            ];
        }
        
        // Create questions and attach them to the assessment
        foreach ($questions as $questionData) {
            // Encode options array to JSON
            $questionData['options'] = json_encode($questionData['options']);
            
            // Get or create category_id based on assessment category
            $categoryId = \DB::table('categories')->where('name', $assessment->category)->value('id');
            if (!$categoryId) {
                // If category doesn't exist, create it
                $categoryId = \DB::table('categories')->insertGetId([
                    'name' => $assessment->category,
                    'description' => $assessment->category . ' category',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $questionData['category_id'] = $categoryId;
            
            // Remove category field if present
            unset($questionData['category']);
            
            // Remove difficulty field as database doesn't have it
            unset($questionData['difficulty']);
            
            // Create the question
            $question = Question::create($questionData);
            
            // Attach question to assessment (many-to-many relationship)
            $assessment->questions()->attach($question->id);
        }
        
        echo "  - Added " . count($questions) . " questions to assessment: {$assessment->name}\n";
    }
}