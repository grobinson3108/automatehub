<?php

namespace App\Console\Commands;

use App\Jobs\SendUpgradeReminderJob;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendNotificationReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:send-reminders 
                            {type : Type de rappel (upgrade, activity, weekly-digest, trial-ending)}
                            {--user-id= : ID d\'un utilisateur spécifique}
                            {--queue : Exécuter en arrière-plan via la queue}
                            {--dry-run : Simulation sans envoi réel}
                            {--batch-size=50 : Nombre d\'utilisateurs à traiter par lot}';

    /**
     * The console command description.
     */
    protected $description = 'Envoie des rappels de notification selon le type spécifié';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->argument('type');
        $validTypes = ['upgrade', 'activity', 'weekly-digest', 'trial-ending'];

        if (!in_array($type, $validTypes)) {
            $this->error("Type de rappel invalide. Types disponibles : " . implode(', ', $validTypes));
            return Command::FAILURE;
        }

        $userId = $this->option('user-id');
        $useQueue = $this->option('queue');
        $dryRun = $this->option('dry-run');
        $batchSize = (int) $this->option('batch-size');

        if ($dryRun) {
            $this->warn('Mode simulation activé - aucun email ne sera réellement envoyé');
        }

        if ($userId) {
            return $this->sendToSingleUser($type, $userId, $useQueue, $dryRun);
        } else {
            return $this->sendToEligibleUsers($type, $useQueue, $dryRun, $batchSize);
        }
    }

    /**
     * Envoie un rappel à un utilisateur spécifique
     */
    private function sendToSingleUser(string $type, int $userId, bool $useQueue, bool $dryRun): int
    {
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("Utilisateur avec l'ID {$userId} non trouvé");
            return Command::FAILURE;
        }

        $this->info("Envoi du rappel '{$type}' à l'utilisateur : {$user->name} ({$user->email})");

        if ($dryRun) {
            $this->displayUserEligibility($user, $type);
            return Command::SUCCESS;
        }

        if ($useQueue) {
            $this->queueNotification($type, $user);
            $this->info('Rappel mis en queue pour envoi en arrière-plan');
        } else {
            try {
                $result = $this->sendNotification($type, $user);
                
                if ($result) {
                    $this->info('✅ Rappel envoyé avec succès');
                } else {
                    $this->warn('⚠️ Rappel non envoyé (préférences utilisateur ou conditions non remplies)');
                }
                
            } catch (\Exception $e) {
                $this->error('Erreur lors de l\'envoi : ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Envoie des rappels à tous les utilisateurs éligibles
     */
    private function sendToEligibleUsers(string $type, bool $useQueue, bool $dryRun, int $batchSize): int
    {
        $eligibleUsers = $this->getEligibleUsers($type);
        $totalUsers = $eligibleUsers->count();

        $this->info("Envoi du rappel '{$type}' à {$totalUsers} utilisateurs éligibles...");

        if ($totalUsers === 0) {
            $this->warn('Aucun utilisateur éligible trouvé');
            return Command::SUCCESS;
        }

        if ($dryRun) {
            $this->displayEligibleUsers($eligibleUsers, $type);
            return Command::SUCCESS;
        }

        if ($useQueue) {
            // Envoyer en masse via la queue
            if ($type === 'upgrade') {
                SendUpgradeReminderJob::dispatch(null, true, $this->getUpgradeReminderType($type));
                $this->info('Campagne de rappels mise en queue pour traitement en arrière-plan');
            } else {
                // Pour les autres types, on traite par lots
                $eligibleUsers->chunk($batchSize, function ($users) use ($type) {
                    foreach ($users as $user) {
                        $this->queueNotification($type, $user);
                    }
                });
                $this->info("Rappels mis en queue par lots de {$batchSize}");
            }
            
            $this->line('Utilisez "php artisan queue:work" pour traiter la queue');
            return Command::SUCCESS;
        }

        // Traitement synchrone
        $sentCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        $progressBar = $this->output->createProgressBar($totalUsers);
        $progressBar->start();

        foreach ($eligibleUsers as $user) {
            try {
                $result = $this->sendNotification($type, $user);
                
                if ($result) {
                    $sentCount++;
                } else {
                    $skippedCount++;
                }

                // Petit délai pour éviter de surcharger le serveur mail
                usleep(100000); // 0.1 seconde
                
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("Erreur pour l'utilisateur {$user->id} : " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Affichage du résumé
        $this->info('=== RÉSUMÉ ===');
        $this->line("Utilisateurs éligibles : {$totalUsers}");
        $this->line("Rappels envoyés : {$sentCount}");
        $this->line("Rappels ignorés : {$skippedCount}");
        
        if ($errorCount > 0) {
            $this->warn("Erreurs rencontrées : {$errorCount}");
        }

        return Command::SUCCESS;
    }

    /**
     * Récupère les utilisateurs éligibles selon le type de rappel
     */
    private function getEligibleUsers(string $type)
    {
        $query = User::query();

        switch ($type) {
            case 'upgrade':
                // Utilisateurs non-Pro qui pourraient être intéressés par un upgrade
                $query->whereIn('subscription_type', ['free', 'premium'])
                      ->where('created_at', '<', now()->subDays(7)); // Inscrits depuis au moins 7 jours
                break;

            case 'activity':
                // Utilisateurs inactifs depuis plus de 7 jours
                $query->where('last_activity_at', '<', now()->subDays(7))
                      ->where('last_activity_at', '>', now()->subDays(30)); // Mais pas trop anciens
                break;

            case 'weekly-digest':
                // Tous les utilisateurs actifs
                $query->where('last_activity_at', '>', now()->subDays(30));
                break;

            case 'trial-ending':
                // Utilisateurs en fin d'essai (à implémenter selon la logique d'essai)
                $query->where('subscription_type', 'free')
                      ->where('created_at', '>', now()->subDays(14))
                      ->where('created_at', '<', now()->subDays(7));
                break;
        }

        // Exclure les utilisateurs qui ont reçu ce type de rappel récemment
        $query->whereDoesntHave('analytics', function ($q) use ($type) {
            $q->where('event_type', 'email_sent')
              ->whereJsonContains('event_data->email_type', $this->getEmailType($type))
              ->where('created_at', '>=', now()->subDays(7));
        });

        return $query->get();
    }

    /**
     * Envoie une notification selon le type
     */
    private function sendNotification(string $type, User $user): bool
    {
        $notificationService = app(NotificationService::class);

        switch ($type) {
            case 'upgrade':
                return $notificationService->sendUpgradeReminder($user);
            
            case 'activity':
                return $notificationService->sendActivityReminder($user);
            
            case 'weekly-digest':
                return $notificationService->sendWeeklyDigest($user);
            
            case 'trial-ending':
                // Calculer les jours restants (exemple)
                $daysRemaining = 7 - $user->created_at->diffInDays(now());
                return $notificationService->sendTrialEndingNotification($user, max(1, $daysRemaining));
            
            default:
                throw new \InvalidArgumentException("Type de notification non supporté : {$type}");
        }
    }

    /**
     * Met une notification en queue
     */
    private function queueNotification(string $type, User $user): void
    {
        switch ($type) {
            case 'upgrade':
                SendUpgradeReminderJob::dispatch($user, false, 'general');
                break;
            
            default:
                // Pour les autres types, on pourrait créer des jobs spécifiques
                // Pour l'instant, on utilise le service directement
                $this->sendNotification($type, $user);
                break;
        }
    }

    /**
     * Affiche l'éligibilité d'un utilisateur
     */
    private function displayUserEligibility(User $user, string $type): void
    {
        $this->line('=== ÉLIGIBILITÉ UTILISATEUR ===');
        $this->line("Nom : {$user->name}");
        $this->line("Email : {$user->email}");
        $this->line("Type d'abonnement : {$user->subscription_type}");
        $this->line("Dernière activité : " . ($user->last_activity_at ? $user->last_activity_at->format('Y-m-d H:i:s') : 'Jamais'));
        $this->line("Inscrit le : {$user->created_at->format('Y-m-d H:i:s')}");

        $notificationService = app(NotificationService::class);
        $canSend = $notificationService->canSendNotification($user, $this->getEmailType($type));
        
        if ($canSend) {
            $this->info('✅ Utilisateur éligible pour ce type de rappel');
        } else {
            $this->warn('❌ Utilisateur non éligible (préférences de notification)');
        }
    }

    /**
     * Affiche la liste des utilisateurs éligibles
     */
    private function displayEligibleUsers($users, string $type): void
    {
        $this->line('=== UTILISATEURS ÉLIGIBLES ===');
        
        foreach ($users as $user) {
            $this->line("- {$user->name} ({$user->email}) - {$user->subscription_type}");
        }
    }

    /**
     * Convertit le type de rappel en type d'email
     */
    private function getEmailType(string $type): string
    {
        $mapping = [
            'upgrade' => 'upgrade_reminder',
            'activity' => 'activity_reminder',
            'weekly-digest' => 'weekly_digest',
            'trial-ending' => 'trial_ending'
        ];

        return $mapping[$type] ?? $type;
    }

    /**
     * Récupère le type de rappel d'upgrade
     */
    private function getUpgradeReminderType(string $type): string
    {
        return 'general'; // Peut être étendu pour différents types d'upgrade
    }
}
