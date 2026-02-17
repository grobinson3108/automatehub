<?php

namespace App\Services\WatchTrend;

use App\Models\WatchtrendWatch;
use App\Jobs\WatchTrend\CollectSourceJob;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CollectorService
{
    /**
     * Find all watches that are due for collection and dispatch jobs.
     * Called by the artisan command watchtrend:collect.
     */
    public function collectDueWatches(bool $force = false): array
    {
        $watches = WatchtrendWatch::where('status', 'active')->get();

        if (!$force) {
            $watches = $watches->filter(fn ($watch) => $this->isDueForCollection($watch));
        }

        $dispatched = 0;
        foreach ($watches as $watch) {
            $sources = $watch->sources()->where('status', 'active')->get();
            foreach ($sources as $source) {
                CollectSourceJob::dispatch($source);
                $dispatched++;
            }
        }

        Log::info("WatchTrend: {$dispatched} source collection jobs dispatched for {$watches->count()} watches");

        return [
            'watches_count'   => $watches->count(),
            'jobs_dispatched' => $dispatched,
        ];
    }

    /**
     * Collect a single watch's sources immediately (for on-demand collection).
     */
    public function collectWatch(WatchtrendWatch $watch): int
    {
        $sources    = $watch->sources()->where('status', 'active')->get();
        $dispatched = 0;
        foreach ($sources as $source) {
            CollectSourceJob::dispatch($source);
            $dispatched++;
        }
        return $dispatched;
    }

    /**
     * Check if a watch is due for collection based on its frequency.
     */
    public function isDueForCollection(WatchtrendWatch $watch): bool
    {
        if (!$watch->last_collected_at) return true;

        $lastCollected = Carbon::parse($watch->last_collected_at);

        return match ($watch->collection_frequency) {
            'daily'     => $lastCollected->lt(now()->subDay()),
            'weekly'    => $lastCollected->lt(now()->subWeek()),
            'monthly'   => $lastCollected->lt(now()->subMonth()),
            'quarterly' => $lastCollected->lt(now()->subMonths(3)),
            default     => $lastCollected->lt(now()->subDay()),
        };
    }

    /**
     * Get the right collector for a source type.
     */
    public static function getCollector(string $type): Collectors\BaseCollector
    {
        return match ($type) {
            'youtube'    => new Collectors\YouTubeCollector(),
            'reddit'     => new Collectors\RedditCollector(),
            'rss'        => new Collectors\RssCollector(),
            'hackernews' => new Collectors\HackerNewsCollector(),
            'github'     => new Collectors\GitHubCollector(),
            'twitter'    => new Collectors\TwitterCollector(),
            default      => throw new \InvalidArgumentException("Unknown collector type: {$type}"),
        };
    }
}
