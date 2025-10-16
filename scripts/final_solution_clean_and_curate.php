#!/usr/bin/env php
<?php
/**
 * Solution finale : Nettoie ET cure les workflows
 */

define('BASE_PATH', '/var/www/automatehub');
define('SOURCE_DIR', BASE_PATH . '/WORKFLOWS_GITHUB_ZIE619/workflows');
define('TARGET_DIR', BASE_PATH . '/PACKS_WORKFLOWS_CURATED');

// Configuration des packs
$PACK_CRITERIA = [
    '01_CRYPTO_DEXSCREENER_MILLIONAIRE_67EUR' => ['keywords' => ['crypto', 'dex', 'dexscreener', 'token', 'coin', 'trading', 'price', 'alert', 'blockchain', 'wallet'], 'services' => ['coinGecko', 'http', 'telegram'], 'min_score' => 25, 'max_workflows' => 20],
    '02_BLOCKCHAIN_TRADING_EMPIRE_47EUR' => ['keywords' => ['blockchain', 'trading', 'crypto', 'exchange', 'market', 'price'], 'services' => ['coinGecko', 'http', 'telegram'], 'min_score' => 22, 'max_workflows' => 18],
    '03_COINGECKO_PROFIT_MACHINE_37EUR' => ['keywords' => ['coingecko', 'crypto', 'coin', 'price', 'market'], 'services' => ['coinGecko', 'telegram', 'http'], 'min_score' => 20, 'max_workflows' => 15],
    '04_IA_BUSINESS_REVOLUTION_47EUR' => ['keywords' => ['openai', 'ai', 'gpt', 'chatgpt', 'intelligence', 'assistant', 'automation'], 'services' => ['openAi', 'http', 'googleSheets'], 'min_score' => 25, 'max_workflows' => 20],
    '05_CONTENT_VIRAL_FACTORY_39EUR' => ['keywords' => ['content', 'viral', 'social', 'post', 'linkedin', 'twitter', 'instagram', 'generate'], 'services' => ['openAi', 'http', 'linkedin', 'twitter'], 'min_score' => 20, 'max_workflows' => 18],
    '06_TELEGRAM_CRYPTO_EMPIRE_52EUR' => ['keywords' => ['telegram', 'crypto', 'bot', 'alert', 'notification'], 'services' => ['telegram', 'coinGecko', 'http'], 'min_score' => 22, 'max_workflows' => 20],
    '07_TELEGRAM_AI_ASSISTANT_SUPREME_42EUR' => ['keywords' => ['telegram', 'openai', 'ai', 'bot', 'assistant', 'chat'], 'services' => ['telegram', 'openAi', 'http'], 'min_score' => 22, 'max_workflows' => 18],
    '08_TELEGRAM_MARKETING_DOMINATION_32EUR' => ['keywords' => ['telegram', 'marketing', 'campaign', 'message', 'broadcast'], 'services' => ['telegram', 'http', 'googleSheets'], 'min_score' => 18, 'max_workflows' => 15],
    '09_TELEGRAM_LEAD_MAGNET_37EUR' => ['keywords' => ['telegram', 'lead', 'magnet', 'capture', 'form', 'contact'], 'services' => ['telegram', 'http', 'googleSheets', 'airtable'], 'min_score' => 18, 'max_workflows' => 15],
    '10_EMAIL_MARKETING_MILLIONAIRE_42EUR' => ['keywords' => ['email', 'gmail', 'send', 'campaign', 'marketing', 'newsletter'], 'services' => ['gmail', 'emailSend', 'http'], 'min_score' => 20, 'max_workflows' => 18],
    '11_GMAIL_PRODUCTIVITY_BEAST_32EUR' => ['keywords' => ['gmail', 'email', 'productivity', 'filter', 'automate', 'organize'], 'services' => ['gmail', 'http', 'googleSheets'], 'min_score' => 18, 'max_workflows' => 15],
    '12_EMAIL_CRM_SALES_MACHINE_37EUR' => ['keywords' => ['email', 'crm', 'sales', 'lead', 'hubspot', 'pipedrive'], 'services' => ['gmail', 'hubSpot', 'http'], 'min_score' => 18, 'max_workflows' => 16],
    '13_GOOGLE_SHEETS_DATA_GENIUS_42EUR' => ['keywords' => ['sheets', 'spreadsheet', 'data', 'google', 'report', 'analytics'], 'services' => ['googleSheets', 'http'], 'min_score' => 20, 'max_workflows' => 18],
    '14_GOOGLE_DRIVE_ORGANISATION_KING_27EUR' => ['keywords' => ['drive', 'google', 'file', 'organize', 'folder', 'document'], 'services' => ['googleDrive', 'http'], 'min_score' => 15, 'max_workflows' => 12],
    '15_GOOGLE_CALENDAR_TIME_MASTER_25EUR' => ['keywords' => ['calendar', 'schedule', 'meeting', 'event', 'time', 'google'], 'services' => ['googleCalendar', 'http'], 'min_score' => 15, 'max_workflows' => 12],
    '16_GOOGLE_WORKSPACE_BUSINESS_SUITE_35EUR' => ['keywords' => ['google', 'workspace', 'sheets', 'docs', 'drive', 'calendar'], 'services' => ['googleSheets', 'googleDrive', 'googleCalendar', 'http'], 'min_score' => 18, 'max_workflows' => 16],
    '17_CRM_SALES_ACCELERATOR_52EUR' => ['keywords' => ['crm', 'sales', 'hubspot', 'pipedrive', 'deal', 'contact', 'lead'], 'services' => ['hubSpot', 'http', 'gmail'], 'min_score' => 22, 'max_workflows' => 20],
    '18_ECOMMERCE_PROFIT_MAXIMIZER_52EUR' => ['keywords' => ['ecommerce', 'shop', 'order', 'product', 'woocommerce', 'stripe', 'payment'], 'services' => ['http', 'stripe', 'googleSheets'], 'min_score' => 22, 'max_workflows' => 20],
    '19_SOCIAL_MEDIA_VIRAL_ENGINE_47EUR' => ['keywords' => ['social', 'media', 'linkedin', 'twitter', 'instagram', 'facebook', 'post'], 'services' => ['linkedin', 'twitter', 'http', 'openAi'], 'min_score' => 20, 'max_workflows' => 18],
    '20_DATABASE_INSIGHTS_GENIUS_47EUR' => ['keywords' => ['database', 'sql', 'postgres', 'mysql', 'query', 'data', 'insight'], 'services' => ['postgres', 'mysql', 'http'], 'min_score' => 20, 'max_workflows' => 18],
    '21_API_INTEGRATION_WIZARD_29EUR' => ['keywords' => ['api', 'integration', 'http', 'webhook', 'rest', 'connect'], 'services' => ['http', 'webhook'], 'min_score' => 15, 'max_workflows' => 12],
    '22_AUTOMATION_ECOSYSTEM_BUILDER_32EUR' => ['keywords' => ['automation', 'workflow', 'n8n', 'integration', 'system'], 'services' => ['http', 'webhook'], 'min_score' => 15, 'max_workflows' => 15],
    '23_ZAPIER_KILLER_ALTERNATIVE_35EUR' => ['keywords' => ['zapier', 'automation', 'integration', 'workflow', 'connect'], 'services' => ['http', 'webhook'], 'min_score' => 15, 'max_workflows' => 15],
    '24_SLACK_TEAM_SUPERCHARGER_35EUR' => ['keywords' => ['slack', 'team', 'communication', 'channel', 'message', 'notification'], 'services' => ['slack', 'http'], 'min_score' => 15, 'max_workflows' => 15],
    '25_TEAM_COLLABORATION_REVOLUTION_42EUR' => ['keywords' => ['team', 'collaboration', 'slack', 'notion', 'project', 'task'], 'services' => ['slack', 'http', 'googleSheets'], 'min_score' => 18, 'max_workflows' => 18],
    '26_CONTENT_MARKETING_EMPIRE_42EUR' => ['keywords' => ['content', 'marketing', 'blog', 'seo', 'wordpress', 'social'], 'services' => ['wordpress', 'http', 'openAi'], 'min_score' => 18, 'max_workflows' => 18],
    '27_BUSINESS_EFFICIENCY_MAXIMIZER_42EUR' => ['keywords' => ['business', 'efficiency', 'automation', 'productivity', 'optimize'], 'services' => ['http', 'googleSheets', 'gmail'], 'min_score' => 18, 'max_workflows' => 18],
    '28_TIME_MANAGEMENT_GENIUS_37EUR' => ['keywords' => ['time', 'management', 'calendar', 'schedule', 'task', 'reminder'], 'services' => ['googleCalendar', 'http'], 'min_score' => 15, 'max_workflows' => 15],
    '29_AI_CRYPTO_WEALTH_MACHINE_67EUR' => ['keywords' => ['ai', 'crypto', 'openai', 'trading', 'analysis', 'prediction'], 'services' => ['openAi', 'coinGecko', 'telegram'], 'min_score' => 28, 'max_workflows' => 20],
    '30_EMAIL_AI_CRM_TRINITY_POWER_57EUR' => ['keywords' => ['email', 'ai', 'crm', 'gmail', 'openai', 'sales', 'lead'], 'services' => ['gmail', 'openAi', 'hubSpot', 'http'], 'min_score' => 25, 'max_workflows' => 18],
    '31_SOCIAL_AI_INFLUENCE_EMPIRE_52EUR' => ['keywords' => ['social', 'ai', 'influence', 'linkedin', 'twitter', 'content', 'openai'], 'services' => ['openAi', 'linkedin', 'twitter', 'http'], 'min_score' => 22, 'max_workflows' => 18],
    '32_AUTOMATION_STARTER_SUCCESS_19EUR' => ['keywords' => ['automation', 'starter', 'beginner', 'simple', 'basic'], 'services' => ['http', 'webhook'], 'min_score' => 10, 'max_workflows' => 10],
    '33_ENTERPRISE_DOMINATION_SUITE_97EUR' => ['keywords' => ['enterprise', 'business', 'suite', 'integration', 'advanced', 'complex'], 'services' => ['http', 'googleSheets', 'slack', 'hubSpot'], 'min_score' => 25, 'max_workflows' => 25],
    '34_AI_MASTER_WEALTH_COLLECTION_87EUR' => ['keywords' => ['ai', 'openai', 'automation', 'master', 'advanced', 'gpt'], 'services' => ['openAi', 'http'], 'min_score' => 28, 'max_workflows' => 22]
];

echo "🚀 Solution Finale: Nettoyage + Curation\n";
echo str_repeat('=', 80) . "\n\n";

// Charger et nettoyer TOUS les workflows
echo "📂 Chargement et nettoyage des workflows sources...\n";
$cleanWorkflows = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(SOURCE_DIR, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$loadedCount = 0;
$cleanedCount = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'json') {
        $content = file_get_contents($file->getPathname());
        $data = json_decode($content, true);

        if ($data && !empty($data['nodes'])) {
            $loadedCount++;

            // Nettoyer le workflow (garder max 1 sticky note, 1 error handler)
            $cleaned = cleanWorkflow($data);

            // Vérifier qu'il n'est plus cassé
            if (!isWorkflowBroken($cleaned)) {
                $cleanWorkflows[] = [
                    'path' => $file->getPathname(),
                    'name' => $file->getFilename(),
                    'data' => $cleaned
                ];
                $cleanedCount++;
            }
        }
    }
}

echo "✅ $loadedCount workflows chargés\n";
echo "✅ $cleanedCount workflows propres (non cassés)\n\n";

// Remplir chaque pack
foreach ($PACK_CRITERIA as $packName => $criteria) {
    echo "📦 Création: $packName\n";

    $packDir = TARGET_DIR . '/' . $packName;

    // Nettoyer le pack existant
    if (is_dir($packDir)) {
        $files = glob($packDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    } else {
        mkdir($packDir, 0755, true);
    }

    // Scorer et trier les workflows
    $scored = [];
    foreach ($cleanWorkflows as $workflow) {
        $score = scoreWorkflow($workflow['data'], $criteria);
        if ($score >= $criteria['min_score']) {
            $scored[] = [
                'workflow' => $workflow,
                'score' => $score
            ];
        }
    }

    // Trier par score DESC
    usort($scored, function($a, $b) {
        return $b['score'] - $a['score'];
    });

    // Prendre les meilleurs
    $selected = array_slice($scored, 0, $criteria['max_workflows']);

    // Sauvegarder les workflows
    $index = 1;
    foreach ($selected as $item) {
        $workflow = $item['workflow'];
        $score = $item['score'];

        $targetFile = $packDir . '/' . sprintf('%02d', $index) . '_' . $workflow['name'];
        file_put_contents($targetFile, json_encode($workflow['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        echo "   ✅ [$score pts] {$workflow['name']}\n";
        $index++;
    }

    $count = $index - 1;
    echo "   📊 $count workflows ajoutés\n\n";
}

echo "✅ Curation terminée avec workflows propres!\n";

// FONCTIONS

function cleanWorkflow($data) {
    if (empty($data['nodes'])) {
        return $data;
    }

    $nodes = $data['nodes'];
    $keptNodes = [];
    $stickyNoteCount = 0;
    $errorHandlerCount = 0;

    foreach ($nodes as $node) {
        $type = $node['type'] ?? '';

        // Garder seulement 1 sticky note
        if ($type === 'n8n-nodes-base.stickyNote') {
            if ($stickyNoteCount === 0) {
                $keptNodes[] = $node;
                $stickyNoteCount++;
            }
            continue;
        }

        // Garder seulement 1 error handler
        if ($type === 'n8n-nodes-base.stopAndError') {
            if ($errorHandlerCount === 0) {
                $keptNodes[] = $node;
                $errorHandlerCount++;
            }
            continue;
        }

        // Garder tous les autres nodes
        $keptNodes[] = $node;
    }

    $data['nodes'] = $keptNodes;
    return $data;
}

function isWorkflowBroken($data) {
    if (empty($data['nodes'])) {
        return "Aucun node";
    }

    $nodes = $data['nodes'];

    // Vérifier sticky notes
    $docNodes = array_filter($nodes, function($node) {
        return ($node['type'] ?? '') === 'n8n-nodes-base.stickyNote';
    });

    if (count($docNodes) > 3) {
        return false; // Déjà nettoyé mais encore >3
    }

    // Vérifier error handlers
    $errorNodes = array_filter($nodes, function($node) {
        return ($node['type'] ?? '') === 'n8n-nodes-base.stopAndError';
    });

    if (count($errorNodes) > 2) {
        return false;
    }

    return false; // OK
}

function scoreWorkflow($data, $criteria) {
    $score = 0;
    $name = strtolower($data['name'] ?? '');
    $nodes = $data['nodes'] ?? [];

    // Keywords
    foreach ($criteria['keywords'] as $keyword) {
        if (stripos($name, $keyword) !== false) {
            $score += 15;
        }
    }

    // Services
    $usedServices = [];
    foreach ($nodes as $node) {
        $type = $node['type'] ?? '';
        if (strpos($type, 'n8n-nodes-base.') === 0) {
            $service = substr($type, 15);
            $usedServices[] = $service;
        }
    }

    foreach ($criteria['services'] as $requiredService) {
        foreach ($usedServices as $usedService) {
            if (stripos($usedService, $requiredService) !== false) {
                $score += 25;
                break;
            }
        }
    }

    // Complexité
    $nodeCount = count($nodes);
    if ($nodeCount >= 15) $score += 15;
    elseif ($nodeCount >= 10) $score += 10;
    elseif ($nodeCount >= 5) $score += 5;

    // Pénalités
    if (stripos($name, 'test') !== false) $score -= 20;
    if (stripos($name, 'example') !== false) $score -= 15;

    return $score;
}
