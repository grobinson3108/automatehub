<?php

namespace App\Services\WatchTrend\Collectors;

use App\Models\WatchtrendSource;
use Illuminate\Support\Facades\Log;

/**
 * Stack Overflow Collector
 *
 * Uses the Stack Exchange public REST API (no key required, but rate-limited):
 * https://api.stackexchange.com/2.3/questions
 *
 * Config:
 *   config.tag — Stack Overflow tag to monitor (e.g. "laravel", "python")
 *
 * Filters:
 *   - Questions with score >= 3 (reduces noise)
 *   - Sorted by activity, descending
 */
class StackOverflowCollector extends BaseCollector
{
    private const MIN_SCORE = 3;
    private const PAGE_SIZE = 25;
    private const API_BASE  = 'https://api.stackexchange.com/2.3';

    public function collect(WatchtrendSource $source): array
    {
        $config = $source->config;
        $tag    = $config['tag'] ?? null;

        if (!$tag) {
            Log::warning("WatchTrend StackOverflowCollector: missing tag", ['source_id' => $source->id]);
            return [];
        }

        $url = self::API_BASE . '/questions?' . http_build_query([
            'order'    => 'desc',
            'sort'     => 'activity',
            'tagged'   => $tag,
            'site'     => 'stackoverflow',
            'pagesize' => min(self::PAGE_SIZE, $this->maxItems),
            'filter'   => 'default',
        ]);

        $data = $this->httpGetJson($url, [
            'Accept'          => 'application/json',
            'Accept-Encoding' => 'gzip',
            'User-Agent'      => 'WatchTrend/1.0 (+https://automatehub.fr)',
        ]);

        if (!$data || !isset($data['items']) || !is_array($data['items'])) {
            Log::warning("WatchTrend StackOverflowCollector: empty or invalid API response", [
                'source_id' => $source->id,
                'tag'       => $tag,
            ]);
            return [];
        }

        return $this->parseItems($data['items'], $tag);
    }

    private function parseItems(array $apiItems, string $tag): array
    {
        $items = [];
        $count = 0;

        foreach ($apiItems as $question) {
            if ($count >= $this->maxItems) break;

            $score = $question['score'] ?? 0;

            // Skip low-quality questions
            if ($score < self::MIN_SCORE) {
                continue;
            }

            $questionId  = $question['question_id'] ?? null;
            $rawTitle    = $question['title'] ?? 'Untitled';
            // Stack Overflow encodes HTML entities in titles
            $title       = html_entity_decode($rawTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $link        = $question['link'] ?? null;
            $owner       = $question['owner']['display_name'] ?? null;
            $createdAt   = isset($question['creation_date'])
                ? \Carbon\Carbon::createFromTimestamp($question['creation_date'])
                : now();
            $tags        = $question['tags'] ?? [];
            $answerCount = $question['answer_count'] ?? 0;
            $viewCount   = $question['view_count'] ?? 0;

            $items[] = [
                'external_id'  => (string) $questionId,
                'url'          => $link,
                'title'        => $title,
                'content'      => '', // Body not included in default filter to save quota
                'author'       => $owner,
                'published_at' => $createdAt,
                'metadata'     => [
                    'tags'         => $tags,
                    'score'        => $score,
                    'answer_count' => $answerCount,
                    'view_count'   => $viewCount,
                    'monitored_tag' => $tag,
                ],
            ];

            $count++;
        }

        return $items;
    }
}
