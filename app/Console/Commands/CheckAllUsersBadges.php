<?php

namespace App\Console\Commands;

use App\Jobs\CheckBadgesJob;
use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Console\Command;

class CheckAllUsersBadges extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'badges:check-all-users 
                            {--user-id= : ID d\'un utilisateur spécifique à vérifier}
                            {--queue : Exécuter en arrière-plan via la queue}
                            {--batch-size=100 : Nombre d\'utilisateurs à traiter par lot}
                            {--dry-run : Simulation sans attribution de badges}';

    /**
     * The console command description.
     */
    protected $description = 'Vérifie et attribue les badges pour tous les utilisateurs ou un utilisateur spécifique';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->option('user-id');
        $useQueue = $this->option('queue');
        $batchSize = (int) $this->option('batch-size');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Mode simulation activé - aucun badge ne sera réellement attribué');
        }

        if ($userId) {
            return $this->checkSingleUser($userId, $useQueue, $dryRun);
        } else {
            return $this->checkAllUsers($useQueue, $batchSize, $dryRun);
        }
    }

    /**
     * Vérifie les badges pour un utilisateur spécifique
     */
    private function checkSingleUser(int $userId, bool $useQueue, bool $dryRun): int
    {
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("Utilisateur avec l'ID {$userId} non trouvé");
            return Command::FAILURE;
        }

        $this->info("Vérification des badges pour l'utilisateur : {$user->name} ({$user->email})");

        if ($useQueue && !$dryRun) {
            CheckBadgesJob::dispatch($user);
            $this->info('Vérification mise en queue pour traitement en arrière-plan');
            $this->line('Utilisez "php artisan queue:work" pour traiter la queue');
        } else {
            try {
                $badgeService = app(BadgeService::class);
                
                if ($dryRun) {
                    $availableBadges = $badgeService->getAvailableBadges($userId);
                    $this->displayUserBadgeStatus($user, $availableBadges);
                } else {
                    $newBadges = $badgeService->checkAndAwardBadges($userId);
                    
                    if (!empty($newBadges)) {
                        $this->info("✅ {count($newBadges)} nouveau(x) badge(s) attribué(s) :");
                        foreach ($newBadges as $badge) {
                            $this->line("  - {$badge->name} : {$badge->description}");
                        }
                    } else {
                        $this->line('Aucun nouveau badge à attribuer');
                    }
                }
                
            } catch (\Exception $e) {
                $this->error('Erreur lors de la vérification : ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Vérifie les badges pour tous les utilisateurs
     */
    private function checkAllUsers(bool $useQueue, int $batchSize, bool $dryRun): int
    {
        $totalUsers = User::count();
        $this->info("Vérification des badges pour {$totalUsers} utilisateurs...");

        if ($useQueue && !$dryRun) {
            CheckBadgesJob::dispatch(null, true);
            $this->info('Vérification de tous les utilisateurs mise en queue');
            $this->line('Utilisez "php artisan queue:work" pour traiter la queue');
            return Command::SUCCESS;
        }

        $processedUsers = 0;
        $totalNewBadges = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($totalUsers);
        $progressBar->start();

        User::chunk($batchSize, function ($users) use (&$processedUsers, &$totalNewBadges, &$errors, $progressBar, $dryRun) {
            $badgeService = app(BadgeService::class);

            foreach ($users as $user) {
                try {
                    if ($dryRun) {
                        // En mode simulation, on compte juste les badges potentiels
                        $availableBadges = $badgeService->getAvailableBadges($user->id);
                        $potentialNewBadges = count($availableBadges['available']);
                        $totalNewBadges += $potentialNewBadges;
                    } else {
                        $newBadges = $badgeService->checkAndAwardBadges($user->id);
                        $totalNewBadges += count($newBadges);
                    }
                    
                    $processedUsers++;
                    
                } catch (\Exception $e) {
                    $errors++;
                    $this->newLine();
                    $this->error("Erreur pour l'utilisateur {$user->id} : " . $e->getMessage());
                }

                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine(2);

        // Affichage du résumé
        $this->info('=== RÉSUMÉ ===');
        $this->line("Utilisateurs traités : {$processedUsers}");
        
        if ($dryRun) {
            $this->line("Badges potentiels à attribuer : {$totalNewBadges}");
        } else {
            $this->line("Nouveaux badges attribués : {$totalNewBadges}");
        }
        
        if ($errors > 0) {
            $this->warn("Erreurs rencontrées : {$errors}");
        }

        return Command::SUCCESS;
    }

    /**
     * Affiche le statut des badges d'un utilisateur en mode simulation
     */
    private function displayUserBadgeStatus(User $user, array $badgeData): void
    {
        $this->line('=== STATUT DES BADGES ===');
        
        $this->info('Badges déjà obtenus :');
        if (!empty($badgeData['earned'])) {
            foreach ($badgeData['earned'] as $badge) {
                $this->line("  ✅ {$badge->name}");
            }
        } else {
            $this->line('  Aucun badge obtenu');
        }

        $this->info('Badges disponibles à obtenir :');
        if (!empty($badgeData['available'])) {
            foreach ($badgeData['available'] as $badge) {
                $this->line("  🎯 {$badge->name} : {$badge->description}");
            }
        } else {
            $this->line('  Aucun badge disponible actuellement');
        }

        $this->info('Badges non encore accessibles :');
        if (!empty($badgeData['locked'])) {
            foreach ($badgeData['locked'] as $badge) {
                $this->line("  🔒 {$badge->name} : {$badge->description}");
            }
        } else {
            $this->line('  Tous les badges sont accessibles');
        }
    }
}
