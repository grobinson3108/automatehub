<?php

namespace App\Services\WatchTrend\Collectors;

use App\Models\WatchtrendSource;
use Illuminate\Support\Facades\Log;

/**
 * LinkedIn Collector — beta
 *
 * LinkedIn has no free public API. This collector attempts to fetch
 * public profile/page posts via third-party RSS proxy services.
 * If the feed is blocked or unavailable, it returns [] gracefully.
 */
class LinkedInCollector extends BaseCollector
{
    public function collect(WatchtrendSource $source): array
    {
        $config     = $source->config;
        $profileUrl = $config['profile_url'] ?? null;

        if (!$profileUrl) {
            Log::warning("WatchTrend LinkedInCollector: missing profile_url", ['source_id' => $source->id]);
            return [];
        }

        // Attempt RSS proxy approaches for LinkedIn public pages/profiles.
        // LinkedIn aggressively blocks scrapers; we try a few known proxies.
        $feedUrls = $this->buildFeedUrls($profileUrl);

        foreach ($feedUrls as $feedUrl) {
            $items = $this->fetchRssFeed($feedUrl, $source->id);
            if (!empty($items)) {
                return $items;
            }
        }

        Log::warning("WatchTrend LinkedInCollector: no feed accessible for profile (source marked beta)", [
            'source_id'   => $source->id,
            'profile_url' => $profileUrl,
        ]);

        return [];
    }

    /**
     * Build candidate RSS proxy URLs from a LinkedIn profile/page URL.
     */
    private function buildFeedUrls(string $profileUrl): array
    {
        $encoded = urlencode($profileUrl);

        return [
            // rss.app proxy (free tier)
            "https://rss.app/feeds/linkedin/{$encoded}.xml",
            // fetchrss proxy
            "https://fetchrss.com/rss/{$encoded}",
        ];
    }

    /**
     * Fetch and parse an RSS feed, returning items array.
     */
    private function fetchRssFeed(string $feedUrl, int $sourceId): array
    {
        $body = $this->httpGet($feedUrl, [
            'User-Agent' => 'WatchTrend/1.0 (+https://automatehub.fr)',
        ]);

        if (!$body) {
            return [];
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);

        if (!$xml) {
            return [];
        }

        $rootName = $xml->getName();

        if ($rootName === 'feed') {
            return $this->parseAtom($xml);
        }

        return $this->parseRss($xml);
    }

    private function parseRss(\SimpleXMLElement $xml): array
    {
        $items   = [];
        $count   = 0;
        $channel = $xml->channel ?? null;

        if (!$channel) {
            return [];
        }

        foreach ($channel->item as $item) {
            if ($count >= $this->maxItems) break;

            $link        = (string) ($item->link ?? '');
            $title       = (string) ($item->title ?? 'Untitled');
            $description = strip_tags((string) ($item->description ?? ''));
            $pubDate     = (string) ($item->pubDate ?? '');

            $author = null;
            if (isset($item->author)) {
                $author = (string) $item->author;
            }

            $publishedAt = null;
            if ($pubDate) {
                try {
                    $publishedAt = \Carbon\Carbon::parse($pubDate);
                } catch (\Exception $e) {
                    $publishedAt = now();
                }
            }

            $items[] = [
                'external_id'  => hash('sha256', $link ?: $title),
                'url'          => $link ?: null,
                'title'        => $title,
                'content'      => mb_substr($description, 0, 5000),
                'author'       => $author ?: null,
                'published_at' => $publishedAt ?? now(),
                'metadata'     => [
                    'source' => 'linkedin',
                ],
            ];

            $count++;
        }

        return $items;
    }

    private function parseAtom(\SimpleXMLElement $xml): array
    {
        $items = [];
        $count = 0;

        foreach ($xml->entry as $entry) {
            if ($count >= $this->maxItems) break;

            $link = '';
            foreach ($entry->link as $linkEl) {
                $rel  = (string) ($linkEl['rel'] ?? 'alternate');
                $href = (string) ($linkEl['href'] ?? '');
                if ($href && ($rel === 'alternate' || $rel === '')) {
                    $link = $href;
                    break;
                }
            }
            if (!$link && isset($entry->link['href'])) {
                $link = (string) $entry->link['href'];
            }

            $title   = (string) ($entry->title ?? 'Untitled');
            $content = '';

            if (isset($entry->content)) {
                $content = strip_tags((string) $entry->content);
            } elseif (isset($entry->summary)) {
                $content = strip_tags((string) $entry->summary);
            }

            $updatedStr  = (string) ($entry->updated ?? '');
            $publishedAt = null;
            if ($updatedStr) {
                try {
                    $publishedAt = \Carbon\Carbon::parse($updatedStr);
                } catch (\Exception $e) {
                    $publishedAt = now();
                }
            }

            $author = null;
            if (isset($entry->author->name)) {
                $author = (string) $entry->author->name;
            }

            $items[] = [
                'external_id'  => hash('sha256', $link ?: $title),
                'url'          => $link ?: null,
                'title'        => $title,
                'content'      => mb_substr($content, 0, 5000),
                'author'       => $author,
                'published_at' => $publishedAt ?? now(),
                'metadata'     => [
                    'source' => 'linkedin',
                ],
            ];

            $count++;
        }

        return $items;
    }
}
