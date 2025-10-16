@extends('layouts.backend')

@section('title', 'Tableau de bord - Automatehub')
@section('meta_description', 'Tableau de bord utilisateur Automatehub')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
    <div class="content content-full">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
            <div class="flex-grow-1">
                <h1 class="h3 fw-bold mb-1">
                    Bienvenue, {{ Auth::user()->first_name }} !
                </h1>
                <h2 class="fs-base lh-base fw-medium text-muted mb-0">
                    Votre espace d'apprentissage n8n personnalisé
                </h2>
            </div>
            <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-alt">
                    <li class="breadcrumb-item">
                        <a class="link-fx" href="{{ route('home') }}">Automatehub</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">
                        Tableau de bord
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">
    <!-- Quiz Alert -->
    @if(!Auth::user()->quiz_completed_at)
        <div class="block block-rounded">
            <div class="block-content">
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="fa fa-fw fa-brain fa-2x me-3"></i>
                    <div class="flex-grow-1">
                        <h4 class="alert-heading mb-1">Évaluez votre niveau n8n !</h4>
                        <p class="mb-0">Répondez à notre quiz pour personnaliser votre expérience et obtenir vos premiers badges.</p>
                    </div>
                    <button type="button" class="btn btn-primary ms-3" id="start-quiz-btn">
                        <i class="fa fa-play me-1"></i>Commencer le quiz
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="block block-rounded">
            <div class="block-content">
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fa fa-fw fa-check-circle fa-2x me-3"></i>
                    <div class="flex-grow-1">
                        <h4 class="alert-heading mb-1">Quiz complété !</h4>
                        <p class="mb-0">Votre niveau n8n : <strong>{{ ucfirst(Auth::user()->level_n8n) }}</strong> - Complété le {{ \Carbon\Carbon::parse(Auth::user()->quiz_completed_at)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Row -->
    <div class="row">
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="#">
                <div class="block-content block-content-full">
                    <div class="fs-2 fw-semibold text-primary">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div class="fs-1 fw-bold">5</div>
                    <div class="text-muted">Tutoriels consultés</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="#">
                <div class="block-content block-content-full">
                    <div class="fs-2 fw-semibold text-success">
                        <i class="fa fa-download"></i>
                    </div>
                    <div class="fs-1 fw-bold">3</div>
                    <div class="text-muted">Workflows téléchargés</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="#">
                <div class="block-content block-content-full">
                    <div class="fs-2 fw-semibold text-warning">
                        <i class="fa fa-star"></i>
                    </div>
                    <div class="fs-1 fw-bold">2</div>
                    <div class="text-muted">Favoris</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="#">
                <div class="block-content block-content-full">
                    <div class="fs-2 fw-semibold text-info">
                        <i class="fa fa-trophy"></i>
                    </div>
                    <div class="fs-1 fw-bold">{{ Auth::user()->badges()->count() }}</div>
                    <div class="text-muted">Badges obtenus</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <div class="col-md-8">
            <!-- Recent Activity -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-fw fa-clock me-1"></i>
                        Activité récente
                    </h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
                    </div>
                </div>
                <div class="block-content">
                    <div class="timeline timeline-alt py-0">
                        <div class="timeline-item">
                            <div class="timeline-point timeline-point-success">
                                <i class="fa fa-graduation-cap"></i>
                            </div>
                            <div class="timeline-event">
                                <div class="timeline-heading">
                                    <h4 class="timeline-title">Tutoriel complété</h4>
                                    <p class="fs-sm fw-semibold text-muted">Il y a 2 heures</p>
                                </div>
                                <p class="fw-medium">Introduction à n8n - Les bases</p>
                                <p class="fs-sm text-muted">Vous avez terminé ce tutoriel avec succès et gagné le badge "Premier pas".</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-point timeline-point-primary">
                                <i class="fa fa-download"></i>
                            </div>
                            <div class="timeline-event">
                                <div class="timeline-heading">
                                    <h4 class="timeline-title">Workflow téléchargé</h4>
                                    <p class="fs-sm fw-semibold text-muted">Hier</p>
                                </div>
                                <p class="fw-medium">Automatisation Email Marketing</p>
                                <p class="fs-sm text-muted">Workflow pour automatiser vos campagnes email.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-point timeline-point-warning">
                                <i class="fa fa-trophy"></i>
                            </div>
                            <div class="timeline-event">
                                <div class="timeline-heading">
                                    <h4 class="timeline-title">Badge obtenu</h4>
                                    <p class="fs-sm fw-semibold text-muted">Il y a 3 jours</p>
                                </div>
                                <p class="fw-medium">Badge "Explorateur"</p>
                                <p class="fs-sm text-muted">Pour avoir exploré 5 tutoriels différents.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Recommended Tutorials -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-fw fa-lightbulb me-1"></i>
                        Recommandés pour vous
                    </h3>
                </div>
                <div class="block-content">
                    <div class="list-group list-group-flush">
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-start" href="#">
                            <div class="me-auto">
                                <div class="fw-semibold">Automatisation des emails</div>
                                <p class="fs-sm text-muted mb-0">Apprenez à automatiser l'envoi d'emails avec n8n</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">Nouveau</span>
                        </a>
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-start" href="#">
                            <div class="me-auto">
                                <div class="fw-semibold">Intégration Slack</div>
                                <p class="fs-sm text-muted mb-0">Connectez vos workflows à Slack</p>
                            </div>
                            <span class="badge bg-success rounded-pill">Populaire</span>
                        </a>
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-start" href="#">
                            <div class="me-auto">
                                <div class="fw-semibold">Base de données</div>
                                <p class="fs-sm text-muted mb-0">Manipulez vos données avec n8n</p>
                            </div>
                        </a>
                    </div>
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-primary">
                            <i class="fa fa-fw fa-graduation-cap me-1"></i> Voir tous les tutoriels
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-fw fa-bolt me-1"></i>
                        Actions rapides
                    </h3>
                </div>
                <div class="block-content">
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fa fa-plus me-1"></i> Nouveau workflow
                        </a>
                        <a href="#" class="btn btn-outline-success">
                            <i class="fa fa-search me-1"></i> Explorer les templates
                        </a>
                        <a href="#" class="btn btn-outline-info">
                            <i class="fa fa-question-circle me-1"></i> Aide & Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quiz Modal -->
<div class="modal fade" id="quiz-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="z-index: 1060;">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content" style="background: linear-gradient(135deg, #6ba3c3 0%, #b4d8e8 100%);">
            <div class="modal-body p-0 d-flex flex-column">
                <!-- Header avec navigation par étapes -->
                <div class="quiz-header p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button type="button" class="btn btn-link text-white p-0" id="quiz-back-btn" style="display: none;">
                            <i class="fas fa-arrow-left fa-lg"></i>
                        </button>
                        <h4 class="text-white mb-0">Quiz d'évaluation n8n</h4>
                        <button type="button" class="btn btn-link text-white p-0" data-bs-dismiss="modal">
                            <i class="fas fa-times fa-lg"></i>
                        </button>
                    </div>
                    
                    <!-- Progression avec étapes -->
                    <div>
                        <div class="progress mb-3" style="height: 8px; background-color: rgba(255,255,255,0.3);">
                            <div class="progress-bar" id="quiz-progress" role="progressbar" style="width: 20%; background-color: white;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <nav class="d-flex flex-column flex-lg-row items-center justify-content-between gap-2" id="quiz-steps">
                            <div class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-3" style="border: 1px solid rgba(255,255,255,0.3);">
                                <div class="flex-grow-0 rounded-circle bg-white text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: bold;">
                                    1
                                </div>
                                <div class="flex-grow-1 text-white">
                                    <div>Question 1</div>
                                    <div class="fw-normal opacity-75">Niveau de base</div>
                                </div>
                            </div>
                            <div class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-3" style="border: 1px solid rgba(255,255,255,0.3);">
                                <div class="flex-grow-0 rounded-circle border border-2 border-white d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; font-weight: bold;">
                                    2
                                </div>
                                <div class="flex-grow-1 text-white opacity-50">
                                    <div>Question 2</div>
                                    <div class="fw-normal">Niveau de base</div>
                                </div>
                            </div>
                            <div class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-3" style="border: 1px solid rgba(255,255,255,0.3);">
                                <div class="flex-grow-0 rounded-circle border border-2 border-white d-flex align-items-center justify-content-center text-white opacity-50" style="width: 32px; height: 32px; font-weight: bold;">
                                    3
                                </div>
                                <div class="flex-grow-1 text-white opacity-50">
                                    <div>Question 3</div>
                                    <div class="fw-normal">Niveau intermédiaire</div>
                                </div>
                            </div>
                            <div class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-3" style="border: 1px solid rgba(255,255,255,0.3);">
                                <div class="flex-grow-0 rounded-circle border border-2 border-white d-flex align-items-center justify-content-center text-white opacity-50" style="width: 32px; height: 32px; font-weight: bold;">
                                    4
                                </div>
                                <div class="flex-grow-1 text-white opacity-50">
                                    <div>Question 4</div>
                                    <div class="fw-normal">Niveau intermédiaire</div>
                                </div>
                            </div>
                            <div class="btn btn-lg btn-alt-secondary bg-transparent w-100 text-start fs-sm d-flex align-items-center justify-content-between gap-3" style="border: 1px solid rgba(255,255,255,0.3);">
                                <div class="flex-grow-0 rounded-circle border border-2 border-white d-flex align-items-center justify-content-center text-white opacity-50" style="width: 32px; height: 32px; font-weight: bold;">
                                    5
                                </div>
                                <div class="flex-grow-1 text-white opacity-50">
                                    <div>Question 5</div>
                                    <div class="fw-normal">Niveau expert</div>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
                
                <!-- Contenu du quiz -->
                <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                    <div class="quiz-container" style="max-width: 600px; width: 100%;">
                        <!-- Questions du quiz seront injectées ici -->
                        <div id="quiz-questions"></div>
                        
                        <!-- Écran de résultats -->
                        <div id="quiz-results" style="display: none;" class="text-center text-white">
                            <div class="mb-4">
                                <i class="fas fa-trophy fa-4x text-warning mb-3"></i>
                                <h2>Félicitations !</h2>
                                <p class="lead">Vous avez terminé le quiz d'évaluation n8n</p>
                            </div>
                            <div id="quiz-score" class="mb-4"></div>
                            <button type="button" class="btn btn-light btn-lg" data-bs-dismiss="modal">
                                <i class="fas fa-check me-2"></i>Continuer vers le dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_after')
<style>
    /* Overlay avec opacité 80% */
    .modal-backdrop {
        opacity: 0.8 !important;
    }
    
    /* Z-index pour éviter les conflits entre modals */
    #quiz-modal {
        z-index: 1060 !important;
    }
    
    .quiz-question {
        background: white;
        border-radius: 1rem;
        padding: 3rem;
        margin: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        text-align: center;
        opacity: 0;
        transform: translateX(100px);
        transition: all 0.5s ease;
    }
    
    .quiz-question.active {
        opacity: 1;
        transform: translateX(0);
    }
    
    .quiz-question.prev {
        transform: translateX(-100px);
    }
    
    .quiz-option {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 0.75rem;
        padding: 1rem 1.5rem;
        margin: 0.75rem 0;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: left;
    }
    
    .quiz-option:hover {
        background: #6ba3c3;
        color: white;
        border-color: #6ba3c3;
        transform: translateX(10px);
    }
    
    .quiz-option.selected {
        background: #6ba3c3;
        color: white;
        border-color: #6ba3c3;
    }
    
    .quiz-question h3 {
        color: #6ba3c3;
        font-weight: 700;
        margin-bottom: 2rem;
    }
</style>

<script>
jQuery(function () {
    let currentQuestion = 0;
    let questions = [];
    let answers = {};
    
    // Afficher le quiz si demandé
    @if(session('show_quiz') && !Auth::user()->quiz_completed_at)
        setTimeout(function() {
            jQuery('#start-quiz-btn').click();
        }, 1000);
    @endif
    
    // Démarrer le quiz
    jQuery('#start-quiz-btn').on('click', function() {
        loadQuiz();
    });
    
    // Bouton retour
    jQuery('#quiz-back-btn').on('click', function() {
        if (currentQuestion > 0) {
            currentQuestion--;
            showQuestion(currentQuestion);
            updateProgress();
            updateBackButton();
        }
    });
    
    // Charger les questions du quiz
    function loadQuiz() {
        jQuery.get('{{ route("quiz.questions") }}')
            .done(function(response) {
                if (response.success) {
                    questions = response.questions;
                    currentQuestion = 0;
                    answers = {};
                    renderQuestions();
                    jQuery('#quiz-modal').modal('show');
                    showQuestion(0);
                    updateProgress();
                    updateBackButton();
                } else {
                    alert('Erreur lors du chargement du quiz');
                }
            })
            .fail(function() {
                alert('Erreur lors du chargement du quiz');
            });
    }
    
    // Rendre les questions
    function renderQuestions() {
        let html = '';
        questions.forEach(function(question, index) {
            html += `
                <div class="quiz-question" data-question="${index}">
                    <h3>Question ${index + 1} sur ${questions.length}</h3>
                    <h4 class="mb-4">${question.question}</h4>
                    <div class="quiz-options">
            `;
            
            Object.keys(question.options).forEach(function(key) {
                html += `
                    <div class="quiz-option" data-answer="${key}">
                        <strong>${key}.</strong> ${question.options[key]}
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        });
        
        jQuery('#quiz-questions').html(html);
        
        // Gérer les clics sur les options
        jQuery('.quiz-option').on('click', function() {
            const questionIndex = jQuery(this).closest('.quiz-question').data('question');
            const answer = jQuery(this).data('answer');
            
            // Marquer la réponse sélectionnée
            jQuery(this).siblings().removeClass('selected');
            jQuery(this).addClass('selected');
            
            // Sauvegarder la réponse
            answers[questionIndex] = answer;
            
            // Passer à la question suivante après un délai
            setTimeout(function() {
                if (currentQuestion < questions.length - 1) {
                    currentQuestion++;
                    showQuestion(currentQuestion);
                    updateProgress();
                    updateBackButton();
                } else {
                    // Soumettre le quiz
                    submitQuiz();
                }
            }, 500);
        });
    }
    
    // Afficher une question spécifique
    function showQuestion(index) {
        jQuery('.quiz-question').removeClass('active prev');
        jQuery('.quiz-question').each(function(i) {
            if (i < index) {
                jQuery(this).addClass('prev');
            } else if (i === index) {
                jQuery(this).addClass('active');
            }
        });
        
        // Restaurer la sélection si elle existe
        if (answers[index]) {
            jQuery(`.quiz-question[data-question="${index}"] .quiz-option[data-answer="${answers[index]}"]`).addClass('selected');
        }
    }
    
    // Mettre à jour la barre de progression
    function updateProgress() {
        const progress = ((currentQuestion + 1) / questions.length) * 100;
        jQuery('#quiz-progress').css('width', progress + '%').attr('aria-valuenow', progress);
        updateStepsVisual();
    }
    
    // Mettre à jour l'affichage visuel des étapes
    function updateStepsVisual() {
        jQuery('#quiz-steps > div').each(function(index) {
            const stepCircle = jQuery(this).find('.rounded-circle');
            const stepText = jQuery(this).find('.flex-grow-1');
            
            if (index < currentQuestion) {
                // Étape complétée
                stepCircle.removeClass('border border-2 border-white bg-white text-primary')
                          .addClass('bg-white text-primary')
                          .html('<i class="fa fa-fw fa-check"></i>');
                stepText.removeClass('opacity-50').addClass('text-white');
            } else if (index === currentQuestion) {
                // Étape actuelle
                stepCircle.removeClass('bg-white text-primary')
                          .addClass('border border-2 border-white text-white')
                          .html(index + 1);
                stepText.removeClass('opacity-50').addClass('text-white');
            } else {
                // Étape future
                stepCircle.removeClass('bg-white text-primary border border-2 border-white')
                          .addClass('border border-2 border-white text-white opacity-50')
                          .html(index + 1);
                stepText.addClass('opacity-50').removeClass('text-white');
            }
        });
    }
    
    // Mettre à jour le bouton retour
    function updateBackButton() {
        if (currentQuestion > 0) {
            jQuery('#quiz-back-btn').show();
        } else {
            jQuery('#quiz-back-btn').hide();
        }
    }
    
    // Soumettre le quiz
    function submitQuiz() {
        const answersArray = [];
        for (let i = 0; i < questions.length; i++) {
            answersArray[i] = answers[i];
        }
        
        jQuery.post('{{ route("quiz.submit") }}', {
            answers: answersArray,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                showResults(response);
            } else {
                alert('Erreur lors de la soumission du quiz');
            }
        })
        .fail(function() {
            alert('Erreur lors de la soumission du quiz');
        });
    }
    
    // Afficher les résultats
    function showResults(response) {
        jQuery('#quiz-questions').hide();
        jQuery('#quiz-back-btn').hide();
        
        let levelText = '';
        switch(response.level) {
            case 'beginner':
                levelText = 'Débutant';
                break;
            case 'intermediate':
                levelText = 'Intermédiaire';
                break;
            case 'expert':
                levelText = 'Expert';
                break;
        }
        
        jQuery('#quiz-score').html(`
            <div class="card bg-white text-dark p-4 mb-3">
                <h4>Votre niveau n8n : <span class="text-primary">${levelText}</span></h4>
                <p>Score : ${response.score.score}/${response.score.total} (${response.score.percentage}%)</p>
                <p class="mb-0">Vos badges ont été attribués automatiquement !</p>
            </div>
        `);
        
        jQuery('#quiz-results').show();
        
        // Mettre à jour la barre de progression à 100%
        jQuery('#quiz-progress').css('width', '100%').attr('aria-valuenow', 100);
    }
    
    // Recharger la page quand le modal se ferme après le quiz
    jQuery('#quiz-modal').on('hidden.bs.modal', function() {
        if (jQuery('#quiz-results').is(':visible')) {
            location.reload();
        }
    });
});
</script>
@endsection
