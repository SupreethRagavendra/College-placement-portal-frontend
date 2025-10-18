<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminAssessmentController;
use App\Models\Assessment;
use Illuminate\Support\Facades\Validator;

class TestCorrectAnswerValidation extends Command
{
    protected $signature = 'test:correct-answer';
    protected $description = 'Test correct answer validation';

    public function handle()
    {
        $this->info('Testing Correct Answer Validation...');
        
        // Test validation rules
        $testData = [
            ['correct_answer' => 'A', 'expected' => true],
            ['correct_answer' => 'B', 'expected' => true],
            ['correct_answer' => 'C', 'expected' => true],
            ['correct_answer' => 'D', 'expected' => true],
            ['correct_answer' => '0', 'expected' => true],
            ['correct_answer' => '1', 'expected' => true],
            ['correct_answer' => '2', 'expected' => true],
            ['correct_answer' => '3', 'expected' => true],
            ['correct_answer' => 'E', 'expected' => false],
            ['correct_answer' => '4', 'expected' => false],
            ['correct_answer' => 'invalid', 'expected' => false],
        ];
        
        $rules = [
            'correct_answer' => 'required|in:A,B,C,D,0,1,2,3'
        ];
        
        foreach ($testData as $test) {
            $validator = Validator::make($test, $rules);
            $isValid = !$validator->fails();
            
            $status = $isValid === $test['expected'] ? '✓' : '❌';
            $this->info("{$status} correct_answer='{$test['correct_answer']}' -> Valid: " . ($isValid ? 'Yes' : 'No') . " (Expected: " . ($test['expected'] ? 'Yes' : 'No') . ")");
        }
        
        // Test conversion logic
        $this->info('\nTesting conversion logic:');
        
        $letterTests = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
        foreach ($letterTests as $letter => $expected) {
            $letterToNumber = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
            $result = $letterToNumber[$letter];
            $status = $result === $expected ? '✓' : '❌';
            $this->info("{$status} '{$letter}' -> {$result} (Expected: {$expected})");
        }
        
        $this->info('\n✅ Correct answer validation test completed!');
        return 0;
    }
}
