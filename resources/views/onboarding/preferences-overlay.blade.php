@extends('layouts.backend')

@section('title', 'Préférences - AutomateHub')

@section('css')
<style>
/* Modal overlay styling */
.onboarding-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    display: flex;
    align-items: center;
    justify-content: center;
}

.onboarding-modal {
    position: relative;
    width: 90%;
    max-width: 700px;
    z-index: 1050;
}

.block-mode-shadow-none {
    box-shadow: none;
    border: 1px solid #e6ebf1;
}

.block-mode-shadow-none:hover {
    border-color: #d1d8e0;
    background-color: #f8f9fa;
}

.form-check-input {
    width: 2em;
    height: 1em;
    margin-top: 0.25em;
}

.form-check-label {
    padding-left: 0.5rem;
}
</style>
@endsection

@section('content')
<!-- Page content (visible en arrière-plan) -->
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="block">
                <div class="block-header">
                    <h3 class="block-title">Tableau de bord</h3>
                </div>
                <div class="block-content">
                    <p>Bienvenue sur votre espace AutomateHub...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Onboarding Overlay -->
<div class="onboarding-overlay">
    <div class="onboarding-modal">
        <div class="block block-rounded block-transparent mb-0">
            <div class="block-header bg-primary-dark">
                <h3 class="block-title text-white">
                    <i class="fa fa-envelope me-2"></i>
                    Préférences de communication
                </h3>
                <div class="block-options">
                    <span class="text-white-50">Étape 2 sur 2</span>
                </div>
            </div>
            <div class="block-content bg-white">
                <!-- Progress Bar -->
                <div class="progress push mb-4" style="height: 8px;">
                    <div class="progress-bar bg-primary" style="width: 100%;"></div>
                </div>

                <!-- Welcome Message -->
                <div class="text-center mb-5">
                    <h4 class="mb-3">Dernière étape ! 📧</h4>
                    <p class="text-muted fs-5">
                        Comment souhaitez-vous recevoir les nouveautés d'AutomateHub ?
                    </p>
                </div>

                <!-- Preferences Form -->
                <form method="POST" action="{{ route('onboarding.update-preferences') }}" id="preferences-form">
                    @csrf
                    
                    <!-- Email Notifications -->
                    <div class="mb-4">
                        <div class="block block-rounded block-mode-shadow-none mb-3">
                            <div class="block-content">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="email_notifications" 
                                           name="email_notifications" 
                                           value="1"
                                           checked>
                                    <label class="form-check-label" for="email_notifications">
                                        <span class="fw-semibold">Notifications importantes</span>
                                        <div class="text-muted small">
                                            <i class="fa fa-bell opacity-50 me-1"></i>
                                            Nouveaux workflows, mises à jour critiques, informations compte
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Weekly Newsletter -->
                        <div class="block block-rounded block-mode-shadow-none">
                            <div class="block-content">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="weekly_digest" 
                                           name="weekly_digest" 
                                           value="1"
                                           checked>
                                    <label class="form-check-label" for="weekly_digest">
                                        <span class="fw-semibold">Newsletter hebdomadaire</span>
                                        <div class="text-muted small">
                                            <i class="fa fa-newspaper opacity-50 me-1"></i>
                                            Récapitulatif des nouveautés de la semaine
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Premium Teaser -->
                    <div class="bg-primary-lighter rounded p-3 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <i class="fa fa-star fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">Passez à Premium</h5>
                                <p class="mb-0 text-muted small">
                                    Accédez à tous les workflows premium et au support prioritaire
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <div class="flex-shrink-0">
                            <i class="fa fa-fw fa-info-circle"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-0">
                                Vous pourrez modifier ces préférences à tout moment depuis votre profil.
                            </p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="block-content block-content-full text-end bg-body">
                <button type="submit" form="preferences-form" class="btn btn-success">
                    <i class="fa fa-check me-1"></i>
                    Terminer et découvrir les workflows
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- jQuery -->
<script src="{{ asset('oneui/js/lib/jquery.min.js') }}"></script>
<!-- OneUI Core JS -->
<script src="{{ asset('oneui/js/oneui.app.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Utiliser jQuery après qu'il soit chargé
    if (typeof jQuery !== 'undefined') {
        jQuery(function() {
            // Prevent clicks on overlay from propagating
            jQuery('.onboarding-overlay').on('click', function(e) {
                if (e.target === this) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });
    }
});
</script>
@endsection