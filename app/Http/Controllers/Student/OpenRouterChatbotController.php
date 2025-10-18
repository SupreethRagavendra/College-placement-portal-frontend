<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\StudentAssessment;
use App\Models\StudentResult;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OpenRouterChatbotController extends Controller
{
    private string $ragServiceUrl;
    private int $timeout;
    
    public function __construct()
    {
        $this->ragServiceUrl = config('rag.service_url', 'http://localhost:8001');
        // Reduced timeout for better performance - 10 seconds max
        $this->timeout = config('rag.timeout', 10);
    }
    
    /**
     * Handle chatbot questions with OpenRouter RAG service
     */
    public function chat(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        $failed = false;
        $errorMessage = null;
        
        try {
            $request->validate([
                'message' => 'required|string|max:500'
            ]);
            
            $query = $request->input('message');
            $studentId = Auth::id();
            $sessionId = session()->getId();
            
            Log::info('OpenRouter Chatbot query', [
                'student_id' => $studentId,
                'query' => $query,
                'session_id' => $sessionId
            ]);
            
            // Get or create conversation with message history
            $conversation = \DB::table('chatbot_conversations')
                ->where('student_id', $studentId)
                ->where('session_id', $sessionId)
                ->first();
            
            $conversationHistory = [];
            if ($conversation && $conversation->messages) {
                $conversationHistory = json_decode($conversation->messages, true) ?? [];
                Log::info('Retrieved conversation history', [
                    'message_count' => count($conversationHistory)
                ]);
            }
            
            // Gather student context (with caching for performance)
            $studentContext = $this->gatherStudentContext($studentId);
            
            Log::info('Fresh context gathered', [
                'available_assessments' => count($studentContext['available_assessments']),
                'completed_assessments' => count($studentContext['completed_assessments']),
                'in_progress' => count($studentContext['in_progress_assessments'])
            ]);
            
            // Call OpenRouter RAG service with context and conversation history
            try {
                $student = Auth::user();
                $response = Http::timeout($this->timeout)->post($this->ragServiceUrl . '/chat', [
                    'student_id' => $studentId,
                    'message' => $query,
                    'student_name' => $student->name,
                    'student_email' => $student->email,
                    'session_id' => $sessionId,
                    'student_context' => $studentContext,
                    'conversation_history' => $conversationHistory  // Add conversation history
                ]);
                
            if ($response->successful()) {
                $data = $response->json();
                
                // Log the full response for debugging
                Log::info('RAG Response received', [
                    'query_type' => $data['query_type'] ?? 'unknown',
                    'model_used' => $data['model_used'] ?? 'NOT SET',
                    'has_special_action' => isset($data['special_action']),
                    'special_action' => $data['special_action'] ?? null,
                    'message_preview' => substr($data['message'] ?? '', 0, 100)
                ]);
                
                // Update conversation history with new messages
                $conversationHistory[] = ['role' => 'user', 'content' => $query];
                $conversationHistory[] = ['role' => 'assistant', 'content' => $data['message']];
                
                // Keep only last 10 messages (5 conversation turns)
                $conversationHistory = array_slice($conversationHistory, -10);
                
                // Save conversation to database
                \DB::table('chatbot_conversations')->updateOrInsert(
                    [
                        'student_id' => $studentId,
                        'session_id' => $sessionId
                    ],
                    [
                        'messages' => json_encode($conversationHistory),
                        'last_message_at' => now(),
                        'last_activity' => now(),
                        'updated_at' => now(),
                        'created_at' => $conversation ? $conversation->created_at : now()
                    ]
                );
                
                Log::info('Conversation history saved', [
                    'total_messages' => count($conversationHistory)
                ]);
                
                // Log analytics
                $responseTime = (microtime(true) - $startTime) * 1000; // milliseconds
                $this->logAnalytics(
                    $studentId,
                    $query,
                    $data['query_type'] ?? 'unknown',
                    $data['message'] ?? '',
                    $data['model_used'] ?? null,
                    $responseTime,
                    $data['tokens_used'] ?? null,
                    $data['from_cache'] ?? false,
                    false,
                    null
                );
                
                // Check for special actions (like name update)
                if (isset($data['special_action']) && $data['special_action']['type'] === 'update_name') {
                    $newName = $data['special_action']['new_name'];
                    
                    Log::info('NAME UPDATE DETECTED', [
                        'new_name' => $newName,
                        'student_id' => $studentId
                    ]);
                    
                    // Update student name in database
                    $currentStudent = Auth::user();
                    $oldName = $currentStudent->name;
                    $currentStudent->name = $newName;
                    $currentStudent->save();
                    
                    // Verify the update
                    $currentStudent->refresh();
                    
                    Log::info('✏️ NAME UPDATED via RAG', [
                        'student_id' => $studentId,
                        'old_name' => $oldName,
                        'new_name' => $newName,
                        'verified_name' => $currentStudent->name
                    ]);
                    
                    // Update message to confirm database save
                    $data['message'] = "Perfect! I've updated your name to {$newName} ✓ Your profile has been updated in the database!";
                }
                
                // Add mode metadata for MODE 1 if not present
                if (!isset($data['mode'])) {
                    $data['mode'] = 'rag_active';
                    $data['mode_name'] = '🟢 Mode 1: RAG ACTIVE';
                    $data['mode_color'] = '#10b981';
                    $data['mode_description'] = 'Full AI-powered responses with context';
                }
                
                Log::info('🟢 MODE 1: RAG ACTIVE - OpenRouter RAG response received', [
                    'query_type' => $data['query_type'] ?? 'unknown',
                    'has_actions' => !empty($data['actions']),
                    'mode' => $data['mode']
                ]);
                
                // Cache successful responses (non-personalized ones)
                if ($this->isCacheable($query)) {
                    $cacheKey = 'chatbot_response_' . md5($query);
                    Cache::put($cacheKey, $data, 300); // 5 minutes
                }
                
                return response()->json($data);
            }
                
                // RAG service returned error
                Log::warning('OpenRouter RAG service returned error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                // Fall back to simple response
                return $this->fallbackResponse($query, $studentId);
                
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('Cannot connect to OpenRouter RAG service: ' . $e->getMessage());
                return $this->fallbackResponse($query, $studentId);
            }
            
        } catch (\Exception $e) {
            $failed = true;
            $errorMessage = $e->getMessage();
            
            Log::error('OpenRouter Chatbot error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Log failed analytics
            $responseTime = (microtime(true) - $startTime) * 1000;
            $this->logAnalytics(
                $studentId ?? 0,
                $query ?? '',
                'error',
                '',
                null,
                $responseTime,
                null,
                false,
                true,
                $errorMessage
            );
            
            // Instead of returning an error, use the fallback response
            return $this->fallbackResponse($query ?? '', $studentId ?? 0);
        }
    }
    
    /**
     * Health check for RAG service
     */
    public function health(): JsonResponse
    {
        try {
            // Check if RAG service is running
            $response = Http::timeout(5)->get($this->ragServiceUrl . '/health');
            
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'status' => 'healthy',
                    'rag_service' => true,
                    'service_url' => $this->ragServiceUrl,
                    'response_time' => $response->transferStats->getHandlerStat('total_time') ?? 0,
                    'details' => $data,
                    'mode' => 'rag_active',
                    'ui_indicator' => '🟢',
                    'ui_text' => 'Online - AI Ready'
                ]);
            } else {
                // RAG service is down but Laravel is working - LIMITED MODE
                return response()->json([
                    'status' => 'limited',
                    'rag_service' => false,
                    'service_url' => $this->ragServiceUrl,
                    'error' => 'RAG service returned error status: ' . $response->status(),
                    'mode' => 'limited',
                    'ui_indicator' => '🟡',
                    'ui_text' => 'Limited Mode',
                    'fallback_available' => true
                ], 200); // Return 200 to indicate Laravel is working
            }
            
        } catch (\Exception $e) {
            Log::warning('RAG service health check failed: ' . $e->getMessage());
            
            // RAG service is down but Laravel is working - LIMITED MODE
            return response()->json([
                'status' => 'limited',
                'rag_service' => false,
                'service_url' => $this->ragServiceUrl,
                'error' => 'Cannot connect to RAG service: ' . $e->getMessage(),
                'mode' => 'limited',
                'ui_indicator' => '🟡',
                'ui_text' => 'Limited Mode',
                'fallback_available' => true
            ], 200); // Return 200 to indicate Laravel is working
        }
    }
    
    /**
     * Sync knowledge base (admin only)
     */
    public function syncKnowledge(Request $request): JsonResponse
    {
        try {
            Log::info('Triggering OpenRouter RAG knowledge sync');
            
            $response = Http::timeout(60)->post($this->ragServiceUrl . '/sync');
            
            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => 'Knowledge base synced successfully',
                    'data' => $data
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to sync knowledge base',
                    'error' => $response->body()
                ], 500);
            }
            
        } catch (\Exception $e) {
            Log::error('Knowledge sync failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync knowledge base: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Gather comprehensive student context with caching for performance
     */
    private function gatherStudentContext(int $studentId): array
    {
        // Cache student context for 5 minutes to reduce database load
        return Cache::remember("student_context_{$studentId}", 300, function() use ($studentId) {
            return $this->fetchStudentContext($studentId);
        });
    }
    
    /**
     * Fetch fresh student context from database
     */
    private function fetchStudentContext(int $studentId): array
    {
        try {
            // Get available assessments (not taken by this student)
            $availableAssessments = Assessment::active()
                ->whereDoesntHave('studentResults', function($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                })
                ->select('id', 'name', 'description', 'category', 'total_time', 'difficulty_level')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($assessment) {
                    return [
                        'id' => $assessment->id,
                        'title' => $assessment->name ?? $assessment->title,
                        'description' => $assessment->description,
                        'category' => $assessment->category,
                        'duration' => $assessment->total_time ?? $assessment->duration ?? 30,
                        'difficulty_level' => $assessment->difficulty_level,
                        'total_questions' => $assessment->questions_count ?? 0
                    ];
                });

            // Get completed assessments with results
            $completedAssessments = StudentResult::where('student_id', $studentId)
                ->with('assessment:id,name,category,total_time')
                ->orderBy('submitted_at', 'desc')
                ->get()
                ->map(function($result) {
                    return [
                        'id' => $result->assessment_id,
                        'title' => $result->assessment->name ?? $result->assessment->title ?? 'Unknown Assessment',
                        'category' => $result->assessment->category ?? 'Unknown',
                        'score' => $result->score,
                        'total_questions' => $result->total_questions,
                        'percentage' => $result->total_questions > 0 ? round(($result->score / $result->total_questions) * 100, 2) : 0,
                        'time_taken' => $result->time_taken,
                        'submitted_at' => $result->submitted_at?->format('Y-m-d H:i:s'),
                        'passed' => $result->total_questions > 0 ? ($result->score / $result->total_questions) >= 0.6 : false
                    ];
                });

            // Get in-progress assessments (if any)
            $inProgressAssessments = StudentAssessment::where('student_id', $studentId)
                ->whereNull('submitted_at')
                ->with('assessment:id,name,category,total_time')
                ->get()
                ->map(function($attempt) {
                    return [
                        'id' => $attempt->assessment_id,
                        'title' => $attempt->assessment->name ?? $attempt->assessment->title ?? 'Unknown Assessment',
                        'category' => $attempt->assessment->category ?? 'Unknown',
                        'started_at' => $attempt->started_at?->format('Y-m-d H:i:s'),
                        'time_remaining' => $attempt->time_remaining ?? 0
                    ];
                });

            // Get student profile
            $student = Auth::user();
            $studentProfile = [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'role' => $student->role,
                'is_approved' => $student->is_approved ?? false,
                'created_at' => $student->created_at?->format('Y-m-d H:i:s')
            ];

            // Calculate statistics
            $totalCompleted = $completedAssessments->count();
            $totalPassed = $completedAssessments->where('passed', true)->count();
            $averageScore = $totalCompleted > 0 ? $completedAssessments->avg('percentage') : 0;

            return [
                'student_profile' => $studentProfile,
                'available_assessments' => $availableAssessments->toArray(),
                'completed_assessments' => $completedAssessments->toArray(),
                'in_progress_assessments' => $inProgressAssessments->toArray(),
                'statistics' => [
                    'total_completed' => $totalCompleted,
                    'total_passed' => $totalPassed,
                    'average_score' => round($averageScore, 2),
                    'pass_rate' => $totalCompleted > 0 ? round(($totalPassed / $totalCompleted) * 100, 2) : 0
                ],
                'timestamp' => now()->toISOString()
            ];

        } catch (\Exception $e) {
            Log::error('Error gathering student context: ' . $e->getMessage());
            
            // Return minimal context on error
            return [
                'student_profile' => [
                    'id' => $studentId,
                    'name' => Auth::user()->name ?? 'Student',
                    'email' => Auth::user()->email ?? '',
                    'role' => 'student'
                ],
                'available_assessments' => [],
                'completed_assessments' => [],
                'in_progress_assessments' => [],
                'statistics' => [
                    'total_completed' => 0,
                    'total_passed' => 0,
                    'average_score' => 0,
                    'pass_rate' => 0
                ],
                'timestamp' => now()->toISOString()
            ];
        }
    }
    
    /**
     * Generate fallback response when RAG service is unavailable
     * MODE 2: LIMITED MODE - Database queries with pattern matching
     */
    private function fallbackResponse(string $query, int $studentId): JsonResponse
    {
        Log::info('🟡 MODE 2: LIMITED MODE activated - Using database fallback', [
            'student_id' => $studentId,
            'query' => $query
        ]);
        
        try {
            $query = strtolower(trim($query));
            
            // Intelligence cutoff: Check if query contains relevant keywords
            $relevantKeywords = [
                // Assessment related
                'assessment', 'test', 'exam', 'quiz', 'available', 'take', 'start',
                // Results related
                'result', 'score', 'performance', 'grade', 'mark', 'pass', 'fail',
                // Statistics related
                'stat', 'progress', 'how am i', 'how many', 'doing',
                // Category related
                'technical', 'aptitude', 'category', 'subject', 'topic',
                // History related
                'recent', 'last', 'latest', 'history', 'completed', 'finished',
                // Performance related
                'best', 'worst', 'highest', 'lowest', 'top', 'improve', 'weak', 'strong',
                // Profile related
                'profile', 'my name', 'who am i', 'account', 'email',
                // Help related
                'help', 'guide', 'what', 'show', 'tell', 'how', 'when', 'where', 'can',
                // Common study words
                'study', 'learn', 'prepare', 'practice', 'ready'
            ];
            
            // Check relevance
            $isRelevant = false;
            $matchedKeyword = null;
            foreach ($relevantKeywords as $keyword) {
                if (str_contains($query, $keyword)) {
                    $isRelevant = true;
                    $matchedKeyword = $keyword;
                    break;
                }
            }
            
            // If query is too short (likely just greeting), treat as relevant
            if (strlen($query) <= 10 && (str_contains($query, 'hi') || str_contains($query, 'hello') || str_contains($query, 'hey'))) {
                $isRelevant = true;
            }
            
            // If query is irrelevant, return friendly refocus message
            if (!$isRelevant && strlen($query) > 2) {
                Log::info('🟡 MODE 2: Off-topic query detected', [
                    'query' => $query,
                    'length' => strlen($query)
                ]);
                
                // Extract meaningful words from query (skip common words)
                $queryWords = array_filter(explode(' ', $query), function($word) {
                    $skipWords = ['the', 'is', 'are', 'was', 'were', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for'];
                    return !in_array($word, $skipWords) && strlen($word) > 2;
                });
                
                // Get first meaningful word or use generic term
                $topicWord = count($queryWords) > 0 ? ucfirst(reset($queryWords)) : 'That';
                
                // Friendly acknowledgment with refocus
                $message = "Hmm, {$topicWord} sounds interesting! But let's stay focused on your studies. 📚\n\n";
                $message .= "I'm your study assistant and can help you with:\n\n";
                $message .= "📝 Available assessments\n";
                $message .= "📊 Your test results\n";
                $message .= "📈 Performance statistics\n";
                $message .= "🎯 Study progress tracking\n\n";
                $message .= "Try asking:\n";
                $message .= "• 'Show available assessments'\n";
                $message .= "• 'What are my results?'\n";
                $message .= "• 'How am I doing?'";
                
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'response' => $message,
                    'data' => [],
                    'actions' => [
                        [
                            'type' => 'link',
                            'label' => 'View Assessments',
                            'url' => route('student.assessments.index')
                        ]
                    ],
                    'follow_up_questions' => [
                        'Show available assessments',
                        'What are my results?',
                        'How am I doing?',
                        'Show my statistics',
                        'What should I study next?'
                    ],
                    'timestamp' => now()->toISOString(),
                    'query_type' => 'off_topic',
                    'model_used' => 'limited',
                    'rag_status' => 'database_only',
                    'mode' => 'database_only',
                    'mode_name' => '🟡 Mode 2: LIMITED MODE',
                    'mode_color' => '#f59e0b',
                    'mode_description' => 'Study assistant - Database queries only',
                    'service_info' => [
                        'indicator' => '🟡',
                        'text' => 'Limited Mode'
                    ]
                ]);
            }
            
            Log::info('🟡 MODE 2: Query accepted as relevant', [
                'query' => $query,
                'matched_keyword' => $matchedKeyword
            ]);
            
            Log::info('🟡 MODE 2: Pattern matching', [
                'query' => $query,
                'contains_assessment' => str_contains($query, 'assessment'),
                'contains_available' => str_contains($query, 'available'),
                'contains_show' => str_contains($query, 'show')
            ]);
            
            // 1. AVAILABLE ASSESSMENTS
            if (str_contains($query, 'assessment') || str_contains($query, 'test') || str_contains($query, 'exam') || str_contains($query, 'available')) {
                Log::info('🟡 MODE 2: Querying assessments from database');
                
                $assessments = Assessment::active()
                    ->whereDoesntHave('studentResults', function($q) use ($studentId) {
                        $q->where('student_id', $studentId);
                    })
                    ->select('id', 'name', 'category', 'total_time', 'difficulty_level')
                    ->limit(5)
                    ->get();
                
                Log::info('🟡 MODE 2: Assessment query result', [
                    'count' => $assessments->count()
                ]);
                
                if ($assessments->count() > 0) {
                    $message = "🟡 LIMITED MODE - Database Query Results:\n\n";
                    $message .= "You have {$assessments->count()} assessment(s) available:\n\n";
                    foreach ($assessments as $assessment) {
                        $assessmentName = $assessment->name ?? $assessment->title;
                        $assessmentDuration = $assessment->total_time ?? $assessment->duration ?? 30;
                        $difficulty = $assessment->difficulty_level ?? 'medium';
                        $difficultyIcon = $difficulty === 'easy' ? '🟢' : ($difficulty === 'hard' ? '🔴' : '🟡');
                        $message .= "📝 {$assessmentName}\n";
                        $message .= "   Category: {$assessment->category} | Duration: {$assessmentDuration} min | {$difficultyIcon} {$difficulty}\n\n";
                    }
                    $message .= "Click 'View Assessments' to start!";
                } else {
                    $message = "🟡 LIMITED MODE:\n\nNo assessments are currently available. Please check back later!";
                }
                
            // 2. RESULTS & PERFORMANCE
            } elseif (str_contains($query, 'result') || str_contains($query, 'score') || str_contains($query, 'performance')) {
                $results = StudentResult::where('student_id', $studentId)
                    ->with('assessment:id,name,category')
                    ->orderBy('submitted_at', 'desc')
                    ->limit(5)
                    ->get();
                
                if ($results->count() > 0) {
                    $totalScore = 0;
                    $totalQuestions = 0;
                    
                    $message = "🟡 LIMITED MODE - Your Recent Results:\n\n";
                    foreach ($results as $result) {
                        $percentage = $result->total_questions > 0 ? round(($result->score / $result->total_questions) * 100, 2) : 0;
                        $assessmentName = $result->assessment->name ?? $result->assessment->title ?? 'Unknown Assessment';
                        $status = $percentage >= 60 ? '✅ Pass' : '❌ Fail';
                        $message .= "📊 {$assessmentName}\n";
                        $message .= "   Score: {$result->score}/{$result->total_questions} ({$percentage}%) {$status}\n";
                        $message .= "   Date: " . $result->submitted_at?->format('M d, Y') . "\n\n";
                        
                        $totalScore += $result->score;
                        $totalQuestions += $result->total_questions;
                    }
                    
                    $avgPercentage = $totalQuestions > 0 ? round(($totalScore / $totalQuestions) * 100, 2) : 0;
                    $message .= "📈 Overall Average: {$avgPercentage}%";
                } else {
                    $message = "🟡 LIMITED MODE:\n\nYou haven't completed any assessments yet. Take an assessment to see your results!";
                }
                
            // 3. STATISTICS & PROGRESS
            } elseif (str_contains($query, 'stat') || str_contains($query, 'progress') || str_contains($query, 'how am i') || str_contains($query, 'how many')) {
                $totalCompleted = StudentResult::where('student_id', $studentId)->count();
                $totalAvailable = Assessment::active()->count();
                $results = StudentResult::where('student_id', $studentId)->get();
                
                $passed = 0;
                $failed = 0;
                $totalScore = 0;
                $totalQuestions = 0;
                
                foreach ($results as $result) {
                    $percentage = $result->total_questions > 0 ? ($result->score / $result->total_questions) * 100 : 0;
                    if ($percentage >= 60) {
                        $passed++;
                    } else {
                        $failed++;
                    }
                    $totalScore += $result->score;
                    $totalQuestions += $result->total_questions;
                }
                
                $avgPercentage = $totalQuestions > 0 ? round(($totalScore / $totalQuestions) * 100, 2) : 0;
                $passRate = $totalCompleted > 0 ? round(($passed / $totalCompleted) * 100, 2) : 0;
                
                $message = "🟡 LIMITED MODE - Your Statistics:\n\n";
                $message .= "📊 Tests Completed: {$totalCompleted}\n";
                $message .= "📝 Tests Available: {$totalAvailable}\n";
                $message .= "✅ Passed: {$passed}\n";
                $message .= "❌ Failed: {$failed}\n";
                $message .= "📈 Average Score: {$avgPercentage}%\n";
                $message .= "🎯 Pass Rate: {$passRate}%\n\n";
                
                if ($avgPercentage >= 80) {
                    $message .= "🌟 Excellent performance! Keep it up!";
                } elseif ($avgPercentage >= 60) {
                    $message .= "👍 Good work! You're on the right track!";
                } else {
                    $message .= "💪 Keep practicing! You can improve!";
                }
                
            // 4. CATEGORY-SPECIFIC QUERIES
            } elseif (str_contains($query, 'technical') || str_contains($query, 'aptitude') || str_contains($query, 'category')) {
                $category = str_contains($query, 'technical') ? 'Technical' : 'Aptitude';
                
                $assessments = Assessment::active()
                    ->where('category', $category)
                    ->whereDoesntHave('studentResults', function($q) use ($studentId) {
                        $q->where('student_id', $studentId);
                    })
                    ->select('id', 'name', 'category', 'total_time', 'difficulty_level')
                    ->limit(5)
                    ->get();
                
                if ($assessments->count() > 0) {
                    $message = "🟡 LIMITED MODE - {$category} Assessments:\n\n";
                    $message .= "Found {$assessments->count()} {$category} assessment(s):\n\n";
                    foreach ($assessments as $assessment) {
                        $assessmentName = $assessment->name ?? $assessment->title;
                        $assessmentDuration = $assessment->total_time ?? $assessment->duration ?? 30;
                        $difficulty = $assessment->difficulty_level ?? 'medium';
                        $message .= "📝 {$assessmentName} ({$assessmentDuration} min, {$difficulty})\n";
                    }
                } else {
                    $message = "🟡 LIMITED MODE:\n\nNo {$category} assessments are currently available.";
                }
                
            // 5. RECENT ACTIVITY
            } elseif (str_contains($query, 'recent') || str_contains($query, 'last') || str_contains($query, 'latest')) {
                $recentResult = StudentResult::where('student_id', $studentId)
                    ->with('assessment:id,name,category')
                    ->orderBy('submitted_at', 'desc')
                    ->first();
                
                if ($recentResult) {
                    $percentage = $recentResult->total_questions > 0 ? round(($recentResult->score / $recentResult->total_questions) * 100, 2) : 0;
                    $assessmentName = $recentResult->assessment->name ?? 'Unknown Assessment';
                    $status = $percentage >= 60 ? '✅ Passed' : '❌ Failed';
                    
                    $message = "🟡 LIMITED MODE - Your Latest Activity:\n\n";
                    $message .= "📝 Assessment: {$assessmentName}\n";
                    $message .= "📊 Score: {$recentResult->score}/{$recentResult->total_questions} ({$percentage}%)\n";
                    $message .= "🎯 Status: {$status}\n";
                    $message .= "📅 Date: " . $recentResult->submitted_at?->format('M d, Y h:i A') . "\n";
                    $message .= "⏱️ Time Taken: " . ($recentResult->time_taken ?? 0) . " minutes\n";
                } else {
                    $message = "🟡 LIMITED MODE:\n\nNo recent activity found. Start an assessment to track your progress!";
                }
                
            // 6. BEST/WORST PERFORMANCE
            } elseif (str_contains($query, 'best') || str_contains($query, 'highest') || str_contains($query, 'top')) {
                $bestResult = StudentResult::where('student_id', $studentId)
                    ->with('assessment:id,name,category')
                    ->get()
                    ->sortByDesc(function($result) {
                        return $result->total_questions > 0 ? ($result->score / $result->total_questions) : 0;
                    })
                    ->first();
                
                if ($bestResult) {
                    $percentage = $bestResult->total_questions > 0 ? round(($bestResult->score / $bestResult->total_questions) * 100, 2) : 0;
                    $assessmentName = $bestResult->assessment->name ?? 'Unknown Assessment';
                    
                    $message = "🟡 LIMITED MODE - Your Best Performance:\n\n";
                    $message .= "🏆 Assessment: {$assessmentName}\n";
                    $message .= "⭐ Score: {$bestResult->score}/{$bestResult->total_questions} ({$percentage}%)\n";
                    $message .= "📅 Date: " . $bestResult->submitted_at?->format('M d, Y') . "\n\n";
                    $message .= "Great job! Keep up the excellent work! 🎉";
                } else {
                    $message = "🟡 LIMITED MODE:\n\nNo results yet. Take an assessment to set your best score!";
                }
                
            } elseif (str_contains($query, 'worst') || str_contains($query, 'lowest') || str_contains($query, 'improve')) {
                $worstResult = StudentResult::where('student_id', $studentId)
                    ->with('assessment:id,name,category')
                    ->get()
                    ->sortBy(function($result) {
                        return $result->total_questions > 0 ? ($result->score / $result->total_questions) : 0;
                    })
                    ->first();
                
                if ($worstResult) {
                    $percentage = $worstResult->total_questions > 0 ? round(($worstResult->score / $worstResult->total_questions) * 100, 2) : 0;
                    $assessmentName = $worstResult->assessment->name ?? 'Unknown Assessment';
                    
                    $message = "🟡 LIMITED MODE - Area for Improvement:\n\n";
                    $message .= "📝 Assessment: {$assessmentName}\n";
                    $message .= "📊 Score: {$worstResult->score}/{$worstResult->total_questions} ({$percentage}%)\n";
                    $message .= "📅 Date: " . $worstResult->submitted_at?->format('M d, Y') . "\n\n";
                    $message .= "💪 Focus on this area to improve your overall performance!";
                } else {
                    $message = "🟡 LIMITED MODE:\n\nNo results yet. Take assessments to identify areas for improvement!";
                }
                
            // 7. PROFILE INFO
            } elseif (str_contains($query, 'profile') || str_contains($query, 'my name') || str_contains($query, 'who am i')) {
                $student = Auth::user();
                $totalCompleted = StudentResult::where('student_id', $studentId)->count();
                $joinedDate = $student->created_at?->format('M d, Y');
                
                $message = "🟡 LIMITED MODE - Your Profile:\n\n";
                $message .= "👤 Name: {$student->name}\n";
                $message .= "📧 Email: {$student->email}\n";
                $message .= "🎓 Role: Student\n";
                $message .= "📅 Joined: {$joinedDate}\n";
                $message .= "📊 Tests Completed: {$totalCompleted}\n";
                
            // 8. HELP & GUIDANCE
            } elseif (str_contains($query, 'help') || str_contains($query, 'how') || str_contains($query, 'guide')) {
                $message = "🟡 LIMITED MODE - I can help you with:\n\n";
                $message .= "📝 Assessments:\n";
                $message .= "   • 'Show available assessments'\n";
                $message .= "   • 'Show technical assessments'\n";
                $message .= "   • 'Show aptitude tests'\n\n";
                $message .= "📊 Results:\n";
                $message .= "   • 'Show my results'\n";
                $message .= "   • 'What's my best score?'\n";
                $message .= "   • 'Show my statistics'\n\n";
                $message .= "📈 Progress:\n";
                $message .= "   • 'How am I doing?'\n";
                $message .= "   • 'Show my progress'\n";
                $message .= "   • 'What's my recent activity?'\n\n";
                $message .= "What would you like to know?";
                
            // 9. DEFAULT GREETING
            } else {
                $student = Auth::user();
                $availableCount = Assessment::active()
                    ->whereDoesntHave('studentResults', function($q) use ($studentId) {
                        $q->where('student_id', $studentId);
                    })
                    ->count();
                $completedCount = StudentResult::where('student_id', $studentId)->count();
                
                $message = "🟡 LIMITED MODE\n\n";
                $message .= "Hello {$student->name}! 👋\n\n";
                $message .= "Quick Stats:\n";
                $message .= "• {$availableCount} assessments available\n";
                $message .= "• {$completedCount} tests completed\n\n";
                $message .= "I can help you with:\n";
                $message .= "• Available assessments\n";
                $message .= "• Your test results\n";
                $message .= "• Performance statistics\n";
                $message .= "• Study guidance\n\n";
                $message .= "What would you like to know?";
            }
        } catch (\Exception $e) {
            Log::error('🟡 MODE 2: Fallback response error', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            $message = "🟡 LIMITED MODE:\n\nI'm currently in limited mode and having trouble accessing the database. Please try using the portal navigation to access your assessments and results.\n\nError: " . $e->getMessage();
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'response' => $message, // Add 'response' field for frontend compatibility
            'data' => [],
            'actions' => [
                [
                    'type' => 'link',
                    'label' => 'View Assessments',
                    'url' => route('student.assessments.index')
                ],
                [
                    'type' => 'link',
                    'label' => 'View History',
                    'url' => route('student.assessment.history')
                ]
            ],
            'follow_up_questions' => [
                'Show available assessments',
                'Show my statistics',
                'What\'s my best score?',
                'Show technical assessments',
                'What\'s my recent activity?'
            ],
            'timestamp' => now()->toISOString(),
            'query_type' => 'database_fallback',
            'model_used' => 'limited',
            'rag_status' => 'database_only',
            // Mode metadata for frontend
            'mode' => 'database_only',
            'mode_name' => '🟡 Mode 2: LIMITED MODE',
            'mode_color' => '#f59e0b',
            'mode_description' => 'Database queries only - RAG service unavailable',
            'service_info' => [
                'indicator' => '🟡',
                'text' => 'Limited Mode'
            ]
        ]);
    }
    
    /**
     * Check if query response can be cached
     */
    private function isCacheable(string $query): bool
    {
        $query = strtolower(trim($query));
        
        // Don't cache personalized queries
        $personalizedKeywords = ['my', 'i', 'me', 'result', 'score', 'performance', 'profile'];
        
        foreach ($personalizedKeywords as $keyword) {
            if (str_contains($query, $keyword)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Log chatbot interaction analytics
     */
    private function logAnalytics(
        int $studentId,
        string $query,
        string $queryType,
        string $response,
        ?string $modelUsed,
        float $responseTime,
        ?int $tokensUsed,
        bool $fromCache,
        bool $failed,
        ?string $errorMessage
    ): void {
        try {
            \DB::table('chatbot_analytics')->insert([
                'student_id' => $studentId,
                'query' => substr($query, 0, 1000), // Limit to 1000 chars
                'query_type' => $queryType,
                'response' => substr($response, 0, 1000), // Limit to 1000 chars
                'model_used' => $modelUsed,
                'response_time_ms' => round($responseTime),
                'tokens_used' => $tokensUsed,
                'from_cache' => $fromCache,
                'failed' => $failed,
                'error_message' => $errorMessage ? substr($errorMessage, 0, 255) : null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            // Don't let analytics logging errors break the chatbot
            Log::warning('Failed to log analytics: ' . $e->getMessage());
        }
    }
}
