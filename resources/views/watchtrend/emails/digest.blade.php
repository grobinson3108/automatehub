<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WatchTrend - Digest {{ $watch->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background-color: #f8f9fa; color: #333; }
        .wrapper { max-width: 640px; margin: 0 auto; padding: 24px 16px; }
        .header { background-color: #4A90A4; border-radius: 8px 8px 0 0; padding: 28px 32px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: 0.5px; }
        .header p { color: rgba(255,255,255,0.85); font-size: 13px; margin-top: 6px; }
        .body { background-color: #ffffff; padding: 32px; border-left: 1px solid #e0e0e0; border-right: 1px solid #e0e0e0; }
        .digest-title { font-size: 18px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
        .digest-date { font-size: 13px; color: #6B7280; margin-bottom: 28px; }
        .article-card { background-color: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 16px; }
        .article-header { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
        .category-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        .badge-critical_update { background-color: #fee2e2; color: #dc2626; }
        .badge-trend { background-color: #dbeafe; color: #2563eb; }
        .badge-worth_watching { background-color: #d1fae5; color: #059669; }
        .badge-low_relevance { background-color: #f3f4f6; color: #6B7280; }
        .score-pill { display: inline-block; background-color: #4A90A4; color: #fff; border-radius: 12px; padding: 3px 10px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .article-title { font-size: 15px; font-weight: 600; color: #1a1a2e; line-height: 1.4; margin-bottom: 8px; }
        .article-title a { color: #1a1a2e; text-decoration: none; }
        .article-title a:hover { color: #4A90A4; }
        .article-summary { font-size: 13px; color: #4B5563; line-height: 1.6; margin-bottom: 10px; }
        .article-insight { font-size: 13px; color: #374151; background-color: #eff6ff; border-left: 3px solid #4A90A4; padding: 8px 12px; border-radius: 0 4px 4px 0; }
        .article-insight strong { color: #4A90A4; }
        .empty-state { text-align: center; padding: 32px 0; color: #6B7280; font-size: 14px; }
        .footer { background-color: #f1f5f9; border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 8px 8px; padding: 20px 32px; text-align: center; }
        .footer a { color: #4A90A4; text-decoration: none; font-size: 13px; }
        .footer a:hover { text-decoration: underline; }
        .footer .separator { color: #9CA3AF; margin: 0 8px; }
        .footer p { font-size: 12px; color: #9CA3AF; margin-top: 8px; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>WatchTrend</h1>
            <p>Votre veille intelligente</p>
        </div>

        <div class="body">
            <div class="digest-title">Digest — {{ $watch->name }}</div>
            <div class="digest-date">{{ now()->format('d/m/Y') }}</div>

            @if(count($analyses) > 0)
                @foreach($analyses as $analysis)
                    @php
                        $item = is_array($analysis) ? null : $analysis->collectedItem ?? null;
                        $title = is_array($analysis) ? ($analysis['collected_item']['title'] ?? 'Sans titre') : ($item?->title ?? 'Sans titre');
                        $url = is_array($analysis) ? ($analysis['collected_item']['url'] ?? null) : $item?->url;
                        $category = is_array($analysis) ? ($analysis['category'] ?? 'low_relevance') : $analysis->category;
                        $score = is_array($analysis) ? ($analysis['relevance_score'] ?? 0) : $analysis->relevance_score;
                        $summary = is_array($analysis) ? ($analysis['summary_fr'] ?? '') : $analysis->summary_fr;
                        $insight = is_array($analysis) ? ($analysis['actionable_insight'] ?? '') : $analysis->actionable_insight;

                        $categoryLabels = [
                            'critical_update' => 'Mise à jour critique',
                            'trend'           => 'Tendance',
                            'worth_watching'  => 'À surveiller',
                            'low_relevance'   => 'Faible pertinence',
                        ];
                        $categoryLabel = $categoryLabels[$category] ?? $category;
                        $summary = mb_substr($summary ?? '', 0, 300);
                    @endphp
                    <div class="article-card">
                        <div class="article-header">
                            <span class="category-badge badge-{{ $category }}">{{ $categoryLabel }}</span>
                            <span class="score-pill">{{ $score }}/100</span>
                        </div>
                        <div class="article-title">
                            @if($url)
                                <a href="{{ $url }}" target="_blank">{{ $title }}</a>
                            @else
                                {{ $title }}
                            @endif
                        </div>
                        @if($summary)
                            <div class="article-summary">{{ $summary }}</div>
                        @endif
                        @if($insight)
                            <div class="article-insight"><strong>Action :</strong> {{ $insight }}</div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    Aucune nouvelle suggestion pour cette période.
                </div>
            @endif
        </div>

        <div class="footer">
            <a href="{{ url('/watchtrend') }}">Voir le dashboard</a>
            <span class="separator">|</span>
            <a href="{{ url('/watchtrend/settings') }}">Paramètres</a>
            <span class="separator">|</span>
            <a href="#">Se désabonner</a>
            <p>AutomateHub &mdash; WatchTrend &mdash; {{ now()->format('Y') }}</p>
        </div>
    </div>
</body>
</html>
