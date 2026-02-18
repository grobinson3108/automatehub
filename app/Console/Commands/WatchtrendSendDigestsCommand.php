<?php

namespace App\Console\Commands;

use App\Services\WatchTrend\DigestService;
use Illuminate\Console\Command;

class WatchtrendSendDigestsCommand extends Command
{
    protected $signature = 'watchtrend:send-digests';

    protected $description = 'Send digest emails for all WatchTrend watches that are due';

    public function handle(DigestService $digestService): int
    {
        $this->info('Checking for due digests...');

        $sent = $digestService->sendAllDueDigests();

        $this->info("Done. {$sent} digest(s) sent.");

        return Command::SUCCESS;
    }
}
