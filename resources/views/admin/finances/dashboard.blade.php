@extends('layouts.backend')

@section('title', 'Tableau de Bord Financier - Automatehub')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          Tableau de Bord Financier
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Suivi des revenus et métriques financières
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item">
            <a class="link-fx" href="#">Finances</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Tableau de Bord
          </li>
        </ol>
      </nav>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">
  <!-- Current Revenue -->
  <div class="row">
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-primary mb-1">{{ number_format($currentRevenue['mrr'], 2) }} €</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Revenu Mensuel (MRR)</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-success mb-1">{{ number_format($currentRevenue['arr'], 2) }} €</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Revenu Annuel (ARR)</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-info mb-1">{{ $currentRevenue['premium_users'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Utilisateurs Premium</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-warning mb-1">{{ $currentRevenue['pro_users'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Utilisateurs Pro</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Current Revenue -->

  <!-- Revenue Evolution & Financial Metrics -->
  <div class="row">
    <div class="col-xl-8">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Évolution des Revenus</h3>
          <div class="block-options">
            <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
              <i class="si si-refresh"></i>
            </button>
          </div>
        </div>
        <div class="block-content block-content-full">
          <div class="py-3">
            <!-- Revenue Chart Container -->
            <canvas id="js-chartjs-revenue-evolution" height="300"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Métriques Financières</h3>
        </div>
        <div class="block-content">
          <div class="py-2">
            <div class="row fs-sm">
              <div class="col-6 mb-3">
                <div class="fw-semibold text-uppercase text-muted">ARPU</div>
                <div class="fs-4 fw-bold">{{ number_format($financialMetrics['arpu'], 2) }} €</div>
                <div class="text-muted">Revenu moyen par utilisateur</div>
              </div>
              <div class="col-6 mb-3">
                <div class="fw-semibold text-uppercase text-muted">LTV</div>
                <div class="fs-4 fw-bold">{{ number_format($financialMetrics['ltv'], 2) }} €</div>
                <div class="text-muted">Valeur à vie d'un client</div>
              </div>
              <div class="col-6 mb-3">
                <div class="fw-semibold text-uppercase text-muted">Taux d'attrition</div>
                <div class="fs-4 fw-bold">{{ number_format($financialMetrics['churn_rate'], 2) }}%</div>
                <div class="text-muted">Taux de désabonnement mensuel</div>
              </div>
              <div class="col-6 mb-3">
                <div class="fw-semibold text-uppercase text-muted">Taux de conversion</div>
                <div class="fs-4 fw-bold">{{ number_format($financialMetrics['conversion_rate'], 2) }}%</div>
                <div class="text-muted">Gratuit vers payant</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Revenue Evolution & Financial Metrics -->

  <!-- Revenue Forecasts -->
  <div class="row">
    <div class="col-xl-12">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Prévisions de Revenus</h3>
        </div>
        <div class="block-content">
          <div class="row text-center">
            <div class="col-md-4">
              <div class="py-3">
                <div class="item item-circle bg-body-light mx-auto">
                  <i class="fa fa-calendar-alt text-primary"></i>
                </div>
                <div class="fs-2 fw-bold mt-3">{{ number_format($forecasts['next_month'], 2) }} €</div>
                <div class="text-muted">Mois prochain</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="py-3">
                <div class="item item-circle bg-body-light mx-auto">
                  <i class="fa fa-calendar-week text-success"></i>
                </div>
                <div class="fs-2 fw-bold mt-3">{{ number_format($forecasts['next_quarter'], 2) }} €</div>
                <div class="text-muted">Trimestre prochain</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="py-3">
                <div class="item item-circle bg-body-light mx-auto">
                  <i class="fa fa-calendar-check text-warning"></i>
                </div>
                <div class="fs-2 fw-bold mt-3">{{ number_format($forecasts['next_year'], 2) }} €</div>
                <div class="text-muted">Année prochaine</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Revenue Forecasts -->

  <!-- Revenue Distribution -->
  <div class="row">
    <div class="col-xl-6">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Répartition des Revenus</h3>
        </div>
        <div class="block-content block-content-full">
          <div class="py-3">
            <!-- Revenue Distribution Chart Container -->
            <canvas id="js-chartjs-revenue-distribution" height="300"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-6">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Actions Rapides</h3>
        </div>
        <div class="block-content">
          <div class="row">
            <div class="col-md-6">
              <a class="block block-rounded block-link-shadow text-center" href="{{ route('admin.finances.transactions') }}">
                <div class="block-content py-5">
                  <div class="item item-circle bg-body-light mx-auto">
                    <i class="fa fa-money-bill text-primary"></i>
                  </div>
                  <div class="fw-semibold mt-3">Transactions</div>
                </div>
              </a>
            </div>
            <div class="col-md-6">
              <a class="block block-rounded block-link-shadow text-center" href="{{ route('admin.finances.invoices') }}">
                <div class="block-content py-5">
                  <div class="item item-circle bg-body-light mx-auto">
                    <i class="fa fa-file-invoice text-success"></i>
                  </div>
                  <div class="fw-semibold mt-3">Factures</div>
                </div>
              </a>
            </div>
            <div class="col-md-6">
              <a class="block block-rounded block-link-shadow text-center" href="{{ route('admin.finances.reports') }}">
                <div class="block-content py-5">
                  <div class="item item-circle bg-body-light mx-auto">
                    <i class="fa fa-chart-line text-warning"></i>
                  </div>
                  <div class="fw-semibold mt-3">Rapports</div>
                </div>
              </a>
            </div>
            <div class="col-md-6">
              <a class="block block-rounded block-link-shadow text-center" href="{{ route('admin.analytics.revenue') }}">
                <div class="block-content py-5">
                  <div class="item item-circle bg-body-light mx-auto">
                    <i class="fa fa-chart-pie text-info"></i>
                  </div>
                  <div class="fw-semibold mt-3">Analyse Détaillée</div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Revenue Distribution -->
</div>
<!-- END Page Content -->
@endsection

@section('js_after')
<script src="{{ asset('js/plugins/chart.js/chart.min.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Revenue Evolution Chart
    const revenueEvolutionData = @json($revenueEvolution);
    
    if (revenueEvolutionData && revenueEvolutionData.length > 0) {
      const dates = revenueEvolutionData.map(item => item.date);
      const revenues = revenueEvolutionData.map(item => item.revenue);
      
      new Chart(document.getElementById('js-chartjs-revenue-evolution'), {
        type: 'line',
        data: {
          labels: dates,
          datasets: [{
            label: 'Revenus (€)',
            fill: true,
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            borderColor: 'rgba(0, 123, 255, 1)',
            pointBackgroundColor: 'rgba(0, 123, 255, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(0, 123, 255, 1)',
            data: revenues
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          tension: 0.4,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return value + ' €';
                }
              }
            }
          }
        }
      });
    }
    
    // Revenue Distribution Chart
    new Chart(document.getElementById('js-chartjs-revenue-distribution'), {
      type: 'pie',
      data: {
        labels: ['Premium', 'Pro'],
        datasets: [{
          data: [
            {{ $currentRevenue['premium_users'] * 19.99 }},
            {{ $currentRevenue['pro_users'] * 49.99 }}
          ],
          backgroundColor: [
            'rgba(23, 162, 184, 0.8)',
            'rgba(255, 193, 7, 0.8)'
          ],
          hoverBackgroundColor: [
            'rgba(23, 162, 184, 1)',
            'rgba(255, 193, 7, 1)'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          tooltip: {
            callbacks: {
              label: function(context) {
                const value = context.raw;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = Math.round((value / total) * 100);
                return `${context.label}: ${value.toFixed(2)} € (${percentage}%)`;
              }
            }
          }
        }
      }
    });
  });
</script>
@endsection
