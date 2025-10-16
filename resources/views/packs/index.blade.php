@extends('layouts.app')

@section('title', 'Packs Workflows Premium - AutomateHub')
@section('description', 'Découvrez nos packs de workflows n8n premium pour automatiser votre business')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-12">
                <h1 class="display-4 fw-bold mb-4">
                    Packs <span class="text-infinity-blue">Workflows Premium</span>
                </h1>
                <p class="lead text-muted mb-5 mx-auto" style="max-width: 700px;">
                    Des collections complètes de workflows n8n prêts à l'emploi pour automatiser votre business
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Filters -->
<section class="section bg-light py-4">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
            <a href="{{ route('packs.index') }}"
               class="btn {{ !request('category') ? 'btn-primary' : 'btn-outline-primary' }}">
                Tous les Packs
            </a>
            <a href="{{ route('packs.index', ['category' => 'crypto']) }}"
               class="btn {{ request('category') === 'crypto' ? 'btn-primary' : 'btn-outline-primary' }}">
                Crypto
            </a>
            <a href="{{ route('packs.index', ['category' => 'ia']) }}"
               class="btn {{ request('category') === 'ia' ? 'btn-primary' : 'btn-outline-primary' }}">
                Intelligence Artificielle
            </a>
            <a href="{{ route('packs.index', ['category' => 'marketing']) }}"
               class="btn {{ request('category') === 'marketing' ? 'btn-primary' : 'btn-outline-primary' }}">
                Marketing
            </a>
            <a href="{{ route('packs.index', ['category' => 'business']) }}"
               class="btn {{ request('category') === 'business' ? 'btn-primary' : 'btn-outline-primary' }}">
                Business
            </a>
        </div>

        <div class="text-center small text-muted">
            <span class="me-3">Trier par:</span>
            <a href="{{ route('packs.index', array_merge(request()->query(), ['sort' => 'featured'])) }}"
               class="text-decoration-none {{ request('sort', 'featured') === 'featured' ? 'fw-bold text-infinity-blue' : 'text-muted' }}">
                Recommandés
            </a>
            <span class="mx-2">|</span>
            <a href="{{ route('packs.index', array_merge(request()->query(), ['sort' => 'popular'])) }}"
               class="text-decoration-none {{ request('sort') === 'popular' ? 'fw-bold text-infinity-blue' : 'text-muted' }}">
                Populaires
            </a>
            <span class="mx-2">|</span>
            <a href="{{ route('packs.index', array_merge(request()->query(), ['sort' => 'price_low'])) }}"
               class="text-decoration-none {{ request('sort') === 'price_low' ? 'fw-bold text-infinity-blue' : 'text-muted' }}">
                Prix croissant
            </a>
            <span class="mx-2">|</span>
            <a href="{{ route('packs.index', array_merge(request()->query(), ['sort' => 'price_high'])) }}"
               class="text-decoration-none {{ request('sort') === 'price_high' ? 'fw-bold text-infinity-blue' : 'text-muted' }}">
                Prix décroissant
            </a>
        </div>
    </div>
</section>

<!-- Packs Grid -->
<section class="section">
    <div class="container">
        @if($packs->count() > 0)
        <div class="row g-4">
            @foreach($packs as $pack)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('packs.show', $pack->slug) }}" class="text-decoration-none">
                    <div class="card h-100 service-card">
                        <!-- Pack Header -->
                        <div class="card-header" style="background: linear-gradient(135deg,
                            {{ $pack->category === 'crypto' ? 'var(--infinity-blue), var(--blue-light)' : '' }}
                            {{ $pack->category === 'ia' ? 'var(--pink-medium), var(--pink-light)' : '' }}
                            {{ $pack->category === 'marketing' ? 'var(--green-service), #7dd3a6' : '' }}
                            {{ $pack->category === 'business' ? 'var(--warning-service), #f0d685' : '' }}
                        ); color: white;">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">
                                    {{ $pack->category === 'crypto' ? '₿' : '' }}
                                    {{ $pack->category === 'ia' ? '🤖' : '' }}
                                    {{ $pack->category === 'marketing' ? '📈' : '' }}
                                    {{ $pack->category === 'business' ? '💼' : '' }}
                                    {{ ucfirst($pack->category) }}
                                </h5>
                                @if($pack->is_featured)
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-star"></i> Premium
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Pack Body -->
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-3">{{ $pack->name }}</h5>
                            <p class="card-text text-muted small mb-3" style="min-height: 48px;">
                                {{ Str::limit($pack->tagline, 90) }}
                            </p>

                            <!-- Stats -->
                            <div class="d-flex justify-content-between text-muted small mb-3">
                                <span><i class="fas fa-layer-group me-1"></i> {{ $pack->workflows_count }} workflows</span>
                                <span><i class="fas fa-eye me-1"></i> {{ number_format($pack->views_count) }}</span>
                            </div>

                            <!-- Complexity Badge -->
                            @if($pack->complexity)
                            <div class="mb-3">
                                <span class="badge
                                    {{ $pack->complexity === 'Débutant' ? 'bg-success' : '' }}
                                    {{ $pack->complexity === 'Intermédiaire' ? 'bg-warning text-dark' : '' }}
                                    {{ $pack->complexity === 'Avancé' ? 'bg-danger' : '' }}
                                ">
                                    {{ $pack->complexity }}
                                </span>
                            </div>
                            @endif

                            <!-- Price -->
                            <div class="d-flex justify-content-between align-items-end pt-3 border-top">
                                <div>
                                    @if($pack->original_price_eur && $pack->getDiscountPercentage() > 0)
                                    <div class="mb-1">
                                        <small class="text-muted text-decoration-line-through">
                                            {{ number_format($pack->original_price_eur, 0) }}€
                                        </small>
                                        <span class="badge bg-success ms-1 small">
                                            -{{ $pack->getDiscountPercentage() }}%
                                        </span>
                                    </div>
                                    @endif
                                    <h3 class="mb-0 text-infinity-blue fw-bold">
                                        {{ number_format($pack->price_eur, 0) }}€
                                    </h3>
                                </div>
                                <span class="btn btn-sm btn-primary">
                                    Voir <i class="fas fa-arrow-right ms-1"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-5 d-flex justify-content-center">
            {{ $packs->links() }}
        </div>

        @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-search fa-4x text-muted"></i>
            </div>
            <h3 class="fw-bold mb-2">Aucun pack trouvé</h3>
            <p class="text-muted">Essayez de modifier vos filtres</p>
            <a href="{{ route('packs.index') }}" class="btn btn-primary mt-3">
                Voir tous les packs
            </a>
        </div>
        @endif
    </div>
</section>

<!-- Stats Section -->
<section class="section bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="mb-2">
                    <i class="fas fa-layer-group fa-2x text-infinity-blue"></i>
                </div>
                <h3 class="fw-bold mb-1">34</h3>
                <p class="text-muted mb-0">Packs Premium</p>
            </div>
            <div class="col-md-3">
                <div class="mb-2">
                    <i class="fas fa-project-diagram fa-2x text-pink-medium"></i>
                </div>
                <h3 class="fw-bold mb-1">580+</h3>
                <p class="text-muted mb-0">Workflows Totaux</p>
            </div>
            <div class="col-md-3">
                <div class="mb-2">
                    <i class="fas fa-users fa-2x text-green-service"></i>
                </div>
                <h3 class="fw-bold mb-1">300+</h3>
                <p class="text-muted mb-0">Clients Satisfaits</p>
            </div>
            <div class="col-md-3">
                <div class="mb-2">
                    <i class="fas fa-star fa-2x text-warning-service"></i>
                </div>
                <h3 class="fw-bold mb-1">4.9/5</h3>
                <p class="text-muted mb-0">Note Moyenne</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section text-white" style="background-color: var(--infinity-blue);">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-3">
            Besoin d'un Pack Personnalisé ?
        </h2>
        <p class="lead mb-4">
            Contactez-nous pour créer un pack sur mesure adapté à vos besoins spécifiques
        </p>
        <a href="{{ route('contact') }}" class="btn btn-light btn-lg">
            <i class="fas fa-envelope me-2"></i> Nous Contacter
        </a>
    </div>
</section>

@endsection

@push('styles')
<style>
    .card-header {
        border-bottom: none;
        padding: 1rem 1.25rem;
    }

    .service-card {
        transition: all 0.3s ease;
        border: none;
    }

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }

    .pagination {
        gap: 0.5rem;
    }

    .pagination .page-link {
        border-radius: 0.5rem;
        border: 1px solid var(--infinity-blue);
        color: var(--infinity-blue);
    }

    .pagination .page-item.active .page-link {
        background-color: var(--infinity-blue);
        border-color: var(--infinity-blue);
    }
</style>
@endpush
