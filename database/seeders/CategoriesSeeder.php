<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Débutant',
                'slug' => 'debutant',
                'description' => 'Tutoriels pour débuter avec n8n. Apprenez les bases de l\'automatisation.',
                'is_premium' => false,
            ],
            [
                'name' => 'Intégrations populaires',
                'slug' => 'integrations-populaires',
                'description' => 'Découvrez comment connecter n8n avec les services les plus utilisés.',
                'is_premium' => false,
            ],
            [
                'name' => 'Automatisation e-commerce',
                'slug' => 'automatisation-ecommerce',
                'description' => 'Automatisez vos processus e-commerce avec n8n.',
                'is_premium' => true,
            ],
            [
                'name' => 'Marketing automation',
                'slug' => 'marketing-automation',
                'description' => 'Créez des campagnes marketing automatisées efficaces.',
                'is_premium' => true,
            ],
            [
                'name' => 'Gestion de données',
                'slug' => 'gestion-donnees',
                'description' => 'Manipulez et transformez vos données avec n8n.',
                'is_premium' => false,
            ],
            [
                'name' => 'Workflows avancés',
                'slug' => 'workflows-avances',
                'description' => 'Tutoriels pour créer des workflows complexes et optimisés.',
                'is_premium' => true,
            ],
            [
                'name' => 'API et webhooks',
                'slug' => 'api-webhooks',
                'description' => 'Maîtrisez les API et webhooks dans vos automatisations.',
                'is_premium' => false,
            ],
            [
                'name' => 'Productivité',
                'slug' => 'productivite',
                'description' => 'Automatisez vos tâches quotidiennes pour gagner en productivité.',
                'is_premium' => false,
            ],
            [
                'name' => 'Entreprise',
                'slug' => 'entreprise',
                'description' => 'Solutions d\'automatisation pour les entreprises.',
                'is_premium' => true,
            ],
            [
                'name' => 'Cas d\'usage spécifiques',
                'slug' => 'cas-usage-specifiques',
                'description' => 'Exemples concrets d\'automatisations métier.',
                'is_premium' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
