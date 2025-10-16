<?php

namespace Database\Seeders;

use App\Models\ApiService;
use App\Models\ApiPricingPlan;
use App\Models\CreditPack;
use Illuminate\Database\Seeder;

class ApiMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        // Content Extractor API
        $contentExtractor = ApiService::create([
            'slug' => 'content-extractor',
            'name' => 'Content Extractor',
            'description' => 'Extrayez du contenu de YouTube, sites web et plus pour vos workflows n8n',
            'icon' => 'fas fa-file-download',
            'category' => 'extraction',
            'features' => [
                'Extraction de transcriptions YouTube',
                'Scraping de pages web',
                'Conversion en markdown',
                'Extraction de métadonnées',
                'Support multi-langues',
                'API REST simple'
            ],
            'endpoint_base' => 'http://localhost:5682',
            'node_package' => 'n8n-nodes-content-extractor',
            'default_quota' => 10
        ]);
        
        // Plans tarifaires pour Content Extractor
        ApiPricingPlan::create([
            'api_service_id' => $contentExtractor->id,
            'name' => 'Gratuit',
            'monthly_price' => 0,
            'monthly_quota' => 10,
            'extra_credit_price' => 0.50,
            'features' => [
                '10 extractions/mois',
                'Support communauté',
                'Accès API complet'
            ],
            'sort_order' => 1
        ]);
        
        ApiPricingPlan::create([
            'api_service_id' => $contentExtractor->id,
            'name' => 'Starter',
            'monthly_price' => 9.90,
            'monthly_quota' => 100,
            'extra_credit_price' => 0.10,
            'features' => [
                '100 extractions/mois',
                'Support prioritaire',
                'Webhook notifications',
                'Batch processing'
            ],
            'sort_order' => 2
        ]);
        
        ApiPricingPlan::create([
            'api_service_id' => $contentExtractor->id,
            'name' => 'Pro',
            'monthly_price' => 29.90,
            'monthly_quota' => 500,
            'extra_credit_price' => 0.06,
            'features' => [
                '500 extractions/mois',
                'Support dédié',
                'API prioritaire',
                'Extraction en masse',
                'Webhook avancés'
            ],
            'sort_order' => 3
        ]);
        
        ApiPricingPlan::create([
            'api_service_id' => $contentExtractor->id,
            'name' => 'Business',
            'monthly_price' => 99.90,
            'monthly_quota' => 2000,
            'extra_credit_price' => 0.04,
            'features' => [
                '2000 extractions/mois',
                'Support 24/7',
                'SLA garanti',
                'API dédiée',
                'Formation incluse'
            ],
            'sort_order' => 4
        ]);
        
        // Packs de crédits
        CreditPack::create([
            'api_service_id' => $contentExtractor->id,
            'name' => 'Pack 50',
            'credits' => 50,
            'price' => 4.99,
            'discount_percentage' => 0
        ]);
        
        CreditPack::create([
            'api_service_id' => $contentExtractor->id,
            'name' => 'Pack 200',
            'credits' => 200,
            'price' => 16.99,
            'discount_percentage' => 15
        ]);
        
        CreditPack::create([
            'api_service_id' => $contentExtractor->id,
            'name' => 'Pack 500',
            'credits' => 500,
            'price' => 34.99,
            'discount_percentage' => 30
        ]);
        
        // Future API : Image Generator (exemple)
        $imageGenerator = ApiService::create([
            'slug' => 'image-generator',
            'name' => 'AI Image Generator',
            'description' => 'Générez des images avec l\'IA pour vos contenus automatiques',
            'icon' => 'fas fa-image',
            'category' => 'ai',
            'features' => [
                'Génération d\'images HD',
                'Styles multiples',
                'API rapide',
                'Formats variés'
            ],
            'endpoint_base' => 'http://localhost:5683',
            'node_package' => 'n8n-nodes-image-generator',
            'default_quota' => 5,
            'is_active' => false // Pas encore actif
        ]);
        
        $this->command->info('API Marketplace seeded successfully!');
    }
}
