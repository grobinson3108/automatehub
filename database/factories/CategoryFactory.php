<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Automatisation Web',
            'Intégrations API',
            'Workflows E-commerce',
            'Notifications et Alertes',
            'Traitement de Données',
            'Réseaux Sociaux',
            'CRM et Marketing',
            'Productivité',
            'Monitoring',
            'Base de Données'
        ]);
        
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => $this->faker->sentence(8),
            'icon' => $this->faker->randomElement([
                'fas fa-globe',
                'fas fa-cogs',
                'fas fa-shopping-cart',
                'fas fa-bell',
                'fas fa-database',
                'fas fa-share-alt',
                'fas fa-chart-line',
                'fas fa-tasks',
                'fas fa-eye',
                'fas fa-server'
            ]),
            'color' => $this->faker->hexColor(),
            'is_active' => $this->faker->boolean(90), // 90% de chance d'être active
            'sort_order' => $this->faker->numberBetween(1, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Catégorie active
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Catégorie inactive
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Catégorie populaire (avec beaucoup de tutoriels)
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => $this->faker->numberBetween(1, 10),
        ]);
    }
}
