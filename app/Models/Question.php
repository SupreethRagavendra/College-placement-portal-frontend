<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'assessment_id',
        'category_id',
        'question',
        'question_text',
        'question_type',
        'options',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'correct_answer',
        'marks',
        'difficulty',
        'difficulty_level',
        'time_per_question',
        'order',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'marks' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Get options - consolidates individual option columns into array
     */
    public function getOptionsAttribute($value)
    {
        // If options JSON field exists and is not empty, use it
        if (!empty($value)) {
            // Handle if already decoded by cast
            if (is_array($value)) {
                return $value; // Don't filter to preserve indices
            }
            // Decode JSON
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded; // Don't filter to preserve indices
            }
        }
        
        // Otherwise, build array from individual option columns
        // IMPORTANT: Use explicit indices to ensure correct letter mapping
        $options = [];
        
        // Always use index 0 for option A, 1 for B, 2 for C, 3 for D
        if (!empty($this->attributes['option_a'])) {
            $options[0] = $this->attributes['option_a'];
        }
        if (!empty($this->attributes['option_b'])) {
            $options[1] = $this->attributes['option_b'];
        }
        if (!empty($this->attributes['option_c'])) {
            $options[2] = $this->attributes['option_c'];
        }
        if (!empty($this->attributes['option_d'])) {
            $options[3] = $this->attributes['option_d'];
        }
        
        return $options;
    }

    /**
     * Get the assessment this question belongs to
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get all assessments this question belongs to (many-to-many)
     */
    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(Assessment::class, 'assessment_questions');
    }

    /**
     * Get all student answers for this question
     */
    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Get the category attribute - handles JSON encoded categories
     */
    public function getCategoryAttribute($value)
    {
        // If it's a JSON string, decode it and extract the name
        if (is_string($value) && (str_starts_with($value, '{') || str_starts_with($value, '['))) {
            $decoded = json_decode($value, true);
            if (is_array($decoded) && isset($decoded['name'])) {
                return $decoded['name'];
            } elseif (is_array($decoded) && isset($decoded[0]['name'])) {
                return $decoded[0]['name'];
            }
        }
        
        // Return as-is if it's already a plain string
        return $value ?: 'Uncategorized';
    }

    /**
     * Get the question attribute (alias for question_text)
     * Maps 'question' to database column 'question_text'
     */
    public function getQuestionAttribute()
    {
        return $this->attributes['question_text'] ?? '';
    }

    /**
     * Set the question attribute (fills both question and question_text columns)
     */
    public function setQuestionAttribute($value)
    {
        // Set both columns to ensure compatibility
        $this->attributes['question'] = $value;
        $this->attributes['question_text'] = $value;
    }

    /**
     * Check if student answer is correct
     */
    public function isCorrectAnswer($studentAnswer): bool
    {
        // Get the correct answer from database field (could be letter A,B,C,D)
        $correctAnswer = $this->attributes['correct_answer'] ?? null;
        
        // If no correct answer is set, try to derive from correct_option (numeric index)
        if (empty($correctAnswer) && isset($this->attributes['correct_option'])) {
            $letters = ['A', 'B', 'C', 'D'];
            $correctOption = $this->attributes['correct_option'];
            $correctAnswer = $letters[$correctOption] ?? null;
        }
        
        if (empty($correctAnswer)) {
            return false;
        }
        
        // Normalize both answers to uppercase letters
        $studentAnswerNormalized = strtoupper(trim($studentAnswer));
        $correctAnswerNormalized = strtoupper(trim($correctAnswer));
        
        // For MCQ questions, compare letters (A, B, C, D)
        if ($this->question_type === 'mcq' || $this->question_type === 'true_false') {
            return $studentAnswerNormalized === $correctAnswerNormalized;
        }
        
        // For short answer questions, case-insensitive comparison
        return strtolower(trim($studentAnswer)) === strtolower(trim($correctAnswer));
    }

    /**
     * Get formatted options for display
     */
    public function getFormattedOptionsAttribute(): array
    {
        if (!is_array($this->options)) {
            return [];
        }

        // For MCQ, return options with letters (A, B, C, D)
        if ($this->question_type === 'mcq') {
            $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
            $formatted = [];
            
            foreach ($this->options as $index => $option) {
                if (isset($letters[$index])) {
                    $formatted[$letters[$index]] = $option;
                }
            }
            
            return $formatted;
        }
        
        return $this->options;
    }

    /**
     * Get the correct option letter (A, B, C, D)
     */
    public function getCorrectOptionLetterAttribute(): string
    {
        // If correct_answer is already set (as A, B, C, D), return it
        if (isset($this->attributes['correct_answer']) && in_array($this->attributes['correct_answer'], ['A', 'B', 'C', 'D'])) {
            return $this->attributes['correct_answer'];
        }
        
        // Otherwise, derive from correct_option
        $letters = ['A', 'B', 'C', 'D'];
        return $letters[$this->correct_option ?? 0] ?? 'A';
    }

    /**
     * Get the correct answer text (the actual option text)
     */
    public function getCorrectAnswerTextAttribute(): string
    {
        // Get the letter (A, B, C, D)
        $letter = $this->correct_option_letter;
        $letterIndex = ord($letter) - ord('A');
        
        // Return the option text at that index
        if (is_array($this->options) && isset($this->options[$letterIndex])) {
            return $this->options[$letterIndex];
        }
        
        // Fallback: try to get from option_{letter} fields
        $optionField = 'option_' . strtolower($letter);
        return $this->attributes[$optionField] ?? 'N/A';
    }

    /**
     * Get difficulty attribute (alias for difficulty_level)
     */
    public function getDifficultyAttribute(): string
    {
        return ucfirst($this->difficulty_level ?? 'Medium');
    }

    /**
     * Set difficulty attribute (alias for difficulty_level)
     */
    public function setDifficultyAttribute($value): void
    {
        $this->attributes['difficulty_level'] = strtolower($value);
    }

    /**
     * Get the category this question belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the category name attribute
     */
    public function getCategoryNameAttribute(): string
    {
        return $this->category ? $this->category->name : 'Uncategorized';
    }
}