<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class StudentPerformanceAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assessment_id',
        'category',
        'topic',
        'difficulty_level',
        'total_questions',
        'correct_answers',
        'incorrect_answers',
        'accuracy_percentage',
        'avg_time_per_question',
        'questions_too_slow',
        'questions_too_fast',
        'questions_skipped',
        'common_mistakes',
        'weak_topics',
        'strong_topics',
        'improvement_rate',
        'consistency_score',
        'streak_correct',
        'max_streak',
        'learning_pace',
        'study_time_minutes',
        'last_activity_date'
    ];

    protected $casts = [
        'common_mistakes' => 'array',
        'weak_topics' => 'array',
        'strong_topics' => 'array',
        'accuracy_percentage' => 'decimal:2',
        'improvement_rate' => 'decimal:2',
        'consistency_score' => 'decimal:2',
        'learning_pace' => 'decimal:2',
        'last_activity_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the student that owns the analytics
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the assessment
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Update analytics from a new assessment attempt
     */
    public static function updateFromAttempt($studentId, $assessmentId, $studentAssessment, $answers)
    {
        $assessment = Assessment::find($assessmentId);
        if (!$assessment) return;

        $category = $assessment->category ?? 'General';
        
        // Group answers by difficulty
        $difficultyGroups = $answers->groupBy(function ($answer) {
            return $answer->question->difficulty ?? 'Medium';
        });

        foreach ($difficultyGroups as $difficulty => $difficultyAnswers) {
            $analytics = self::firstOrNew([
                'student_id' => $studentId,
                'assessment_id' => $assessmentId,
                'category' => $category,
                'difficulty_level' => $difficulty
            ]);

            // Update basic metrics
            $correctCount = $difficultyAnswers->where('is_correct', true)->count();
            $incorrectCount = $difficultyAnswers->where('is_correct', false)->count();
            $totalCount = $difficultyAnswers->count();

            $analytics->total_questions += $totalCount;
            $analytics->correct_answers += $correctCount;
            $analytics->incorrect_answers += $incorrectCount;
            
            // Calculate accuracy
            if ($analytics->total_questions > 0) {
                $analytics->accuracy_percentage = round(($analytics->correct_answers / $analytics->total_questions) * 100, 2);
            }

            // Time management metrics
            $avgTime = $difficultyAnswers->avg('time_spent');
            $analytics->avg_time_per_question = round($avgTime ?? 0);
            
            $tooSlow = $difficultyAnswers->where('time_spent', '>', $avgTime * 1.5)->count();
            $tooFast = $difficultyAnswers->where('time_spent', '<', $avgTime * 0.3)->count();
            $skipped = $difficultyAnswers->whereNull('student_answer')->count();
            
            $analytics->questions_too_slow += $tooSlow;
            $analytics->questions_too_fast += $tooFast;
            $analytics->questions_skipped += $skipped;

            // Track streaks
            $currentStreak = 0;
            foreach ($difficultyAnswers as $answer) {
                if ($answer->is_correct) {
                    $currentStreak++;
                    $analytics->max_streak = max($analytics->max_streak, $currentStreak);
                } else {
                    $currentStreak = 0;
                }
            }
            $analytics->streak_correct = $currentStreak;

            // Update weak/strong topics based on accuracy
            $topicPerformance = self::analyzeTopicPerformance($difficultyAnswers);
            $analytics->weak_topics = array_merge(
                $analytics->weak_topics ?? [],
                $topicPerformance['weak']
            );
            $analytics->strong_topics = array_merge(
                $analytics->strong_topics ?? [],
                $topicPerformance['strong']
            );

            // Calculate improvement rate
            $analytics->improvement_rate = self::calculateImprovementRate($studentId, $category, $difficulty);
            
            // Calculate consistency score
            $analytics->consistency_score = self::calculateConsistencyScore($studentId, $category, $difficulty);
            
            // Update study time
            $analytics->study_time_minutes += round($studentAssessment->time_taken / 60);
            $analytics->last_activity_date = now();

            $analytics->save();
        }
    }

    /**
     * Analyze topic performance from answers
     */
    private static function analyzeTopicPerformance($answers): array
    {
        $topics = [];
        
        foreach ($answers->groupBy('question.category') as $topic => $topicAnswers) {
            if (!$topic) continue;
            
            $correct = $topicAnswers->where('is_correct', true)->count();
            $total = $topicAnswers->count();
            $accuracy = $total > 0 ? ($correct / $total * 100) : 0;
            
            if ($accuracy < 50) {
                $topics['weak'][] = $topic;
            } elseif ($accuracy > 80) {
                $topics['strong'][] = $topic;
            }
        }
        
        return [
            'weak' => array_unique($topics['weak'] ?? []),
            'strong' => array_unique($topics['strong'] ?? [])
        ];
    }

    /**
     * Calculate improvement rate over time
     */
    private static function calculateImprovementRate($studentId, $category, $difficulty): float
    {
        $recentAnalytics = self::where('student_id', $studentId)
            ->where('category', $category)
            ->where('difficulty_level', $difficulty)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->pluck('accuracy_percentage');
        
        if ($recentAnalytics->count() < 2) {
            return 0;
        }
        
        // Calculate trend
        $firstHalf = $recentAnalytics->take(floor($recentAnalytics->count() / 2))->avg();
        $secondHalf = $recentAnalytics->skip(floor($recentAnalytics->count() / 2))->avg();
        
        return round($secondHalf - $firstHalf, 2);
    }

    /**
     * Calculate consistency score
     */
    private static function calculateConsistencyScore($studentId, $category, $difficulty): float
    {
        $recentScores = self::where('student_id', $studentId)
            ->where('category', $category)
            ->where('difficulty_level', $difficulty)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->pluck('accuracy_percentage');
        
        if ($recentScores->count() < 3) {
            return 0;
        }
        
        $mean = $recentScores->avg();
        $variance = $recentScores->map(function ($score) use ($mean) {
            return pow($score - $mean, 2);
        })->avg();
        
        $standardDeviation = sqrt($variance);
        
        // Lower standard deviation means more consistent
        // Convert to a 0-100 score where higher is better
        return round(max(0, 100 - ($standardDeviation * 2)), 2);
    }

    /**
     * Get weakness summary for a student
     */
    public static function getWeaknessSummary($studentId): array
    {
        $weaknesses = self::where('student_id', $studentId)
            ->where('accuracy_percentage', '<', 60)
            ->select('category', 'difficulty_level', 'weak_topics', 'accuracy_percentage')
            ->orderBy('accuracy_percentage')
            ->limit(5)
            ->get();
        
        $summary = [];
        foreach ($weaknesses as $weakness) {
            $summary[] = [
                'category' => $weakness->category,
                'difficulty' => $weakness->difficulty_level,
                'topics' => $weakness->weak_topics,
                'accuracy' => $weakness->accuracy_percentage
            ];
        }
        
        return $summary;
    }

    /**
     * Get strength summary for a student
     */
    public static function getStrengthSummary($studentId): array
    {
        $strengths = self::where('student_id', $studentId)
            ->where('accuracy_percentage', '>', 80)
            ->select('category', 'difficulty_level', 'strong_topics', 'accuracy_percentage')
            ->orderBy('accuracy_percentage', 'desc')
            ->limit(5)
            ->get();
        
        $summary = [];
        foreach ($strengths as $strength) {
            $summary[] = [
                'category' => $strength->category,
                'difficulty' => $strength->difficulty_level,
                'topics' => $strength->strong_topics,
                'accuracy' => $strength->accuracy_percentage
            ];
        }
        
        return $summary;
    }

    /**
     * Get time management insights
     */
    public static function getTimeManagementInsights($studentId): array
    {
        $analytics = self::where('student_id', $studentId)
            ->select(
                DB::raw('AVG(avg_time_per_question) as overall_avg_time'),
                DB::raw('SUM(questions_too_slow) as total_slow'),
                DB::raw('SUM(questions_too_fast) as total_fast'),
                DB::raw('SUM(questions_skipped) as total_skipped')
            )
            ->first();
        
        return [
            'avg_time_per_question' => round($analytics->overall_avg_time ?? 0),
            'questions_too_slow' => $analytics->total_slow ?? 0,
            'questions_too_fast' => $analytics->total_fast ?? 0,
            'questions_skipped' => $analytics->total_skipped ?? 0,
            'recommendation' => self::getTimeRecommendation($analytics)
        ];
    }

    /**
     * Get time management recommendation
     */
    private static function getTimeRecommendation($analytics): string
    {
        if (!$analytics) {
            return "Start taking assessments to get personalized recommendations.";
        }
        
        if ($analytics->total_slow > $analytics->total_fast * 2) {
            return "You tend to spend too much time on difficult questions. Consider moving on and returning to them later.";
        }
        
        if ($analytics->total_fast > $analytics->total_slow * 2) {
            return "You're answering questions very quickly. Take more time to read and understand each question.";
        }
        
        if ($analytics->total_skipped > 10) {
            return "You're skipping many questions. Try to attempt all questions as there's no negative marking.";
        }
        
        return "Your time management is good. Keep practicing to maintain consistency.";
    }
}
