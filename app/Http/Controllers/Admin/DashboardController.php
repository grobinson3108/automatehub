<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Models\User;
use App\Models\Tutorial;
use App\Models\Download;
use App\Models\Analytics;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the admin dashboard with main KPIs.
     */
    public function index()
    {
        try {
            // KPIs principaux avec gestion des cas vides
            $totalUsers = User::count();
            $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->count();
            
            $premiumUsers = User::where('subscription_type', 'premium')->count();
            $proUsers = User::where('subscription_type', 'pro')->count();
            
            $totalTutorials = Tutorial::count();
            // Correction : utiliser is_draft au lieu de status
            $publishedTutorials = Tutorial::where('is_draft', false)
                                        ->whereNotNull('published_at')
                                        ->count();
            
            $totalDownloads = Download::count();
            $downloadsThisMonth = Download::whereMonth('downloaded_at', Carbon::now()->month)
                                        ->whereYear('downloaded_at', Carbon::now()->year)
                                        ->count();
            
            // Taux de conversion avec protection division par zéro
            $conversionRate = $totalUsers > 0 ? (($premiumUsers + $proUsers) / $totalUsers) * 100 : 0;
            
            // Chiffre d'affaires estimé (basé sur les abonnements)
            $monthlyRevenue = ($premiumUsers * 19.99) + ($proUsers * 49.99);
            
            // Utilisateurs actifs (connectés dans les 30 derniers jours)
            $activeUsers = User::where('last_activity_at', '>=', Carbon::now()->subDays(30))->count();
            
            $kpis = [
                'total_users' => $totalUsers,
                'new_users_this_month' => $newUsersThisMonth,
                'premium_users' => $premiumUsers,
                'pro_users' => $proUsers,
                'active_users' => $activeUsers,
                'total_tutorials' => $totalTutorials,
                'published_tutorials' => $publishedTutorials,
                'total_downloads' => $totalDownloads,
                'downloads_this_month' => $downloadsThisMonth,
                'conversion_rate' => round($conversionRate, 2),
                'monthly_revenue' => round($monthlyRevenue, 2),
            ];

            return view('admin.dashboard.index', compact('kpis'));
        } catch (\Exception $e) {
            // En cas d'erreur, retourner des valeurs par défaut
            $kpis = [
                'total_users' => 0,
                'new_users_this_month' => 0,
                'premium_users' => 0,
                'pro_users' => 0,
                'active_users' => 0,
                'total_tutorials' => 0,
                'published_tutorials' => 0,
                'total_downloads' => 0,
                'downloads_this_month' => 0,
                'conversion_rate' => 0,
                'monthly_revenue' => 0,
            ];

            \Log::error('Erreur dans AdminDashboardController::index', ['error' => $e->getMessage()]);
            return view('admin.dashboard.index', compact('kpis'));
        }
    }

    /**
     * Get statistics data for charts.
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '30'); // 7, 30, 90 jours
            $startDate = Carbon::now()->subDays($period);

            // Évolution des inscriptions
            $userRegistrations = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                    ->where('created_at', '>=', $startDate)
                                    ->groupBy('date')
                                    ->orderBy('date')
                                    ->get();

            // Évolution des téléchargements
            $downloadStats = Download::selectRaw('DATE(downloaded_at) as date, COUNT(*) as count')
                                    ->where('downloaded_at', '>=', $startDate)
                                    ->groupBy('date')
                                    ->orderBy('date')
                                    ->get();

            // Répartition par type d'abonnement
            $subscriptionStats = User::selectRaw('subscription_type, COUNT(*) as count')
                                    ->whereNotNull('subscription_type')
                                    ->groupBy('subscription_type')
                                    ->get();

            // Répartition par niveau n8n
            $levelStats = User::selectRaw('n8n_level, COUNT(*) as count')
                             ->whereNotNull('n8n_level')
                             ->groupBy('n8n_level')
                             ->get();

            // Top tutoriels les plus téléchargés
            $topTutorials = Tutorial::withCount('downloads')
                                  ->orderBy('downloads_count', 'desc')
                                  ->limit(10)
                                  ->get(['id', 'title', 'downloads_count']);

            return response()->json([
                'user_registrations' => $userRegistrations,
                'download_stats' => $downloadStats,
                'subscription_stats' => $subscriptionStats,
                'level_stats' => $levelStats,
                'top_tutorials' => $topTutorials,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur dans AdminDashboardController::getStats', ['error' => $e->getMessage()]);
            return response()->json([
                'user_registrations' => [],
                'download_stats' => [],
                'subscription_stats' => [],
                'level_stats' => [],
                'top_tutorials' => [],
            ]);
        }
    }

    /**
     * Get recent activity for the dashboard.
     */
    public function getRecentActivity(): JsonResponse
    {
        try {
            // Dernières inscriptions
            $recentUsers = User::with(['badges'])
                              ->latest()
                              ->limit(5)
                              ->get(['id', 'first_name', 'last_name', 'email', 'subscription_type', 'created_at']);

            // Derniers téléchargements
            $recentDownloads = Download::with(['user:id,first_name,last_name', 'tutorial:id,title'])
                                     ->latest()
                                     ->limit(10)
                                     ->get();

            // Derniers tutoriels publiés - Correction : utiliser is_draft au lieu de status
            $recentTutorials = Tutorial::where('is_draft', false)
                                     ->whereNotNull('published_at')
                                     ->latest('published_at')
                                     ->limit(5)
                                     ->get(['id', 'title', 'category_id', 'published_at']);

            // Analytics récentes
            $recentAnalytics = Analytics::with(['user:id,first_name,last_name'])
                                      ->latest()
                                      ->limit(10)
                                      ->get();

            return response()->json([
                'recent_users' => $recentUsers,
                'recent_downloads' => $recentDownloads,
                'recent_tutorials' => $recentTutorials,
                'recent_analytics' => $recentAnalytics,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur dans AdminDashboardController::getRecentActivity', ['error' => $e->getMessage()]);
            return response()->json([
                'recent_users' => [],
                'recent_downloads' => [],
                'recent_tutorials' => [],
                'recent_analytics' => [],
            ]);
        }
    }

    /**
     * Get system health metrics.
     */
    public function getSystemHealth(): JsonResponse
    {
        try {
            // Vérifications système
            $diskUsage = $this->getDiskUsage();
            $databaseSize = $this->getDatabaseSize();
            $errorLogs = $this->getRecentErrors();
            
            return response()->json([
                'disk_usage' => $diskUsage,
                'database_size' => $databaseSize,
                'recent_errors' => $errorLogs,
                'uptime' => $this->getUptime(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur dans AdminDashboardController::getSystemHealth', ['error' => $e->getMessage()]);
            return response()->json([
                'disk_usage' => ['used' => '0 B', 'free' => '0 B', 'total' => '0 B', 'percentage' => 0],
                'database_size' => ['size_mb' => 0, 'tables_count' => 0],
                'recent_errors' => [],
                'uptime' => 'N/A',
            ]);
        }
    }

    /**
     * Get disk usage information.
     */
    private function getDiskUsage(): array
    {
        try {
            $bytes = disk_free_space('/');
            $total = disk_total_space('/');
            
            if ($bytes === false || $total === false) {
                return ['used' => '0 B', 'free' => '0 B', 'total' => '0 B', 'percentage' => 0];
            }
            
            $used = $total - $bytes;
            
            return [
                'used' => $this->formatBytes($used),
                'free' => $this->formatBytes($bytes),
                'total' => $this->formatBytes($total),
                'percentage' => round(($used / $total) * 100, 2),
            ];
        } catch (\Exception $e) {
            return ['used' => '0 B', 'free' => '0 B', 'total' => '0 B', 'percentage' => 0];
        }
    }

    /**
     * Get database size information.
     */
    private function getDatabaseSize(): array
    {
        try {
            $result = \DB::select("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
            ");
            
            $tablesResult = \DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE()");
            
            return [
                'size_mb' => $result[0]->size_mb ?? 0,
                'tables_count' => $tablesResult[0]->count ?? 0,
            ];
        } catch (\Exception $e) {
            return ['size_mb' => 0, 'tables_count' => 0];
        }
    }

    /**
     * Get recent error logs.
     */
    private function getRecentErrors(): array
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (!file_exists($logFile)) {
                return [];
            }
            
            $lines = file($logFile);
            if ($lines === false) {
                return [];
            }
            
            // Récupérer les 10 dernières erreurs
            $errorLines = array_filter($lines, function($line) {
                return strpos($line, 'ERROR') !== false;
            });
            
            return array_slice(array_reverse($errorLines), 0, 10);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get system uptime.
     */
    private function getUptime(): string
    {
        try {
            if (PHP_OS_FAMILY === 'Linux') {
                $uptime = shell_exec('uptime -p');
                return trim($uptime) ?: 'N/A';
            }
            
            return 'N/A';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Paramètres système
     */
    public function settings()
    {
        return view('admin.settings.index');
    }

    /**
     * Mettre à jour les paramètres
     */
    public function updateSettings(Request $request)
    {
        // Logique de mise à jour des paramètres
        return redirect()->back()->with('success', 'Paramètres mis à jour avec succès');
    }

    /**
     * Vider le cache
     */
    public function clearCache()
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            
            return response()->json(['success' => true, 'message' => 'Cache vidé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors du vidage du cache']);
        }
    }

    /**
     * Afficher les logs
     */
    public function logs()
    {
        return view('admin.settings.logs');
    }

    /**
     * Display n8n MasterClass overview
     */
    public function masterclass()
    {
        return view('admin.masterclass');
    }

    /**
     * Display specific module details
     */
    public function masterclassModule(int $module)
    {
        return view('admin.masterclass.module', compact('module'));
    }

    /**
     * Display all workflows
     */
    public function masterclassWorkflows()
    {
        return view('admin.masterclass.workflows');
    }

    /**
     * Display video scripts
     */
    public function masterclassScripts()
    {
        return view('admin.masterclass.scripts');
    }

    /**
     * Display progress tracking
     */
    public function masterclassProgress()
    {
        return view('admin.masterclass.progress');
    }

    /**
     * Display quiz management
     */
    public function masterclassQuiz()
    {
        return view('admin.masterclass.quiz');
    }
}
