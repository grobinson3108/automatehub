<?php
namespace App\Services\WatchTrend;

use App\Models\WatchtrendWatch;
use App\Models\WatchtrendCollectedItem;
use App\Models\WatchtrendAnalysis;
use App\Models\UserAppCredential;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalyzerService
{
    private string $defaultModel = 'gpt-4o-mini';
    private int $maxContentLength = 2000;
    private int $timeout = 60;

    /**
     * Analyze a batch of unanalyzed items for a watch.
     * @param int $watchId
     * @param int $batchSize Max items per batch (default 10)
     * @return array Stats: ['analyzed' => int, 'errors' => int]
     */
    public function analyzeBatch(int $watchId, int $batchSize = 10): array
    {
        $watch = WatchtrendWatch::with([
            'interests' => fn($q) => $q->where('is_active', true),
            'painPoints' => fn($q) => $q->where('status', 'active'),
        ])->findOrFail($watchId);

        $items = WatchtrendCollectedItem::where('watch_id', $watchId)
            ->where('is_analyzed', false)
            ->orderBy('published_at', 'desc')
            ->limit($batchSize)
            ->get();

        if ($items->isEmpty()) {
            return ['analyzed' => 0, 'errors' => 0];
        }

        $apiKey = $this->getApiKey($watch);
        if (!$apiKey) {
            Log::warning("WatchTrend: No API key for watch {$watchId}, skipping analysis");
            return ['analyzed' => 0, 'errors' => 0, 'reason' => 'no_api_key'];
        }

        $model = $this->getModel($watch);
        $analyzed = 0;
        $errors = 0;

        foreach ($items as $item) {
            try {
                $result = $this->analyzeItem($item, $watch, $apiKey, $model);
                if ($result) {
                    $this->storeAnalysis($item, $watch, $result, $model);
                    $item->update(['is_analyzed' => true]);
                    $analyzed++;
                } else {
                    $errors++;
                }
            } catch (\Exception $e) {
                Log::error("WatchTrend analyze error", [
                    'item_id' => $item->id,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            }
        }

        Log::info("WatchTrend analyze batch: watch={$watchId}, analyzed={$analyzed}, errors={$errors}");
        return ['analyzed' => $analyzed, 'errors' => $errors];
    }

    /**
     * Analyze a single item via OpenAI API.
     */
    private function analyzeItem(WatchtrendCollectedItem $item, WatchtrendWatch $watch, string $apiKey, string $model): ?array
    {
        $prompt = $this->buildPrompt($item, $watch);

        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un assistant de veille technologique. Tu réponds UNIQUEMENT en JSON valide.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 500,
                'response_format' => ['type' => 'json_object'],
            ]);

        if (!$response->successful()) {
            Log::error("WatchTrend OpenAI error", [
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 500),
            ]);
            return null;
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? null;
        if (!$content) {
            return null;
        }

        $result = json_decode($content, true);
        if (!$result || !isset($result['relevance_score'])) {
            Log::warning("WatchTrend: Invalid AI response", ['content' => mb_substr($content, 0, 500)]);
            return null;
        }

        $result['tokens_used'] = ($data['usage']['total_tokens'] ?? 0);

        return $result;
    }

    /**
     * Build the analysis prompt including user context.
     */
    private function buildPrompt(WatchtrendCollectedItem $item, WatchtrendWatch $watch): string
    {
        $interestsText = $watch->interests->map(function ($i) {
            $keywords = is_array($i->keywords) ? implode(', ', $i->keywords) : $i->keywords;
            $line = "- {$i->name} (priorité: {$i->priority}) — Mots-clés: {$keywords}";
            if ($i->context_description) {
                $line .= "\n  Contexte: {$i->context_description}";
            }
            return $line;
        })->implode("\n");

        $painPointsText = $watch->painPoints->map(fn($pp) =>
            "- [{$pp->priority}] {$pp->title}: {$pp->description}"
        )->implode("\n") ?: 'Aucun pain point défini.';

        $source = $item->source;
        $sourceInfo = $source ? "{$source->type} - {$source->name}" : 'Source inconnue';

        $content = mb_substr($item->content ?? '', 0, $this->maxContentLength);

        $metadataStr = '';
        if ($item->metadata && is_array($item->metadata)) {
            $meta = $item->metadata;
            $parts = [];
            if (isset($meta['score'])) $parts[] = "Score: {$meta['score']}";
            if (isset($meta['num_comments'])) $parts[] = "Commentaires: {$meta['num_comments']}";
            if (isset($meta['points'])) $parts[] = "Points: {$meta['points']}";
            if (isset($meta['tag_name'])) $parts[] = "Version: {$meta['tag_name']}";
            $metadataStr = implode(', ', $parts);
        }

        $language = $watch->summary_language === 'en' ? 'anglais' : 'français';

        return <<<PROMPT
Analyse l'élément suivant en le comparant aux centres d'intérêt de l'utilisateur.

## Contexte utilisateur
Intérêts :
{$interestsText}

Pain points :
{$painPointsText}

## Élément à analyser
Source : {$sourceInfo}
Titre : {$item->title}
Contenu : {$content}
Auteur : {$item->author}
Date : {$item->published_at}
Métriques : {$metadataStr}

## Instructions
- Score de pertinence 0-100 basé sur la correspondance avec les intérêts
- Résumé en {$language} de 2-3 phrases max
- Suggestion actionnable concrète
- Catégorisation : critical_update (doit agir), trend (tendance à suivre), worth_watching (à surveiller), low_relevance (peu pertinent)
- Répondre UNIQUEMENT en JSON valide avec ces clés exactes :
  relevance_score (int), category (string), summary_fr (string), actionable_insight (string), matching_interests (array of strings), key_takeaways (array of strings, 2-3 max)
PROMPT;
    }

    /**
     * Validate and store the analysis result in DB.
     */
    private function storeAnalysis(WatchtrendCollectedItem $item, WatchtrendWatch $watch, array $result, string $model): void
    {
        $score = max(0, min(100, intval($result['relevance_score'] ?? 0)));
        $validCategories = ['critical_update', 'trend', 'worth_watching', 'low_relevance'];
        $category = in_array($result['category'] ?? '', $validCategories) ? $result['category'] : 'low_relevance';

        WatchtrendAnalysis::create([
            'collected_item_id' => $item->id,
            'watch_id'          => $watch->id,
            'relevance_score'   => $score,
            'category'          => $category,
            'summary_fr'        => mb_substr($result['summary_fr'] ?? '', 0, 2000),
            'actionable_insight' => mb_substr($result['actionable_insight'] ?? '', 0, 1000),
            'matching_interests' => $result['matching_interests'] ?? [],
            'key_takeaways'     => $result['key_takeaways'] ?? [],
            'ai_model'          => $model,
            'ai_mode'           => $watch->ai_mode ?? 'byok',
            'tokens_used'       => $result['tokens_used'] ?? 0,
            'credits_used'      => $this->calculateCredits($watch, $model),
        ]);
    }

    /**
     * Get the OpenAI API key: BYOK from user credentials, managed from env.
     */
    private function getApiKey(WatchtrendWatch $watch): ?string
    {
        $aiMode = $watch->ai_mode ?? 'byok';

        if ($aiMode === 'byok') {
            $credential = UserAppCredential::where('user_id', $watch->user_id)
                ->where('app_slug', 'watchtrend')
                ->where('service', 'openai')
                ->where('is_active', true)
                ->first();

            if ($credential) {
                try {
                    $creds = $credential->getDecryptedCredentials();
                    return $creds['api_key'] ?? null;
                } catch (\Exception $e) {
                    Log::error("WatchTrend: Failed to decrypt API key", ['user_id' => $watch->user_id]);
                    return null;
                }
            }
            return null;
        }

        return config('services.openai.api_key') ?: env('OPENAI_API_KEY');
    }

    /**
     * Get the AI model for this watch (gpt-4o-mini for MVP).
     */
    private function getModel(WatchtrendWatch $watch): string
    {
        return $this->defaultModel;
    }

    /**
     * Credits used per analysis: 0 for BYOK, 1 for managed gpt-4o-mini, 2 for gpt-4o.
     */
    private function calculateCredits(WatchtrendWatch $watch, string $model): float
    {
        if (($watch->ai_mode ?? 'byok') === 'byok') {
            return 0;
        }

        return match ($model) {
            'gpt-4o-mini' => 1,
            'gpt-4o'      => 2,
            default       => 1,
        };
    }
}
