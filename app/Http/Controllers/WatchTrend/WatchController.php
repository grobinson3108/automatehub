<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use App\Models\WatchtrendWatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchController extends Controller
{
    private function getUserWatch(int $watchId): WatchtrendWatch
    {
        return WatchtrendWatch::where('user_id', Auth::id())->findOrFail($watchId);
    }

    public function index()
    {
        $watches = WatchtrendWatch::where('user_id', Auth::id())
            ->withCount(['interests', 'sources'])
            ->orderBy('sort_order')
            ->get();

        return view('watchtrend.watches.index', compact('watches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'description'          => 'nullable|string|max:1000',
            'icon'                 => 'nullable|string|max:10',
            'collection_frequency' => 'in:daily,weekly,monthly,quarterly',
        ]);

        $validated['user_id']    = Auth::id();
        $validated['status']     = 'active';
        $validated['sort_order'] = (WatchtrendWatch::where('user_id', Auth::id())->max('sort_order') ?? 0) + 1;

        if (empty($validated['collection_frequency'])) {
            $validated['collection_frequency'] = 'daily';
        }

        $watch = WatchtrendWatch::create($validated);

        return response()->json(['success' => true, 'item' => $watch]);
    }

    public function show($watch)
    {
        $watch = $this->getUserWatch((int) $watch);
        $watch->load([
            'interests' => fn ($q) => $q->orderBy('sort_order'),
            'sources',
        ]);

        $itemsCount      = $watch->collectedItems()->count();
        $lastCollectedAt = $watch->last_collected_at;

        return view('watchtrend.watches.show', compact('watch', 'itemsCount', 'lastCollectedAt'));
    }

    public function update(Request $request, $watch)
    {
        $watch = $this->getUserWatch((int) $watch);

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'description'          => 'nullable|string|max:1000',
            'icon'                 => 'nullable|string|max:10',
            'collection_frequency' => 'in:daily,weekly,monthly,quarterly',
        ]);

        $watch->update($validated);

        return response()->json(['success' => true, 'item' => $watch->fresh()]);
    }

    public function destroy($watch)
    {
        $watch = $this->getUserWatch((int) $watch);
        $watch->delete();

        return response()->json(['success' => true, 'message' => 'Watch deleted.']);
    }

    public function pause($watch)
    {
        $watch = $this->getUserWatch((int) $watch);
        $watch->update(['status' => 'paused']);

        return response()->json(['success' => true, 'item' => $watch->fresh()]);
    }

    public function resume($watch)
    {
        $watch = $this->getUserWatch((int) $watch);
        $watch->update(['status' => 'active']);

        return response()->json(['success' => true, 'item' => $watch->fresh()]);
    }

    public function archive($watch)
    {
        $watch = $this->getUserWatch((int) $watch);
        $watch->update(['status' => 'archived']);

        return response()->json(['success' => true, 'item' => $watch->fresh()]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer',
        ]);

        $userId = Auth::id();

        foreach ($request->ids as $order => $id) {
            WatchtrendWatch::where('id', $id)
                ->where('user_id', $userId)
                ->update(['sort_order' => $order + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Watches reordered.']);
    }
}
