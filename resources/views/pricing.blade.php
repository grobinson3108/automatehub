@extends('layouts.app')

@section('title', 'Tarifs - AutomateHub')

@section('content')
<section class="hero hero-with-bg py-5">
    <div class="hero-overlay"></div>
    <div class="container hero-content text-center text-white">
        <h1 class="display-4 fw-bold mb-3">Tarifs et possibilités d'inscription</h1>
        <p class="lead">3 formules pour maîtriser n8n avec l'intelligence artificielle</p>
        <div class="mt-4">
            <span class="badge bg-primary me-2 px-3 py-2"><i class="fas fa-robot me-1"></i> Workflows Téléchargeables</span>
            <span class="badge bg-success me-2 px-3 py-2"><i class="fas fa-graduation-cap me-1"></i> Vidéos Tutoriels</span>
            <span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-crown me-1"></i> Communauté gamifiée</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <!-- Freemium -->
            <div class="col-lg-4">
                <div class="pricing-card h-100" data-plan="freemium">
                    <div class="pricing-header" style="background: linear-gradient(120deg, #10B981, #059669);">
                        <div class="pricing-icon">
                            <i class="fas fa-play"></i>
                        </div>
                        <h3 class="text-white mb-2">Freemium</h3>
                        <p class="text-white-75 mb-0">Inscription gratuite</p>
                    </div>
                    <div class="pricing-body">
                        <div class="pricing-price mb-4">
                            <span class="price-currency">0€</span>
                            <span class="price-period">/ mois</span>
                        </div>
                        
                        <div class="pricing-highlight mb-4">
                            <i class="fas fa-gift me-2"></i>
                            <strong>Accès Workflows Gratuits</strong>
                        </div>
                        
                        <ul class="pricing-features">
                            <li><i class="fas fa-check"></i> Accès aux Workflows gratuits</li>
                            <li><i class="fas fa-check"></i> Accès vidéos YouTube gratuites</li>
                            <li><i class="fas fa-check"></i> Communauté active (posts, questions)</li>
                            <li><i class="fas fa-check"></i> Quiz n8n niveau</li>
                            <li><i class="fas fa-check"></i> AutomateHub Académie</li>
                            <li class="disabled"><i class="fas fa-times"></i> Workflows Premium</li>
                            <li class="disabled"><i class="fas fa-times"></i> Création avec IA</li>
                        </ul>
                        
                        <div class="pricing-action">
                            <a href="/register" class="btn btn-outline-success btn-lg w-100">
                                <i class="fas fa-rocket me-2"></i>Commencer Gratuit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Premium -->
            <div class="col-lg-4">
                <div class="pricing-card h-100 featured" data-plan="premium">
                    <div class="pricing-badge mt-4">
                        <span class="badge bg-warning text-dark">RECOMMANDÉ</span>
                    </div>
                    <div class="pricing-header" style="background: linear-gradient(120deg, #3B82F6, #1D4ED8);">
                        <div class="pricing-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="text-white mb-2">Premium</h3>
                        <p class="text-white-75 mb-0">Full Accès Illimité</p>
                    </div>
                    <div class="pricing-body">
                        <div class="pricing-price mb-4">
                            <span class="price-currency">39€</span>
                            <span class="price-period">/ mois</span>
                        </div>
                        
                        <div class="pricing-highlight mb-4">
                            <i class="fas fa-robot me-2"></i>
                            <strong>Accès Workflows Premiums</strong>
                        </div>
                        
                        <ul class="pricing-features">
                            <li><i class="fas fa-check"></i> Tous les workflows Freemium</li>
                            <li><i class="fas fa-check"></i> <strong>Accès aux Workflows Premiums</strong></li>
                            <li><i class="fas fa-check"></i> Vidéos tutoriels premium (36 vidéos)</li>
                            <li><i class="fas fa-check"></i> Communauté active (posts, questions)</li>
                            <li><i class="fas fa-check"></i> 20 badges de progression</li>
                            <li><i class="fas fa-check"></i> Téléchargements illimités</li>
                        </ul>
                        
                        <div class="pricing-action">
                            <a href="/register" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-crown me-2"></i>Choisir Premium
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Business -->
            <div class="col-lg-4">
                <div class="pricing-card h-100" data-plan="business">
                    <div class="pricing-header" style="background: linear-gradient(120deg, #8B5CF6, #7C3AED);">
                        <div class="pricing-icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <h3 class="text-white mb-2">Business</h3>
                        <p class="text-white-75 mb-0">Crééez vos Workflow avec IA</p>
                    </div>
                    <div class="pricing-body">
                        <div class="pricing-price mb-4">
                            <span class="price-currency">97€</span>
                            <span class="price-period">/ mois</span>
                        </div>
                        
                        <div class="pricing-highlight mb-4">
                            <i class="fas fa-magic me-2"></i>
                            <strong>Créez des Workflows avec IA</strong>
                        </div>
                        
                        <ul class="pricing-features">
                            <li><i class="fas fa-check"></i> Tout le contenu Premium</li>
                            <li><i class="fas fa-check"></i> <strong>Création de vos workflows avec IA</strong></li>
                            <li><i class="fas fa-check"></i> <strong>Stratégies d'automatisation avec IA</strong></li>
                            <li><i class="fas fa-check"></i> Accès prioritaire nouveautés</li>
                            <li><i class="fas fa-check"></i> Badge Business exclusif</li>
                        </ul>
                        
                        <div class="pricing-action">
                            <a href="/register" class="btn btn-outline-purple btn-lg w-100">
                                <i class="fas fa-rocket me-2"></i>Choisir Business
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Avantages IA Section -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">
                <i class="fas fa-robot text-primary me-2"></i>
                Pourquoi mes workflows IA sont différents ?
            </h2>
            <p class="lead text-muted">
                Je ne copie pas, je crée. Chaque workflow est conçu avec IA puis testé dans de vrais projets.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-brain fa-3x text-primary"></i>
                    </div>
                    <h5>Intelligence Augmentée</h5>
                    <p class="text-muted">
                        Mon IA analyse votre besoin et conçoit l'architecture optimale
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-flask fa-3x text-success"></i>
                    </div>
                    <h5>Testé en Réel</h5>
                    <p class="text-muted">
                        Chaque workflow est implémenté et validé dans mes propres projets
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-rocket fa-3x text-warning"></i>
                    </div>
                    <h5>Performance Optimisée</h5>
                    <p class="text-muted">
                        Logique optimisée par IA pour réduire les erreurs et la consommation
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x text-info"></i>
                    </div>
                    <h5>Communauté Active</h5>
                    <p class="text-muted">
                        Testez et dites ce que vous en pensez. Améliorons le tout ensemble.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Comparaison Section -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">
                <i class="fas fa-balance-scale text-warning me-2"></i>
                AutomateHub vs Concurrents
            </h2>
            <p class="lead text-muted">
                Pourquoi choisir AutomateHub plutôt que les autres plateformes ?
            </p>
        </div>

        <div class="row g-4">
            <!-- AutomateHub Card -->
            <div class="col-lg-3">
                <div class="comparison-card winner">
                    <div class="comparison-header">
                        <div class="platform-badge bg-primary">
                            <i class="fas fa-crown"></i>
                        </div>
                        <h4 class="text-primary fw-bold">AutomateHub</h4>
                        <p class="text-muted small">Workflows IA + Communauté</p>
                    </div>
                    <div class="comparison-body">
                        <div class="comparison-price">
                            <span class="price">39-97€</span>
                            <span class="period">/mois</span>
                        </div>
                        <ul class="comparison-features">
                            <li class="feature-item">
                                <i class="fas fa-check text-success"></i>
                                <span>Workflows Claude IA</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check text-success"></i>
                                <span>Communauté gamifiée</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check text-success"></i>
                                <span>Création sur-mesure</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check text-success"></i>
                                <span>Testés en production</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check text-success"></i>
                                <span>Support français</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Skool Communities -->
            <div class="col-lg-3">
                <div class="comparison-card">
                    <div class="comparison-header">
                        <div class="platform-badge bg-orange">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Skool</h4>
                        <p class="text-muted small">Communautés privées</p>
                    </div>
                    <div class="comparison-body">
                        <div class="comparison-price">
                            <span class="price">50-200€</span>
                            <span class="period">/mois</span>
                        </div>
                        <ul class="comparison-features">
                            <li class="feature-item">
                                <i class="fas fa-times text-danger"></i>
                                <span>Workflows IA créés</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check text-success"></i>
                                <span>Communauté</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-question text-warning"></i>
                                <span>Création sur-mesure</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-question text-warning"></i>
                                <span>Workflows testés</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-times text-danger"></i>
                                <span>Support en français</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Udemy -->
            <div class="col-lg-3">
                <div class="comparison-card">
                    <div class="comparison-header">
                        <div class="platform-badge bg-purple">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <h4>Udemy</h4>
                        <p class="text-muted small">Cours en ligne</p>
                    </div>
                    <div class="comparison-body">
                        <div class="comparison-price">
                            <span class="price">50-200€</span>
                            <span class="period">unique</span>
                        </div>
                        <ul class="comparison-features">
                            <li class="feature-item">
                                <i class="fas fa-times text-danger"></i>
                                <span>Workflows IA créés</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-times text-danger"></i>
                                <span>Communauté active</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-times text-danger"></i>
                                <span>Création sur-mesure</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-question text-warning"></i>
                                <span>Workflows testés</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-times text-danger"></i>
                                <span>Support continu</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- YouTube Gratuit -->
            <div class="col-lg-3">
                <div class="comparison-card">
                    <div class="comparison-header">
                        <div class="platform-badge bg-red">
                            <i class="fab fa-youtube"></i>
                        </div>
                        <h4>YouTube</h4>
                        <p class="text-muted small">Tutoriels gratuits</p>
                    </div>
                    <div class="comparison-body">
                        <div class="comparison-price">
                            <span class="price">0€</span>
                            <span class="period">gratuit</span>
                        </div>
                        <ul class="comparison-features">
                            <li class="feature-item">
                                <i class="fas fa-times text-danger"></i>
                                <span>Workflows IA créés</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-times text-danger"></i>
                                <span>Communauté structurée</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-times text-danger"></i>
                                <span>Création sur-mesure</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-times text-danger"></i>
                                <span>Workflows téléchargeables</span>
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-times text-danger"></i>
                                <span>Support personnalisé</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Résumé comparatif -->
        <div class="row mt-5">
            <div class="col-lg-12 mx-auto">
                <div class="comparison-summary">
                    <h3 class="text-center mb-4">
                        <i class="fas fa-trophy text-warning me-2"></i>
                        Pourquoi AutomateHub ?
                    </h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="summary-item">
                                <i class="fas fa-robot text-primary"></i>
                                <strong>Workflows avec IA uniques</strong> - Aucune autre plateforme française ne propose des workflows créés avec une IA avancée
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-item">
                                <i class="fas fa-balance-scale text-success"></i>
                                <strong>Rapport qualité/prix imbattable</strong> - Moins cher que Skool, plus complet qu'Udemy, plus structuré que YouTube
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-item">
                                <i class="fas fa-flag text-info"></i>
                                <strong>Plateforme 100% française</strong> - Communauté et contenu créés par et pour des francophones
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-item">
                                <i class="fas fa-magic text-warning"></i>
                                <strong>Création sur-mesure</strong> - Plan Business avec création de Workflows assistés par IA
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
/* Pricing Cards - Inspired by Audelalia */
.pricing-card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    position: relative;
    background: white;
}

.pricing-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

.pricing-card.featured {
    transform: scale(1.05);
    z-index: 2;
    border: 3px solid #3B82F6;
}

.pricing-card.featured:hover {
    transform: scale(1.05) translateY(-12px);
}

.pricing-header {
    padding: 2.5rem 2rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.pricing-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.1);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.pricing-card:hover .pricing-header::before {
    opacity: 1;
}

.pricing-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    transition: all 0.3s ease;
}

.pricing-card:hover .pricing-icon {
    transform: scale(1.1);
    background: rgba(255,255,255,0.3);
}

.pricing-body {
    padding: 2rem;
}

.pricing-price {
    text-align: center;
}

.price-currency {
    font-size: 3rem;
    font-weight: 700;
    color: var(--dark-text);
    line-height: 1;
}

.price-period {
    font-size: 1.1rem;
    color: var(--gray-text);
    margin-left: 0.5rem;
}

.price-old {
    font-size: 0.9rem;
    color: #dc3545;
    text-decoration: line-through;
    margin-top: 0.5rem;
}

.pricing-highlight {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(16, 185, 129, 0.1));
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
    color: #1D4ED8;
}

.pricing-features {
    list-style: none;
    padding: 0;
    margin: 0;
}

.pricing-features li {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    font-size: 0.95rem;
}

.pricing-features li:last-child {
    border-bottom: none;
}

.pricing-features li i {
    width: 20px;
    margin-right: 1rem;
    font-size: 0.9rem;
}

.pricing-features li .fa-check {
    color: #10B981;
}

.pricing-features li.disabled {
    color: #9CA3AF;
}

.pricing-features li.disabled .fa-times {
    color: #EF4444;
}

.pricing-action {
    margin-top: 2rem;
}

.pricing-badge {
    position: absolute;
    top: -10px;
    right: 20px;
    z-index: 3;
}

.pricing-badge .badge {
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Custom Button Styles */
.btn-outline-purple {
    color: #8B5CF6;
    border-color: #8B5CF6;
    background: transparent;
}

.btn-outline-purple:hover {
    background: #8B5CF6;
    border-color: #8B5CF6;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
}

.btn-outline-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

/* Responsive */
@media (max-width: 991px) {
    .pricing-card.featured {
        transform: none;
        margin-top: 2rem;
    }
    
    .pricing-card.featured:hover {
        transform: translateY(-12px);
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.pricing-card {
    animation: fadeInUp 0.6s ease-out;
}

.pricing-card:nth-child(2) {
    animation-delay: 0.1s;
}

.pricing-card:nth-child(3) {
    animation-delay: 0.2s;
}

/* Comparison Cards */
.comparison-card {
    background: white;
    border-radius: 12px;
    padding: 0;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 2px solid transparent;
    height: 100%;
}

.comparison-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.comparison-card.winner {
    border-color: #3B82F6;
    transform: scale(1.02);
    position: relative;
}

.comparison-card.winner::before {
    content: '✓ MEILLEUR CHOIX';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #3B82F6, #1D4ED8);
    color: white;
    text-align: center;
    padding: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    z-index: 2;
}

.comparison-card.winner:hover {
    transform: scale(1.02) translateY(-8px);
}

.comparison-header {
    padding: 2rem 1.5rem 1rem;
    text-align: center;
    border-bottom: 1px solid #f1f5f9;
}

.comparison-card.winner .comparison-header {
    padding-top: 3rem;
}

.platform-badge {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: white;
}

.bg-orange {
    background: linear-gradient(135deg, #FF6B35, #F7931E);
}

.bg-purple {
    background: linear-gradient(135deg, #8B5CF6, #7C3AED);
}

.bg-red {
    background: linear-gradient(135deg, #EF4444, #DC2626);
}

.comparison-header h4 {
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.comparison-body {
    padding: 1.5rem;
}

.comparison-price {
    text-align: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f1f5f9;
}

.comparison-price .price {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1f2937;
}

.comparison-price .period {
    font-size: 0.9rem;
    color: #6b7280;
    margin-left: 0.25rem;
}

.comparison-features {
    list-style: none;
    padding: 0;
    margin: 0;
}

.feature-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    font-size: 0.9rem;
    border-bottom: 1px solid #f9fafb;
}

.feature-item:last-child {
    border-bottom: none;
}

.feature-item i {
    width: 20px;
    margin-right: 0.75rem;
    font-size: 0.8rem;
}

.feature-item span {
    color: #374151;
}

/* Comparison Summary */
.comparison-summary {
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    border-radius: 16px;
    padding: 2.5rem;
    border: 1px solid #e2e8f0;
}

.summary-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    margin-bottom: 1rem;
}

.summary-item i {
    font-size: 1.25rem;
    margin-right: 1rem;
    margin-top: 0.25rem;
    flex-shrink: 0;
}

.summary-item strong {
    display: block;
    margin-bottom: 0.25rem;
    color: #1f2937;
}

/* Responsive */
@media (max-width: 991px) {
    .comparison-card.winner {
        transform: none;
        margin-top: 1rem;
    }
    
    .comparison-card.winner:hover {
        transform: translateY(-8px);
    }
    
    .comparison-summary {
        padding: 1.5rem;
    }
}
</style>
@endpush