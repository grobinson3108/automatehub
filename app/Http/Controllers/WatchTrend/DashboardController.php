<?php

namespace App\Http\Controllers\WatchTrend;

use App\Http\Controllers\Controller;
use App\Models\WatchtrendAnalysis;
use App\Models\WatchtrendFeedback;
use App\Models\WatchtrendSource;
use App\Models\WatchtrendUserSetting;
use App\Models\WatchtrendWatch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            'watch_id'           => $request->query('watch_id'),
            'source_type'        => $request->query('source_type'),
            'category'           => $request->query('category'),
            'period'             => $request->query('period', 'week'),
            'search'             => $request->query('search'),
            'sort'               => $request->query('sort', 'relevance'),
            'show_low_relevance' => $request->boolean('show_low_relevance', false),
            'favorites_only'     => $request->boolean('favorites_only', false),
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

        // Filter: favorites only
        if ($filters['favorites_only']) {
            $query->where('is_favorite', true);
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

    public function analytics(Request $request)
    {
        $userId = Auth::id();

        $watches = WatchtrendWatch::where('user_id', $userId)
            ->orderBy('sort_order')
            ->get();

        $watchIds = $watches->pluck('id');

        // Optional filters
        $selectedWatchId = $request->query('watch_id');
        $period          = $request->query('period', '30');

        // Scope watch IDs to the selected watch if provided
        if ($selectedWatchId && $watchIds->contains((int) $selectedWatchId)) {
            $scopedWatchIds = collect([(int) $selectedWatchId]);
        } else {
            $scopedWatchIds = $watchIds;
        }

        // Period start date
        $since = match ($period) {
            '90'  => now()->subDays(90)->startOfDay(),
            'all' => null,
            default => now()->subDays(30)->startOfDay(),
        };

        // Base query builder helper
        $baseAnalyses = fn () => WatchtrendAnalysis::whereIn('watch_id', $scopedWatchIds)
            ->when($since, fn ($q) => $q->where('created_at', '>=', $since));

        // --- Analyses over time (line chart) ---
        $analysesOverTime = ($baseAnalyses)()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => ['date' => $row->date, 'count' => (int) $row->count]);

        // --- Category distribution (pie chart) ---
        $categoryDistribution = ($baseAnalyses)()
            ->select('category', DB::raw('COUNT(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->get()
            ->map(fn ($row) => ['category' => $row->category, 'count' => (int) $row->count]);

        // --- Source distribution (doughnut chart) ---
        $sourceDistribution = WatchtrendSource::whereIn('watch_id', $scopedWatchIds)
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get()
            ->map(fn ($row) => ['type' => $row->type, 'count' => (int) $row->count]);

        // --- Score distribution (bar chart) ---
        $scoreDistribution = collect([
            ['range' => '0-20',   'count' => 0],
            ['range' => '20-40',  'count' => 0],
            ['range' => '40-60',  'count' => 0],
            ['range' => '60-80',  'count' => 0],
            ['range' => '80-100', 'count' => 0],
        ]);
        $scoreCounts = ($baseAnalyses)()
            ->select(
                DB::raw('FLOOR(relevance_score / 20) as bucket'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('bucket')
            ->get();
        foreach ($scoreCounts as $row) {
            $idx = min((int) $row->bucket, 4);
            $scoreDistribution[$idx] = [
                'range' => $scoreDistribution[$idx]['range'],
                'count' => (int) $row->count,
            ];
        }

        // --- Feedback stats (bar chart) ---
        $feedbackStats = WatchtrendFeedback::whereIn('watch_id', $scopedWatchIds)
            ->select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating')
            ->get()
            ->map(fn ($row) => ['rating' => (int) $row->rating, 'count' => (int) $row->count]);

        // Fill missing ratings 1-5
        $feedbackByRating = [];
        foreach (range(1, 5) as $r) {
            $feedbackByRating[] = [
                'rating' => $r,
                'count'  => $feedbackStats->firstWhere('rating', $r)['count'] ?? 0,
            ];
        }

        // --- Top 5 sources ---
        $topSources = WatchtrendSource::whereIn('watch_id', $scopedWatchIds)
            ->orderBy('items_collected_total', 'desc')
            ->limit(5)
            ->get(['name', 'type', 'items_collected_total', 'last_collected_at']);

        // --- Global stats ---
        $globalStats = [
            'total_analyses'  => ($baseAnalyses)()->count(),
            'avg_score'       => round((float) ($baseAnalyses)()->avg('relevance_score') ?? 0, 1),
            'total_sources'   => WatchtrendSource::whereIn('watch_id', $scopedWatchIds)->count(),
            'total_items'     => WatchtrendSource::whereIn('watch_id', $scopedWatchIds)->sum('items_collected_total'),
            'total_feedbacks' => WatchtrendFeedback::whereIn('watch_id', $scopedWatchIds)->count(),
        ];

        return view('watchtrend.analytics.index', compact(
            'watches',
            'selectedWatchId',
            'period',
            'analysesOverTime',
            'categoryDistribution',
            'sourceDistribution',
            'scoreDistribution',
            'feedbackByRating',
            'topSources',
            'globalStats'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $userId = Auth::id();

        $watches = WatchtrendWatch::where('user_id', $userId)
            ->where('status', 'active')
            ->pluck('id');

        $filters = [
            'watch_id'           => $request->query('watch_id'),
            'source_type'        => $request->query('source_type'),
            'category'           => $request->query('category'),
            'period'             => $request->query('period', 'week'),
            'search'             => $request->query('search'),
            'sort'               => $request->query('sort', 'relevance'),
            'show_low_relevance' => $request->boolean('show_low_relevance', false),
            'favorites_only'     => $request->boolean('favorites_only', false),
        ];

        $query = WatchtrendAnalysis::with(['collectedItem.source'])
            ->whereIn('watch_id', $watches);

        if ($filters['watch_id']) {
            $query->where('watch_id', (int) $filters['watch_id']);
        }

        if ($filters['source_type']) {
            $query->whereHas('collectedItem.source', function ($q) use ($filters) {
                $q->where('type', $filters['source_type']);
            });
        }

        if ($filters['category']) {
            $query->where('category', $filters['category']);
        }

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

        if ($filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('summary_fr', 'like', "%{$search}%")
                  ->orWhereHas('collectedItem', function ($q2) use ($search) {
                      $q2->where('title', 'like', "%{$search}%");
                  });
            });
        }

        if (!$filters['show_low_relevance']) {
            $query->where('relevance_score', '>=', 20);
        }

        if ($filters['favorites_only']) {
            $query->where('is_favorite', true);
        }

        $query = match ($filters['sort']) {
            'date'  => $query->latest('created_at'),
            default => $query->orderBy('relevance_score', 'desc'),
        };

        $analyses = $query->limit(1000)->get();

        $filename = 'watchtrend-export-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($analyses) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Titre', 'URL', 'Catégorie', 'Score', 'Résumé', 'Insight', 'Source', 'Date', 'Favori'], ';');

            foreach ($analyses as $analysis) {
                $item = $analysis->collectedItem;
                fputcsv($handle, [
                    $item->title ?? '',
                    $item->url ?? '',
                    $analysis->category ?? '',
                    (int) $analysis->relevance_score,
                    $analysis->summary_fr ?? '',
                    $analysis->actionable_insight ?? '',
                    $item->source->name ?? '',
                    $item->published_at ? $item->published_at->format('d/m/Y') : '',
                    $analysis->is_favorite ? 'Oui' : 'Non',
                ], ';');
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache',
        ]);
    }
}
