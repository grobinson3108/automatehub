<?php

namespace App\Jobs\WatchTrend;

use App\Models\WatchtrendSource;
use App\Services\WatchTrend\CollectorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CollectSourceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60; // seconds between retries
    public int $timeout = 120;

    public function __construct(public WatchtrendSource $source) {}

    public function handle(): void
    {
        $source = $this->source;

        try {
            $collector = CollectorService::getCollector($source->type);
            $rawItems  = $collector->collect($source);
            $newCount  = $collector->storeItems($source, $rawItems);

            // Update source stats
            $source->update([
                'last_collected_at'     => now(),
                'items_collected_total' => ($source->items_collected_total ?? 0) + $newCount,
                'error_message'         => null,
                'error_count'           => 0,
            ]);

            // Update watch last_collected_at
            $source->watch->update(['last_collected_at' => now()]);

            // Dispatch analysis job if new items found
            if ($newCount > 0) {
                AnalyzeItemsJob::dispatch($source->watch_id);
            }

            Log::info("WatchTrend collect: {$source->type} '{$source->name}' - {$newCount} new items (from " . count($rawItems) . " fetched)");

        } catch (\Exception $e) {
            $source->update([
                'error_message' => mb_substr($e->getMessage(), 0, 500),
                'error_count'   => ($source->error_count ?? 0) + 1,
            ]);

            // If too many errors, pause the source
            if ($source->error_count >= 10) {
                $source->update(['status' => 'error']);
            }

            Log::error("WatchTrend collect error: {$source->type} '{$source->name}'", [
                'error'       => $e->getMessage(),
                'error_count' => $source->error_count,
            ]);

            throw $e; // Re-throw for retry
        }
    }
}
