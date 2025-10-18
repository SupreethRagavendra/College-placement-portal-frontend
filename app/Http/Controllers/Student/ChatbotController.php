<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ChatbotController extends Controller
{
    private $ragServiceUrl;
    
    public function __construct()
    {
        $this->ragServiceUrl = env('RAG_SERVICE_URL', 'http://localhost:8001');
    }
    
    /**
     * Handle chatbot questions with new OpenRouter RAG implementation
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);
        
        $student = Auth::user();
        
        try {
            $response = Http::timeout(30)
                ->post("{$this->ragServiceUrl}/chat", [
                    'student_id' => $student->id,
                    'message' => $request->message,
                    'student_email' => $student->email,
                    'student_name' => $student->name
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }
            
            throw new \Exception('RAG service error');
            
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'I apologize, but I\'m having trouble processing your request right now. Please try again in a moment.'
            ], 500);
        }
    }
    
    /**
     * Alternative endpoint for API access (maintains backward compatibility)
     */
    public function ask(Request $request): JsonResponse
    {
        return $this->chat($request);
    }
    
    /**
     * Trigger knowledge base sync
     */
    public function syncKnowledge()
    {
        try {
            $response = Http::timeout(60)
                ->post("{$this->ragServiceUrl}/sync-knowledge");
            
            return response()->json([
                'success' => $response->successful(),
                'message' => 'Knowledge base sync initiated'
            ]);
        } catch (\Exception $e) {
            Log::error('Knowledge sync error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sync failed'
            ], 500);
        }
    }
    
    /**
     * Get service health status
     */
    public function health(): JsonResponse
    {
        try {
            $response = Http::timeout(10)
                ->get("{$this->ragServiceUrl}/health");
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'RAG service is not responding'
            ], 503);
            
        } catch (\Exception $e) {
            Log::error('Health check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to connect to RAG service'
            ], 503);
        }
    }
}