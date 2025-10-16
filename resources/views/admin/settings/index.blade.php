@extends('layouts.backend')

@section('title', 'Paramètres Admin - Automatehub')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          Paramètres Système
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Configuration et maintenance de la plateforme
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Paramètres
          </li>
        </ol>
      </nav>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">
  <!-- System Actions -->
  <div class="row">
    <div class="col-lg-6">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Actions Système</h3>
        </div>
        <div class="block-content">
          <div class="row">
            <div class="col-12 mb-3">
              <button type="button" class="btn btn-warning w-100" onclick="clearCache()">
                <i class="fa fa-broom me-1"></i> Vider le Cache
              </button>
            </div>
            <div class="col-12 mb-3">
              <a href="{{ route('admin.settings.logs') }}" class="btn btn-info w-100">
                <i class="fa fa-file-alt me-1"></i> Consulter les Logs
              </a>
            </div>
            <div class="col-12 mb-3">
              <button type="button" class="btn btn-success w-100" onclick="runMaintenance()">
                <i class="fa fa-tools me-1"></i> Maintenance Système
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-6">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Informations Système</h3>
        </div>
        <div class="block-content">
          <table class="table table-borderless table-sm">
            <tbody>
              <tr>
                <td class="fw-semibold">Version Laravel :</td>
                <td>{{ app()->version() }}</td>
              </tr>
              <tr>
                <td class="fw-semibold">Version PHP :</td>
                <td>{{ PHP_VERSION }}</td>
              </tr>
              <tr>
                <td class="fw-semibold">Environnement :</td>
                <td>
                  <span class="badge bg-{{ app()->environment() === 'production' ? 'success' : 'warning' }}">
                    {{ ucfirst(app()->environment()) }}
                  </span>
                </td>
              </tr>
              <tr>
                <td class="fw-semibold">Debug Mode :</td>
                <td>
                  <span class="badge bg-{{ config('app.debug') ? 'danger' : 'success' }}">
                    {{ config('app.debug') ? 'Activé' : 'Désactivé' }}
                  </span>
                </td>
              </tr>
              <tr>
                <td class="fw-semibold">Timezone :</td>
                <td>{{ config('app.timezone') }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!-- END System Actions -->

  <!-- Configuration -->
  <div class="row">
    <div class="col-lg-12">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Configuration Générale</h3>
        </div>
        <div class="block-content">
          <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
              <div class="col-lg-6">
                <div class="mb-4">
                  <label class="form-label" for="site_name">Nom du Site</label>
                  <input type="text" class="form-control" id="site_name" name="site_name" 
                         value="{{ config('app.name') }}" placeholder="AutomateHub">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="mb-4">
                  <label class="form-label" for="site_description">Description du Site</label>
                  <input type="text" class="form-control" id="site_description" name="site_description" 
                         value="Plateforme d'apprentissage n8n" placeholder="Description...">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-6">
                <div class="mb-4">
                  <label class="form-label" for="admin_email">Email Administrateur</label>
                  <input type="email" class="form-control" id="admin_email" name="admin_email" 
                         value="admin@automatehub.fr" placeholder="admin@example.com">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="mb-4">
                  <label class="form-label" for="support_email">Email Support</label>
                  <input type="email" class="form-control" id="support_email" name="support_email" 
                         value="support@automatehub.fr" placeholder="support@example.com">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-4">
                <div class="mb-4">
                  <label class="form-label" for="free_download_limit">Limite Téléchargements Gratuits</label>
                  <input type="number" class="form-control" id="free_download_limit" name="free_download_limit" 
                         value="10" min="1" max="100">
                </div>
              </div>
              <div class="col-lg-4">
                <div class="mb-4">
                  <label class="form-label" for="premium_price">Prix Premium (€)</label>
                  <input type="number" class="form-control" id="premium_price" name="premium_price" 
                         value="19.99" step="0.01" min="0">
                </div>
              </div>
              <div class="col-lg-4">
                <div class="mb-4">
                  <label class="form-label" for="pro_price">Prix Pro (€)</label>
                  <input type="number" class="form-control" id="pro_price" name="pro_price" 
                         value="49.99" step="0.01" min="0">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <div class="mb-4">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode">
                    <label class="form-check-label" for="maintenance_mode">
                      Mode Maintenance
                    </label>
                    <div class="form-text">Activer le mode maintenance pour bloquer l'accès au site</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <div class="mb-4">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="registration_enabled" name="registration_enabled" checked>
                    <label class="form-check-label" for="registration_enabled">
                      Inscriptions Ouvertes
                    </label>
                    <div class="form-text">Permettre aux nouveaux utilisateurs de s'inscrire</div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-save me-1"></i> Sauvegarder les Paramètres
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- END Configuration -->

  <!-- Statistics -->
  <div class="row">
    <div class="col-lg-12">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">Statistiques Rapides</h3>
        </div>
        <div class="block-content">
          <div class="row text-center">
            <div class="col-6 col-lg-3">
              <div class="fs-2 fw-semibold text-primary">{{ \App\Models\User::count() }}</div>
              <div class="fs-sm fw-semibold text-uppercase text-muted">Utilisateurs</div>
            </div>
            <div class="col-6 col-lg-3">
              <div class="fs-2 fw-semibold text-success">{{ \App\Models\Tutorial::count() }}</div>
              <div class="fs-sm fw-semibold text-uppercase text-muted">Tutoriels</div>
            </div>
            <div class="col-6 col-lg-3">
              <div class="fs-2 fw-semibold text-info">{{ \App\Models\Download::count() }}</div>
              <div class="fs-sm fw-semibold text-uppercase text-muted">Téléchargements</div>
            </div>
            <div class="col-6 col-lg-3">
              <div class="fs-2 fw-semibold text-warning">{{ \App\Models\User::whereIn('subscription_type', ['premium', 'pro'])->count() }}</div>
              <div class="fs-sm fw-semibold text-uppercase text-muted">Abonnés Payants</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Statistics -->
</div>
<!-- END Page Content -->
@endsection

@section('js_after')
<script>
function clearCache() {
    if (confirm('Êtes-vous sûr de vouloir vider le cache ?')) {
        fetch('{{ route("admin.settings.cache.clear") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cache vidé avec succès !');
            } else {
                alert('Erreur lors du vidage du cache');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors du vidage du cache');
        });
    }
}

function runMaintenance() {
    if (confirm('Êtes-vous sûr de vouloir lancer la maintenance système ?')) {
        alert('Maintenance système lancée (fonctionnalité à implémenter)');
    }
}
</script>
@endsection
