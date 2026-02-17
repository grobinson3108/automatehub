@extends('watchtrend.layouts.app')

@section('title', $watch->name)
@section('page-title', $watch->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('watchtrend.watches.index') }}">Mes Watches</a></li>
    <li class="breadcrumb-item active">{{ $watch->name }}</li>
@endsection

@section('content')

    {{-- Watch Info Card --}}
    <div class="block block-rounded mb-4">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <span class="me-2">{{ $watch->icon ?? '📊' }}</span>
                {{ $watch->name }}
            </h3>
            <div class="block-options d-flex align-items-center gap-2">
                <span class="badge
                    @if($watch->status === 'active') bg-success
                    @elseif($watch->status === 'paused') bg-warning text-dark
                    @else bg-secondary
                    @endif">
                    @if($watch->status === 'active') Actif
                    @elseif($watch->status === 'paused') En pause
                    @else Archivé
                    @endif
                </span>
                <a href="{{ route('watchtrend.watches.index') }}" class="btn btn-sm btn-alt-secondary">
                    <i class="fa fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
        <div class="block-content">
            <div class="row g-4">
                <div class="col-md-6">
                    @if($watch->description)
                        <p class="mb-3">{{ $watch->description }}</p>
                    @else
                        <p class="text-muted mb-3"><em>Aucune description</em></p>
                    @endif
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary-subtle text-primary fs-sm">
                            <i class="fa fa-clock me-1"></i>
                            @switch($watch->collection_frequency)
                                @case('daily') Collecte quotidienne @break
                                @case('weekly') Collecte hebdomadaire @break
                                @case('monthly') Collecte mensuelle @break
                                @case('quarterly') Collecte trimestrielle @break
                                @default {{ $watch->collection_frequency }}
                            @endswitch
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Créé le</dt>
                        <dd class="col-sm-7">{{ $watch->created_at->format('d/m/Y') }}</dd>
                        <dt class="col-sm-5 text-muted">Modifié le</dt>
                        <dd class="col-sm-7">{{ $watch->updated_at->format('d/m/Y') }}</dd>
                        @if($lastCollectedAt)
                            <dt class="col-sm-5 text-muted">Dernière collecte</dt>
                            <dd class="col-sm-7">{{ \Carbon\Carbon::parse($lastCollectedAt)->format('d/m/Y H:i') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-4 mb-4">
        <div class="col-4">
            <div class="block block-rounded text-center py-3">
                <div class="fs-2 fw-bold text-primary">{{ $itemsCount ?? 0 }}</div>
                <div class="text-muted small">Éléments collectés</div>
            </div>
        </div>
        <div class="col-4">
            <div class="block block-rounded text-center py-3">
                <div class="fs-2 fw-bold text-success">{{ $watch->interests->count() }}</div>
                <div class="text-muted small">Centres d'intérêt</div>
            </div>
        </div>
        <div class="col-4">
            <div class="block block-rounded text-center py-3">
                <div class="fs-2 fw-bold text-info">{{ $watch->sources->count() }}</div>
                <div class="text-muted small">Sources actives</div>
            </div>
        </div>
    </div>

    {{-- Interests & Sources --}}
    <div class="row g-4">

        {{-- Interests Mini-List --}}
        <div class="col-md-6">
            <div class="block block-rounded h-100">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-bullseye me-2 text-warning"></i>Centres d'intérêt
                    </h3>
                    <div class="block-options">
                        <a href="{{ route('watchtrend.interests.index', $watch) }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-arrow-right me-1"></i>Gérer
                        </a>
                    </div>
                </div>
                <div class="block-content p-0">
                    @if($watch->interests->isNotEmpty())
                        <ul class="list-group list-group-flush">
                            @foreach($watch->interests->take(5) as $interest)
                                <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2">
                                    <span>{{ $interest->name }}</span>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($interest->keywords && count($interest->keywords) > 0)
                                            <span class="badge bg-light text-dark small">
                                                {{ count($interest->keywords) }} mots-clés
                                            </span>
                                        @endif
                                        <span class="badge
                                            @if($interest->priority === 'high') bg-danger
                                            @elseif($interest->priority === 'medium') bg-warning text-dark
                                            @else bg-info
                                            @endif small">
                                            @if($interest->priority === 'high') Haute
                                            @elseif($interest->priority === 'medium') Moyenne
                                            @else Basse
                                            @endif
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                            @if($watch->interests->count() > 5)
                                <li class="list-group-item text-center text-muted small py-2">
                                    + {{ $watch->interests->count() - 5 }} autre(s)
                                </li>
                            @endif
                        </ul>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fa fa-bullseye fa-2x mb-2 opacity-50"></i>
                            <p class="mb-2">Aucun centre d'intérêt</p>
                            <a href="{{ route('watchtrend.interests.index', $watch) }}" class="btn btn-sm btn-alt-primary">
                                <i class="fa fa-plus me-1"></i>Ajouter
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sources Mini-List --}}
        <div class="col-md-6">
            <div class="block block-rounded h-100">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-satellite-dish me-2 text-info"></i>Sources
                    </h3>
                    <div class="block-options">
                        <a href="{{ route('watchtrend.sources.index') }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-arrow-right me-1"></i>Gérer
                        </a>
                    </div>
                </div>
                <div class="block-content p-0">
                    @if($watch->sources->isNotEmpty())
                        <ul class="list-group list-group-flush">
                            @foreach($watch->sources->take(5) as $source)
                                <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        @switch($source->type)
                                            @case('youtube') <i class="fab fa-youtube text-danger"></i> @break
                                            @case('reddit') <i class="fab fa-reddit text-orange"></i> @break
                                            @case('rss') <i class="fa fa-rss text-warning"></i> @break
                                            @case('hackernews') <i class="fab fa-hacker-news text-warning"></i> @break
                                            @case('github') <i class="fab fa-github text-dark"></i> @break
                                            @case('twitter') <i class="fab fa-twitter text-info"></i> @break
                                            @default <i class="fa fa-globe text-muted"></i>
                                        @endswitch
                                        <span>{{ $source->name }}</span>
                                    </div>
                                    <span class="badge
                                        @if($source->status === 'active') bg-success
                                        @elseif($source->status === 'paused') bg-warning text-dark
                                        @elseif($source->status === 'error') bg-danger
                                        @else bg-secondary
                                        @endif small">
                                        @if($source->status === 'active') Active
                                        @elseif($source->status === 'paused') En pause
                                        @elseif($source->status === 'error') Erreur
                                        @else {{ $source->status }}
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                            @if($watch->sources->count() > 5)
                                <li class="list-group-item text-center text-muted small py-2">
                                    + {{ $watch->sources->count() - 5 }} autre(s)
                                </li>
                            @endif
                        </ul>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fa fa-satellite-dish fa-2x mb-2 opacity-50"></i>
                            <p class="mb-2">Aucune source configurée</p>
                            <a href="{{ route('watchtrend.sources.index') }}" class="btn btn-sm btn-alt-primary">
                                <i class="fa fa-plus me-1"></i>Ajouter
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
