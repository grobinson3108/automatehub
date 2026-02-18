<?php

namespace App\Jobs\WatchTrend;

use App\Models\WatchtrendWatch;
use App\Services\WatchTrend\DigestService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDigestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 300;

    public function __construct(public readonly WatchtrendWatch $watch) {}

    public function handle(DigestService $digestService): void
    {
        Log::info("WatchTrend SendDigestJob: sending digest for watch {$this->watch->id}");
        $digestService->sendDigest($this->watch);
    }
}
