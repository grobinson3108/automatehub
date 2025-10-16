<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CleanLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clean 
                            {--days=30 : Nombre de jours à conserver}
                            {--dry-run : Afficher ce qui serait supprimé sans le faire}
                            {--size-limit=100 : Taille max en MB avant nettoyage forcé}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoie les anciens fichiers de logs pour libérer l\'espace disque';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $sizeLimitMB = (int) $this->option('size-limit');
        
        $this->info("🧹 Nettoyage des logs (conservation: {$days} jours, limite: {$sizeLimitMB}MB)");
        
        if ($dryRun) {
            $this->warn('⚠️  Mode simulation - aucun fichier ne sera supprimé');
        }

        $logPaths = [
            storage_path('logs'),
            '/var/log', // Logs système (si accessibles)
        ];

        $totalCleaned = 0;
        $totalSize = 0;

        foreach ($logPaths as $logPath) {
            if (!File::exists($logPath)) {
                continue;
            }

            $this->line("\n📁 Analyse du répertoire: {$logPath}");
            
            try {
                $result = $this->cleanDirectory($logPath, $days, $sizeLimitMB, $dryRun);
                $totalCleaned += $result['files'];
                $totalSize += $result['size'];
                
                $this->info("   ✅ {$result['files']} fichiers, " . $this->formatBytes($result['size']) . " libérés");
                
            } catch (\Exception $e) {
                $this->error("   ❌ Erreur: " . $e->getMessage());
            }
        }

        // Nettoyage spécifique Laravel
        $this->line("\n📋 Nettoyage spécifique Laravel:");
        $laravelResult = $this->cleanLaravelLogs($days, $dryRun);
        $totalCleaned += $laravelResult['files'];
        $totalSize += $laravelResult['size'];

        // Résumé
        $this->line("\n" . str_repeat('=', 50));
        $this->info("🎯 Résumé du nettoyage:");
        $this->line("   📄 Fichiers supprimés: {$totalCleaned}");
        $this->line("   💾 Espace libéré: " . $this->formatBytes($totalSize));
        
        if (!$dryRun) {
            // Logger l'opération
            Log::info('Logs cleanup completed', [
                'files_cleaned' => $totalCleaned,
                'size_freed' => $totalSize,
                'retention_days' => $days
            ]);
        }

        return Command::SUCCESS;
    }

    /**
     * Nettoie un répertoire de logs
     */
    private function cleanDirectory(string $path, int $days, int $sizeLimitMB, bool $dryRun): array
    {
        $files = File::allFiles($path);
        $cutoffDate = now()->subDays($days);
        $sizeLimitBytes = $sizeLimitMB * 1024 * 1024;
        
        $cleaned = 0;
        $sizeFreed = 0;
        $currentSize = 0;

        // Calculer la taille totale actuelle
        foreach ($files as $file) {
            if ($this->isLogFile($file->getPathname())) {
                $currentSize += $file->getSize();
            }
        }

        $this->line("   📊 Taille actuelle: " . $this->formatBytes($currentSize));

        foreach ($files as $file) {
            $filePath = $file->getPathname();
            
            if (!$this->isLogFile($filePath)) {
                continue;
            }

            $fileTime = File::lastModified($filePath);
            $fileSize = File::size($filePath);
            $shouldDelete = false;
            $reason = '';

            // Critères de suppression
            if ($fileTime < $cutoffDate->timestamp) {
                $shouldDelete = true;
                $reason = 'ancien (' . now()->createFromTimestamp($fileTime)->diffForHumans() . ')';
            } elseif ($currentSize > $sizeLimitBytes && $this->isRotatableLog($filePath)) {
                $shouldDelete = true;
                $reason = 'limite de taille dépassée';
            } elseif ($fileSize > 50 * 1024 * 1024) { // Fichiers > 50MB
                $shouldDelete = true;
                $reason = 'fichier très volumineux (' . $this->formatBytes($fileSize) . ')';
            }

            if ($shouldDelete) {
                $relativePath = str_replace($path . '/', '', $filePath);
                
                if ($dryRun) {
                    $this->line("   🗑️  Serait supprimé: {$relativePath} ({$reason})");
                } else {
                    try {
                        File::delete($filePath);
                        $this->line("   ✅ Supprimé: {$relativePath} ({$reason})");
                        $cleaned++;
                        $sizeFreed += $fileSize;
                        $currentSize -= $fileSize;
                    } catch (\Exception $e) {
                        $this->error("   ❌ Impossible de supprimer {$relativePath}: " . $e->getMessage());
                    }
                }
            }
        }

        return ['files' => $cleaned, 'size' => $sizeFreed];
    }

    /**
     * Nettoyage spécifique des logs Laravel
     */
    private function cleanLaravelLogs(int $days, bool $dryRun): array
    {
        $cleaned = 0;
        $sizeFreed = 0;

        // Vider les logs Laravel anciens
        $logFiles = [
            storage_path('logs/laravel.log'),
            storage_path('logs/backup.log'),
            storage_path('logs/backup_cron.log'),
            storage_path('logs/optimization.log'),
            storage_path('logs/php-fpm.log'),
        ];

        foreach ($logFiles as $logFile) {
            if (!File::exists($logFile)) {
                continue;
            }

            $size = File::size($logFile);
            $lastModified = File::lastModified($logFile);
            
            // Si le fichier est trop vieux ou trop gros
            if ($lastModified < now()->subDays($days)->timestamp || $size > 10 * 1024 * 1024) {
                
                if ($dryRun) {
                    $this->line("   📝 Serait tronqué: " . basename($logFile) . " (" . $this->formatBytes($size) . ")");
                } else {
                    // Garder les dernières lignes (1000) et vider le reste
                    $this->truncateLogFile($logFile, 1000);
                    $newSize = File::size($logFile);
                    $freed = $size - $newSize;
                    
                    $this->line("   📝 Tronqué: " . basename($logFile) . " (" . $this->formatBytes($freed) . " libérés)");
                    $cleaned++;
                    $sizeFreed += $freed;
                }
            }
        }

        return ['files' => $cleaned, 'size' => $sizeFreed];
    }

    /**
     * Vérifie si un fichier est un log
     */
    private function isLogFile(string $filePath): bool
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $basename = strtolower(basename($filePath));
        
        return in_array($extension, ['log']) ||
               str_contains($basename, '.log') ||
               str_contains($basename, 'error') ||
               str_contains($basename, 'access') ||
               preg_match('/\d{4}-\d{2}-\d{2}/', $basename); // Fichiers avec dates
    }

    /**
     * Vérifie si un log peut être tourné
     */
    private function isRotatableLog(string $filePath): bool
    {
        $basename = basename($filePath);
        
        // Ne pas supprimer les logs actifs principaux
        $protectedLogs = ['laravel.log', 'error.log', 'access.log'];
        
        return !in_array($basename, $protectedLogs);
    }

    /**
     * Tronque un fichier de log en gardant les dernières lignes
     */
    private function truncateLogFile(string $filePath, int $keepLines = 1000): void
    {
        try {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES);
            
            if (count($lines) > $keepLines) {
                $keepLines = array_slice($lines, -$keepLines);
                File::put($filePath, implode("\n", $keepLines) . "\n");
            }
        } catch (\Exception $e) {
            $this->error("Erreur lors de la troncature de {$filePath}: " . $e->getMessage());
        }
    }

    /**
     * Formate les bytes en unités lisibles
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}