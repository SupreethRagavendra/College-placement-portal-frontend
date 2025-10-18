<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EmbeddingHelper
{
    private string $openRouterKey;
    private string $embeddingModel;
    
    public function __construct()
    {
        $this->openRouterKey = env('OPENROUTER_API_KEY', '');
        $this->embeddingModel = env('EMBEDDING_MODEL', 'text-embedding-ada-002');
    }
    
    /**
     * Generate embedding for text using OpenRouter
     */
    public function generateEmbedding(string $text): ?array
    {
        try {
            // Check if API key is configured
            if (empty($this->openRouterKey)) {
                Log::error('OpenRouter API key not configured');
                return null;
            }
            
            // Check cache first
            $cacheKey = 'embedding_v2_' . md5($text);
            if ($cached = Cache::get($cacheKey)) {
                return $cached;
            }
            
            // Truncate text if too long (max 8191 tokens for most models)
            $text = $this->truncateText($text, 8000);
            
            Log::info('Generating embedding for text', ['length' => strlen($text)]);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openRouterKey,
                'HTTP-Referer' => env('APP_URL', 'https://college-placement-portal.com'),
                'X-Title' => 'College Placement Portal',
                'Content-Type' => 'application/json'
            ])->timeout(30)->post('https://openrouter.ai/api/v1/embeddings', [
                'model' => $this->embeddingModel,
                'input' => $text
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['data'][0]['embedding'])) {
                    $embedding = $data['data'][0]['embedding'];
                    
                    Log::info('Embedding generated successfully', ['dimension' => count($embedding)]);
                    
                    // Cache for 24 hours
                    Cache::put($cacheKey, $embedding, 86400);
                    
                    return $embedding;
                }
                
                Log::error('Embedding response missing data', ['response' => $data]);
                return null;
            }
            
            Log::error('Embedding generation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'model' => $this->embeddingModel
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Embedding generation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Generate embeddings for multiple texts (batch)
     */
    public function generateBatchEmbeddings(array $texts): array
    {
        $embeddings = [];
        
        foreach ($texts as $text) {
            $embedding = $this->generateEmbedding($text);
            if ($embedding) {
                $embeddings[] = $embedding;
            } else {
                // Return null embedding if generation fails
                $embeddings[] = null;
            }
        }
        
        return $embeddings;
    }
    
    /**
     * Calculate cosine similarity between two embeddings
     */
    public function cosineSimilarity(array $embedding1, array $embedding2): float
    {
        if (count($embedding1) !== count($embedding2)) {
            return 0.0;
        }
        
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;
        
        for ($i = 0; $i < count($embedding1); $i++) {
            $dotProduct += $embedding1[$i] * $embedding2[$i];
            $magnitude1 += $embedding1[$i] * $embedding1[$i];
            $magnitude2 += $embedding2[$i] * $embedding2[$i];
        }
        
        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);
        
        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0.0;
        }
        
        return $dotProduct / ($magnitude1 * $magnitude2);
    }
    
    /**
     * Truncate text to approximate token limit
     */
    private function truncateText(string $text, int $maxTokens): string
    {
        // Rough approximation: 1 token ≈ 4 characters
        $maxChars = $maxTokens * 4;
        
        if (strlen($text) <= $maxChars) {
            return $text;
        }
        
        // Truncate and add ellipsis
        return substr($text, 0, $maxChars - 3) . '...';
    }
    
    /**
     * Normalize embedding vector
     */
    public function normalizeEmbedding(array $embedding): array
    {
        $magnitude = 0;
        
        foreach ($embedding as $value) {
            $magnitude += $value * $value;
        }
        
        $magnitude = sqrt($magnitude);
        
        if ($magnitude == 0) {
            return $embedding;
        }
        
        return array_map(function($value) use ($magnitude) {
            return $value / $magnitude;
        }, $embedding);
    }
}
