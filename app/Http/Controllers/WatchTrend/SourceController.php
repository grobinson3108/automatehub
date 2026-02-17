<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use App\Models\WatchtrendSource;
use App\Models\WatchtrendWatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SourceController extends Controller
{
    private function getUserWatch(int $watchId): WatchtrendWatch
    {
        return WatchtrendWatch::where('user_id', Auth::id())->findOrFail($watchId);
    }

    private function verifySourceOwnership(WatchtrendSource $source): void
    {
        abort_if($source->watch->user_id !== Auth::id(), 403);
    }

    private function configRules(string $type): array
    {
        return match ($type) {
            'youtube'    => ['config.channel_id' => 'required|string'],
            'reddit'     => ['config.subreddit'  => 'required|string'],
            'rss'        => ['config.feed_url'   => 'required|url'],
            'hackernews' => ['config.query'      => 'required|string'],
            'github'     => ['config.repo'       => 'required|string'],
            'twitter'    => ['config.username'   => 'required|string'],
            default      => [],
        };
    }

    private function generateName(string $type, array $config): string
    {
        return match ($type) {
            'youtube'    => 'YouTube - ' . ($config['channel_id'] ?? ''),
            'reddit'     => 'Reddit - r/' . ($config['subreddit'] ?? ''),
            'rss'        => 'RSS - ' . ($config['feed_url'] ?? ''),
            'hackernews' => 'HackerNews - ' . ($config['query'] ?? ''),
            'github'     => 'GitHub - ' . ($config['repo'] ?? ''),
            'twitter'    => 'Twitter - @' . ($config['username'] ?? ''),
            default      => $type,
        };
    }

    public function index()
    {
        $watches = WatchtrendWatch::where('user_id', Auth::id())
            ->orderBy('sort_order')
            ->get();

        $sources = WatchtrendSource::whereIn('watch_id', $watches->pluck('id'))
            ->orderBy('name')
            ->get()
            ->map(function ($source) use ($watches) {
                $source->watch_name = $watches->firstWhere('id', $source->watch_id)?->name;
                $source->items_count = $source->items_collected_total ?? 0;
                return $source;
            });

        return view('watchtrend.sources.index', compact('watches', 'sources'));
    }

    public function store(Request $request)
    {
        $base = $request->validate([
            'watch_id' => 'required|integer',
            'type'     => 'required|in:youtube,reddit,rss,hackernews,github,twitter',
            'name'     => 'nullable|string|max:255',
            'config'   => 'nullable|array',
        ]);

        $watch = $this->getUserWatch((int) $base['watch_id']);

        $request->validate($this->configRules($base['type']));

        $name = !empty($base['name'])
            ? $base['name']
            : $this->generateName($base['type'], $request->input('config', []));

        $source = WatchtrendSource::create([
            'watch_id' => $watch->id,
            'type'     => $base['type'],
            'name'     => $name,
            'config'   => $request->input('config', []),
            'status'   => 'active',
        ]);

        return response()->json(['success' => true, 'item' => $source]);
    }

    public function update(Request $request, $source)
    {
        $source = WatchtrendSource::with('watch')->findOrFail((int) $source);
        $this->verifySourceOwnership($source);

        $base = $request->validate([
            'watch_id' => 'required|integer',
            'type'     => 'required|in:youtube,reddit,rss,hackernews,github,twitter',
            'name'     => 'nullable|string|max:255',
            'config'   => 'nullable|array',
        ]);

        // verify the new watch_id still belongs to the user
        $watch = $this->getUserWatch((int) $base['watch_id']);

        $request->validate($this->configRules($base['type']));

        $name = !empty($base['name'])
            ? $base['name']
            : $this->generateName($base['type'], $request->input('config', []));

        $source->update([
            'watch_id' => $watch->id,
            'type'     => $base['type'],
            'name'     => $name,
            'config'   => $request->input('config', []),
        ]);

        return response()->json(['success' => true, 'item' => $source->fresh()]);
    }

    public function destroy($source)
    {
        $source = WatchtrendSource::with('watch')->findOrFail((int) $source);
        $this->verifySourceOwnership($source);
        $source->delete();

        return response()->json(['success' => true, 'message' => 'Source deleted.']);
    }

    public function toggle($source)
    {
        $source = WatchtrendSource::with('watch')->findOrFail((int) $source);
        $this->verifySourceOwnership($source);

        $newStatus = $source->status === 'active' ? 'paused' : 'active';
        $source->update(['status' => $newStatus]);

        return response()->json(['success' => true, 'item' => $source->fresh()]);
    }

    public function test($source)
    {
        $source = WatchtrendSource::with('watch')->findOrFail((int) $source);
        $this->verifySourceOwnership($source);

        return response()->json(['success' => true, 'message' => 'Test will be available in Sprint 2.']);
    }

    public function validateSource(Request $request)
    {
        $base = $request->validate([
            'watch_id' => 'required|integer',
            'type'     => 'required|in:youtube,reddit,rss,hackernews,github,twitter',
            'config'   => 'nullable|array',
        ]);

        $this->getUserWatch((int) $base['watch_id']);

        $request->validate($this->configRules($base['type']));

        return response()->json(['success' => true, 'valid' => true]);
    }
}
