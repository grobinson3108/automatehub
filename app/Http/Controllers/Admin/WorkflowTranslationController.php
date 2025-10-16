<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class WorkflowTranslationController extends Controller
{
    /**
     * Display the workflow translation interface.
     */
    public function index()
    {
        try {
            return view('admin.tools.workflow-translation');
        } catch (\Exception $e) {
            \Log::error('Erreur dans workflow translation index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->view('admin.tools.workflow-translation-simple', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Translate a JSON workflow using the existing translation scripts.
     */
    public function translate(Request $request): JsonResponse
    {
        try {
            // Debug temporaire
            \Log::info('Translation request received', [
                'has_file' => $request->hasFile('workflow_file'),
                'json_content_length' => $request->has('json_content') ? strlen($request->input('json_content')) : 0,
                'workflow_name' => $request->input('workflow_name'),
                'all_input' => $request->all()
            ]);
            // Validation simplifiée
            if (!$request->hasFile('workflow_file') && !$request->filled('json_content')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Veuillez fournir soit un fichier JSON soit du contenu JSON'
                ], 422);
            }

            if ($request->hasFile('workflow_file')) {
                $request->validate([
                    'workflow_file' => 'required|file|max:10240',
                    'workflow_name' => 'nullable|string|max:255'
                ]);
            } else {
                $request->validate([
                    'json_content' => 'required|string',
                    'workflow_name' => 'nullable|string|max:255'
                ]);
            }

            // Déterminer la source du JSON
            if ($request->hasFile('workflow_file')) {
                // Upload de fichier
                $file = $request->file('workflow_file');

                // Vérifier l'extension
                $extension = strtolower($file->getClientOriginalExtension());
                if (!in_array($extension, ['json', 'txt'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Le fichier doit avoir une extension .json ou .txt'
                    ], 400);
                }

                $jsonContent = file_get_contents($file->getPathname());
                $workflowName = $request->input('workflow_name') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            } else {
                // JSON collé
                $jsonContent = $request->input('json_content');
                $workflowName = $request->input('workflow_name', 'workflow_' . time());
            }

            // Valider que c'est un JSON valide
            $workflowData = json_decode($jsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'JSON invalide: ' . json_last_error_msg()
                ], 400);
            }

            // Créer un fichier temporaire avec le workflow
            $tempDir = storage_path('app/temp/translations');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $tempFilePath = $tempDir . '/' . $workflowName . '.json';
            file_put_contents($tempFilePath, $jsonContent);

            // Utiliser le système de traduction en 3 étapes (extraction -> OpenAI -> application)
            $extractScript = base_path('scripts/workflow_translator/extract_texts.py');
            $translateScript = base_path('scripts/workflow_translator/translate_with_openai.py');
            $applyScript = base_path('scripts/workflow_translator/apply_translations.py');

            // Étape 1: Extraction des textes
            $extractCommand = "cd " . base_path() . " && python3 {$extractScript} \"{$tempFilePath}\"";
            $extractOutput = [];
            $extractReturnCode = 0;
            exec($extractCommand . ' 2>&1', $extractOutput, $extractReturnCode);

            if ($extractReturnCode !== 0) {
                @unlink($tempFilePath);
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'extraction des textes',
                    'script_output' => implode("\n", $extractOutput)
                ], 500);
            }

            // Étape 2: Traduction avec OpenAI
            $extractedFile = $tempDir . '/' . $workflowName . '_texts_to_translate.json';
            if (!file_exists($extractedFile)) {
                @unlink($tempFilePath);
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier d\'extraction non trouvé',
                    'script_output' => implode("\n", $extractOutput)
                ], 500);
            }

            $translateCommand = "cd " . base_path() . " && python3 {$translateScript} \"{$extractedFile}\"";
            $translateOutput = [];
            $translateReturnCode = 0;
            exec($translateCommand . ' 2>&1', $translateOutput, $translateReturnCode);

            if ($translateReturnCode !== 0) {
                @unlink($tempFilePath);
                @unlink($extractedFile);
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la traduction OpenAI',
                    'script_output' => implode("\n", array_merge($extractOutput, $translateOutput))
                ], 500);
            }

            // Étape 3: Application des traductions
            $translatedFile = $tempDir . '/' . $workflowName . '_texts_translated.json';
            if (!file_exists($translatedFile)) {
                @unlink($tempFilePath);
                @unlink($extractedFile);
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier de traduction non trouvé',
                    'script_output' => implode("\n", array_merge($extractOutput, $translateOutput))
                ], 500);
            }

            $applyCommand = "cd " . base_path() . " && python3 {$applyScript} \"{$tempFilePath}\" \"{$translatedFile}\"";
            $applyOutput = [];
            $applyReturnCode = 0;
            exec($applyCommand . ' 2>&1', $applyOutput, $applyReturnCode);

            $allOutput = array_merge($extractOutput, $translateOutput, $applyOutput);

            // Définir le chemin du fichier final traduit (doit correspondre au script apply_translations.py)
            $finalTranslatedFile = $tempDir . '/' . pathinfo($tempFilePath, PATHINFO_FILENAME) . '_FR.json';

            if ($applyReturnCode === 0 && file_exists($finalTranslatedFile)) {
                $translatedContent = file_get_contents($finalTranslatedFile);
                $translatedData = json_decode($translatedContent, true);

                // Nettoyer les fichiers temporaires
                @unlink($tempFilePath);
                @unlink($extractedFile);
                @unlink($translatedFile);
                @unlink($finalTranslatedFile);

                return response()->json([
                    'success' => true,
                    'message' => 'Workflow traduit avec succès (3 étapes : extraction → OpenAI → application)',
                    'original_json' => $workflowData,
                    'translated_json' => $translatedData,
                    'translated_content' => $translatedContent,
                    'script_output' => implode("\n", $allOutput)
                ]);
            } else {
                // Nettoyer les fichiers temporaires
                @unlink($tempFilePath);
                @unlink($extractedFile);
                if (file_exists($translatedFile)) @unlink($translatedFile);
                if (file_exists($finalTranslatedFile)) @unlink($finalTranslatedFile);

                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'application des traductions',
                    'script_output' => implode("\n", $allOutput),
                    'return_code' => $applyReturnCode
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la traduction de workflow', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download the translated workflow as a file.
     */
    public function download(Request $request)
    {
        try {
            $request->validate([
                'translated_json' => 'required|string',
                'filename' => 'nullable|string|max:255'
            ]);

            $translatedJson = $request->input('translated_json');
            $filename = $request->input('filename', 'workflow_traduit_' . time() . '.json');

            // Assurer que le nom de fichier se termine par .json
            if (!str_ends_with($filename, '.json')) {
                $filename .= '.json';
            }

            // Valider que c'est un JSON valide
            $data = json_decode($translatedJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'JSON traduit invalide'
                ], 400);
            }

            // Formater le JSON pour le téléchargement
            $formattedJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            return response($formattedJson)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors du téléchargement de workflow traduit', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement'
            ], 500);
        }
    }

    /**
     * Get translation statistics and script status.
     */
    public function getStatus(): JsonResponse
    {
        try {
            $scriptPath = base_path('scripts/workflow_translator/intelligent_translate.py');
            $scriptsExist = file_exists($scriptPath);

            return response()->json([
                'success' => true,
                'scripts_available' => $scriptsExist,
                'python_version' => $this->getPythonVersion(),
                'script_path' => $scriptPath
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du statut'
            ], 500);
        }
    }

    /**
     * Get Python version for diagnostics.
     */
    private function getPythonVersion(): string
    {
        try {
            $output = shell_exec('python3 --version 2>&1');
            return $output ? trim($output) : 'Non disponible';
        } catch (\Exception $e) {
            return 'Erreur: ' . $e->getMessage();
        }
    }
}