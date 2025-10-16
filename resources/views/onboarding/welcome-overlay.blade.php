@extends('layouts.backend')

@section('title', 'Bienvenue sur AutomateHub')

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
    max-width: 800px;
    z-index: 1050;
}

.form-selectgroup-item {
    position: relative;
    display: block;
    cursor: pointer;
}

.form-selectgroup-input {
    position: absolute;
    opacity: 0;
    z-index: -1;
}

.form-selectgroup-label {
    border: 2px solid #e6ebf1;
    border-radius: 0.375rem;
    transition: all 0.25s ease-out;
    background: #fff;
    min-height: 250px;
}

.form-selectgroup-input:checked ~ .form-selectgroup-label {
    border-color: #0665d0;
    background-color: #e7f1ff;
    transform: scale(1.02);
    box-shadow: 0 0.375rem 0.75rem rgba(6, 101, 208, 0.15);
}

.form-selectgroup-label:hover {
    border-color: #a5ccec;
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
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
                    <i class="fa fa-magic me-2"></i>
                    Bienvenue sur AutomateHub !
                </h3>
                <div class="block-options">
                    <span class="text-white-50">Étape 1 sur 2</span>
                </div>
            </div>
            <div class="block-content bg-white">
                <!-- Progress Bar -->
                <div class="progress push mb-4" style="height: 8px;">
                    <div class="progress-bar bg-primary" style="width: 50%;"></div>
                </div>

                <!-- Welcome Message -->
                <div class="text-center mb-5">
                    <h4 class="mb-3">Bonjour {{ auth()->user()->first_name ?? auth()->user()->name }} ! 👋</h4>
                    <p class="text-muted fs-5">
                        Pour personnaliser votre expérience, dites-nous quel est votre niveau avec n8n.
                    </p>
                </div>

                <!-- Level Selection Form -->
                <form method="POST" action="{{ route('onboarding.update-level') }}" id="onboarding-form">
                    @csrf
                    <div class="row g-4 mb-4">
                        <!-- Beginner -->
                        <div class="col-md-4">
                            <label class="form-selectgroup-item h-100">
                                <input type="radio" name="level_n8n" value="beginner" class="form-selectgroup-input" required>
                                <div class="form-selectgroup-label d-flex flex-column align-items-center justify-content-center p-4 h-100">
                                    <div class="mb-3">
                                        <i class="fa fa-seedling fa-4x text-success"></i>
                                    </div>
                                    <h5 class="mb-2">Débutant</h5>
                                    <p class="text-muted text-center mb-0 small">
                                        Je découvre n8n et l'automatisation
                                    </p>
                                </div>
                            </label>
                        </div>

                        <!-- Intermediate -->
                        <div class="col-md-4">
                            <label class="form-selectgroup-item h-100">
                                <input type="radio" name="level_n8n" value="intermediate" class="form-selectgroup-input" required>
                                <div class="form-selectgroup-label d-flex flex-column align-items-center justify-content-center p-4 h-100">
                                    <div class="mb-3">
                                        <i class="fa fa-layer-group fa-4x text-primary"></i>
                                    </div>
                                    <h5 class="mb-2">Intermédiaire</h5>
                                    <p class="text-muted text-center mb-0 small">
                                        J'ai déjà créé quelques workflows
                                    </p>
                                </div>
                            </label>
                        </div>

                        <!-- Expert -->
                        <div class="col-md-4">
                            <label class="form-selectgroup-item h-100">
                                <input type="radio" name="level_n8n" value="expert" class="form-selectgroup-input" required>
                                <div class="form-selectgroup-label d-flex flex-column align-items-center justify-content-center p-4 h-100">
                                    <div class="mb-3">
                                        <i class="fa fa-rocket fa-4x text-warning"></i>
                                    </div>
                                    <h5 class="mb-2">Expert</h5>
                                    <p class="text-muted text-center mb-0 small">
                                        Je maîtrise n8n et ses concepts
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="block-content block-content-full text-end bg-body">
                <button type="submit" form="onboarding-form" class="btn btn-primary">
                    <i class="fa fa-arrow-right me-1"></i>
                    Continuer
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