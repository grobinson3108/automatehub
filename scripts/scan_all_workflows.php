<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\VideoContentPlan;

function extractWorkflowInfo($filePath) {
    $content = file_get_contents($filePath);
    $workflow = json_decode($content, true);

    if (!$workflow) {
        return null;
    }

    // Extraire le nom
    $name = $workflow['name'] ?? basename($filePath, '.json');
    $name = str_replace('_', ' ', $name);
    $name = ucwords($name);

    // Extraire la description des notes ou créer une description basique
    $description = '';
    if (isset($workflow['meta']['description'])) {
        $description = $workflow['meta']['description'];
    } elseif (isset($workflow['notes'])) {
        $description = strip_tags($workflow['notes']);
        $description = substr($description, 0, 200) . '...';
    } else {
        // Analyser les nœuds pour comprendre le workflow
        $nodes = $workflow['nodes'] ?? [];
        $nodeTypes = array_map(function($node) {
            return $node['type'] ?? 'unknown';
        }, $nodes);

        $hasAI = array_intersect($nodeTypes, ['@n8n/n8n-nodes-langchain.chatOpenAi', 'n8n-nodes-base.openAi']);
        $hasTrigger = array_intersect($nodeTypes, ['n8n-nodes-base.webhook', 'n8n-nodes-base.manualTrigger', 'n8n-nodes-base.cronTrigger']);
        $hasSocial = array_intersect($nodeTypes, ['n8n-nodes-base.telegram', 'n8n-nodes-base.twitter', 'n8n-nodes-base.discord']);

        if (!empty($hasAI)) {
            $description = "Workflow utilisant l'intelligence artificielle pour automatiser des tâches complexes";
        } elseif (!empty($hasSocial)) {
            $description = "Automation pour réseaux sociaux et communication";
        } elseif (!empty($hasTrigger)) {
            $description = "Workflow d'automatisation avec déclencheurs programmés";
        } else {
            $description = "Workflow d'automatisation n8n personnalisé";
        }
    }

    // Déterminer le potentiel viral basé sur le nom/contenu
    $viralKeywords = [
        'social', 'viral', 'tiktok', 'youtube', 'instagram', 'ai', 'chatgpt', 'assistant',
        'automation', 'clone', 'generate', 'content', 'post', 'video', 'image'
    ];

    $viral_potential = 3; // Par défaut
    $nameAndDesc = strtolower($name . ' ' . $description);

    $viralScore = 0;
    foreach ($viralKeywords as $keyword) {
        if (strpos($nameAndDesc, $keyword) !== false) {
            $viralScore++;
        }
    }

    if ($viralScore >= 4) $viral_potential = 5;
    elseif ($viralScore >= 3) $viral_potential = 4;
    elseif ($viralScore >= 1) $viral_potential = 3;
    else $viral_potential = 2;

    // Déterminer les plateformes recommandées
    $platforms = ['youtube', 'linkedin']; // Par défaut

    if (strpos($nameAndDesc, 'tiktok') !== false || strpos($nameAndDesc, 'viral') !== false) {
        $platforms[] = 'tiktok';
        $platforms[] = 'instagram';
    }

    if (strpos($nameAndDesc, 'business') !== false || strpos($nameAndDesc, 'enterprise') !== false) {
        $platforms = ['linkedin', 'youtube'];
    }

    if (strpos($nameAndDesc, 'social') !== false || strpos($nameAndDesc, 'post') !== false) {
        $platforms = ['youtube', 'tiktok', 'linkedin', 'instagram', 'facebook'];
    }

    // Déterminer la priorité (plus il y a de nœuds complexes, plus c'est prioritaire)
    $nodeCount = count($nodes);
    $priority = 50; // Par défaut

    if ($nodeCount > 15) $priority = 20;
    elseif ($nodeCount > 10) $priority = 30;
    elseif ($nodeCount > 5) $priority = 40;

    // Ajuster la priorité selon le potentiel viral
    $priority = $priority - ($viral_potential * 5);

    return [
        'workflow_name' => $name,
        'workflow_file_path' => $filePath,
        'workflow_description' => $description,
        'platforms' => array_unique($platforms),
        'priority' => max(1, $priority),
        'viral_potential' => $viral_potential,
        'estimated_videos' => 1,
        'video_details' => generateBasicVideoDetails($name, $description, $platforms, $viral_potential),
    ];
}

function generateBasicVideoDetails($name, $description, $platforms, $viral_potential) {
    $details = [];

    foreach ($platforms as $platform) {
        switch ($platform) {
            case 'youtube':
                $details[$platform] = [
                    'videos' => [
                        [
                            'title' => "Comment créer " . $name . " avec n8n",
                            'description' => $description . " - Tutorial complet étape par étape",
                            'duration' => $viral_potential >= 4 ? '10-15 min' : '8-12 min',
                            'hook' => 'Tutorial ' . strtolower($name),
                            'tags' => ['n8n', 'automation', 'tutorial', 'workflow'],
                            'target_audience' => 'Entrepreneurs, freelancers',
                            'call_to_action' => 'Télécharge le workflow en description',
                            'video_type' => 'Tutorial',
                            'difficulty' => 'Intermédiaire'
                        ]
                    ]
                ];
                break;

            case 'tiktok':
                $details[$platform] = [
                    'videos' => [
                        [
                            'title' => "Cette automation va changer ta vie",
                            'description' => "Découvre " . strtolower($name),
                            'duration' => '30-60s',
                            'hook' => 'Demo choc',
                            'tags' => ['#automation', '#TechTok', '#productivity', '#n8n'],
                            'target_audience' => 'Gen Z, tech enthusiasts',
                            'call_to_action' => 'Follow pour plus d\'automations',
                            'video_type' => 'Demo rapide',
                            'music' => 'Trending tech sound'
                        ]
                    ]
                ];
                break;

            case 'linkedin':
                $details[$platform] = [
                    'videos' => [
                        [
                            'title' => $name . " : ROI et impact business",
                            'description' => "Case study : " . $description,
                            'duration' => '5-8 min',
                            'hook' => 'Business case study',
                            'tags' => ['automation', 'business', 'ROI', 'productivity'],
                            'target_audience' => 'Business professionals, managers',
                            'call_to_action' => 'Contactez-moi pour une implémentation',
                            'video_type' => 'Business case',
                            'difficulty' => 'Débutant'
                        ]
                    ]
                ];
                break;

            default:
                $details[$platform] = [
                    'videos' => [
                        [
                            'title' => $name . " en action",
                            'description' => $description,
                            'duration' => '3-5 min',
                            'hook' => 'Demo ' . strtolower($name),
                            'tags' => ['automation', 'demo'],
                            'target_audience' => 'Tech enthusiasts',
                            'call_to_action' => 'Voir plus d\'automations',
                            'video_type' => 'Demo'
                        ]
                    ]
                ];
        }
    }

    return $details;
}

echo "🔍 Scan des workflows en cours...\n";

// Scanner tous les fichiers JSON récursivement
function getAllJsonFiles($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'json') {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

$workflowFiles = getAllJsonFiles('/var/www/automatehub/workflows');

echo "📁 Trouvé " . count($workflowFiles) . " fichiers workflows\n";

$processedCount = 0;
$existingWorkflows = VideoContentPlan::pluck('workflow_name')->toArray();

foreach ($workflowFiles as $file) {
    try {
        $workflowInfo = extractWorkflowInfo($file);

        if (!$workflowInfo) {
            continue;
        }

        // Éviter les doublons
        if (in_array($workflowInfo['workflow_name'], $existingWorkflows)) {
            continue;
        }

        VideoContentPlan::create($workflowInfo);
        $processedCount++;

        echo "✅ Ajouté: " . $workflowInfo['workflow_name'] . " (Priorité: " . $workflowInfo['priority'] . ", Viral: " . $workflowInfo['viral_potential'] . "⭐)\n";

    } catch (Exception $e) {
        echo "❌ Erreur avec " . basename($file) . ": " . $e->getMessage() . "\n";
    }
}

echo "\n🎉 Scan terminé !\n";
echo "📊 $processedCount nouveaux workflows ajoutés\n";
echo "💾 Total dans la base: " . VideoContentPlan::count() . " workflows\n";