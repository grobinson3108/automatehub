#!/usr/bin/env php
<?php
/**
 * Script de traduction en masse des packs de workflows
 *
 * Ce script traduit tous les workflows JSON des packs en utilisant
 * le système de traduction existant (extraction → OpenAI → application)
 *
 * Usage: php translate_packs_mass.php [--pack=NOM_DU_PACK] [--dry-run] [--resume]
 */

// Configuration
define('BASE_PATH', '/var/www/automatehub');
define('SOURCE_DIR', BASE_PATH . '/PACKS_WORKFLOWS_PREMIUM');
define('TARGET_DIR', BASE_PATH . '/PACKS_WORKFLOWS_PREMIUM_FR');
define('TEMP_DIR', BASE_PATH . '/PACKS_WORKFLOWS_PREMIUM_FR/.temp');
define('LOG_FILE', BASE_PATH . '/storage/logs/mass_translation.log');
define('PROGRESS_FILE', BASE_PATH . '/storage/logs/translation_progress.json');

// Scripts Python
define('EXTRACT_SCRIPT', BASE_PATH . '/scripts/workflow_translator/extract_texts.py');
define('TRANSLATE_SCRIPT', BASE_PATH . '/scripts/workflow_translator/translate_with_openai.py');
define('APPLY_SCRIPT', BASE_PATH . '/scripts/workflow_translator/apply_translations.py');

// Statistiques globales
$stats = [
    'total_packs' => 0,
    'total_workflows' => 0,
    'processed_workflows' => 0,
    'successful_workflows' => 0,
    'failed_workflows' => 0,
    'skipped_workflows' => 0,
    'start_time' => time(),
    'errors' => []
];

// Options de ligne de commande
$options = parseArgs($argv);
$dryRun = isset($options['dry-run']);
$specificPack = $options['pack'] ?? null;
$resume = isset($options['resume']);

// Initialisation
echo "🚀 Script de traduction en masse des packs de workflows\n";
echo "================================================\n\n";

if ($dryRun) {
    echo "⚠️  MODE DRY-RUN: Aucune traduction ne sera effectuée\n\n";
}

if ($specificPack) {
    echo "🎯 Traitement du pack spécifique: {$specificPack}\n\n";
}

if ($resume) {
    echo "🔄 Mode reprise activé\n\n";
}

// Créer les répertoires nécessaires
createDirectories();

// Charger la progression si mode reprise
$progress = $resume ? loadProgress() : [];

// Scanner les packs
$packs = scanPacks($specificPack);
$stats['total_packs'] = count($packs);

echo "📦 {$stats['total_packs']} pack(s) trouvé(s)\n";
echo "================================================\n\n";

// Traiter chaque pack
foreach ($packs as $packDir) {
    processPack($packDir, $dryRun, $progress);

    // Pause entre les packs pour éviter de surcharger l'API
    if (!$dryRun) {
        echo "  ⏸️  Pause de 2 secondes entre les packs...\n\n";
        sleep(2);
    }
}

// Afficher le rapport final
displayFinalReport();

// Sauvegarder le rapport
saveFinalReport();

echo "\n✅ Traitement terminé!\n";

/**
 * Parse les arguments de ligne de commande
 */
function parseArgs($argv) {
    $options = [];
    foreach ($argv as $arg) {
        if (strpos($arg, '--') === 0) {
            $parts = explode('=', substr($arg, 2), 2);
            $options[$parts[0]] = $parts[1] ?? true;
        }
    }
    return $options;
}

/**
 * Créer les répertoires nécessaires
 */
function createDirectories() {
    $dirs = [TARGET_DIR, TEMP_DIR, dirname(LOG_FILE)];
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
            logMessage("📁 Répertoire créé: {$dir}");
        }
    }
}

/**
 * Scanner les packs disponibles
 */
function scanPacks($specificPack = null) {
    if ($specificPack) {
        $packPath = SOURCE_DIR . '/' . $specificPack;
        if (!is_dir($packPath)) {
            die("❌ Erreur: Le pack '{$specificPack}' n'existe pas\n");
        }
        return [$specificPack];
    }

    $packs = [];
    $items = scandir(SOURCE_DIR);

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $path = SOURCE_DIR . '/' . $item;
        if (is_dir($path)) {
            $packs[] = $item;
        }
    }

    sort($packs);
    return $packs;
}

/**
 * Traiter un pack complet
 */
function processPack($packDir, $dryRun, &$progress) {
    global $stats;

    $sourcePath = SOURCE_DIR . '/' . $packDir;
    $targetPath = TARGET_DIR . '/' . $packDir;

    echo "📦 Traitement du pack: {$packDir}\n";
    echo str_repeat('-', 80) . "\n";

    // Créer le répertoire cible
    if (!$dryRun && !file_exists($targetPath)) {
        mkdir($targetPath, 0755, true);
        logMessage("📁 Répertoire créé: {$targetPath}");
    }

    // Scanner les workflows JSON
    $workflows = glob($sourcePath . '/*.json');
    $totalWorkflows = count($workflows);
    $stats['total_workflows'] += $totalWorkflows;

    echo "  📄 {$totalWorkflows} workflow(s) trouvé(s)\n\n";

    if ($totalWorkflows === 0) {
        echo "  ⚠️  Aucun workflow trouvé dans ce pack\n\n";
        return;
    }

    $packStats = [
        'success' => 0,
        'failed' => 0,
        'skipped' => 0
    ];

    // Traiter chaque workflow
    foreach ($workflows as $index => $workflowPath) {
        $workflowName = basename($workflowPath);
        $num = $index + 1;

        echo "  [{$num}/{$totalWorkflows}] {$workflowName}\n";

        // Vérifier si déjà traité (mode reprise)
        $progressKey = $packDir . '/' . $workflowName;
        if (isset($progress[$progressKey]) && $progress[$progressKey] === 'success') {
            echo "    ⏭️  Déjà traduit (reprise), ignoré\n";
            $packStats['skipped']++;
            $stats['skipped_workflows']++;
            $stats['processed_workflows']++;
            continue;
        }

        if ($dryRun) {
            echo "    🔍 [DRY-RUN] Serait traduit\n";
            $packStats['success']++;
            continue;
        }

        // Traduire le workflow
        $result = translateWorkflow($workflowPath, $targetPath, $workflowName);

        if ($result['success']) {
            echo "    ✅ Traduit avec succès\n";
            $packStats['success']++;
            $stats['successful_workflows']++;
            $progress[$progressKey] = 'success';
            saveProgress($progress);
        } else {
            echo "    ❌ Échec: {$result['error']}\n";
            $packStats['failed']++;
            $stats['failed_workflows']++;
            $stats['errors'][] = [
                'pack' => $packDir,
                'workflow' => $workflowName,
                'error' => $result['error']
            ];
            $progress[$progressKey] = 'failed';
            saveProgress($progress);
        }

        $stats['processed_workflows']++;

        // Pause entre les workflows
        if (!$dryRun && ($index + 1) < $totalWorkflows) {
            echo "    ⏸️  Pause 1s...\n";
            sleep(1);
        }

        echo "\n";
    }

    // Résumé du pack
    echo "  📊 Résumé du pack: {$packStats['success']} réussis, {$packStats['failed']} échoués, {$packStats['skipped']} ignorés\n";
    echo str_repeat('-', 80) . "\n\n";
}

/**
 * Traduire un workflow en utilisant le système existant
 */
function translateWorkflow($sourcePath, $targetDir, $workflowName) {
    try {
        // Lire le fichier source
        $jsonContent = file_get_contents($sourcePath);

        // Valider le JSON
        $workflowData = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'error' => 'JSON invalide: ' . json_last_error_msg()
            ];
        }

        // Créer un fichier temporaire
        $baseName = pathinfo($workflowName, PATHINFO_FILENAME);
        $tempFilePath = TEMP_DIR . '/' . $baseName . '.json';
        file_put_contents($tempFilePath, $jsonContent);

        // ÉTAPE 1: Extraction des textes
        $extractCommand = "cd " . BASE_PATH . " && python3 " . EXTRACT_SCRIPT . " \"{$tempFilePath}\" 2>&1";
        exec($extractCommand, $extractOutput, $extractReturnCode);

        if ($extractReturnCode !== 0) {
            @unlink($tempFilePath);
            return [
                'success' => false,
                'error' => 'Extraction échouée: ' . implode("\n", $extractOutput)
            ];
        }

        // ÉTAPE 2: Traduction avec OpenAI
        $extractedFile = TEMP_DIR . '/' . $baseName . '_texts_to_translate.json';
        if (!file_exists($extractedFile)) {
            @unlink($tempFilePath);
            return [
                'success' => false,
                'error' => 'Fichier d\'extraction non trouvé'
            ];
        }

        $translateCommand = "cd " . BASE_PATH . " && python3 " . TRANSLATE_SCRIPT . " \"{$extractedFile}\" 2>&1";
        exec($translateCommand, $translateOutput, $translateReturnCode);

        if ($translateReturnCode !== 0) {
            @unlink($tempFilePath);
            @unlink($extractedFile);
            return [
                'success' => false,
                'error' => 'Traduction OpenAI échouée'
            ];
        }

        // ÉTAPE 3: Application des traductions
        $translatedFile = TEMP_DIR . '/' . $baseName . '_texts_translated.json';
        if (!file_exists($translatedFile)) {
            @unlink($tempFilePath);
            @unlink($extractedFile);
            return [
                'success' => false,
                'error' => 'Fichier de traduction non trouvé'
            ];
        }

        $applyCommand = "cd " . BASE_PATH . " && python3 " . APPLY_SCRIPT . " \"{$tempFilePath}\" \"{$translatedFile}\" 2>&1";
        exec($applyCommand, $applyOutput, $applyReturnCode);

        // Le fichier final traduit
        $finalTranslatedFile = TEMP_DIR . '/' . $baseName . '_FR.json';

        if ($applyReturnCode === 0 && file_exists($finalTranslatedFile)) {
            // Copier le fichier traduit dans le répertoire cible
            $targetFile = $targetDir . '/' . $baseName . '_FR.json';
            copy($finalTranslatedFile, $targetFile);

            // Nettoyer les fichiers temporaires
            @unlink($tempFilePath);
            @unlink($extractedFile);
            @unlink($translatedFile);
            @unlink($finalTranslatedFile);

            return [
                'success' => true,
                'target_file' => $targetFile
            ];
        } else {
            // Nettoyer
            @unlink($tempFilePath);
            @unlink($extractedFile);
            @unlink($translatedFile);
            @unlink($finalTranslatedFile);

            return [
                'success' => false,
                'error' => 'Application des traductions échouée'
            ];
        }

    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Charger la progression sauvegardée
 */
function loadProgress() {
    if (file_exists(PROGRESS_FILE)) {
        $content = file_get_contents(PROGRESS_FILE);
        return json_decode($content, true) ?: [];
    }
    return [];
}

/**
 * Sauvegarder la progression
 */
function saveProgress($progress) {
    file_put_contents(PROGRESS_FILE, json_encode($progress, JSON_PRETTY_PRINT));
}

/**
 * Afficher le rapport final
 */
function displayFinalReport() {
    global $stats;

    $duration = time() - $stats['start_time'];
    $minutes = floor($duration / 60);
    $seconds = $duration % 60;

    echo "\n";
    echo "================================================\n";
    echo "📊 RAPPORT FINAL\n";
    echo "================================================\n\n";

    echo "📦 Packs traités: {$stats['total_packs']}\n";
    echo "📄 Workflows totaux: {$stats['total_workflows']}\n";
    echo "✅ Réussis: {$stats['successful_workflows']}\n";
    echo "❌ Échoués: {$stats['failed_workflows']}\n";
    echo "⏭️  Ignorés: {$stats['skipped_workflows']}\n";
    echo "⏱️  Durée totale: {$minutes}min {$seconds}s\n";

    if ($stats['failed_workflows'] > 0) {
        echo "\n❌ Erreurs détaillées:\n";
        echo str_repeat('-', 80) . "\n";
        foreach ($stats['errors'] as $error) {
            echo "  • {$error['pack']} / {$error['workflow']}\n";
            echo "    Erreur: {$error['error']}\n\n";
        }
    }

    // Taux de réussite
    if ($stats['processed_workflows'] > 0) {
        $successRate = round(($stats['successful_workflows'] / $stats['processed_workflows']) * 100, 2);
        echo "\n🎯 Taux de réussite: {$successRate}%\n";
    }
}

/**
 * Sauvegarder le rapport final
 */
function saveFinalReport() {
    global $stats;

    $reportFile = BASE_PATH . '/storage/logs/mass_translation_report_' . date('Y-m-d_H-i-s') . '.json';
    file_put_contents($reportFile, json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "\n💾 Rapport sauvegardé: {$reportFile}\n";
}

/**
 * Logger un message
 */
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logLine = "[{$timestamp}] {$message}\n";
    file_put_contents(LOG_FILE, $logLine, FILE_APPEND);
}
