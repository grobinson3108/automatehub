<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use App\Models\WatchtrendInterest;
use App\Models\WatchtrendWatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterestController extends Controller
{
    private function getUserWatch(int $watchId): WatchtrendWatch
    {
        return WatchtrendWatch::where('user_id', Auth::id())->findOrFail($watchId);
    }

    public function index($watch)
    {
        $watch     = $this->getUserWatch((int) $watch);
        $interests = $watch->interests()->orderBy('sort_order')->get();
        $watches   = WatchtrendWatch::where('user_id', Auth::id())->orderBy('sort_order')->get();

        return view('watchtrend.interests.index', compact('watch', 'interests', 'watches'));
    }

    public function store(Request $request, $watch)
    {
        $watch = $this->getUserWatch((int) $watch);

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'keywords'            => 'required|array|min:1',
            'keywords.*'          => 'string|max:100',
            'priority'            => 'in:high,medium,low',
            'context_description' => 'nullable|string|max:1000',
        ]);

        $validated['watch_id']   = $watch->id;
        $validated['sort_order'] = ($watch->interests()->max('sort_order') ?? 0) + 1;
        $validated['is_active']  = true;

        if (empty($validated['priority'])) {
            $validated['priority'] = 'medium';
        }

        $interest = WatchtrendInterest::create($validated);

        return response()->json(['success' => true, 'item' => $interest]);
    }

    public function update(Request $request, $watch, $interest)
    {
        $watch    = $this->getUserWatch((int) $watch);
        $interest = WatchtrendInterest::where('watch_id', $watch->id)->findOrFail((int) $interest);

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'keywords'            => 'required|array|min:1',
            'keywords.*'          => 'string|max:100',
            'priority'            => 'in:high,medium,low',
            'context_description' => 'nullable|string|max:1000',
        ]);

        $interest->update($validated);

        return response()->json(['success' => true, 'item' => $interest->fresh()]);
    }

    public function destroy($watch, $interest)
    {
        $watch    = $this->getUserWatch((int) $watch);
        $interest = WatchtrendInterest::where('watch_id', $watch->id)->findOrFail((int) $interest);
        $interest->delete();

        return response()->json(['success' => true, 'message' => 'Interest deleted.']);
    }

    public function reorder(Request $request, $watch)
    {
        $watch = $this->getUserWatch((int) $watch);

        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer',
        ]);

        foreach ($request->ids as $order => $id) {
            WatchtrendInterest::where('id', $id)
                ->where('watch_id', $watch->id)
                ->update(['sort_order' => $order + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Interests reordered.']);
    }
}
