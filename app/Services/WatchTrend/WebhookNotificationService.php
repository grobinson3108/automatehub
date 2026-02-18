<?php

namespace App\Services\WatchTrend;

use App\Models\WatchtrendAnalysis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookNotificationService
{
    private const CATEGORY_EMOJIS = [
        'critical_update' => '🚨',
        'trend'           => '📈',
        'worth_watching'  => '👀',
        'low_relevance'   => '📌',
    ];

    private const CATEGORY_COLORS = [
        'critical_update' => 15158332, // Red  #E74C3C
        'trend'           => 3066993,  // Green #2ECC71
        'worth_watching'  => 3447003,  // Blue  #3498DB
        'low_relevance'   => 10066329, // Grey  #999999
    ];

    /**
     * Send a Slack Block Kit notification for an analysis.
     */
    public function sendSlackNotification(WatchtrendAnalysis $analysis, string $webhookUrl): bool
    {
        $item    = $analysis->collectedItem;
        $emoji   = self::CATEGORY_EMOJIS[$analysis->category] ?? '📌';
        $summary = mb_substr($analysis->summary_fr ?? '', 0, 280);
        $title   = $item?->title ?? 'Sans titre';
        $url     = $item?->url ?? '';
        $score   = $analysis->relevance_score;

        $blocks = [
            [
                'type' => 'header',
                'text' => [
                    'type'  => 'plain_text',
                    'text'  => "{$emoji} {$title}",
                    'emoji' => true,
                ],
            ],
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => $summary,
                ],
                'accessory' => [
                    'type'  => 'button',
                    'text'  => [
                        'type'  => 'plain_text',
                        'text'  => 'Lire l\'article',
                        'emoji' => false,
                    ],
                    'url'   => $url ?: '#',
                    'style' => 'primary',
                ],
            ],
            [
                'type' => 'context',
                'elements' => [
                    [
                        'type' => 'mrkdwn',
                        'text' => "*Score de pertinence :* {$score}/100  |  *WatchTrend*",
                    ],
                ],
            ],
            ['type' => 'divider'],
        ];

        $payload = ['blocks' => $blocks];

        try {
            $response = Http::timeout(10)->post($webhookUrl, $payload);

            if (!$response->successful()) {
                Log::warning('WatchTrend Slack: sendSlackNotification failed', [
                    'analysis_id' => $analysis->id,
                    'status'      => $response->status(),
                    'body'        => mb_substr($response->body(), 0, 500),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('WatchTrend Slack: exception sending notification', [
                'analysis_id' => $analysis->id,
                'error'       => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send a Discord embed notification for an analysis.
     */
    public function sendDiscordNotification(WatchtrendAnalysis $analysis, string $webhookUrl): bool
    {
        $item    = $analysis->collectedItem;
        $emoji   = self::CATEGORY_EMOJIS[$analysis->category] ?? '📌';
        $summary = mb_substr($analysis->summary_fr ?? '', 0, 300);
        $title   = $item?->title ?? 'Sans titre';
        $url     = $item?->url ?? '';
        $score   = $analysis->relevance_score;
        $color   = self::CATEGORY_COLORS[$analysis->category] ?? 10066329;

        $embed = [
            'title'       => "{$emoji} {$title}",
            'description' => $summary,
            'color'       => $color,
            'footer'      => [
                'text' => "Score de pertinence : {$score}/100 · WatchTrend",
            ],
            'timestamp'   => now()->toIso8601String(),
        ];

        if ($url) {
            $embed['url'] = $url;
        }

        $payload = [
            'username'   => 'WatchTrend',
            'avatar_url' => '',
            'embeds'     => [$embed],
        ];

        try {
            $response = Http::timeout(10)->post($webhookUrl, $payload);

            if (!$response->successful()) {
                Log::warning('WatchTrend Discord: sendDiscordNotification failed', [
                    'analysis_id' => $analysis->id,
                    'status'      => $response->status(),
                    'body'        => mb_substr($response->body(), 0, 500),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('WatchTrend Discord: exception sending notification', [
                'analysis_id' => $analysis->id,
                'error'       => $e->getMessage(),
            ]);
            return false;
        }
    }
}
