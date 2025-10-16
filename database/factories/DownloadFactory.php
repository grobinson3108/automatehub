<?php

namespace Database\Factories;

use App\Models\Download;
use App\Models\User;
use App\Models\Tutorial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Download>
 */
class DownloadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tutorial_id' => Tutorial::factory(),
            'file_name' => $this->faker->randomElement([
                'workflow.json',
                'guide.pdf',
                'examples.zip',
                'config.json',
                'readme.md',
                'templates.json',
                'screenshots.zip'
            ]),
            'file_size' => $this->faker->numberBetween(1024, 10485760), // 1KB à 10MB
            'download_count' => $this->faker->numberBetween(1, 5),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Téléchargement récent
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Téléchargement ancien
     */
    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
        ]);
    }

    /**
     * Téléchargement de ce mois
     */
    public function thisMonth(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween(now()->startOfMonth(), 'now'),
        ]);
    }

    /**
     * Gros fichier
     */
    public function largeFile(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_name' => $this->faker->randomElement(['large-dataset.zip', 'complete-workflows.zip', 'full-course.pdf']),
            'file_size' => $this->faker->numberBetween(50000000, 100000000), // 50MB à 100MB
        ]);
    }

    /**
     * Petit fichier
     */
    public function smallFile(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_name' => $this->faker->randomElement(['config.json', 'readme.md', 'simple-workflow.json']),
            'file_size' => $this->faker->numberBetween(1024, 100000), // 1KB à 100KB
        ]);
    }

    /**
     * Téléchargement multiple
     */
    public function multipleDownloads(): static
    {
        return $this->state(fn (array $attributes) => [
            'download_count' => $this->faker->numberBetween(3, 10),
        ]);
    }

    /**
     * Premier téléchargement
     */
    public function firstDownload(): static
    {
        return $this->state(fn (array $attributes) => [
            'download_count' => 1,
        ]);
    }
}
