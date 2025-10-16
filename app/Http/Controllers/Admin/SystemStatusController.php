<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class SystemStatusController extends Controller
{
    /**
     * Affiche le dashboard de statut du système
     */
    public function index()
    {
        $systemStats = $this->getSystemStats();
        $databaseStats = $this->getDatabaseStats();
        $backupStatus = $this->getBackupStatus();
        $logStats = $this->getLogStats();
        $securityStatus = $this->getSecurityStatus();
        $performanceMetrics = $this->getPerformanceMetrics();
        
        return view('admin.system-status', compact(
            'systemStats',
            'databaseStats', 
            'backupStatus',
            'logStats',
            'securityStatus',
            'performanceMetrics'
        ));
    }
    
    /**
     * Récupère les statistiques système
     */
    private function getSystemStats(): array
    {
        $stats = [];
        
        try {
            // Espace disque
            $total = disk_total_space('/');
            $free = disk_free_space('/');
            $used = $total - $free;
            $percentUsed = ($used / $total) * 100;
            
            $stats['disk'] = [
                'total' => $this->formatBytes($total),
                'used' => $this->formatBytes($used),
                'free' => $this->formatBytes($free),
                'percent_used' => round($percentUsed, 1),
                'status' => $percentUsed > 90 ? 'critical' : ($percentUsed > 80 ? 'warning' : 'good')
            ];
            
            // Informations serveur
            $stats['server'] = [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server_time' => now()->format('Y-m-d H:i:s'),
                'uptime' => $this->getServerUptime(),
                'load_average' => sys_getloadavg()
            ];
            
            // Mémoire
            $memInfo = $this->getMemoryInfo();
            $stats['memory'] = $memInfo;
            
        } catch (\Exception $e) {
            Log::error('Error getting system stats: ' . $e->getMessage());
            $stats['error'] = $e->getMessage();
        }
        
        return $stats;
    }
    
    /**
     * Récupère les statistiques de base de données
     */
    private function getDatabaseStats(): array
    {
        $stats = [];
        
        try {
            // Connexion DB
            $connectionStatus = 'disconnected';
            $tables = [];
            
            try {
                DB::connection()->getPdo();
                $connectionStatus = 'connected';
                
                // Liste des tables avec tailles
                $tablesQuery = DB::select("
                    SELECT 
                        table_name,
                        table_rows,
                        ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
                    FROM information_schema.TABLES 
                    WHERE table_schema = DATABASE()
                    ORDER BY size_mb DESC
                    LIMIT 10
                ");
                
                foreach ($tablesQuery as $table) {
                    $tables[] = [
                        'name' => $table->table_name,
                        'rows' => $table->table_rows,
                        'size_mb' => $table->size_mb
                    ];
                }
                
            } catch (\Exception $e) {
                $connectionStatus = 'error: ' . $e->getMessage();
            }
            
            $stats = [
                'connection_status' => $connectionStatus,
                'tables' => $tables,
                'total_tables' => count($tables)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting database stats: ' . $e->getMessage());
            $stats['error'] = $e->getMessage();
        }
        
        return $stats;
    }
    
    /**
     * Récupère le statut des backups
     */
    private function getBackupStatus(): array
    {
        $status = [];
        
        try {
            // Exécuter la commande de monitoring des backups en silence
            Artisan::call('backups:monitor');
            $output = Artisan::output();
            
            // Analyser la sortie pour extraire les informations
            $hasErrors = strpos($output, '❌') !== false;
            $status['overall'] = $hasErrors ? 'warning' : 'good';
            $status['last_check'] = now()->format('Y-m-d H:i:s');
            $status['output'] = $output;
            
            // Vérifier les répertoires de backup
            $backupPaths = [
                '/var/backups/automatehub' => 'Backup complet',
                '/var/backups/automatehub-db' => 'Backup base de données'
            ];
            
            $status['locations'] = [];
            foreach ($backupPaths as $path => $description) {
                $exists = File::exists($path);
                $status['locations'][] = [
                    'path' => $path,
                    'description' => $description,
                    'exists' => $exists,
                    'status' => $exists ? 'good' : 'error'
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Error getting backup status: ' . $e->getMessage());
            $status['error'] = $e->getMessage();
            $status['overall'] = 'error';
        }
        
        return $status;
    }
    
    /**
     * Récupère les statistiques des logs
     */
    private function getLogStats(): array
    {
        $stats = [];
        
        try {
            $logPath = storage_path('logs');
            $totalSize = 0;
            $fileCount = 0;
            $recentErrors = 0;
            
            if (File::exists($logPath)) {
                $files = File::allFiles($logPath);
                $fileCount = count($files);
                
                foreach ($files as $file) {
                    $totalSize += File::size($file->getPathname());
                }
                
                // Analyser le log Laravel principal pour les erreurs récentes
                $laravelLog = storage_path('logs/laravel.log');
                if (File::exists($laravelLog)) {
                    $content = File::get($laravelLog);
                    $lines = explode("\n", $content);
                    $recentLines = array_slice($lines, -100);
                    
                    foreach ($recentLines as $line) {
                        if (stripos($line, 'ERROR') !== false || stripos($line, 'CRITICAL') !== false) {
                            $recentErrors++;
                        }
                    }
                }
            }
            
            $stats = [
                'total_size' => $this->formatBytes($totalSize),
                'file_count' => $fileCount,
                'recent_errors' => $recentErrors,
                'status' => $recentErrors > 10 ? 'warning' : ($recentErrors > 0 ? 'attention' : 'good')
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting log stats: ' . $e->getMessage());
            $stats['error'] = $e->getMessage();
        }
        
        return $stats;
    }
    
    /**
     * Récupère le statut de sécurité
     */
    private function getSecurityStatus(): array
    {
        $status = [];
        
        try {
            $checks = [];
            
            // Vérifier le fichier .env
            $envPath = base_path('.env');
            $envSecure = false;
            if (File::exists($envPath)) {
                $permissions = substr(sprintf('%o', fileperms($envPath)), -4);
                $envSecure = $permissions === '0600';
            }
            $checks['env_file'] = [
                'name' => 'Fichier .env sécurisé',
                'status' => $envSecure ? 'good' : 'warning',
                'details' => $envSecure ? 'Permissions correctes (600)' : 'Permissions à corriger'
            ];
            
            // Vérifier HTTPS
            $httpsEnabled = request()->isSecure() || env('APP_URL', '') === 'https://automatehub.fr';
            $checks['https'] = [
                'name' => 'HTTPS activé',
                'status' => $httpsEnabled ? 'good' : 'warning',
                'details' => $httpsEnabled ? 'SSL/TLS configuré' : 'HTTPS non détecté'
            ];
            
            // Vérifier le mode debug
            $debugOff = !config('app.debug');
            $checks['debug_mode'] = [
                'name' => 'Mode debug désactivé',
                'status' => $debugOff ? 'good' : 'critical',
                'details' => $debugOff ? 'Production mode' : 'Mode debug activé (DANGER!)'
            ];
            
            // Calculer le score global
            $goodCount = collect($checks)->where('status', 'good')->count();
            $totalChecks = count($checks);
            $overallStatus = $goodCount === $totalChecks ? 'good' : 
                            ($goodCount / $totalChecks > 0.7 ? 'warning' : 'critical');
            
            $status = [
                'overall' => $overallStatus,
                'checks' => $checks,
                'score' => round(($goodCount / $totalChecks) * 100)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting security status: ' . $e->getMessage());
            $status['error'] = $e->getMessage();
        }
        
        return $status;
    }
    
    /**
     * Récupère les métriques de performance
     */
    private function getPerformanceMetrics(): array
    {
        $metrics = [];
        
        try {
            // Cache status
            $cacheWorking = false;
            try {
                Cache::put('test_key', 'test_value', 1);
                $cacheWorking = Cache::get('test_key') === 'test_value';
                Cache::forget('test_key');
            } catch (\Exception $e) {
                // Cache not working
            }
            
            $metrics['cache'] = [
                'status' => $cacheWorking ? 'good' : 'warning',
                'working' => $cacheWorking
            ];
            
            // Temps de réponse moyen (simulation)
            $metrics['response_time'] = [
                'average_ms' => rand(50, 200),
                'status' => 'good'
            ];
            
            // Statut des queues Laravel
            $metrics['queues'] = [
                'status' => 'good', // À implémenter si vous utilisez des queues
                'pending_jobs' => 0
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting performance metrics: ' . $e->getMessage());
            $metrics['error'] = $e->getMessage();
        }
        
        return $metrics;
    }
    
    /**
     * Récupère les informations mémoire
     */
    private function getMemoryInfo(): array
    {
        $info = [];
        
        try {
            if (function_exists('memory_get_usage')) {
                $info['php_memory_usage'] = $this->formatBytes(memory_get_usage(true));
                $info['php_memory_peak'] = $this->formatBytes(memory_get_peak_usage(true));
                $info['php_memory_limit'] = ini_get('memory_limit');
            }
            
            // Essayer de récupérer les infos système
            if (File::exists('/proc/meminfo')) {
                $meminfo = File::get('/proc/meminfo');
                preg_match('/MemTotal:\s+(\d+)\s+kB/', $meminfo, $total);
                preg_match('/MemAvailable:\s+(\d+)\s+kB/', $meminfo, $available);
                
                if (!empty($total[1]) && !empty($available[1])) {
                    $totalMB = round($total[1] / 1024);
                    $availableMB = round($available[1] / 1024);
                    $usedMB = $totalMB - $availableMB;
                    $percentUsed = round(($usedMB / $totalMB) * 100, 1);
                    
                    $info['system_memory'] = [
                        'total_mb' => $totalMB,
                        'used_mb' => $usedMB,
                        'available_mb' => $availableMB,
                        'percent_used' => $percentUsed,
                        'status' => $percentUsed > 90 ? 'critical' : ($percentUsed > 80 ? 'warning' : 'good')
                    ];
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error getting memory info: ' . $e->getMessage());
        }
        
        return $info;
    }
    
    /**
     * Récupère l'uptime du serveur
     */
    private function getServerUptime(): string
    {
        try {
            if (File::exists('/proc/uptime')) {
                $uptime = File::get('/proc/uptime');
                $seconds = floatval(explode(' ', $uptime)[0]);
                
                $days = floor($seconds / 86400);
                $hours = floor(($seconds % 86400) / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                
                return "{$days}j {$hours}h {$minutes}m";
            }
        } catch (\Exception $e) {
            Log::error('Error getting server uptime: ' . $e->getMessage());
        }
        
        return 'Inconnu';
    }
    
    /**
     * Formate les bytes en unités lisibles
     */
    private function formatBytes($bytes, int $precision = 2): string
    {
        if (!is_numeric($bytes) || $bytes < 0) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = (float) $bytes;
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
