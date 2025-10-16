<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\N8nApiService;
use App\Models\Workflow;
use Illuminate\Support\Facades\Log;

class SyncN8nWorkflows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'n8n:sync 
                            {--force : Force sync even if workflows exist}
                            {--clean : Remove workflows not in n8n}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize workflows between n8n and Laravel database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting n8n workflow synchronization...');
        
        try {
            $n8nService = app(N8nApiService::class);
            
            // Test connection first
            if (!$n8nService->testConnection()) {
                $this->error('Cannot connect to n8n. Please check your configuration.');
                return Command::FAILURE;
            }
            
            // Get all workflows from n8n
            $n8nWorkflows = $n8nService->getWorkflows();
            
            if (empty($n8nWorkflows)) {
                $this->warn('No workflows found in n8n.');
                return Command::SUCCESS;
            }
            
            $this->info('Found ' . count($n8nWorkflows) . ' workflows in n8n.');
            
            $synced = 0;
            $updated = 0;
            $errors = 0;
            
            // Sync each workflow
            foreach ($n8nWorkflows as $n8nWorkflow) {
                try {
                    $workflow = Workflow::where('n8n_id', $n8nWorkflow['id'])->first();
                    
                    if ($workflow) {
                        // Update existing workflow
                        $workflow->update([
                            'name' => $n8nWorkflow['name'] ?? 'Unnamed Workflow',
                            'description' => $n8nWorkflow['description'] ?? null,
                            'active' => $n8nWorkflow['active'] ?? false,
                            'nodes' => $n8nWorkflow['nodes'] ?? [],
                            'connections' => $n8nWorkflow['connections'] ?? [],
                            'tags' => $n8nWorkflow['tags'] ?? [],
                            'metadata' => [
                                'createdAt' => $n8nWorkflow['createdAt'] ?? null,
                                'updatedAt' => $n8nWorkflow['updatedAt'] ?? null,
                                'versionId' => $n8nWorkflow['versionId'] ?? null,
                            ],
                            'last_synced_at' => now(),
                        ]);
                        $updated++;
                        $this->line('Updated: ' . $workflow->name);
                    } else {
                        // Create new workflow
                        $workflow = Workflow::create([
                            'n8n_id' => $n8nWorkflow['id'],
                            'user_id' => 1, // Default to admin user
                            'name' => $n8nWorkflow['name'] ?? 'Unnamed Workflow',
                            'description' => $n8nWorkflow['description'] ?? null,
                            'active' => $n8nWorkflow['active'] ?? false,
                            'nodes' => $n8nWorkflow['nodes'] ?? [],
                            'connections' => $n8nWorkflow['connections'] ?? [],
                            'tags' => $n8nWorkflow['tags'] ?? [],
                            'category_id' => null,
                            'difficulty_level' => 'intermediate',
                            'is_template' => false,
                            'metadata' => [
                                'createdAt' => $n8nWorkflow['createdAt'] ?? null,
                                'updatedAt' => $n8nWorkflow['updatedAt'] ?? null,
                                'versionId' => $n8nWorkflow['versionId'] ?? null,
                            ],
                            'last_synced_at' => now(),
                        ]);
                        $synced++;
                        $this->line('Synced: ' . $workflow->name);
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $this->error('Error syncing workflow ' . ($n8nWorkflow['name'] ?? 'Unknown') . ': ' . $e->getMessage());
                    Log::error('N8N Sync Error', [
                        'workflow' => $n8nWorkflow,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Clean up workflows not in n8n if requested
            if ($this->option('clean')) {
                $n8nIds = array_column($n8nWorkflows, 'id');
                $deleted = Workflow::whereNotIn('n8n_id', $n8nIds)->delete();
                $this->info('Removed ' . $deleted . ' workflows not found in n8n.');
            }
            
            $this->info('Synchronization complete!');
            $this->info('New: ' . $synced . ' | Updated: ' . $updated . ' | Errors: ' . $errors);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Synchronization failed: ' . $e->getMessage());
            Log::error('N8N Sync Failed', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }
}