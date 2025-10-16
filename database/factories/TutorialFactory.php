<?php

namespace Database\Factories;

use App\Models\Tutorial;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tutorial>
 */
class TutorialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(4);
        
        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'description' => $this->faker->paragraph(3),
            'content' => $this->generateContent(),
            'category_id' => Category::factory(),
            'difficulty' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'duration_minutes' => $this->faker->numberBetween(15, 180),
            'subscription_required' => $this->faker->randomElement(['free', 'premium', 'pro']),
            'is_published' => $this->faker->boolean(80), // 80% de chance d'être publié
            'featured' => $this->faker->boolean(20), // 20% de chance d'être featured
            'views_count' => $this->faker->numberBetween(0, 1000),
            'downloads_count' => $this->faker->numberBetween(0, 500),
            'likes_count' => $this->faker->numberBetween(0, 200),
            'files' => $this->generateFiles(),
            'meta_title' => $title,
            'meta_description' => $this->faker->sentence(10),
            'published_at' => $this->faker->optional(0.8)->dateTimeBetween('-6 months', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Génère un contenu de tutoriel réaliste
     */
    private function generateContent(): string
    {
        $sections = [
            "## Introduction\n\n" . $this->faker->paragraph(2),
            "## Prérequis\n\n" . $this->faker->paragraph(1),
            "## Étape 1 : Configuration\n\n" . $this->faker->paragraph(3),
            "## Étape 2 : Création du workflow\n\n" . $this->faker->paragraph(4),
            "## Étape 3 : Tests et validation\n\n" . $this->faker->paragraph(2),
            "## Conclusion\n\n" . $this->faker->paragraph(1),
        ];

        return implode("\n\n", $sections);
    }

    /**
     * Génère une liste de fichiers pour le tutoriel
     */
    private function generateFiles(): string
    {
        $files = [];
        $fileTypes = ['workflow.json', 'guide.pdf', 'examples.zip', 'config.json', 'readme.md'];
        
        $numFiles = $this->faker->numberBetween(1, 3);
        for ($i = 0; $i < $numFiles; $i++) {
            $files[] = $this->faker->randomElement($fileTypes);
        }

        return json_encode(array_unique($files));
    }

    /**
     * Tutoriel gratuit
     */
    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_required' => 'free',
        ]);
    }

    /**
     * Tutoriel premium
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_required' => 'premium',
        ]);
    }

    /**
     * Tutoriel pro
     */
    public function pro(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_required' => 'pro',
        ]);
    }

    /**
     * Tutoriel publié
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    /**
     * Tutoriel en brouillon
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    /**
     * Tutoriel featured
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
            'is_published' => true,
        ]);
    }

    /**
     * Tutoriel populaire
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'views_count' => $this->faker->numberBetween(500, 2000),
            'downloads_count' => $this->faker->numberBetween(200, 800),
            'likes_count' => $this->faker->numberBetween(50, 300),
        ]);
    }

    /**
     * Tutoriel pour débutants
     */
    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty' => 'beginner',
            'duration_minutes' => $this->faker->numberBetween(15, 60),
        ]);
    }

    /**
     * Tutoriel intermédiaire
     */
    public function intermediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty' => 'intermediate',
            'duration_minutes' => $this->faker->numberBetween(45, 120),
        ]);
    }

    /**
     * Tutoriel avancé
     */
    public function advanced(): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty' => 'advanced',
            'duration_minutes' => $this->faker->numberBetween(90, 180),
        ]);
    }

    /**
     * Tutoriel avec beaucoup de fichiers
     */
    public function withManyFiles(): static
    {
        return $this->state(fn (array $attributes) => [
            'files' => json_encode([
                'workflow.json',
                'guide.pdf',
                'examples.zip',
                'config.json',
                'readme.md',
                'screenshots.zip',
                'templates.json'
            ]),
        ]);
    }

    /**
     * Tutoriel récent
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'published_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Tutoriel ancien
     */
    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-2 years', '-6 months'),
            'published_at' => $this->faker->dateTimeBetween('-2 years', '-6 months'),
        ]);
    }
}
