<?php
namespace App\Jobs\WatchTrend;

use App\Services\WatchTrend\AnalyzerService;
use App\Services\WatchTrend\CreditService;
use App\Models\WatchtrendWatch;
use App\Models\WatchtrendCollectedItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeItemsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 120;
    public int $timeout = 300;

    public function __construct(public int $watchId, public int $batchSize = 10) {}

    public function handle(AnalyzerService $analyzerService, CreditService $creditService): void
    {
        $watch = WatchtrendWatch::find($this->watchId);
        if (!$watch || $watch->status !== 'active') {
            return;
        }

        if (!$creditService->hasCredits($watch, $this->batchSize)) {
            Log::warning("WatchTrend: Insufficient credits for watch {$this->watchId}");
            return;
        }

        $result = $analyzerService->analyzeBatch($this->watchId, $this->batchSize);

        Log::info("WatchTrend analyze job done", [
            'watch_id' => $this->watchId,
            'analyzed' => $result['analyzed'],
            'errors'   => $result['errors'],
        ]);

        $remaining = WatchtrendCollectedItem::where('watch_id', $this->watchId)
            ->where('is_analyzed', false)
            ->count();

        if ($remaining > 0 && $result['analyzed'] > 0) {
            self::dispatch($this->watchId, $this->batchSize)->delay(now()->addSeconds(10));
        }
    }
}
