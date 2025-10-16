<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SetupN8nApi extends Command
{
    protected $signature = 'n8n:setup-api {--check-only : Only check current configuration}';
    protected $description = 'Setup n8n API integration';

    public function handle()
    {
        $this->info('🔧 n8n API Setup');
        $this->line('');

        $checkOnly = $this->option('check-only');

        // Check current configuration
        $this->info('📋 Current Configuration:');
        $this->line('N8N_URL: ' . (config('n8n.url') ?: 'Not set'));
        $this->line('N8N_API_KEY: ' . (config('n8n.api_key') ? 'Set (****)' : 'Not set'));
        $this->line('N8N_TIMEOUT: ' . config('n8n.timeout', 30));
        $this->line('N8N_VERIFY_SSL: ' . (config('n8n.verify_ssl', true) ? 'true' : 'false'));
        $this->line('');

        if ($checkOnly) {
            return 0;
        }

        // Check if n8n is accessible
        $this->info('🔄 Checking n8n accessibility...');
        
        try {
            $response = Http::timeout(10)->withoutVerifying()->get(config('n8n.url'));
            
            if ($response->successful()) {
                $this->info('✅ n8n is accessible at ' . config('n8n.url'));
            } else {
                $this->warn('⚠️  n8n responded with status: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('❌ Cannot reach n8n at ' . config('n8n.url'));
            $this->error('Error: ' . $e->getMessage());
            $this->line('');
            $this->warn('💡 Please verify:');
            $this->line('1. n8n service is running: systemctl status n8n');
            $this->line('2. n8n is accessible at: ' . config('n8n.url'));
            $this->line('3. SSL certificate is valid');
            return 1;
        }

        $this->line('');
        
        if (!config('n8n.api_key')) {
            $this->warn('⚠️  No API key configured');
            $this->line('');
            $this->info('📝 To set up the API key:');
            $this->line('1. Open n8n at: ' . config('n8n.url'));
            $this->line('2. Go to Settings > API Keys');
            $this->line('3. Create a new API key');
            $this->line('4. Add it to your .env file:');
            $this->line('   N8N_API_KEY=your_api_key_here');
            $this->line('');
            
            if ($this->confirm('Do you want to add the API key now?')) {
                $apiKey = $this->secret('Enter your n8n API key');
                
                if ($apiKey) {
                    $this->updateEnvFile('N8N_API_KEY', $apiKey);
                    $this->info('✅ API key added to .env file');
                    $this->warn('🔄 Please run the application cache clear: php artisan config:clear');
                }
            }
        } else {
            $this->info('✅ API key is configured');
        }

        $this->line('');
        $this->info('🎉 Setup completed!');
        $this->line('');
        $this->info('📝 Next steps:');
        $this->line('1. Clear config cache: php artisan config:clear');
        $this->line('2. Test connection: php artisan n8n:test-connection');
        
        return 0;
    }

    private function updateEnvFile($key, $value)
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            $this->error('❌ .env file not found');
            return;
        }

        $content = file_get_contents($envFile);
        
        // Check if key already exists
        if (preg_match("/^{$key}=/m", $content)) {
            // Update existing key
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            // Add new key
            $content .= "\n{$key}={$value}\n";
        }
        
        file_put_contents($envFile, $content);
    }
}