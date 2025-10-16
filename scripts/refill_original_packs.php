#!/usr/bin/env php
<?php
/**
 * Remplir les 34 packs originaux avec des workflows VRAIMENT pertinents
 *
 * On garde les NOMS (géniaux !) mais on change le CONTENU
 */

define('BASE_PATH', '/var/www/automatehub');
define('GITHUB_WORKFLOWS', BASE_PATH . '/WORKFLOWS_GITHUB_ZIE619/workflows');
define('OLD_PACKS', BASE_PATH . '/PACKS_WORKFLOWS_VENDEURS');
define('OUTPUT_DIR', BASE_PATH . '/PACKS_WORKFLOWS_CURATED');

echo "🎯 Remplissage Intelligent des Packs Originaux\n";
echo str_repeat('=', 80) . "\n\n";

if (!file_exists(OUTPUT_DIR)) {
    mkdir(OUTPUT_DIR, 0755, true);
}

// Définir les critères de sélection pour chaque pack
$packCriteria = [
    '01_CRYPTO_DEXSCREENER_MILLIONAIRE_67EUR' => [
        'keywords' => ['crypto', 'dex', 'dexscreener', 'token', 'coin', 'trading', 'price', 'alert'],
        'services' => ['coinGecko', 'http', 'telegram'],
        'min_score' => 25,
        'max_workflows' => 20
    ],
    '02_BLOCKCHAIN_TRADING_EMPIRE_47EUR' => [
        'keywords' => ['blockchain', 'trading', 'crypto', 'exchange', 'portfolio', 'wallet'],
        'services' => ['coinGecko', 'telegram', 'http'],
        'min_score' => 20,
        'max_workflows' => 18
    ],
    '03_COINGECKO_PROFIT_MACHINE_37EUR' => [
        'keywords' => ['coingecko', 'coin', 'crypto', 'price', 'market', 'profit'],
        'services' => ['coinGecko', 'googleSheets', 'telegram'],
        'min_score' => 20,
        'max_workflows' => 15
    ],
    '04_IA_BUSINESS_REVOLUTION_47EUR' => [
        'keywords' => ['ai', 'gpt', 'openai', 'business', 'automation', 'assistant', 'generate'],
        'services' => ['openAi', 'googleSheets', 'slack'],
        'min_score' => 25,
        'max_workflows' => 20
    ],
    '05_CONTENT_VIRAL_FACTORY_39EUR' => [
        'keywords' => ['content', 'viral', 'post', 'social', 'generate', 'creative', 'idea'],
        'services' => ['openAi', 'twitter', 'linkedin', 'facebook'],
        'min_score' => 20,
        'max_workflows' => 18
    ],
    '06_TELEGRAM_CRYPTO_EMPIRE_52EUR' => [
        'keywords' => ['telegram', 'crypto', 'bot', 'alert', 'notification', 'channel'],
        'services' => ['telegram', 'coinGecko'],
        'min_score' => 25,
        'max_workflows' => 20
    ],
    '07_TELEGRAM_AI_ASSISTANT_SUPREME_42EUR' => [
        'keywords' => ['telegram', 'ai', 'gpt', 'assistant', 'bot', 'chat'],
        'services' => ['telegram', 'openAi'],
        'min_score' => 25,
        'max_workflows' => 18
    ],
    '08_TELEGRAM_MARKETING_DOMINATION_32EUR' => [
        'keywords' => ['telegram', 'marketing', 'campaign', 'broadcast', 'message'],
        'services' => ['telegram', 'googleSheets'],
        'min_score' => 20,
        'max_workflows' => 15
    ],
    '09_TELEGRAM_LEAD_MAGNET_37EUR' => [
        'keywords' => ['telegram', 'lead', 'capture', 'funnel', 'subscribe'],
        'services' => ['telegram', 'airtable', 'googleSheets'],
        'min_score' => 20,
        'max_workflows' => 15
    ],
    '10_EMAIL_MARKETING_MILLIONAIRE_42EUR' => [
        'keywords' => ['email', 'newsletter', 'campaign', 'mailchimp', 'sendgrid'],
        'services' => ['gmail', 'mailchimp', 'googleSheets'],
        'min_score' => 20,
        'max_workflows' => 18
    ],
    '11_GMAIL_PRODUCTIVITY_BEAST_32EUR' => [
        'keywords' => ['gmail', 'email', 'productivity', 'organize', 'filter', 'automation'],
        'services' => ['gmail', 'googleSheets'],
        'min_score' => 20,
        'max_workflows' => 15
    ],
    '12_EMAIL_CRM_SALES_MACHINE_37EUR' => [
        'keywords' => ['email', 'crm', 'sales', 'lead', 'follow', 'hubspot', 'pipedrive'],
        'services' => ['gmail', 'hubspot', 'pipedrive', 'salesforce'],
        'min_score' => 22,
        'max_workflows' => 16
    ],
    '13_GOOGLE_SHEETS_DATA_GENIUS_42EUR' => [
        'keywords' => ['sheets', 'data', 'analytics', 'dashboard', 'report'],
        'services' => ['googleSheets', 'http'],
        'min_score' => 20,
        'max_workflows' => 18
    ],
    '14_GOOGLE_DRIVE_ORGANISATION_KING_27EUR' => [
        'keywords' => ['drive', 'file', 'organize', 'folder', 'document'],
        'services' => ['googleDrive', 'googleDocs'],
        'min_score' => 18,
        'max_workflows' => 12
    ],
    '15_GOOGLE_CALENDAR_TIME_MASTER_25EUR' => [
        'keywords' => ['calendar', 'schedule', 'meeting', 'event', 'time'],
        'services' => ['googleCalendar', 'zoom'],
        'min_score' => 18,
        'max_workflows' => 12
    ],
    '16_GOOGLE_WORKSPACE_BUSINESS_SUITE_35EUR' => [
        'keywords' => ['workspace', 'google', 'business', 'suite', 'productivity'],
        'services' => ['googleSheets', 'googleDocs', 'googleDrive', 'googleCalendar', 'gmail'],
        'min_score' => 20,
        'max_workflows' => 16
    ],
    '17_CRM_SALES_ACCELERATOR_52EUR' => [
        'keywords' => ['crm', 'sales', 'lead', 'deal', 'pipeline', 'hubspot', 'salesforce'],
        'services' => ['hubspot', 'salesforce', 'pipedrive'],
        'min_score' => 25,
        'max_workflows' => 20
    ],
    '18_ECOMMERCE_PROFIT_MAXIMIZER_52EUR' => [
        'keywords' => ['ecommerce', 'shopify', 'woocommerce', 'order', 'product', 'store'],
        'services' => ['shopify', 'woocommerce', 'stripe'],
        'min_score' => 25,
        'max_workflows' => 20
    ],
    '19_SOCIAL_MEDIA_VIRAL_ENGINE_47EUR' => [
        'keywords' => ['social', 'viral', 'post', 'twitter', 'linkedin', 'instagram', 'facebook'],
        'services' => ['twitter', 'linkedin', 'instagram', 'facebook'],
        'min_score' => 22,
        'max_workflows' => 18
    ],
    '20_DATABASE_INSIGHTS_GENIUS_47EUR' => [
        'keywords' => ['database', 'sql', 'mysql', 'postgres', 'data', 'query'],
        'services' => ['mysql', 'postgres', 'googleSheets'],
        'min_score' => 22,
        'max_workflows' => 18
    ],
    '21_API_INTEGRATION_WIZARD_29EUR' => [
        'keywords' => ['api', 'integration', 'http', 'webhook', 'rest'],
        'services' => ['http', 'webhook'],
        'min_score' => 18,
        'max_workflows' => 12
    ],
    '22_AUTOMATION_ECOSYSTEM_BUILDER_32EUR' => [
        'keywords' => ['automation', 'workflow', 'integrate', 'connect', 'sync'],
        'services' => [],
        'min_score' => 20,
        'max_workflows' => 15
    ],
    '23_ZAPIER_KILLER_ALTERNATIVE_35EUR' => [
        'keywords' => ['automation', 'integrate', 'connect', 'workflow', 'trigger'],
        'services' => [],
        'min_score' => 20,
        'max_workflows' => 15
    ],
    '24_SLACK_TEAM_SUPERCHARGER_35EUR' => [
        'keywords' => ['slack', 'team', 'notification', 'alert', 'message'],
        'services' => ['slack'],
        'min_score' => 20,
        'max_workflows' => 15
    ],
    '25_TEAM_COLLABORATION_REVOLUTION_42EUR' => [
        'keywords' => ['team', 'collaboration', 'project', 'task', 'notion', 'asana'],
        'services' => ['notion', 'asana', 'trello', 'slack'],
        'min_score' => 20,
        'max_workflows' => 18
    ],
    '26_CONTENT_MARKETING_EMPIRE_42EUR' => [
        'keywords' => ['content', 'marketing', 'blog', 'seo', 'publish', 'wordpress'],
        'services' => ['wordpress', 'openAi', 'googleSheets'],
        'min_score' => 22,
        'max_workflows' => 18
    ],
    '27_BUSINESS_EFFICIENCY_MAXIMIZER_42EUR' => [
        'keywords' => ['business', 'efficiency', 'automate', 'optimize', 'productivity'],
        'services' => [],
        'min_score' => 20,
        'max_workflows' => 18
    ],
    '28_TIME_MANAGEMENT_GENIUS_37EUR' => [
        'keywords' => ['time', 'management', 'calendar', 'schedule', 'productivity'],
        'services' => ['googleCalendar', 'notion'],
        'min_score' => 18,
        'max_workflows' => 15
    ],
    '29_AI_CRYPTO_WEALTH_MACHINE_67EUR' => [
        'keywords' => ['ai', 'crypto', 'trading', 'analysis', 'gpt', 'prediction'],
        'services' => ['openAi', 'coinGecko', 'telegram'],
        'min_score' => 28,
        'max_workflows' => 20
    ],
    '30_EMAIL_AI_CRM_TRINITY_POWER_57EUR' => [
        'keywords' => ['email', 'ai', 'crm', 'automation', 'gpt', 'sales'],
        'services' => ['gmail', 'openAi', 'hubspot'],
        'min_score' => 25,
        'max_workflows' => 18
    ],
    '31_SOCIAL_AI_INFLUENCE_EMPIRE_52EUR' => [
        'keywords' => ['social', 'ai', 'influence', 'content', 'gpt', 'viral'],
        'services' => ['openAi', 'twitter', 'linkedin', 'instagram'],
        'min_score' => 25,
        'max_workflows' => 18
    ],
    '32_AUTOMATION_STARTER_SUCCESS_19EUR' => [
        'keywords' => ['starter', 'beginner', 'simple', 'basic', 'automation'],
        'services' => [],
        'min_score' => 10,
        'max_workflows' => 10
    ],
    '33_ENTERPRISE_DOMINATION_SUITE_97EUR' => [
        'keywords' => ['enterprise', 'business', 'scale', 'advanced', 'integration'],
        'services' => [],
        'min_score' => 25,
        'max_workflows' => 25
    ],
    '34_AI_MASTER_WEALTH_COLLECTION_87EUR' => [
        'keywords' => ['ai', 'master', 'gpt', 'advanced', 'automation', 'business'],
        'services' => ['openAi'],
        'min_score' => 28,
        'max_workflows' => 22
    ],
];

echo "📦 " . count($packCriteria) . " packs à remplir\n\n";

// Scanner tous les workflows
echo "🔍 Scan de tous les workflows disponibles...\n";
$allWorkflows = scanAllWorkflows();
echo "   ✅ " . count($allWorkflows) . " workflows disponibles\n\n";

// Remplir chaque pack
$totalSelected = 0;
$stats = [];

foreach ($packCriteria as $packName => $criteria) {
    echo "📦 Remplissage: $packName\n";

    $packDir = OUTPUT_DIR . '/' . $packName;
    if (!file_exists($packDir)) {
        mkdir($packDir, 0755, true);
    }

    // Trouver les workflows correspondants
    $matches = findMatchingWorkflows($allWorkflows, $criteria);

    // Sélectionner les meilleurs
    $selected = array_slice($matches, 0, $criteria['max_workflows']);

    // Copier les fichiers
    foreach ($selected as $index => $workflow) {
        $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
        $targetFile = $packDir . '/' . $num . '_' . basename($workflow['path']);
        copy($workflow['path'], $targetFile);
    }

    $totalSelected += count($selected);
    $stats[$packName] = [
        'selected' => count($selected),
        'available' => count($matches),
        'target' => $criteria['max_workflows']
    ];

    echo "   ✅ " . count($selected) . " workflows ajoutés (sur " . count($matches) . " trouvés)\n\n";
}

echo "📊 RÉSUMÉ FINAL\n";
echo str_repeat('-', 80) . "\n";
echo "Total workflows sélectionnés: $totalSelected\n";
echo "Packs avec contenu complet: " . count(array_filter($stats, fn($s) => $s['selected'] >= $s['target'] * 0.8)) . "/" . count($stats) . "\n\n";

// Sauvegarder les stats
file_put_contents(
    OUTPUT_DIR . '/CURATION_STATS.json',
    json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

echo "✅ Remplissage terminé!\n";
echo "📁 Packs disponibles dans: $OUTPUT_DIR\n";

/**
 * Scanner tous les workflows
 */
function scanAllWorkflows() {
    $workflows = [];

    // Workflows GitHub
    if (is_dir(GITHUB_WORKFLOWS)) {
        $categories = scandir(GITHUB_WORKFLOWS);
        foreach ($categories as $category) {
            if ($category === '.' || $category === '..') continue;
            $categoryPath = GITHUB_WORKFLOWS . '/' . $category;
            if (!is_dir($categoryPath)) continue;

            $files = glob($categoryPath . '/*.json');
            foreach ($files as $file) {
                $data = @json_decode(file_get_contents($file), true);
                if (!$data) continue;

                $workflows[] = [
                    'path' => $file,
                    'data' => $data,
                    'name' => strtolower($data['name'] ?? ''),
                    'nodes' => $data['nodes'] ?? []
                ];
            }
        }
    }

    return $workflows;
}

/**
 * Trouver les workflows correspondants
 */
function findMatchingWorkflows($allWorkflows, $criteria) {
    $matches = [];

    foreach ($allWorkflows as $workflow) {
        $score = 0;

        // Score par mots-clés
        foreach ($criteria['keywords'] as $keyword) {
            if (stripos($workflow['name'], $keyword) !== false) {
                $score += 15;
            }
        }

        // Score par services
        $usedServices = [];
        foreach ($workflow['nodes'] as $node) {
            $type = $node['type'] ?? '';
            if (strpos($type, 'n8n-nodes-base.') === 0) {
                $service = str_replace('n8n-nodes-base.', '', $type);
                $usedServices[] = $service;
            }
        }

        if (!empty($criteria['services'])) {
            foreach ($criteria['services'] as $requiredService) {
                if (in_array($requiredService, $usedServices)) {
                    $score += 25;
                }
            }
        }

        // Bonus complexité
        $nodeCount = count($workflow['nodes']);
        if ($nodeCount >= 5) $score += 5;
        if ($nodeCount >= 10) $score += 10;
        if ($nodeCount >= 15) $score += 15;

        // Pénalités
        if (stripos($workflow['name'], 'test') !== false) $score -= 20;
        if (stripos($workflow['name'], 'example') !== false) $score -= 15;

        if ($score >= $criteria['min_score']) {
            $matches[] = array_merge($workflow, ['score' => $score]);
        }
    }

    // Trier par score
    usort($matches, function($a, $b) {
        return $b['score'] - $a['score'];
    });

    return $matches;
}
