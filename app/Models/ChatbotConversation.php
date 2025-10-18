<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'session_id',
        'title',
        'topic',
        'context',
        'metadata',
        'status',
        'last_activity'
    ];

    protected $casts = [
        'context' => 'array',
        'metadata' => 'array',
        'last_activity' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the student that owns the conversation
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get all messages for this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatbotMessage::class, 'conversation_id');
    }

    /**
     * Get the last message in the conversation
     */
    public function lastMessage()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Get conversation summary
     */
    public function getSummaryAttribute(): string
    {
        $lastMessage = $this->lastMessage();
        if ($lastMessage) {
            return str_limit($lastMessage->message, 100);
        }
        return 'New conversation';
    }

    /**
     * Update conversation context
     */
    public function updateContext(array $newContext): void
    {
        $currentContext = $this->context ?? [];
        $this->context = array_merge($currentContext, $newContext);
        $this->last_activity = now();
        $this->save();
    }

    /**
     * Archive the conversation
     */
    public function archive(): void
    {
        $this->status = 'archived';
        $this->save();
    }

    /**
     * Scope for active conversations
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for recent conversations
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('last_activity', 'desc');
    }

    /**
     * Generate a title from the first message
     */
    public function generateTitle(): void
    {
        $firstMessage = $this->messages()->orderBy('created_at')->first();
        if ($firstMessage && !$this->title) {
            $this->title = str_limit($firstMessage->message, 50);
            $this->save();
        }
    }

    /**
     * Extract the main topic from messages
     */
    public function extractTopic(): void
    {
        $messages = $this->messages()->pluck('intent')->filter()->toArray();
        if (count($messages) > 0) {
            $intents = array_count_values($messages);
            arsort($intents);
            $this->topic = key($intents);
            $this->save();
        }
    }
}
