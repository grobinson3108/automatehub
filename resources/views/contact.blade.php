@extends('layouts.app')

@section('title', 'Contact - AutomateHub')

@section('content')
<section class="hero hero-with-bg py-5">
    <div class="hero-overlay"></div>
    <div class="container hero-content text-center text-white">
        <h1 class="display-4 fw-bold mb-3">Contactez-moi</h1>
        <p class="lead">Une question ? Un projet ? Parlons-en !</p>
    </div>
</section>

<!-- Contact Options Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-4">Comment me contacter ?</h2>
            <p class="lead text-muted fs-4">Choisissez le moyen qui vous convient le mieux</p>
        </div>
        
        <div class="row g-5 mb-5">
            <div class="col-md-4">
                <div class="contact-method text-center">
                    <div class="contact-icon mb-4">
                        <i class="fas fa-envelope fa-3x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Email Direct</h4>
                    <p class="text-muted fs-5 mb-4">Pour les demandes spécifiques et les projets personnalisés</p>
                    <a href="#contact-form" class="btn btn-outline-primary">Formulaire de contact</a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="contact-method text-center">
                    <div class="contact-icon mb-4">
                        <i class="fab fa-youtube fa-3x text-danger"></i>
                    </div>
                    <h4 class="fw-bold mb-3">YouTube</h4>
                    <p class="text-muted fs-5 mb-4">Commentaires sur mes vidéos et questions publiques</p>
                    <a href="#" class="btn btn-outline-danger">Voir la chaîne</a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="contact-method text-center">
                    <div class="contact-icon mb-4">
                        <i class="fas fa-users fa-3x text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Communauté</h4>
                    <p class="text-muted fs-5 mb-4">Rejoignez la communauté pour échanger avec d'autres membres</p>
                    <a href="/register" class="btn btn-outline-success">Rejoindre</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-5 bg-light" id="contact-form">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold mb-3">Envoyez-moi un message</h2>
                    <p class="lead text-muted">Je réponds personnellement à tous les messages</p>
                </div>
                
                <div class="contact-form-container">
                    <form class="contact-form">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control form-control-lg" id="name" placeholder="Votre nom" required>
                                    <label for="name">Votre nom</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control form-control-lg" id="email" placeholder="Votre email" required>
                                    <label for="email">Votre email</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-floating mt-4">
                            <select class="form-select form-select-lg" id="subject">
                                <option value="">Choisissez un sujet</option>
                                <option value="question">Question générale</option>
                                <option value="workflow">Demande de workflow personnalisé</option>
                                <option value="support">Support technique</option>
                                <option value="partnership">Proposition de partenariat</option>
                                <option value="collaboration">Collaboration</option>
                            </select>
                            <label for="subject">Sujet de votre message</label>
                        </div>
                        
                        <div class="form-floating mt-4">
                            <textarea class="form-control" id="message" placeholder="Votre message" style="height: 150px" required></textarea>
                            <label for="message">Votre message</label>
                        </div>
                        
                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-primary btn-lg px-5 py-3">
                                <i class="fas fa-paper-plane me-2"></i>
                                Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Questions Fréquentes</h2>
            <p class="lead text-muted">Les réponses aux questions les plus courantes</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="faq-container">
                    <div class="faq-item mb-4">
                        <div class="faq-question">
                            <h5 class="fw-bold mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                Quel est le délai de réponse ?
                            </h5>
                            <p class="text-muted fs-5">Je réponds généralement sous 24-48h en semaine. Les demandes complexes peuvent prendre plus de temps.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item mb-4">
                        <div class="faq-question">
                            <h5 class="fw-bold mb-2">
                                <i class="fas fa-euro-sign text-success me-2"></i>
                                Proposez-vous des workflows sur-mesure ?
                            </h5>
                            <p class="text-muted fs-5">Oui ! Les abonnements Business incluent la création de workflows personnalisés selon vos besoins spécifiques.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item mb-4">
                        <div class="faq-question">
                            <h5 class="fw-bold mb-2">
                                <i class="fas fa-graduation-cap text-warning me-2"></i>
                                Accompagnez-vous les débutants ?
                            </h5>
                            <p class="text-muted fs-5">Absolument ! La communauté est conçue pour tous les niveaux, avec des workflows adaptés aux débutants.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item mb-4">
                        <div class="faq-question">
                            <h5 class="fw-bold mb-2">
                                <i class="fas fa-handshake text-info me-2"></i>
                                Acceptez-vous les partenariats ?
                            </h5>
                            <p class="text-muted fs-5">Je suis ouvert aux collaborations intéressantes ! Contactez-moi pour discuter de votre projet.</p>
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
/* Contact Page Custom Styles */
.contact-method {
    padding: 2rem 1rem;
    transition: transform 0.3s ease;
}

.contact-method:hover {
    transform: translateY(-10px);
}

.contact-icon {
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.contact-form-container {
    background: white;
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.contact-form .form-control,
.contact-form .form-select {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1rem;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.contact-form .form-control:focus,
.contact-form .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(107, 163, 195, 0.25);
    transform: translateY(-2px);
}

.contact-form .form-floating > label {
    font-weight: 500;
    color: #6c757d;
}

.faq-container {
    background: white;
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
}

.faq-item {
    padding: 1.5rem 0;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.3s ease;
}

.faq-item:last-child {
    border-bottom: none;
}

.faq-item:hover {
    transform: translateX(10px);
    background: rgba(59, 130, 246, 0.02);
    border-radius: 12px;
    padding-left: 1rem;
    padding-right: 1rem;
}

.faq-question h5 {
    transition: color 0.3s ease;
}

.faq-item:hover .faq-question h5 {
    color: var(--primary-color);
}

/* Button Styles */
.btn-outline-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

.btn-outline-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
}

.btn-outline-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(25, 135, 84, 0.3);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(59, 130, 246, 0.4);
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

.contact-method,
.contact-form-container,
.faq-container {
    animation: fadeInUp 0.6s ease-out;
}

.contact-method:nth-child(2) {
    animation-delay: 0.1s;
}

.contact-method:nth-child(3) {
    animation-delay: 0.2s;
}

/* Responsive */
@media (max-width: 768px) {
    .contact-form-container {
        padding: 2rem 1.5rem;
    }
    
    .faq-container {
        padding: 2rem 1.5rem;
    }
    
    .contact-icon {
        height: 80px;
    }
    
    .contact-icon i {
        font-size: 2rem !important;
    }
}

/* Scroll smooth */
html {
    scroll-behavior: smooth;
}
</style>
@endpush