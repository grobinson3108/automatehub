@extends('layouts.app')

@section('title', 'Accueil - AutomateHub')
@section('description', 'Apprenez n8n avec mes workflows créés par Claude IA. Tutoriels vidéo en français pour automatiser votre business.')

@section('content')
<!-- Hero Section -->
<section class="hero hero-with-bg">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-6 mb-5 mb-lg-0 animate-fade-in">
                <h1 class="display-4 fw-bold mb-4">
                    Je Crée Vos Workflows n8n avec l'IA
                </h1>
                <p class="lead mb-4">
                    Chaque semaine, je partage de nouveaux workflows créés avec l'IA. 
                    Des tutoriels vidéo en français pour automatiser votre business efficacement.
                </p>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <a href="/register" class="btn btn-light btn-lg">
                        <i class="fas fa-rocket me-2"></i> Commencer Gratuitement
                    </a>
                    <a href="/tutorials" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-play-circle me-2"></i> Voir les Workflows
                    </a>
                </div>
                <div class="d-flex align-items-center gap-4 text-white-50">
                    <div><i class="fas fa-users me-1"></i> <strong>322+</strong> abonnés</div>
                    <div><i class="fas fa-video me-1"></i> <strong>16</strong> vidéos</div>
                    <div><i class="fas fa-eye me-1"></i> <strong>5.2k</strong> vues</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="position-relative" x-data>
                    <img src="/oneui/media/various/promo-code.png" 
                         alt="n8n workflows" 
                         class="img-fluid rounded shadow-lg"
                         style="animation: float 3s ease-in-out infinite;">
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-warning text-dark p-2">
                            <i class="fas fa-star me-1"></i> Nouveau
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5 animate-fade-in">
            <h2 class="display-5 fw-bold mb-3">Ce que je vous propose</h2>
            <p class="lead text-muted">
                Ma mission : démocratiser n8n en France avec des contenus de qualité
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle p-3" 
                             style="width: 80px; height: 80px; background-color: rgba(107, 163, 195, 0.1);">
                            <i class="fas fa-robot fa-2x text-primary"></i>
                        </div>
                    </div>
                    <h5>Workflows IA</h5>
                    <p class="text-muted">
                        Je crée chaque workflow avec l'IA et les teste personnellement
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle p-3" 
                             style="width: 80px; height: 80px; background-color: rgba(232, 180, 180, 0.1);">
                            <i class="fas fa-video fa-2x text-secondary"></i>
                        </div>
                    </div>
                    <h5>Tutoriels Vidéo</h5>
                    <p class="text-muted">
                        J'explique chaque workflow en détail (16 vidéos déjà publiées !)
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle p-3" 
                             style="width: 80px; height: 80px; background-color: rgba(84, 180, 141, 0.1);">
                            <i class="fas fa-download fa-2x text-success"></i>
                        </div>
                    </div>
                    <h5>Import Direct</h5>
                    <p class="text-muted">
                        Fichiers JSON prêts à importer dans votre instance n8n
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle p-3" 
                             style="width: 80px; height: 80px; background-color: rgba(233, 196, 106, 0.1);">
                            <i class="fas fa-bullseye fa-2x text-warning"></i>
                        </div>
                    </div>
                    <h5>Vision Future</h5>
                    <p class="text-muted">
                        Je bâtis LA référence française pour apprendre n8n
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Explorez les catégories</h2>
            <p class="lead text-muted">
                Plus de 50 workflows organisés par thématique
            </p>
        </div>

        <div class="row g-4 mb-4">
            @php
            $categories = [
                ['icon' => '✉️', 'name' => 'Email', 'count' => 12],
                ['icon' => '👥', 'name' => 'CRM', 'count' => 8],
                ['icon' => '🛒', 'name' => 'E-commerce', 'count' => 15],
                ['icon' => '🔌', 'name' => 'API', 'count' => 10, 'locked' => true],
                ['icon' => '💬', 'name' => 'Slack', 'count' => 6, 'locked' => true],
                ['icon' => '📊', 'name' => 'Analytics', 'count' => 9, 'locked' => true],
            ];
            @endphp

            @foreach($categories as $index => $category)
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card text-center {{ isset($category['locked']) ? 'opacity-50' : '' }}">
                    <div class="card-body p-3">
                        <div class="fs-1 mb-2">{{ $category['icon'] }}</div>
                        <h6 class="mb-1">{{ $category['name'] }}</h6>
                        <p class="text-muted small mb-0">{{ $category['count'] }} workflows</p>
                        @if(isset($category['locked']))
                        <i class="fas fa-lock text-warning mt-2"></i>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center">
            <p class="text-muted mb-3">
                <i class="fas fa-exclamation-circle text-warning me-2"></i>
                Plus de 10 catégories disponibles après inscription
            </p>
            <a href="/register" class="btn btn-primary">
                <i class="fas fa-unlock me-2"></i> Voir toutes les catégories
            </a>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Comment ça marche ?</h2>
            <p class="lead text-muted">
                3 étapes simples pour automatiser votre business
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="mb-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px; background-color: var(--primary-color); color: white;">
                        <span class="fs-2 fw-bold">1</span>
                    </div>
                </div>
                <h4>Inscrivez-vous</h4>
                <p class="text-muted">
                    Créez votre compte gratuit et accédez immédiatement aux workflows
                </p>
            </div>

            <div class="col-md-4 text-center">
                <div class="mb-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px; background-color: var(--secondary-color); color: white;">
                        <span class="fs-2 fw-bold">2</span>
                    </div>
                </div>
                <h4>Choisissez</h4>
                <p class="text-muted">
                    Parcourez la bibliothèque et sélectionnez les workflows adaptés
                </p>
            </div>

            <div class="col-md-4 text-center">
                <div class="mb-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px; background-color: var(--success-color); color: white;">
                        <span class="fs-2 fw-bold">3</span>
                    </div>
                </div>
                <h4>Automatisez</h4>
                <p class="text-muted">
                    Importez le JSON et suivez le tutoriel vidéo pour l'implémenter
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section text-white" style="background-color: var(--primary-color);">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-3">
            Prêt à automatiser votre business ?
        </h2>
        <p class="lead mb-4">
            Je lance AutomateHub pour partager mes créations et aider la communauté francophone
        </p>
        <a href="/register" class="btn btn-light btn-lg">
            <i class="fas fa-arrow-right me-2"></i> Créer mon compte gratuit
        </a>
    </div>
</section>

@endsection

@push('styles')
<style>
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
        100% { transform: translateY(0px); }
    }
</style>
@endpush