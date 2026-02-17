<?php

namespace App\Services\WatchTrend\Collectors;

use App\Models\WatchtrendSource;
use Illuminate\Support\Facades\Log;

class TwitterCollector extends BaseCollector
{
    private const NITTER_HOST = 'https://nitter.net';

    public function collect(WatchtrendSource $source): array
    {
        $config   = $source->config;
        $username = $config['username'] ?? null;

        if (!$username) {
            Log::warning("WatchTrend TwitterCollector: missing username", ['source_id' => $source->id]);
            return [];
        }

        // Clean @ prefix if present
        $username = ltrim($username, '@');

        $rssUrl = self::NITTER_HOST . "/{$username}/rss";
        $body   = $this->httpGet($rssUrl);

        if (!$body) {
            Log::warning("WatchTrend TwitterCollector: Nitter RSS unavailable, returning empty", [
                'source_id' => $source->id,
                'username'  => $username,
            ]);
            return [];
        }

        return $this->parseNitterRss($body, $username);
    }

    private function parseNitterRss(string $body, string $username): array
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);

        if (!$xml) {
            Log::warning("WatchTrend TwitterCollector: failed to parse Nitter RSS", ['username' => $username]);
            return [];
        }

        $items   = [];
        $count   = 0;
        $channel = $xml->channel ?? null;

        if (!$channel) {
            return [];
        }

        foreach ($channel->item as $item) {
            if ($count >= $this->maxItems) break;

            $link        = (string) ($item->link ?? '');
            $fullText    = strip_tags((string) ($item->description ?? ''));
            $title       = mb_substr($fullText, 0, 100);
            $pubDate     = (string) ($item->pubDate ?? '');

            $publishedAt = null;
            if ($pubDate) {
                try {
                    $publishedAt = \Carbon\Carbon::parse($pubDate);
                } catch (\Exception $e) {
                    $publishedAt = now();
                }
            }

            $items[] = [
                'external_id'  => hash('sha256', $link),
                'url'          => $link,
                'title'        => $title ?: 'Tweet',
                'content'      => mb_substr($fullText, 0, 5000),
                'author'       => $username,
                'published_at' => $publishedAt ?? now(),
                'metadata'     => [
                    'username' => $username,
                    'source'   => 'nitter',
                ],
            ];

            $count++;
        }

        return $items;
    }
}
