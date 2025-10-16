<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\N8nApiService;
use Exception;

class ManageN8nWorkspace extends Command
{
    protected $signature = 'n8n:workspace 
                            {action : Action to perform (list|create-folder|move|tag)}
                            {--workspace=Personal : Workspace name}
                            {--folder= : Folder name for create-folder action}
                            {--workflow= : Workflow ID for move/tag actions}
                            {--tags=* : Additional tags to add}';
    
    protected $description = 'Manage n8n workspace organization using tags';

    private array $workspaceStructure = [
        'Personal' => [
            'folders' => ['Freemium', 'Premium', 'Claude', 'Masterclass'],
            'tag_prefix' => 'Personal'
        ],
        'Business' => [
            'folders' => ['Templates', 'Client Projects', 'Internal'],
            'tag_prefix' => 'Business'
        ]
    ];

    public function handle(N8nApiService $n8nService)
    {
        $action = $this->argument('action');
        $workspace = $this->option('workspace');

        try {
            switch ($action) {
                case 'list':
                    return $this->listWorkspaceStructure($n8nService, $workspace);
                
                case 'create-folder':
                    return $this->createFolder($n8nService, $workspace);
                
                case 'move':
                    return $this->moveWorkflowToFolder($n8nService, $workspace);
                
                case 'tag':
                    return $this->tagWorkflow($n8nService);
                
                default:
                    $this->error("Unknown action: {$action}");
                    $this->line('Available actions: list, create-folder, move, tag');
                    return 1;
            }
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    private function listWorkspaceStructure(N8nApiService $n8nService, string $workspace): int
    {
        $this->info("📁 Workspace Structure for: {$workspace}");
        $this->line('');

        // Get all workflows
        $workflows = $n8nService->getWorkflows();
        
        // Define workspace folders
        $folders = $this->workspaceStructure[$workspace]['folders'] ?? [];
        $tagPrefix = $this->workspaceStructure[$workspace]['tag_prefix'] ?? $workspace;

        // Display folder structure
        $this->line("└── {$workspace}/");
        
        foreach ($folders as $folder) {
            $folderTag = "{$tagPrefix}:{$folder}";
            $count = $this->countWorkflowsWithTag($workflows, $folderTag);
            
            $this->line("    ├── {$folder}/ ({$count} workflows)");
            
            // Show workflows in this folder
            if ($count > 0) {
                $folderWorkflows = $this->getWorkflowsWithTag($workflows, $folderTag);
                foreach ($folderWorkflows as $idx => $workflow) {
                    $isLast = ($idx === count($folderWorkflows) - 1);
                    $prefix = $isLast ? "    │   └── " : "    │   ├── ";
                    $active = $workflow['active'] ? '🟢' : '🔴';
                    $this->line("{$prefix}{$active} {$workflow['name']} (ID: {$workflow['id']})");
                }
            }
        }

        // Show unorganized workflows
        $unorganized = $this->getUnorganizedWorkflows($workflows, $workspace);
        if (count($unorganized) > 0) {
            $this->line("    └── 📂 Unorganized ({count($unorganized)} workflows)");
            foreach ($unorganized as $workflow) {
                $active = $workflow['active'] ? '🟢' : '🔴';
                $this->line("        ├── {$active} {$workflow['name']} (ID: {$workflow['id']})");
            }
        }

        return 0;
    }

    private function createFolder(N8nApiService $n8nService, string $workspace): int
    {
        $folder = $this->option('folder');
        
        if (!$folder) {
            $this->error('Please specify a folder name with --folder=FolderName');
            return 1;
        }

        // Add folder to workspace structure
        if (!isset($this->workspaceStructure[$workspace])) {
            $this->workspaceStructure[$workspace] = [
                'folders' => [],
                'tag_prefix' => $workspace
            ];
        }

        if (in_array($folder, $this->workspaceStructure[$workspace]['folders'])) {
            $this->warn("Folder '{$folder}' already exists in {$workspace} workspace");
            return 0;
        }

        // For demonstration, we'll create a sample workflow in the new folder
        $tagPrefix = $this->workspaceStructure[$workspace]['tag_prefix'];
        $folderTag = "{$tagPrefix}:{$folder}";

        $this->info("✅ Creating folder '{$folder}' in {$workspace} workspace");
        $this->info("📌 Workflows in this folder will be tagged with: {$folderTag}");
        
        // Create a sample workflow to demonstrate the folder
        $sampleWorkflow = [
            'name' => "{$folder} - Welcome Workflow",
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
                                    'name' => 'folder',
                                    'value' => $folder
                                ],
                                [
                                    'name' => 'workspace',
                                    'value' => $workspace
                                ]
                            ]
                        ]
                    ],
                    'id' => 'set-node',
                    'name' => 'Set Folder Info',
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
                                'node' => 'Set Folder Info',
                                'type' => 'main',
                                'index' => 0
                            ]
                        ]
                    ]
                ]
            ],
            'active' => false,
            'tags' => [$folderTag, 'AutomateHub', $workspace]
        ];

        try {
            $workflow = $n8nService->createWorkflow($sampleWorkflow);
            $this->info("✅ Created sample workflow in {$folder} folder");
            $this->info("🌐 View at: " . config('n8n.url') . '/workflow/' . $workflow['id']);
        } catch (Exception $e) {
            $this->warn("Could not create sample workflow: " . $e->getMessage());
        }

        return 0;
    }

    private function moveWorkflowToFolder(N8nApiService $n8nService, string $workspace): int
    {
        $workflowId = $this->option('workflow');
        $folder = $this->option('folder');

        if (!$workflowId || !$folder) {
            $this->error('Please specify both --workflow=ID and --folder=FolderName');
            return 1;
        }

        try {
            // Get current workflow
            $workflow = $n8nService->getWorkflow($workflowId);
            
            // Remove old workspace tags and add new ones
            $tagPrefix = $this->workspaceStructure[$workspace]['tag_prefix'] ?? $workspace;
            $newFolderTag = "{$tagPrefix}:{$folder}";
            
            // Filter out old workspace tags
            $currentTags = $workflow['tags'] ?? [];
            $filteredTags = array_filter($currentTags, function($tag) {
                // Remove tags that look like workspace:folder
                return !preg_match('/^(Personal|Business|Custom):/i', $tag);
            });
            
            // Add new tags
            $newTags = array_unique(array_merge($filteredTags, [$newFolderTag, $workspace]));
            
            // Update workflow - include all required fields (excluding read-only fields)
            $updateData = [
                'name' => $workflow['name'],
                'nodes' => $workflow['nodes'],
                'connections' => $workflow['connections'],
                'tags' => array_values($newTags),
                'settings' => $workflow['settings'] ?? (object)[],
                'staticData' => $workflow['staticData'] ?? null
            ];
            
            $n8nService->updateWorkflow($workflowId, $updateData);
            
            $this->info("✅ Moved workflow '{$workflow['name']}' to {$workspace}/{$folder}");
            $this->info("📌 Tags: " . implode(', ', $newTags));
            
            return 0;
        } catch (Exception $e) {
            $this->error("Failed to move workflow: " . $e->getMessage());
            return 1;
        }
    }

    private function tagWorkflow(N8nApiService $n8nService): int
    {
        $workflowId = $this->option('workflow');
        $additionalTags = $this->option('tags');

        if (!$workflowId) {
            $this->error('Please specify --workflow=ID');
            return 1;
        }

        try {
            $workflow = $n8nService->getWorkflow($workflowId);
            $currentTags = $workflow['tags'] ?? [];
            $newTags = array_unique(array_merge($currentTags, $additionalTags));
            
            $updateData = [
                'name' => $workflow['name'],
                'nodes' => $workflow['nodes'],
                'connections' => $workflow['connections'],
                'tags' => array_values($newTags),
                'settings' => $workflow['settings'] ?? (object)[],
                'staticData' => $workflow['staticData'] ?? null
            ];
            
            $n8nService->updateWorkflow($workflowId, $updateData);
            
            $this->info("✅ Updated tags for workflow '{$workflow['name']}'");
            $this->info("📌 Tags: " . implode(', ', $newTags));
            
            return 0;
        } catch (Exception $e) {
            $this->error("Failed to tag workflow: " . $e->getMessage());
            return 1;
        }
    }

    private function countWorkflowsWithTag(array $workflows, string $tag): int
    {
        return count(array_filter($workflows, function($workflow) use ($tag) {
            return in_array($tag, $workflow['tags'] ?? []);
        }));
    }

    private function getWorkflowsWithTag(array $workflows, string $tag): array
    {
        return array_filter($workflows, function($workflow) use ($tag) {
            return in_array($tag, $workflow['tags'] ?? []);
        });
    }

    private function getUnorganizedWorkflows(array $workflows, string $workspace): array
    {
        $tagPrefix = $this->workspaceStructure[$workspace]['tag_prefix'] ?? $workspace;
        
        return array_filter($workflows, function($workflow) use ($tagPrefix, $workspace) {
            $tags = $workflow['tags'] ?? [];
            
            // Check if workflow has any folder tag for this workspace
            foreach ($tags as $tag) {
                if (str_starts_with($tag, "{$tagPrefix}:")) {
                    return false;
                }
            }
            
            // Check if workflow belongs to this workspace
            return in_array($tagPrefix, $tags) || in_array($workspace, $tags);
        });
    }
}