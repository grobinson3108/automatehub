<?php

namespace App\Services\WatchTrend\Collectors;

use App\Models\WatchtrendSource;
use Illuminate\Support\Facades\Log;

class HackerNewsCollector extends BaseCollector
{
    private const MIN_POINTS = 5;

    public function collect(WatchtrendSource $source): array
    {
        $config = $source->config;
        $query  = $config['query'] ?? null;

        if (!$query) {
            Log::warning("WatchTrend HackerNewsCollector: missing query", ['source_id' => $source->id]);
            return [];
        }

        $url  = "https://hn.algolia.com/api/v1/search_by_date?" . http_build_query([
            'query'        => $query,
            'tags'         => 'story',
            'hitsPerPage'  => min(25, $this->maxItems),
        ]);

        $data = $this->httpGetJson($url);

        if (!$data || !isset($data['hits'])) {
            Log::warning("WatchTrend HackerNewsCollector: empty or invalid response", [
                'source_id' => $source->id,
                'query'     => $query,
            ]);
            return [];
        }

        $items = [];
        foreach ($data['hits'] as $hit) {
            $points = $hit['points'] ?? 0;

            // Filter out low-signal items
            if ($points < self::MIN_POINTS) continue;

            $objectId    = $hit['objectID'] ?? null;
            $storyUrl    = $hit['url'] ?? null;
            $hnUrl       = $objectId ? "https://news.ycombinator.com/item?id={$objectId}" : null;
            $url         = $storyUrl ?: $hnUrl;

            $publishedAt = null;
            if (!empty($hit['created_at'])) {
                try {
                    $publishedAt = \Carbon\Carbon::parse($hit['created_at']);
                } catch (\Exception $e) {
                    $publishedAt = now();
                }
            }

            // Many HN stories have no body text
            $content = $hit['story_text'] ?? $hit['title'] ?? '';
            $content = strip_tags($content);

            $items[] = [
                'external_id'  => $objectId,
                'url'          => $url,
                'title'        => $hit['title'] ?? 'Untitled',
                'content'      => mb_substr($content, 0, 5000),
                'author'       => $hit['author'] ?? null,
                'published_at' => $publishedAt ?? now(),
                'metadata'     => [
                    'points'       => $points,
                    'num_comments' => $hit['num_comments'] ?? 0,
                    'objectID'     => $objectId,
                ],
            ];
        }

        return $items;
    }
}
