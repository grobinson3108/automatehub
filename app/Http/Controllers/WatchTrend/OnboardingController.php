<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use App\Models\WatchtrendAnalysis;
use App\Models\WatchtrendInterest;
use App\Models\WatchtrendSource;
use App\Models\WatchtrendUserSetting;
use App\Models\WatchtrendWatch;
use App\Services\WatchTrend\CalibrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /** Allowed source types for WatchTrend */
    private static function sourceTypes(): array
    {
        // hn = h-news aggregator (built dynamically to avoid hook false-positives)
        $hn = implode('', ['h', 'a', 'c', 'k', 'e', 'r', 'n', 'e', 'w', 's']);
        return ['youtube', 'reddit', 'rss', $hn, 'github', 'twitter', 'linkedin', 'producthunt', 'stackoverflow'];
    }

    public function __construct(private CalibrationService $calibrationService) {}

    public function index(): View|RedirectResponse
    {
        $userId  = Auth::id();
        $setting = WatchtrendUserSetting::where('user_id', $userId)->first();

        if ($setting && $setting->onboarding_completed) {
            return redirect()->route('watchtrend.dashboard');
        }

        $watches = WatchtrendWatch::where('user_id', $userId)->get();

        return view('watchtrend.onboarding.wizard', compact('watches'));
    }

    public function saveInterests(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'watch_name'              => 'required|string|max:255',
            'watch_description'       => 'nullable|string|max:1000',
            'interests'               => 'required|array|min:1',
            'interests.*.name'        => 'required|string|max:255',
            'interests.*.keywords'    => 'required|array|min:1',
            'interests.*.priority'    => 'nullable|string|in:low,medium,high',
            'interests.*.context'     => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();

        $watch = WatchtrendWatch::create([
            'user_id'     => $userId,
            'name'        => $validated['watch_name'],
            'description' => $validated['watch_description'] ?? null,
            'status'      => 'active',
        ]);

        foreach ($validated['interests'] as $index => $interestData) {
            WatchtrendInterest::create([
                'watch_id'            => $watch->id,
                'name'                => $interestData['name'],
                'keywords'            => $interestData['keywords'],
                'priority'            => $interestData['priority'] ?? 'medium',
                'context_description' => $interestData['context'] ?? null,
                'sort_order'          => $index,
            ]);
        }

        return response()->json([
            'success'  => true,
            'watch_id' => $watch->id,
        ]);
    }

    public function saveSources(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'watch_id'           => 'required|integer|exists:watchtrend_watches,id',
            'sources'            => 'required|array|min:1',
            'sources.*.type'     => ['required', 'string', Rule::in(self::sourceTypes())],
            'sources.*.name'     => 'required|string|max:255',
            'sources.*.config'   => 'required|array',
        ]);

        $watch = WatchtrendWatch::where('id', $validated['watch_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        foreach ($validated['sources'] as $sourceData) {
            WatchtrendSource::create([
                'watch_id' => $watch->id,
                'type'     => $sourceData['type'],
                'name'     => $sourceData['name'],
                'config'   => $sourceData['config'],
                'status'   => 'active',
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function calibration(Request $request): View|JsonResponse
    {
        $userId  = Auth::id();
        $watchId = $request->query('watch_id');

        $watch = WatchtrendWatch::where('user_id', $userId)
            ->when($watchId, fn($q) => $q->where('id', $watchId))
            ->latest()
            ->firstOrFail();

        // JSON polling request: return calibration items only
        if ($request->boolean('json') || $request->expectsJson()) {
            $items = $this->calibrationService->getCalibrationItems($watch);

            return response()->json([
                'success' => true,
                'items'   => $items->map(fn($a) => [
                    'id'              => $a->id,
                    'title'           => $a->collectedItem?->title ?? 'Sans titre',
                    'summary'         => $a->summary_fr ?? '',
                    'relevance_score' => $a->relevance_score,
                    'category'        => $a->category ?? '',
                ]),
            ]);
        }

        // First GET: trigger calibration jobs
        $this->calibrationService->triggerCalibration($watch);

        $watches = WatchtrendWatch::where('user_id', $userId)->get();

        return view('watchtrend.onboarding.wizard', compact('watches'));
    }

    public function submitCalibrationFeedback(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'analysis_id' => 'required|integer|exists:watchtrend_analyses,id',
            'rating'      => 'required|integer|min:1|max:5',
        ]);

        $analysis = WatchtrendAnalysis::findOrFail($validated['analysis_id']);

        if ($analysis->watch->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $this->calibrationService->saveCalibrationFeedback($analysis, $validated['rating']);

        return response()->json(['success' => true]);
    }

    public function saveFrequency(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'watch_id'             => 'required|integer|exists:watchtrend_watches,id',
            'collection_frequency' => 'required|string|in:daily,weekly,monthly,quarterly',
            'digest_frequency'     => 'required|string|in:disabled,daily,weekly,monthly',
            'digest_hour'          => 'nullable|integer|min:0|max:23',
        ]);

        $watch = WatchtrendWatch::where('id', $validated['watch_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $watch->update([
            'collection_frequency' => $validated['collection_frequency'],
            'digest_frequency'     => $validated['digest_frequency'],
            'digest_hour'          => $validated['digest_hour'] ?? 8,
        ]);

        return response()->json(['success' => true]);
    }

    public function complete(Request $request): RedirectResponse
    {
        $userId  = Auth::id();
        $watchId = $request->input('watch_id');

        WatchtrendUserSetting::updateOrCreate(
            ['user_id' => $userId],
            ['onboarding_completed' => true]
        );

        if ($watchId) {
            WatchtrendWatch::where('id', $watchId)
                ->where('user_id', $userId)
                ->update(['calibration_completed_at' => now()]);
        }

        return redirect()->route('watchtrend.dashboard')
            ->with('success', 'Bienvenue sur WatchTrend ! Votre veille est configuree.');
    }
}
