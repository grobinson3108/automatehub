<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pack;
use Illuminate\Support\Str;

class PackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packs = [
            // PACKS PREMIUM (50€+)
            [
                'name' => 'Enterprise Domination Suite',
                'marketing_title' => '🏆 Suite Complète Enterprise - Dominez Votre Marché',
                'tagline' => 'Arsenal complet pour conquérir votre marché comme les Fortune 500. Automatisation enterprise de classe mondiale.',
                'price_eur' => 97.00,
                'price_usd' => 107.00,
                'original_price_eur' => 197.00,
                'original_price_usd' => 217.00,
                'category' => 'business',
                'complexity' => 'Avancé',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/33_ENTERPRISE_DOMINATION_SUITE_97EUR',
                'is_featured' => true,
            ],
            [
                'name' => 'AI Master Wealth Collection',
                'marketing_title' => '🧙 Collection Maître IA - Construisez Votre Empire',
                'tagline' => 'Tous les secrets IA pour construire votre empire digital. Intelligence artificielle au service de votre richesse.',
                'price_eur' => 87.00,
                'price_usd' => 97.00,
                'original_price_eur' => 177.00,
                'original_price_usd' => 197.00,
                'category' => 'ia',
                'complexity' => 'Avancé',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/34_AI_MASTER_WEALTH_COLLECTION_87EUR',
                'is_featured' => true,
            ],
            [
                'name' => 'Crypto DexScreener Millionaire',
                'marketing_title' => '🚀 DexScreener Pro - Trading Crypto Automatisé',
                'tagline' => 'Workflows professionnels qui génèrent 1000€/jour en trading crypto. Devenez un pro avec DexScreener.',
                'price_eur' => 67.00,
                'price_usd' => 74.00,
                'original_price_eur' => 127.00,
                'original_price_usd' => 140.00,
                'category' => 'crypto',
                'complexity' => 'Avancé',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/01_CRYPTO_DEXSCREENER_MILLIONAIRE_67EUR',
                'is_featured' => true,
            ],
            [
                'name' => 'AI Crypto Wealth Machine',
                'marketing_title' => '💰 Machine à Richesse IA + Crypto 24/7',
                'tagline' => 'Fusion ultime IA + Crypto qui génère des profits 24h/24 automatiquement. La machine à cash parfaite.',
                'price_eur' => 67.00,
                'price_usd' => 74.00,
                'original_price_eur' => 127.00,
                'original_price_usd' => 140.00,
                'category' => 'crypto',
                'complexity' => 'Avancé',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/29_AI_CRYPTO_WEALTH_MACHINE_67EUR',
                'is_featured' => true,
            ],
            [
                'name' => 'Email AI CRM Trinity Power',
                'marketing_title' => '🔱 Trinité Email + IA + CRM - Puissance Ultime',
                'tagline' => 'Triple force qui transforme prospects en clients fidèles. Automation CRM avec IA avancée.',
                'price_eur' => 57.00,
                'price_usd' => 63.00,
                'original_price_eur' => 107.00,
                'original_price_usd' => 118.00,
                'category' => 'marketing',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/30_EMAIL_AI_CRM_TRINITY_POWER_57EUR',
            ],
            [
                'name' => 'Telegram Crypto Empire',
                'marketing_title' => '💎 Empire Telegram Crypto - Bots 24/7',
                'tagline' => 'Bots qui analysent et tradent 24h/24 pour vous enrichir. Trading crypto automatisé sur Telegram.',
                'price_eur' => 52.00,
                'price_usd' => 57.00,
                'original_price_eur' => 102.00,
                'original_price_usd' => 112.00,
                'category' => 'crypto',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/06_TELEGRAM_CRYPTO_EMPIRE_52EUR',
            ],
            [
                'name' => 'CRM Sales Accelerator',
                'marketing_title' => '🚀 Accélérateur CRM - Multipliez Vos Ventes x5',
                'tagline' => 'IA qui qualifie vos leads et multiplie vos revenus par 5. CRM intelligent et automatisé.',
                'price_eur' => 52.00,
                'price_usd' => 57.00,
                'original_price_eur' => 102.00,
                'original_price_usd' => 112.00,
                'category' => 'business',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/17_CRM_SALES_ACCELERATOR_52EUR',
            ],
            [
                'name' => 'Ecommerce Profit Maximizer',
                'marketing_title' => '💎 Maximiseur E-commerce - IA Prix & Stock',
                'tagline' => 'IA qui optimise prix, stock et ventes automatiquement. Maximisez vos profits e-commerce.',
                'price_eur' => 52.00,
                'price_usd' => 57.00,
                'original_price_eur' => 102.00,
                'original_price_usd' => 112.00,
                'category' => 'business',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/18_ECOMMERCE_PROFIT_MAXIMIZER_52EUR',
            ],
            [
                'name' => 'Social AI Influence Empire',
                'marketing_title' => '👑 Empire Influence - Social Media + IA',
                'tagline' => 'Devenez influenceur avec une audience engagée automatiquement. Growth hacking IA pour réseaux sociaux.',
                'price_eur' => 52.00,
                'price_usd' => 57.00,
                'original_price_eur' => 102.00,
                'original_price_usd' => 112.00,
                'category' => 'marketing',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/31_SOCIAL_AI_INFLUENCE_EMPIRE_52EUR',
            ],

            // PACKS PROFESSIONNELS (35-49€)
            [
                'name' => 'Blockchain Trading Empire',
                'marketing_title' => '⚡ Empire Trading Blockchain - Hedge Fund Auto',
                'tagline' => 'Automatisez vos gains crypto comme un hedge fund professionnel. Trading blockchain avancé.',
                'price_eur' => 47.00,
                'price_usd' => 52.00,
                'original_price_eur' => 87.00,
                'original_price_usd' => 96.00,
                'category' => 'crypto',
                'complexity' => 'Avancé',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/02_BLOCKCHAIN_TRADING_EMPIRE_47EUR',
            ],
            [
                'name' => 'IA Business Révolution',
                'marketing_title' => '🤖 Révolution Business IA - OpenAI Pro',
                'tagline' => 'Automation OpenAI qui remplace 10 employés. Révolutionnez votre business avec l\'IA.',
                'price_eur' => 47.00,
                'price_usd' => 52.00,
                'original_price_eur' => 87.00,
                'original_price_usd' => 96.00,
                'category' => 'ia',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/04_IA_BUSINESS_REVOLUTION_47EUR',
            ],
            [
                'name' => 'Social Media Viral Engine',
                'marketing_title' => '🔥 Moteur Viral - Explosez Votre Audience',
                'tagline' => 'IA qui crée du contenu viral et explose votre audience. Social media marketing automatisé.',
                'price_eur' => 47.00,
                'price_usd' => 52.00,
                'original_price_eur' => 87.00,
                'original_price_usd' => 96.00,
                'category' => 'marketing',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/19_SOCIAL_MEDIA_VIRAL_ENGINE_47EUR',
            ],
            [
                'name' => 'Database Insights Genius',
                'marketing_title' => '🧠 Génie Insights BDD - IA Analytics',
                'tagline' => 'IA qui analyse vos données et révèle des opportunités cachées. Business intelligence automatisée.',
                'price_eur' => 47.00,
                'price_usd' => 52.00,
                'original_price_eur' => 87.00,
                'original_price_usd' => 96.00,
                'category' => 'business',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/20_DATABASE_INSIGHTS_GENIUS_47EUR',
            ],
            [
                'name' => 'Telegram AI Assistant Suprême',
                'marketing_title' => '🧠 Assistant IA Telegram - Cerveau 24/7',
                'tagline' => 'Votre cerveau artificiel personnel disponible 24h/24 sur Telegram. Assistant IA suprême.',
                'price_eur' => 42.00,
                'price_usd' => 46.00,
                'original_price_eur' => 82.00,
                'original_price_usd' => 90.00,
                'category' => 'ia',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/07_TELEGRAM_AI_ASSISTANT_SUPREME_42EUR',
            ],
            [
                'name' => 'Email Marketing Millionaire',
                'marketing_title' => '💰 Email Marketing - Conversion 47%',
                'tagline' => 'IA qui écrit et envoie des emails qui convertissent à 47%. Email marketing millionnaire.',
                'price_eur' => 42.00,
                'price_usd' => 46.00,
                'original_price_eur' => 82.00,
                'original_price_usd' => 90.00,
                'category' => 'marketing',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/10_EMAIL_MARKETING_MILLIONAIRE_42EUR',
            ],
            [
                'name' => 'Google Sheets Data Genius',
                'marketing_title' => '📈 Génie Google Sheets - IA Analytics',
                'tagline' => 'IA qui transforme vos tableaux en insights business puissants. Data genius pour Sheets.',
                'price_eur' => 42.00,
                'price_usd' => 46.00,
                'original_price_eur' => 82.00,
                'original_price_usd' => 90.00,
                'category' => 'business',
                'complexity' => 'Débutant',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/13_GOOGLE_SHEETS_DATA_GENIUS_42EUR',
            ],
            [
                'name' => 'Team Collaboration Révolution',
                'marketing_title' => '🤝 Révolution Collaboration - Chef d\'Orchestre IA',
                'tagline' => 'IA qui coordonne vos teams comme un chef d\'orchestre. Collaboration révolutionnaire.',
                'price_eur' => 42.00,
                'price_usd' => 46.00,
                'original_price_eur' => 82.00,
                'original_price_usd' => 90.00,
                'category' => 'business',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/25_TEAM_COLLABORATION_REVOLUTION_42EUR',
            ],
            [
                'name' => 'Content Marketing Empire',
                'marketing_title' => '📝 Empire Content Marketing - Stratégie IA',
                'tagline' => 'IA qui crée une stratégie content et l\'exécute parfaitement. Marketing automation complet.',
                'price_eur' => 42.00,
                'price_usd' => 46.00,
                'original_price_eur' => 82.00,
                'original_price_usd' => 90.00,
                'category' => 'marketing',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/26_CONTENT_MARKETING_EMPIRE_42EUR',
            ],
            [
                'name' => 'Business Efficiency Maximizer',
                'marketing_title' => '⚙️ Maximiseur Efficacité - Optimisation Totale',
                'tagline' => 'Optimisez chaque processus, éliminez le gaspillage. Efficacité business maximale.',
                'price_eur' => 42.00,
                'price_usd' => 46.00,
                'original_price_eur' => 82.00,
                'original_price_usd' => 90.00,
                'category' => 'business',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/27_BUSINESS_EFFICIENCY_MAXIMIZER_42EUR',
            ],
            [
                'name' => 'Content Viral Factory',
                'marketing_title' => '🔥 Factory Contenu Viral - 100 Posts/Jour',
                'tagline' => 'IA qui génère 100 posts/jour et fait exploser votre audience. Factory de contenu viral.',
                'price_eur' => 39.00,
                'price_usd' => 43.00,
                'original_price_eur' => 79.00,
                'original_price_usd' => 87.00,
                'category' => 'marketing',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/05_CONTENT_VIRAL_FACTORY_39EUR',
            ],
            [
                'name' => 'CoinGecko Profit Machine',
                'marketing_title' => '📊 Machine CoinGecko - Data Mining Crypto',
                'tagline' => 'Data mining crypto qui révèle les pépites avant tout le monde. Profit machine CoinGecko.',
                'price_eur' => 37.00,
                'price_usd' => 41.00,
                'original_price_eur' => 77.00,
                'original_price_usd' => 85.00,
                'category' => 'crypto',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/03_COINGECKO_PROFIT_MACHINE_37EUR',
            ],
            [
                'name' => 'Telegram Lead Magnet',
                'marketing_title' => '🧲 Aimant Prospects Telegram - Vente Auto',
                'tagline' => 'Transformez chaque message en vente automatique. Lead magnet Telegram puissant.',
                'price_eur' => 37.00,
                'price_usd' => 41.00,
                'original_price_eur' => 77.00,
                'original_price_usd' => 85.00,
                'category' => 'marketing',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/09_TELEGRAM_LEAD_MAGNET_37EUR',
            ],
            [
                'name' => 'Email CRM Sales Machine',
                'marketing_title' => '🎯 Machine Email + CRM - Deals Automatiques',
                'tagline' => 'Nurturez vos prospects et fermez des deals automatiquement. Email CRM sales automation.',
                'price_eur' => 37.00,
                'price_usd' => 41.00,
                'original_price_eur' => 77.00,
                'original_price_usd' => 85.00,
                'category' => 'marketing',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/12_EMAIL_CRM_SALES_MACHINE_37EUR',
            ],
            [
                'name' => 'Time Management Genius',
                'marketing_title' => '⏱️ Génie Gestion Temps - +4h/Jour',
                'tagline' => 'IA qui vous fait gagner 4h/jour en optimisant tout. Gestion du temps révolutionnaire.',
                'price_eur' => 37.00,
                'price_usd' => 41.00,
                'original_price_eur' => 77.00,
                'original_price_usd' => 85.00,
                'category' => 'business',
                'complexity' => 'Débutant',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/28_TIME_MANAGEMENT_GENIUS_37EUR',
            ],
            [
                'name' => 'Google Workspace Business Suite',
                'marketing_title' => '🏢 Suite Google Workspace - Machine Pro',
                'tagline' => 'Transformez Google Workspace en machine de guerre professionnelle. Suite complète.',
                'price_eur' => 35.00,
                'price_usd' => 39.00,
                'original_price_eur' => 70.00,
                'original_price_usd' => 77.00,
                'category' => 'business',
                'complexity' => 'Débutant',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/16_GOOGLE_WORKSPACE_BUSINESS_SUITE_35EUR',
            ],
            [
                'name' => 'Zapier Killer Alternative',
                'marketing_title' => '⚔️ Alternative Zapier - 10x Plus Puissant',
                'tagline' => '10x plus puissant, 5x moins cher, infiniment personnalisable. Zapier killer.',
                'price_eur' => 35.00,
                'price_usd' => 39.00,
                'original_price_eur' => 70.00,
                'original_price_usd' => 77.00,
                'category' => 'business',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/23_ZAPIER_KILLER_ALTERNATIVE_35EUR',
            ],
            [
                'name' => 'Slack Team Supercharger',
                'marketing_title' => '⚡ Surcharge Slack - Productivité x3',
                'tagline' => 'Multipliez la productivité de votre team par 3 instantanément. Slack supercharged.',
                'price_eur' => 35.00,
                'price_usd' => 39.00,
                'original_price_eur' => 70.00,
                'original_price_usd' => 77.00,
                'category' => 'business',
                'complexity' => 'Débutant',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/24_SLACK_TEAM_SUPERCHARGER_35EUR',
            ],

            // PACKS STANDARDS (25-34€)
            [
                'name' => 'Telegram Marketing Domination',
                'marketing_title' => '📱 Domination Marketing Telegram - 10K Clients',
                'tagline' => 'Automatisez vos ventes et fidélisez 10000 clients via Telegram. Domination marketing.',
                'price_eur' => 32.00,
                'price_usd' => 35.00,
                'original_price_eur' => 64.00,
                'original_price_usd' => 70.00,
                'category' => 'marketing',
                'complexity' => 'Intermédiaire',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/08_TELEGRAM_MARKETING_DOMINATION_32EUR',
            ],
            [
                'name' => 'Gmail Productivity Beast',
                'marketing_title' => '⚡ Bête Productivité Gmail - 1000 Emails/Jour',
                'tagline' => 'Gérez 1000 emails/jour sans effort, triez tout automatiquement. Gmail beast mode.',
                'price_eur' => 32.00,
                'price_usd' => 35.00,
                'original_price_eur' => 64.00,
                'original_price_usd' => 70.00,
                'category' => 'business',
                'complexity' => 'Débutant',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/11_GMAIL_PRODUCTIVITY_BEAST_32EUR',
            ],
            [
                'name' => 'Automation Ecosystem Builder',
                'marketing_title' => '🏗️ Architecte Écosystème - Empire Digital',
                'tagline' => 'Construisez votre empire digital interconnecté. Architecte automation professionnel.',
                'price_eur' => 32.00,
                'price_usd' => 35.00,
                'original_price_eur' => 64.00,
                'original_price_usd' => 70.00,
                'category' => 'business',
                'complexity' => 'Avancé',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/22_AUTOMATION_ECOSYSTEM_BUILDER_32EUR',
            ],
            [
                'name' => 'API Integration Wizard',
                'marketing_title' => '🪄 Magicien Intégrations API - Connectez Tout',
                'tagline' => 'Connectez tout à tout, créez votre écosystème parfait. Wizard intégrations API.',
                'price_eur' => 29.00,
                'price_usd' => 32.00,
                'original_price_eur' => 59.00,
                'original_price_usd' => 65.00,
                'category' => 'business',
                'complexity' => 'Avancé',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/21_API_INTEGRATION_WIZARD_29EUR',
            ],
            [
                'name' => 'Google Drive Organisation King',
                'marketing_title' => '👑 Roi Organisation Drive - Recherche 2 Sec',
                'tagline' => 'Triez, classez et retrouvez n\'importe quel fichier en 2 secondes. Drive organisation king.',
                'price_eur' => 27.00,
                'price_usd' => 30.00,
                'original_price_eur' => 54.00,
                'original_price_usd' => 59.00,
                'category' => 'business',
                'complexity' => 'Débutant',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/14_GOOGLE_DRIVE_ORGANISATION_KING_27EUR',
            ],
            [
                'name' => 'Google Calendar Time Master',
                'marketing_title' => '⏰ Maître Temps Calendar - Sync Parfaite',
                'tagline' => 'Synchronisez votre vie, ne ratez plus jamais un RDV important. Time master Calendar.',
                'price_eur' => 25.00,
                'price_usd' => 28.00,
                'original_price_eur' => 50.00,
                'original_price_usd' => 55.00,
                'category' => 'business',
                'complexity' => 'Débutant',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/15_GOOGLE_CALENDAR_TIME_MASTER_25EUR',
            ],

            // PACK STARTER
            [
                'name' => 'Automation Starter Success',
                'marketing_title' => '🌱 Starter Automation - Résultats 24h',
                'tagline' => 'Kit parfait pour commencer et voir des résultats en 24h. Starter automation success.',
                'price_eur' => 19.00,
                'price_usd' => 21.00,
                'original_price_eur' => 39.00,
                'original_price_usd' => 43.00,
                'category' => 'business',
                'complexity' => 'Débutant',
                'folder_path' => 'PACKS_WORKFLOWS_VENDEURS/32_AUTOMATION_STARTER_SUCCESS_19EUR',
            ],
        ];

        foreach ($packs as $packData) {
            // Generate slug from name
            $packData['slug'] = Str::slug($packData['name']);

            // Set default values
            $packData['description'] = $packData['tagline'];
            $packData['is_active'] = true;
            $packData['workflows_count'] = rand(8, 25); // Will be updated when we scan folders

            // Add features based on category
            $packData['features'] = $this->getFeaturesByCategory($packData['category']);

            // Add benefits
            $packData['benefits'] = [
                'Workflows prêts à l\'emploi',
                'Guide d\'installation inclus',
                'Support communauté Skool gratuit',
                'Mises à jour gratuites à vie',
                'Garantie 30 jours satisfait ou remboursé'
            ];

            // Add requirements
            $packData['requirements'] = [
                'Compte n8n (gratuit ou cloud)',
                'APIs nécessaires selon workflows',
                'Connaissances de base n8n recommandées'
            ];

            Pack::create($packData);
        }

        $this->command->info('34 packs created successfully!');
    }

    /**
     * Get features based on pack category
     */
    private function getFeaturesByCategory($category)
    {
        $features = [
            'crypto' => [
                'Tracking prix en temps réel',
                'Alertes automatiques',
                'Analyse de marché IA',
                'Trading signals automatisés',
                'Dashboard analytics complet'
            ],
            'ia' => [
                'Intégration OpenAI/Claude',
                'Prompts optimisés inclus',
                'Génération de contenu automatique',
                'Analyse IA avancée',
                'Templates personnalisables'
            ],
            'marketing' => [
                'Publication automatique multi-plateformes',
                'Génération de contenu IA',
                'Analytics et reporting',
                'A/B testing automatisé',
                'Optimisation engagement'
            ],
            'business' => [
                'Automatisation complète',
                'Intégrations multiples',
                'Gain de temps considérable',
                'ROI mesurable',
                'Scalabilité optimale'
            ]
        ];

        return $features[$category] ?? $features['business'];
    }
}
