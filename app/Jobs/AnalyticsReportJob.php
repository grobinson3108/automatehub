<?php

namespace App\Jobs;

use App\Services\AnalyticsService;
use App\Services\NotificationService;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AnalyticsReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $reportType;
    protected array $parameters;
    protected ?int $adminUserId;

    /**
     * Nombre de tentatives maximum
     */
    public int $tries = 2;

    /**
     * Délai avant retry en secondes
     */
    public int $backoff = 300;

    /**
     * Timeout du job en secondes
     */
    public int $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(string $reportType, array $parameters = [], ?int $adminUserId = null)
    {
        $this->reportType = $reportType;
        $this->parameters = $parameters;
        $this->adminUserId = $adminUserId;
        $this->onQueue('reports');
    }

    /**
     * Execute the job.
     */
    public function handle(AnalyticsService $analyticsService): void
    {
        try {
            Log::info('Starting analytics report generation', [
                'report_type' => $this->reportType,
                'parameters' => $this->parameters,
                'admin_user_id' => $this->adminUserId
            ]);

            $reportData = $this->generateReport($analyticsService);
            $filePath = $this->saveReport($reportData);

            if ($this->adminUserId) {
                $this->notifyAdmin($filePath);
            }

            Log::info('Analytics report generated successfully', [
                'report_type' => $this->reportType,
                'file_path' => $filePath,
                'data_points' => count($reportData)
            ]);

        } catch (\Exception $e) {
            Log::error('Analytics report generation failed', [
                'report_type' => $this->reportType,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            throw $e;
        }
    }

    /**
     * Génère le rapport selon le type demandé
     */
    private function generateReport(AnalyticsService $analyticsService): array
    {
        $startDate = $this->parameters['start_date'] ?? now()->subDays(30);
        $endDate = $this->parameters['end_date'] ?? now();

        switch ($this->reportType) {
            case 'daily_summary':
                return $this->generateDailySummaryReport($analyticsService, $startDate, $endDate);
            
            case 'user_engagement':
                return $this->generateUserEngagementReport($analyticsService, $startDate, $endDate);
            
            case 'conversion_funnel':
                return $this->generateConversionFunnelReport($analyticsService, $startDate, $endDate);
            
            case 'content_performance':
                return $this->generateContentPerformanceReport($analyticsService, $startDate, $endDate);
            
            case 'revenue_analysis':
                return $this->generateRevenueAnalysisReport($analyticsService, $startDate, $endDate);
            
            case 'weekly_digest':
                return $this->generateWeeklyDigestReport($analyticsService);
            
            case 'monthly_summary':
                return $this->generateMonthlySummaryReport($analyticsService);
            
            default:
                throw new \InvalidArgumentException("Unknown report type: {$this->reportType}");
        }
    }

    /**
     * Génère un rapport de résumé quotidien
     */
    private function generateDailySummaryReport(AnalyticsService $analyticsService, $startDate, $endDate): array
    {
        $report = [
            'report_type' => 'daily_summary',
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'generated_at' => now(),
            'data' => []
        ];

        $currentDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        while ($currentDate->lte($endDate)) {
            $dayData = [
                'date' => $currentDate->format('Y-m-d'),
                'users' => [
                    'new_registrations' => User::whereDate('created_at', $currentDate)->count(),
                    'active_users' => User::whereDate('last_activity_at', $currentDate)->count(),
                ],
                'content' => [
                    'tutorials_viewed' => $analyticsService->getEventCount('tutorial_viewed', $currentDate, $currentDate->copy()->endOfDay()),
                    'downloads' => $analyticsService->getEventCount('download_completed', $currentDate, $currentDate->copy()->endOfDay()),
                ],
                'engagement' => [
                    'avg_session_duration' => $analyticsService->getAverageSessionDuration($currentDate),
                    'bounce_rate' => $analyticsService->getBounceRate($currentDate),
                ]
            ];

            $report['data'][] = $dayData;
            $currentDate->addDay();
        }

        return $report;
    }

    /**
     * Génère un rapport d'engagement utilisateur
     */
    private function generateUserEngagementReport(AnalyticsService $analyticsService, $startDate, $endDate): array
    {
        return [
            'report_type' => 'user_engagement',
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'generated_at' => now(),
            'data' => [
                'user_segments' => $analyticsService->getUserSegments($startDate, $endDate),
                'retention_rates' => $analyticsService->getRetentionRates($startDate, $endDate),
                'engagement_metrics' => $analyticsService->getEngagementMetrics($startDate, $endDate),
                'top_users' => $analyticsService->getTopActiveUsers($startDate, $endDate, 50),
                'churn_analysis' => $analyticsService->getChurnAnalysis($startDate, $endDate),
            ]
        ];
    }

    /**
     * Génère un rapport d'entonnoir de conversion
     */
    private function generateConversionFunnelReport(AnalyticsService $analyticsService, $startDate, $endDate): array
    {
        return [
            'report_type' => 'conversion_funnel',
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'generated_at' => now(),
            'data' => [
                'funnel_steps' => [
                    'visitors' => $analyticsService->getUniqueVisitors($startDate, $endDate),
                    'registrations' => $analyticsService->getRegistrations($startDate, $endDate),
                    'first_tutorial_view' => $analyticsService->getFirstTutorialViews($startDate, $endDate),
                    'first_download' => $analyticsService->getFirstDownloads($startDate, $endDate),
                    'premium_upgrades' => $analyticsService->getPremiumUpgrades($startDate, $endDate),
                ],
                'conversion_rates' => $analyticsService->getConversionRates($startDate, $endDate),
                'drop_off_analysis' => $analyticsService->getDropOffAnalysis($startDate, $endDate),
            ]
        ];
    }

    /**
     * Génère un rapport de performance du contenu
     */
    private function generateContentPerformanceReport(AnalyticsService $analyticsService, $startDate, $endDate): array
    {
        return [
            'report_type' => 'content_performance',
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'generated_at' => now(),
            'data' => [
                'top_tutorials' => $analyticsService->getTopTutorials($startDate, $endDate, 20),
                'tutorial_completion_rates' => $analyticsService->getTutorialCompletionRates($startDate, $endDate),
                'download_statistics' => $analyticsService->getDownloadStatistics($startDate, $endDate),
                'category_performance' => $analyticsService->getCategoryPerformance($startDate, $endDate),
                'search_analytics' => $analyticsService->getSearchAnalytics($startDate, $endDate),
            ]
        ];
    }

    /**
     * Génère un rapport d'analyse des revenus
     */
    private function generateRevenueAnalysisReport(AnalyticsService $analyticsService, $startDate, $endDate): array
    {
        return [
            'report_type' => 'revenue_analysis',
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'generated_at' => now(),
            'data' => [
                'subscription_metrics' => $analyticsService->getSubscriptionMetrics($startDate, $endDate),
                'revenue_by_plan' => $analyticsService->getRevenueByPlan($startDate, $endDate),
                'upgrade_patterns' => $analyticsService->getUpgradePatterns($startDate, $endDate),
                'churn_revenue_impact' => $analyticsService->getChurnRevenueImpact($startDate, $endDate),
                'ltv_analysis' => $analyticsService->getLTVAnalysis($startDate, $endDate),
            ]
        ];
    }

    /**
     * Génère un rapport de digest hebdomadaire
     */
    private function generateWeeklyDigestReport(AnalyticsService $analyticsService): array
    {
        $startDate = now()->startOfWeek()->subWeek();
        $endDate = now()->startOfWeek()->subSecond();

        return [
            'report_type' => 'weekly_digest',
            'week_of' => $startDate->format('Y-m-d'),
            'generated_at' => now(),
            'data' => [
                'summary' => $analyticsService->getWeeklySummary($startDate, $endDate),
                'highlights' => $analyticsService->getWeeklyHighlights($startDate, $endDate),
                'growth_metrics' => $analyticsService->getGrowthMetrics($startDate, $endDate),
                'user_activity' => $analyticsService->getWeeklyUserActivity($startDate, $endDate),
                'content_highlights' => $analyticsService->getWeeklyContentHighlights($startDate, $endDate),
            ]
        ];
    }

    /**
     * Génère un rapport de résumé mensuel
     */
    private function generateMonthlySummaryReport(AnalyticsService $analyticsService): array
    {
        $startDate = now()->startOfMonth()->subMonth();
        $endDate = now()->startOfMonth()->subSecond();

        return [
            'report_type' => 'monthly_summary',
            'month_of' => $startDate->format('Y-m'),
            'generated_at' => now(),
            'data' => [
                'executive_summary' => $analyticsService->getExecutiveSummary($startDate, $endDate),
                'key_metrics' => $analyticsService->getKeyMetrics($startDate, $endDate),
                'user_growth' => $analyticsService->getUserGrowth($startDate, $endDate),
                'revenue_summary' => $analyticsService->getRevenueSummary($startDate, $endDate),
                'content_performance' => $analyticsService->getMonthlyContentPerformance($startDate, $endDate),
                'goals_tracking' => $analyticsService->getGoalsTracking($startDate, $endDate),
            ]
        ];
    }

    /**
     * Sauvegarde le rapport dans un fichier
     */
    private function saveReport(array $reportData): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "analytics_report_{$this->reportType}_{$timestamp}.json";
        $filePath = "reports/analytics/{$filename}";

        Storage::disk('local')->put($filePath, json_encode($reportData, JSON_PRETTY_PRINT));

        return $filePath;
    }

    /**
     * Notifie l'administrateur que le rapport est prêt
     */
    private function notifyAdmin(string $filePath): void
    {
        if (!$this->adminUserId) {
            return;
        }

        try {
            $admin = User::find($this->adminUserId);
            if (!$admin) {
                return;
            }

            $notificationService = app(NotificationService::class);
            
            // Ici on pourrait envoyer un email avec le lien de téléchargement
            // Pour l'instant, on log juste l'information
            Log::info('Analytics report ready for admin', [
                'admin_id' => $this->adminUserId,
                'report_type' => $this->reportType,
                'file_path' => $filePath
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to notify admin about report', [
                'admin_id' => $this->adminUserId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('AnalyticsReportJob failed permanently', [
            'report_type' => $this->reportType,
            'parameters' => $this->parameters,
            'admin_user_id' => $this->adminUserId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        $tags = ['analytics', 'report', 'type:' . $this->reportType];

        if ($this->adminUserId) {
            $tags[] = 'admin:' . $this->adminUserId;
        }

        return $tags;
    }
}
