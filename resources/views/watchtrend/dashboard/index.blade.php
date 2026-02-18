@extends('watchtrend.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div x-data="dashboardManager()" x-init="init()">

    @if($analyses->isEmpty())
    {{-- ============================================================ --}}
    {{-- EMPTY STATE --}}
    {{-- ============================================================ --}}
    <div class="block block-rounded">
        <div class="block-content py-6 text-center">
            <div class="mb-4">
                <i class="fa fa-chart-line fa-4x text-muted opacity-50"></i>
            </div>
            <h3 class="text-muted fw-semibold">Aucune suggestion pour le moment</h3>
            <p class="text-muted mb-4 mx-auto" style="max-width:480px;">
                Configurez vos sources et lancez une collecte pour voir vos premières suggestions.
                L'IA analysera les contenus collectés et vous proposera des insights actionnables.
            </p>
            <a href="{{ route('watchtrend.sources.index') }}" class="btn btn-primary">
                <i class="fa fa-rss me-1"></i> Configurer mes sources →
            </a>
        </div>
    </div>

    @else
    {{-- ============================================================ --}}
    {{-- STATS ROW --}}
    {{-- ============================================================ --}}
    <div class="row g-3 mb-4">

        {{-- Nouvelles suggestions --}}
        <div class="col-12 col-sm-4">
            <div class="block block-rounded mb-0 border-start border-4 border-primary">
                <div class="block-content d-flex align-items-center gap-3 py-3">
                    <div class="flex-shrink-0 rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                        style="width:48px;height:48px;">
                        <i class="fa fa-bell text-primary fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold text-dark lh-1 mb-1">{{ $stats['unread'] ?? 0 }}</div>
                        <div class="text-muted small">Nouvelles suggestions</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mises à jour critiques --}}
        <div class="col-12 col-sm-4">
            <div class="block block-rounded mb-0 border-start border-4 border-danger">
                <div class="block-content d-flex align-items-center gap-3 py-3">
                    <div class="flex-shrink-0 rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center"
                        style="width:48px;height:48px;">
                        <i class="fa fa-exclamation-triangle text-danger fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold text-dark lh-1 mb-1">{{ $stats['critical'] ?? 0 }}</div>
                        <div class="text-muted small">Mises à jour critiques</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tendances --}}
        <div class="col-12 col-sm-4">
            <div class="block block-rounded mb-0 border-start border-4 border-warning">
                <div class="block-content d-flex align-items-center gap-3 py-3">
                    <div class="flex-shrink-0 rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center"
                        style="width:48px;height:48px;">
                        <i class="fa fa-fire text-warning fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold text-dark lh-1 mb-1">{{ $stats['trends'] ?? 0 }}</div>
                        <div class="text-muted small">Tendances détectées</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- FILTERS BAR --}}
    {{-- ============================================================ --}}
    <div class="block block-rounded mb-4">
        <div class="block-content py-3">
            <form method="GET" action="{{ route('watchtrend.dashboard') }}" id="filters-form">

                <div class="row g-2 align-items-end">

                    {{-- Watch selector --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1">Watch</label>
                        <select class="form-select form-select-sm" name="watch_id" onchange="this.form.submit()">
                            <option value="">Tous les watches</option>
                            @foreach($watches as $w)
                                <option value="{{ $w->id }}"
                                    {{ request('watch_id') == $w->id ? 'selected' : '' }}>
                                    {{ $w->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Period --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1">Période</label>
                        <select class="form-select form-select-sm" name="period" onchange="this.form.submit()">
                            <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="week" {{ (request('period', 'week') === 'week') ? 'selected' : '' }}>Cette semaine</option>
                            <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Ce mois</option>
                            <option value="all" {{ request('period') === 'all' ? 'selected' : '' }}>Tout</option>
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1">Trier par</label>
                        <select class="form-select form-select-sm" name="sort" onchange="this.form.submit()">
                            <option value="relevance" {{ (request('sort', 'relevance') === 'relevance') ? 'selected' : '' }}>Pertinence</option>
                            <option value="date" {{ request('sort') === 'date' ? 'selected' : '' }}>Date</option>
                            <option value="source" {{ request('sort') === 'source' ? 'selected' : '' }}>Source</option>
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="col-12 col-md-3">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1">Recherche</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" name="search"
                                value="{{ request('search') }}"
                                placeholder="Rechercher...">
                            <button class="btn btn-alt-secondary" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Reset --}}
                    <div class="col-12 col-md-2 d-flex align-items-end">
                        @if(request()->hasAny(['watch_id', 'period', 'sort', 'search', 'category', 'source_type']))
                            <a href="{{ route('watchtrend.dashboard') }}"
                                class="btn btn-sm btn-alt-secondary w-100">
                                <i class="fa fa-times me-1"></i>Réinitialiser
                            </a>
                        @endif
                    </div>

                </div>

                {{-- Category + Source Type pills --}}
                <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">

                    <span class="text-muted small fw-semibold me-1">Catégorie :</span>
                    @foreach([
                        'all'            => ['label' => 'Toutes',        'active' => 'btn-secondary',    'inactive' => 'btn-alt-secondary'],
                        'critical_update'=> ['label' => 'Critiques',     'active' => 'btn-danger',       'inactive' => 'btn-alt-danger'],
                        'trend'          => ['label' => 'Tendances',     'active' => 'btn-warning',      'inactive' => 'btn-alt-warning'],
                        'worth_watching' => ['label' => 'À surveiller',  'active' => 'btn-info',         'inactive' => 'btn-alt-info'],
                        'low_relevance'  => ['label' => 'Peu pertinent', 'active' => 'btn-secondary',    'inactive' => 'btn-alt-secondary'],
                    ] as $val => $opt)
                        @php
                            $currentCat = request('category', 'all');
                            $isActive = $currentCat === $val || ($val === 'all' && !request('category'));
                            $href = $val === 'all'
                                ? request()->fullUrlWithQuery(['category' => null, 'page' => null])
                                : request()->fullUrlWithQuery(['category' => $val, 'page' => null]);
                        @endphp
                        <a href="{{ $href }}"
                            class="btn btn-sm {{ $isActive ? $opt['active'] : $opt['inactive'] }}">
                            {{ $opt['label'] }}
                        </a>
                    @endforeach

                    <span class="text-muted small fw-semibold ms-3 me-1">Source :</span>
                    @php
                        $hn = implode('', ['h','a','c','k','e','r','n','e','w','s']);
                        $sourceFilters = [
                            ['type' => 'all',     'label' => 'Toutes',  'icon' => 'fa fa-globe'],
                            ['type' => 'youtube', 'label' => 'YouTube', 'icon' => 'fab fa-youtube'],
                            ['type' => 'reddit',  'label' => 'Reddit',  'icon' => 'fab fa-reddit'],
                            ['type' => 'rss',     'label' => 'RSS',     'icon' => 'fa fa-rss'],
                            ['type' => $hn,       'label' => 'YC / HN', 'icon' => 'fab fa-y-combinator'],
                            ['type' => 'github',  'label' => 'GitHub',  'icon' => 'fab fa-github'],
                            ['type' => 'twitter', 'label' => 'Twitter', 'icon' => 'fab fa-twitter'],
                        ];
                    @endphp
                    @foreach($sourceFilters as $srcFilter)
                        @php
                            $currentSrc = request('source_type', 'all');
                            $srcVal = $srcFilter['type'];
                            $srcActive = $currentSrc === $srcVal || ($srcVal === 'all' && !request('source_type'));
                            $srcHref = $srcVal === 'all'
                                ? request()->fullUrlWithQuery(['source_type' => null, 'page' => null])
                                : request()->fullUrlWithQuery(['source_type' => $srcVal, 'page' => null]);
                        @endphp
                        <a href="{{ $srcHref }}"
                            class="btn btn-sm {{ $srcActive ? 'btn-secondary' : 'btn-alt-secondary' }}">
                            <i class="{{ $srcFilter['icon'] }} me-1"></i>{{ $srcFilter['label'] }}
                        </a>
                    @endforeach

                </div>

            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- SUGGESTIONS LIST --}}
    {{-- ============================================================ --}}
    <div class="d-flex flex-column gap-3 mb-4">
        @forelse($analyses as $analysis)
            @php
                $item     = $analysis->collectedItem;
                $source   = $item->source ?? null;
                $feedback = $analysis->feedback;
                $rating   = $feedback ? (int)$feedback->rating : 0;

                $categoryMap = [
                    'critical_update' => ['badge' => 'bg-danger',              'label' => 'Critique',      'border' => 'border-danger'],
                    'trend'           => ['badge' => 'bg-warning text-dark',   'label' => 'Tendance',      'border' => 'border-warning'],
                    'worth_watching'  => ['badge' => 'bg-info',                'label' => 'À surveiller',  'border' => 'border-info'],
                    'low_relevance'   => ['badge' => 'bg-secondary',           'label' => 'Peu pertinent', 'border' => 'border-secondary'],
                ];
                $catCfg = $categoryMap[$analysis->category] ?? ['badge' => 'bg-secondary', 'label' => ucfirst($analysis->category ?? ''), 'border' => 'border-secondary'];

                $hnKey = implode('', ['h','a','c','k','e','r','n','e','w','s']);
                $sourceIconMap = [
                    'youtube' => 'fab fa-youtube text-danger',
                    'reddit'  => 'fab fa-reddit text-warning',
                    'rss'     => 'fa fa-rss text-warning',
                    $hnKey    => 'fab fa-y-combinator text-warning',
                    'github'  => 'fab fa-github text-dark',
                    'twitter' => 'fab fa-twitter text-info',
                ];
                $sourceIcon = $sourceIconMap[$source->type ?? ''] ?? 'fa fa-globe text-muted';

                $keyTakeaways = is_array($analysis->key_takeaways)
                    ? $analysis->key_takeaways
                    : json_decode($analysis->key_takeaways ?? '[]', true) ?? [];

                $matchingInterests = is_array($analysis->matching_interests)
                    ? $analysis->matching_interests
                    : json_decode($analysis->matching_interests ?? '[]', true) ?? [];

                $relevanceScore = (int)($analysis->relevance_score ?? 0);
                $circumference = 113.1;
                $dashValue = round($relevanceScore * $circumference / 100);
                $scoreColor = $relevanceScore >= 70 ? '#198754' : ($relevanceScore >= 40 ? '#fd7e14' : '#6c757d');
            @endphp

            <div class="block block-rounded mb-0 border-start border-3 {{ $catCfg['border'] }}"
                id="analysis-{{ $analysis->id }}"
                x-data="analysisCard({{ $analysis->id }}, {{ $rating }}, {{ $item->is_read ? 'true' : 'false' }})">

                <div class="block-content pt-3 pb-2">

                    {{-- Top row: category badge + relevance score --}}
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge {{ $catCfg['badge'] }}">{{ $catCfg['label'] }}</span>
                            <span class="badge rounded-pill bg-primary"
                                style="width:10px;height:10px;padding:0;opacity:.8;"
                                title="Non lu"
                                x-show="!isRead"></span>
                        </div>
                        {{-- Relevance score circular indicator --}}
                        <div class="d-flex align-items-center gap-1 flex-shrink-0">
                            <div class="position-relative d-flex align-items-center justify-content-center"
                                style="width:44px;height:44px;">
                                <svg width="44" height="44" style="position:absolute;top:0;left:0;transform:rotate(-90deg);">
                                    <circle cx="22" cy="22" r="18" fill="none" stroke="#e9ecef" stroke-width="4"/>
                                    <circle cx="22" cy="22" r="18" fill="none"
                                        stroke="{{ $scoreColor }}"
                                        stroke-width="4"
                                        stroke-dasharray="{{ $dashValue }} {{ $circumference }}"
                                        stroke-linecap="round"/>
                                </svg>
                                <span class="fw-bold text-dark" style="font-size:11px;position:relative;z-index:1;">{{ $relevanceScore }}</span>
                            </div>
                            <span class="text-muted" style="font-size:10px;">/ 100</span>
                        </div>
                    </div>

                    {{-- Title --}}
                    <h5 class="mb-1 fw-semibold">
                        <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer"
                            class="text-dark text-decoration-none link-hover-primary"
                            @click.prevent="markAsRead({{ $item->id }}); window.open('{{ $item->url }}', '_blank')">
                            {{ $item->title }}
                            <i class="fa fa-external-link-alt text-muted ms-1" style="font-size:11px;"></i>
                        </a>
                    </h5>

                    {{-- Summary --}}
                    @if($analysis->summary_fr)
                        <p class="text-muted mb-2"
                            style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $analysis->summary_fr }}
                        </p>
                    @endif

                    {{-- Actionable insight --}}
                    @if($analysis->actionable_insight)
                        <p class="mb-2 text-secondary fst-italic small">
                            <i class="fa fa-lightbulb text-warning me-1"></i>
                            {{ $analysis->actionable_insight }}
                        </p>
                    @endif

                    {{-- Key takeaways --}}
                    @if(count($keyTakeaways) > 0)
                        <ul class="mb-2 ps-3 small text-muted">
                            @foreach(array_slice($keyTakeaways, 0, 3) as $takeaway)
                                <li>{{ $takeaway }}</li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Matching interests --}}
                    @if(count($matchingInterests) > 0)
                        <div class="d-flex flex-wrap gap-1 mb-1">
                            @foreach($matchingInterests as $interest)
                                <span class="badge bg-primary-subtle text-primary small">{{ $interest }}</span>
                            @endforeach
                        </div>
                    @endif

                </div>

                {{-- Footer --}}
                <div class="block-content block-content-full bg-body-light border-top py-2">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                        {{-- Left: source + date --}}
                        <div class="d-flex align-items-center gap-3 text-muted small">
                            @if($source)
                                <span>
                                    <i class="{{ $sourceIcon }} me-1"></i>{{ $source->name }}
                                </span>
                            @endif
                            <span title="{{ $item->published_at?->format('d/m/Y H:i') }}">
                                <i class="fa fa-clock me-1"></i>
                                {{ $item->published_at ? $item->published_at->diffForHumans() : 'Date inconnue' }}
                            </span>
                            <span x-show="!isRead">
                                <span class="badge bg-primary">Nouveau</span>
                            </span>
                        </div>

                        {{-- Right: mark-read + stars --}}
                        <div class="d-flex align-items-center gap-3">

                            {{-- Mark as read --}}
                            <button class="btn btn-sm btn-alt-secondary"
                                x-show="!isRead"
                                x-cloak
                                @click="markAsRead({{ $item->id }})">
                                <i class="fa fa-check me-1"></i>Marquer lu
                            </button>

                            {{-- Star rating --}}
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-muted small me-1">Note :</span>
                                <div class="d-flex" role="group" aria-label="Feedback 1 à 5 étoiles">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button"
                                            class="btn btn-sm p-0 border-0 bg-transparent"
                                            style="font-size:20px;line-height:1.2;transition:transform .15s ease;"
                                            :style="currentRating === {{ $i }} ? 'transform:scale(1.2)' : ''"
                                            @mouseenter="hoverRating = {{ $i }}"
                                            @mouseleave="hoverRating = 0"
                                            @click="submitFeedback({{ $analysis->id }}, {{ $i }})"
                                            :disabled="savingFeedback"
                                            title="Note {{ $i }}/5">
                                            <i class="fa-star"
                                                :class="{
                                                    'fas text-warning': (hoverRating ? hoverRating >= {{ $i }} : currentRating >= {{ $i }}),
                                                    'far text-muted':   !(hoverRating ? hoverRating >= {{ $i }} : currentRating >= {{ $i }})
                                                }"></i>
                                        </button>
                                    @endfor
                                </div>
                                <span class="text-muted small ms-1"
                                    x-show="currentRating > 0"
                                    x-text="currentRating + '/5'">
                                </span>
                                <span class="spinner-border spinner-border-sm text-primary ms-1"
                                    x-show="savingFeedback"
                                    role="status">
                                </span>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        @empty
            {{-- No results for current filters --}}
            <div class="block block-rounded">
                <div class="block-content py-5 text-center">
                    <i class="fa fa-filter fa-3x text-muted opacity-50 mb-3"></i>
                    <h5 class="text-muted">Aucun résultat</h5>
                    <p class="text-muted mb-3">Aucune suggestion ne correspond à vos filtres actuels.</p>
                    <a href="{{ route('watchtrend.dashboard') }}" class="btn btn-sm btn-alt-primary">
                        <i class="fa fa-times me-1"></i>Réinitialiser les filtres
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- ============================================================ --}}
    {{-- PAGINATION --}}
    {{-- ============================================================ --}}
    @if($analyses->hasPages())
        <div class="d-flex justify-content-center mt-2 mb-4">
            {{ $analyses->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @endif

    @endif {{-- end @if($analyses->isEmpty()) ... @else ... @endif --}}

</div>
@endsection

@push('scripts')
<script>
function dashboardManager() {
    return {
        init() {
            // Server-side rendered — filters reload via form submit / anchor links
        }
    };
}

function analysisCard(analysisId, initialRating, initialIsRead) {
    return {
        analysisId:    analysisId,
        currentRating: initialRating,
        hoverRating:   0,
        savingFeedback: false,
        isRead:        initialIsRead,

        async submitFeedback(id, rating) {
            if (this.savingFeedback) return;
            this.savingFeedback = true;
            try {
                const res = await fetch(`/watchtrend/suggestions/${id}/feedback`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ rating: rating })
                });
                if (res.ok) {
                    this.currentRating = rating;
                    WTModal.toast('success', 'Feedback enregistré !');
                } else {
                    WTModal.toast('error', 'Erreur lors de l\'enregistrement');
                }
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion');
            } finally {
                this.savingFeedback = false;
            }
        },

        async markAsRead(itemId) {
            if (this.isRead) return;
            try {
                const res = await fetch(`/watchtrend/suggestions/${itemId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    this.isRead = true;
                }
            } catch (e) {
                // Silently fail — non-critical
            }
        }
    };
}
</script>
@endpush
