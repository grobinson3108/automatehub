<?php

namespace Database\Factories;

use App\Models\UserTutorialProgress;
use App\Models\User;
use App\Models\Tutorial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserTutorialProgress>
 */
class UserTutorialProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $completed = $this->faker->boolean(70); // 70% de chance d'être complété
        $progress = $completed ? 100 : $this->faker->numberBetween(10, 95);
        
        return [
            'user_id' => User::factory(),
            'tutorial_id' => Tutorial::factory(),
            'progress_percentage' => $progress,
            'completed' => $completed,
            'completed_at' => $completed ? $this->faker->dateTimeBetween('-3 months', 'now') : null,
            'time_spent_minutes' => $this->faker->numberBetween(5, 180),
            'last_accessed_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'notes' => $this->faker->optional(0.3)->paragraph(), // 30% de chance d'avoir des notes
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Tutoriel complété
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'progress_percentage' => 100,
            'completed' => true,
            'completed_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'time_spent_minutes' => $this->faker->numberBetween(30, 180),
        ]);
    }

    /**
     * Tutoriel en cours
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'progress_percentage' => $this->faker->numberBetween(10, 95),
            'completed' => false,
            'completed_at' => null,
            'time_spent_minutes' => $this->faker->numberBetween(5, 120),
            'last_accessed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Tutoriel commencé récemment
     */
    public function started(): static
    {
        return $this->state(fn (array $attributes) => [
            'progress_percentage' => $this->faker->numberBetween(1, 25),
            'completed' => false,
            'completed_at' => null,
            'time_spent_minutes' => $this->faker->numberBetween(5, 30),
            'last_accessed_at' => $this->faker->dateTimeBetween('-3 days', 'now'),
        ]);
    }

    /**
     * Tutoriel abandonné
     */
    public function abandoned(): static
    {
        return $this->state(fn (array $attributes) => [
            'progress_percentage' => $this->faker->numberBetween(5, 50),
            'completed' => false,
            'completed_at' => null,
            'time_spent_minutes' => $this->faker->numberBetween(5, 60),
            'last_accessed_at' => $this->faker->dateTimeBetween('-2 months', '-1 month'),
        ]);
    }

    /**
     * Progression récente
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_accessed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Progression ancienne
     */
    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_accessed_at' => $this->faker->dateTimeBetween('-6 months', '-2 months'),
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-6 months'),
        ]);
    }

    /**
     * Avec des notes détaillées
     */
    public function withNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => $this->faker->paragraphs(3, true),
        ]);
    }

    /**
     * Progression rapide (beaucoup de temps passé)
     */
    public function intensive(): static
    {
        return $this->state(fn (array $attributes) => [
            'time_spent_minutes' => $this->faker->numberBetween(120, 300),
            'progress_percentage' => $this->faker->numberBetween(50, 100),
        ]);
    }

    /**
     * Progression lente (peu de temps passé)
     */
    public function casual(): static
    {
        return $this->state(fn (array $attributes) => [
            'time_spent_minutes' => $this->faker->numberBetween(5, 45),
            'progress_percentage' => $this->faker->numberBetween(10, 60),
        ]);
    }

    /**
     * Complété récemment
     */
    public function recentlyCompleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'progress_percentage' => 100,
            'completed' => true,
            'completed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'last_accessed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Complété il y a longtemps
     */
    public function completedLongAgo(): static
    {
        return $this->state(fn (array $attributes) => [
            'progress_percentage' => 100,
            'completed' => true,
            'completed_at' => $this->faker->dateTimeBetween('-6 months', '-2 months'),
            'last_accessed_at' => $this->faker->dateTimeBetween('-6 months', '-2 months'),
        ]);
    }
}
