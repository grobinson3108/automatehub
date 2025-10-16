<?php

namespace Database\Factories;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Badge>
 */
class BadgeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $badgeData = $this->faker->randomElement([
            [
                'name' => 'Bienvenue',
                'description' => 'Premier pas sur AutomateHub',
                'type' => 'registration',
                'criteria' => ['action' => 'register'],
                'icon' => 'welcome.svg'
            ],
            [
                'name' => 'Premier Tutoriel',
                'description' => 'Premier tutoriel complété',
                'type' => 'tutorial_completion',
                'criteria' => ['tutorials_completed' => 1],
                'icon' => 'first-tutorial.svg'
            ],
            [
                'name' => 'Apprenant Assidu',
                'description' => '5 tutoriels complétés',
                'type' => 'tutorial_completion',
                'criteria' => ['tutorials_completed' => 5],
                'icon' => 'learner.svg'
            ],
            [
                'name' => 'Maître des Workflows',
                'description' => '20 tutoriels complétés',
                'type' => 'tutorial_completion',
                'criteria' => ['tutorials_completed' => 20],
                'icon' => 'master.svg'
            ],
            [
                'name' => 'Premier Téléchargement',
                'description' => 'Premier fichier téléchargé',
                'type' => 'download',
                'criteria' => ['downloads_count' => 1],
                'icon' => 'first-download.svg'
            ],
            [
                'name' => 'Collectionneur',
                'description' => '10 téléchargements effectués',
                'type' => 'download',
                'criteria' => ['downloads_count' => 10],
                'icon' => 'collector.svg'
            ],
            [
                'name' => 'Débutant n8n',
                'description' => 'Niveau débutant en n8n',
                'type' => 'n8n_level',
                'criteria' => ['n8n_level' => 'beginner'],
                'icon' => 'beginner.svg'
            ],
            [
                'name' => 'Expert n8n',
                'description' => 'Niveau expert en n8n',
                'type' => 'n8n_level',
                'criteria' => ['n8n_level' => 'advanced'],
                'icon' => 'expert.svg'
            ],
            [
                'name' => 'Utilisateur Actif',
                'description' => 'Actif pendant 7 jours consécutifs',
                'type' => 'engagement',
                'criteria' => ['consecutive_days' => 7],
                'icon' => 'active-user.svg'
            ],
            [
                'name' => 'Pionnier',
                'description' => 'Parmi les 100 premiers utilisateurs',
                'type' => 'special',
                'criteria' => ['user_rank' => 100],
                'icon' => 'pioneer.svg'
            ]
        ]);
        
        return [
            'name' => $badgeData['name'],
            'description' => $badgeData['description'],
            'type' => $badgeData['type'],
            'criteria' => json_encode($badgeData['criteria']),
            'icon' => $badgeData['icon'],
            'color' => $this->faker->hexColor(),
            'is_active' => $this->faker->boolean(95), // 95% de chance d'être actif
            'sort_order' => $this->faker->numberBetween(1, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Badge d'inscription
     */
    public function registration(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Bienvenue',
            'description' => 'Premier pas sur AutomateHub',
            'type' => 'registration',
            'criteria' => json_encode(['action' => 'register']),
            'icon' => 'welcome.svg',
        ]);
    }

    /**
     * Badge de niveau n8n
     */
    public function n8nLevel(string $level = 'beginner'): static
    {
        $levelData = [
            'beginner' => ['name' => 'Débutant n8n', 'description' => 'Niveau débutant en n8n'],
            'intermediate' => ['name' => 'Intermédiaire n8n', 'description' => 'Niveau intermédiaire en n8n'],
            'advanced' => ['name' => 'Expert n8n', 'description' => 'Niveau expert en n8n'],
        ];

        return $this->state(fn (array $attributes) => [
            'name' => $levelData[$level]['name'],
            'description' => $levelData[$level]['description'],
            'type' => 'n8n_level',
            'criteria' => json_encode(['n8n_level' => $level]),
            'icon' => $level . '.svg',
        ]);
    }

    /**
     * Badge de complétion de tutoriel
     */
    public function tutorialCompletion(int $count = 1): static
    {
        $names = [
            1 => 'Premier Tutoriel',
            5 => 'Apprenant Assidu',
            10 => 'Étudiant Dévoué',
            20 => 'Maître des Workflows',
            50 => 'Expert AutomateHub'
        ];

        $name = $names[$count] ?? "Compléteur de {$count} Tutoriels";

        return $this->state(fn (array $attributes) => [
            'name' => $name,
            'description' => "{$count} tutoriel" . ($count > 1 ? 's' : '') . " complété" . ($count > 1 ? 's' : ''),
            'type' => 'tutorial_completion',
            'criteria' => json_encode(['tutorials_completed' => $count]),
            'icon' => 'tutorial-' . $count . '.svg',
        ]);
    }

    /**
     * Badge de téléchargement
     */
    public function download(int $count = 1): static
    {
        $names = [
            1 => 'Premier Téléchargement',
            10 => 'Collectionneur',
            25 => 'Archiviste',
            50 => 'Bibliothécaire',
            100 => 'Maître Collectionneur'
        ];

        $name = $names[$count] ?? "Téléchargeur de {$count} Fichiers";

        return $this->state(fn (array $attributes) => [
            'name' => $name,
            'description' => "{$count} fichier" . ($count > 1 ? 's' : '') . " téléchargé" . ($count > 1 ? 's' : ''),
            'type' => 'download',
            'criteria' => json_encode(['downloads_count' => $count]),
            'icon' => 'download-' . $count . '.svg',
        ]);
    }

    /**
     * Badge d'engagement
     */
    public function engagement(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Utilisateur Actif',
            'description' => 'Actif pendant 7 jours consécutifs',
            'type' => 'engagement',
            'criteria' => json_encode(['consecutive_days' => 7]),
            'icon' => 'active-user.svg',
        ]);
    }

    /**
     * Badge spécial
     */
    public function special(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Pionnier',
            'description' => 'Parmi les 100 premiers utilisateurs',
            'type' => 'special',
            'criteria' => json_encode(['user_rank' => 100]),
            'icon' => 'pioneer.svg',
        ]);
    }

    /**
     * Badge d'événement spécial
     */
    public function specialEvent(string $event = 'beta_program'): static
    {
        $eventData = [
            'beta_program' => ['name' => 'Participant Beta', 'description' => 'Participant au programme beta'],
            'launch_day' => ['name' => 'Jour de Lancement', 'description' => 'Présent le jour du lancement'],
            'christmas_2024' => ['name' => 'Noël 2024', 'description' => 'Actif pendant les fêtes 2024'],
        ];

        $data = $eventData[$event] ?? ['name' => 'Événement Spécial', 'description' => 'Participant à un événement spécial'];

        return $this->state(fn (array $attributes) => [
            'name' => $data['name'],
            'description' => $data['description'],
            'type' => 'special_event',
            'criteria' => json_encode(['event' => $event]),
            'icon' => $event . '.svg',
        ]);
    }

    /**
     * Badge actif
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Badge inactif
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Badge rare (ordre de tri élevé)
     */
    public function rare(): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => $this->faker->numberBetween(80, 100),
            'color' => $this->faker->randomElement(['#FFD700', '#C0C0C0', '#CD7F32']), // Or, Argent, Bronze
        ]);
    }

    /**
     * Badge commun (ordre de tri bas)
     */
    public function common(): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => $this->faker->numberBetween(1, 20),
        ]);
    }
}
