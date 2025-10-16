<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MonitorBackupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backups:monitor 
                            {--notify : Envoyer des notifications en cas de problème}
                            {--max-age=24 : Âge maximum accepté pour un backup (en heures)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Surveille l\'état des backups et alerte en cas de problème';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notify = $this->option('notify');
        $maxAgeHours = (int) $this->option('max-age');
        
        $this->info('🔍 Surveillance des backups AutomateHub');
        $this->line('=' . str_repeat('=', 50));
        
        $issues = [];
        $backupPaths = [
            '/var/backups/automatehub' => 'Backup complet',
            '/var/backups/automatehub-db' => 'Backup base de données',
            '/var/log/backup.log' => 'Logs de backup'
        ];
        
        foreach ($backupPaths as $path => $description) {
            $this->line("\n📁 Vérification: {$description}");
            
            if (!file_exists($path)) {
                $issue = "❌ {$description}: Répertoire/fichier inexistant ({$path})";
                $this->error("   {$issue}");
                $issues[] = $issue;
                continue;
            }
            
            if (is_dir($path)) {
                $result = $this->checkBackupDirectory($path, $description, $maxAgeHours);
                if ($result['issues']) {
                    $issues = array_merge($issues, $result['issues']);
                }
            } else {
                $result = $this->checkLogFile($path, $description, $maxAgeHours);
                if ($result['issues']) {
                    $issues = array_merge($issues, $result['issues']);
                }
            }
        }
        
        // Vérifier l'espace disque
        $diskResult = $this->checkDiskSpace();
        if ($diskResult['issues']) {
            $issues = array_merge($issues, $diskResult['issues']);
        }
        
        // Vérifier le cron de backup
        $cronResult = $this->checkBackupCron();
        if ($cronResult['issues']) {
            $issues = array_merge($issues, $cronResult['issues']);
        }
        
        // Résumé final
        $this->line("\n" . str_repeat('=', 60));
        
        if (empty($issues)) {
            $this->info('✅ Tous les backups sont OK!');
            \Log::info('Backup monitoring: All systems OK');
        } else {
            $this->error("❌ {" . count($issues) . "} problème(s) détecté(s):");
            foreach ($issues as $issue) {
                $this->line("   • {$issue}");
            }
            
            // Logger les problèmes
            \Log::warning('Backup monitoring issues detected', [
                'issues_count' => count($issues),
                'issues' => $issues
            ]);
            
            // Notifications si activées
            if ($notify) {
                $this->sendNotifications($issues);
            }
            
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Vérifie un répertoire de backup
     */
    private function checkBackupDirectory(string $path, string $description, int $maxAgeHours): array
    {
        $issues = [];
        
        try {
            $files = File::allFiles($path);
            $this->line("   📄 Fichiers trouvés: " . count($files));
            
            if (empty($files)) {
                $issues[] = "{$description}: Aucun fichier de backup trouvé";
                return ['issues' => $issues];
            }
            
            // Trouver le fichier le plus récent
            $latestFile = null;
            $latestTime = 0;
            $totalSize = 0;
            
            foreach ($files as $file) {
                $mtime = File::lastModified($file->getPathname());
                $totalSize += File::size($file->getPathname());
                
                if ($mtime > $latestTime) {
                    $latestTime = $mtime;
                    $latestFile = $file;
                }
            }
            
            if ($latestFile) {
                $ageHours = (time() - $latestTime) / 3600;
                $this->line("   🕒 Dernier backup: " . date('Y-m-d H:i:s', $latestTime) . " (il y a " . round($ageHours, 1) . "h)");
                $this->line("   📊 Taille totale: " . $this->formatBytes($totalSize));
                
                if ($ageHours > $maxAgeHours) {
                    $issues[] = "{$description}: Backup trop ancien (" . round($ageHours, 1) . "h > {$maxAgeHours}h)";
                } else {
                    $this->info("   ✅ Backup récent et disponible");
                }
            }
            
        } catch (\Exception $e) {
            $issues[] = "{$description}: Erreur lors de la vérification (" . $e->getMessage() . ")";
        }
        
        return ['issues' => $issues];
    }
    
    /**
     * Vérifie un fichier de log
     */
    private function checkLogFile(string $path, string $description, int $maxAgeHours): array
    {
        $issues = [];
        
        try {
            $mtime = File::lastModified($path);
            $size = File::size($path);
            $ageHours = (time() - $mtime) / 3600;
            
            $this->line("   🕒 Dernière modification: " . date('Y-m-d H:i:s', $mtime) . " (il y a " . round($ageHours, 1) . "h)");
            $this->line("   📊 Taille: " . $this->formatBytes($size));
            
            // Vérifier si le log contient des erreurs récentes
            $content = File::get($path);
            $lines = explode("\n", $content);
            $recentLines = array_slice($lines, -50); // Dernières 50 lignes
            
            $errorCount = 0;
            foreach ($recentLines as $line) {
                if (stripos($line, 'error') !== false || stripos($line, 'failed') !== false) {
                    $errorCount++;
                }
            }
            
            if ($errorCount > 0) {
                $issues[] = "{$description}: {$errorCount} erreur(s) récente(s) détectée(s)";
            } else {
                $this->info("   ✅ Aucune erreur récente détectée");
            }
            
        } catch (\Exception $e) {
            $issues[] = "{$description}: Erreur lors de la lecture (" . $e->getMessage() . ")";
        }
        
        return ['issues' => $issues];
    }
    
    /**
     * Vérifie l'espace disque
     */
    private function checkDiskSpace(): array
    {
        $issues = [];
        
        $this->line("\n💾 Vérification de l'espace disque");
        
        try {
            $total = disk_total_space('/');
            $free = disk_free_space('/');
            $used = $total - $free;
            $percentUsed = ($used / $total) * 100;
            
            $this->line("   📊 Espace total: " . $this->formatBytes($total));
            $this->line("   📊 Espace utilisé: " . $this->formatBytes($used) . " (" . round($percentUsed, 1) . "%)");
            $this->line("   📊 Espace libre: " . $this->formatBytes($free));
            
            if ($percentUsed > 90) {
                $issues[] = "Espace disque critique: " . round($percentUsed, 1) . "% utilisé";
            } elseif ($percentUsed > 80) {
                $this->warn("   ⚠️ Espace disque faible: " . round($percentUsed, 1) . "% utilisé");
            } else {
                $this->info("   ✅ Espace disque suffisant");
            }
            
        } catch (\Exception $e) {
            $issues[] = "Erreur lors de la vérification de l'espace disque: " . $e->getMessage();
        }
        
        return ['issues' => $issues];
    }
    
    /**
     * Vérifie la configuration du cron de backup
     */
    private function checkBackupCron(): array
    {
        $issues = [];
        
        $this->line("\n⏰ Vérification du cron de backup");
        
        try {
            // Vérifier si le cron backup.sh est configuré
            $cronContent = shell_exec('crontab -l 2>/dev/null');
            
            if (empty($cronContent)) {
                $issues[] = "Aucune tâche cron configurée";
            } elseif (strpos($cronContent, 'backup.sh') === false) {
                $issues[] = "Script backup.sh non trouvé dans le cron";
            } else {
                $this->info("   ✅ Tâche cron backup configurée");
            }
            
        } catch (\Exception $e) {
            $issues[] = "Erreur lors de la vérification du cron: " . $e->getMessage();
        }
        
        return ['issues' => $issues];
    }
    
    /**
     * Envoie des notifications en cas de problème
     */
    private function sendNotifications(array $issues): void
    {
        $this->line("\n📧 Envoi des notifications...");
        
        try {
            // Ici vous pouvez intégrer votre système de notification préféré
            // Exemples : email, Slack, Discord, etc.
            
            $message = "AutomateHub - Problèmes de backup détectés:\n\n";
            foreach ($issues as $issue) {
                $message .= "- {$issue}\n";
            }
            
            // Pour l'instant, on log juste le message
            Log::alert('Backup monitoring alert', [
                'message' => $message,
                'issues' => $issues,
                'server' => gethostname(),
                'timestamp' => now()->toDateTimeString()
            ]);
            
            $this->info("   ✅ Notification enregistrée dans les logs");
            
        } catch (\Exception $e) {
            $this->error("   ❌ Erreur lors de l'envoi de notification: " . $e->getMessage());
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
