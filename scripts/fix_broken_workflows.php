#!/usr/bin/env php
<?php
/**
 * Détecte et remplace les workflows cassés dans les packs
 */

define('BASE_PATH', '/var/www/automatehub');
define('PACKS_DIR', BASE_PATH . '/PACKS_WORKFLOWS_CURATED');
define('GITHUB_WORKFLOWS', BASE_PATH . '/WORKFLOWS_GITHUB_ZIE619/workflows');

echo "🔧 Détection et Réparation des Workflows Cassés\n";
echo str_repeat('=', 80) . "\n\n";

$stats = [
    'total_workflows' => 0,
    'broken_workflows' => 0,
    'fixed_workflows' => 0,
    'errors' => []
];

// Scanner tous les packs
$packs = scandir(PACKS_DIR);

foreach ($packs as $pack) {
    if ($pack === '.' || $pack === '..' || !is_dir(PACKS_DIR . '/' . $pack)) continue;

    echo "📦 Vérification: $pack\n";

    $packDir = PACKS_DIR . '/' . $pack;
    $workflows = glob($packDir . '/*.json');

    $brokenInPack = 0;

    foreach ($workflows as $workflowPath) {
        $stats['total_workflows']++;
        $filename = basename($workflowPath);

        $content = file_get_contents($workflowPath);
        $data = json_decode($content, true);

        if (!$data) {
            echo "   ❌ JSON invalide: $filename\n";
            unlink($workflowPath);
            $stats['broken_workflows']++;
            $brokenInPack++;
            continue;
        }

        $isBroken = isWorkflowBroken($data);

        if ($isBroken) {
            echo "   🔴 Cassé: $filename\n";
            echo "      Raison: {$isBroken}\n";

            // Supprimer le workflow cassé
            unlink($workflowPath);

            $stats['broken_workflows']++;
            $brokenInPack++;
            $stats['errors'][] = [
                'pack' => $pack,
                'file' => $filename,
                'reason' => $isBroken
            ];
        }
    }

    if ($brokenInPack > 0) {
        echo "   ⚠️  $brokenInPack workflow(s) cassé(s) supprimé(s)\n";
    } else {
        echo "   ✅ Tous les workflows sont OK\n";
    }

    echo "\n";
}

echo "\n📊 RÉSUMÉ\n";
echo str_repeat('-', 80) . "\n";
echo "Total workflows analysés: {$stats['total_workflows']}\n";
echo "Workflows cassés trouvés: {$stats['broken_workflows']}\n";
echo "Taux de succès: " . round((($stats['total_workflows'] - $stats['broken_workflows']) / $stats['total_workflows']) * 100, 2) . "%\n\n";

if (!empty($stats['errors'])) {
    echo "❌ Détail des workflows cassés:\n";
    echo str_repeat('-', 80) . "\n";
    foreach ($stats['errors'] as $error) {
        echo "  • {$error['pack']} / {$error['file']}\n";
        echo "    → {$error['reason']}\n\n";
    }
}

// Sauvegarder le rapport
file_put_contents(
    PACKS_DIR . '/BROKEN_WORKFLOWS_REPORT.json',
    json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

echo "✅ Analyse terminée!\n";
echo "📄 Rapport sauvegardé: BROKEN_WORKFLOWS_REPORT.json\n";

/**
 * Vérifie si un workflow est cassé
 */
function isWorkflowBroken($data) {
    // Vérifier les nodes
    if (empty($data['nodes'])) {
        return "Aucun node";
    }

    $nodes = $data['nodes'];
    $connections = $data['connections'] ?? [];

    // Trop de nodes de documentation = workflow mal exporté
    $docNodes = array_filter($nodes, function($node) {
        return ($node['type'] ?? '') === 'n8n-nodes-base.stickyNote';
    });

    if (count($docNodes) > 5) {
        return "Trop de sticky notes (" . count($docNodes) . ") - workflow mal exporté";
    }

    // Trop de error handlers = workflow mal exporté
    $errorNodes = array_filter($nodes, function($node) {
        return ($node['type'] ?? '') === 'n8n-nodes-base.stopAndError';
    });

    if (count($errorNodes) > 3) {
        return "Trop d'error handlers (" . count($errorNodes) . ") - workflow mal exporté";
    }

    // Vérifier que les connections ne pointent pas QUE vers des error handlers
    if (!empty($connections)) {
        $totalConnections = 0;
        $errorConnections = 0;

        foreach ($connections as $nodeId => $outputs) {
            foreach ($outputs['main'] ?? [] as $outputIndex => $targets) {
                foreach ($targets as $target) {
                    $totalConnections++;

                    $targetNode = $target['node'] ?? '';
                    if (strpos($targetNode, 'error-handler') === 0 || strpos($targetNode, 'Error Handler') !== false) {
                        $errorConnections++;
                    }
                }
            }
        }

        // Si plus de 80% des connections vont vers des error handlers
        if ($totalConnections > 0 && ($errorConnections / $totalConnections) > 0.8) {
            return "Connections cassées - {$errorConnections}/{$totalConnections} vont vers error handlers";
        }
    }

    // Vérifier les nodes NoOp (peuvent indiquer un workflow incomplet)
    $noOpNodes = array_filter($nodes, function($node) {
        return ($node['type'] ?? '') === 'n8n-nodes-base.noOp';
    });

    if (count($noOpNodes) > count($nodes) * 0.5) {
        return "Trop de NoOp nodes (" . count($noOpNodes) . "/" . count($nodes) . ") - workflow incomplet";
    }

    // Workflow valide
    return false;
}
