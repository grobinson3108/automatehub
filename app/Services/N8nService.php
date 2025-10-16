<?php

namespace App\Services;

use App\Models\Workflow;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class N8nService
{
    private $apiUrl;
    private $apiKey;
    
    public function __construct()
    {
        $this->apiUrl = env('N8N_API_URL', 'https://n8n.automatehub.fr/api/v1');
        $this->apiKey = env('N8N_API_KEY');
    }
    
    /**
     * Import workflow to n8n for testing
     */
    public function importWorkflow($jsonData, $name = null)
    {
        try {
            $workflowData = is_string($jsonData) ? json_decode($jsonData, true) : $jsonData;
            
            // Add metadata
            $workflowData['name'] = $name ?? 'Test Workflow - ' . now()->format('Y-m-d H:i');
            $workflowData['active'] = false; // Always inactive for testing
            
            $response = Http::withHeaders([
                'X-N8N-API-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->post($this->apiUrl . '/workflows', $workflowData);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'workflow_id' => $response->json('id'),
                    'workflow_url' => env('N8N_URL') . '/workflow/' . $response->json('id'),
                ];
            }
            
            return [
                'success' => false,
                'error' => $response->json('message') ?? 'Import failed',
            ];
            
        } catch (\Exception $e) {
            Log::error('N8n import error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Get workflow from n8n
     */
    public function getWorkflow($workflowId)
    {
        try {
            $response = Http::withHeaders([
                'X-N8N-API-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->apiUrl . '/workflows/' . $workflowId);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('N8n get workflow error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Sync workflows from n8n to database
     */
    public function syncWorkflows()
    {
        try {
            $response = Http::withHeaders([
                'X-N8N-API-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->apiUrl . '/workflows');
            
            if (!$response->successful()) {
                return ['error' => 'Failed to fetch workflows'];
            }
            
            $workflows = $response->json('data');
            $synced = 0;
            
            foreach ($workflows as $n8nWorkflow) {
                // Skip test workflows
                if (Str::contains(strtolower($n8nWorkflow['name']), ['test', 'demo', 'claude'])) {
                    continue;
                }
                
                // Detect category from name
                $category = $this->detectCategory($n8nWorkflow['name']);
                $isPremium = $this->detectPremium($n8nWorkflow['name']);
                
                Workflow::updateOrCreate(
                    ['n8n_id' => $n8nWorkflow['id']],
                    [
                        'name' => $n8nWorkflow['name'],
                        'description' => $this->generateDescription($n8nWorkflow),
                        'category' => $category,
                        'is_premium' => $isPremium,
                        'active' => $n8nWorkflow['active'] ?? false,
                        'json_data' => $n8nWorkflow,
                        'node_count' => count($n8nWorkflow['nodes'] ?? []),
                        'tags' => $this->extractTags($n8nWorkflow),
                        'published' => false, // Admin must publish manually
                    ]
                );
                
                $synced++;
            }
            
            return [
                'success' => true,
                'synced' => $synced,
                'total' => count($workflows),
            ];
            
        } catch (\Exception $e) {
            Log::error('N8n sync error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Detect category from workflow name
     */
    private function detectCategory($name)
    {
        $categories = [
            'M1' => 'fondamentaux',
            'M2' => 'integrations',
            'M3' => 'business',
            'M4' => 'avance',
            'M5' => 'premium',
            'MasterClass' => 'masterclass',
        ];
        
        foreach ($categories as $key => $category) {
            if (Str::contains($name, $key)) {
                return $category;
            }
        }
        
        return 'general';
    }
    
    /**
     * Detect if workflow is premium
     */
    private function detectPremium($name)
    {
        $premiumKeywords = ['M4', 'M5', 'Premium', 'Pro', 'Enterprise', 'Advanced'];
        
        foreach ($premiumKeywords as $keyword) {
            if (Str::contains($name, $keyword)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generate description from workflow data
     */
    private function generateDescription($workflow)
    {
        $nodeTypes = collect($workflow['nodes'] ?? [])
            ->pluck('type')
            ->unique()
            ->filter(function($type) {
                return !Str::startsWith($type, 'n8n-nodes-base.');
            })
            ->values();
        
        $description = "Workflow automatisé utilisant ";
        
        if ($nodeTypes->count() > 0) {
            $description .= $nodeTypes->join(', ', ' et ') . '.';
        } else {
            $description .= "plusieurs nodes n8n.";
        }
        
        return $description;
    }
    
    /**
     * Extract tags from workflow
     */
    private function extractTags($workflow)
    {
        $tags = [];
        
        // Extract from node types
        $nodeTypes = collect($workflow['nodes'] ?? [])->pluck('type')->unique();
        
        foreach ($nodeTypes as $type) {
            if (Str::contains($type, 'webhook')) $tags[] = 'webhook';
            if (Str::contains($type, 'http')) $tags[] = 'api';
            if (Str::contains($type, 'database')) $tags[] = 'database';
            if (Str::contains($type, 'email')) $tags[] = 'email';
            if (Str::contains($type, 'slack')) $tags[] = 'slack';
            if (Str::contains($type, 'google')) $tags[] = 'google';
            if (Str::contains($type, 'openai')) $tags[] = 'ai';
        }
        
        return array_unique($tags);
    }
}