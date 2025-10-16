@extends('layouts.backend')

@section('title', 'Analytics Dashboard - Automatehub')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          Analytics Dashboard
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Analyse des performances et statistiques détaillées
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Analytics
          </li>
        </ol>
      </nav>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">
  <!-- General Metrics -->
  <div class="row">
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="{{ route('admin.users.index') }}">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-primary">{{ $generalMetrics['total_users'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Utilisateurs Total</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="{{ route('admin.tutorials.index') }}">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-success">{{ $generalMetrics['total_tutorials'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Tutoriels</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-info">{{ $generalMetrics['total_downloads'] }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Téléchargements</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="{{ route('admin.analytics.revenue') }}">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-warning">{{ number_format($generalMetrics['total_revenue'], 2) }}€</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Revenus</div>
        </div>
      </a>
    </div>
  </div>
  <!-- END General Metrics -->

  <!-- Growth Metrics -->
  <div class="row">
    <div class="col-xl-8">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Croissance (30 derniers jours)</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
          </div>
        </div>
        <div class="block-content block-content-full">
          <div class="row">
            <div class="col-md-4">
              <div class="text-center">
                <div class="fs-sm fw-semibold text-uppercase text-muted">Utilisateurs</div>
                <div class="fs-3 fw-semibold text-{{ $growthMetrics['users_growth'] >= 0 ? 'success' : 'danger' }}">
                  {{ $growthMetrics['users_growth'] > 0 ? '+' : '' }}{{ $growthMetrics['users_growth'] }}%
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="text-center">
                <div class="fs-sm fw-semibold text-uppercase text-muted">Téléchargements</div>
                <div class="fs-3 fw-semibold text-{{ $growthMetrics['downloads_growth'] >= 0 ? 'success' : 'danger' }}">
                  {{ $growthMetrics['downloads_growth'] > 0 ? '+' : '' }}{{ $growthMetrics['downloads_growth'] }}%
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="text-center">
                <div class="fs-sm fw-semibold text-uppercase text-muted">Tutoriels</div>
                <div class="fs-3 fw-semibold text-{{ $growthMetrics['tutorials_growth'] >= 0 ? 'success' : 'danger' }}">
                  {{ $growthMetrics['tutorials_growth'] > 0 ? '+' : '' }}{{ $growthMetrics['tutorials_growth'] }}%
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Actions Rapides</h3>
        </div>
        <div class="block-content">
          <div class="d-grid gap-2">
            <a class="btn btn-primary" href="{{ route('admin.analytics.users') }}">
              <i class="fa fa-users me-1"></i> Analytics Utilisateurs
            </a>
            <a class="btn btn-success" href="{{ route('admin.analytics.content') }}">
              <i class="fa fa-chart-bar me-1"></i> Performance Contenu
            </a>
            <a class="btn btn-info" href="{{ route('admin.analytics.conversions') }}">
              <i class="fa fa-funnel-dollar me-1"></i> Entonnoir Conversion
            </a>
            <a class="btn btn-warning" href="{{ route('admin.analytics.revenue') }}">
              <i class="fa fa-euro-sign me-1"></i> Analyse Revenus
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Growth Metrics -->

  <!-- Top Performers -->
  <div class="row">
    <div class="col-lg-6">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Top Tutoriels</h3>
          <div class="block-options">
            <a class="btn-block-option" href="{{ route('admin.tutorials.index') }}">
              <i class="si si-arrow-right"></i>
            </a>
          </div>
        </div>
        <div class="block-content">
          @if($topPerformers['top_tutorials']->count() > 0)
            <ul class="list-group list-group-flush">
              @foreach($topPerformers['top_tutorials'] as $tutorial)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold">{{ Str::limit($tutorial->title, 40) }}</div>
                    <div class="fs-sm text-muted">{{ $tutorial->downloads_count }} téléchargements</div>
                  </div>
                  <span class="badge bg-primary rounded-pill">
                    {{ $tutorial->downloads_count }}
                  </span>
                </li>
              @endforeach
            </ul>
          @else
            <div class="text-center py-4">
              <i class="fa fa-graduation-cap fa-2x text-muted mb-3"></i>
              <p class="text-muted">Aucun tutoriel disponible</p>
            </div>
          @endif
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Top Utilisateurs</h3>
          <div class="block-options">
            <a class="btn-block-option" href="{{ route('admin.users.index') }}">
              <i class="si si-arrow-right"></i>
            </a>
          </div>
        </div>
        <div class="block-content">
          @if($topPerformers['top_users']->count() > 0)
            <ul class="list-group list-group-flush">
              @foreach($topPerformers['top_users'] as $user)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                    <div class="fs-sm text-muted">{{ $user->email }}</div>
                  </div>
                  <span class="badge bg-success rounded-pill">
                    {{ $user->downloads_count }} DL
                  </span>
                </li>
              @endforeach
            </ul>
          @else
            <div class="text-center py-4">
              <i class="fa fa-users fa-2x text-muted mb-3"></i>
              <p class="text-muted">Aucun utilisateur actif</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  <!-- END Top Performers -->

  <!-- Charts Section -->
  <div class="row">
    <div class="col-lg-12">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Graphiques de Performance</h3>
          <div class="block-options">
            <div class="dropdown">
              <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" data-bs-toggle="dropdown">
                Période
              </button>
              <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="?period=7">7 jours</a>
                <a class="dropdown-item" href="?period=30">30 jours</a>
                <a class="dropdown-item" href="?period=90">90 jours</a>
              </div>
            </div>
          </div>
        </div>
        <div class="block-content">
          <div class="row">
            <div class="col-md-6">
              <canvas id="userRegistrationsChart" width="400" height="200"></canvas>
              <div class="text-center mt-2">
                <small class="text-muted">Évolution des inscriptions</small>
              </div>
            </div>
            <div class="col-md-6">
              <canvas id="downloadsChart" width="400" height="200"></canvas>
              <div class="text-center mt-2">
                <small class="text-muted">Évolution des téléchargements</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Charts Section -->

  @if($generalMetrics['total_users'] == 0)
  <!-- Empty State -->
  <div class="row">
    <div class="col-lg-12">
      <div class="block block-rounded">
        <div class="block-content text-center py-5">
          <div class="mb-3">
            <i class="fa fa-chart-line fa-3x text-muted"></i>
          </div>
          <h3 class="fw-semibold text-muted">Aucune donnée analytique</h3>
          <p class="text-muted">
            Les analytics apparaîtront une fois que des utilisateurs commenceront à utiliser la plateforme.
          </p>
          <div class="mt-4">
            <a class="btn btn-primary" href="{{ route('admin.users.index') }}">
              <i class="fa fa-users me-1"></i> Gérer les utilisateurs
            </a>
            <a class="btn btn-success ms-2" href="{{ route('admin.tutorials.index') }}">
              <i class="fa fa-graduation-cap me-1"></i> Gérer les tutoriels
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
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration des graphiques
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    };

    // Graphique des inscriptions
    const userCtx = document.getElementById('userRegistrationsChart');
    if (userCtx) {
        new Chart(userCtx, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                datasets: [{
                    label: 'Inscriptions',
                    data: [12, 19, 3, 5, 2, 3, 8],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: chartOptions
        });
    }

    // Graphique des téléchargements
    const downloadCtx = document.getElementById('downloadsChart');
    if (downloadCtx) {
        new Chart(downloadCtx, {
            type: 'bar',
            data: {
                labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                datasets: [{
                    label: 'Téléchargements',
                    data: [65, 59, 80, 81, 56, 55, 40],
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });
    }

    // Actualisation automatique des données toutes les 5 minutes
    setInterval(function() {
        console.log('Actualisation des données analytics...');
        // Ici on pourrait ajouter du JavaScript pour actualiser les données via AJAX
    }, 300000); // 5 minutes
});
</script>
@endsection
