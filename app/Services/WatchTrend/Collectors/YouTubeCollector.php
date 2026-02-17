<?php

namespace App\Services\WatchTrend\Collectors;

use App\Models\WatchtrendSource;
use Illuminate\Support\Facades\Log;

class YouTubeCollector extends BaseCollector
{
    public function collect(WatchtrendSource $source): array
    {
        $config = $source->config;
        $channelId = $config['channel_id'] ?? null;

        if (!$channelId) {
            Log::warning("WatchTrend YouTubeCollector: missing channel_id", ['source_id' => $source->id]);
            return [];
        }

        $apiKey = env('YOUTUBE_API_KEY');

        if ($apiKey) {
            $items = $this->collectViaApi($channelId, $apiKey);
            if ($items !== null) {
                return $items;
            }
            Log::warning("WatchTrend YouTubeCollector: API failed, falling back to RSS", ['source_id' => $source->id]);
        }

        return $this->collectViaRss($channelId);
    }

    private function collectViaApi(string $channelId, string $apiKey): ?array
    {
        $url = "https://www.googleapis.com/youtube/v3/search?" . http_build_query([
            'part'       => 'snippet',
            'channelId'  => $channelId,
            'maxResults' => min(20, $this->maxItems),
            'order'      => 'date',
            'type'       => 'video',
            'key'        => $apiKey,
        ]);

        $data = $this->httpGetJson($url);

        if (!$data || !isset($data['items'])) {
            return null;
        }

        $items = [];
        foreach ($data['items'] as $video) {
            $videoId  = $video['id']['videoId'] ?? null;
            $snippet  = $video['snippet'] ?? [];

            if (!$videoId) continue;

            $publishedAt = null;
            if (!empty($snippet['publishedAt'])) {
                try {
                    $publishedAt = \Carbon\Carbon::parse($snippet['publishedAt']);
                } catch (\Exception $e) {
                    $publishedAt = now();
                }
            }

            $items[] = [
                'external_id'  => $videoId,
                'url'          => "https://youtube.com/watch?v={$videoId}",
                'title'        => $snippet['title'] ?? 'Untitled',
                'content'      => $snippet['description'] ?? '',
                'author'       => $snippet['channelTitle'] ?? null,
                'published_at' => $publishedAt ?? now(),
                'metadata'     => [
                    'channelId'    => $snippet['channelId'] ?? null,
                    'thumbnailUrl' => $snippet['thumbnails']['high']['url'] ?? ($snippet['thumbnails']['default']['url'] ?? null),
                ],
            ];
        }

        return $items;
    }

    private function collectViaRss(string $channelId): array
    {
        $url  = "https://www.youtube.com/feeds/videos.xml?channel_id={$channelId}";
        $body = $this->httpGet($url);

        if (!$body) {
            return [];
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);
        if (!$xml) {
            Log::warning("WatchTrend YouTubeCollector: failed to parse RSS", ['channel_id' => $channelId]);
            return [];
        }

        $namespaces = $xml->getNamespaces(true);
        $items      = [];
        $count      = 0;

        foreach ($xml->entry as $entry) {
            if ($count >= $this->maxItems) break;

            $mediaGroup  = isset($namespaces['media']) ? $entry->children($namespaces['media'])->group : null;
            $videoId     = null;

            if (isset($namespaces['yt'])) {
                $yt      = $entry->children($namespaces['yt']);
                $videoId = (string) ($yt->videoId ?? '');
            }

            $link        = (string) ($entry->link['href'] ?? '');
            $title       = (string) ($entry->title ?? 'Untitled');
            $description = '';
            $thumbnail   = null;

            if ($mediaGroup) {
                $description = (string) ($mediaGroup->description ?? '');
                $thumbnail   = (string) ($mediaGroup->thumbnail['url'] ?? '');
            }

            $publishedAt = null;
            $publishedStr = (string) ($entry->published ?? '');
            if ($publishedStr) {
                try {
                    $publishedAt = \Carbon\Carbon::parse($publishedStr);
                } catch (\Exception $e) {
                    $publishedAt = now();
                }
            }

            $author = (string) ($entry->author->name ?? null);

            $items[] = [
                'external_id'  => $videoId ?: hash('sha256', $link),
                'url'          => $link,
                'title'        => $title,
                'content'      => strip_tags($description),
                'author'       => $author ?: null,
                'published_at' => $publishedAt ?? now(),
                'metadata'     => [
                    'channelId'    => $channelId,
                    'thumbnailUrl' => $thumbnail ?: null,
                ],
            ];

            $count++;
        }

        return $items;
    }
}
