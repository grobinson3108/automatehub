<?php

namespace App\Services\WatchTrend\Collectors;

use App\Models\WatchtrendSource;
use Illuminate\Support\Facades\Log;

class RedditCollector extends BaseCollector
{
    public function collect(WatchtrendSource $source): array
    {
        $config     = $source->config;
        $subreddit  = $config['subreddit'] ?? null;

        if (!$subreddit) {
            Log::warning("WatchTrend RedditCollector: missing subreddit", ['source_id' => $source->id]);
            return [];
        }

        // Clean r/ prefix if present
        if (str_starts_with($subreddit, 'r/')) {
            $subreddit = substr($subreddit, 2);
        }
        $subreddit = ltrim($subreddit, '/');

        $url  = "https://www.reddit.com/r/{$subreddit}/hot.json?limit=" . min(25, $this->maxItems);
        $data = $this->httpGetJson($url, [
            'User-Agent' => 'WatchTrend/1.0',
        ]);

        if (!$data || !isset($data['data']['children'])) {
            Log::warning("WatchTrend RedditCollector: empty or invalid response", [
                'source_id' => $source->id,
                'subreddit' => $subreddit,
            ]);
            return [];
        }

        $items = [];
        foreach ($data['data']['children'] as $child) {
            $post = $child['data'] ?? [];

            // Skip stickied posts
            if (!empty($post['stickied'])) continue;

            $postId      = $post['id'] ?? null;
            $permalink   = $post['permalink'] ?? null;
            $fullUrl     = $permalink ? "https://www.reddit.com{$permalink}" : null;

            $publishedAt = null;
            if (!empty($post['created_utc'])) {
                try {
                    $publishedAt = \Carbon\Carbon::createFromTimestamp((int) $post['created_utc']);
                } catch (\Exception $e) {
                    $publishedAt = now();
                }
            }

            $items[] = [
                'external_id'  => $postId,
                'url'          => $fullUrl,
                'title'        => $post['title'] ?? 'Untitled',
                'content'      => mb_substr(strip_tags($post['selftext'] ?? ''), 0, 5000),
                'author'       => $post['author'] ?? null,
                'published_at' => $publishedAt ?? now(),
                'metadata'     => [
                    'score'        => $post['score'] ?? 0,
                    'num_comments' => $post['num_comments'] ?? 0,
                    'subreddit'    => $post['subreddit'] ?? $subreddit,
                    'is_self'      => $post['is_self'] ?? false,
                    'domain'       => $post['domain'] ?? null,
                ],
            ];
        }

        return $items;
    }
}
