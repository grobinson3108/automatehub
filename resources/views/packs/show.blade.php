@extends('layouts.app')

@section('title', $pack->marketing_title . ' - AutomateHub')
@section('description', $pack->tagline)

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                @if($pack->is_featured)
                <div class="mb-3">
                    <span class="badge bg-warning text-dark px-3 py-2">
                        <i class="fas fa-star me-1"></i> Pack Premium
                    </span>
                </div>
                @endif

                <h1 class="display-4 fw-bold mb-3">
                    {{ $pack->marketing_title }}
                </h1>

                <p class="lead text-muted mb-4">
                    {{ $pack->tagline }}
                </p>

                <!-- Pricing -->
                <div class="d-flex align-items-center gap-3 mb-4">
                    @if($pack->original_price_eur && $pack->getDiscountPercentage() > 0)
                    <span class="h4 text-muted text-decoration-line-through mb-0">
                        {{ number_format($pack->original_price_eur, 0) }}€
                    </span>
                    @endif
                    <span class="display-3 fw-bold text-infinity-blue mb-0" id="price-display">
                        {{ number_format($pack->price_eur, 0) }}€
                    </span>
                    @if($pack->getDiscountPercentage() > 0)
                    <span class="badge bg-success px-3 py-2 fs-6">
                        -{{ $pack->getDiscountPercentage() }}%
                    </span>
                    @endif
                </div>

                <!-- Currency Toggle -->
                <div class="btn-group mb-4" role="group">
                    <button type="button" class="btn btn-primary" id="btn-eur" onclick="switchCurrency('EUR')">
                        EUR (€)
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="btn-usd" onclick="switchCurrency('USD')">
                        USD ($)
                    </button>
                </div>

                <!-- CTA -->
                <div class="mb-4">
                    <form action="{{ route('packs.checkout', $pack->slug) }}" method="POST">
                        @csrf
                        <input type="hidden" name="currency" id="selected-currency" value="EUR">
                        <button type="submit" class="btn btn-primary btn-lg px-5 py-3">
                            <i class="fas fa-shopping-cart me-2"></i> Acheter Maintenant
                        </button>
                    </form>
                </div>

                <!-- Trust Badges -->
                <div class="d-flex flex-wrap gap-4 text-muted small">
                    <div><i class="fas fa-shield-alt text-green-service me-1"></i> Paiement Sécurisé</div>
                    <div><i class="fas fa-bolt text-warning-service me-1"></i> Livraison Immédiate</div>
                    <div><i class="fas fa-undo text-infinity-blue me-1"></i> Garantie 30 jours</div>
                </div>

                <!-- Scarcity -->
                <div class="alert alert-warning mt-3 d-inline-block">
                    <i class="fas fa-fire me-1"></i> Plus que <strong>{{ rand(3, 8) }} copies</strong> disponibles à ce prix
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="fas fa-robot fa-4x text-infinity-blue"></i>
                        </div>
                        <h3 class="fw-bold mb-3">{{ $pack->workflows_count }} Workflows Inclus</h3>
                        <p class="text-muted">Prêts à l'emploi, testés et optimisés pour votre business</p>

                        <div class="row mt-4">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-eye text-pink-medium fs-4"></i>
                                    <p class="mb-0 mt-2 small text-muted">{{ number_format($pack->views_count) }} vues</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded">
                                    <i class="fas fa-shopping-bag text-green-service fs-4"></i>
                                    <p class="mb-0 mt-2 small text-muted">{{ number_format($pack->sales_count) }} ventes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Workflows Section -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">
                <span class="text-infinity-blue">{{ $pack->workflows_count }} Workflows</span> Dans Ce Pack
            </h2>
            <p class="lead text-muted">Prêts à l'emploi et optimisés pour votre réussite</p>
        </div>

        <div class="row g-4">
            @foreach($workflows as $workflow)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 service-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title">{{ $workflow['name'] }}</h5>
                            <span class="badge
                                {{ $workflow['complexity'] === 'Simple' ? 'bg-success' : '' }}
                                {{ $workflow['complexity'] === 'Intermédiaire' ? 'bg-warning' : '' }}
                                {{ $workflow['complexity'] === 'Avancé' ? 'bg-danger' : '' }}
                            ">
                                {{ $workflow['complexity'] }}
                            </span>
                        </div>
                        <div class="d-flex gap-3 text-muted small">
                            <span><i class="fas fa-project-diagram me-1"></i> {{ $workflow['nodes'] }} nodes</span>
                            <span><i class="fas fa-link me-1"></i> {{ $workflow['connections'] }} connexions</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Features & Benefits -->
<section class="section bg-light">
    <div class="container">
        <div class="row g-5">
            <!-- Features -->
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">
                    <i class="fas fa-sparkles text-infinity-blue me-2"></i> Fonctionnalités
                </h2>
                <ul class="list-unstyled">
                    @foreach($pack->features ?? [] as $feature)
                    <li class="mb-3 d-flex align-items-start">
                        <div class="me-3">
                            <div class="bg-infinity-blue text-white rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px;">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <span>{{ $feature }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Benefits -->
            <div class="col-lg-6">
                <h2 class="h3 fw-bold mb-4">
                    <i class="fas fa-gift text-green-service me-2"></i> Avantages
                </h2>
                <ul class="list-unstyled">
                    @foreach($pack->benefits ?? [] as $benefit)
                    <li class="mb-3 d-flex align-items-start">
                        <div class="me-3">
                            <div class="bg-green-service text-white rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 32px; height: 32px;">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <span>{{ $benefit }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Requirements -->
@if($pack->requirements && count($pack->requirements) > 0)
<section class="section">
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="h4 fw-bold mb-4">
                    <i class="fas fa-tools text-pink-medium me-2"></i> Prérequis Techniques
                </h3>
                <div class="row">
                    @foreach($pack->requirements as $requirement)
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            <span>{{ $requirement }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- FAQ -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Questions Fréquentes</h2>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item mb-3 border-0 shadow-sm">
                        <h3 class="accordion-header">
                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Comment recevoir mes workflows après l'achat ?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Vous recevrez un email immédiatement après l'achat avec un lien de téléchargement sécurisé. Le lien est valide pendant 48h et vous permet 3 téléchargements maximum.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3 border-0 shadow-sm">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Comment installer les workflows dans n8n ?
                            </button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Chaque pack inclut un guide d'installation détaillé. Vous devez simplement importer les fichiers JSON dans votre instance n8n et configurer vos API keys.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3 border-0 shadow-sm">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Puis-je obtenir un remboursement ?
                            </button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Oui ! Nous offrons une garantie satisfait ou remboursé de 30 jours. Si les workflows ne correspondent pas à vos attentes, contactez-nous pour un remboursement complet.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3 border-0 shadow-sm">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Y a-t-il du support disponible ?
                            </button>
                        </h3>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Oui ! Rejoignez notre communauté Skool gratuite pour poser vos questions et obtenir de l'aide :
                                <a href="https://www.skool.com/audelalia-4222" class="text-infinity-blue fw-bold" target="_blank">
                                    skool.com/audelalia-4222
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Packs -->
@if($relatedPacks->count() > 0)
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h3 fw-bold">Ces Packs Pourraient Vous Intéresser</h2>
        </div>

        <div class="row g-4">
            @foreach($relatedPacks as $related)
            <div class="col-md-4">
                <a href="{{ route('packs.show', $related->slug) }}" class="text-decoration-none">
                    <div class="card h-100 service-card">
                        <div class="card-body">
                            <h5 class="card-title text-infinity-blue">{{ $related->name }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($related->tagline, 80) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="h4 mb-0 text-infinity-blue fw-bold">{{ number_format($related->price_eur, 0) }}€</span>
                                <span class="text-muted small">{{ $related->workflows_count }} workflows</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Final CTA -->
<section class="section text-white" style="background-color: var(--infinity-blue);">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-3">
            Prêt à Automatiser Votre Business ?
        </h2>
        <p class="lead mb-4">
            Rejoignez les centaines d'entrepreneurs qui automatisent déjà avec nos workflows
        </p>
        <form action="{{ route('packs.checkout', $pack->slug) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="currency" value="{{ $currency }}">
            <button type="submit" class="btn btn-light btn-lg px-5">
                <i class="fas fa-rocket me-2"></i>
                Acheter pour <span id="cta-price">{{ number_format($pack->price_eur, 0) }}€</span>
            </button>
        </form>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Currency switcher
    let currentCurrency = 'EUR';
    const prices = {
        EUR: {{ $pack->price_eur }},
        USD: {{ $pack->price_usd }}
    };

    function switchCurrency(currency) {
        currentCurrency = currency;
        document.getElementById('selected-currency').value = currency;

        const symbol = currency === 'USD' ? '$' : '€';
        const price = Math.floor(prices[currency]);

        document.getElementById('price-display').textContent = price + symbol;
        document.getElementById('cta-price').textContent = price + symbol;

        // Update button styles
        if (currency === 'EUR') {
            document.getElementById('btn-eur').className = 'btn btn-primary';
            document.getElementById('btn-usd').className = 'btn btn-outline-primary';
        } else {
            document.getElementById('btn-eur').className = 'btn btn-outline-primary';
            document.getElementById('btn-usd').className = 'btn btn-primary';
        }
    }
</script>
@endpush
