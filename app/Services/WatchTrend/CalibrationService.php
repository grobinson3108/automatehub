<?php

namespace App\Services\WatchTrend;

use App\Jobs\WatchTrend\CollectSourceJob;
use App\Models\WatchtrendAnalysis;
use App\Models\WatchtrendFeedback;
use App\Models\WatchtrendWatch;

class CalibrationService
{
    /**
     * Dispatch CollectSourceJob for each active source of the watch.
     * Returns the number of jobs dispatched.
     */
    public function triggerCalibration(WatchtrendWatch $watch): int
    {
        $sources = $watch->sources()->where('status', 'active')->get();

        foreach ($sources as $source) {
            CollectSourceJob::dispatch($source);
        }

        return $sources->count();
    }

    /**
     * Get the top N analyses (by relevance score desc) for calibration display,
     * with the collectedItem eager loaded.
     */
    public function getCalibrationItems(WatchtrendWatch $watch, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return WatchtrendAnalysis::with('collectedItem')
            ->where('watch_id', $watch->id)
            ->orderByDesc('relevance_score')
            ->limit($limit)
            ->get();
    }

    /**
     * Create or update feedback for an analysis during onboarding calibration.
     */
    public function saveCalibrationFeedback(WatchtrendAnalysis $analysis, int $rating): WatchtrendFeedback
    {
        return WatchtrendFeedback::updateOrCreate(
            [
                'analysis_id' => $analysis->id,
                'watch_id'    => $analysis->watch_id,
            ],
            [
                'rating'         => $rating,
                'source_channel' => 'onboarding',
            ]
        );
    }
}
