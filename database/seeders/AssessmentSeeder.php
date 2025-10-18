<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Assessment;

class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample assessments if they don't exist
        $assessments = [
            [
                'title' => 'Aptitude Test - Basic Mathematics',
                'description' => 'Basic mathematics questions covering percentages, ratios, and simple interest.',
                'category' => 'Aptitude',
                'total_time' => 30,
                'is_active' => true,
            ],
            [
                'title' => 'Technical Test - Java Fundamentals',
                'description' => 'Fundamental Java programming concepts and syntax.',
                'category' => 'Technical',
                'total_time' => 45,
                'is_active' => true,
            ],
            [
                'title' => 'Aptitude Test - Logical Reasoning',
                'description' => 'Logical reasoning and analytical thinking questions.',
                'category' => 'Aptitude',
                'total_time' => 25,
                'is_active' => false,
            ],
            [
                'title' => 'Technical Test - Database Concepts',
                'description' => 'Database fundamentals including SQL and normalization.',
                'category' => 'Technical',
                'total_time' => 40,
                'is_active' => true,
            ],
        ];

        foreach ($assessments as $assessmentData) {
            // Check if assessment already exists
            $existing = Assessment::where('title', $assessmentData['title'])->first();
            
            if (!$existing) {
                Assessment::create($assessmentData);
            }
        }
    }
}
