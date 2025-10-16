#!/usr/bin/env php
<?php
/**
 * Nettoie les workflows en supprimant les sticky notes et error handlers en trop
 */

define('BASE_PATH', '/var/www/automatehub');
define('PACKS_DIR', BASE_PATH . '/PACKS_WORKFLOWS_CURATED');

echo "🧹 Nettoyage des Workflows Enrichis\n";
echo str_repeat('=', 80) . "\n\n";

$stats = [
    'total_workflows' => 0,
    'cleaned_workflows' => 0,
    'errors' => []
];

// Scanner tous les packs
$packs = scandir(PACKS_DIR);

foreach ($packs as $pack) {
    if ($pack === '.' || $pack === '..' || !is_dir(PACKS_DIR . '/' . $pack)) continue;

    echo "📦 Nettoyage: $pack\n";

    $packDir = PACKS_DIR . '/' . $pack;
    $workflows = glob($packDir . '/*.json');

    $cleanedInPack = 0;

    foreach ($workflows as $workflowPath) {
        $stats['total_workflows']++;
        $filename = basename($workflowPath);

        $content = file_get_contents($workflowPath);
        $data = json_decode($content, true);

        if (!$data) {
            echo "   ❌ JSON invalide: $filename\n";
            continue;
        }

        $originalNodeCount = count($data['nodes'] ?? []);
        $cleaned = cleanWorkflow($data);
        $newNodeCount = count($cleaned['nodes'] ?? []);

        if ($newNodeCount < $originalNodeCount) {
            // Sauvegarder le workflow nettoyé
            file_put_contents($workflowPath, json_encode($cleaned, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            $removed = $originalNodeCount - $newNodeCount;
            echo "   ✅ $filename: $removed nodes supprimés ($originalNodeCount → $newNodeCount)\n";

            $stats['cleaned_workflows']++;
            $cleanedInPack++;
        }
    }

    if ($cleanedInPack > 0) {
        echo "   🎉 $cleanedInPack workflow(s) nettoyé(s)\n";
    } else {
        echo "   ✅ Aucun nettoyage nécessaire\n";
    }

    echo "\n";
}

echo "\n📊 RÉSUMÉ\n";
echo str_repeat('-', 80) . "\n";
echo "Total workflows analysés: {$stats['total_workflows']}\n";
echo "Workflows nettoyés: {$stats['cleaned_workflows']}\n";
echo "Taux de nettoyage: " . round(($stats['cleaned_workflows'] / $stats['total_workflows']) * 100, 2) . "%\n\n";

echo "✅ Nettoyage terminé!\n";

/**
 * Nettoie un workflow en gardant seulement 1 sticky note et 1 error handler
 */
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
                // Garder la première sticky note avec contenu minimal
                $node['parameters']['content'] = generateMinimalDocumentation($data['name'] ?? 'Workflow');
                $node['id'] = 'documentation-node';
                $node['name'] = 'Workflow Documentation';
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

/**
 * Génère une documentation minimale
 */
function generateMinimalDocumentation($workflowName) {
    return "# $workflowName

## Overview
Automated workflow for $workflowName

## Usage
1. Configure credentials
2. Test workflow
3. Deploy to production

## Security
- Ensure all credentials are properly configured
- Test thoroughly before production use";
}
