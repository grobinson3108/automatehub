<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class N8nApiService
{
    private string $baseUrl;
    private ?string $apiKey;
    private int $timeout;
    private bool $verifySSL;
    private int $maxRetries;
    private int $retryDelay;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('n8n.url'), '/');
        $this->apiKey = config('n8n.api_key');
        $this->timeout = config('n8n.timeout', 30);
        $this->verifySSL = config('n8n.verify_ssl', true);
        $this->maxRetries = config('n8n.max_retries', 3);
        $this->retryDelay = config('n8n.retry_delay', 1000);
    }

    /**
     * Get all workflows
     */
    public function getWorkflows(): array
    {
        try {
            $response = $this->makeRequest('GET', '/api/v1/workflows');
            
            if ($response->successful()) {
                return $response->json('data', []);
            }
            
            throw new Exception('Failed to fetch workflows: ' . $response->body());
        } catch (Exception $e) {
            Log::error('N8N API Error - Get Workflows', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get workflow by ID
     */
    public function getWorkflow(string $workflowId): array
    {
        try {
            $response = $this->makeRequest('GET', "/api/v1/workflows/{$workflowId}");
            
            if ($response->successful()) {
                return $response->json();
            }
            
            throw new Exception("Failed to fetch workflow {$workflowId}: " . $response->body());
        } catch (Exception $e) {
            Log::error('N8N API Error - Get Workflow', [
                'workflow_id' => $workflowId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new workflow
     */
    public function createWorkflow(array $workflowData): array
    {
        try {
            $response = $this->makeRequest('POST', '/api/v1/workflows', $workflowData);
            
            if ($response->successful()) {
                Log::info('N8N Workflow Created', [
                    'workflow_name' => $workflowData['name'] ?? 'Unknown'
                ]);
                return $response->json();
            }
            
            throw new Exception('Failed to create workflow: ' . $response->body());
        } catch (Exception $e) {
            Log::error('N8N API Error - Create Workflow', [
                'workflow_data' => $workflowData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing workflow
     */
    public function updateWorkflow(string $workflowId, array $workflowData): array
    {
        try {
            $response = $this->makeRequest('PUT', "/api/v1/workflows/{$workflowId}", $workflowData);
            
            if ($response->successful()) {
                Log::info('N8N Workflow Updated', [
                    'workflow_id' => $workflowId,
                    'workflow_name' => $workflowData['name'] ?? 'Unknown'
                ]);
                return $response->json();
            }
            
            throw new Exception("Failed to update workflow {$workflowId}: " . $response->body());
        } catch (Exception $e) {
            Log::error('N8N API Error - Update Workflow', [
                'workflow_id' => $workflowId,
                'workflow_data' => $workflowData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a workflow
     */
    public function deleteWorkflow(string $workflowId): bool
    {
        try {
            $response = $this->makeRequest('DELETE', "/api/v1/workflows/{$workflowId}");
            
            if ($response->successful()) {
                Log::info('N8N Workflow Deleted', ['workflow_id' => $workflowId]);
                return true;
            }
            
            throw new Exception("Failed to delete workflow {$workflowId}: " . $response->body());
        } catch (Exception $e) {
            Log::error('N8N API Error - Delete Workflow', [
                'workflow_id' => $workflowId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Activate/Deactivate a workflow
     */
    public function toggleWorkflow(string $workflowId, bool $active): array
    {
        try {
            $endpoint = $active ? "activate" : "deactivate";
            $response = $this->makeRequest('POST', "/api/v1/workflows/{$workflowId}/{$endpoint}");
            
            if ($response->successful()) {
                Log::info('N8N Workflow Toggled', [
                    'workflow_id' => $workflowId,
                    'active' => $active
                ]);
                return $response->json();
            }
            
            throw new Exception("Failed to {$endpoint} workflow {$workflowId}: " . $response->body());
        } catch (Exception $e) {
            Log::error('N8N API Error - Toggle Workflow', [
                'workflow_id' => $workflowId,
                'active' => $active,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Execute a workflow
     */
    public function executeWorkflow(string $workflowId, array $data = []): array
    {
        try {
            $response = $this->makeRequest('POST', "/api/v1/workflows/{$workflowId}/execute", $data);
            
            if ($response->successful()) {
                Log::info('N8N Workflow Executed', [
                    'workflow_id' => $workflowId,
                    'data' => $data
                ]);
                return $response->json();
            }
            
            throw new Exception("Failed to execute workflow {$workflowId}: " . $response->body());
        } catch (Exception $e) {
            Log::error('N8N API Error - Execute Workflow', [
                'workflow_id' => $workflowId,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get workflow executions
     */
    public function getWorkflowExecutions(string $workflowId, int $limit = 20): array
    {
        try {
            $response = $this->makeRequest('GET', "/api/v1/executions", [
                'filter' => json_encode(['workflowId' => $workflowId]),
                'limit' => $limit
            ]);
            
            if ($response->successful()) {
                return $response->json('data', []);
            }
            
            throw new Exception("Failed to fetch executions for workflow {$workflowId}: " . $response->body());
        } catch (Exception $e) {
            Log::error('N8N API Error - Get Executions', [
                'workflow_id' => $workflowId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Export workflow to file
     */
    public function exportWorkflow(string $workflowId, string $filename = null): string
    {
        try {
            $workflow = $this->getWorkflow($workflowId);
            
            $exportPath = config('n8n.export_path');
            if (!file_exists($exportPath)) {
                mkdir($exportPath, 0755, true);
            }
            
            $filename = $filename ?: "workflow_{$workflowId}_" . date('Y-m-d_H-i-s') . '.json';
            $filePath = $exportPath . '/' . $filename;
            
            file_put_contents($filePath, json_encode($workflow, JSON_PRETTY_PRINT));
            
            Log::info('N8N Workflow Exported', [
                'workflow_id' => $workflowId,
                'file_path' => $filePath
            ]);
            
            return $filePath;
        } catch (Exception $e) {
            Log::error('N8N API Error - Export Workflow', [
                'workflow_id' => $workflowId,
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Import workflow from file
     */
    public function importWorkflow(string $filePath): array
    {
        try {
            if (!file_exists($filePath)) {
                throw new Exception("File not found: {$filePath}");
            }
            
            $workflowData = json_decode(file_get_contents($filePath), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON in file: " . json_last_error_msg());
            }
            
            // Remove ID to create new workflow
            unset($workflowData['id']);
            
            return $this->createWorkflow($workflowData);
        } catch (Exception $e) {
            Log::error('N8N API Error - Import Workflow', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Test n8n connection
     */
    public function testConnection(): bool
    {
        try {
            $response = $this->makeRequest('GET', '/api/v1/workflows', [], false);
            return $response->successful();
        } catch (Exception $e) {
            Log::error('N8N Connection Test Failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Make HTTP request to n8n API with retry logic
     */
    private function makeRequest(string $method, string $endpoint, array $data = [], bool $withRetry = true): Response
    {
        $url = $this->baseUrl . $endpoint;
        $attempts = 0;
        
        while ($attempts <= $this->maxRetries) {
            try {
                $http = Http::timeout($this->timeout);
                
                if (!$this->verifySSL) {
                    $http = $http->withoutVerifying();
                }
                
                if ($this->apiKey) {
                    $http = $http->withHeaders([
                        'X-N8N-API-KEY' => $this->apiKey,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ]);
                }
                
                $response = match(strtoupper($method)) {
                    'GET' => $http->get($url, $data),
                    'POST' => $http->post($url, $data),
                    'PUT' => $http->put($url, $data),
                    'DELETE' => $http->delete($url, $data),
                    default => throw new Exception("Unsupported HTTP method: {$method}")
                };
                
                if ($response->successful() || !$withRetry || $attempts >= $this->maxRetries) {
                    return $response;
                }
                
                $attempts++;
                if ($attempts <= $this->maxRetries) {
                    usleep($this->retryDelay * 1000); // Convert to microseconds
                }
                
            } catch (Exception $e) {
                if (!$withRetry || $attempts >= $this->maxRetries) {
                    throw $e;
                }
                
                $attempts++;
                if ($attempts <= $this->maxRetries) {
                    usleep($this->retryDelay * 1000);
                }
            }
        }
        
        throw new Exception("Max retries exceeded for {$method} {$url}");
    }
}