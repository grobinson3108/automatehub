<?php

namespace App\Services\WatchTrend;

use App\Models\WatchtrendWatch;
use App\Models\WatchtrendFeedback;

class ScoringService
{
    /**
     * Analyze recent feedbacks and return preference adjustments for a watch.
     * Returns an array of categories preferences based on feedback ratings.
     *
     * @param WatchtrendWatch $watch
     * @return array ['categories_liked' => [], 'categories_disliked' => [], 'avg_rating' => float, 'total_feedbacks' => int]
     */
    public function adjustScoreFromFeedback(WatchtrendWatch $watch): array
    {
        $feedbacks = WatchtrendFeedback::where('watch_id', $watch->id)
            ->with('analysis')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        if ($feedbacks->isEmpty()) {
            return [
                'categories_liked'    => [],
                'categories_disliked' => [],
                'avg_rating'          => 0.0,
                'total_feedbacks'     => 0,
            ];
        }

        $positiveFeedbacks = $feedbacks->filter(fn($f) => $f->rating >= 4);
        $negativeFeedbacks = $feedbacks->filter(fn($f) => $f->rating <= 2);

        $categoriesLiked = $positiveFeedbacks
            ->map(fn($f) => $f->analysis?->category)
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->values()
            ->toArray();

        $categoriesDisliked = $negativeFeedbacks
            ->map(fn($f) => $f->analysis?->category)
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->values()
            ->toArray();

        $avgRating = round($feedbacks->avg('rating'), 2);

        return [
            'categories_liked'    => $categoriesLiked,
            'categories_disliked' => $categoriesDisliked,
            'avg_rating'          => (float) $avgRating,
            'total_feedbacks'     => $feedbacks->count(),
        ];
    }

    /**
     * Build a full preference profile for a watch based on all historical feedbacks.
     * This profile can be passed to AnalyzerService to bias AI scoring.
     *
     * @param WatchtrendWatch $watch
     * @return array
     */
    public function getPreferenceProfile(WatchtrendWatch $watch): array
    {
        $feedbacks = WatchtrendFeedback::where('watch_id', $watch->id)
            ->with('analysis')
            ->get();

        if ($feedbacks->isEmpty()) {
            return [
                'categories_liked'    => [],
                'categories_disliked' => [],
                'avg_rating'          => 0.0,
                'total_feedbacks'     => 0,
            ];
        }

        $positiveFeedbacks = $feedbacks->filter(fn($f) => $f->rating >= 4);
        $negativeFeedbacks = $feedbacks->filter(fn($f) => $f->rating <= 2);

        $categoriesLiked = $positiveFeedbacks
            ->map(fn($f) => $f->analysis?->category)
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->values()
            ->toArray();

        $categoriesDisliked = $negativeFeedbacks
            ->map(fn($f) => $f->analysis?->category)
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->values()
            ->toArray();

        $avgRating      = round($feedbacks->avg('rating'), 2);
        $totalFeedbacks = $feedbacks->count();

        return [
            'categories_liked'    => $categoriesLiked,
            'categories_disliked' => $categoriesDisliked,
            'avg_rating'          => (float) $avgRating,
            'total_feedbacks'     => $totalFeedbacks,
        ];
    }
}
