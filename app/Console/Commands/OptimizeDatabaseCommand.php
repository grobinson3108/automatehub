<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class OptimizeDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:optimize 
                            {--analyze : Analyser les performances sans modifier}
                            {--force : Forcer les optimisations même en production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimise les performances de la base de données AutomateHub';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $analyze = $this->option('analyze');
        $force = $this->option('force');
        
        $this->info('🚀 Optimisation de la base de données AutomateHub');
        $this->line('=' . str_repeat('=', 50));
        
        // Vérifier l'environnement
        if (app()->environment('production') && !$force && !$analyze) {
            $this->error('⚠️  Mode production détecté. Utilisez --force pour continuer ou --analyze pour analyser seulement.');
            return Command::FAILURE;
        }
        
        if ($analyze) {
            $this->info('📊 Mode analyse activé - Aucune modification ne sera effectuée');
        }
        
        $optimizations = [
            'analyzeTableSizes' => 'Analyse des tailles de tables',
            'optimizeIndexes' => 'Optimisation des index',
            'cleanupOrphanedRecords' => 'Nettoyage des enregistrements orphelins',
            'optimizeQueries' => 'Optimisation des requêtes courantes',
            'analyzeSlowQueries' => 'Analyse des requêtes lentes',
            'optimizeTablesStructure' => 'Optimisation de la structure des tables'
        ];
        
        foreach ($optimizations as $method => $description) {
            $this->line("\n📈 {$description}...");
            try {
                $this->$method($analyze);
                $this->info("   ✅ {$description} terminé");
            } catch (\Exception $e) {
                $this->error("   ❌ Erreur: " . $e->getMessage());
                Log::error("Database optimization error in {$method}: " . $e->getMessage());
            }
        }
        
        $this->line("\n" . str_repeat('=', 60));
        $this->info('🎯 Optimisation terminée!');
        
        if (!$analyze) {
            $this->warn('💡 Conseils:');
            $this->line('   • Redémarrez PHP-FPM pour appliquer les optimisations');
            $this->line('   • Surveillez les performances avec le dashboard système');
            $this->line('   • Relancez cette commande avec --analyze pour vérifier les améliorations');
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Analyse les tailles des tables
     */
    private function analyzeTableSizes(bool $analyzeOnly = false): void
    {
        $tables = DB::select("
            SELECT 
                TABLE_NAME as table_name,
                TABLE_ROWS as table_rows,
                ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS size_mb,
                ROUND((DATA_LENGTH / 1024 / 1024), 2) AS data_mb,
                ROUND((INDEX_LENGTH / 1024 / 1024), 2) AS index_mb
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = DATABASE()
            ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
        ");
        
        $this->line('   📊 Tailles des tables:');
        foreach ($tables as $table) {
            $this->line(sprintf(
                '      %-20s %8s lignes | %6.1f MB total (%4.1f data + %4.1f index)',
                $table->table_name,
                number_format($table->table_rows),
                $table->size_mb,
                $table->data_mb,
                $table->index_mb
            ));
        }
        
        // Identifier les tables à optimiser
        $largeTables = collect($tables)->where('size_mb', '>', 10);
        if ($largeTables->count() > 0) {
            $this->warn('   ⚠️  Tables volumineuses détectées (>10MB):');
            foreach ($largeTables as $table) {
                $this->line("      - {$table->table_name}: {$table->size_mb}MB");
            }
        }
    }
    
    /**
     * Optimise les index
     */
    private function optimizeIndexes(bool $analyzeOnly = false): void
    {
        $optimizations = [
            // Index pour les requêtes courantes de tutorials
            [
                'table' => 'tutorials',
                'name' => 'idx_tutorials_published_category',
                'columns' => ['is_draft', 'published_at', 'category_id'],
                'reason' => 'Filtrage par publication et catégorie'
            ],
            [
                'table' => 'tutorials',
                'name' => 'idx_tutorials_subscription_level',
                'columns' => ['subscription_required', 'required_level'],
                'reason' => 'Filtrage par abonnement et niveau'
            ],
            // Index pour users
            [
                'table' => 'users',
                'name' => 'idx_users_activity',
                'columns' => ['last_activity_at', 'subscription_type'],
                'reason' => 'Analyse d\'activité et abonnements'
            ],
            [
                'table' => 'users',
                'name' => 'idx_users_admin_active',
                'columns' => ['is_admin', 'last_activity_at'],
                'reason' => 'Gestion des admins et activité'
            ]
        ];
        
        foreach ($optimizations as $opt) {
            $exists = $this->indexExists($opt['table'], $opt['name']);
            
            if ($exists) {
                $this->line("   ✅ Index {$opt['name']} déjà présent");
                continue;
            }
            
            if ($analyzeOnly) {
                $this->line("   📝 Suggéré: Créer index {$opt['name']} sur {$opt['table']} ({$opt['reason']})");
                continue;
            }
            
            try {
                $columns = implode(', ', $opt['columns']);
                DB::statement("CREATE INDEX {$opt['name']} ON {$opt['table']} ({$columns})");
                $this->info("   ✅ Index {$opt['name']} créé sur {$opt['table']}");
            } catch (\Exception $e) {
                $this->warn("   ⚠️  Impossible de créer l'index {$opt['name']}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Nettoie les enregistrements orphelins
     */
    private function cleanupOrphanedRecords(bool $analyzeOnly = false): void
    {
        $cleanups = [
            [
                'name' => 'Favoris orphelins',
                'query' => 'SELECT COUNT(*) as count FROM favorites f LEFT JOIN tutorials t ON f.tutorial_id = t.id WHERE t.id IS NULL',
                'delete' => 'DELETE f FROM favorites f LEFT JOIN tutorials t ON f.tutorial_id = t.id WHERE t.id IS NULL'
            ],
            [
                'name' => 'Téléchargements orphelins',
                'query' => 'SELECT COUNT(*) as count FROM downloads d LEFT JOIN tutorials t ON d.tutorial_id = t.id WHERE t.id IS NULL',
                'delete' => 'DELETE d FROM downloads d LEFT JOIN tutorials t ON d.tutorial_id = t.id WHERE t.id IS NULL'
            ],
            [
                'name' => 'Progrès utilisateur orphelins',
                'query' => 'SELECT COUNT(*) as count FROM user_tutorial_progress p LEFT JOIN tutorials t ON p.tutorial_id = t.id WHERE t.id IS NULL',
                'delete' => 'DELETE p FROM user_tutorial_progress p LEFT JOIN tutorials t ON p.tutorial_id = t.id WHERE t.id IS NULL'
            ]
        ];
        
        foreach ($cleanups as $cleanup) {
            try {
                $result = DB::select($cleanup['query']);
                $count = $result[0]->count ?? 0;
                
                if ($count > 0) {
                    if ($analyzeOnly) {
                        $this->warn("   ⚠️  {$cleanup['name']}: {$count} enregistrement(s) orphelin(s) détecté(s)");
                    } else {
                        DB::statement($cleanup['delete']);
                        $this->info("   🗑️  {$cleanup['name']}: {$count} enregistrement(s) supprimé(s)");
                    }
                } else {
                    $this->line("   ✅ {$cleanup['name']}: Aucun orphelin détecté");
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Erreur lors du nettoyage {$cleanup['name']}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Optimise les requêtes courantes
     */
    private function optimizeQueries(bool $analyzeOnly = false): void
    {
        // Analyser les requêtes les plus courantes
        $commonQueries = [
            'Tutorials publiés' => 'SELECT COUNT(*) FROM tutorials WHERE is_draft = 0 AND published_at <= NOW()',
            'Users actifs (30j)' => 'SELECT COUNT(*) FROM users WHERE last_activity_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)',
            'Downloads par mois' => 'SELECT COUNT(*) FROM downloads WHERE downloaded_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)'
        ];
        
        foreach ($commonQueries as $name => $query) {
            try {
                $start = microtime(true);
                $result = DB::select($query);
                $duration = (microtime(true) - $start) * 1000;
                
                $status = $duration < 100 ? '✅' : ($duration < 500 ? '⚠️' : '❌');
                $this->line(sprintf('   %s %-25s: %6.1fms (%s résultats)', $status, $name, $duration, $result[0]->{'COUNT(*)'} ?? 'N/A'));
                
            } catch (\Exception $e) {
                $this->error("   ❌ Erreur requête {$name}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Analyse les requêtes lentes
     */
    private function analyzeSlowQueries(bool $analyzeOnly = false): void
    {
        try {
            // Vérifier si le slow query log est activé
            $slowLogStatus = DB::select('SHOW VARIABLES LIKE "slow_query_log"');
            $logEnabled = ($slowLogStatus[0]->Value ?? 'OFF') === 'ON';
            
            if (!$logEnabled) {
                $this->warn('   ⚠️  Le slow query log n\'est pas activé');
                if (!$analyzeOnly) {
                    $this->line('   💡 Pour l\'activer: SET GLOBAL slow_query_log = "ON";');
                }
                return;
            }
            
            $this->info('   ✅ Slow query log activé');
            
            // Obtenir les statistiques des requêtes
            $queryStats = DB::select("
                SELECT 
                    sql_text,
                    count_star as executions,
                    avg_timer_wait/1000000000 as avg_duration_ms,
                    sum_timer_wait/1000000000 as total_duration_ms
                FROM performance_schema.events_statements_summary_by_digest 
                WHERE schema_name = DATABASE()
                AND avg_timer_wait > 100000000
                ORDER BY avg_timer_wait DESC 
                LIMIT 5
            ");
            
            if (empty($queryStats)) {
                $this->info('   ✅ Aucune requête lente détectée');
            } else {
                $this->warn('   ⚠️  Requêtes les plus lentes:');
                foreach ($queryStats as $stat) {
                    $query = substr($stat->sql_text, 0, 60) . (strlen($stat->sql_text) > 60 ? '...' : '');
                    $this->line(sprintf('      %6.1fms avg | %s', $stat->avg_duration_ms, $query));
                }
            }
            
        } catch (\Exception $e) {
            $this->warn('   ⚠️  Impossible d\'analyser les requêtes lentes: ' . $e->getMessage());
        }
    }
    
    /**
     * Optimise la structure des tables
     */
    private function optimizeTablesStructure(bool $analyzeOnly = false): void
    {
        $tables = ['users', 'tutorials', 'downloads', 'favorites', 'user_tutorial_progress'];
        
        foreach ($tables as $table) {
            try {
                if ($analyzeOnly) {
                    // Analyser la fragmentation
                    $analysis = DB::select("
                        SELECT 
                            data_free/1024/1024 as fragmentation_mb,
                            (data_free/(data_length+index_length+data_free))*100 as fragmentation_percent
                        FROM information_schema.TABLES 
                        WHERE table_schema = DATABASE() AND table_name = ?
                    ", [$table]);
                    
                    if (!empty($analysis)) {
                        $frag = $analysis[0];
                        if ($frag->fragmentation_mb > 1) {
                            $this->warn(sprintf('   ⚠️  Table %s: %.1fMB fragmentée (%.1f%%)', 
                                $table, $frag->fragmentation_mb, $frag->fragmentation_percent));
                        } else {
                            $this->line("   ✅ Table {$table}: Pas de fragmentation significative");
                        }
                    }
                } else {
                    // Optimiser la table
                    DB::statement("OPTIMIZE TABLE {$table}");
                    $this->info("   ✅ Table {$table} optimisée");
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Erreur sur table {$table}: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Vérifie si un index existe
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return !empty($indexes);
        } catch (\Exception $e) {
            return false;
        }
    }
}
