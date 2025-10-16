<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Bienvenue sur AutomateHub</title>
    
    <!-- OneUI CSS -->
    <link rel="stylesheet" id="css-main" href="{{ asset('oneui/css/oneui.min.css') }}">
    <link rel="stylesheet" id="css-theme" href="{{ asset('oneui/css/themes/audelalia.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    
    <style>
    body {
        background-color: #f5f5f5;
    }
    .modal-backdrop-custom {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 1040;
    }
    .modal-custom {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1050;
        width: 90%;
        max-width: 800px;
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
</head>
<body>
    <!-- Backdrop -->
    <div class="modal-backdrop-custom"></div>
    
    <!-- Modal -->
    <div class="modal-custom">
        <div class="modal-content">
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
                <div class="block-content">
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

    <!-- jQuery and OneUI JS -->
    <script src="{{ asset('oneui/js/lib/jquery.min.js') }}"></script>
    <script src="{{ asset('oneui/js/oneui.app.min.js') }}"></script>
</body>
</html>