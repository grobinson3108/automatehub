<?php

namespace App\Console\Commands;

use App\Services\WatchTrend\CollectorService;
use Illuminate\Console\Command;

class WatchtrendCollectCommand extends Command
{
    protected $signature = 'watchtrend:collect
        {--watch= : Collect a specific watch ID}
        {--force : Force collection even if not due}';

    protected $description = 'Collect data from all due WatchTrend sources';

    public function handle(CollectorService $service): int
    {
        $watchId = $this->option('watch');
        $force   = $this->option('force');

        if ($watchId) {
            $watch = \App\Models\WatchtrendWatch::findOrFail($watchId);
            $count = $service->collectWatch($watch);
            $this->info("Dispatched {$count} collection jobs for watch '{$watch->name}'");
        } else {
            $result = $service->collectDueWatches($force);
            $this->info("Dispatched {$result['jobs_dispatched']} collection jobs for {$result['watches_count']} watches");
        }

        return Command::SUCCESS;
    }
}
