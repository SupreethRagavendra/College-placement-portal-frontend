<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender',
        'message',
        'formatted_message',
        'context_used',
        'knowledge_retrieved',
        'model_used',
        'intent',
        'entities',
        'confidence_score',
        'is_helpful',
        'reaction'
    ];

    protected $casts = [
        'context_used' => 'array',
        'knowledge_retrieved' => 'array',
        'entities' => 'array',
        'is_helpful' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the conversation that owns the message
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }

    /**
     * Check if the message is from a student
     */
    public function isFromStudent(): bool
    {
        return $this->sender === 'student';
    }

    /**
     * Check if the message is from the bot
     */
    public function isFromBot(): bool
    {
        return $this->sender === 'bot';
    }

    /**
     * Get formatted message with rich content
     */
    public function getFormattedAttribute(): string
    {
        if ($this->formatted_message) {
            return $this->formatted_message;
        }

        // Convert markdown-like syntax to HTML
        $formatted = $this->message;
        
        // Convert code blocks
        $formatted = preg_replace('/```(.*?)```/s', '<pre><code>$1</code></pre>', $formatted);
        
        // Convert inline code
        $formatted = preg_replace('/`([^`]+)`/', '<code>$1</code>', $formatted);
        
        // Convert bold
        $formatted = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $formatted);
        
        // Convert lists
        $formatted = preg_replace('/^\* (.+)$/m', '<li>$1</li>', $formatted);
        $formatted = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $formatted);
        
        // Convert line breaks
        $formatted = nl2br($formatted);
        
        return $formatted;
    }

    /**
     * Add user feedback
     */
    public function addFeedback(bool $helpful, ?string $reaction = null): void
    {
        $this->is_helpful = $helpful;
        if ($reaction) {
            $this->reaction = $reaction;
        }
        $this->save();
    }

    /**
     * Scope for high confidence messages
     */
    public function scopeHighConfidence($query)
    {
        return $query->where('confidence_score', '>=', 80);
    }

    /**
     * Scope for helpful messages
     */
    public function scopeHelpful($query)
    {
        return $query->where('is_helpful', true);
    }

    /**
     * Extract entities from the message
     */
    public function extractEntities(): array
    {
        $entities = [];
        $message = strtolower($this->message);

        // Extract assessment names
        if (preg_match_all('/\b(aptitude|technical|programming|database|web)\b/i', $message, $matches)) {
            $entities['assessments'] = array_unique($matches[0]);
        }

        // Extract score/percentage mentions
        if (preg_match_all('/\b(\d+)%?\b/', $message, $matches)) {
            $entities['numbers'] = $matches[1];
        }

        // Extract time mentions
        if (preg_match_all('/\b(today|yesterday|last week|this month)\b/i', $message, $matches)) {
            $entities['time_references'] = $matches[0];
        }

        // Extract difficulty levels
        if (preg_match_all('/\b(easy|medium|hard|difficult)\b/i', $message, $matches)) {
            $entities['difficulty'] = array_unique($matches[0]);
        }

        $this->entities = $entities;
        $this->save();

        return $entities;
    }
}
