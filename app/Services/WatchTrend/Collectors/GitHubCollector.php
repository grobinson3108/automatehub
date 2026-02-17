<?php

namespace App\Services\WatchTrend\Collectors;

use App\Models\WatchtrendSource;
use Illuminate\Support\Facades\Log;

class GitHubCollector extends BaseCollector
{
    public function collect(WatchtrendSource $source): array
    {
        $config = $source->config;
        $repo   = $config['repo'] ?? null;

        if (!$repo || !str_contains($repo, '/')) {
            Log::warning("WatchTrend GitHubCollector: missing or invalid repo (expected owner/repo)", ['source_id' => $source->id]);
            return [];
        }

        $headers = [
            'Accept'     => 'application/vnd.github.v3+json',
            'User-Agent' => 'WatchTrend/1.0',
        ];

        $githubToken = env('GITHUB_TOKEN');
        if ($githubToken) {
            $headers['Authorization'] = "Bearer {$githubToken}";
        }

        $items = $this->collectReleases($repo, $headers);

        // Fallback to tags if no releases found
        if (empty($items)) {
            Log::info("WatchTrend GitHubCollector: no releases found, falling back to tags", [
                'source_id' => $source->id,
                'repo'      => $repo,
            ]);
            $items = $this->collectTags($repo, $headers);
        }

        return $items;
    }

    private function collectReleases(string $repo, array $headers): array
    {
        $url  = "https://api.github.com/repos/{$repo}/releases?per_page=" . min(10, $this->maxItems);
        $data = $this->httpGetJson($url, $headers);

        if (!$data || !is_array($data)) {
            return [];
        }

        $items = [];
        foreach ($data as $release) {
            // Skip drafts
            if (!empty($release['draft'])) continue;

            $releaseId   = $release['id'] ?? null;
            $publishedAt = null;

            if (!empty($release['published_at'])) {
                try {
                    $publishedAt = \Carbon\Carbon::parse($release['published_at']);
                } catch (\Exception $e) {
                    $publishedAt = now();
                }
            }

            $body    = $release['body'] ?? '';
            $content = mb_substr($body, 0, 2000);

            $items[] = [
                'external_id'  => (string) $releaseId,
                'url'          => $release['html_url'] ?? null,
                'title'        => $release['name'] ?: ($release['tag_name'] ?? 'Release'),
                'content'      => $content,
                'author'       => $release['author']['login'] ?? null,
                'published_at' => $publishedAt ?? now(),
                'metadata'     => [
                    'tag_name'     => $release['tag_name'] ?? null,
                    'prerelease'   => $release['prerelease'] ?? false,
                    'draft'        => false,
                    'assets_count' => count($release['assets'] ?? []),
                ],
            ];
        }

        return $items;
    }

    private function collectTags(string $repo, array $headers): array
    {
        $url  = "https://api.github.com/repos/{$repo}/tags?per_page=" . min(10, $this->maxItems);
        $data = $this->httpGetJson($url, $headers);

        if (!$data || !is_array($data)) {
            return [];
        }

        $items = [];
        foreach ($data as $tag) {
            $tagName  = $tag['name'] ?? null;
            $commitSha = $tag['commit']['sha'] ?? null;

            if (!$tagName) continue;

            $items[] = [
                'external_id'  => $commitSha ?? hash('sha256', $repo . '|' . $tagName),
                'url'          => "https://github.com/{$repo}/releases/tag/{$tagName}",
                'title'        => "Tag: {$tagName}",
                'content'      => '',
                'author'       => null,
                'published_at' => now(),
                'metadata'     => [
                    'tag_name'     => $tagName,
                    'prerelease'   => false,
                    'draft'        => false,
                    'assets_count' => 0,
                ],
            ];
        }

        return $items;
    }
}
