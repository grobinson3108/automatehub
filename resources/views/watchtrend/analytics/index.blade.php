@extends('watchtrend.layouts.app')

@section('title', 'Analytics')
@section('page-title', 'Analytics')

@section('breadcrumb')
    <li class="breadcrumb-item active">Analytics</li>
@endsection

@section('content')
<div x-data="analyticsManager()">

    {{-- ============================================================ --}}
    {{-- FILTERS --}}
    {{-- ============================================================ --}}
    <div class="block block-rounded mb-4">
        <div class="block-content py-3">
            <form method="GET" action="{{ route('watchtrend.analytics') }}" id="analytics-filters-form">
                <div class="row g-2 align-items-end">

                    <div class="col-12 col-md-4">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1">Watch</label>
                        <select class="form-select form-select-sm"
                            x-model="selectedWatch"
                            name="watch_id"
                            @change="applyFilters()">
                            <option value="">Toutes les watches</option>
                            @foreach($watches as $w)
                                <option value="{{ $w->id }}"
                                    {{ $selectedWatchId == $w->id ? 'selected' : '' }}>
                                    {{ $w->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1">Période</label>
                        <select class="form-select form-select-sm"
                            x-model="selectedPeriod"
                            name="period"
                            @change="applyFilters()">
                            <option value="30" {{ $period === '30' ? 'selected' : '' }}>30 derniers jours</option>
                            <option value="90" {{ $period === '90' ? 'selected' : '' }}>90 derniers jours</option>
                            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Tout l'historique</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-2 d-flex align-items-end">
                        @if(request()->hasAny(['watch_id', 'period']))
                            <a href="{{ route('watchtrend.analytics') }}" class="btn btn-sm btn-alt-secondary w-100">
                                <i class="fa fa-times me-1"></i>Réinitialiser
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ROW 1 : STAT CARDS --}}
    {{-- ============================================================ --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center mb-0">
                <div class="block-content py-3">
                    <div class="fs-1 fw-bold text-primary">{{ number_format($globalStats['total_analyses']) }}</div>
                    <div class="text-muted small mt-1"><i class="fa fa-chart-bar me-1"></i>Analyses totales</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center mb-0">
                <div class="block-content py-3">
                    <div class="fs-1 fw-bold" style="color: #7CB68D;">{{ $globalStats['avg_score'] }}</div>
                    <div class="text-muted small mt-1"><i class="fa fa-star me-1"></i>Score moyen / 100</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center mb-0">
                <div class="block-content py-3">
                    <div class="fs-1 fw-bold" style="color: #E8A87C;">{{ number_format($globalStats['total_items']) }}</div>
                    <div class="text-muted small mt-1"><i class="fa fa-database me-1"></i>Items collectés</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center mb-0">
                <div class="block-content py-3">
                    <div class="fs-1 fw-bold" style="color: #6B7280;">{{ number_format($globalStats['total_feedbacks']) }}</div>
                    <div class="text-muted small mt-1"><i class="fa fa-thumbs-up me-1"></i>Feedbacks donnés</div>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- ROW 2 : LINE CHART + PIE CHART --}}
    {{-- ============================================================ --}}
    <div class="row g-4 mb-4">

        {{-- Line chart: Analyses par jour --}}
        <div class="col-12 col-lg-7">
            <div class="block block-rounded h-100 mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-chart-line text-primary me-2"></i>Analyses par jour
                    </h3>
                </div>
                <div class="block-content">
                    <div style="height: 300px;">
                        <canvas id="chartAnalysesOverTime"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pie chart: Répartition par catégorie --}}
        <div class="col-12 col-lg-5">
            <div class="block block-rounded h-100 mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-chart-pie text-primary me-2"></i>Répartition par catégorie
                    </h3>
                </div>
                <div class="block-content">
                    <div style="height: 300px;">
                        <canvas id="chartCategoryDistribution"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- ROW 3 : DOUGHNUT + BAR CHART --}}
    {{-- ============================================================ --}}
    <div class="row g-4 mb-4">

        {{-- Doughnut chart: Sources par type --}}
        <div class="col-12 col-lg-5">
            <div class="block block-rounded h-100 mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-rss text-primary me-2"></i>Sources par type
                    </h3>
                </div>
                <div class="block-content">
                    <div style="height: 300px;">
                        <canvas id="chartSourceDistribution"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bar chart: Distribution des scores --}}
        <div class="col-12 col-lg-7">
            <div class="block block-rounded h-100 mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-sliders-h text-primary me-2"></i>Distribution des scores
                    </h3>
                </div>
                <div class="block-content">
                    <div style="height: 300px;">
                        <canvas id="chartScoreDistribution"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- ROW 4 : FEEDBACKS BAR CHART --}}
    {{-- ============================================================ --}}
    <div class="row g-4 mb-4">

        <div class="col-12 col-lg-6">
            <div class="block block-rounded mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-star text-primary me-2"></i>Feedbacks par note
                    </h3>
                </div>
                <div class="block-content">
                    <div style="height: 260px;">
                        <canvas id="chartFeedbackStats"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- ROW 5 : TOP 5 SOURCES --}}
    {{-- ============================================================ --}}
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <i class="fa fa-trophy text-primary me-2"></i>Top 5 sources par volume collecté
            </h3>
        </div>
        <div class="block-content block-content-full">
            @if($topSources->isEmpty())
                <div class="py-4 text-center text-muted">
                    <i class="fa fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                    Aucune source configurée pour le moment.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-muted small text-uppercase">#</th>
                                <th class="text-muted small text-uppercase">Source</th>
                                <th class="text-muted small text-uppercase">Type</th>
                                <th class="text-muted small text-uppercase text-end">Items collectés</th>
                                <th class="text-muted small text-uppercase text-end">Dernière collecte</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topSources as $i => $source)
                                @php
                                    $sourceIconMap = [
                                        'youtube'       => 'fab fa-youtube text-danger',
                                        'reddit'        => 'fab fa-reddit text-warning',
                                        'rss'           => 'fa fa-rss text-warning',
                                        'hackernews'    => 'fab fa-y-combinator text-warning',
                                        'github'        => 'fab fa-github text-dark',
                                        'twitter'       => 'fab fa-twitter text-info',
                                        'linkedin'      => 'fab fa-linkedin text-primary',
                                        'producthunt'   => 'fab fa-product-hunt text-warning',
                                        'stackoverflow' => 'fab fa-stack-overflow text-warning',
                                    ];
                                    $icon = $sourceIconMap[$source->type] ?? 'fa fa-globe text-muted';
                                @endphp
                                <tr>
                                    <td>
                                        <span class="fw-bold text-muted">{{ $i + 1 }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $source->name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="{{ $icon }} me-1"></i>{{ ucfirst($source->type) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold" style="color: #4A90A4;">
                                            {{ number_format($source->items_collected_total ?? 0) }}
                                        </span>
                                    </td>
                                    <td class="text-end text-muted small">
                                        {{ $source->last_collected_at ? $source->last_collected_at->diffForHumans() : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- Chart.js 4.x via CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>

<script>
function analyticsManager() {
    return {
        selectedWatch: '{{ $selectedWatchId ?? '' }}',
        selectedPeriod: '{{ $period }}',

        applyFilters() {
            const params = new URLSearchParams();
            if (this.selectedWatch) params.set('watch_id', this.selectedWatch);
            if (this.selectedPeriod) params.set('period', this.selectedPeriod);
            window.location.href = '{{ route('watchtrend.analytics') }}' + (params.toString() ? '?' + params.toString() : '');
        }
    };
}

document.addEventListener('DOMContentLoaded', function () {

    const COLORS = {
        blue:   '#4A90A4',
        green:  '#7CB68D',
        orange: '#E8A87C',
        red:    '#dc2626',
        gray:   '#6B7280',
        blueAlpha:   'rgba(74, 144, 164, 0.15)',
    };

    const CATEGORY_COLORS = {
        critical_update: COLORS.red,
        trend:           COLORS.orange,
        worth_watching:  COLORS.blue,
        low_relevance:   COLORS.gray,
    };

    const CATEGORY_LABELS = {
        critical_update: 'Critique',
        trend:           'Tendance',
        worth_watching:  'À surveiller',
        low_relevance:   'Peu pertinent',
    };

    // ----------------------------------------------------------------
    // 1. Line chart — Analyses par jour
    // ----------------------------------------------------------------
    const analysesData = @json($analysesOverTime);

    new Chart(document.getElementById('chartAnalysesOverTime'), {
        type: 'line',
        data: {
            labels: analysesData.map(d => d.date),
            datasets: [{
                label: 'Analyses',
                data: analysesData.map(d => d.count),
                borderColor: COLORS.blue,
                backgroundColor: COLORS.blueAlpha,
                fill: true,
                tension: 0.3,
                pointRadius: 3,
                pointHoverRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });

    // ----------------------------------------------------------------
    // 2. Pie chart — Répartition par catégorie
    // ----------------------------------------------------------------
    const categoryData = @json($categoryDistribution);

    new Chart(document.getElementById('chartCategoryDistribution'), {
        type: 'pie',
        data: {
            labels: categoryData.map(d => CATEGORY_LABELS[d.category] || d.category),
            datasets: [{
                data: categoryData.map(d => d.count),
                backgroundColor: categoryData.map(d => CATEGORY_COLORS[d.category] || COLORS.gray),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // ----------------------------------------------------------------
    // 3. Doughnut chart — Sources par type
    // ----------------------------------------------------------------
    const sourceData = @json($sourceDistribution);
    const SOURCE_COLORS = [COLORS.blue, COLORS.green, COLORS.orange, COLORS.red, COLORS.gray, '#a78bfa', '#34d399'];

    new Chart(document.getElementById('chartSourceDistribution'), {
        type: 'doughnut',
        data: {
            labels: sourceData.map(d => d.type.charAt(0).toUpperCase() + d.type.slice(1)),
            datasets: [{
                data: sourceData.map(d => d.count),
                backgroundColor: sourceData.map((_, i) => SOURCE_COLORS[i % SOURCE_COLORS.length]),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // ----------------------------------------------------------------
    // 4. Bar chart — Distribution des scores
    // ----------------------------------------------------------------
    const scoreData = @json($scoreDistribution);

    new Chart(document.getElementById('chartScoreDistribution'), {
        type: 'bar',
        data: {
            labels: scoreData.map(d => d.range),
            datasets: [{
                label: 'Nombre d\'analyses',
                data: scoreData.map(d => d.count),
                backgroundColor: [COLORS.gray, COLORS.blue, COLORS.green, COLORS.orange, COLORS.red],
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });

    // ----------------------------------------------------------------
    // 5. Horizontal bar chart — Feedbacks par note
    // ----------------------------------------------------------------
    const feedbackData = @json($feedbackByRating);

    new Chart(document.getElementById('chartFeedbackStats'), {
        type: 'bar',
        data: {
            labels: feedbackData.map(d => d.rating + ' étoile' + (d.rating > 1 ? 's' : '')),
            datasets: [{
                label: 'Feedbacks',
                data: feedbackData.map(d => d.count),
                backgroundColor: [COLORS.gray, COLORS.gray, COLORS.blue, COLORS.green, COLORS.orange],
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });

});
</script>
@endpush
