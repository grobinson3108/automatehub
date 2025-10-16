<?php

namespace App\Services;

use App\Models\Analytics;
use App\Models\User;
use App\Models\Tutorial;
use App\Models\Download;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Enregistre une vue de page
     */
    public function trackPageView($userId, $page)
    {
        Analytics::create([
            'user_id' => $userId,
            'event_type' => 'page_view',
            'metadata' => [
                'page' => $page,
                'timestamp' => now(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip()
            ],
            'created_at' => now()
        ]);
    }

    /**
     * Enregistre un téléchargement
     */
    public function trackDownload($userId, $tutorialId, $fileName)
    {
        Analytics::create([
            'user_id' => $userId,
            'event_type' => 'download',
            'metadata' => [
                'tutorial_id' => $tutorialId,
                'file_name' => $fileName,
                'timestamp' => now(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip()
            ],
            'created_at' => now()
        ]);
    }

    /**
     * Enregistre la vue d'un tutoriel
     */
    public function trackTutorialView($userId, $tutorialId)
    {
        Analytics::create([
            'user_id' => $userId,
            'event_type' => 'tutorial_view',
            'metadata' => [
                'tutorial_id' => $tutorialId,
                'timestamp' => now(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip()
            ],
            'created_at' => now()
        ]);
    }

    /**
     * Récupère les statistiques globales pour le dashboard admin
     */
    public function getGlobalStats()
    {
        $stats = [];

        // Statistiques utilisateurs
        $stats['users'] = [
            'total' => User::count(),
            'new_today' => User::whereDate('created_at', today())->count(),
            'new_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'active_today' => User::whereDate('last_activity_at', today())->count(),
            'by_subscription' => [
                'free' => User::where('subscription_type', 'free')->count(),
                'premium' => User::where('subscription_type', 'premium')->count(),
                'pro' => User::where('subscription_type', 'pro')->count(),
            ],
            'by_type' => [
                'professional' => User::where('is_professional', true)->count(),
                'individual' => User::where('is_professional', false)->count(),
            ]
        ];

        // Statistiques tutoriels
        $stats['tutorials'] = [
            'total' => Tutorial::count(),
            'by_level' => [
                'beginner' => Tutorial::where('level', 'beginner')->count(),
                'intermediate' => Tutorial::where('level', 'intermediate')->count(),
                'advanced' => Tutorial::where('level', 'advanced')->count(),
            ],
            'by_subscription' => [
                'free' => Tutorial::where('subscription_required', 'free')->count(),
                'premium' => Tutorial::where('subscription_required', 'premium')->count(),
                'pro' => Tutorial::where('subscription_required', 'pro')->count(),
            ]
        ];

        // Statistiques téléchargements
        $stats['downloads'] = [
            'total' => Download::count(),
            'today' => Download::whereDate('downloaded_at', today())->count(),
            'this_week' => Download::whereBetween('downloaded_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Download::whereMonth('downloaded_at', now()->month)->count(),
        ];

        // Statistiques analytics
        $stats['analytics'] = [
            'page_views_today' => Analytics::where('event_type', 'page_view')
                ->whereDate('created_at', today())->count(),
            'tutorial_views_today' => Analytics::where('event_type', 'tutorial_view')
                ->whereDate('created_at', today())->count(),
            'downloads_today' => Analytics::where('event_type', 'download')
                ->whereDate('created_at', today())->count(),
        ];

        // Top pages vues (derniers 7 jours)
        $stats['top_pages'] = Analytics::where('event_type', 'page_view')
            ->where('created_at', '>=', now()->subDays(7))
            ->get()
            ->groupBy(function($item) {
                return $item->metadata['page'] ?? 'unknown';
            })
            ->map(function($group) {
                return $group->count();
            })
            ->sortDesc()
            ->take(10);

        // Top tutoriels vus (derniers 7 jours)
        $stats['top_tutorials'] = Analytics::where('event_type', 'tutorial_view')
            ->where('created_at', '>=', now()->subDays(7))
            ->get()
            ->groupBy(function($item) {
                return $item->metadata['tutorial_id'] ?? 'unknown';
            })
            ->map(function($group) {
                return $group->count();
            })
            ->sortDesc()
            ->take(10);

        // Évolution des inscriptions (30 derniers jours)
        $stats['registrations_chart'] = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Évolution des téléchargements (30 derniers jours)
        $stats['downloads_chart'] = Download::select(
                DB::raw('DATE(downloaded_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('downloaded_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $stats;
    }

    /**
     * Récupère les statistiques d'un utilisateur pour son dashboard
     */
    public function getUserStats($userId)
    {
        $user = User::findOrFail($userId);
        $stats = [];

        // Statistiques générales de l'utilisateur
        $stats['user_info'] = [
            'registration_date' => $user->created_at,
            'last_activity' => $user->last_activity_at,
            'subscription_type' => $user->subscription_type,
            'n8n_level' => $user->n8n_level,
            'is_professional' => $user->is_professional,
        ];

        // Activité de l'utilisateur
        $stats['activity'] = [
            'total_page_views' => Analytics::where('user_id', $userId)
                ->where('event_type', 'page_view')->count(),
            'total_tutorial_views' => Analytics::where('user_id', $userId)
                ->where('event_type', 'tutorial_view')->count(),
            'total_downloads' => Download::where('user_id', $userId)->count(),
            'page_views_this_month' => Analytics::where('user_id', $userId)
                ->where('event_type', 'page_view')
                ->whereMonth('created_at', now()->month)->count(),
            'tutorial_views_this_month' => Analytics::where('user_id', $userId)
                ->where('event_type', 'tutorial_view')
                ->whereMonth('created_at', now()->month)->count(),
            'downloads_this_month' => Download::where('user_id', $userId)
                ->whereMonth('downloaded_at', now()->month)->count(),
        ];

        // Progression tutoriels
        $stats['tutorials'] = [
            'completed' => DB::table('user_tutorial_progress')
                ->where('user_id', $userId)
                ->where('completed', true)->count(),
            'in_progress' => DB::table('user_tutorial_progress')
                ->where('user_id', $userId)
                ->where('completed', false)->count(),
            'favorites' => DB::table('favorites')
                ->where('user_id', $userId)->count(),
        ];

        // Badges obtenus
        $stats['badges'] = [
            'total_earned' => DB::table('user_badges')
                ->where('user_id', $userId)->count(),
            'recent_badges' => DB::table('user_badges')
                ->join('badges', 'user_badges.badge_id', '=', 'badges.id')
                ->where('user_badges.user_id', $userId)
                ->orderBy('user_badges.earned_at', 'desc')
                ->take(5)
                ->get(['badges.name', 'badges.description', 'user_badges.earned_at']),
        ];

        // Historique des téléchargements récents
        $stats['recent_downloads'] = Download::where('user_id', $userId)
            ->join('tutorials', 'downloads.tutorial_id', '=', 'tutorials.id')
            ->orderBy('downloads.downloaded_at', 'desc')
            ->take(10)
            ->get(['downloads.*', 'tutorials.title as tutorial_title']);

        // Pages les plus visitées par l'utilisateur
        $stats['favorite_pages'] = Analytics::where('user_id', $userId)
            ->where('event_type', 'page_view')
            ->get()
            ->groupBy(function($item) {
                return $item->metadata['page'] ?? 'unknown';
            })
            ->map(function($group) {
                return $group->count();
            })
            ->sortDesc()
            ->take(5);

        // Activité par jour (derniers 30 jours)
        $stats['activity_chart'] = Analytics::where('user_id', $userId)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $stats;
    }

    /**
     * Nettoie les anciennes données analytics (plus de 6 mois)
     */
    public function cleanOldAnalytics()
    {
        $deletedCount = Analytics::where('created_at', '<', now()->subMonths(6))->delete();
        
        return $deletedCount;
    }

    /**
     * Enregistre un événement générique
     */
    public function track($userId, $eventType, $eventData = [])
    {
        Analytics::create([
            'user_id' => $userId,
            'event_type' => $eventType,
            'metadata' => array_merge($eventData, [
                'timestamp' => now(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip()
            ]),
            'created_at' => now()
        ]);
    }

    /**
     * Récupère les statistiques de conversion (pour admin)
     */
    public function getConversionStats()
    {
        $stats = [];

        // Taux de conversion inscription
        $totalVisitors = Analytics::where('event_type', 'page_view')
            ->distinct('user_id')
            ->count();
        $totalRegistrations = User::count();
        
        $stats['registration_rate'] = $totalVisitors > 0 ? 
            round(($totalRegistrations / $totalVisitors) * 100, 2) : 0;

        // Taux de conversion premium
        $freeUsers = User::where('subscription_type', 'free')->count();
        $premiumUsers = User::whereIn('subscription_type', ['premium', 'pro'])->count();
        
        $stats['premium_conversion_rate'] = $freeUsers > 0 ? 
            round(($premiumUsers / ($freeUsers + $premiumUsers)) * 100, 2) : 0;

        // Engagement moyen
        $avgPageViews = Analytics::where('event_type', 'page_view')
            ->groupBy('user_id')
            ->selectRaw('COUNT(*) as views')
            ->get()
            ->avg('views');
            
        $stats['avg_page_views_per_user'] = round($avgPageViews, 2);

        return $stats;
    }
}
