<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\N8nApiService;
use Exception;

class CreateSampleWorkflow extends Command
{
    protected $signature = 'n8n:create-sample {--name=Sample Workflow : Workflow name}';
    protected $description = 'Create a sample workflow in n8n for testing';

    public function handle(N8nApiService $n8nService)
    {
        $workflowName = $this->option('name');
        
        $this->info("🔄 Creating sample workflow: {$workflowName}");

        // Simple workflow with a manual trigger and a Set node
        $workflowData = [
            'name' => $workflowName,
            'nodes' => [
                [
                    'parameters' => [],
                    'id' => 'manual-trigger',
                    'name' => 'Manual Trigger',
                    'type' => 'n8n-nodes-base.manualTrigger',
                    'typeVersion' => 1,
                    'position' => [100, 200]
                ],
                [
                    'parameters' => [
                        'values' => [
                            'string' => [
                                [
                                    'name' => 'message',
                                    'value' => 'Hello from AutomateHub!'
                                ]
                            ]
                        ]
                    ],
                    'id' => 'set-node',
                    'name' => 'Set Message',
                    'type' => 'n8n-nodes-base.set',
                    'typeVersion' => 1,
                    'position' => [300, 200]
                ]
            ],
            'connections' => [
                'Manual Trigger' => [
                    'main' => [
                        [
                            [
                                'node' => 'Set Message',
                                'type' => 'main',
                                'index' => 0
                            ]
                        ]
                    ]
                ]
            ],
            'active' => false,
            'tags' => ['AutomateHub', 'Sample', 'Test']
        ];

        try {
            $workflow = $n8nService->createWorkflow($workflowData);
            
            $this->info('✅ Sample workflow created successfully!');
            $this->line('');
            $this->info('📋 Workflow Details:');
            $this->line('ID: ' . $workflow['id']);
            $this->line('Name: ' . $workflow['name']);
            $this->line('Active: ' . ($workflow['active'] ? 'Yes' : 'No'));
            $this->line('');
            $this->info('🌐 You can view it at: ' . config('n8n.url') . '/workflow/' . $workflow['id']);
            
            return 0;

        } catch (Exception $e) {
            $this->error('❌ Failed to create sample workflow');
            $this->error('Error: ' . $e->getMessage());
            
            if (str_contains($e->getMessage(), 'authentication') || str_contains($e->getMessage(), 'unauthorized')) {
                $this->line('');
                $this->warn('💡 This might be an authentication issue:');
                $this->line('1. Make sure your API key is correct');
                $this->line('2. Check that the API is enabled in n8n');
                $this->line('3. Run: php artisan n8n:setup-api');
            }
            
            return 1;
        }
    }
}