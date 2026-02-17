<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use App\Models\WatchtrendPainPoint;
use App\Models\WatchtrendWatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PainPointController extends Controller
{
    private function getUserWatch(int $watchId): WatchtrendWatch
    {
        return WatchtrendWatch::where('user_id', Auth::id())->findOrFail($watchId);
    }

    private function verifyPainPointOwnership(WatchtrendPainPoint $painPoint): void
    {
        abort_if($painPoint->watch->user_id !== Auth::id(), 403);
    }

    public function index()
    {
        $watches = WatchtrendWatch::where('user_id', Auth::id())
            ->orderBy('sort_order')
            ->get();

        $painPoints = WatchtrendPainPoint::whereIn('watch_id', $watches->pluck('id'))
            ->orderBy('priority')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($pp) use ($watches) {
                $pp->watch_name = $watches->firstWhere('id', $pp->watch_id)?->name;
                return $pp;
            });

        return view('watchtrend.pain-points.index', compact('watches', 'painPoints'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'watch_id'    => 'required|integer',
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'priority'    => 'in:high,medium,low',
        ]);

        $watch = $this->getUserWatch((int) $validated['watch_id']);

        $activeCount = $watch->painPoints()->where('status', 'active')->count();

        if ($activeCount >= 10) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum 10 active pain points per watch. Please resolve some before adding new ones.',
            ], 422);
        }

        $validated['watch_id'] = $watch->id;
        $validated['status']   = 'active';

        if (empty($validated['priority'])) {
            $validated['priority'] = 'medium';
        }

        $painPoint = WatchtrendPainPoint::create($validated);

        return response()->json(['success' => true, 'item' => $painPoint]);
    }

    public function update(Request $request, $painPoint)
    {
        $painPoint = WatchtrendPainPoint::with('watch')->findOrFail((int) $painPoint);
        $this->verifyPainPointOwnership($painPoint);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'priority'    => 'in:high,medium,low',
        ]);

        $painPoint->update($validated);

        return response()->json(['success' => true, 'item' => $painPoint->fresh()]);
    }

    public function destroy($painPoint)
    {
        $painPoint = WatchtrendPainPoint::with('watch')->findOrFail((int) $painPoint);
        $this->verifyPainPointOwnership($painPoint);
        $painPoint->delete();

        return response()->json(['success' => true, 'message' => 'Pain point deleted.']);
    }

    public function resolve($painPoint)
    {
        $painPoint = WatchtrendPainPoint::with('watch')->findOrFail((int) $painPoint);
        $this->verifyPainPointOwnership($painPoint);
        $painPoint->update(['status' => 'resolved']);

        return response()->json(['success' => true, 'item' => $painPoint->fresh()]);
    }
}
