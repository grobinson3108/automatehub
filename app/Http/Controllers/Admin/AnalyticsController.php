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
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the main analytics dashboard.
     */
    public function dashboard()
    {
        try {
            // Métriques générales
            $generalMetrics = [
                'total_users' => User::count(),
                'total_tutorials' => Tutorial::count(),
                'total_downloads' => Download::count(),
                'total_revenue' => $this->calculateTotalRevenue(),
            ];

            // Métriques de croissance (30 derniers jours)
            $growthMetrics = $this->getGrowthMetrics();

            // Top performers
            $topPerformers = [
                'top_tutorials' => Tutorial::withCount('downloads')
                    ->orderBy('downloads_count', 'desc')
                    ->limit(5)
                    ->get(['id', 'title', 'downloads_count']),
                'top_users' => User::withCount('downloads')
                    ->orderBy('downloads_count', 'desc')
                    ->limit(5)
                    ->get(['id', 'first_name', 'last_name', 'email', 'downloads_count']),
            ];

            return view('admin.analytics.dashboard', compact('generalMetrics', 'growthMetrics', 'topPerformers'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans AnalyticsController::dashboard', ['error' => $e->getMessage()]);
            
            // Valeurs par défaut en cas d'erreur
            $generalMetrics = ['total_users' => 0, 'total_tutorials' => 0, 'total_downloads' => 0, 'total_revenue' => 0];
            $growthMetrics = ['users_growth' => 0, 'downloads_growth' => 0, 'tutorials_growth' => 0];
            $topPerformers = ['top_tutorials' => collect(), 'top_users' => collect()];

            return view('admin.analytics.dashboard', compact('generalMetrics', 'growthMetrics', 'topPerformers'));
        }
    }

    /**
     * Display finance dashboard.
     */
    public function financeDashboard()
    {
        try {
            // Revenus actuels
            $currentRevenue = [
                'mrr' => $this->calculateTotalRevenue(),
                'arr' => $this->calculateTotalRevenue() * 12,
                'premium_users' => User::where('subscription_type', 'premium')->count(),
                'pro_users' => User::where('subscription_type', 'pro')->count(),
            ];

            // Évolution des revenus (30 derniers jours)
            $revenueEvolution = $this->getRevenueEvolution();

            // Métriques financières
            $financialMetrics = [
                'arpu' => $this->calculateARPU(),
                'ltv' => $this->calculateLTV(),
                'churn_rate' => $this->calculateChurnRate(),
                'conversion_rate' => $this->calculateConversionRate(
                    User::where('subscription_type', 'free')->count(),
                    User::whereIn('subscription_type', ['premium', 'pro'])->count()
                ),
            ];

            // Prévisions
            $forecasts = $this->calculateRevenueForecasts();

            return view('admin.finances.dashboard', compact('currentRevenue', 'revenueEvolution', 'financialMetrics', 'forecasts'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans AnalyticsController::financeDashboard', ['error' => $e->getMessage()]);
            
            // Valeurs par défaut
            $currentRevenue = ['mrr' => 0, 'arr' => 0, 'premium_users' => 0, 'pro_users' => 0];
            $revenueEvolution = [];
            $financialMetrics = ['arpu' => 0, 'ltv' => 0, 'churn_rate' => 0, 'conversion_rate' => 0];
            $forecasts = ['next_month' => 0, 'next_quarter' => 0, 'next_year' => 0];

            return view('admin.finances.dashboard', compact('currentRevenue', 'revenueEvolution', 'financialMetrics', 'forecasts'));
        }
    }

    /**
     * Display detailed user analytics.
     */
    public function users(Request $request)
    {
        try {
            $period = $request->get('period', '30');
            $startDate = Carbon::now()->subDays($period);

            // Évolution des inscriptions
            $registrationTrend = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Répartition par abonnement
            $subscriptionDistribution = User::selectRaw('subscription_type, COUNT(*) as count')
                ->groupBy('subscription_type')
                ->get();

            // Répartition par niveau n8n
            $levelDistribution = User::selectRaw('level_n8n, COUNT(*) as count')
                ->whereNotNull('level_n8n')
                ->groupBy('level_n8n')
                ->get();

            // Répartition pro/particulier
            $audienceDistribution = User::selectRaw('is_professional, COUNT(*) as count')
                ->groupBy('is_professional')
                ->get();

            // Utilisateurs actifs par période
            $activityMetrics = [
                'daily_active' => User::where('last_activity_at', '>=', Carbon::now()->subDay())->count(),
                'weekly_active' => User::where('last_activity_at', '>=', Carbon::now()->subWeek())->count(),
                'monthly_active' => User::where('last_activity_at', '>=', Carbon::now()->subMonth())->count(),
            ];

            // Taux de rétention
            $retentionRates = $this->calculateRetentionRates();

            return view('admin.analytics.users', compact(
                'registrationTrend',
                'subscriptionDistribution',
                'levelDistribution',
                'audienceDistribution',
                'activityMetrics',
                'retentionRates'
            ));
        } catch (\Exception $e) {
            \Log::error('Erreur dans AnalyticsController::users', ['error' => $e->getMessage()]);
            
            // Valeurs par défaut
            $registrationTrend = collect();
            $subscriptionDistribution = collect();
            $levelDistribution = collect();
            $audienceDistribution = collect();
            $activityMetrics = ['daily_active' => 0, 'weekly_active' => 0, 'monthly_active' => 0];
            $retentionRates = ['weekly' => 0, 'monthly' => 0];

            return view('admin.analytics.users', compact(
                'registrationTrend',
                'subscriptionDistribution',
                'levelDistribution',
                'audienceDistribution',
                'activityMetrics',
                'retentionRates'
            ));
        }
    }

    /**
     * Display content performance analytics.
     */
    public function content(Request $request)
    {
        try {
            $period = $request->get('period', '30');
            $startDate = Carbon::now()->subDays($period);

            // Performance des tutoriels
            $tutorialPerformance = Tutorial::with(['category'])
                ->withCount([
                    'downloads' => function($query) use ($startDate) {
                        $query->where('downloaded_at', '>=', $startDate);
                    },
                    'favorites',
                    'tutorialProgress as completions' => function($query) {
                        $query->where('completed', true);
                    }
                ])
                ->orderBy('downloads_count', 'desc')
                ->limit(20)
                ->get();

            // Tendances de téléchargement
            $downloadTrends = Download::selectRaw('DATE(downloaded_at) as date, COUNT(*) as count')
                ->where('downloaded_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Taux de completion
            $completionRates = $this->calculateCompletionRates();

            return view('admin.analytics.content', compact(
                'tutorialPerformance',
                'downloadTrends',
                'completionRates'
            ));
        } catch (\Exception $e) {
            \Log::error('Erreur dans AnalyticsController::content', ['error' => $e->getMessage()]);
            
            // Valeurs par défaut
            $tutorialPerformance = collect();
            $downloadTrends = collect();
            $completionRates = ['overall_rate' => 0, 'by_difficulty' => collect()];

            return view('admin.analytics.content', compact(
                'tutorialPerformance',
                'downloadTrends',
                'completionRates'
            ));
        }
    }

    /**
     * Display conversion funnel analytics.
     */
    public function conversions(Request $request)
    {
        try {
            $period = $request->get('period', '30');
            $startDate = Carbon::now()->subDays($period);

            // Entonnoir de conversion
            $conversionFunnel = [
                'visitors' => $this->getVisitorCount($startDate),
                'registrations' => User::where('created_at', '>=', $startDate)->count(),
                'first_download' => User::whereHas('downloads', function($query) use ($startDate) {
                    $query->where('downloaded_at', '>=', $startDate);
                })->count(),
                'premium_upgrades' => User::where('subscription_type', 'premium')
                    ->where('updated_at', '>=', $startDate)
                    ->count(),
                'pro_upgrades' => User::where('subscription_type', 'pro')
                    ->where('updated_at', '>=', $startDate)
                    ->count(),
            ];

            // Taux de conversion
            $conversionRates = [
                'visitor_to_registration' => $this->calculateConversionRate($conversionFunnel['visitors'], $conversionFunnel['registrations']),
                'registration_to_download' => $this->calculateConversionRate($conversionFunnel['registrations'], $conversionFunnel['first_download']),
                'free_to_premium' => $this->calculateConversionRate(
                    User::where('subscription_type', 'free')->count(),
                    $conversionFunnel['premium_upgrades']
                ),
            ];

            // Analyse des abandons
            $dropoffAnalysis = [
                'registered_no_download' => User::whereDoesntHave('downloads')->count(),
                'downloaded_no_completion' => User::whereHas('downloads')
                    ->whereDoesntHave('tutorialProgress', function($query) {
                        $query->where('completed', true);
                    })->count(),
            ];

            return view('admin.analytics.conversions', compact(
                'conversionFunnel',
                'conversionRates',
                'dropoffAnalysis'
            ));
        } catch (\Exception $e) {
            \Log::error('Erreur dans AnalyticsController::conversions', ['error' => $e->getMessage()]);
            
            // Valeurs par défaut
            $conversionFunnel = ['visitors' => 0, 'registrations' => 0, 'first_download' => 0, 'premium_upgrades' => 0, 'pro_upgrades' => 0];
            $conversionRates = ['visitor_to_registration' => 0, 'registration_to_download' => 0, 'free_to_premium' => 0];
            $dropoffAnalysis = ['registered_no_download' => 0, 'downloaded_no_completion' => 0];

            return view('admin.analytics.conversions', compact(
                'conversionFunnel',
                'conversionRates',
                'dropoffAnalysis'
            ));
        }
    }

    /**
     * Display revenue analytics.
     */
    public function revenue(Request $request)
    {
        try {
            $period = $request->get('period', '30');
            $startDate = Carbon::now()->subDays($period);

            // Revenus par période
            $revenueByPeriod = $this->calculateRevenueByPeriod($startDate);

            // Revenus par type d'abonnement
            $revenueBySubscription = [
                'premium' => User::where('subscription_type', 'premium')->count() * 19.99,
                'pro' => User::where('subscription_type', 'pro')->count() * 49.99,
            ];

            // MRR (Monthly Recurring Revenue)
            $mrr = $revenueBySubscription['premium'] + $revenueBySubscription['pro'];

            // ARR (Annual Recurring Revenue)
            $arr = $mrr * 12;

            // ARPU (Average Revenue Per User)
            $totalPaidUsers = User::whereIn('subscription_type', ['premium', 'pro'])->count();
            $arpu = $totalPaidUsers > 0 ? $mrr / $totalPaidUsers : 0;

            // Churn rate (taux d'attrition)
            $churnRate = $this->calculateChurnRate();

            // LTV (Customer Lifetime Value)
            $ltv = $churnRate > 0 ? $arpu / $churnRate : 0;

            // Prévisions de revenus
            $revenueForecasts = $this->calculateRevenueForecasts();

            return view('admin.analytics.revenue', compact(
                'revenueByPeriod',
                'revenueBySubscription',
                'mrr',
                'arr',
                'arpu',
                'churnRate',
                'ltv',
                'revenueForecasts'
            ));
        } catch (\Exception $e) {
            \Log::error('Erreur dans AnalyticsController::revenue', ['error' => $e->getMessage()]);
            
            // Valeurs par défaut
            $revenueByPeriod = ['premium' => collect(), 'pro' => collect()];
            $revenueBySubscription = ['premium' => 0, 'pro' => 0];
            $mrr = 0;
            $arr = 0;
            $arpu = 0;
            $churnRate = 0;
            $ltv = 0;
            $revenueForecasts = ['next_month' => 0, 'next_quarter' => 0, 'next_year' => 0];

            return view('admin.analytics.revenue', compact(
                'revenueByPeriod',
                'revenueBySubscription',
                'mrr',
                'arr',
                'arpu',
                'churnRate',
                'ltv',
                'revenueForecasts'
            ));
        }
    }

    /**
     * Calculate total revenue.
     */
    private function calculateTotalRevenue(): float
    {
        try {
            $premiumUsers = User::where('subscription_type', 'premium')->count();
            $proUsers = User::where('subscription_type', 'pro')->count();
            
            return ($premiumUsers * 19.99) + ($proUsers * 49.99);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calculate ARPU.
     */
    private function calculateARPU(): float
    {
        try {
            $totalPaidUsers = User::whereIn('subscription_type', ['premium', 'pro'])->count();
            $totalRevenue = $this->calculateTotalRevenue();
            
            return $totalPaidUsers > 0 ? round($totalRevenue / $totalPaidUsers, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calculate LTV.
     */
    private function calculateLTV(): float
    {
        try {
            $arpu = $this->calculateARPU();
            $churnRate = $this->calculateChurnRate();
            
            return $churnRate > 0 ? round($arpu / ($churnRate / 100), 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get revenue evolution.
     */
    private function getRevenueEvolution(): array
    {
        try {
            $thirtyDaysAgo = Carbon::now()->subDays(30);
            
            return User::selectRaw('DATE(updated_at) as date, 
                SUM(CASE WHEN subscription_type = "premium" THEN 19.99 ELSE 0 END) +
                SUM(CASE WHEN subscription_type = "pro" THEN 49.99 ELSE 0 END) as revenue')
                ->where('updated_at', '>=', $thirtyDaysAgo)
                ->whereIn('subscription_type', ['premium', 'pro'])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get growth metrics for the last 30 days.
     */
    private function getGrowthMetrics(): array
    {
        try {
            $thirtyDaysAgo = Carbon::now()->subDays(30);
            $sixtyDaysAgo = Carbon::now()->subDays(60);

            $currentPeriod = [
                'users' => User::where('created_at', '>=', $thirtyDaysAgo)->count(),
                'downloads' => Download::where('downloaded_at', '>=', $thirtyDaysAgo)->count(),
                'tutorials' => Tutorial::where('created_at', '>=', $thirtyDaysAgo)->count(),
            ];

            $previousPeriod = [
                'users' => User::whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])->count(),
                'downloads' => Download::whereBetween('downloaded_at', [$sixtyDaysAgo, $thirtyDaysAgo])->count(),
                'tutorials' => Tutorial::whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])->count(),
            ];

            return [
                'users_growth' => $this->calculateGrowthRate($previousPeriod['users'], $currentPeriod['users']),
                'downloads_growth' => $this->calculateGrowthRate($previousPeriod['downloads'], $currentPeriod['downloads']),
                'tutorials_growth' => $this->calculateGrowthRate($previousPeriod['tutorials'], $currentPeriod['tutorials']),
            ];
        } catch (\Exception $e) {
            return ['users_growth' => 0, 'downloads_growth' => 0, 'tutorials_growth' => 0];
        }
    }

    /**
     * Calculate growth rate percentage.
     */
    private function calculateGrowthRate(int $previous, int $current): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Calculate retention rates.
     */
    private function calculateRetentionRates(): array
    {
        try {
            // Utilisateurs inscrits il y a 7 jours et encore actifs
            $weeklyRetention = User::where('created_at', '<=', Carbon::now()->subDays(7))
                ->where('last_activity_at', '>=', Carbon::now()->subDays(7))
                ->count();
            
            $totalWeekOldUsers = User::where('created_at', '<=', Carbon::now()->subDays(7))->count();

            // Utilisateurs inscrits il y a 30 jours et encore actifs
            $monthlyRetention = User::where('created_at', '<=', Carbon::now()->subDays(30))
                ->where('last_activity_at', '>=', Carbon::now()->subDays(30))
                ->count();
            
            $totalMonthOldUsers = User::where('created_at', '<=', Carbon::now()->subDays(30))->count();

            return [
                'weekly' => $totalWeekOldUsers > 0 ? round(($weeklyRetention / $totalWeekOldUsers) * 100, 2) : 0,
                'monthly' => $totalMonthOldUsers > 0 ? round(($monthlyRetention / $totalMonthOldUsers) * 100, 2) : 0,
            ];
        } catch (\Exception $e) {
            return ['weekly' => 0, 'monthly' => 0];
        }
    }

    /**
     * Calculate completion rates for tutorials.
     */
    private function calculateCompletionRates(): array
    {
        try {
            $tutorials = Tutorial::withCount([
                'tutorialProgress as total_started',
                'tutorialProgress as total_completed' => function($query) {
                    $query->where('completed', true);
                }
            ])->get();

            $overallStarted = $tutorials->sum('total_started');
            $overallCompleted = $tutorials->sum('total_completed');

            return [
                'overall_rate' => $overallStarted > 0 ? round(($overallCompleted / $overallStarted) * 100, 2) : 0,
                'by_difficulty' => $tutorials->groupBy('required_level')->map(function($group) {
                    $started = $group->sum('total_started');
                    $completed = $group->sum('total_completed');
                    return $started > 0 ? round(($completed / $started) * 100, 2) : 0;
                }),
            ];
        } catch (\Exception $e) {
            return ['overall_rate' => 0, 'by_difficulty' => collect()];
        }
    }

    /**
     * Calculate conversion rate.
     */
    private function calculateConversionRate(int $total, int $converted): float
    {
        return $total > 0 ? round(($converted / $total) * 100, 2) : 0;
    }

    /**
     * Get visitor count (placeholder - à intégrer avec Google Analytics).
     */
    private function getVisitorCount(Carbon $startDate): int
    {
        // Placeholder - à remplacer par l'intégration Google Analytics
        return User::where('created_at', '>=', $startDate)->count() * 5; // Estimation
    }

    /**
     * Calculate revenue by period.
     */
    private function calculateRevenueByPeriod(Carbon $startDate): array
    {
        try {
            // Simulation basée sur les upgrades d'abonnement
            $premiumUpgrades = User::where('subscription_type', 'premium')
                ->where('updated_at', '>=', $startDate)
                ->selectRaw('DATE(updated_at) as date, COUNT(*) * 19.99 as revenue')
                ->groupBy('date')
                ->get();

            $proUpgrades = User::where('subscription_type', 'pro')
                ->where('updated_at', '>=', $startDate)
                ->selectRaw('DATE(updated_at) as date, COUNT(*) * 49.99 as revenue')
                ->groupBy('date')
                ->get();

            return [
                'premium' => $premiumUpgrades,
                'pro' => $proUpgrades,
            ];
        } catch (\Exception $e) {
            return ['premium' => collect(), 'pro' => collect()];
        }
    }

    /**
     * Calculate churn rate.
     */
    private function calculateChurnRate(): float
    {
        try {
            // Simulation - à adapter selon la logique métier
            $totalPaidUsers = User::whereIn('subscription_type', ['premium', 'pro'])->count();
            $inactiveUsers = User::whereIn('subscription_type', ['premium', 'pro'])
                ->where('last_activity_at', '<', Carbon::now()->subDays(30))
                ->count();

            return $totalPaidUsers > 0 ? round(($inactiveUsers / $totalPaidUsers) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calculate revenue forecasts.
     */
    private function calculateRevenueForecasts(): array
    {
        try {
            $currentMrr = $this->calculateTotalRevenue();
            $growthRate = 0.05; // 5% de croissance mensuelle estimée

            return [
                'next_month' => round($currentMrr * (1 + $growthRate), 2),
                'next_quarter' => round($currentMrr * pow(1 + $growthRate, 3), 2),
                'next_year' => round($currentMrr * pow(1 + $growthRate, 12), 2),
            ];
        } catch (\Exception $e) {
            return ['next_month' => 0, 'next_quarter' => 0, 'next_year' => 0];
        }
    }
}
