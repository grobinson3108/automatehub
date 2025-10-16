<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'name' => 'Bienvenue',
                'slug' => 'welcome',
                'type' => 'registration',
                'description' => 'Bienvenue sur AutomateHub ! Merci de vous être inscrit.',
                'icon' => 'fas fa-hand-wave',
                'requirements' => ['welcome' => true],
                'points_required' => 0,
            ],
            [
                'name' => 'Débutant n8n',
                'slug' => 'n8n-beginner',
                'type' => 'n8n_level',
                'description' => 'Vous avez démontré une connaissance de base de n8n.',
                'icon' => 'fas fa-seedling',
                'requirements' => ['level_n8n' => 'beginner'],
                'points_required' => 0,
            ],
            [
                'name' => 'Intermédiaire n8n',
                'slug' => 'n8n-intermediate',
                'type' => 'n8n_level',
                'description' => 'Vous avez un niveau intermédiaire en n8n.',
                'icon' => 'fas fa-chart-line',
                'requirements' => ['level_n8n' => 'intermediate'],
                'points_required' => 0,
            ],
            [
                'name' => 'Expert n8n',
                'slug' => 'n8n-expert',
                'type' => 'n8n_level',
                'description' => 'Vous êtes un expert en n8n !',
                'icon' => 'fas fa-crown',
                'requirements' => ['level_n8n' => 'expert'],
                'points_required' => 0,
            ],
            [
                'name' => 'Professionnel',
                'slug' => 'professional',
                'type' => 'registration',
                'description' => 'Badge spécial pour les utilisateurs professionnels.',
                'icon' => 'fas fa-briefcase',
                'requirements' => ['is_professional' => true],
                'points_required' => 0,
            ],
            [
                'name' => 'Premier pas',
                'slug' => 'first-step',
                'type' => 'tutorial_completion',
                'description' => 'Félicitations ! Vous avez terminé votre premier tutoriel n8n.',
                'icon' => 'fas fa-baby',
                'requirements' => ['tutorials_completed' => 1],
                'points_required' => 0,
            ],
            [
                'name' => 'Débutant motivé',
                'slug' => 'motivated-beginner',
                'type' => 'tutorial_completion',
                'description' => 'Vous avez terminé 5 tutoriels. Vous êtes sur la bonne voie !',
                'icon' => 'fas fa-seedling',
                'requirements' => ['tutorials_completed' => 5],
                'points_required' => 50,
            ],
            [
                'name' => 'Apprenti automatiseur',
                'slug' => 'apprentice-automator',
                'type' => 'tutorial_completion',
                'description' => 'Bravo ! 10 tutoriels terminés. Vous maîtrisez les bases.',
                'icon' => 'fas fa-cogs',
                'requirements' => ['tutorials_completed' => 10],
                'points_required' => 100,
            ],
            [
                'name' => 'Niveau intermédiaire',
                'slug' => 'intermediate-level',
                'type' => 'tutorial_completion',
                'description' => 'Excellent ! Vous avez atteint le niveau intermédiaire avec 20 tutoriels.',
                'icon' => 'fas fa-chart-line',
                'requirements' => ['tutorials_completed' => 20, 'level' => 'intermediate'],
                'points_required' => 200,
            ],
            [
                'name' => 'Expert en devenir',
                'slug' => 'expert-in-training',
                'type' => 'tutorial_completion',
                'description' => 'Impressionnant ! 50 tutoriels terminés. Vous êtes presque un expert.',
                'icon' => 'fas fa-graduation-cap',
                'requirements' => ['tutorials_completed' => 50],
                'points_required' => 500,
            ],
            [
                'name' => 'Maître n8n',
                'slug' => 'n8n-master',
                'type' => 'tutorial_completion',
                'description' => 'Félicitations ! Vous êtes maintenant un expert n8n avec 100 tutoriels terminés.',
                'icon' => 'fas fa-crown',
                'requirements' => ['tutorials_completed' => 100, 'level' => 'expert'],
                'points_required' => 1000,
            ],
            [
                'name' => 'Téléchargeur actif',
                'slug' => 'active-downloader',
                'type' => 'download',
                'description' => 'Vous avez téléchargé 25 fichiers. Merci pour votre engagement !',
                'icon' => 'fas fa-download',
                'requirements' => ['downloads_count' => 25],
                'points_required' => 75,
            ],
            [
                'name' => 'Collectionneur',
                'slug' => 'collector',
                'type' => 'activity',
                'description' => 'Vous avez ajouté 10 tutoriels à vos favoris.',
                'icon' => 'fas fa-heart',
                'requirements' => ['favorites_count' => 10],
                'points_required' => 30,
            ],
            [
                'name' => 'Utilisateur premium',
                'slug' => 'premium-user',
                'type' => 'subscription',
                'description' => 'Merci d\'avoir souscrit à un abonnement premium !',
                'icon' => 'fas fa-star',
                'requirements' => ['subscription_type' => 'premium'],
                'points_required' => 0,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['slug' => $badge['slug'] ?? null],
                $badge
            );
        }
    }
}
