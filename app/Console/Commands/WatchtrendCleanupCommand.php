<?php

namespace App\Console\Commands;

use App\Models\WatchtrendAnalysis;
use App\Models\WatchtrendCollectedItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WatchtrendCleanupCommand extends Command
{
    protected $signature = 'watchtrend:cleanup
        {--days=90 : Delete items older than this many days}';

    protected $description = 'Clean up old WatchTrend collected items and orphaned analyses';

    public function handle(): int
    {
        $days      = (int) $this->option('days');
        $threshold = now()->subDays($days);

        $unreadUnanalyzed = WatchtrendCollectedItem::where('is_read', false)
            ->where('is_analyzed', false)
            ->where('created_at', '<', $threshold)
            ->count();

        WatchtrendCollectedItem::where('is_read', false)
            ->where('is_analyzed', false)
            ->where('created_at', '<', $threshold)
            ->delete();

        $lowScoreAnalyzed = WatchtrendCollectedItem::where('is_analyzed', true)
            ->where('created_at', '<', $threshold)
            ->whereHas('analysis', fn($q) => $q->where('relevance_score', '<', 20))
            ->count();

        WatchtrendCollectedItem::where('is_analyzed', true)
            ->where('created_at', '<', $threshold)
            ->whereHas('analysis', fn($q) => $q->where('relevance_score', '<', 20))
            ->delete();

        $orphanedAnalyses = WatchtrendAnalysis::whereDoesntHave('collectedItem')->count();

        WatchtrendAnalysis::whereDoesntHave('collectedItem')->delete();

        $this->info("WatchTrend cleanup completed (threshold: {$days} days):");
        $this->info("  - Unread/unanalyzed items deleted: {$unreadUnanalyzed}");
        $this->info("  - Low-score analyzed items deleted: {$lowScoreAnalyzed}");
        $this->info("  - Orphaned analyses deleted: {$orphanedAnalyses}");

        Log::info("WatchTrend cleanup", [
            'days'                   => $days,
            'unread_unanalyzed'      => $unreadUnanalyzed,
            'low_score_analyzed'     => $lowScoreAnalyzed,
            'orphaned_analyses'      => $orphanedAnalyses,
        ]);

        return Command::SUCCESS;
    }
}
