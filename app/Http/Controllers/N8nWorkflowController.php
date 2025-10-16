<?php

namespace App\Http\Controllers;

use App\Services\N8nApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class N8nWorkflowController extends Controller
{
    private N8nApiService $n8nService;

    public function __construct(N8nApiService $n8nService)
    {
        $this->n8nService = $n8nService;
    }

    /**
     * Get all workflows
     */
    public function index(): JsonResponse
    {
        try {
            $workflows = $this->n8nService->getWorkflows();
            
            return response()->json([
                'success' => true,
                'data' => $workflows,
                'message' => 'Workflows retrieved successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve workflows', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve workflows',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific workflow
     */
    public function show(string $workflowId): JsonResponse
    {
        try {
            $workflow = $this->n8nService->getWorkflow($workflowId);
            
            return response()->json([
                'success' => true,
                'data' => $workflow,
                'message' => 'Workflow retrieved successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve workflow', [
                'workflow_id' => $workflowId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve workflow',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new workflow
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'nodes' => 'required|array',
                'connections' => 'required|array',
                'active' => 'boolean',
                'tags' => 'array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $workflowData = [
                'name' => $request->input('name'),
                'nodes' => $request->input('nodes'),
                'connections' => $request->input('connections'),
                'active' => $request->input('active', false),
                'tags' => $request->input('tags', [])
            ];

            $workflow = $this->n8nService->createWorkflow($workflowData);
            
            return response()->json([
                'success' => true,
                'data' => $workflow,
                'message' => 'Workflow created successfully'
            ], 201);
        } catch (Exception $e) {
            Log::error('Failed to create workflow', [
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create workflow',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing workflow
     */
    public function update(Request $request, string $workflowId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'nodes' => 'sometimes|required|array',
                'connections' => 'sometimes|required|array',
                'active' => 'sometimes|boolean',
                'tags' => 'sometimes|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $workflowData = $request->only(['name', 'nodes', 'connections', 'active', 'tags']);
            $workflow = $this->n8nService->updateWorkflow($workflowId, $workflowData);
            
            return response()->json([
                'success' => true,
                'data' => $workflow,
                'message' => 'Workflow updated successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update workflow', [
                'workflow_id' => $workflowId,
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update workflow',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete workflow
     */
    public function destroy(string $workflowId): JsonResponse
    {
        try {
            $this->n8nService->deleteWorkflow($workflowId);
            
            return response()->json([
                'success' => true,
                'message' => 'Workflow deleted successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to delete workflow', [
                'workflow_id' => $workflowId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete workflow',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle workflow active state
     */
    public function toggle(Request $request, string $workflowId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'active' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $active = $request->input('active');
            $workflow = $this->n8nService->toggleWorkflow($workflowId, $active);
            
            return response()->json([
                'success' => true,
                'data' => $workflow,
                'message' => $active ? 'Workflow activated successfully' : 'Workflow deactivated successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to toggle workflow', [
                'workflow_id' => $workflowId,
                'active' => $request->input('active'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle workflow',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Execute workflow
     */
    public function execute(Request $request, string $workflowId): JsonResponse
    {
        try {
            $data = $request->input('data', []);
            $execution = $this->n8nService->executeWorkflow($workflowId, $data);
            
            return response()->json([
                'success' => true,
                'data' => $execution,
                'message' => 'Workflow execution started successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to execute workflow', [
                'workflow_id' => $workflowId,
                'data' => $request->input('data'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to execute workflow',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get workflow executions
     */
    public function executions(Request $request, string $workflowId): JsonResponse
    {
        try {
            $limit = $request->input('limit', 20);
            $executions = $this->n8nService->getWorkflowExecutions($workflowId, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $executions,
                'message' => 'Workflow executions retrieved successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to retrieve workflow executions', [
                'workflow_id' => $workflowId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve workflow executions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export workflow
     */
    public function export(Request $request, string $workflowId): JsonResponse
    {
        try {
            $filename = $request->input('filename');
            $filePath = $this->n8nService->exportWorkflow($workflowId, $filename);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'file_path' => $filePath,
                    'download_url' => url('storage/tutorials/n8n-exports/' . basename($filePath))
                ],
                'message' => 'Workflow exported successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to export workflow', [
                'workflow_id' => $workflowId,
                'filename' => $request->input('filename'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to export workflow',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import workflow
     */
    public function import(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:json|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $file = $request->file('file');
            $tempPath = $file->getPathname();
            
            $workflow = $this->n8nService->importWorkflow($tempPath);
            
            return response()->json([
                'success' => true,
                'data' => $workflow,
                'message' => 'Workflow imported successfully'
            ], 201);
        } catch (Exception $e) {
            Log::error('Failed to import workflow', [
                'file_name' => $request->file('file')?->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to import workflow',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test n8n connection
     */
    public function testConnection(): JsonResponse
    {
        try {
            $isConnected = $this->n8nService->testConnection();
            
            return response()->json([
                'success' => $isConnected,
                'message' => $isConnected ? 'Connection to n8n successful' : 'Failed to connect to n8n',
                'data' => [
                    'connected' => $isConnected,
                    'url' => config('n8n.url')
                ]
            ], $isConnected ? 200 : 500);
        } catch (Exception $e) {
            Log::error('n8n connection test failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}