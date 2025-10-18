<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assessment_id',
        'start_time',
        'end_time',
        'submit_time',
        'status',
        'total_marks',
        'obtained_marks',
        'percentage',
        'pass_status',
        'time_taken'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'submit_time' => 'datetime',
        'total_marks' => 'integer',
        'obtained_marks' => 'integer',
        'percentage' => 'decimal:2',
        'time_taken' => 'integer'
    ];

    /**
     * Get the student who took this assessment
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the assessment that was taken
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get all answers for this assessment attempt
     */
    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Check if the assessment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Calculate the time taken in a human-readable format
     */
    public function getFormattedTimeTakenAttribute(): string
    {
        $minutes = floor($this->time_taken / 60);
        $seconds = $this->time_taken % 60;
        
        if ($minutes > 0) {
            return "{$minutes} min {$seconds} sec";
        }
        
        return "{$seconds} sec";
    }

    /**
     * Get the result status as a badge class
     */
    public function getResultBadgeClassAttribute(): string
    {
        if ($this->pass_status === 'pass') {
            return 'bg-success';
        } elseif ($this->pass_status === 'fail') {
            return 'bg-danger';
        }
        
        return 'bg-secondary';
    }
}