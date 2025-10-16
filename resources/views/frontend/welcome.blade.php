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

.card-header {
    background: linear-gradient(45deg, #b4d8e8, #6ba3c3);
    color: white;
}

.card.premium .card-header {
    background: linear-gradient(45deg, #f3d5d5, #e8b4b4);
}

.feature-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #b4d8e8, #6ba3c3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 32px;
    margin-bottom: 20px;
}

.text-infinity-blue {
    color: #6ba3c3;
}

.text-pink-medium {
    color: #e8b4b4;
}
</style>

<!-- Hero Section -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-1 order-2" data-aos="fade-up">
                <h1 class="display-4 fw-bold text-infinity-blue mb-4">
                    Maîtrisez n8n<br>avec Automatehub
                </h1>
                <p class="lead mb-4">
                    Apprenez l'automatisation avec des tutoriels pratiques,<br>des workflows prêts à l'emploi et une communauté active...
                </p>
                <div class="d-flex gap-3">
                    <a href="{{ route('tutorials.index') }}" class="btn btn-primary btn-lg rounded-pill px-4">
                        Voir les tutoriels
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                        Commencer gratuitement
                    </a>
                </div>
            </div>
            <div class="col-lg-6 order-lg-2 order-1 mb-5 mb-lg-0" data-aos="fade-left" data-aos-delay="200">
                <img src="{{ asset('images/logo/Logo_900.png') }}" alt="Automatehub - Plateforme n8n" class="img-fluid rounded-4 shadow">
            </div>
        </div>
    </div>
</section>

<!-- Bénéfices Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold text-infinity-blue">L'apprentissage n8n simplifié</h2>
            <p class="lead">Voici en quelques mots ce qu'Automatehub peut vous apporter.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon mx-auto">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="h5 fw-bold">Apprenez rapidement</h3>
                <p>Maîtrisez n8n en quelques semaines<br>grâce à nos tutoriels structurés et<br>nos exemples pratiques concrets.</p>
            </div>
            
            <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon mx-auto" style="background: linear-gradient(45deg, #f3d5d5, #e8b4b4);">
                    <i class="fas fa-download"></i>
                </div>
                <h3 class="h5 fw-bold">Ressources prêtes</h3>
                <p>Téléchargez des workflows complets<br>et des templates pour démarrer<br>vos projets d'automatisation immédiatement.</p>
            </div>
            
            <div class="col-md-4 text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon mx-auto" style="background: linear-gradient(45deg, #92c1df, #6ba3c3);">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="h5 fw-bold">Communauté active</h3>
                <p>Rejoignez une communauté passionnée d'automatisation qui partage ses connaissances et vous aide à progresser.</p>
            </div>
        </div>
    </div>
</section>

<!-- Exemples de tutoriels -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold text-infinity-blue">Tutoriels populaires & pratiques</h2>
            <p class="lead">Découvrez nos tutoriels les plus appréciés pour débuter avec n8n...</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-play"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-0">Premiers pas avec n8n</h4>
                        </div>
                        <p class="mb-3">Découvrez l'interface n8n et créez votre premier workflow d'automatisation !</p>
                        <div class="bg-light p-3 rounded mb-3">
                            <p class="fw-bold mb-1 text-primary">Ce que vous apprendrez :</p>
                            <ul class="mb-0">
                                <li>Installation et configuration de n8n</li>
                                <li>Interface et concepts de base</li>
                                <li>Création de votre premier workflow</li>
                            </ul>
                        </div>
                        <a href="{{ route('tutorials.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Voir le tutoriel</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-0">Automatisation email</h4>
                        </div>
                        <p class="mb-3">Créez des workflows pour automatiser vos campagnes email et notifications !</p>
                        <div class="bg-light p-3 rounded mb-3">
                            <p class="fw-bold mb-1 text-secondary">Fonctionnalités couvertes :</p>
                            <ul class="mb-0">
                                <li>Intégration avec Gmail et Outlook</li>
                                <li>Déclencheurs automatiques</li>
                                <li>Templates d'emails dynamiques</li>
                            </ul>
                        </div>
                        <a href="{{ route('tutorials.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">Voir le tutoriel</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-database"></i>
                            </div>
                            <h4 class="h5 fw-bold mb-0">Intégrations API</h4>
                        </div>
                        <p class="mb-3">Connectez n8n avec vos outils favoris via les APIs et webhooks !</p>
                        <div class="bg-light p-3 rounded mb-3">
                            <p class="fw-bold mb-1 text-info">APIs populaires :</p>
                            <ul class="mb-0">
                                <li>Slack, Discord, Teams</li>
                                <li>Google Sheets, Airtable</li>
                                <li>Zapier, Make, Webhooks</li>
                            </ul>
                        </div>
                        <a href="{{ route('tutorials.index') }}" class="btn btn-sm btn-outline-info rounded-pill">Voir le tutoriel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Catégories de contenu -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold text-infinity-blue">Nos catégories d'apprentissage</h2>
            <p class="lead">Du débutant à l'expert, trouvez le contenu adapté à votre niveau</p>
        </div>

        <div class="row g-4 mb-5">
            <!-- Carte 1 - Débutant -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3);" class="p-4 text-white text-center rounded-top">
                    <i class="fas fa-seedling fa-3x mb-3"></i>
                    <h3 class="h5 fw-bold">Niveau Débutant</h3>
                </div>
                <div class="bg-white p-4 shadow rounded-bottom">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Installation et configuration</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Interface et navigation</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Premiers workflows simples</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Concepts fondamentaux</li>
                    </ul>
                    <div class="text-center mt-4">
                        <a href="{{ route('tutorials.index') }}" class="btn btn-outline-primary rounded-pill">Commencer</a>
                    </div>
                </div>
            </div>

            <!-- Carte 2 - Intermédiaire -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div style="background: linear-gradient(45deg, #f3d5d5, #e8b4b4);" class="p-4 text-white text-center rounded-top">
                    <i class="fas fa-cogs fa-3x mb-3"></i>
                    <h3 class="h5 fw-bold">Niveau Intermédiaire</h3>
                </div>
                <div class="bg-white p-4 shadow rounded-bottom">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-secondary me-2"></i> Workflows complexes</li>
                        <li class="mb-2"><i class="fas fa-check text-secondary me-2"></i> Intégrations avancées</li>
                        <li class="mb-2"><i class="fas fa-check text-secondary me-2"></i> Gestion des erreurs</li>
                        <li class="mb-2"><i class="fas fa-check text-secondary me-2"></i> Optimisation des performances</li>
                    </ul>
                    <div class="text-center mt-4">
                        <a href="{{ route('tutorials.index') }}" class="btn btn-outline-secondary rounded-pill">Progresser</a>
                    </div>
                </div>
            </div>

            <!-- Carte 3 - Expert -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div style="background: linear-gradient(45deg, #a9c2d9, #7a9cb5);" class="p-4 text-white text-center rounded-top">
                    <i class="fas fa-crown fa-3x mb-3"></i>
                    <h3 class="h5 fw-bold">Niveau Expert</h3>
                </div>
                <div class="bg-white p-4 shadow rounded-bottom">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-info me-2"></i> Développement de nodes custom</li>
                        <li class="mb-2"><i class="fas fa-check text-info me-2"></i> Architecture d'entreprise</li>
                        <li class="mb-2"><i class="fas fa-check text-info me-2"></i> Sécurité et monitoring</li>
                        <li class="mb-2"><i class="fas fa-check text-info me-2"></i> Déploiement en production</li>
                    </ul>
                    <div class="text-center mt-4">
                        <a href="{{ route('tutorials.index') }}" class="btn btn-outline-info rounded-pill">Maîtriser</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Carte 4 - Téléchargements -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div style="background: linear-gradient(45deg, #eca192, #e76f51);" class="p-4 text-white text-center rounded-top">
                    <i class="fas fa-download fa-3x mb-3"></i>
                    <h3 class="h5 fw-bold">Téléchargements</h3>
                </div>
                <div class="bg-white p-4 shadow rounded-bottom">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-danger me-2"></i> Workflows prêts à l'emploi</li>
                        <li class="mb-2"><i class="fas fa-check text-danger me-2"></i> Templates personnalisables</li>
                        <li class="mb-2"><i class="fas fa-check text-danger me-2"></i> Exemples de code</li>
                        <li class="mb-2"><i class="fas fa-check text-danger me-2"></i> Documentation PDF</li>
                    </ul>
                    <div class="text-center mt-4">
                        <a href="{{ route('downloads') }}" class="btn btn-outline-danger rounded-pill">Télécharger</a>
                    </div>
                </div>
            </div>
            
            <!-- Carte 5 - Blog -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div style="background: linear-gradient(45deg, #92c1df, #6ba3c3);" class="p-4 text-white text-center rounded-top">
                    <i class="fas fa-blog fa-3x mb-3"></i>
                    <h3 class="h5 fw-bold">Blog & Actualités</h3>
                </div>
                <div class="bg-white p-4 shadow rounded-bottom">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Actualités n8n</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Cas d'usage réels</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Conseils et astuces</li>
                        <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Retours d'expérience</li>
                    </ul>
                    <div class="text-center mt-4">
                        <a href="{{ route('blog.index') }}" class="btn btn-outline-primary rounded-pill">Lire le blog</a>
                    </div>
                </div>
            </div>
            
            <!-- Carte 6 - Communauté -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div style="background: linear-gradient(45deg, #f2dfa0, #e9c46a);" class="p-4 text-white text-center rounded-top">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h3 class="h5 fw-bold">Communauté</h3>
                </div>
                <div class="bg-white p-4 shadow rounded-bottom">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fas fa-check text-warning me-2"></i> Forum d'entraide</li>
                        <li class="mb-2"><i class="fas fa-check text-warning me-2"></i> Partage de workflows</li>
                        <li class="mb-2"><i class="fas fa-check text-warning me-2"></i> Événements en ligne</li>
                        <li class="mb-2"><i class="fas fa-check text-warning me-2"></i> Réseau de passionnés</li>
                    </ul>
                    <div class="text-center mt-4">
                        <a href="{{ route('contact') }}" class="btn btn-outline-warning rounded-pill">Rejoindre</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('register') }}" class="btn btn-primary btn-lg rounded-pill px-4">
                Commencer votre apprentissage gratuitement
            </a>
        </div>
    </div>
</section>

<!-- Formules d'abonnement -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold text-infinity-blue">Nos formules</h2>
            <p class="lead">Choisissez la formule qui correspond à vos besoins d'apprentissage</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card service-card h-100 border-0 shadow-sm">
                    <div class="card-header text-center py-4">
                        <h3 class="fw-bold mb-0">Gratuit</h3>
                        <p class="text-white mb-0">Pour découvrir</p>
                    </div>
                    <div class="card-body text-center">
                        <h4 class="display-6 fw-bold text-infinity-blue mb-3">0€<span class="fs-6 fw-normal text-muted">/mois</span></h4>
                        <p class="mb-4">Accès limité</p>
                        <ul class="list-unstyled text-start mb-4">
                            <li class="mb-3"><i class="fas fa-check text-primary me-2"></i> 5 tutoriels de base</li>
                            <li class="mb-3"><i class="fas fa-check text-primary me-2"></i> 2 téléchargements par mois</li>
                            <li class="mb-3"><i class="fas fa-check text-primary me-2"></i> Accès au blog</li>
                            <li class="mb-3"><i class="fas fa-check text-primary me-2"></i> Support communautaire</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4">S'inscrire gratuitement</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card service-card premium h-100 border-0 shadow">
                    <div class="card-header text-center py-4">
                        <span class="badge bg-warning position-absolute top-0 end-0 mt-3 me-3">Populaire</span>
                        <h3 class="fw-bold mb-0">Premium</h3>
                        <p class="text-white mb-0">Pour progresser</p>
                    </div>
                    <div class="card-body text-center">
                        <h4 class="display-6 fw-bold text-pink-medium mb-3">19€<span class="fs-6 fw-normal text-muted">/mois</span></h4>
                        <p class="mb-4">Accès complet</p>
                        <ul class="list-unstyled text-start mb-4">
                            <li class="mb-3"><i class="fas fa-check text-secondary me-2"></i> <strong>Tous les tutoriels</strong></li>
                            <li class="mb-3"><i class="fas fa-check text-secondary me-2"></i> Téléchargements illimités</li>
                            <li class="mb-3"><i class="fas fa-check text-secondary me-2"></i> Workflows exclusifs</li>
                            <li class="mb-3"><i class="fas fa-check text-secondary me-2"></i> Support prioritaire</li>
                            <li class="mb-3"><i class="fas fa-check text-secondary me-2"></i> Accès anticipé aux nouveautés</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-secondary rounded-pill px-4">Choisir Premium</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card service-card h-100 border-0 shadow-sm">
                    <div class="card-header text-center py-4">
                        <h3 class="fw-bold mb-0">Entreprise</h3>
                        <p class="text-white mb-0">Pour les équipes</p>
                    </div>
                    <div class="card-body text-center">
                        <h4 class="display-6 fw-bold text-infinity-blue mb-3">Sur devis</h4>
                        <p class="mb-4">Solution personnalisée</p>
                        <ul class="list-unstyled text-start mb-4">
                            <li class="mb-3"><i class="fas fa-check text-primary me-2"></i> <strong>Tout Premium inclus</strong></li>
                            <li class="mb-3"><i class="fas fa-check text-primary me-2"></i> Formation sur mesure</li>
                            <li class="mb-3"><i class="fas fa-check text-primary me-2"></i> Consulting personnalisé</li>
                            <li class="mb-3"><i class="fas fa-check text-primary me-2"></i> Support dédié</li>
                            <li class="mb-3"><i class="fas fa-check text-primary me-2"></i> Licences multiples</li>
                        </ul>
                        <a href="{{ route('contact') }}" class="btn btn-primary rounded-pill px-4">Nous contacter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold text-infinity-blue">Ce que disent nos utilisateurs</h2>
            <p class="lead">Des développeurs et entrepreneurs satisfaits de leur apprentissage n8n</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <div class="d-flex mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <p class="mb-4">"Automatehub m'a permis de maîtriser n8n en quelques semaines. Les tutoriels sont clairs et les exemples très pratiques. Je recommande vivement !"</p>
                    <div class="d-flex align-items-center mt-auto">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <span class="fw-bold">M</span>
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-0 fw-bold">Marie</h5>
                            <p class="text-muted mb-0">Développeuse, Paris</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <div class="d-flex mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <p class="mb-4">"Grâce aux workflows téléchargeables, j'ai pu automatiser mes processus métier en un temps record. La plateforme est excellente pour débuter."</p>
                    <div class="d-flex align-items-center mt-auto">
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <span class="fw-bold">J</span>
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-0 fw-bold">Julien</h5>
                            <p class="text-muted mb-0">Entrepreneur, Lyon</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <div class="d-flex mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <p class="mb-4">"La communauté est très active et bienveillante. J'ai toujours trouvé de l'aide rapidement. Automatehub est devenu ma référence pour n8n."</p>
                    <div class="d-flex align-items-center mt-auto">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <span class="fw-bold">S</span>
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-0 fw-bold">Sophie</h5>
                            <p class="text-muted mb-0">Chef de projet, Toulouse</p>
                        </div>
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
                            <p class="mb-4 text-white">Rejoignez des milliers d'utilisateurs qui ont déjà transformé leur façon de travailler grâce à l'automatisation avec n8n.</p>
                            <div class="d-flex gap-3">
                                <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-4">
                                    Commencer gratuitement
                                </a>
                                <a href="{{ route('tutorials.index') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                                    Voir les tutoriels
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 p-5 bg-white d-flex flex-column justify-content-center">
                            <div class="text-center">
                                <div class="feature-icon mx-auto mb-4" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3);">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <h3 class="fw-bold text-infinity-blue mb-3">Démarrez dès aujourd'hui</h3>
                                <p class="text-muted">Accès immédiat à nos tutoriels de base, workflows d'exemple et communauté d'entraide.</p>
                                <div class="d-flex justify-content-center gap-4 mt-4">
                                    <div class="text-center">
                                        <h4 class="fw-bold text-infinity-blue mb-1">500+</h4>
                                        <small class="text-muted">Utilisateurs actifs</small>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="fw-bold text-infinity-blue mb-1">100+</h4>
                                        <small class="text-muted">Tutoriels disponibles</small>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="fw-bold text-infinity-blue mb-1">50+</h4>
                                        <small class="text-muted">Workflows prêts</small>
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
