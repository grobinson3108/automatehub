@extends('layouts.app')

@section('title', 'Workflows n8n - AutomateHub')

@section('content')
<!-- Hero Section -->
<section class="hero hero-with-bg py-5">
    <div class="hero-overlay"></div>
    <div class="container hero-content text-center text-white">
        <h1 class="display-4 fw-bold mb-3">Bibliothèque de Workflows n8n</h1>
        <p class="lead">Plus de 50 workflows créés avec l'IA et testés personnellement</p>
        <div class="mt-4">
            <span class="badge bg-primary me-2 px-3 py-2"><i class="fas fa-robot me-1"></i> Créés par IA</span>
            <span class="badge bg-success me-2 px-3 py-2"><i class="fas fa-play me-1"></i> Avec vidéos explicatives</span>
            <span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-trophy me-1"></i> Système de badges</span>
        </div>
    </div>
</section>

<!-- Filters Section -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <input type="text" class="form-control form-control-lg" placeholder="Rechercher : chatbot, email, CRM, reporting...">
            </div>
            <div class="col-md-4">
                <select class="form-select form-select-lg">
                    <option>Toutes les catégories</option>
                    <option>E-commerce (15)</option>
                    <option>Email Marketing (12)</option>
                    <option>CRM & Lead Gen (10)</option>
                    <option>SaaS Automation (8)</option>
                    <option>Social Media (6)</option>
                    <option>Analytics & Reports (9)</option>
                    <option>API Integrations (7)</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Tutorials Grid -->
<section class="section">
    <div class="container">
        <div class="row g-4">
            @php
            $tutorials = [
                [
                    'title' => 'Automatisation Email Marketing',
                    'description' => 'Workflow IA : Séquences email automatisées avec segmentation comportementale',
                    'level' => 'Débutant',
                    'duration' => '15 min',
                    'category' => 'Email Marketing',
                    'badge' => 'GRATUIT',
                    'badge_color' => 'success',
                    'badges_earned' => ['Email Rookie'],
                    'views' => 1240
                ],
                [
                    'title' => 'Shopify → CRM → Email Auto',
                    'description' => 'Workflow IA : Synchronisation Shopify vers HubSpot avec nurturing automatique',
                    'level' => 'Intermédiaire', 
                    'duration' => '28 min',
                    'category' => 'E-commerce',
                    'badge' => 'PREMIUM',
                    'badge_color' => 'warning',
                    'locked' => true,
                    'badges_earned' => ['E-commerce Master', 'Integration Expert'],
                    'views' => 890
                ],
                [
                    'title' => 'SaaS Onboarding Intelligent',
                    'description' => 'Workflow IA : Parcours d\'onboarding adaptatif avec scoring utilisateur IA',
                    'level' => 'Expert',
                    'duration' => '42 min',
                    'category' => 'SaaS Automation',
                    'badge' => 'BUSINESS',
                    'badge_color' => 'danger',
                    'locked' => true,
                    'badges_earned' => ['SaaS Wizard', 'AI Specialist', 'Retention Master'],
                    'views' => 445
                ],
                [
                    'title' => 'Lead Scoring IA Avancé',
                    'description' => 'Workflow IA : Scoring multi-canal avec prédiction d\'achat et routing intelligent',
                    'level' => 'Expert',
                    'duration' => '38 min',
                    'category' => 'CRM & Lead Gen',
                    'badge' => 'BUSINESS',
                    'badge_color' => 'danger',
                    'locked' => true,
                    'badges_earned' => ['Lead Master', 'AI Specialist', 'Sales Booster'],
                    'views' => 523
                ],
                [
                    'title' => 'Analytics Cross-Platform',
                    'description' => 'Workflow IA : Dashboard unifie Facebook, Google, LinkedIn + prédictions ROI',
                    'level' => 'Intermédiaire',
                    'duration' => '32 min',
                    'category' => 'Analytics & Reports',
                    'badge' => 'PREMIUM',
                    'badge_color' => 'warning',
                    'locked' => true,
                    'badges_earned' => ['Analytics Pro', 'Data Ninja'],
                    'views' => 672
                ],
                [
                    'title' => 'WhatsApp Business API',
                    'description' => 'Workflow IA : Notifications personnalisées et chatbot WhatsApp pour e-commerce',
                    'level' => 'Débutant',
                    'duration' => '22 min',
                    'category' => 'Social Media',
                    'badge' => 'GRATUIT',
                    'badge_color' => 'success',
                    'badges_earned' => ['Social Expert'],
                    'views' => 1156
                ]
            ];
            @endphp

            @foreach($tutorials as $index => $tutorial)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 {{ isset($tutorial['locked']) ? 'position-relative overflow-hidden' : '' }}">
                    @if(isset($tutorial['locked']))
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-black bg-opacity-10" style="z-index: 1;"></div>
                    @endif
                    
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">{{ $tutorial['title'] }}</h5>
                            <small class="text-muted"><i class="fas fa-robot me-1"></i> {{ $tutorial['category'] }}</small>
                        </div>
                        <span class="badge bg-{{ $tutorial['badge_color'] }}">{{ $tutorial['badge'] }}</span>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">{{ $tutorial['description'] }}</p>
                        
                        <!-- Badges Gamification -->
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Badges à débloquer :</small>
                            @foreach($tutorial['badges_earned'] as $badge)
                            <span class="badge bg-light text-dark border me-1 mb-1">
                                <i class="fas fa-trophy text-warning me-1"></i>{{ $badge }}
                            </span>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-between text-muted small mb-3">
                            <span><i class="fas fa-signal me-1"></i> {{ $tutorial['level'] }}</span>
                            <span><i class="fas fa-clock me-1"></i> {{ $tutorial['duration'] }}</span>
                            <span><i class="fas fa-eye me-1"></i> {{ $tutorial['views'] }} vues</span>
                        </div>
                        
                        @if(isset($tutorial['locked']))
                        <div class="position-absolute top-50 start-50 translate-middle text-center" style="z-index: 2;">
                            <i class="fas fa-lock fa-2x text-{{ $tutorial['badge_color'] }} mb-2"></i>
                            <p class="fw-semibold mb-2">Contenu {{ $tutorial['badge'] }}</p>
                            <a href="/register" class="btn btn-sm btn-{{ $tutorial['badge_color'] }}">
                                Débloquer
                            </a>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        @if($index === 0)
                        <a href="/register" class="btn btn-primary w-100">
                            <i class="fas fa-play me-2"></i> Découvrir ce workflow
                        </a>
                        @else
                        <a href="/register" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-lock me-2"></i> Débloquer l'accès
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Add more tutorial cards... -->
            @for($i = 0; $i < 6; $i++)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 opacity-50">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Plus de workflows disponibles après inscription</p>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-gradient" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
    <div class="container text-center text-white">
        <h2 class="display-5 fw-bold mb-3">
            <i class="fas fa-robot text-warning me-2"></i>
            67 workflows IA + vidéos explicatives
        </h2>
        <p class="lead mb-4">
            J'ai créé <strong>67 workflows avec l'IA</strong> et je publie <strong>4 nouveaux workflows/semaine</strong> avec tutoriels vidéo complets.
        </p>
        <div class="row text-center mb-4">
            <div class="col-md-3">
                <div class="h4 mb-1"><i class="fas fa-robot text-warning"></i> 67</div>
                <small>Workflows IA</small>
            </div>
            <div class="col-md-3">
                <div class="h4 mb-1"><i class="fas fa-play text-info"></i> 36</div>
                <small>Vidéos tutoriels</small>
            </div>
            <div class="col-md-3">
                <div class="h4 mb-1"><i class="fas fa-trophy text-success"></i> 28</div>
                <small>Badges disponibles</small>
            </div>
            <div class="col-md-3">
                <div class="h4 mb-1"><i class="fas fa-users text-primary"></i> 322</div>
                <small>Abonnés YouTube</small>
            </div>
        </div>
        <a href="/register" class="btn btn-light btn-lg me-2">
            <i class="fas fa-rocket me-2"></i> Accès gratuit immédiat
        </a>
        <a href="/pricing" class="btn btn-outline-light btn-lg">
            <i class="fas fa-crown me-2"></i> Voir les offres Premium
        </a>
    </div>
</section>
@endsection