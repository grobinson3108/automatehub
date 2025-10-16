<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\File;

class CommandsTest extends TestCase
{

    /**
     * Test de la commande de monitoring des backups
     */
    public function test_backup_monitor_command(): void
    {
        $this->artisan('backups:monitor')
             ->expectsOutput('🔍 Surveillance des backups AutomateHub')
             ->assertExitCode(1); // Exit code 1 car pas de backups configurés
    }

    /**
     * Test de la commande d'optimisation de base de données en mode analyse
     */
    public function test_database_optimize_analyze_command(): void
    {
        $this->artisan('db:optimize --analyze')
             ->expectsOutput('🚀 Optimisation de la base de données AutomateHub')
             ->expectsOutput('📊 Mode analyse activé - Aucune modification ne sera effectuée')
             ->assertExitCode(0);
    }

    /**
     * Test de la commande de nettoyage des logs en mode dry-run
     */
    public function test_logs_clean_dry_run_command(): void
    {
        // Créer un fichier de log temporaire pour le test
        $logPath = storage_path('logs/test-' . uniqid() . '.log');
        File::put($logPath, "Test log content\n");

        $this->artisan('logs:clean --dry-run')
             ->expectsOutput('🧹 Nettoyage des logs (conservation: 30 jours, limite: 100MB)')
             ->expectsOutput('⚠️  Mode simulation - aucun fichier ne sera supprimé')
             ->assertExitCode(0);

        // Le fichier doit toujours exister en mode dry-run
        $this->assertTrue(File::exists($logPath));

        // Nettoyer après le test
        File::delete($logPath);
    }

    /**
     * Test de la commande de nettoyage des logs en mode réel
     */
    public function test_logs_clean_real_command(): void
    {
        // Créer un vieux fichier de log pour le test
        $oldLogPath = storage_path('logs/old-test-' . uniqid() . '.log');
        File::put($oldLogPath, "Old log content\n");
        
        // Modifier la date de modification pour simuler un vieux fichier
        touch($oldLogPath, strtotime('-40 days'));

        $this->artisan('logs:clean --days=30')
             ->expectsOutput('🧹 Nettoyage des logs (conservation: 30 jours, limite: 100MB)')
             ->assertExitCode(0);

        // Le vieux fichier devrait être supprimé
        $this->assertFalse(File::exists($oldLogPath));
    }

    /**
     * Test de la commande d'optimisation avec force en production
     */
    public function test_database_optimize_force_in_production(): void
    {
        // Simuler l'environnement de production
        app()->detectEnvironment(function () {
            return 'production';
        });

        $this->artisan('db:optimize --force')
             ->expectsOutput('🚀 Optimisation de la base de données AutomateHub')
             ->assertExitCode(0);
    }

    /**
     * Test que la commande d'optimisation refuse de s'exécuter en production sans --force
     */
    public function test_database_optimize_refuses_production_without_force(): void
    {
        // Simuler l'environnement de production
        app()->detectEnvironment(function () {
            return 'production';
        });

        $this->artisan('db:optimize')
             ->expectsOutput('⚠️  Mode production détecté. Utilisez --force pour continuer ou --analyze pour analyser seulement.')
             ->assertExitCode(1);
    }

    /**
     * Test de la commande de monitoring avec notifications
     */
    public function test_backup_monitor_with_notifications(): void
    {
        $this->artisan('backups:monitor --notify')
             ->expectsOutput('🔍 Surveillance des backups AutomateHub')
             ->assertExitCode(1);
    }

    /**
     * Test de la commande de monitoring avec seuil personnalisé
     */
    public function test_backup_monitor_with_custom_max_age(): void
    {
        $this->artisan('backups:monitor --max-age=12')
             ->expectsOutput('🔍 Surveillance des backups AutomateHub')
             ->assertExitCode(1);
    }

    /**
     * Test que les commandes affichent l'aide correctement
     */
    public function test_commands_help(): void
    {
        // Test simplifié : vérifier que les commandes existent et retournent l'aide
        $this->artisan('backups:monitor --help')->assertExitCode(0);
        $this->artisan('db:optimize --help')->assertExitCode(0);  
        $this->artisan('logs:clean --help')->assertExitCode(0);
    }

    /**
     * Test de performance des commandes
     */
    public function test_commands_performance(): void
    {
        $commands = [
            'db:optimize --analyze',
            'logs:clean --dry-run',
            'backups:monitor'
        ];

        foreach ($commands as $command) {
            $start = microtime(true);
            $this->artisan($command);
            $duration = (microtime(true) - $start) * 1000;

            $this->assertLessThan(
                10000, // 10 secondes max
                $duration,
                "La commande '{$command}' est trop lente: {$duration}ms"
            );
        }
    }
}
