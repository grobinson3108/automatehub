<?php

namespace App\Services\WatchTrend\Collectors;

use App\Models\WatchtrendSource;
use Illuminate\Support\Facades\Log;

/**
 * Product Hunt Collector
 *
 * Uses the free Product Hunt RSS feeds (no API token required):
 * - Main feed: https://www.producthunt.com/feed
 * - Topic feed: https://www.producthunt.com/topics/{topic}/feed
 *
 * Items are filtered to the last 7 days.
 */
class ProductHuntCollector extends BaseCollector
{
    private const CUTOFF_DAYS = 7;

    public function collect(WatchtrendSource $source): array
    {
        $config = $source->config;
        $topic  = $config['topic'] ?? null;

        $feedUrl = $topic
            ? 'https://www.producthunt.com/topics/' . rawurlencode($topic) . '/feed'
            : 'https://www.producthunt.com/feed';

        $body = $this->httpGet($feedUrl, [
            'User-Agent' => 'WatchTrend/1.0 (+https://automatehub.fr)',
            'Accept'     => 'application/rss+xml, application/xml, text/xml',
        ]);

        if (!$body) {
            Log::warning("WatchTrend ProductHuntCollector: could not fetch feed", [
                'source_id' => $source->id,
                'feed_url'  => $feedUrl,
            ]);
            return [];
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);

        if (!$xml) {
            Log::warning("WatchTrend ProductHuntCollector: failed to parse RSS feed", [
                'source_id' => $source->id,
                'feed_url'  => $feedUrl,
            ]);
            return [];
        }

        return $this->parseRss($xml, $source->id);
    }

    private function parseRss(\SimpleXMLElement $xml, int $sourceId): array
    {
        $items   = [];
        $count   = 0;
        $channel = $xml->channel ?? null;
        $cutoff  = now()->subDays(self::CUTOFF_DAYS);

        if (!$channel) {
            return [];
        }

        foreach ($channel->item as $item) {
            if ($count >= $this->maxItems) break;

            $link        = (string) ($item->link ?? '');
            $title       = (string) ($item->title ?? 'Untitled');
            $description = strip_tags((string) ($item->description ?? ''));
            $pubDate     = (string) ($item->pubDate ?? '');

            $publishedAt = null;
            if ($pubDate) {
                try {
                    $publishedAt = \Carbon\Carbon::parse($pubDate);
                } catch (\Exception $e) {
                    $publishedAt = now();
                }
            }

            // Filter: skip items older than CUTOFF_DAYS
            if ($publishedAt && $publishedAt->lt($cutoff)) {
                continue;
            }

            // Collect categories/tags
            $categories = [];
            foreach ($item->category as $cat) {
                $categories[] = (string) $cat;
            }

            $items[] = [
                'external_id'  => hash('sha256', $link ?: $title),
                'url'          => $link ?: null,
                'title'        => $title,
                'content'      => mb_substr($description, 0, 5000),
                'author'       => null,
                'published_at' => $publishedAt ?? now(),
                'metadata'     => [
                    'categories' => $categories,
                    'source'     => 'producthunt',
                ],
            ];

            $count++;
        }

        return $items;
    }
}
