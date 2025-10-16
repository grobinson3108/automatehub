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

.download-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.download-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.download-header {
    padding: 30px 20px;
    text-align: center;
    color: white;
}

.download-workflows .download-header {
    background: linear-gradient(45deg, #b4d8e8, #6ba3c3);
}

.download-templates .download-header {
    background: linear-gradient(45deg, #f3d5d5, #e8b4b4);
}

.download-scripts .download-header {
    background: linear-gradient(45deg, #a9c2d9, #7a9cb5);
}

.download-guides .download-header {
    background: linear-gradient(45deg, #eca192, #e76f51);
}

.download-tools .download-header {
    background: linear-gradient(45deg, #f2dfa0, #e9c46a);
}

.download-header .icon-container {
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

.download-body {
    padding: 30px;
}

.type-included {
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
                    Téléchargements<br>n8n gratuits
                </h1>
                <p class="lead mb-4">
                    Des ressources prêtes à l'emploi pour accélérer vos projets d'automatisation : workflows complets, templates personnalisables, scripts utiles et guides pratiques.
                </p>
            </div>
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                <img src="{{ asset('images/logo/Logo_900.png') }}" alt="Téléchargements n8n Automatehub" class="img-fluid rounded-4 shadow">
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
                    Ressources prêtes à utiliser
                </h2>
                <p class="lead mb-5">
                    Gagnez du temps avec nos ressources téléchargeables : workflows fonctionnels, templates personnalisables et outils pratiques pour vos projets d'automatisation n8n.
                </p>
            </div>
        </div>
        
        <div class="row g-4 mb-5">
            <!-- Téléchargement 1 - Workflows -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card download-card h-100 border-0 shadow-sm">
                    <div style="background: linear-gradient(120deg, #b4d8e8, #6ba3c3); border-radius: 12px 12px 0 0;" class="p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-project-diagram fa-3x" style="color: #6ba3c3;"></i>
                        </div>
                        <h3 class="h3 fw-bold text-white mb-1">Workflows</h3>
                        <p class="text-white mb-0 opacity-80">Prêts à l'emploi</p>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-4">
                            Workflows complets et fonctionnels pour les cas d'usage les plus courants : email, CRM, e-commerce, réseaux sociaux et bien plus.
                        </p>
                        <a href="#workflows" class="btn btn-outline-primary rounded-pill d-block">Voir les workflows</a>
                    </div>
                </div>
            </div>
            
            <!-- Téléchargement 2 - Templates -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card download-card h-100 border-0 shadow-sm">
                    <div style="background: linear-gradient(120deg, #f3d5d5, #e8b4b4); border-radius: 12px 12px 0 0;" class="p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-file-code fa-3x" style="color: #e8b4b4;"></i>
                        </div>
                        <h3 class="h3 fw-bold text-white mb-1">Templates</h3>
                        <p class="text-white mb-0 opacity-80">Personnalisables</p>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-4">
                            Templates de base personnalisables pour créer rapidement vos propres workflows selon vos besoins spécifiques.
                        </p>
                        <a href="#templates" class="btn btn-outline-secondary rounded-pill d-block">Voir les templates</a>
                    </div>
                </div>
            </div>
            
            <!-- Téléchargement 3 - Scripts -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card download-card h-100 border-0 shadow-sm">
                    <div style="background: linear-gradient(120deg, #a9c2d9, #7a9cb5); border-radius: 12px 12px 0 0;" class="p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-code fa-3x" style="color: #7a9cb5;"></i>
                        </div>
                        <h3 class="h3 fw-bold text-white mb-1">Scripts</h3>
                        <p class="text-white mb-0 opacity-80">Utilitaires</p>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-4">
                            Scripts JavaScript utiles pour étendre les fonctionnalités de n8n : fonctions personnalisées, helpers et utilitaires.
                        </p>
                        <a href="#scripts" class="btn btn-outline-info rounded-pill d-block">Voir les scripts</a>
                    </div>
                </div>
            </div>
            
            <!-- Téléchargement 4 - Guides -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
                <div class="card download-card h-100 border-0 shadow-sm">
                    <div style="background: linear-gradient(120deg, #eca192, #e76f51); border-radius: 12px 12px 0 0;" class="p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-book fa-3x" style="color: #e76f51;"></i>
                        </div>
                        <h3 class="h3 fw-bold text-white mb-1">Guides</h3>
                        <p class="text-white mb-0 opacity-80">Documentation</p>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-4">
                            Guides PDF détaillés, checklists et documentation pour vous accompagner dans vos projets d'automatisation.
                        </p>
                        <a href="#guides" class="btn btn-outline-danger rounded-pill d-block">Voir les guides</a>
                    </div>
                </div>
            </div>
            
            <!-- Téléchargement 5 - Outils -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
                <div class="card download-card h-100 border-0 shadow-sm">
                    <div style="background: linear-gradient(120deg, #f2dfa0, #e9c46a); border-radius: 12px 12px 0 0;" class="p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-tools fa-3x" style="color: #e9c46a;"></i>
                        </div>
                        <h3 class="h3 fw-bold text-white mb-1">Outils</h3>
                        <p class="text-white mb-0 opacity-80">Complémentaires</p>
                    </div>
                    <div class="card-body p-4">
                        <p class="mb-4">
                            Outils complémentaires pour optimiser votre environnement n8n : extensions, configurations et utilitaires système.
                        </p>
                        <a href="#outils" class="btn btn-outline-warning rounded-pill d-block">Voir les outils</a>
                    </div>
                </div>
            </div>
            
            <!-- Demande personnalisée -->
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="600">
                <div class="card download-card h-100 border-0 shadow-sm">
                    <div style="background: linear-gradient(120deg, #e8e8e8, #d1d1d1); border-radius: 12px 12px 0 0;" class="p-4 text-center">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-30 rounded-circle mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-question fa-3x text-infinity-blue"></i>
                        </div>
                        <h3 class="h3 fw-bold text-infinity-blue mb-1">Sur mesure</h3>
                        <p class="text-muted mb-0">Demande spéciale</p>
                    </div>
                    <div class="card-body p-4 text-center">
                        <p class="mb-4">
                            Besoin d'un workflow ou d'un outil spécifique ? Demandez-nous de créer une ressource personnalisée pour votre cas d'usage.
                        </p>
                        <a href="{{ route('contact') }}?subject=demande-ressource" class="btn btn-primary rounded-pill px-4">Faire une demande</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- Workflows Section -->
<section id="workflows" class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 order-lg-1 order-2" data-aos="fade-up">
                <span class="badge bg-primary mb-2">Workflows complets</span>
                <h2 class="fw-bold text-infinity-blue mb-4">Workflows prêts à l'emploi</h2>
                <p class="lead mb-4">
                    Des workflows complets et fonctionnels que vous pouvez importer directement dans votre instance n8n et utiliser immédiatement pour vos projets.
                </p>
                <div class="d-flex flex-wrap mb-4">
                    <span class="type-included me-2 mb-2">Email automation</span>
                    <span class="type-included me-2 mb-2">CRM sync</span>
                    <span class="type-included me-2 mb-2">Social media</span>
                </div>
            </div>
            <div class="col-lg-6 order-lg-2 order-1 mb-4 mb-lg-0" data-aos="fade-left">
                <img src="{{ asset('images/logo/Logo_500.png') }}" alt="Workflows n8n prêts à l'emploi" class="img-fluid rounded-4 shadow">
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8" data-aos="fade-up">
                <h3 class="h4 fw-bold text-infinity-blue mb-4">Workflows disponibles</h3>
                
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header py-3" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3);">
                        <h4 class="h5 fw-bold text-white mb-0">Automatisation email marketing</h4>
                    </div>
                    <div class="card-body p-4">
                        <p>
                            Workflow complet pour automatiser vos campagnes email : segmentation, personnalisation, envoi automatique et suivi des performances.
                        </p>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3);">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <span>Campagnes automatisées</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3);">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <span>Segmentation avancée</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3);">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <span>Analytics intégrés</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3);">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <span>Planification flexible</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header py-3" style="background: linear-gradient(45deg, #f3d5d5, #e8b4b4);">
                        <h4 class="h5 fw-bold text-white mb-0">Synchronisation CRM multi-plateformes</h4>
                    </div>
                    <div class="card-body p-4">
                        <p>
                            Synchronisez automatiquement vos données clients entre différents CRM et outils marketing pour maintenir une base de données cohérente.
                        </p>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #f3d5d5, #e8b4b4);">
                                        <i class="fas fa-sync"></i>
                                    </div>
                                    <span>Sync bidirectionnelle</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #f3d5d5, #e8b4b4);">
                                        <i class="fas fa-database"></i>
                                    </div>
                                    <span>Déduplication automatique</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #f3d5d5, #e8b4b4);">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <span>Validation des données</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #f3d5d5, #e8b4b4);">
                                        <i class="fas fa-history"></i>
                                    </div>
                                    <span>Historique des modifications</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header py-3" style="background: linear-gradient(45deg, #eca192, #e76f51);">
                        <h4 class="h5 fw-bold text-white mb-0">Gestion réseaux sociaux</h4>
                    </div>
                    <div class="card-body p-4">
                        <p>
                            Automatisez vos publications sur les réseaux sociaux, surveillez les mentions de votre marque et analysez l'engagement de votre audience.
                        </p>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #eca192, #e76f51);">
                                        <i class="fas fa-share-alt"></i>
                                    </div>
                                    <span>Publication automatique</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #eca192, #e76f51);">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <span>Monitoring des mentions</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #eca192, #e76f51);">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <span>Analytics d'engagement</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #eca192, #e76f51);">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <span>Planification de contenu</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
                    <div class="card-header py-3" style="background: linear-gradient(45deg, #a9c2d9, #7a9cb5);">
                        <h4 class="h5 fw-bold text-white mb-0">E-commerce automation</h4>
                    </div>
                    <div class="card-body p-4">
                        <p>
                            Automatisez votre boutique en ligne : gestion des commandes, mise à jour des stocks, notifications clients et suivi des expéditions.
                        </p>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #a9c2d9, #7a9cb5);">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <span>Gestion des commandes</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #a9c2d9, #7a9cb5);">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                    <span>Suivi des stocks</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #a9c2d9, #7a9cb5);">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <span>Suivi des expéditions</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="benefit-item">
                                    <div class="benefit-icon" style="background: linear-gradient(45deg, #a9c2d9, #7a9cb5);">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                    <span>Notifications automatiques</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card border-0 shadow-sm rounded-4 sticky-lg-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h4 class="h5 fw-bold text-infinity-blue mb-4">Accès aux workflows</h4>
                        
                        <div class="mb-4 pb-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Workflows de base :</span>
                                <span class="h5 fw-bold text-success mb-0">Gratuit</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Workflows avancés :</span>
                                <span class="h5 fw-bold text-pink-medium mb-0">Premium</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Formats disponibles :</span>
                                <span class="text-muted">JSON, ZIP</span>
                            </div>
                        </div>
                        
                        <h5 class="h6 fw-bold mb-3">Inclus avec chaque workflow :</h5>
                        <ul class="mb-4">
                            <li class="mb-2">Fichier JSON prêt à importer</li>
                            <li class="mb-2">Documentation d'installation</li>
                            <li class="mb-2">Guide de configuration</li>
                            <li class="mb-2">Exemples d'utilisation</li>
                        </ul>
                        
                        <a href="{{ route('register') }}" class="btn btn-primary rounded-pill d-block mb-3">
                            Télécharger gratuitement
                        </a>
                        <a href="#" class="btn btn-outline-primary rounded-pill d-block">
                            Voir tous les workflows
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
                            <h2 class="fw-bold mb-3 text-white">Accélérez vos projets</h2>
                            <p class="mb-4 text-white">Ne partez pas de zéro ! Utilisez nos ressources prêtes à l'emploi pour gagner du temps et vous concentrer sur ce qui compte vraiment.</p>
                            <div class="d-flex gap-3">
                                <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-4">
                                    Accéder aux téléchargements
                                </a>
                                <a href="{{ route('tutorials.index') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                                    Voir les tutoriels
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 p-5 bg-white d-flex flex-column justify-content-center">
                            <div class="text-center">
                                <div class="feature-icon mx-auto mb-4" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 32px;">
                                    <i class="fas fa-download"></i>
                                </div>
                                <h3 class="fw-bold text-infinity-blue mb-3">Ressources de qualité</h3>
                                <p class="text-muted">Toutes nos ressources sont testées, documentées et maintenues à jour pour vous garantir la meilleure expérience.</p>
                                <div class="d-flex justify-content-center gap-4 mt-4">
                                    <div class="text-center">
                                        <h4 class="fw-bold text-infinity-blue mb-1">100+</h4>
                                        <small class="text-muted">Ressources disponibles</small>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="fw-bold text-infinity-blue mb-1">5000+</h4>
                                        <small class="text-muted">Téléchargements</small>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="fw-bold text-infinity-blue mb-1">98%</h4>
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
