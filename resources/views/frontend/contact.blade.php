@extends('layouts.frontend')

@section('content')
<!-- Contact Hero -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8" data-aos="fade-up">
                <h1 class="display-4 fw-bold text-infinity-blue mb-4">Contactez-nous</h1>
                <p class="lead mb-4">
                    Vous avez des questions sur n8n ou vous souhaitez améliorer vos workflows d'automatisation ?
                    Nous sommes là pour vous accompagner dans votre transformation digitale.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6" data-aos="fade-up">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <h2 class="fw-bold text-infinity-blue mb-4">Envoyez-nous un message !</h2>
                    <p class="mb-4">
                        Remplissez le formulaire ci-dessous et nous vous répondrons dans les plus brefs délais.
                        Vous pouvez également nous appeler ou nous envoyer un email directement.
                    </p>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('contact.submit') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="company" class="form-label">Entreprise</label>
                                <input type="text" class="form-control @error('company') is-invalid @enderror" id="company" name="company" value="{{ old('company') }}">
                                @error('company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="subject" class="form-label">Sujet <span class="text-danger">*</span></label>
                                <select class="form-select @error('subject') is-invalid @enderror" id="subject" name="subject" required>
                                    <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Sélectionnez un sujet</option>
                                    <option value="tutoriel-personnalise" {{ old('subject') == 'tutoriel-personnalise' || request('subject') == 'tutoriel-personnalise' ? 'selected' : '' }}>Demande de tutoriel personnalisé</option>
                                    <option value="formation" {{ old('subject') == 'formation' ? 'selected' : '' }}>Formation n8n</option>
                                    <option value="consultation" {{ old('subject') == 'consultation' ? 'selected' : '' }}>Consultation workflow</option>
                                    <option value="support" {{ old('subject') == 'support' ? 'selected' : '' }}>Support technique</option>
                                    <option value="partenariat" {{ old('subject') == 'partenariat' ? 'selected' : '' }}>Partenariat</option>
                                    <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12" id="tutorialDetails" style="{{ old('subject') == 'tutoriel-personnalise' || request('subject') == 'tutoriel-personnalise' ? '' : 'display: none;' }}">
                                <label for="tutorial_type" class="form-label">Type de tutoriel souhaité</label>
                                <select class="form-select @error('tutorial_type') is-invalid @enderror" id="tutorial_type" name="tutorial_type">
                                    <option value="" {{ old('tutorial_type') ? '' : 'selected' }}>Sélectionnez un type</option>
                                    <option value="integration-specifique" {{ old('tutorial_type') == 'integration-specifique' ? 'selected' : '' }}>Intégration spécifique</option>
                                    <option value="cas-usage-metier" {{ old('tutorial_type') == 'cas-usage-metier' ? 'selected' : '' }}>Cas d'usage métier</option>
                                    <option value="workflow-complexe" {{ old('tutorial_type') == 'workflow-complexe' ? 'selected' : '' }}>Workflow complexe</option>
                                    <option value="optimisation" {{ old('tutorial_type') == 'optimisation' ? 'selected' : '' }}>Optimisation de workflow existant</option>
                                </select>
                                @error('tutorial_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input @error('privacy') is-invalid @enderror" type="checkbox" id="privacy" name="privacy" required {{ old('privacy') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="privacy">
                                        J'accepte que mes données soient traitées conformément à la <a href="{{ route('privacy') }}" target="_blank">politique de confidentialité</a>. <span class="text-danger">*</span>
                                    </label>
                                    @error('privacy')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer le message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h2 class="fw-bold text-infinity-blue mb-4">Informations de contact</h2>
                    <ul class="list-unstyled">
                        <li class="d-flex mb-4">
                            <div class="feature-icon flex-shrink-0 me-3" style="width: 40px; height: 40px; font-size: 1rem; background: linear-gradient(45deg, #b4d8e8, #6ba3c3); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Localisation</h5>
                                <p class="mb-0">Service en ligne<br>Disponible partout en France</p>
                            </div>
                        </li>
                        <li class="d-flex mb-4">
                            <div class="feature-icon flex-shrink-0 me-3" style="width: 40px; height: 40px; font-size: 1rem; background: linear-gradient(45deg, #f3d5d5, #e8b4b4); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Email</h5>
                                <p class="mb-0"><a href="mailto:contact@automatehub.fr" class="text-decoration-none text-dark">contact@automatehub.fr</a></p>
                            </div>
                        </li>
                        <li class="d-flex mb-4">
                            <div class="feature-icon flex-shrink-0 me-3" style="width: 40px; height: 40px; font-size: 1rem; background: linear-gradient(45deg, #a9c2d9, #7a9cb5); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Horaires de réponse</h5>
                                <p class="mb-0">
                                    Lundi - Vendredi: 9h00 - 18h00<br>
                                    Réponse sous 24h maximum
                                </p>
                            </div>
                        </li>
                        <li class="d-flex">
                            <div class="feature-icon flex-shrink-0 me-3" style="width: 40px; height: 40px; font-size: 1rem; background: linear-gradient(45deg, #eca192, #e76f51); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Support</h5>
                                <p class="mb-0">
                                    Support technique gratuit<br>
                                    pour tous nos utilisateurs
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h3 class="fw-bold text-infinity-blue mb-3">Suivez-nous</h3>
                    <div class="d-flex gap-2">
                        <a href="#" class="social-icon" style="width: 40px; height: 40px; background: linear-gradient(45deg, #b4d8e8, #6ba3c3); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: transform 0.3s ease;">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="social-icon" style="width: 40px; height: 40px; background: linear-gradient(45deg, #f3d5d5, #e8b4b4); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: transform 0.3s ease;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="social-icon" style="width: 40px; height: 40px; background: linear-gradient(45deg, #a9c2d9, #7a9cb5); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: transform 0.3s ease;">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="social-icon" style="width: 40px; height: 40px; background: linear-gradient(45deg, #eca192, #e76f51); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; text-decoration: none; transition: transform 0.3s ease;">
                            <i class="fab fa-discord"></i>
                        </a>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm p-4">
                    <h3 class="fw-bold text-infinity-blue mb-3">Communauté n8n</h3>
                    <p class="mb-3">Rejoignez notre communauté d'utilisateurs n8n pour échanger, partager vos workflows et obtenir de l'aide.</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-primary rounded-pill px-3 py-2">
                            <i class="fab fa-discord me-1"></i>Discord
                        </a>
                        <a href="#" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                            <i class="fab fa-github me-1"></i>GitHub
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg overflow-hidden" data-aos="fade-up">
                    <div class="row g-0">
                        <div class="col-md-6 p-5 d-flex flex-column justify-content-center" style="background: linear-gradient(45deg, #f3d5d5, #e8b4b4);">
                            <h2 class="fw-bold mb-3 text-white">Besoin d'aide immédiate ?</h2>
                            <p class="mb-4 text-white">Consultez notre documentation complète ou rejoignez notre communauté pour obtenir des réponses rapides à vos questions sur n8n.</p>
                            <div class="d-flex gap-3">
                                <a href="{{ route('tutorials.index') }}" class="btn btn-light btn-lg rounded-pill px-4">
                                    Voir les tutoriels
                                </a>
                                <a href="{{ route('downloads') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                                    Téléchargements
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 p-5 bg-white d-flex flex-column justify-content-center">
                            <div class="text-center">
                                <div class="feature-icon mx-auto mb-4" style="background: linear-gradient(45deg, #b4d8e8, #6ba3c3); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 32px;">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <h3 class="fw-bold text-infinity-blue mb-3">FAQ et Support</h3>
                                <p class="text-muted mb-4">Trouvez rapidement des réponses aux questions les plus fréquentes ou contactez notre équipe de support.</p>
                                <div class="d-flex justify-content-center gap-4 mt-4">
                                    <div class="text-center">
                                        <h4 class="fw-bold text-infinity-blue mb-1">24h</h4>
                                        <small class="text-muted">Temps de réponse</small>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="fw-bold text-infinity-blue mb-1">100%</h4>
                                        <small class="text-muted">Gratuit</small>
                                    </div>
                                    <div class="text-center">
                                        <h4 class="fw-bold text-infinity-blue mb-1">7j/7</h4>
                                        <small class="text-muted">Disponibilité</small>
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

<style>
.social-icon:hover {
    transform: translateY(-3px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .btn-lg {
        font-size: 1rem;
        padding: 0.5rem 1.5rem;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
    
    .card {
        padding: 1.5rem !important;
    }
    
    .feature-icon {
        width: 35px !important;
        height: 35px !important;
        font-size: 0.9rem !important;
    }
    
    .d-flex.gap-3 {
        flex-direction: column;
        gap: 1rem !important;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Afficher/masquer les détails du tutoriel en fonction du sujet choisi
        const subjectSelect = document.getElementById('subject');
        const tutorialDetails = document.getElementById('tutorialDetails');
        
        subjectSelect.addEventListener('change', function() {
            if (this.value === 'tutoriel-personnalise') {
                tutorialDetails.style.display = 'block';
            } else {
                tutorialDetails.style.display = 'none';
            }
        });
        
        // Validation du formulaire
        const forms = document.querySelectorAll('.needs-validation');
        
        Array.from(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
@endsection
