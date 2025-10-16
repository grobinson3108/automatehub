<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailMarketingService;
use App\Models\Workflow;

class SendEarlyAccessEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:early-access {workflow_id : The ID of the workflow}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send early access email to premium members for a new workflow';

    /**
     * Execute the console command.
     */
    public function handle(EmailMarketingService $emailService)
    {
        $workflowId = $this->argument('workflow_id');
        $workflow = Workflow::find($workflowId);
        
        if (!$workflow) {
            $this->error('Workflow not found with ID: ' . $workflowId);
            return 1;
        }
        
        $this->info('📧 Sending early access email for: ' . $workflow->name);
        
        $count = $emailService->sendEarlyAccessEmail($workflow);
        
        $this->info('✅ Early access email sent to ' . $count . ' premium members!');
        
        return 0;
    }
}