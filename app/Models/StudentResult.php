<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class StudentResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assessment_id',
        'score',
        'total_questions',
        'time_taken',
        'answers',
        'submitted_at'
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'integer',
        'total_questions' => 'integer',
        'time_taken' => 'integer',
        'submitted_at' => 'datetime'
    ];

    /**
     * The student who took the test
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * The assessment that was taken
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get score as percentage (cached)
     */
    public function getScorePercentageAttribute(): float
    {
        return Cache::remember("result_score_percentage_{$this->id}", 300, function() {
            if ($this->total_questions == 0) return 0;
            return round(($this->score / $this->total_questions) * 100, 2);
        });
    }

    /**
     * Get formatted time taken
     */
    public function getFormattedTimeAttribute(): string
    {
        $minutes = floor($this->time_taken / 60);
        $seconds = $this->time_taken % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Get time taken in minutes and seconds
     */
    public function getTimeInMinutesAttribute(): string
    {
        $minutes = floor($this->time_taken / 60);
        $seconds = $this->time_taken % 60;
        
        if ($minutes > 0) {
            return $seconds > 0 ? "{$minutes}m {$seconds}s" : "{$minutes}m";
        }
        
        return "{$seconds}s";
    }

    /**
     * Get grade based on percentage (cached)
     */
    public function getGradeAttribute(): string
    {
        return Cache::remember("result_grade_{$this->id}", 300, function() {
            $percentage = $this->score_percentage;
            
            if ($percentage >= 90) return 'A+';
            if ($percentage >= 80) return 'A';
            if ($percentage >= 70) return 'B+';
            if ($percentage >= 60) return 'B';
            if ($percentage >= 50) return 'C';
            if ($percentage >= 40) return 'D';
            
            return 'F';
        });
    }

    /**
     * Check if the result is a pass (cached)
     */
    public function getIsPassAttribute(): bool
    {
        return Cache::remember("result_is_pass_{$this->id}", 300, function() {
            return $this->score_percentage >= 50; // 50% is pass
        });
    }

    /**
     * Scope for specific student
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope for specific assessment
     */
    public function scopeForAssessment($query, $assessmentId)
    {
        return $query->where('assessment_id', $assessmentId);
    }

    /**
     * Scope for passed results
     */
    public function scopePassed($query)
    {
        return $query->whereRaw('(score / total_questions) * 100 >= 50');
    }

    /**
     * Scope for failed results
     */
    public function scopeFailed($query)
    {
        return $query->whereRaw('(score / total_questions) * 100 < 50');
    }
}