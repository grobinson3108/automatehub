<?php

namespace App\Console\Commands;

use App\Jobs\AnalyticsReportJob;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateAnalyticsReport extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'analytics:generate-report 
                            {type : Type de rapport (daily_summary, user_engagement, conversion_funnel, content_performance, revenue_analysis, weekly_digest, monthly_summary)}
                            {--start-date= : Date de début (YYYY-MM-DD)}
                            {--end-date= : Date de fin (YYYY-MM-DD)}
                            {--admin-email= : Email de l\'admin à notifier}
                            {--queue : Exécuter en arrière-plan via la queue}';

    /**
     * The console command description.
     */
    protected $description = 'Génère un rapport d\'analytics selon le type spécifié';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->argument('type');
        $validTypes = [
            'daily_summary',
            'user_engagement', 
            'conversion_funnel',
            'content_performance',
            'revenue_analysis',
            'weekly_digest',
            'monthly_summary'
        ];

        if (!in_array($type, $validTypes)) {
            $this->error("Type de rapport invalide. Types disponibles : " . implode(', ', $validTypes));
            return Command::FAILURE;
        }

        // Préparer les paramètres
        $parameters = [];
        
        if ($this->option('start-date')) {
            try {
                $parameters['start_date'] = Carbon::createFromFormat('Y-m-d', $this->option('start-date'));
            } catch (\Exception $e) {
                $this->error('Format de date de début invalide. Utilisez YYYY-MM-DD');
                return Command::FAILURE;
            }
        }

        if ($this->option('end-date')) {
            try {
                $parameters['end_date'] = Carbon::createFromFormat('Y-m-d', $this->option('end-date'));
            } catch (\Exception $e) {
                $this->error('Format de date de fin invalide. Utilisez YYYY-MM-DD');
                return Command::FAILURE;
            }
        }

        // Vérifier l'admin si spécifié
        $adminUserId = null;
        if ($this->option('admin-email')) {
            $admin = User::where('email', $this->option('admin-email'))
                         ->where('role', 'admin')
                         ->first();
            
            if (!$admin) {
                $this->error('Administrateur non trouvé avec cet email');
                return Command::FAILURE;
            }
            
            $adminUserId = $admin->id;
        }

        $this->info("Génération du rapport '{$type}'...");
        
        if ($this->option('start-date')) {
            $this->line("Date de début : " . $this->option('start-date'));
        }
        
        if ($this->option('end-date')) {
            $this->line("Date de fin : " . $this->option('end-date'));
        }

        if ($this->option('queue')) {
            // Exécution en arrière-plan
            AnalyticsReportJob::dispatch($type, $parameters, $adminUserId);
            $this->info('Rapport mis en queue pour génération en arrière-plan');
            $this->line('Utilisez "php artisan queue:work" pour traiter la queue');
        } else {
            // Exécution synchrone
            try {
                $job = new AnalyticsReportJob($type, $parameters, $adminUserId);
                $analyticsService = app(\App\Services\AnalyticsService::class);
                $job->handle($analyticsService);
                
                $this->info('Rapport généré avec succès !');
                $this->line('Le fichier a été sauvegardé dans storage/app/reports/analytics/');
                
            } catch (\Exception $e) {
                $this->error('Erreur lors de la génération du rapport : ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
