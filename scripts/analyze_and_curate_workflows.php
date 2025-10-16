#!/usr/bin/env php
<?php
/**
 * Script d'analyse et de curation intelligente des workflows n8n
 *
 * Ce script :
 * 1. Analyse TOUS les workflows disponibles (GitHub + projet existant)
 * 2. Évalue leur qualité et pertinence
 * 3. Les catégorise par thème RÉEL
 * 4. Crée des packs PREMIUM de haute qualité
 */

define('BASE_PATH', '/var/www/automatehub');
define('GITHUB_WORKFLOWS', BASE_PATH . '/WORKFLOWS_GITHUB_ZIE619/workflows');
define('OLD_PACKS', BASE_PATH . '/PACKS_WORKFLOWS_VENDEURS');
define('OUTPUT_DIR', BASE_PATH . '/PACKS_WORKFLOWS_PREMIUM');

echo "🔍 Analyse et Curation Intelligente des Workflows n8n\n";
echo str_repeat('=', 80) . "\n\n";

// Créer le dossier de sortie
if (!file_exists(OUTPUT_DIR)) {
    mkdir(OUTPUT_DIR, 0755, true);
}

// Étape 1: Scanner et analyser tous les workflows
$allWorkflows = [];

echo "📦 Étape 1: Scan des workflows GitHub...\n";
$githubWorkflows = scanGitHubWorkflows();
echo "   ✅ " . count($githubWorkflows) . " workflows GitHub trouvés\n\n";

echo "📦 Étape 2: Scan des anciens packs...\n";
$oldPackWorkflows = scanOldPacks();
echo "   ✅ " . count($oldPackWorkflows) . " workflows des anciens packs trouvés\n\n";

// Fusionner tous les workflows
$allWorkflows = array_merge($githubWorkflows, $oldPackWorkflows);
echo "📊 Total: " . count($allWorkflows) . " workflows à analyser\n\n";

// Étape 2: Analyser et scorer chaque workflow
echo "🤖 Étape 3: Analyse de la qualité de chaque workflow...\n";
$analyzedWorkflows = analyzeWorkflows($allWorkflows);
echo "   ✅ Analyse terminée\n\n";

// Étape 3: Catégoriser par thème RÉEL
echo "🏷️  Étape 4: Catégorisation par thème...\n";
$categorized = categorizeWorkflows($analyzedWorkflows);
echo "   ✅ " . count($categorized) . " catégories identifiées\n\n";

// Étape 4: Créer les packs PREMIUM
echo "💎 Étape 5: Création des packs PREMIUM...\n";
createPremiumPacks($categorized);

echo "\n✅ Curation terminée!\n";
echo "📁 Packs créés dans: " . OUTPUT_DIR . "\n";

/**
 * Scanner les workflows GitHub
 */
function scanGitHubWorkflows() {
    $workflows = [];

    if (!is_dir(GITHUB_WORKFLOWS)) {
        return $workflows;
    }

    $categories = scandir(GITHUB_WORKFLOWS);

    foreach ($categories as $category) {
        if ($category === '.' || $category === '..') continue;

        $categoryPath = GITHUB_WORKFLOWS . '/' . $category;
        if (!is_dir($categoryPath)) continue;

        $files = glob($categoryPath . '/*.json');

        foreach ($files as $file) {
            $workflows[] = [
                'path' => $file,
                'source' => 'github',
                'category' => $category,
                'filename' => basename($file)
            ];
        }
    }

    return $workflows;
}

/**
 * Scanner les anciens packs
 */
function scanOldPacks() {
    $workflows = [];

    if (!is_dir(OLD_PACKS)) {
        return $workflows;
    }

    $packs = scandir(OLD_PACKS);

    foreach ($packs as $pack) {
        if ($pack === '.' || $pack === '..' || !is_dir(OLD_PACKS . '/' . $pack)) continue;

        $files = glob(OLD_PACKS . '/' . $pack . '/*.json');

        foreach ($files as $file) {
            $workflows[] = [
                'path' => $file,
                'source' => 'old_pack',
                'category' => $pack,
                'filename' => basename($file)
            ];
        }
    }

    return $workflows;
}

/**
 * Analyser et scorer les workflows
 */
function analyzeWorkflows($workflows) {
    $analyzed = [];
    $total = count($workflows);
    $current = 0;

    foreach ($workflows as $workflow) {
        $current++;

        if ($current % 100 == 0) {
            echo "   📝 Analysé $current/$total workflows...\n";
        }

        $content = @file_get_contents($workflow['path']);
        if (!$content) continue;

        $data = json_decode($content, true);
        if (!$data) continue;

        // Analyser le workflow
        $analysis = performAnalysis($data, $workflow);

        $analyzed[] = array_merge($workflow, [
            'data' => $data,
            'analysis' => $analysis
        ]);
    }

    return $analyzed;
}

/**
 * Analyser un workflow et lui donner un score de qualité
 */
function performAnalysis($data, $workflow) {
    $score = 0;
    $tags = [];
    $category = 'general';

    // Extraire les informations
    $name = $data['name'] ?? 'Unnamed';
    $nodes = $data['nodes'] ?? [];
    $nodeCount = count($nodes);

    // SCORING

    // 1. Complexité (plus de nodes = plus intéressant, généralement)
    if ($nodeCount >= 5) $score += 30;
    elseif ($nodeCount >= 3) $score += 20;
    elseif ($nodeCount >= 2) $score += 10;
    else $score -= 20; // Workflow trop simple

    // 2. Détection des services utilisés
    $services = [];
    foreach ($nodes as $node) {
        $type = $node['type'] ?? '';

        if (strpos($type, 'n8n-nodes-base.') === 0) {
            $service = str_replace('n8n-nodes-base.', '', $type);
            $services[] = $service;

            // Bonus pour les services populaires/utiles
            if (in_array($service, ['openAi', 'telegram', 'gmail', 'googleSheets', 'slack', 'notion', 'airtable'])) {
                $score += 15;
            }
        }
    }

    $services = array_unique($services);

    // 3. Détection du thème RÉEL basé sur les nodes
    $category = detectRealCategory($services, $name, $nodes);

    // 4. Pénaliser les workflows génériques/vides
    if (stripos($name, 'test') !== false) $score -= 30;
    if (stripos($name, 'example') !== false) $score -= 20;
    if (stripos($name, 'sample') !== false) $score -= 20;
    if (stripos($name, 'manual') !== false && $nodeCount < 3) $score -= 15;

    // 5. Bonus pour les workflows avec webhooks/automation
    foreach ($nodes as $node) {
        $type = $node['type'] ?? '';
        if (strpos($type, 'webhook') !== false) $score += 10;
        if (strpos($type, 'cron') !== false) $score += 10;
        if (strpos($type, 'schedule') !== false) $score += 10;
    }

    // 6. Bonus pour les workflows avec de la logique
    foreach ($nodes as $node) {
        $type = $node['type'] ?? '';
        if (strpos($type, 'function') !== false) $score += 5;
        if (strpos($type, 'code') !== false) $score += 5;
        if (strpos($type, 'if') !== false) $score += 5;
        if (strpos($type, 'switch') !== false) $score += 5;
    }

    return [
        'score' => $score,
        'name' => $name,
        'nodeCount' => $nodeCount,
        'services' => $services,
        'category' => $category,
        'quality' => $score >= 40 ? 'excellent' : ($score >= 20 ? 'good' : ($score >= 0 ? 'average' : 'poor'))
    ];
}

/**
 * Détecter la VRAIE catégorie d'un workflow
 */
function detectRealCategory($services, $name, $nodes) {
    // Catégories basées sur les services réellement utilisés

    // CRYPTO & BLOCKCHAIN
    if (preg_match('/(crypto|blockchain|bitcoin|eth|dex|coingecko|trading)/i', $name)) {
        return 'crypto_blockchain';
    }

    // IA & AI
    if (in_array('openAi', $services) || preg_match('/(ai|gpt|openai|chatgpt|assistant)/i', $name)) {
        return 'ai_automation';
    }

    // TELEGRAM
    if (in_array('telegram', $services)) {
        return 'telegram';
    }

    // EMAIL & GMAIL
    if (in_array('gmail', $services) || in_array('email', $services) || in_array('imap', $services)) {
        return 'email_productivity';
    }

    // GOOGLE WORKSPACE
    if (count(array_intersect($services, ['googleSheets', 'googleDocs', 'googleDrive', 'googleCalendar'])) >= 2) {
        return 'google_workspace';
    }

    // CRM & SALES
    if (count(array_intersect($services, ['hubspot', 'salesforce', 'pipedrive', 'copper'])) > 0) {
        return 'crm_sales';
    }

    // ECOMMERCE
    if (in_array('shopify', $services) || in_array('woocommerce', $services) || in_array('stripe', $services)) {
        return 'ecommerce';
    }

    // SOCIAL MEDIA
    if (count(array_intersect($services, ['twitter', 'facebook', 'instagram', 'linkedin'])) > 0) {
        return 'social_media';
    }

    // PROJECT MANAGEMENT
    if (count(array_intersect($services, ['asana', 'trello', 'notion', 'clickup', 'mondaycom'])) > 0) {
        return 'project_management';
    }

    // DATA & ANALYTICS
    if (count(array_intersect($services, ['airtable', 'googleSheets', 'mysql', 'postgres'])) >= 2) {
        return 'data_analytics';
    }

    return 'automation_general';
}

/**
 * Catégoriser les workflows
 */
function categorizeWorkflows($workflows) {
    $categories = [];

    foreach ($workflows as $workflow) {
        $category = $workflow['analysis']['category'];

        if (!isset($categories[$category])) {
            $categories[$category] = [];
        }

        $categories[$category][] = $workflow;
    }

    // Trier chaque catégorie par score
    foreach ($categories as $cat => &$workflows) {
        usort($workflows, function($a, $b) {
            return $b['analysis']['score'] - $a['analysis']['score'];
        });
    }

    return $categories;
}

/**
 * Créer les packs PREMIUM
 */
function createPremiumPacks($categorized) {
    $packDefinitions = [
        'crypto_blockchain' => [
            'name' => '01_CRYPTO_BLOCKCHAIN_MASTER_97EUR',
            'max_workflows' => 25,
            'min_score' => 20
        ],
        'ai_automation' => [
            'name' => '02_AI_AUTOMATION_REVOLUTION_87EUR',
            'max_workflows' => 25,
            'min_score' => 20
        ],
        'telegram' => [
            'name' => '03_TELEGRAM_AUTOMATION_PRO_67EUR',
            'max_workflows' => 20,
            'min_score' => 20
        ],
        'email_productivity' => [
            'name' => '04_EMAIL_PRODUCTIVITY_SUITE_57EUR',
            'max_workflows' => 20,
            'min_score' => 15
        ],
        'google_workspace' => [
            'name' => '05_GOOGLE_WORKSPACE_POWERPACK_67EUR',
            'max_workflows' => 25,
            'min_score' => 15
        ],
        'crm_sales' => [
            'name' => '06_CRM_SALES_ACCELERATOR_77EUR',
            'max_workflows' => 20,
            'min_score' => 20
        ],
        'ecommerce' => [
            'name' => '07_ECOMMERCE_AUTOMATION_67EUR',
            'max_workflows' => 20,
            'min_score' => 20
        ],
        'social_media' => [
            'name' => '08_SOCIAL_MEDIA_DOMINATION_57EUR',
            'max_workflows' => 20,
            'min_score' => 15
        ],
        'project_management' => [
            'name' => '09_PROJECT_MANAGEMENT_PRO_57EUR',
            'max_workflows' => 20,
            'min_score' => 15
        ],
        'data_analytics' => [
            'name' => '10_DATA_ANALYTICS_GENIUS_67EUR',
            'max_workflows' => 20,
            'min_score' => 15
        ]
    ];

    $stats = [];

    foreach ($packDefinitions as $category => $packDef) {
        if (!isset($categorized[$category])) {
            echo "   ⚠️  Catégorie '$category' non trouvée, ignorée\n";
            continue;
        }

        $packDir = OUTPUT_DIR . '/' . $packDef['name'];

        if (!file_exists($packDir)) {
            mkdir($packDir, 0755, true);
        }

        $workflows = $categorized[$category];
        $selected = 0;
        $skipped = 0;

        foreach ($workflows as $index => $workflow) {
            if ($selected >= $packDef['max_workflows']) break;

            $score = $workflow['analysis']['score'];

            if ($score < $packDef['min_score']) {
                $skipped++;
                continue;
            }

            // Copier le workflow
            $num = str_pad($selected + 1, 2, '0', STR_PAD_LEFT);
            $filename = $num . '_' . $workflow['filename'];
            $targetPath = $packDir . '/' . $filename;

            copy($workflow['path'], $targetPath);
            $selected++;
        }

        $stats[$packDef['name']] = [
            'selected' => $selected,
            'skipped' => $skipped,
            'total_available' => count($workflows)
        ];

        echo "   ✅ {$packDef['name']}: $selected workflows (score min: {$packDef['min_score']})\n";
    }

    // Sauvegarder les stats
    file_put_contents(
        OUTPUT_DIR . '/CURATION_STATS.json',
        json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}
