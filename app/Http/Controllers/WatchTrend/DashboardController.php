<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use App\Models\WatchtrendAnalysis;
use App\Models\WatchtrendUserSetting;
use App\Models\WatchtrendWatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Load all user's active watches for sidebar/filter
        $watches = WatchtrendWatch::where('user_id', $userId)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        $watchIds = $watches->pluck('id');

        // Retrieve user's items_per_page preference
        $userSetting  = WatchtrendUserSetting::where('user_id', $userId)->first();
        $itemsPerPage = $userSetting?->items_per_page ?? 20;

        // Read filter query params
        $filters = [
            'watch_id'          => $request->query('watch_id'),
            'source_type'       => $request->query('source_type'),
            'category'          => $request->query('category'),
            'period'            => $request->query('period', 'week'),
            'search'            => $request->query('search'),
            'sort'              => $request->query('sort', 'relevance'),
            'show_low_relevance' => $request->boolean('show_low_relevance', false),
        ];

        // Build analyses query scoped to user's watches
        $query = WatchtrendAnalysis::with([
            'collectedItem.source',
            'feedback',
        ])
            ->whereIn('watch_id', $watchIds);

        // Filter: specific watch
        if ($filters['watch_id']) {
            $query->where('watch_id', (int) $filters['watch_id']);
        }

        // Filter: source type (via collected_item → source)
        if ($filters['source_type']) {
            $query->whereHas('collectedItem.source', function ($q) use ($filters) {
                $q->where('type', $filters['source_type']);
            });
        }

        // Filter: category
        if ($filters['category']) {
            $query->where('category', $filters['category']);
        }

        // Filter: period (based on collected_item published_at)
        if ($filters['period'] && $filters['period'] !== 'all') {
            $since = match ($filters['period']) {
                'today' => now()->startOfDay(),
                'week'  => now()->subWeek()->startOfDay(),
                'month' => now()->subMonth()->startOfDay(),
                default => null,
            };

            if ($since) {
                $query->whereHas('collectedItem', function ($q) use ($since) {
                    $q->where('published_at', '>=', $since);
                });
            }
        }

        // Filter: text search on collectedItem title/summary_fr
        if ($filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('summary_fr', 'like', "%{$search}%")
                  ->orWhereHas('collectedItem', function ($q2) use ($search) {
                      $q2->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Hide low relevance by default (score < 20)
        if (!$filters['show_low_relevance']) {
            $query->where('relevance_score', '>=', 20);
        }

        // Sorting
        $query = match ($filters['sort']) {
            'date'      => $query->latest('created_at'),
            'source'    => $query->orderByRaw('(SELECT type FROM watchtrend_sources WHERE watchtrend_sources.id = (SELECT source_id FROM watchtrend_collected_items WHERE watchtrend_collected_items.id = watchtrend_analyses.collected_item_id) LIMIT 1)'),
            default     => $query->orderBy('relevance_score', 'desc'), // relevance
        };

        $analyses = $query->paginate($itemsPerPage)->withQueryString();

        // Quick stats for the dashboard header
        $statsBase = WatchtrendAnalysis::whereIn('watch_id', $watchIds);

        $stats = [
            'unread'   => WatchtrendAnalysis::whereIn('watch_id', $watchIds)
                ->whereHas('collectedItem', fn ($q) => $q->where('is_read', false))
                ->count(),
            'critical' => (clone $statsBase)->where('category', 'critical_update')->count(),
            'trends'   => (clone $statsBase)->where('category', 'trend')->count(),
        ];

        // Unread count for sidebar badge
        $unreadCount = WatchtrendAnalysis::whereIn('watch_id', $watchIds)
            ->whereHas('collectedItem', fn ($q) => $q->where('is_read', false))
            ->count();

        return view('watchtrend.dashboard.index', compact(
            'watches',
            'analyses',
            'stats',
            'filters',
            'unreadCount'
        ));
    }

    public function suggestions(Request $request)
    {
        $userId = Auth::id();

        // Load all user's active watches for sidebar/filter
        $watches = WatchtrendWatch::where('user_id', $userId)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        $watchIds = $watches->pluck('id');

        // Retrieve user's items_per_page preference
        $userSetting  = WatchtrendUserSetting::where('user_id', $userId)->first();
        $itemsPerPage = $userSetting?->items_per_page ?? 20;

        // Optional watch filter via query param
        $watchId = $request->query('watch_id');

        // Verify ownership if a specific watch is requested
        $watch = null;
        if ($watchId) {
            $watch = WatchtrendWatch::where('user_id', $userId)
                ->where('status', 'active')
                ->findOrFail((int) $watchId);
        }

        // Read filter query params
        $filters = [
            'watch_id'           => $watchId,
            'source_type'        => $request->query('source_type'),
            'category'           => $request->query('category'),
            'period'             => $request->query('period', 'week'),
            'search'             => $request->query('search'),
            'sort'               => $request->query('sort', 'relevance'),
            'show_low_relevance' => $request->boolean('show_low_relevance', false),
        ];

        // Scope watch IDs
        $scopedWatchIds = $watchId ? [$watch->id] : $watchIds;

        // Build analyses query
        $query = WatchtrendAnalysis::with([
            'collectedItem.source',
            'feedback',
        ])
            ->whereIn('watch_id', $scopedWatchIds);

        // Filter: source type
        if ($filters['source_type']) {
            $query->whereHas('collectedItem.source', function ($q) use ($filters) {
                $q->where('type', $filters['source_type']);
            });
        }

        // Filter: category
        if ($filters['category']) {
            $query->where('category', $filters['category']);
        }

        // Filter: period
        if ($filters['period'] && $filters['period'] !== 'all') {
            $since = match ($filters['period']) {
                'today' => now()->startOfDay(),
                'week'  => now()->subWeek()->startOfDay(),
                'month' => now()->subMonth()->startOfDay(),
                default => null,
            };

            if ($since) {
                $query->whereHas('collectedItem', function ($q) use ($since) {
                    $q->where('published_at', '>=', $since);
                });
            }
        }

        // Filter: text search
        if ($filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('summary_fr', 'like', "%{$search}%")
                  ->orWhereHas('collectedItem', function ($q2) use ($search) {
                      $q2->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Hide low relevance by default
        if (!$filters['show_low_relevance']) {
            $query->where('relevance_score', '>=', 20);
        }

        // Sorting
        $query = match ($filters['sort']) {
            'date'  => $query->latest('created_at'),
            default => $query->orderBy('relevance_score', 'desc'),
        };

        $analyses = $query->paginate($itemsPerPage)->withQueryString();

        // Quick stats
        $statsBase = WatchtrendAnalysis::whereIn('watch_id', $scopedWatchIds);
        $stats = [
            'unread'   => WatchtrendAnalysis::whereIn('watch_id', $scopedWatchIds)
                ->whereHas('collectedItem', fn ($q) => $q->where('is_read', false))
                ->count(),
            'critical' => (clone $statsBase)->where('category', 'critical_update')->count(),
            'trends'   => (clone $statsBase)->where('category', 'trend')->count(),
        ];

        // Unread count for sidebar badge
        $unreadCount = $stats['unread'];

        return view('watchtrend.dashboard.index', compact(
            'watches',
            'watch',
            'analyses',
            'stats',
            'filters',
            'unreadCount'
        ));
    }
}
