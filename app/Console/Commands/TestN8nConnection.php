<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\N8nApiService;
use Exception;

class TestN8nConnection extends Command
{
    protected $signature = 'n8n:test-connection';
    protected $description = 'Test connection to n8n API';

    public function handle(N8nApiService $n8nService)
    {
        $this->info('Testing connection to n8n...');
        $this->info('n8n URL: ' . config('n8n.url'));

        try {
            // Test basic connection
            $this->line('');
            $this->info('🔄 Testing basic connection...');
            
            if ($n8nService->testConnection()) {
                $this->info('✅ Connection successful!');
            } else {
                $this->error('❌ Connection failed!');
                return 1;
            }

            // Try to get workflows
            $this->line('');
            $this->info('🔄 Fetching workflows...');
            
            $workflows = $n8nService->getWorkflows();
            $count = count($workflows);
            
            $this->info("✅ Found {$count} workflow(s)");
            
            if ($count > 0) {
                $this->line('');
                $this->info('📋 Available workflows:');
                
                foreach (array_slice($workflows, 0, 5) as $workflow) {
                    $active = $workflow['active'] ? '🟢' : '🔴';
                    $this->line("  {$active} {$workflow['name']} (ID: {$workflow['id']})");
                }
                
                if ($count > 5) {
                    $this->line("  ... and " . ($count - 5) . " more");
                }
            }

            $this->line('');
            $this->info('🎉 n8n integration is working correctly!');
            
            return 0;

        } catch (Exception $e) {
            $this->line('');
            $this->error('❌ Connection test failed!');
            $this->error('Error: ' . $e->getMessage());
            
            $this->line('');
            $this->warn('💡 Troubleshooting tips:');
            $this->line('1. Check if n8n is running: systemctl status n8n');
            $this->line('2. Verify n8n URL in .env file: N8N_URL=' . config('n8n.url'));
            $this->line('3. Check if API key is configured: N8N_API_KEY=...');
            $this->line('4. Test direct access: curl ' . config('n8n.url') . '/api/v1/workflows');
            
            return 1;
        }
    }
}