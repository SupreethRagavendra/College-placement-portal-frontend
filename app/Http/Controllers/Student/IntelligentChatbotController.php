<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\StudentAssessment;
use App\Models\StudentResult;
use App\Models\Category;
use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use App\Models\StudentPerformanceAnalytics;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class IntelligentChatbotController extends Controller
{
    private $ragServiceUrl = 'http://127.0.0.1:8001';
    
    /**
     * Handle intelligent chatbot conversations
     */
    public function chat(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
                'session_id' => 'nullable|string',
                'conversation_id' => 'nullable|integer'
            ]);
            
            $message = $request->input('message');
            $sessionId = $request->input('session_id') ?? Str::uuid()->toString();
            $conversationId = $request->input('conversation_id');
            $studentId = Auth::id();
            
            // Get or create conversation
            $conversation = $this->getOrCreateConversation($studentId, $sessionId, $conversationId);
            
            // Get comprehensive context with performance analytics
            $context = $this->getEnhancedStudentContext($studentId);
            
            // Add conversation context
            $context['session_id'] = $sessionId;
            $context['conversation_id'] = $conversation->id;
            
            // Call intelligent RAG service
            $response = $this->callIntelligentRAG($message, $context, $sessionId);
            
            // Save message to database
            $this->saveMessage($conversation, $message, $response);
            
            // Update conversation metadata
            $conversation->updateContext([
                'last_intent' => $response['intent'] ?? 'general',
                'topics' => array_unique(array_merge(
                    $conversation->context['topics'] ?? [],
                    [$response['intent'] ?? 'general']
                ))
            ]);
            
            // Track intent for analytics
            $this->trackIntent($studentId, $response['intent'] ?? 'general');
            
            // Return enhanced response
            return response()->json([
                'response' => $response['response'],
                'conversation_id' => $conversation->id,
                'session_id' => $sessionId,
                'intent' => $response['intent'] ?? null,
                'suggestions' => $response['suggestions'] ?? [],
                'confidence' => $response['confidence'] ?? 0,
                'mode_used' => $response['mode_used'] ?? 'intelligent',
                'model_used' => $response['model_used'] ?? 'unknown',
                'success' => true
            ]);
            
        } catch (\Exception $e) {
            Log::error('Intelligent Chatbot Error: ' . $e->getMessage());
            
            return response()->json([
                'response' => 'I encountered an error. Please try again.',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get or create conversation
     */
    private function getOrCreateConversation($studentId, $sessionId, $conversationId = null)
    {
        if ($conversationId) {
            $conversation = ChatbotConversation::find($conversationId);
            if ($conversation && $conversation->student_id == $studentId) {
                return $conversation;
            }
        }
        
        // Try to find active conversation for this session
        $conversation = ChatbotConversation::where('student_id', $studentId)
            ->where('session_id', $sessionId)
            ->where('status', 'active')
            ->first();
        
        if (!$conversation) {
            // Create new conversation
            $conversation = ChatbotConversation::create([
                'student_id' => $studentId,
                'session_id' => $sessionId,
                'status' => 'active',
                'last_activity' => now(),
                'context' => [
                    'start_time' => now()->toISOString(),
                    'topics' => []
                ]
            ]);
        }
        
        return $conversation;
    }
    
    /**
     * Get enhanced student context with performance analytics
     */
    private function getEnhancedStudentContext($studentId): array
    {
        $context = [];
        $user = Auth::user();
        
        // Basic info
        $context['student_id'] = $studentId;
        $context['student_name'] = $user->name;
        $context['student_email'] = $user->email;
        
        // Available assessments
        $context['available_assessments'] = $this->getAvailableAssessments();
        
        // Completed assessments with detailed analysis
        $completedData = $this->getCompletedAssessmentsWithAnalysis($studentId);
        $context['completed_assessments'] = $completedData['assessments'];
        $context['has_completed_assessments'] = count($completedData['assessments']) > 0;
        $context['completed_count'] = count($completedData['assessments']);
        
        // Performance statistics
        $context['student_statistics'] = $completedData['statistics'];
        
        // Performance analytics
        $context['performance_analytics'] = $this->getPerformanceAnalytics($studentId);
        
        // Weakness and strength analysis
        $context['weakness_summary'] = StudentPerformanceAnalytics::getWeaknessSummary($studentId);
        $context['strength_summary'] = StudentPerformanceAnalytics::getStrengthSummary($studentId);
        
        // Time management insights
        $context['time_management'] = StudentPerformanceAnalytics::getTimeManagementInsights($studentId);
        
        // Recent mistakes analysis
        $context['recent_mistakes'] = $this->getRecentMistakes($studentId);
        
        // Learning velocity
        $context['learning_velocity'] = $this->calculateLearningVelocity($studentId);
        
        // In-progress assessments
        $context['in_progress_assessments'] = $this->getInProgressAssessments($studentId);
        
        // Upcoming deadlines
        $context['upcoming_deadlines'] = $this->getUpcomingDeadlines();
        
        return $context;
    }
    
    /**
     * Get available assessments
     */
    private function getAvailableAssessments(): array
    {
        return Assessment::where('is_active', true)
            ->where(function($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->select('id', 'name', 'description', 'total_time', 'category', 'difficulty_level')
            ->get()
            ->map(function($assessment) {
                return [
                    'id' => $assessment->id,
                    'title' => $assessment->name,
                    'description' => $assessment->description,
                    'total_time' => $assessment->total_time ?? 30,
                    'category' => $assessment->category ?? 'General',
                    'difficulty' => $assessment->difficulty_level ?? 'Medium',
                    'total_questions' => $assessment->questions()->count()
                ];
            })
            ->toArray();
    }
    
    /**
     * Get completed assessments with detailed analysis
     */
    private function getCompletedAssessmentsWithAnalysis($studentId): array
    {
        $assessments = StudentAssessment::where('student_id', $studentId)
            ->where('status', 'completed')
            ->with(['assessment', 'answers.question'])
            ->orderBy('submit_time', 'desc')
            ->get();
        
        $formattedAssessments = [];
        $totalScore = 0;
        $totalQuestions = 0;
        $passedCount = 0;
        $failedCount = 0;
        $categoryPerformance = [];
        
        foreach ($assessments as $assessment) {
            $formatted = [
                'assessment_id' => $assessment->assessment_id,
                'title' => $assessment->assessment->name ?? 'Unknown',
                'category' => $assessment->assessment->category ?? 'General',
                'difficulty' => $assessment->assessment->difficulty_level ?? 'Medium',
                'score' => $assessment->obtained_marks,
                'total' => $assessment->total_marks,
                'percentage' => $assessment->percentage,
                'status' => $assessment->pass_status,
                'completed_at' => $assessment->submit_time,
                'time_taken' => $assessment->time_taken,
            ];
            
            // Analyze questions by difficulty
            if ($assessment->answers) {
                $byDifficulty = $assessment->answers->groupBy('question.difficulty');
                $formatted['performance_by_difficulty'] = [];
                
                foreach ($byDifficulty as $difficulty => $answers) {
                    $correct = $answers->where('is_correct', true)->count();
                    $total = $answers->count();
                    $formatted['performance_by_difficulty'][$difficulty] = [
                        'correct' => $correct,
                        'total' => $total,
                        'percentage' => $total > 0 ? round(($correct / $total) * 100, 1) : 0
                    ];
                }
            }
            
            $formattedAssessments[] = $formatted;
            
            // Update statistics
            $totalScore += $assessment->obtained_marks;
            $totalQuestions += $assessment->total_marks;
            
            if ($assessment->pass_status === 'pass') {
                $passedCount++;
            } else {
                $failedCount++;
            }
            
            // Track category performance
            $category = $assessment->assessment->category ?? 'General';
            if (!isset($categoryPerformance[$category])) {
                $categoryPerformance[$category] = [
                    'total' => 0,
                    'score' => 0,
                    'count' => 0
                ];
            }
            $categoryPerformance[$category]['total'] += $assessment->total_marks;
            $categoryPerformance[$category]['score'] += $assessment->obtained_marks;
            $categoryPerformance[$category]['count']++;
        }
        
        // Calculate overall statistics
        $statistics = [
            'total_completed' => count($assessments),
            'average_percentage' => count($assessments) > 0 
                ? round(($totalScore / max($totalQuestions, 1)) * 100, 1) 
                : 0,
            'passed_count' => $passedCount,
            'failed_count' => $failedCount,
            'pass_rate' => count($assessments) > 0 
                ? round(($passedCount / count($assessments)) * 100, 1) 
                : 0,
            'category_performance' => []
        ];
        
        // Format category performance
        foreach ($categoryPerformance as $category => $data) {
            $statistics['category_performance'][$category] = [
                'attempts' => $data['count'],
                'average_score' => round(($data['score'] / max($data['total'], 1)) * 100, 1)
            ];
        }
        
        return [
            'assessments' => $formattedAssessments,
            'statistics' => $statistics
        ];
    }
    
    /**
     * Get performance analytics
     */
    private function getPerformanceAnalytics($studentId): array
    {
        $analytics = StudentPerformanceAnalytics::where('student_id', $studentId)
            ->select('category', 'difficulty_level', 'accuracy_percentage', 'improvement_rate', 'consistency_score')
            ->get()
            ->groupBy('category');
        
        $formatted = [];
        foreach ($analytics as $category => $items) {
            $formatted[$category] = [
                'overall_accuracy' => round($items->avg('accuracy_percentage'), 1),
                'improvement_rate' => round($items->avg('improvement_rate'), 1),
                'consistency_score' => round($items->avg('consistency_score'), 1),
                'by_difficulty' => $items->mapWithKeys(function ($item) {
                    return [$item->difficulty_level => [
                        'accuracy' => $item->accuracy_percentage,
                        'improvement' => $item->improvement_rate
                    ]];
                })->toArray()
            ];
        }
        
        return $formatted;
    }
    
    /**
     * Get recent mistakes analysis
     */
    private function getRecentMistakes($studentId, $limit = 10): array
    {
        $recentMistakes = StudentAnswer::whereHas('studentAssessment', function($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->where('is_correct', false)
            ->with('question')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        
        $mistakes = [];
        $patterns = [];
        
        foreach ($recentMistakes as $mistake) {
            $question = $mistake->question;
            if ($question) {
                $category = $question->category ?? 'General';
                $difficulty = $question->difficulty ?? 'Medium';
                
                // Track patterns
                $patterns[$category] = ($patterns[$category] ?? 0) + 1;
                
                $mistakes[] = [
                    'category' => $category,
                    'difficulty' => $difficulty,
                    'question_snippet' => substr($question->question_text, 0, 100) . '...'
                ];
            }
        }
        
        return [
            'recent' => $mistakes,
            'patterns' => $patterns
        ];
    }
    
    /**
     * Calculate learning velocity
     */
    private function calculateLearningVelocity($studentId): array
    {
        $lastWeek = StudentAssessment::where('student_id', $studentId)
            ->where('created_at', '>=', now()->subWeek())
            ->count();
        
        $lastMonth = StudentAssessment::where('student_id', $studentId)
            ->where('created_at', '>=', now()->subMonth())
            ->count();
        
        $totalTime = StudentAssessment::where('student_id', $studentId)
            ->sum('time_taken');
        
        return [
            'assessments_last_week' => $lastWeek,
            'assessments_last_month' => $lastMonth,
            'total_study_hours' => round($totalTime / 3600, 1),
            'average_per_week' => $lastMonth > 0 ? round($lastMonth / 4, 1) : 0
        ];
    }
    
    /**
     * Get in-progress assessments
     */
    private function getInProgressAssessments($studentId): array
    {
        return StudentAssessment::where('student_id', $studentId)
            ->where('status', 'in_progress')
            ->with('assessment')
            ->get()
            ->map(function($item) {
                return [
                    'title' => $item->assessment->name ?? 'Unknown',
                    'started_at' => $item->start_time,
                    'time_elapsed' => now()->diffInMinutes($item->start_time)
                ];
            })
            ->toArray();
    }
    
    /**
     * Get upcoming assessment deadlines
     */
    private function getUpcomingDeadlines(): array
    {
        return Assessment::where('is_active', true)
            ->whereNotNull('end_date')
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addWeek())
            ->orderBy('end_date')
            ->get()
            ->map(function($assessment) {
                return [
                    'title' => $assessment->name,
                    'deadline' => $assessment->end_date,
                    'days_remaining' => now()->diffInDays($assessment->end_date)
                ];
            })
            ->toArray();
    }
    
    /**
     * Call intelligent RAG service
     */
    private function callIntelligentRAG($message, $context, $sessionId): array
    {
        try {
            $response = Http::timeout(15)->post($this->ragServiceUrl . '/chat', [
                'question' => $message,
                'context' => $context,
                'session_id' => $sessionId,
                'mode' => 'intelligent'
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new \Exception('RAG service error: ' . $response->status());
            
        } catch (\Exception $e) {
            Log::error('Intelligent RAG Error: ' . $e->getMessage());
            
            // Fallback response
            return [
                'response' => $this->generateFallbackResponse($message),
                'intent' => 'general',
                'confidence' => 0.5,
                'suggestions' => ['Check available assessments', 'View your results'],
                'mode_used' => 'fallback',
                'model_used' => 'rule-based'
            ];
        }
    }
    
    /**
     * Save message to database
     */
    private function saveMessage($conversation, $userMessage, $botResponse): void
    {
        // Save user message
        ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'student',
            'message' => $userMessage,
            'created_at' => now()
        ]);
        
        // Save bot response
        ChatbotMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'bot',
            'message' => $botResponse['response'] ?? '',
            'model_used' => $botResponse['model_used'] ?? null,
            'intent' => $botResponse['intent'] ?? null,
            'entities' => $botResponse['entities'] ?? null,
            'confidence_score' => isset($botResponse['confidence']) 
                ? round($botResponse['confidence'] * 100) 
                : null,
            'created_at' => now()
        ]);
        
        // Update conversation
        $conversation->last_activity = now();
        $conversation->save();
    }
    
    /**
     * Track user intent for analytics
     */
    private function trackIntent($studentId, $intent): void
    {
        Cache::remember("intent_{$studentId}_{$intent}", 3600, function() use ($studentId, $intent) {
            // Simple intent tracking (can be enhanced)
            return true;
        });
    }
    
    /**
     * Generate fallback response
     */
    private function generateFallbackResponse($message): string
    {
        $lower = strtolower($message);
        
        if (str_contains($lower, 'weak') || str_contains($lower, 'improve')) {
            return "To improve your performance, focus on regular practice and review your mistakes. Would you like specific recommendations?";
        }
        
        if (str_contains($lower, 'assessment') || str_contains($lower, 'test')) {
            return "You can view available assessments in the Assessments section. Would you like me to show you what's available?";
        }
        
        return "I'm here to help with your placement preparation. You can ask about assessments, results, or get study tips.";
    }
    
    /**
     * Get conversation history
     */
    public function getConversationHistory(Request $request): JsonResponse
    {
        $studentId = Auth::id();
        
        $conversations = ChatbotConversation::where('student_id', $studentId)
            ->with(['messages' => function($query) {
                $query->latest()->limit(5);
            }])
            ->active()
            ->recent()
            ->limit(10)
            ->get();
        
        $formatted = $conversations->map(function($conv) {
            return [
                'id' => $conv->id,
                'title' => $conv->title ?? $conv->getSummaryAttribute(),
                'topic' => $conv->topic,
                'last_activity' => $conv->last_activity,
                'message_count' => $conv->messages()->count(),
                'recent_messages' => $conv->messages->map(function($msg) {
                    return [
                        'sender' => $msg->sender,
                        'message' => str_limit($msg->message, 100),
                        'time' => $msg->created_at->diffForHumans()
                    ];
                })
            ];
        });
        
        return response()->json([
            'conversations' => $formatted,
            'success' => true
        ]);
    }
    
    /**
     * Add feedback to message
     */
    public function addFeedback(Request $request): JsonResponse
    {
        $request->validate([
            'message_id' => 'required|integer|exists:chatbot_messages,id',
            'helpful' => 'required|boolean',
            'reaction' => 'nullable|string|in:👍,👎,❤️,🤔,💡'
        ]);
        
        $message = ChatbotMessage::find($request->message_id);
        
        // Verify ownership
        if ($message->conversation->student_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $message->addFeedback($request->helpful, $request->reaction);
        
        return response()->json([
            'success' => true,
            'message' => 'Feedback recorded'
        ]);
    }
    
    /**
     * Get performance insights for the student
     */
    public function getPerformanceInsights(): JsonResponse
    {
        $studentId = Auth::id();
        
        // Get comprehensive insights
        $weaknessSummary = StudentPerformanceAnalytics::getWeaknessSummary($studentId);
        $strengthSummary = StudentPerformanceAnalytics::getStrengthSummary($studentId);
        $timeManagement = StudentPerformanceAnalytics::getTimeManagementInsights($studentId);
        
        // Get recent performance trend
        $recentAssessments = StudentAssessment::where('student_id', $studentId)
            ->where('status', 'completed')
            ->orderBy('submit_time', 'desc')
            ->limit(5)
            ->pluck('percentage')
            ->toArray();
        
        $trend = 'unknown';
        if (count($recentAssessments) >= 3) {
            $recent = array_slice($recentAssessments, 0, 3);
            $older = array_slice($recentAssessments, 2, 3);
            
            $recentAvg = array_sum($recent) / count($recent);
            $olderAvg = count($older) > 0 ? array_sum($older) / count($older) : $recentAvg;
            
            if ($recentAvg > $olderAvg + 5) {
                $trend = 'improving';
            } elseif ($recentAvg < $olderAvg - 5) {
                $trend = 'declining';
            } else {
                $trend = 'consistent';
            }
        }
        
        // Generate recommendations
        $recommendations = [];
        
        if (!empty($weaknessSummary)) {
            $weakest = $weaknessSummary[0];
            $recommendations[] = "Focus on {$weakest['category']} - your accuracy is {$weakest['accuracy']}%";
        }
        
        if ($timeManagement['questions_too_slow'] > 10) {
            $recommendations[] = "Practice time management - you're spending too long on difficult questions";
        }
        
        if ($trend === 'declining') {
            $recommendations[] = "Take a break and review fundamentals - your recent scores are declining";
        } elseif ($trend === 'improving') {
            $recommendations[] = "Great progress! Challenge yourself with harder assessments";
        }
        
        return response()->json([
            'success' => true,
            'insights' => [
                'weak_areas' => $weaknessSummary,
                'strong_areas' => $strengthSummary,
                'trend' => $trend,
                'time_management' => $timeManagement['recommendation'] ?? 'Keep practicing',
                'recommendations' => $recommendations
            ]
        ]);
    }
}
