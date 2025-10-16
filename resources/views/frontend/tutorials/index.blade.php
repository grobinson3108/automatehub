@extends('layouts.frontend')

@section('content')

<style>
/* Styles forcés avec !important pour garantir qu'ils s'appliquent */
.bg-blue-service {
    background-color: #b4d8e8 !important;
}

.bg-pink-service {
    background-color: #f3d5d5 !important;
}

.bg-green-service {
    background-color: #54b48d !important;
}

.bg-danger-service {
    background-color: #e76f51 !important;
}

.bg-warning-service {
    background-color: #e9c46a !important;
}

.bg-blue-medium-service {
    background-color: #7a9cb5 !important;
}

.section-divider {
    height: 2px;
    background: linear-gradient(90deg, rgba(180, 216, 232, 0), rgba(106, 163, 195, 1), rgba(180, 216, 232, 0));
    margin: 80px 0;
}

.service-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    flex-shrink: 0;
}

.tutorial-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.tutorial-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.tutorial-header {
    padding: 30px 20px;
    text-align: center;
    color: white;
}

.tutorial-debutant .tutorial-header {
    background: linear-gradient(45deg, #b4d8e8, #6ba3c3);
}

.tutorial-intermediaire .tutorial-header {
    background: linear-gradient(45deg, #f3d5d5, #e8b4b4);
}

.tutorial-avance .tutorial-header {
    background: linear-gradient(45deg, #a9c2d9, #7a9cb5);
}

.tutorial-expert .tutorial-header {
    background: linear-gradient(45deg, #eca192, #e76f51);
}

.tutorial-specialise .tutorial-header {
    background: linear-gradient(45deg, #f2dfa0, #e9c46a);
}

.tutorial-header .icon-container {
    width: 80px;
    height: 80px;
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    margin: 0 auto 15px;
}

.tutorial-body {
    padding: 30px;
}

.level-included {
    display: inline-block;
    font-size: 14px;
    padding: 3px 15px;
    border-radius: 50px;
    margin-right: 5px;
    margin-bottom: 5px;
    background-color: rgba(106, 163, 195, 0.1);
    color: #6ba3c3;
    border: 1px solid #6ba3c3;
}

.benefit-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.benefit-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 15px;
    flex-shrink: 0;
}

.text-infinity-blue {
    color: #6ba3c3 !important;
}

.text-pink-medium {
    color: #e8b4b4 !important;
}

.text-green-service {
    color: #54b48d !important;
}
</style>

<!-- Hero Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-up">
                <h1 class="display-4 fw-bold text-infinity-blue mb-4">
                    Tutoriels<br>n8n complets
                </h1>
                <p class="lead mb-4">
                    Des tutoriels structurés et progressifs pour maîtriser n8n, de l'installation aux workflows avancés. Apprenez à votre rythme avec des exemples pratiques et des projets concrets.
                </p>
            </div>
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                <img src="{{ asset('images/logo/Logo_900.png') }}" alt="Tutoriels n8n Automatehub" class="img-fluid rounded-4 shadow">
            </div>
        </div>
    </div>
</section>

<!-- Introduction Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center" data-aos="fade-up">
                <h2 class="fw-bold text-infinity-blue mb-4">
                    Apprenez n8n étape par étape
                </h2>
                <p class="lead mb-5">
                    Nos tutoriels sont organisés par niveau de difficulté et par thématique pour vous permettre de progresser efficacement dans l'apprentissage de n8n et de l'automatisation.
                </p>
            </div>
        </div>
        
        <div class="row g-4 mb-5">
            <!-- Tutoriel 1 - Débutant -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card tutorial-card h-100 border-0 shadow-sm">
                    <div style="background: linear-gradient(120deg, #b4d8e8, #6ba3c3); border-radius: 12px 12px 0 0;" class="p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-seedling fa-3x" style="color: #6ba3c3;"></i>
                        </div>
                        <h3 class="h3 fw-bold text-white mb-1">Débutant</h3>
                        <p class="text-white mb-0 opacity-80">Premiers pas avec n8n</p>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-4">
                            Découvrez les bases de n8n : installation, interface, création de vos premiers workflows simples et concepts fondamentaux de l'automatisation.
                        </p>
                        <a href="#debutant" class="btn btn-outline-primary rounded-pill d-block">Voir les tutoriels</a>
                    </div>
                </div>
            </div>
            
            <!-- Tutoriel 2 - Intermédiaire -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card tutorial-card h-100 border-0 shadow-sm">
                    <div style="background: linear-gradient(120deg, #f3d5d5, #e8b4b4); border-radius: 12px 12px 0 0;" class="p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-cogs fa-3x" style="color: #e8b4b4;"></i>
                        </div>
                        <h3 class="h3 fw-bold text-white mb-1">Intermédiaire</h3>
                        <p class="text-white mb-0 opacity-80">Workflows plus complexes</p>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-4">
                            Approfondissez vos connaissances avec des workflows complexes, la gestion des erreurs et l'intégration avec des APIs externes.
                        </p>
                        <a href="#intermediaire" class="btn btn-outline-secondary rounded-pill d-block">Voir les tutoriels</a>
                    </div>
                </div>
            </div>
            
            <!-- Tutoriel 3 - Avancé -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card tutorial-card h-100 border-0 shadow-sm">
                    <div style="background: linear-gradient(120deg, #a9c2d9, #7a9cb5); border-radius: 12px 12px 0 0;" class="p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-rocket fa-3x" style="color: #7a9cb5;"></i>
                        </div>
                        <h3 class="h3 fw-bold text-white mb-1">Avancé</h3>
                        <p class="text-white mb-0 opacity-80">Optimisation et performance</p>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-4">
                            Maîtrisez les techniques avancées : optimisation des performances, sécurité, monitoring et déploiement en production.
                        </p>
                        <a href="#avance" class="btn btn-outline-info rounded-pill d-block">Voir les tutoriels</a>
                    </div>
                </div>
            </div>
            
            <!-- Tutoriel 4 - Expert -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
                <div class="card tutorial-card h-100 border-0 shadow-sm">
                    <div style="background: linear-gradient(120deg, #eca192, #e76f51); border-radius: 12px 12px 0 0;" class="p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-crown fa-3x" style="color: #e76f51;"></i>
                        </div>
                        <h3 class="h3 fw-bold text-white mb-1">Expert</h3>
                        <p class="text-white mb-0 opacity-80">Développement custom</p>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-4">
                            Développez vos propres nodes, créez des extensions personnalisées et architecturez des solutions d'entreprise complexes.
                        </p>
                        <a href="#expert" class="btn btn-outline-danger rounded-pill d-block">Voir les tutoriels</a>
                    </div>
                </div>
            </div>
            
            <!-- Tutoriel 5 - Spécialisé -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
                <div class="card tutorial-card h-100 border-0 shadow-sm">
                    <div style="background: linear-gradient(120deg, #f2dfa0, #e9c46a); border-radius: 12px 12px 0 0;" class="p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-industry fa-3x" style="color: #e9c46a;"></i>
                        </div>
                        <h3 class="h3 fw-bold text-white mb-1">Spécialisé</h3>
                        <p class="text-white mb-0 opacity-80">Cas d'usage métier</p>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-4">
                            Tutoriels spécialisés par secteur d'activité : e-commerce, marketing, finance, RH et bien d'autres domaines professionnels.
                        </p>
                        <a href="#specialise" class="btn btn-outline-warning rounded-pill d-block">Voir les tutoriels</a>
                    </div>
                </div>
            </div>
            
            <!-- Tutoriel personnalisé -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="600">
                <div class="card tutorial-card h-100 border-0 shadow-sm">
                    <div style="background: linear-gradient(120deg, #e8e8e8, #d1d1d1); border-radius: 12px 12px 0 0;" class="p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-30 rounded-circle mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-question fa-3x text-infinity-blue"></i>
                        </div>
                        <h3 class="h3 fw-bold text-infinity-blue mb-1">Besoin d'aide ?</h3>
                        <p class="text-muted mb-0">Tutoriel personnalisé</p>
                    </div>
                    <div class="card-body p-4 text-center">
                        <p class="mb-4">
                            Vous ne trouvez pas le tutoriel qu'il vous faut ? Demandez-nous de créer un tutoriel personnalisé pour votre cas d'usage spécifique.
                        </p>
                        <a href="{{ route('contact') }}?subject=tutoriel-personnalise" class="btn btn-primary rounded-pill px-4">Demander un tutoriel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- Tutoriels Débutant Section -->
<section id="debutant" class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 order-lg-1 order-2" data-aos="fade-up">
                <span class="badge bg-primary mb-2">Niveau Débutant</span>
                <h2 class="fw-bold text-infinity-blue mb-4">Premiers pas avec n8n</h2>
                <p class="lead mb-4">
                    Commencez votre apprentissage de n8n avec nos tutoriels débutant. Apprenez les bases, créez vos premiers workflows et découvrez les concepts fondamentaux de l'automatisation.
                </p>
                <div class="d-flex flex-wrap mb-4">
                    <span class="level-included me-2 mb-2">Installation</span>
                    <span class="level-included me-2 mb-2">Interface</span>
                    <span class="level-included me-2 mb-2">Premiers workflows</span>
                </div>
            </div>
            <div class="col-lg-6 order-lg-2 order-1 mb-4 mb-lg-0" data-aos="fade-left">
                <img src="{{ asset('images/logo/Logo_500.png') }}" alt="Tutoriels débutant n8n" class="img-fluid rounded-4 shadow">
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8" data-aos="fade-up">
                <h3 class="h4 fw-bold text-infinity-blue mb-4">Tutoriels inclus dans ce niveau</h3>
                
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header py-3" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3);">
                        <h4 class="h5 fw-bold text-white mb-0">Installation et configuration</h4>
                    </div>
                    <div class="card-body p-4">
                        <p>
                            Apprenez à installer n8n sur votre machine locale, configurez votre environnement de développement et découvrez les différentes options de déploiement disponibles.
                        </p>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3);">
                                        <i class="fas fa-download"></i>
                                    </div>
                                    <span>Installation locale</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3);">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <span>Configuration initiale</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3);">
                                        <i class="fas fa-cloud"></i>
                                    </div>
                                    <span>Options cloud</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3);">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <span>Sécurité de base</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm rounded-4 sticky-lg-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h4 class="h5 fw-bold text-infinity-blue mb-4">Accès aux tutoriels</h4>
                        
                        <div class="mb-4 pb-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Niveau débutant :</span>
                                <span class="h5 fw-bold text-success mb-0">Gratuit</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Durée estimée :</span>
                                <span class="text-muted">4-6 heures</span>
                            </div>
                        </div>
                        
                        <h5 class="h6 fw-bold mb-3">Ce niveau inclut :</h5>
                        <ul class="mb-4">
                            <li class="mb-2">8 tutoriels vidéo HD</li>
                            <li class="mb-2">Fichiers d'exercices téléchargeables</li>
                            <li class="mb-2">Quiz de validation</li>
                            <li class="mb-2">Certificat de completion</li>
                        </ul>
                        
                        <a href="{{ route('register') }}" class="btn btn-primary rounded-pill d-block mb-3">
                            Commencer gratuitement
                        </a>
                        <a href="#" class="btn btn-outline-primary rounded-pill d-block">
                            Voir le programme détaillé
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg overflow-hidden" data-aos="fade-up">
                    <div class="row g-0">
                        <div class="col-md-6 p-5 d-flex flex-column justify-content-center" style="background: linear-gradient(45deg, #f3d5d5, #e8b4b4);">
                            <h2 class="fw-bold mb-3 text-white">Prêt à maîtriser n8n ?</h2>
                            <p class="mb-4 text-white">Commencez dès maintenant avec nos tutoriels gratuits et progressez à votre rythme vers l'expertise en automatisation.</p>
                            <div class="d-flex gap-3">
                                <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-4">
                                    Commencer gratuitement
                                </a>
                                <a href="{{ route('downloads') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                                    Voir les téléchargements
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 p-5 bg-white d-flex flex-column justify-content-center">
                            <div class="text-center">
                                <div class="feature-icon mx-auto mb-4" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 32px;">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <h3 class="fw-bold text-infinity-blue mb-3">Apprentissage progressif</h3>
                                <p class="text-muted">Du débutant à l'expert, suivez un parcours structuré avec des projets concrets et un support communautaire.</p>
                                <div class="d-flex justify-content-center gap-4 mt-4">
                                    <div class="text-center">
                                        <h4 class="fw-bold text-infinity-blue mb-1">50+</h4>
                                        <small class="text-muted">Tutoriels disponibles</small>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="fw-bold text-infinity-blue mb-1">1000+</h4>
                                        <small class="text-muted">Étudiants actifs</small>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="fw-bold text-infinity-blue mb-1">95%</h4>
                                        <small class="text-muted">Taux de satisfaction</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
