@extends('layouts.frontend')

@section('content')
<!-- Hero Section -->
<div class="bg-primary">
    <div class="bg-black-25">
        <div class="content content-full text-center py-6">
            <h1 class="h2 text-white mb-2">Bibliothèque de Workflows n8n</h1>
            <p class="text-white-75 mb-0">
                Découvrez et téléchargez des workflows prêts à l'emploi pour automatiser vos processus
            </p>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="bg-body-light">
    <div class="content content-boxed py-3">
        <form method="GET" action="{{ route('workflows.index') }}" class="row g-3">
            <div class="col-md-4">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="">Toutes les catégories</option>
                    <option value="fondamentaux" {{ request('category') == 'fondamentaux' ? 'selected' : '' }}>M1 - Fondamentaux</option>
                    <option value="integrations" {{ request('category') == 'integrations' ? 'selected' : '' }}>M2 - Intégrations</option>
                    <option value="business" {{ request('category') == 'business' ? 'selected' : '' }}>M3 - Business</option>
                    <option value="avance" {{ request('category') == 'avance' ? 'selected' : '' }}>M4 - Avancé</option>
                    <option value="premium" {{ request('category') == 'premium' ? 'selected' : '' }}>M5 - Premium</option>
                    <option value="masterclass" {{ request('category') == 'masterclass' ? 'selected' : '' }}>MasterClass</option>
                </select>
            </div>
            <div class="col-md-4">
                <select name="access" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous les accès</option>
                    <option value="freemium" {{ request('access') == 'freemium' ? 'selected' : '' }}>Gratuit</option>
                    <option value="premium" {{ request('access') == 'premium' ? 'selected' : '' }}>Premium</option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Rechercher un workflow..." 
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Workflows Grid -->
<div class="content content-boxed">
    @auth
        @if(!auth()->user()->onboarding_completed)
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="fa fa-info-circle fa-2x me-3"></i>
                <div>
                    <p class="mb-0">
                        <strong>Bienvenue sur AutomateHub !</strong> 
                        Complétez votre profil pour débloquer toutes les fonctionnalités.
                        <a href="{{ route('onboarding.welcome') }}" class="alert-link">Commencer →</a>
                    </p>
                </div>
            </div>
        @endif
    @endauth

    @guest
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="fa fa-lock fa-2x me-3"></i>
            <div>
                <p class="mb-0">
                    <strong>Connectez-vous pour télécharger les workflows</strong><br>
                    <a href="{{ route('login') }}" class="alert-link">Se connecter</a> ou 
                    <a href="{{ route('register') }}" class="alert-link">créer un compte gratuit</a>
                </p>
            </div>
        </div>
    @endguest

    <div class="row items-push">
        @forelse($workflows as $workflow)
            <div class="col-md-6 col-lg-4">
                <div class="block block-rounded block-link-shadow h-100 mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title text-truncate" title="{{ $workflow->name }}">
                            {{ Str::limit($workflow->name, 40) }}
                        </h3>
                        <div class="block-options">
                            @if($workflow->is_premium)
                                <span class="badge bg-warning">
                                    <i class="fa fa-star"></i> Premium
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="fa fa-check"></i> Gratuit
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="block-content">
                        <p class="text-muted mb-3">
                            {{ $workflow->description ?? 'Workflow d\'automatisation n8n' }}
                        </p>
                        
                        <div class="mb-3">
                            @if($workflow->tags && count($workflow->tags) > 0)
                                @foreach(array_slice($workflow->tags, 0, 3) as $tag)
                                    <span class="badge bg-body-light text-body me-1">#{{ $tag }}</span>
                                @endforeach
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fa fa-download"></i> {{ $workflow->download_count ?? 0 }} téléchargements
                            </small>
                            <small class="text-muted">
                                <i class="fa fa-cube"></i> {{ $workflow->node_count ?? 0 }} nodes
                            </small>
                        </div>
                    </div>
                    <div class="block-content block-content-full bg-body-light">
                        <div class="d-grid">
                            <a href="{{ route('workflows.show', $workflow) }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-eye me-1"></i> Voir le workflow
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fa fa-info-circle fa-2x mb-3"></i>
                    <p class="mb-0">Aucun workflow trouvé avec ces critères.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $workflows->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit on category/access change is already handled inline
</script>
@endpush