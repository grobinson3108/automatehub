<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SyncN8nWorkflowsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting scheduled n8n workflows synchronization');
        
        try {
            // Run the sync command
            Artisan::call('n8n:sync', ['--clean' => true]);
            
            $output = Artisan::output();
            Log::info('N8n sync completed: ' . $output);
            
        } catch (\Exception $e) {
            Log::error('N8n sync job failed: ' . $e->getMessage());
            throw $e; // Re-throw to mark job as failed
        }
    }
}