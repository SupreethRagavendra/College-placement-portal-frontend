<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class AdditionalAssessmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories if they don't exist
        $categories = [
            'Programming' => 'Tests on various programming languages',
            'Web Development' => 'HTML, CSS, JavaScript and frameworks',
            'Database' => 'SQL and database management',
            'Aptitude' => 'Logical reasoning and problem solving',
            'Technical' => 'Computer science fundamentals'
        ];
        
        foreach ($categories as $name => $description) {
            Category::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }
        
        // Create JavaScript Assessment
        $jsAssessment = Assessment::create([
            'title' => 'JavaScript Fundamentals',
            'name' => 'JavaScript Fundamentals',
            'description' => 'Test your knowledge of JavaScript basics, ES6 features, and DOM manipulation',
            'total_time' => 45,
            'duration' => 45,
            'total_questions' => 30,
            'pass_percentage' => 60,
            'is_active' => true,
            'allow_multiple_attempts' => true,
            'randomize_questions' => true,
            'show_results' => true,
            'category' => 'Web Development',
            'created_by' => 1,
            'status' => 'published'
        ]);
        
        // JavaScript Questions
        $jsQuestions = [
            [
                'question' => 'What is the correct way to declare a constant in JavaScript?',
                'option_a' => 'const x = 5;',
                'option_b' => 'let x = 5;',
                'option_c' => 'var x = 5;',
                'option_d' => 'constant x = 5;',
                'correct_answer' => 'A',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'Which method is used to add an element at the end of an array?',
                'option_a' => 'push()',
                'option_b' => 'pop()',
                'option_c' => 'shift()',
                'option_d' => 'unshift()',
                'correct_answer' => 'A',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'What does the "===" operator do in JavaScript?',
                'option_a' => 'Compares only values',
                'option_b' => 'Compares values and types',
                'option_c' => 'Assigns a value',
                'option_d' => 'Checks if not equal',
                'correct_answer' => 'B',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'Which of the following is NOT a JavaScript data type?',
                'option_a' => 'Number',
                'option_b' => 'String',
                'option_c' => 'Float',
                'option_d' => 'Boolean',
                'correct_answer' => 'C',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'How do you create a function in JavaScript?',
                'option_a' => 'function myFunction() {}',
                'option_b' => 'def myFunction() {}',
                'option_c' => 'func myFunction() {}',
                'option_d' => 'create myFunction() {}',
                'correct_answer' => 'A',
                'marks' => 1,
                'time_limit' => 60
            ]
        ];
        
        foreach ($jsQuestions as $q) {
            $question = Question::create(array_merge($q, [
                'type' => 'multiple_choice',
                'difficulty' => 'medium',
                'is_active' => true
            ]));
            $jsAssessment->questions()->attach($question->id);
        }
        
        // Create Python Assessment
        $pythonAssessment = Assessment::create([
            'title' => 'Python Programming Basics',
            'name' => 'Python Programming Basics',
            'description' => 'Test your knowledge of Python syntax, data structures, and basic concepts',
            'total_time' => 40,
            'duration' => 40,
            'total_questions' => 25,
            'pass_percentage' => 60,
            'is_active' => true,
            'allow_multiple_attempts' => true,
            'randomize_questions' => true,
            'show_results' => true,
            'category' => 'Programming',
            'created_by' => 1,
            'status' => 'published'
        ]);
        
        // Python Questions
        $pythonQuestions = [
            [
                'question' => 'Which of the following is used to define a block of code in Python?',
                'option_a' => 'Indentation',
                'option_b' => 'Curly braces',
                'option_c' => 'Parentheses',
                'option_d' => 'Square brackets',
                'correct_answer' => 'A',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'What is the correct file extension for Python files?',
                'option_a' => '.python',
                'option_b' => '.py',
                'option_c' => '.pyt',
                'option_d' => '.pt',
                'correct_answer' => 'B',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'Which keyword is used to create a function in Python?',
                'option_a' => 'function',
                'option_b' => 'def',
                'option_c' => 'fun',
                'option_d' => 'define',
                'correct_answer' => 'B',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'How do you insert comments in Python code?',
                'option_a' => '// This is a comment',
                'option_b' => '/* This is a comment */',
                'option_c' => '# This is a comment',
                'option_d' => '<!-- This is a comment -->',
                'correct_answer' => 'C',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'Which of the following is a mutable data type in Python?',
                'option_a' => 'String',
                'option_b' => 'Tuple',
                'option_c' => 'List',
                'option_d' => 'Integer',
                'correct_answer' => 'C',
                'marks' => 1,
                'time_limit' => 60
            ]
        ];
        
        foreach ($pythonQuestions as $q) {
            $question = Question::create(array_merge($q, [
                'type' => 'multiple_choice',
                'difficulty' => 'easy',
                'is_active' => true
            ]));
            $pythonAssessment->questions()->attach($question->id);
        }
        
        // Create SQL Database Assessment
        $sqlAssessment = Assessment::create([
            'title' => 'SQL and Database Management',
            'name' => 'SQL and Database Management',
            'description' => 'Test your knowledge of SQL queries, database design, and management',
            'total_time' => 40,
            'duration' => 40,
            'total_questions' => 25,
            'pass_percentage' => 60,
            'is_active' => true,
            'allow_multiple_attempts' => true,
            'randomize_questions' => false,
            'show_results' => true,
            'category' => 'Database',
            'created_by' => 1,
            'status' => 'published'
        ]);
        
        // SQL Questions
        $sqlQuestions = [
            [
                'question' => 'Which SQL statement is used to extract data from a database?',
                'option_a' => 'GET',
                'option_b' => 'SELECT',
                'option_c' => 'EXTRACT',
                'option_d' => 'PULL',
                'correct_answer' => 'B',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'Which SQL clause is used to filter records?',
                'option_a' => 'WHERE',
                'option_b' => 'HAVING',
                'option_c' => 'FILTER',
                'option_d' => 'SELECT',
                'correct_answer' => 'A',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'What does SQL stand for?',
                'option_a' => 'Structured Query Language',
                'option_b' => 'Simple Query Language',
                'option_c' => 'Structured Question Language',
                'option_d' => 'System Query Language',
                'correct_answer' => 'A',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'Which SQL statement is used to update data in a database?',
                'option_a' => 'MODIFY',
                'option_b' => 'UPDATE',
                'option_c' => 'SAVE',
                'option_d' => 'CHANGE',
                'correct_answer' => 'B',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'Which operator is used to search for a specified pattern in a column?',
                'option_a' => 'LIKE',
                'option_b' => 'BETWEEN',
                'option_c' => 'IN',
                'option_d' => 'SEARCH',
                'correct_answer' => 'A',
                'marks' => 1,
                'time_limit' => 60
            ]
        ];
        
        foreach ($sqlQuestions as $q) {
            $question = Question::create(array_merge($q, [
                'type' => 'multiple_choice',
                'difficulty' => 'medium',
                'is_active' => true
            ]));
            $sqlAssessment->questions()->attach($question->id);
        }
        
        // Create Data Structures Assessment
        $dsAssessment = Assessment::create([
            'title' => 'Data Structures and Algorithms',
            'name' => 'Data Structures and Algorithms',
            'description' => 'Test your knowledge of data structures, algorithms, and complexity analysis',
            'total_time' => 60,
            'duration' => 60,
            'total_questions' => 30,
            'pass_percentage' => 60,
            'is_active' => true,
            'allow_multiple_attempts' => true,
            'randomize_questions' => true,
            'show_results' => true,
            'category' => 'Technical',
            'created_by' => 1,
            'status' => 'published'
        ]);
        
        // Data Structures Questions
        $dsQuestions = [
            [
                'question' => 'What is the time complexity of accessing an element in an array by index?',
                'option_a' => 'O(1)',
                'option_b' => 'O(n)',
                'option_c' => 'O(log n)',
                'option_d' => 'O(n²)',
                'correct_answer' => 'A',
                'marks' => 1,
                'time_limit' => 90
            ],
            [
                'question' => 'Which data structure uses LIFO (Last In First Out) principle?',
                'option_a' => 'Queue',
                'option_b' => 'Stack',
                'option_c' => 'Array',
                'option_d' => 'Linked List',
                'correct_answer' => 'B',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'What is the worst-case time complexity of Quick Sort?',
                'option_a' => 'O(n)',
                'option_b' => 'O(n log n)',
                'option_c' => 'O(n²)',
                'option_d' => 'O(log n)',
                'correct_answer' => 'C',
                'marks' => 1,
                'time_limit' => 90
            ],
            [
                'question' => 'Which data structure is used in breadth-first search?',
                'option_a' => 'Stack',
                'option_b' => 'Queue',
                'option_c' => 'Tree',
                'option_d' => 'Graph',
                'correct_answer' => 'B',
                'marks' => 1,
                'time_limit' => 60
            ],
            [
                'question' => 'What is a binary search tree property?',
                'option_a' => 'Left child is greater than parent',
                'option_b' => 'Right child is less than parent',
                'option_c' => 'Left child is less than parent, right child is greater',
                'option_d' => 'All nodes have exactly two children',
                'correct_answer' => 'C',
                'marks' => 1,
                'time_limit' => 90
            ]
        ];
        
        foreach ($dsQuestions as $q) {
            $question = Question::create(array_merge($q, [
                'type' => 'multiple_choice',
                'difficulty' => 'hard',
                'is_active' => true
            ]));
            $dsAssessment->questions()->attach($question->id);
        }
        
        // Create Web Development Assessment
        $webAssessment = Assessment::create([
            'title' => 'Web Development (HTML/CSS)',
            'name' => 'Web Development (HTML/CSS)',
            'description' => 'Test your knowledge of HTML5, CSS3, and responsive web design',
            'total_time' => 30,
            'duration' => 30,
            'total_questions' => 20,
            'pass_percentage' => 60,
            'is_active' => true,
            'allow_multiple_attempts' => true,
            'randomize_questions' => false,
            'show_results' => true,
            'category' => 'Web Development',
            'created_by' => 1,
            'status' => 'published'
        ]);
        
        // Web Development Questions
        $webQuestions = [
            [
                'question' => 'What does HTML stand for?',
                'option_a' => 'Hyper Text Markup Language',
                'option_b' => 'High Tech Modern Language',
                'option_c' => 'Home Tool Markup Language',
                'option_d' => 'Hyperlinks and Text Markup Language',
                'correct_answer' => 'A',
                'marks' => 1,
                'time_limit' => 45
            ],
            [
                'question' => 'Which HTML tag is used to define an internal style sheet?',
                'option_a' => '<script>',
                'option_b' => '<style>',
                'option_c' => '<css>',
                'option_d' => '<link>',
                'correct_answer' => 'B',
                'marks' => 1,
                'time_limit' => 45
            ],
            [
                'question' => 'Which CSS property is used to change the text color?',
                'option_a' => 'text-color',
                'option_b' => 'color',
                'option_c' => 'font-color',
                'option_d' => 'text-style',
                'correct_answer' => 'B',
                'marks' => 1,
                'time_limit' => 45
            ],
            [
                'question' => 'How do you make text bold in HTML?',
                'option_a' => '<bold>',
                'option_b' => '<b>',
                'option_c' => '<strong>',
                'option_d' => 'Both B and C',
                'correct_answer' => 'D',
                'marks' => 1,
                'time_limit' => 45
            ],
            [
                'question' => 'Which property is used to change the background color in CSS?',
                'option_a' => 'bgcolor',
                'option_b' => 'background-color',
                'option_c' => 'color-background',
                'option_d' => 'bg-color',
                'correct_answer' => 'B',
                'marks' => 1,
                'time_limit' => 45
            ]
        ];
        
        foreach ($webQuestions as $q) {
            $question = Question::create(array_merge($q, [
                'type' => 'multiple_choice',
                'difficulty' => 'easy',
                'is_active' => true
            ]));
            $webAssessment->questions()->attach($question->id);
        }
        
        echo "✅ Additional assessments created successfully!\n";
        echo "📝 Created assessments:\n";
        echo "   - JavaScript Fundamentals (45 min)\n";
        echo "   - Python Programming Basics (40 min)\n";
        echo "   - SQL and Database Management (40 min)\n";
        echo "   - Data Structures and Algorithms (60 min)\n";
        echo "   - Web Development HTML/CSS (30 min)\n";
    }
}
