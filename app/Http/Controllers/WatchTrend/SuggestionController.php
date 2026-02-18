<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use App\Models\WatchtrendAnalysis;
use App\Models\WatchtrendCollectedItem;
use App\Models\WatchtrendFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuggestionController extends Controller
{
    public function feedback(Request $request, $analysis)
    {
        $analysis = WatchtrendAnalysis::with('collectedItem.watch')->findOrFail((int) $analysis);

        abort_if($analysis->watch->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $feedback = WatchtrendFeedback::updateOrCreate(
            [
                'watch_id'    => $analysis->watch_id,
                'analysis_id' => $analysis->id,
            ],
            [
                'rating'         => $validated['rating'],
                'source_channel' => 'web',
            ]
        );

        return response()->json([
            'success' => true,
            'rating'  => $feedback->rating,
        ]);
    }

    public function markRead($item)
    {
        $item = WatchtrendCollectedItem::with('watch')->findOrFail((int) $item);

        abort_if($item->watch->user_id !== Auth::id(), 403);

        $item->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function toggleFavorite($item)
    {
        $collectedItem = WatchtrendCollectedItem::with('watch')->findOrFail((int) $item);

        abort_if($collectedItem->watch->user_id !== Auth::id(), 403);

        $analysis = WatchtrendAnalysis::where('collected_item_id', $collectedItem->id)->firstOrFail();

        $analysis->update(['is_favorite' => !$analysis->is_favorite]);

        return response()->json([
            'success'     => true,
            'is_favorite' => $analysis->is_favorite,
        ]);
    }
}
