@extends('layouts.backend')

@section('title', 'Dashboard Admin - Automatehub')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          Dashboard Admin
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Vue d'ensemble de la plateforme AutomateHub
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Dashboard
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
      <a class="block block-rounded block-link-shadow text-center" href="{{ route('admin.users.index') }}">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-primary">{{ $kpis['total_users'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Utilisateurs Total</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="{{ route('admin.users.index') }}">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-success">{{ $kpis['new_users_this_month'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Nouveaux ce mois</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="{{ route('admin.tutorials.index') }}">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-info">{{ $kpis['published_tutorials'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Tutoriels Publiés</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="{{ route('admin.analytics.dashboard') }}">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-warning">{{ $kpis['total_downloads'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Téléchargements</div>
        </div>
      </a>
    </div>
  </div>
  <!-- END Overview -->

  <!-- Statistics -->
  <div class="row">
    <div class="col-xl-8">
      <!-- Earnings -->
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Statistiques Utilisateurs</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
          </div>
        </div>
        <div class="block-content block-content-full">
          <div class="row">
            <div class="col-md-6">
              <div class="fs-sm fw-semibold text-uppercase text-muted">Utilisateurs Premium</div>
              <div class="fs-3 fw-semibold text-success">{{ $kpis['premium_users'] }}</div>
            </div>
            <div class="col-md-6">
              <div class="fs-sm fw-semibold text-uppercase text-muted">Utilisateurs Pro</div>
              <div class="fs-3 fw-semibold text-primary">{{ $kpis['pro_users'] }}</div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-6">
              <div class="fs-sm fw-semibold text-uppercase text-muted">Taux de Conversion</div>
              <div class="fs-3 fw-semibold text-info">{{ $kpis['conversion_rate'] }}%</div>
            </div>
            <div class="col-md-6">
              <div class="fs-sm fw-semibold text-uppercase text-muted">Revenus Mensuels</div>
              <div class="fs-3 fw-semibold text-success">{{ $kpis['monthly_revenue'] }}€</div>
            </div>
          </div>
        </div>
      </div>
      <!-- END Earnings -->
    </div>
    <div class="col-xl-4">
      <!-- Sales -->
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Activité</h3>
        </div>
        <div class="block-content">
          <div class="row items-push text-center">
            <div class="col-6">
              <div class="fs-sm fw-semibold text-uppercase text-muted">Utilisateurs Actifs</div>
              <div class="fs-4 fw-semibold text-primary">{{ $kpis['active_users'] }}</div>
            </div>
            <div class="col-6">
              <div class="fs-sm fw-semibold text-uppercase text-muted">Téléchargements ce mois</div>
              <div class="fs-4 fw-semibold text-success">{{ $kpis['downloads_this_month'] }}</div>
            </div>
          </div>
        </div>
      </div>
      <!-- END Sales -->
    </div>
  </div>
  <!-- END Statistics -->

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
              <a class="btn btn-primary w-100" href="{{ route('admin.tutorials.create') }}">
                <i class="fa fa-plus me-1"></i> Nouveau Tutoriel
              </a>
            </div>
            <div class="col-md-3 mb-3">
              <a class="btn btn-success w-100" href="{{ route('admin.users.index') }}">
                <i class="fa fa-users me-1"></i> Gérer Utilisateurs
              </a>
            </div>
            <div class="col-md-3 mb-3">
              <a class="btn btn-info w-100" href="{{ route('admin.analytics.dashboard') }}">
                <i class="fa fa-chart-bar me-1"></i> Analytics
              </a>
            </div>
            <div class="col-md-3 mb-3">
              <a class="btn btn-warning w-100" href="{{ route('admin.settings.index') }}">
                <i class="fa fa-cog me-1"></i> Paramètres
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Quick Actions -->

  @if($kpis['total_users'] == 0)
  <!-- Empty State -->
  <div class="row">
    <div class="col-lg-12">
      <div class="block block-rounded">
        <div class="block-content text-center py-5">
          <div class="mb-3">
            <i class="fa fa-chart-line fa-3x text-muted"></i>
          </div>
          <h3 class="fw-semibold text-muted">Aucune donnée disponible</h3>
          <p class="text-muted">
            La plateforme vient d'être installée. Les statistiques apparaîtront une fois que des utilisateurs s'inscriront et utiliseront la plateforme.
          </p>
          <div class="mt-4">
            <a class="btn btn-primary" href="{{ route('admin.users.create') }}">
              <i class="fa fa-user-plus me-1"></i> Créer un utilisateur
            </a>
            <a class="btn btn-success ms-2" href="{{ route('admin.tutorials.create') }}">
              <i class="fa fa-plus me-1"></i> Créer un tutoriel
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Empty State -->
  @endif
</div>
<!-- END Page Content -->
@endsection

@section('js_after')
<script>
// Actualisation automatique des KPIs toutes les 5 minutes
setInterval(function() {
    // Ici on pourrait ajouter du JavaScript pour actualiser les données via AJAX
    console.log('Actualisation des KPIs...');
}, 300000); // 5 minutes
</script>
@endsection
