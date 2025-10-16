@extends('layouts.backend')

@section('title', 'Mon Dashboard - Automatehub')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          Bonjour {{ Auth::user()->first_name }} !
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Bienvenue sur votre espace personnel AutomateHub
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('user.dashboard') }}">Dashboard</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Accueil
          </li>
        </ol>
      </nav>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">
  <!-- Overview -->
  <div class="row">
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="{{ route('user.downloads.index') }}">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-primary">{{ $userStats['total_downloads'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Téléchargements</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="{{ route('user.tutorials.index') }}">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-success">{{ $userStats['tutorials_completed'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Tutoriels Terminés</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="{{ route('user.tutorials.favorites') }}">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-info">{{ $userStats['favorites_count'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Favoris</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="{{ route('user.badges.index') }}">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-warning">{{ $userStats['badges_count'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Badges</div>
        </div>
      </a>
    </div>
  </div>
  <!-- END Overview -->

  <!-- User Info & Progress -->
  <div class="row">
    <div class="col-xl-8">
      <!-- Progress -->
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Ma Progression</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
          </div>
        </div>
        <div class="block-content block-content-full">
          <div class="row">
            <div class="col-md-6">
              <div class="fs-sm fw-semibold text-uppercase text-muted">Niveau Actuel</div>
              <div class="fs-3 fw-semibold text-primary">{{ ucfirst($userStats['current_level']) }}</div>
              <div class="progress mt-2" style="height: 8px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $levelProgress['percentage'] }}%"></div>
              </div>
              <div class="fs-sm text-muted mt-1">{{ $levelProgress['completed_tutorials'] }}/{{ $levelProgress['total_tutorials'] }} tutoriels</div>
            </div>
            <div class="col-md-6">
              <div class="fs-sm fw-semibold text-uppercase text-muted">Abonnement</div>
              <div class="fs-3 fw-semibold text-{{ $userStats['subscription_type'] === 'free' ? 'secondary' : 'success' }}">
                {{ ucfirst($userStats['subscription_type']) }}
              </div>
              @if($userStats['subscription_type'] === 'free')
                <a href="{{ route('user.subscription.upgrade') }}" class="btn btn-sm btn-primary mt-2">
                  <i class="fa fa-arrow-up me-1"></i> Passer Premium
                </a>
              @endif
            </div>
          </div>
        </div>
      </div>
      <!-- END Progress -->
    </div>
    <div class="col-xl-4">
      <!-- Quick Stats -->
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Ce Mois-ci</h3>
        </div>
        <div class="block-content">
          <div class="row items-push text-center">
            <div class="col-6">
              <div class="fs-sm fw-semibold text-uppercase text-muted">Téléchargements</div>
              <div class="fs-4 fw-semibold text-primary">{{ $userStats['downloads_this_month'] }}</div>
            </div>
            <div class="col-6">
              <div class="fs-sm fw-semibold text-uppercase text-muted">En Cours</div>
              <div class="fs-4 fw-semibold text-info">{{ $userStats['tutorials_in_progress'] }}</div>
            </div>
          </div>
          <div class="text-center mt-3">
            <div class="fs-sm fw-semibold text-uppercase text-muted">Membre depuis</div>
            <div class="fs-6 fw-semibold text-success">{{ $userStats['days_since_registration'] }} jours</div>
          </div>
        </div>
      </div>
      <!-- END Quick Stats -->
    </div>
  </div>
  <!-- END User Info & Progress -->

  <!-- Recent Activity & Recommendations -->
  <div class="row">
    <div class="col-lg-6">
      <!-- Recent Downloads -->
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Téléchargements Récents</h3>
          <div class="block-options">
            <a class="btn-block-option" href="{{ route('user.downloads.index') }}">
              <i class="si si-arrow-right"></i>
            </a>
          </div>
        </div>
        <div class="block-content">
          @if($recentActivity['recent_downloads']->count() > 0)
            <ul class="list-group list-group-flush">
              @foreach($recentActivity['recent_downloads']->take(5) as $download)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold">{{ $download->tutorial->title ?? 'Tutoriel' }}</div>
                    <div class="fs-sm text-muted">{{ $download->created_at->diffForHumans() }}</div>
                  </div>
                  <span class="badge bg-primary rounded-pill">
                    <i class="fa fa-download"></i>
                  </span>
                </li>
              @endforeach
            </ul>
          @else
            <div class="text-center py-4">
              <i class="fa fa-download fa-2x text-muted mb-3"></i>
              <p class="text-muted">Aucun téléchargement récent</p>
              <a href="{{ route('user.tutorials.index') }}" class="btn btn-sm btn-primary">
                Découvrir les tutoriels
              </a>
            </div>
          @endif
        </div>
      </div>
      <!-- END Recent Downloads -->
    </div>
    <div class="col-lg-6">
      <!-- Recent Progress -->
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Progression Récente</h3>
          <div class="block-options">
            <a class="btn-block-option" href="{{ route('user.tutorials.history') }}">
              <i class="si si-arrow-right"></i>
            </a>
          </div>
        </div>
        <div class="block-content">
          @if($recentActivity['recent_progress']->count() > 0)
            <ul class="list-group list-group-flush">
              @foreach($recentActivity['recent_progress']->take(5) as $progress)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold">{{ $progress->tutorial->title ?? 'Tutoriel' }}</div>
                    <div class="fs-sm text-muted">{{ $progress->updated_at->diffForHumans() }}</div>
                  </div>
                  <span class="badge bg-{{ $progress->completed ? 'success' : 'warning' }} rounded-pill">
                    @if($progress->completed)
                      <i class="fa fa-check"></i> Terminé
                    @else
                      <i class="fa fa-clock"></i> En cours
                    @endif
                  </span>
                </li>
              @endforeach
            </ul>
          @else
            <div class="text-center py-4">
              <i class="fa fa-chart-line fa-2x text-muted mb-3"></i>
              <p class="text-muted">Aucune progression récente</p>
              <a href="{{ route('user.tutorials.index') }}" class="btn btn-sm btn-success">
                Commencer un tutoriel
              </a>
            </div>
          @endif
        </div>
      </div>
      <!-- END Recent Progress -->
    </div>
  </div>
  <!-- END Recent Activity & Recommendations -->

  <!-- Quick Actions -->
  <div class="row">
    <div class="col-lg-12">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Actions Rapides</h3>
        </div>
        <div class="block-content">
          <div class="row">
            <div class="col-md-3 mb-3">
              <a class="btn btn-primary w-100" href="{{ route('user.tutorials.index') }}">
                <i class="fa fa-graduation-cap me-1"></i> Parcourir Tutoriels
              </a>
            </div>
            <div class="col-md-3 mb-3">
              <a class="btn btn-success w-100" href="{{ route('user.downloads.index') }}">
                <i class="fa fa-download me-1"></i> Mes Téléchargements
              </a>
            </div>
            <div class="col-md-3 mb-3">
              <a class="btn btn-info w-100" href="{{ route('user.badges.index') }}">
                <i class="fa fa-trophy me-1"></i> Mes Badges
              </a>
            </div>
            <div class="col-md-3 mb-3">
              <a class="btn btn-warning w-100" href="{{ route('user.profile.index') }}">
                <i class="fa fa-user me-1"></i> Mon Profil
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Quick Actions -->

  @if($userStats['total_downloads'] == 0 && $userStats['tutorials_completed'] == 0)
  <!-- Welcome Message -->
  <div class="row">
    <div class="col-lg-12">
      <div class="block block-rounded">
        <div class="block-content text-center py-5">
          <div class="mb-3">
            <i class="fa fa-rocket fa-3x text-primary"></i>
          </div>
          <h3 class="fw-semibold text-primary">Bienvenue sur AutomateHub !</h3>
          <p class="text-muted">
            Vous venez de rejoindre notre communauté d'automatisation n8n. 
            Commencez par explorer nos tutoriels pour développer vos compétences.
          </p>
          <div class="mt-4">
            <a class="btn btn-primary btn-lg" href="{{ route('user.tutorials.free') }}">
              <i class="fa fa-play me-1"></i> Commencer avec les tutoriels gratuits
            </a>
            @if($userStats['subscription_type'] === 'free')
              <a class="btn btn-success btn-lg ms-2" href="{{ route('user.subscription.upgrade') }}">
                <i class="fa fa-star me-1"></i> Découvrir Premium
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Welcome Message -->
  @endif
</div>
<!-- END Page Content -->

{{-- Modal d'onboarding si nécessaire --}}
@if(isset($showOnboarding) && $showOnboarding)
<div class="onboarding-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.8); z-index: 1040; display: flex; align-items: center; justify-content: center;">
    <div class="onboarding-modal" style="position: relative; width: 90%; max-width: 800px; z-index: 1050;">
        @if($onboardingStep === 'level')
        {{-- Étape 1: Choix du niveau --}}
        <div class="block block-rounded mb-0">
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
                <div class="progress push mb-4" style="height: 8px;">
                    <div class="progress-bar bg-primary" style="width: 50%;"></div>
                </div>
                <div class="text-center mb-5">
                    <h4 class="mb-3">Bonjour {{ auth()->user()->first_name ?? auth()->user()->name }} ! 👋</h4>
                    <p class="text-muted fs-5">
                        Pour personnaliser votre expérience, dites-nous quel est votre niveau avec n8n.
                    </p>
                </div>
                <form method="POST" action="{{ route('onboarding.update-level') }}" id="onboarding-form">
                    @csrf
                    <div class="row g-4 mb-4">
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
        @else
        {{-- Étape 2: Préférences --}}
        <div class="block block-rounded mb-0">
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
                <div class="progress push mb-4" style="height: 8px;">
                    <div class="progress-bar bg-primary" style="width: 100%;"></div>
                </div>
                <div class="text-center mb-5">
                    <h4 class="mb-3">Dernière étape ! 📧</h4>
                    <p class="text-muted fs-5">
                        Comment souhaitez-vous recevoir les nouveautés d'AutomateHub ?
                    </p>
                </div>
                <form method="POST" action="{{ route('onboarding.update-preferences') }}" id="preferences-form">
                    @csrf
                    <div class="mb-4">
                        <div class="block block-rounded block-mode-shadow-none mb-3">
                            <div class="block-content">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1" checked>
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
                        <div class="block block-rounded block-mode-shadow-none">
                            <div class="block-content">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="weekly_digest" name="weekly_digest" value="1" checked>
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
                </form>
            </div>
            <div class="block-content block-content-full text-end bg-body">
                <button type="submit" form="preferences-form" class="btn btn-success">
                    <i class="fa fa-check me-1"></i>
                    Terminer et découvrir les workflows
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
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
.block-mode-shadow-none {
    box-shadow: none;
    border: 1px solid #e6ebf1;
}
.block-mode-shadow-none:hover {
    border-color: #d1d8e0;
    background-color: #f8f9fa;
}
</style>
@endif
@endsection

@section('js_after')
<script>
// Actualisation automatique des statistiques toutes les 5 minutes
setInterval(function() {
    // Ici on pourrait ajouter du JavaScript pour actualiser les données via AJAX
    console.log('Actualisation des statistiques utilisateur...');
}, 300000); // 5 minutes
</script>
@endsection
