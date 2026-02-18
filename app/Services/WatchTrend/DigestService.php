<?php

namespace App\Services\WatchTrend;

use App\Mail\WatchTrend\WatchtrendDigestMail;
use App\Models\WatchtrendAnalysis;
use App\Models\WatchtrendWatch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DigestService
{
    /**
     * Find all watches whose digest is due based on their digest_frequency and last_digest_sent_at.
     *
     * @return Collection<WatchtrendWatch>
     */
    public function getWatchesDueForDigest(): Collection
    {
        $watches = WatchtrendWatch::where('status', 'active')->get();

        return $watches->filter(fn($watch) => $this->isDueForDigest($watch));
    }

    /**
     * Build the digest data for a watch.
     * Retrieves analyses since last_digest_sent_at (or 7 days if null), sorted by score, limited to 10.
     *
     * @param WatchtrendWatch $watch
     * @return array ['watch' => [...], 'analyses' => [...], 'period_start' => Carbon, 'period_end' => Carbon]
     */
    public function buildDigest(WatchtrendWatch $watch): array
    {
        $periodStart = $watch->last_digest_sent_at
            ? Carbon::parse($watch->last_digest_sent_at)
            : now()->subDays(7);

        $periodEnd = now();

        $analyses = WatchtrendAnalysis::where('watch_id', $watch->id)
            ->where('created_at', '>=', $periodStart)
            ->orderByDesc('relevance_score')
            ->limit(10)
            ->with('collectedItem')
            ->get();

        return [
            'watch'        => $watch,
            'analyses'     => $analyses,
            'period_start' => $periodStart,
            'period_end'   => $periodEnd,
        ];
    }

    /**
     * Build and send the digest email for a watch, then update last_digest_sent_at.
     */
    public function sendDigest(WatchtrendWatch $watch): bool
    {
        $digestData = $this->buildDigest($watch);

        if ($digestData['analyses']->isEmpty()) {
            Log::info("WatchTrend digest: no analyses to send for watch {$watch->id}");
            $watch->update(['last_digest_sent_at' => now()]);
            return false;
        }

        $user = $watch->user;

        if (!$user || !$user->email) {
            Log::warning("WatchTrend digest: no user email for watch {$watch->id}");
            return false;
        }

        Mail::to($user->email)->send(new WatchtrendDigestMail($watch, $digestData['analyses']->toArray()));

        $watch->update(['last_digest_sent_at' => now()]);

        Log::info("WatchTrend digest sent", [
            'watch_id'  => $watch->id,
            'user_id'   => $user->id,
            'analyses'  => $digestData['analyses']->count(),
        ]);

        return true;
    }

    /**
     * Send digests for all due watches.
     *
     * @return int Number of digests sent
     */
    public function sendAllDueDigests(): int
    {
        $watches = $this->getWatchesDueForDigest();
        $sent    = 0;

        foreach ($watches as $watch) {
            if ($this->sendDigest($watch)) {
                $sent++;
            }
        }

        Log::info("WatchTrend: {$sent} digests sent out of {$watches->count()} due watches");

        return $sent;
    }

    /**
     * Determine if a watch is due for a digest based on its frequency setting.
     */
    private function isDueForDigest(WatchtrendWatch $watch): bool
    {
        if (!$watch->last_digest_sent_at) {
            return true;
        }

        $lastSent = Carbon::parse($watch->last_digest_sent_at);

        return match ($watch->digest_frequency) {
            'daily'   => $lastSent->addDay()->isPast(),
            'weekly'  => $lastSent->addWeek()->isPast(),
            'monthly' => $lastSent->addMonth()->isPast(),
            default   => false,
        };
    }
}
