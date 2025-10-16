<?php

namespace App\Console\Commands;

use App\Models\Analytics;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupOldAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cleanup:old-analytics 
                            {--days=90 : Nombre de jours à conserver (défaut: 90)}
                            {--archive : Archiver les données avant suppression}
                            {--dry-run : Simulation sans suppression réelle}
                            {--batch-size=1000 : Nombre d\'enregistrements à traiter par lot}
                            {--keep-important : Conserver les événements importants}';

    /**
     * The console command description.
     */
    protected $description = 'Nettoie les anciennes données d\'analytics pour optimiser les performances';

    /**
     * Types d'événements considérés comme importants
     */
    private array $importantEvents = [
        'user_registered',
        'subscription_upgraded',
        'subscription_downgraded',
        'tutorial_completed',
        'badge_earned',
        'payment_completed'
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $archive = $this->option('archive');
        $dryRun = $this->option('dry-run');
        $batchSize = (int) $this->option('batch-size');
        $keepImportant = $this->option('keep-important');

        if ($days < 30) {
            $this->error('Le nombre de jours doit être d\'au moins 30 pour éviter la perte de données importantes');
            return Command::FAILURE;
        }

        $cutoffDate = now()->subDays($days);
        
        $this->info("Nettoyage des données analytics antérieures au {$cutoffDate->format('Y-m-d H:i:s')}");
        
        if ($dryRun) {
            $this->warn('Mode simulation activé - aucune donnée ne sera réellement supprimée');
        }

        if ($keepImportant) {
            $this->info('Les événements importants seront conservés');
        }

        // Analyser les données à nettoyer
        $analysisResult = $this->analyzeDataToCleanup($cutoffDate, $keepImportant);
        $this->displayAnalysis($analysisResult);

        if (!$dryRun && !$this->confirm('Voulez-vous continuer avec le nettoyage ?')) {
            $this->info('Nettoyage annulé');
            return Command::SUCCESS;
        }

        // Archiver si demandé
        if ($archive && !$dryRun) {
            $this->info('Archivage des données...');
            $archiveResult = $this->archiveOldData($cutoffDate, $keepImportant);
            
            if ($archiveResult) {
                $this->info('✅ Données archivées avec succès');
            } else {
                $this->error('❌ Erreur lors de l\'archivage');
                return Command::FAILURE;
            }
        }

        // Nettoyer les données
        if (!$dryRun) {
            $deletedCount = $this->cleanupOldData($cutoffDate, $batchSize, $keepImportant);
            $this->info("✅ {$deletedCount} enregistrements supprimés");
        }

        // Optimiser la table
        if (!$dryRun) {
            $this->info('Optimisation de la table analytics...');
            $this->optimizeTable();
            $this->info('✅ Table optimisée');
        }

        // Nettoyer les anciens fichiers de rapports
        if (!$dryRun) {
            $this->info('Nettoyage des anciens fichiers de rapports...');
            $cleanedFiles = $this->cleanupOldReports($days);
            $this->info("✅ {$cleanedFiles} fichiers de rapports supprimés");
        }

        $this->info('Nettoyage terminé avec succès !');
        return Command::SUCCESS;
    }

    /**
     * Analyse les données à nettoyer
     */
    private function analyzeDataToCleanup(Carbon $cutoffDate, bool $keepImportant): array
    {
        $query = Analytics::where('created_at', '<', $cutoffDate);

        if ($keepImportant) {
            $query->whereNotIn('event_type', $this->importantEvents);
        }

        $totalToDelete = $query->count();
        $totalSize = $query->sum(DB::raw('LENGTH(event_data)')) / 1024 / 1024; // MB

        // Analyser par type d'événement
        $eventTypes = Analytics::where('created_at', '<', $cutoffDate)
            ->when($keepImportant, function ($q) {
                return $q->whereNotIn('event_type', $this->importantEvents);
            })
            ->select('event_type', DB::raw('COUNT(*) as count'))
            ->groupBy('event_type')
            ->orderBy('count', 'desc')
            ->get();

        // Analyser par mois
        $monthlyData = Analytics::where('created_at', '<', $cutoffDate)
            ->when($keepImportant, function ($q) {
                return $q->whereNotIn('event_type', $this->importantEvents);
            })
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return [
            'total_records' => $totalToDelete,
            'estimated_size_mb' => round($totalSize, 2),
            'event_types' => $eventTypes,
            'monthly_data' => $monthlyData,
            'cutoff_date' => $cutoffDate
        ];
    }

    /**
     * Affiche l'analyse des données
     */
    private function displayAnalysis(array $analysis): void
    {
        $this->line('=== ANALYSE DES DONNÉES À NETTOYER ===');
        $this->line("Enregistrements à supprimer : {$analysis['total_records']}");
        $this->line("Taille estimée : {$analysis['estimated_size_mb']} MB");
        $this->line("Date limite : {$analysis['cutoff_date']->format('Y-m-d H:i:s')}");

        if ($analysis['event_types']->isNotEmpty()) {
            $this->line('');
            $this->info('Répartition par type d\'événement :');
            foreach ($analysis['event_types'] as $eventType) {
                $this->line("  - {$eventType->event_type}: {$eventType->count} enregistrements");
            }
        }

        if ($analysis['monthly_data']->isNotEmpty()) {
            $this->line('');
            $this->info('Répartition par mois (12 derniers mois) :');
            foreach ($analysis['monthly_data'] as $monthData) {
                $this->line("  - {$monthData->year}-{$monthData->month}: {$monthData->count} enregistrements");
            }
        }
    }

    /**
     * Archive les anciennes données
     */
    private function archiveOldData(Carbon $cutoffDate, bool $keepImportant): bool
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "analytics_archive_{$timestamp}.json";
            $filePath = "archives/analytics/{$filename}";

            $query = Analytics::where('created_at', '<', $cutoffDate);

            if ($keepImportant) {
                $query->whereNotIn('event_type', $this->importantEvents);
            }

            $archiveData = [
                'archived_at' => now(),
                'cutoff_date' => $cutoffDate,
                'keep_important' => $keepImportant,
                'total_records' => $query->count(),
                'data' => []
            ];

            // Traiter par lots pour éviter les problèmes de mémoire
            $query->chunk(1000, function ($records) use (&$archiveData) {
                foreach ($records as $record) {
                    $archiveData['data'][] = [
                        'id' => $record->id,
                        'user_id' => $record->user_id,
                        'event_type' => $record->event_type,
                        'event_data' => $record->event_data,
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at
                    ];
                }
            });

            Storage::disk('local')->put($filePath, json_encode($archiveData, JSON_PRETTY_PRINT));

            $this->line("Archive créée : storage/app/{$filePath}");
            return true;

        } catch (\Exception $e) {
            $this->error('Erreur lors de l\'archivage : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Nettoie les anciennes données
     */
    private function cleanupOldData(Carbon $cutoffDate, int $batchSize, bool $keepImportant): int
    {
        $totalDeleted = 0;

        $query = Analytics::where('created_at', '<', $cutoffDate);

        if ($keepImportant) {
            $query->whereNotIn('event_type', $this->importantEvents);
        }

        $totalRecords = $query->count();
        $progressBar = $this->output->createProgressBar($totalRecords);
        $progressBar->start();

        do {
            $batch = $query->limit($batchSize)->get();
            
            if ($batch->isEmpty()) {
                break;
            }

            $ids = $batch->pluck('id')->toArray();
            $deleted = Analytics::whereIn('id', $ids)->delete();
            
            $totalDeleted += $deleted;
            $progressBar->advance($deleted);

        } while ($batch->count() === $batchSize);

        $progressBar->finish();
        $this->newLine();

        return $totalDeleted;
    }

    /**
     * Optimise la table analytics
     */
    private function optimizeTable(): void
    {
        try {
            DB::statement('OPTIMIZE TABLE analytics');
        } catch (\Exception $e) {
            $this->warn('Impossible d\'optimiser la table : ' . $e->getMessage());
        }
    }

    /**
     * Nettoie les anciens fichiers de rapports
     */
    private function cleanupOldReports(int $days): int
    {
        $cutoffDate = now()->subDays($days);
        $cleanedFiles = 0;

        try {
            $reportDirectories = ['reports/analytics', 'archives/analytics'];

            foreach ($reportDirectories as $directory) {
                if (!Storage::disk('local')->exists($directory)) {
                    continue;
                }

                $files = Storage::disk('local')->files($directory);

                foreach ($files as $file) {
                    $lastModified = Storage::disk('local')->lastModified($file);
                    
                    if ($lastModified < $cutoffDate->timestamp) {
                        Storage::disk('local')->delete($file);
                        $cleanedFiles++;
                    }
                }
            }

        } catch (\Exception $e) {
            $this->warn('Erreur lors du nettoyage des fichiers : ' . $e->getMessage());
        }

        return $cleanedFiles;
    }
}
