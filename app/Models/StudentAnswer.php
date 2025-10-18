<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_assessment_id',
        'question_id',
        'student_answer',
        'is_correct',
        'marks_obtained',
        'time_spent'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'marks_obtained' => 'integer',
        'time_spent' => 'integer'
    ];

    /**
     * Get the student assessment this answer belongs to
     */
    public function studentAssessment(): BelongsTo
    {
        return $this->belongsTo(StudentAssessment::class);
    }

    /**
     * Get the question this answer is for
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get formatted time spent
     */
    public function getFormattedTimeSpentAttribute(): string
    {
        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;
        
        if ($minutes > 0) {
            return "{$minutes} min {$seconds} sec";
        }
        
        return "{$seconds} sec";
    }
}