<?php

namespace App\Services\WatchTrend\Collectors;

use App\Models\WatchtrendSource;
use Illuminate\Support\Facades\Log;

class RssCollector extends BaseCollector
{
    public function collect(WatchtrendSource $source): array
    {
        $config  = $source->config;
        $feedUrl = $config['feed_url'] ?? null;

        if (!$feedUrl) {
            Log::warning("WatchTrend RssCollector: missing feed_url", ['source_id' => $source->id]);
            return [];
        }

        $body = $this->httpGet($feedUrl);

        if (!$body) {
            return [];
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);

        if (!$xml) {
            Log::warning("WatchTrend RssCollector: failed to parse feed", [
                'source_id' => $source->id,
                'feed_url'  => $feedUrl,
            ]);
            return [];
        }

        $rootName = $xml->getName();

        // Atom feed: root element is <feed>
        if ($rootName === 'feed') {
            return $this->parseAtom($xml);
        }

        // RSS 2.0: root element is <rss>, items inside <channel><item>
        return $this->parseRss($xml);
    }

    private function parseRss(\SimpleXMLElement $xml): array
    {
        $items    = [];
        $count    = 0;
        $channel  = $xml->channel ?? null;

        if (!$channel) {
            return [];
        }

        foreach ($channel->item as $item) {
            if ($count >= $this->maxItems) break;

            $link        = (string) ($item->link ?? '');
            $title       = (string) ($item->title ?? 'Untitled');
            $description = strip_tags((string) ($item->description ?? ''));
            $pubDate     = (string) ($item->pubDate ?? '');

            // Try dc:creator for author
            $namespaces = $item->getNamespaces(true);
            $author     = null;
            if (isset($namespaces['dc'])) {
                $dc     = $item->children($namespaces['dc']);
                $author = (string) ($dc->creator ?? '');
            }
            if (!$author && isset($item->author)) {
                $author = (string) $item->author;
            }

            // Collect categories
            $categories = [];
            foreach ($item->category as $cat) {
                $categories[] = (string) $cat;
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
                'external_id'  => hash('sha256', $link),
                'url'          => $link,
                'title'        => $title,
                'content'      => mb_substr($description, 0, 5000),
                'author'       => $author ?: null,
                'published_at' => $publishedAt ?? now(),
                'metadata'     => [
                    'categories' => $categories,
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

            // Get href from link element
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
                'external_id'  => hash('sha256', $link),
                'url'          => $link,
                'title'        => $title,
                'content'      => mb_substr($content, 0, 5000),
                'author'       => $author,
                'published_at' => $publishedAt ?? now(),
                'metadata'     => [
                    'categories' => [],
                ],
            ];

            $count++;
        }

        return $items;
    }
}
