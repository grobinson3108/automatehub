<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\N8nApiService;
use Exception;

class CreateMasterclassWorkflow extends Command
{
    protected $signature = 'n8n:create-masterclass 
                            {name : Workflow name}
                            {--description= : Workflow description}';
    
    protected $description = 'Create a new workflow in the Personal/Masterclass folder';

    public function handle(N8nApiService $n8nService)
    {
        $name = $this->argument('name');
        $description = $this->option('description') ?? "Masterclass workflow created by AutomateHub";
        
        $this->info("🔄 Creating Masterclass workflow: {$name}");

        // Create workflow with Masterclass tags using the correct n8n structure
        $workflowData = [
            'name' => $name,
            'nodes' => [
                [
                    'parameters' => (object)[],
                    'id' => 'manual-trigger',
                    'name' => 'Manual Trigger',
                    'type' => 'n8n-nodes-base.manualTrigger',
                    'typeVersion' => 1,
                    'position' => [250, 300]
                ],
                [
                    'parameters' => [
                        'values' => [
                            'string' => [
                                [
                                    'name' => 'folder',
                                    'value' => 'Masterclass'
                                ],
                                [
                                    'name' => 'workspace',
                                    'value' => 'Personal'
                                ],
                                [
                                    'name' => 'description',
                                    'value' => $description
                                ]
                            ]
                        ]
                    ],
                    'id' => 'set-node',
                    'name' => 'Set Workflow Info',
                    'type' => 'n8n-nodes-base.set',
                    'typeVersion' => 1,
                    'position' => [450, 300]
                ]
            ],
            'connections' => [
                'Manual Trigger' => [
                    'main' => [
                        [
                            [
                                'node' => 'Set Workflow Info',
                                'type' => 'main',
                                'index' => 0
                            ]
                        ]
                    ]
                ]
            ],
            'settings' => [
                'executionOrder' => 'v1'
            ],
            'staticData' => null
        ];

        try {
            // First create the workflow
            $workflow = $n8nService->createWorkflow($workflowData);
            
            // Show creation success
            $this->info('✅ Workflow created: ' . $workflow['id']);
            
            // Then add tags - we need to ensure parameters remain as objects
            $nodes = $workflow['nodes'];
            foreach ($nodes as &$node) {
                if (empty($node['parameters'])) {
                    $node['parameters'] = (object)[];
                }
            }
            
            $updateData = [
                'name' => $workflow['name'],
                'nodes' => $nodes,
                'connections' => $workflow['connections'],
                'settings' => $workflow['settings'] ?? (object)['executionOrder' => 'v1'],
                'staticData' => $workflow['staticData'] ?? null,
                'tags' => [
                    'Personal:Masterclass',
                    'Personal',
                    'Masterclass',
                    'AutomateHub'
                ]
            ];
            
            try {
                $workflow = $n8nService->updateWorkflow($workflow['id'], $updateData);
                $this->info('✅ Tags added successfully');
            } catch (Exception $e) {
                $this->warn('⚠️ Could not add tags: ' . $e->getMessage());
                $this->info('You can manually tag the workflow in n8n with: Personal:Masterclass');
            }
            
            $this->info('✅ Masterclass workflow created successfully!');
            $this->line('');
            $this->info('📋 Workflow Details:');
            $this->line('ID: ' . $workflow['id']);
            $this->line('Name: ' . $workflow['name']);
            $this->line('Folder: Personal/Masterclass');
            $this->line('Tags: ' . implode(', ', $workflow['tags'] ?? []));
            $this->line('');
            $this->info('🌐 Edit workflow at: ' . config('n8n.url') . '/workflow/' . $workflow['id']);
            
            // Now let's list the workspace to show it's in the right folder
            $this->line('');
            $this->call('n8n:workspace', ['action' => 'list', '--workspace' => 'Personal']);
            
            return 0;

        } catch (Exception $e) {
            $this->error('❌ Failed to create Masterclass workflow');
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}