#!/usr/bin/env php
<?php
/**
 * Script de test pour traduire UN SEUL workflow
 */

define('BASE_PATH', '/var/www/automatehub');
define('TEMP_DIR', BASE_PATH . '/PACKS_WORKFLOWS_VENDEURS_FR/TEST_TEMP');

// Scripts Python
define('EXTRACT_SCRIPT', BASE_PATH . '/scripts/workflow_translator/extract_texts.py');
define('TRANSLATE_SCRIPT', BASE_PATH . '/scripts/workflow_translator/translate_with_openai.py');
define('APPLY_SCRIPT', BASE_PATH . '/scripts/workflow_translator/apply_translations.py');

// Fichier à tester
$testFile = BASE_PATH . '/PACKS_WORKFLOWS_VENDEURS/32_AUTOMATION_STARTER_SUCCESS_19EUR/02_0007_Manual_Todoist_Create_Triggered.json';
$outputDir = BASE_PATH . '/PACKS_WORKFLOWS_VENDEURS_FR/TEST';

echo "🧪 Test de traduction d'un seul workflow\n";
echo "==========================================\n\n";

// Créer les répertoires
if (!file_exists(TEMP_DIR)) mkdir(TEMP_DIR, 0755, true);
if (!file_exists($outputDir)) mkdir($outputDir, 0755, true);

// Lire le fichier
echo "📖 Lecture du fichier source...\n";
$jsonContent = file_get_contents($testFile);
$originalData = json_decode($jsonContent, true);

echo "   Nom du workflow: " . ($originalData['name'] ?? 'N/A') . "\n";
echo "   Nombre de nodes: " . count($originalData['nodes'] ?? []) . "\n\n";

// Créer un fichier temporaire
$baseName = 'test_workflow';
$tempFilePath = TEMP_DIR . '/' . $baseName . '.json';
file_put_contents($tempFilePath, $jsonContent);

echo "🔄 Étape 1/3: Extraction des textes...\n";
$extractCommand = "cd " . BASE_PATH . " && python3 " . EXTRACT_SCRIPT . " \"{$tempFilePath}\" 2>&1";
exec($extractCommand, $extractOutput, $extractReturnCode);

if ($extractReturnCode !== 0) {
    echo "❌ ÉCHEC de l'extraction\n";
    echo implode("\n", $extractOutput) . "\n";
    exit(1);
}

echo "✅ Extraction réussie\n";
echo "   Output: " . implode("\n   ", $extractOutput) . "\n\n";

// Vérifier le fichier d'extraction
$extractedFile = TEMP_DIR . '/' . $baseName . '_texts_to_translate.json';
if (!file_exists($extractedFile)) {
    echo "❌ Fichier d'extraction non trouvé: {$extractedFile}\n";
    exit(1);
}

$extractedData = json_decode(file_get_contents($extractedFile), true);
echo "📝 Textes extraits: " . count($extractedData['texts'] ?? []) . "\n";
foreach ($extractedData['texts'] ?? [] as $id => $text) {
    echo "   • {$id}: " . substr($text['original'], 0, 50) . "...\n";
}
echo "\n";

echo "🌐 Étape 2/3: Traduction avec OpenAI...\n";
$translateCommand = "cd " . BASE_PATH . " && python3 " . TRANSLATE_SCRIPT . " \"{$extractedFile}\" 2>&1";
exec($translateCommand, $translateOutput, $translateReturnCode);

if ($translateReturnCode !== 0) {
    echo "❌ ÉCHEC de la traduction OpenAI\n";
    echo implode("\n", $translateOutput) . "\n";
    exit(1);
}

echo "✅ Traduction OpenAI réussie\n";
echo "   Output: " . implode("\n   ", array_slice($translateOutput, -5)) . "\n\n";

// Vérifier le fichier de traduction
$translatedFile = TEMP_DIR . '/' . $baseName . '_texts_translated.json';
if (!file_exists($translatedFile)) {
    echo "❌ Fichier de traduction non trouvé: {$translatedFile}\n";
    exit(1);
}

$translatedData = json_decode(file_get_contents($translatedFile), true);
echo "📝 Textes traduits: " . count($translatedData['texts'] ?? []) . "\n";
foreach ($translatedData['texts'] ?? [] as $id => $text) {
    echo "   • {$id}:\n";
    echo "     EN: " . substr($text['original'], 0, 40) . "...\n";
    echo "     FR: " . substr($text['translated'] ?? 'N/A', 0, 40) . "...\n";
}
echo "\n";

echo "✨ Étape 3/3: Application des traductions...\n";
$applyCommand = "cd " . BASE_PATH . " && python3 " . APPLY_SCRIPT . " \"{$tempFilePath}\" \"{$translatedFile}\" 2>&1";
exec($applyCommand, $applyOutput, $applyReturnCode);

if ($applyReturnCode !== 0) {
    echo "❌ ÉCHEC de l'application\n";
    echo implode("\n", $applyOutput) . "\n";
    exit(1);
}

echo "✅ Application réussie\n";
echo "   Output: " . implode("\n   ", array_slice($applyOutput, -3)) . "\n\n";

// Vérifier le fichier final
$finalFile = TEMP_DIR . '/' . $baseName . '_FR.json';
if (!file_exists($finalFile)) {
    echo "❌ Fichier final non trouvé: {$finalFile}\n";
    exit(1);
}

// Copier dans le dossier de test
$targetFile = $outputDir . '/02_0007_Manual_Todoist_Create_Triggered_FR.json';
copy($finalFile, $targetFile);

echo "🎉 SUCCÈS!\n";
echo "==========================================\n\n";

// Afficher une comparaison
$finalData = json_decode(file_get_contents($finalFile), true);

echo "📊 Comparaison:\n";
echo "   Original: " . ($originalData['name'] ?? 'N/A') . "\n";
echo "   Traduit:  " . ($finalData['name'] ?? 'N/A') . "\n\n";

echo "   Nodes originaux:\n";
foreach ($originalData['nodes'] ?? [] as $node) {
    echo "   • " . ($node['name'] ?? 'N/A') . "\n";
}

echo "\n   Nodes traduits:\n";
foreach ($finalData['nodes'] ?? [] as $node) {
    echo "   • " . ($node['name'] ?? 'N/A') . "\n";
}

echo "\n💾 Fichier sauvegardé: {$targetFile}\n";

// Nettoyer
@unlink($tempFilePath);
@unlink($extractedFile);
@unlink($translatedFile);
@unlink($finalFile);

echo "\n✅ Test terminé avec succès!\n";
