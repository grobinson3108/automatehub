@extends('layouts.app')

@section('title', 'À propos - AutomateHub')

@section('content')
<section class="hero hero-with-bg py-5">
    <div class="hero-overlay"></div>
    <div class="container hero-content text-center text-white">
        <h1 class="display-4 fw-bold mb-3">Mon Projet : AutomateHub</h1>
        <p class="lead">Je lance AutomateHub pour partager mes workflows n8n créés avec l'IA</p>
    </div>
</section>

<!-- Mission Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold mb-4">Ma Mission</h2>
                    <p class="lead fs-4 text-muted mb-5">
                        Devenir <strong class="text-primary">LA référence française</strong> pour apprendre n8n en créant une communauté 
                        où l'IA et l'expertise humaine se rencontrent pour révolutionner l'automatisation business.
                    </p>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="quote-container text-center py-5">
                            <i class="fas fa-quote-left fa-2x text-primary mb-4"></i>
                            <blockquote class="fs-5 fst-italic text-muted">
                                "Je ne veux pas juste enseigner n8n, je veux créer l'écosystème français qui manque à cette technologie."
                            </blockquote>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Expertise Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Mon Expertise</h2>
            <p class="lead text-muted">Les compétences qui me permettent de créer des workflows innovants</p>
        </div>
                
        <div class="row g-5">
            <div class="col-md-6">
                <div class="expertise-item text-center">
                    <div class="icon-wrapper mb-4">
                        <i class="fas fa-robot fa-3x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Intelligence Artificielle</h4>
                    <p class="text-muted fs-5">Maîtrise de l'IA pour concevoir des workflows complexes et optimisés</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="expertise-item text-center">
                    <div class="icon-wrapper mb-4">
                        <i class="fas fa-project-diagram fa-3x text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Architecture n8n</h4>
                    <p class="text-muted fs-5">Conception de workflows robustes pour e-commerce, SaaS, CRM, marketing</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="expertise-item text-center">
                    <div class="icon-wrapper mb-4">
                        <i class="fas fa-chart-line fa-3x text-warning"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Growth & Analytics</h4>
                    <p class="text-muted fs-5">Optimisation des conversions et tracking avancé multi-plateformes</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="expertise-item text-center">
                    <div class="icon-wrapper mb-4">
                        <i class="fas fa-graduation-cap fa-3x text-info"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Pédagogie</h4>
                    <p class="text-muted fs-5">Transmission de concepts complexes via vidéos et communauté gamifiée</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Résultats Concrets</h2>
            <p class="lead text-muted">Les chiffres qui prouvent l'impact de mon travail</p>
        </div>
                
        <div class="row g-4 text-center">
            <div class="col-6 col-lg-3">
                <div class="stat-item">
                    <div class="stat-number text-primary display-4 fw-bold">67</div>
                    <div class="stat-label text-muted fs-5">Workflows IA</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-item">
                    <div class="stat-number text-success display-4 fw-bold">322</div>
                    <div class="stat-label text-muted fs-5">Abonnés YouTube</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-item">
                    <div class="stat-number text-warning display-4 fw-bold">36</div>
                    <div class="stat-label text-muted fs-5">Vidéos tutoriels</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-item">
                    <div class="stat-number text-info display-4 fw-bold">5.2k</div>
                    <div class="stat-label text-muted fs-5">Vues mensuelles</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Timeline Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Mon Parcours</h2>
            <p class="lead text-muted">L'évolution qui m'a mené à créer AutomateHub</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <div class="timeline-container">
                    <div class="timeline-item mb-5">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="timeline-icon bg-warning">
                                    <i class="fas fa-lightbulb fa-2x text-white"></i>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <h4 class="fw-bold mb-2">Découverte n8n</h4>
                                <p class="text-muted fs-5 mb-0">Fasciné par le potentiel de n8n, j'ai commencé à créer mes premiers workflows pour mes projets personnels.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item mb-5">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="timeline-icon bg-primary">
                                    <i class="fas fa-robot fa-2x text-white"></i>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <h4 class="fw-bold mb-2">Intégration IA</h4>
                                <p class="text-muted fs-5 mb-0">J'ai réalisé que l'IA pouvait concevoir des architectures n8n plus intelligentes que ce que je faisais manuellement.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item mb-5">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="timeline-icon bg-danger">
                                    <i class="fas fa-video fa-2x text-white"></i>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <h4 class="fw-bold mb-2">Lancement YouTube</h4>
                                <p class="text-muted fs-5 mb-0">Partage de mes créations : 4 vidéos/semaine, croissance organique de 80 abonnés/semaine.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item mb-5">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="timeline-icon bg-success">
                                    <i class="fas fa-users fa-2x text-white"></i>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <h4 class="fw-bold mb-2">AutomateHub</h4>
                                <p class="text-muted fs-5 mb-0">Création de la première plateforme communautaire française n8n avec workflows IA.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vision Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Vision 2025</h2>
            <p class="lead text-muted">Mes objectifs pour révolutionner l'automatisation en France</p>
        </div>
                
        <div class="row g-5">
            <div class="col-md-4">
                <div class="vision-item text-center">
                    <div class="vision-icon mb-4">
                        <i class="fas fa-trophy fa-4x text-warning"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Leader Français</h4>
                    <p class="text-muted fs-5">Devenir la référence incontournable pour apprendre n8n en France</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="vision-item text-center">
                    <div class="vision-icon mb-4">
                        <i class="fas fa-users fa-4x text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-3">5,000 Membres</h4>
                    <p class="text-muted fs-5">Construire une communauté active de 5,000+ entrepreneurs automatisés</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="vision-item text-center">
                    <div class="vision-icon mb-4">
                        <i class="fas fa-certificate fa-4x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Certification</h4>
                    <p class="text-muted fs-5">Lancer la première certification n8n officielle en français</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary">
    <div class="container">
        <div class="row align-items-center text-white">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-3">
                    <i class="fas fa-handshake me-3"></i>
                    Rejoignez l'aventure !
                </h3>
                <p class="fs-5 mb-0">
                    Vous voulez faire partie de cette révolution ? Commencez par suivre ma chaîne YouTube et rejoignez la communauté.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="/register" class="btn btn-light btn-lg px-4 py-3">
                    <i class="fas fa-rocket me-2"></i>
                    Commencer maintenant
                </a>
            </div>
        </div>
    </div>
</section>
                

@endsection

@push('styles')
<style>
/* About Page Custom Styles */
.quote-container {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(16, 185, 129, 0.05));
    border-radius: 20px;
}

.expertise-item {
    padding: 2rem 1rem;
    transition: transform 0.3s ease;
}

.expertise-item:hover {
    transform: translateY(-10px);
}

.icon-wrapper {
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-item {
    padding: 2rem 1rem;
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: scale(1.05);
}

.stat-number {
    margin-bottom: 1rem;
}

.timeline-container {
    position: relative;
}

.timeline-item {
    position: relative;
}

.timeline-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.timeline-icon:hover {
    transform: scale(1.1);
}

.vision-item {
    padding: 2rem 1rem;
    transition: transform 0.3s ease;
}

.vision-item:hover {
    transform: translateY(-15px);
}

.vision-icon {
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
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

.expertise-item,
.stat-item,
.timeline-item,
.vision-item {
    animation: fadeInUp 0.6s ease-out;
}

.timeline-item:nth-child(2) {
    animation-delay: 0.1s;
}

.timeline-item:nth-child(3) {
    animation-delay: 0.2s;
}

.timeline-item:nth-child(4) {
    animation-delay: 0.3s;
}

/* Responsive */
@media (max-width: 768px) {
    .timeline-icon {
        width: 60px;
        height: 60px;
        margin-bottom: 1rem;
    }
    
    .vision-icon {
        height: 80px;
    }
    
    .vision-icon i {
        font-size: 2.5rem !important;
    }
}
</style>
@endpush