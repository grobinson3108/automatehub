<?php

namespace App\Services\WatchTrend\Collectors;

use App\Models\WatchtrendSource;
use App\Models\WatchtrendCollectedItem;
use Illuminate\Support\Facades\Log;

abstract class BaseCollector
{
    protected int $timeout = 30; // seconds
    protected int $maxItems = 50; // max items per collection

    /**
     * Collect new items from the source.
     * @return array Array of raw item data (each with: external_id, url, title, content, author, published_at, metadata)
     */
    abstract public function collect(WatchtrendSource $source): array;

    /**
     * Store collected items, deduplicating by content_hash.
     * Returns count of new items stored.
     */
    public function storeItems(WatchtrendSource $source, array $rawItems): int
    {
        $stored = 0;
        foreach ($rawItems as $item) {
            $hash = hash('sha256', ($item['url'] ?? '') . '|' . ($item['title'] ?? ''));

            // Skip if duplicate (same hash + same watch)
            $exists = WatchtrendCollectedItem::where('content_hash', $hash)
                ->where('watch_id', $source->watch_id)
                ->exists();
            if ($exists) continue;

            WatchtrendCollectedItem::create([
                'source_id'    => $source->id,
                'watch_id'     => $source->watch_id,
                'external_id'  => $item['external_id'] ?? null,
                'url'          => $item['url'] ?? null,
                'title'        => $item['title'] ?? 'Untitled',
                'content'      => mb_substr($item['content'] ?? '', 0, 5000), // truncate
                'author'       => $item['author'] ?? null,
                'published_at' => $item['published_at'] ?? now(),
                'metadata'     => $item['metadata'] ?? [],
                'content_hash' => $hash,
                'is_analyzed'  => false,
                'is_read'      => false,
            ]);
            $stored++;
        }
        return $stored;
    }

    /**
     * Make an HTTP GET request with timeout.
     */
    protected function httpGet(string $url, array $headers = []): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout($this->timeout)
                ->withHeaders($headers)
                ->get($url);

            if ($response->successful()) {
                return $response->body();
            }

            Log::warning("WatchTrend collector HTTP error", [
                'url'    => $url,
                'status' => $response->status(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error("WatchTrend collector HTTP exception", [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Make an HTTP GET request returning JSON.
     */
    protected function httpGetJson(string $url, array $headers = []): ?array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout($this->timeout)
                ->withHeaders($headers)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }
            return null;
        } catch (\Exception $e) {
            Log::error("WatchTrend collector JSON exception", [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
