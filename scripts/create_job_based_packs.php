#!/usr/bin/env php
<?php
/**
 * Création de packs basés sur les MÉTIERS
 *
 * Ex: "MARKETING MANAGER", "SALES PRO", "CEO SUITE", etc.
 */

define('BASE_PATH', '/var/www/automatehub');
define('GITHUB_WORKFLOWS', BASE_PATH . '/WORKFLOWS_GITHUB_ZIE619/workflows');
define('OUTPUT_DIR', BASE_PATH . '/PACKS_WORKFLOWS_JOBS');

echo "🎯 Création de Packs Orientés MÉTIERS\n";
echo str_repeat('=', 80) . "\n\n";

// Créer le dossier
if (!file_exists(OUTPUT_DIR)) {
    mkdir(OUTPUT_DIR, 0755, true);
}

// Définition des packs MÉTIERS
$jobPacks = [
    'MARKETING_MANAGER_97EUR' => [
        'description' => 'Pack complet pour Marketing Manager',
        'workflows' => [
            'Recherche de tendances' => ['keywords' => ['trend', 'research', 'seo', 'analytics', 'reddit', 'twitter'], 'services' => ['openAi', 'googleSheets']],
            'Génération d\'idées de contenu' => ['keywords' => ['content', 'idea', 'generate', 'brainstorm'], 'services' => ['openAi']],
            'Génération d\'images' => ['keywords' => ['image', 'dalle', 'midjourney', 'stable'], 'services' => ['openAi']],
            'Création de posts sociaux' => ['keywords' => ['post', 'social', 'content', 'publish'], 'services' => ['openAi', 'twitter', 'linkedin', 'facebook']],
            'Publication automatique' => ['keywords' => ['publish', 'schedule', 'post'], 'services' => ['twitter', 'linkedin', 'instagram', 'facebook']],
            'Analytics & Reporting' => ['keywords' => ['analytics', 'report', 'metric', 'dashboard'], 'services' => ['googleSheets', 'airtable']],
            'Curation de contenu' => ['keywords' => ['rss', 'feed', 'curate', 'aggregate'], 'services' => ['rssFeedRead']],
            'Email marketing' => ['keywords' => ['email', 'newsletter', 'campaign'], 'services' => ['gmail', 'mailchimp']],
        ]
    ],

    'SALES_PROFESSIONAL_87EUR' => [
        'description' => 'Workflows pour Sales & Business Development',
        'workflows' => [
            'Lead Generation' => ['keywords' => ['lead', 'prospect', 'find', 'search'], 'services' => ['linkedin', 'hubspot']],
            'Enrichissement de leads' => ['keywords' => ['enrich', 'clearbit', 'hunter'], 'services' => ['clearbit', 'hunterIo']],
            'CRM Automation' => ['keywords' => ['crm', 'contact', 'deal', 'pipeline'], 'services' => ['hubspot', 'salesforce', 'pipedrive']],
            'Follow-up automatique' => ['keywords' => ['follow', 'reminder', 'email', 'sequence'], 'services' => ['gmail', 'hubspot']],
            'Devis & Facturation' => ['keywords' => ['invoice', 'quote', 'proposal'], 'services' => ['stripe', 'invoiceNinja']],
            'Sales Analytics' => ['keywords' => ['analytics', 'report', 'dashboard', 'metric'], 'services' => ['googleSheets', 'hubspot']],
        ]
    ],

    'CEO_EXECUTIVE_SUITE_127EUR' => [
        'description' => 'Suite complète pour CEO & Executives',
        'workflows' => [
            'Dashboard quotidien' => ['keywords' => ['dashboard', 'daily', 'report', 'summary'], 'services' => ['googleSheets', 'slack']],
            'Analytics stratégiques' => ['keywords' => ['analytics', 'metric', 'kpi', 'business'], 'services' => ['googleSheets', 'airtable']],
            'Veille concurrentielle' => ['keywords' => ['competitor', 'monitoring', 'alert'], 'services' => ['openAi', 'googleSheets']],
            'Gestion d\'équipe' => ['keywords' => ['team', 'task', 'project', 'assign'], 'services' => ['slack', 'notion', 'asana']],
            'Automatisation de réunions' => ['keywords' => ['meeting', 'calendar', 'schedule'], 'services' => ['googleCalendar', 'zoom']],
            'Reporting financier' => ['keywords' => ['financial', 'revenue', 'expense', 'budget'], 'services' => ['googleSheets', 'stripe']],
        ]
    ],

    'CONTENT_CREATOR_77EUR' => [
        'description' => 'Workflows pour Créateurs de Contenu',
        'workflows' => [
            'Génération d\'idées AI' => ['keywords' => ['content', 'idea', 'generate', 'creative'], 'services' => ['openAi']],
            'Création de visuels' => ['keywords' => ['image', 'visual', 'design'], 'services' => ['openAi', 'bannerbear']],
            'Publication multi-plateformes' => ['keywords' => ['publish', 'post', 'cross'], 'services' => ['twitter', 'linkedin', 'instagram']],
            'Transcription vidéo/audio' => ['keywords' => ['transcribe', 'whisper', 'audio'], 'services' => ['openAi']],
            'SEO & Optimisation' => ['keywords' => ['seo', 'optimize', 'keyword'], 'services' => ['openAi', 'googleSheets']],
            'Analytics de performance' => ['keywords' => ['analytics', 'engagement', 'metric'], 'services' => ['googleSheets']],
        ]
    ],

    'ECOMMERCE_MANAGER_87EUR' => [
        'description' => 'Automation pour E-commerce',
        'workflows' => [
            'Gestion des commandes' => ['keywords' => ['order', 'shopify', 'woocommerce'], 'services' => ['shopify', 'woocommerce']],
            'Stock & Inventaire' => ['keywords' => ['inventory', 'stock', 'product'], 'services' => ['shopify', 'airtable']],
            'Service client automatisé' => ['keywords' => ['customer', 'support', 'ticket'], 'services' => ['gmail', 'slack']],
            'Marketing automation' => ['keywords' => ['marketing', 'campaign', 'email'], 'services' => ['mailchimp', 'klaviyo']],
            'Gestion des avis' => ['keywords' => ['review', 'rating', 'feedback'], 'services' => ['openAi', 'googleSheets']],
            'Analytics & Reporting' => ['keywords' => ['analytics', 'sales', 'revenue'], 'services' => ['googleSheets', 'shopify']],
        ]
    ],

    'CRYPTO_TRADER_97EUR' => [
        'description' => 'Workflows pour Trading Crypto',
        'workflows' => [
            'Monitoring de prix' => ['keywords' => ['crypto', 'price', 'coinGecko', 'alert'], 'services' => ['coinGecko', 'telegram']],
            'Alertes de trading' => ['keywords' => ['alert', 'signal', 'trade'], 'services' => ['telegram', 'discord']],
            'Portfolio tracking' => ['keywords' => ['portfolio', 'holding', 'balance'], 'services' => ['coinGecko', 'googleSheets']],
            'Analyse de marché' => ['keywords' => ['analysis', 'market', 'trend'], 'services' => ['openAi', 'googleSheets']],
            'News & Veille' => ['keywords' => ['news', 'rss', 'feed'], 'services' => ['rssFeedRead', 'telegram']],
        ]
    ],

    'SOCIAL_MEDIA_MANAGER_77EUR' => [
        'description' => 'Gestion complète des réseaux sociaux',
        'workflows' => [
            'Planification de contenu' => ['keywords' => ['schedule', 'content', 'calendar'], 'services' => ['airtable', 'googleCalendar']],
            'Publication automatique' => ['keywords' => ['publish', 'post', 'schedule'], 'services' => ['twitter', 'linkedin', 'facebook', 'instagram']],
            'Génération de posts AI' => ['keywords' => ['generate', 'content', 'post'], 'services' => ['openAi']],
            'Monitoring de mentions' => ['keywords' => ['mention', 'monitor', 'alert'], 'services' => ['twitter', 'slack']],
            'Analytics sociaux' => ['keywords' => ['analytics', 'engagement', 'metric'], 'services' => ['googleSheets']],
            'Gestion des commentaires' => ['keywords' => ['comment', 'reply', 'engagement'], 'services' => ['openAi']],
        ]
    ],

    'DEVELOPER_PRODUCTIVITY_67EUR' => [
        'description' => 'Workflows pour Développeurs',
        'workflows' => [
            'CI/CD Notifications' => ['keywords' => ['github', 'gitlab', 'deploy', 'build'], 'services' => ['github', 'gitlab', 'slack']],
            'Code Review automation' => ['keywords' => ['review', 'pull', 'request'], 'services' => ['github', 'openAi']],
            'Documentation auto' => ['keywords' => ['documentation', 'generate', 'readme'], 'services' => ['openAi', 'github']],
            'Bug tracking' => ['keywords' => ['bug', 'issue', 'jira'], 'services' => ['jira', 'github']],
            'Monitoring & Alertes' => ['keywords' => ['monitor', 'alert', 'error'], 'services' => ['webhook', 'slack']],
        ]
    ],
];

echo "🎯 Création de " . count($jobPacks) . " packs métiers...\n\n";

// Scanner tous les workflows disponibles
$allWorkflows = scanAllWorkflows();
echo "📊 " . count($allWorkflows) . " workflows disponibles pour la sélection\n\n";

// Créer chaque pack
foreach ($jobPacks as $packName => $packDef) {
    echo "💼 Création du pack: $packName\n";
    echo "   {$packDef['description']}\n";

    $packDir = OUTPUT_DIR . '/' . $packName;
    if (!file_exists($packDir)) {
        mkdir($packDir, 0755, true);
    }

    $selectedCount = 0;

    foreach ($packDef['workflows'] as $workflowType => $criteria) {
        echo "   🔍 Recherche: $workflowType...\n";

        $matches = findMatchingWorkflows($allWorkflows, $criteria);

        if (empty($matches)) {
            echo "      ⚠️  Aucun workflow trouvé\n";
            continue;
        }

        // Prendre les 3 meilleurs
        $selected = array_slice($matches, 0, 3);

        foreach ($selected as $workflow) {
            $selectedCount++;
            $num = str_pad($selectedCount, 2, '0', STR_PAD_LEFT);
            $safeType = preg_replace('/[^a-zA-Z0-9_-]/', '_', $workflowType);
            $filename = $num . '_' . $safeType . '_' . basename($workflow['path']);

            copy($workflow['path'], $packDir . '/' . $filename);
        }

        echo "      ✅ " . count($selected) . " workflow(s) ajouté(s)\n";
    }

    echo "   📊 Total: $selectedCount workflows dans ce pack\n\n";
}

echo "✅ Tous les packs métiers créés!\n";

/**
 * Scanner tous les workflows
 */
function scanAllWorkflows() {
    $workflows = [];

    // GitHub workflows
    if (is_dir(GITHUB_WORKFLOWS)) {
        $categories = scandir(GITHUB_WORKFLOWS);
        foreach ($categories as $category) {
            if ($category === '.' || $category === '..') continue;
            $categoryPath = GITHUB_WORKFLOWS . '/' . $category;
            if (!is_dir($categoryPath)) continue;

            $files = glob($categoryPath . '/*.json');
            foreach ($files as $file) {
                $data = json_decode(file_get_contents($file), true);
                if (!$data) continue;

                $workflows[] = [
                    'path' => $file,
                    'data' => $data,
                    'name' => $data['name'] ?? '',
                    'nodes' => $data['nodes'] ?? []
                ];
            }
        }
    }

    return $workflows;
}

/**
 * Trouver les workflows correspondant aux critères
 */
function findMatchingWorkflows($allWorkflows, $criteria) {
    $matches = [];

    foreach ($allWorkflows as $workflow) {
        $score = 0;

        $name = strtolower($workflow['name']);
        $nodes = $workflow['nodes'];

        // Score par mots-clés dans le nom
        foreach ($criteria['keywords'] as $keyword) {
            if (stripos($name, $keyword) !== false) {
                $score += 10;
            }
        }

        // Score par services utilisés
        $usedServices = [];
        foreach ($nodes as $node) {
            $type = $node['type'] ?? '';
            if (strpos($type, 'n8n-nodes-base.') === 0) {
                $service = str_replace('n8n-nodes-base.', '', $type);
                $usedServices[] = $service;
            }
        }

        foreach ($criteria['services'] as $requiredService) {
            if (in_array($requiredService, $usedServices)) {
                $score += 20;
            }
        }

        // Bonus pour complexité
        if (count($nodes) >= 5) $score += 5;
        if (count($nodes) >= 10) $score += 10;

        if ($score >= 15) {
            $matches[] = array_merge($workflow, ['score' => $score]);
        }
    }

    // Trier par score
    usort($matches, function($a, $b) {
        return $b['score'] - $a['score'];
    });

    return $matches;
}
