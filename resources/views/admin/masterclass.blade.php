@extends('layouts.backend')

@section('title', 'n8n MasterClass')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          🎓 n8n MasterClass
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Formation complète : De Zéro à Expert en 40 leçons
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Dashboard</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            MasterClass
          </li>
        </ol>
      </nav>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">
  <!-- Stats Overview -->
  <div class="row">
    <div class="col-6 col-xl-3">
      <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
          <div>
            <i class="fa fa-2x fa-graduation-cap text-primary"></i>
          </div>
          <div class="ms-3 text-end">
            <p class="text-muted mb-0">
              Total Modules
            </p>
            <p class="fs-3 fw-bold text-dark mb-0">
              10
            </p>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-xl-3">
      <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
          <div>
            <i class="fa fa-2x fa-play-circle text-success"></i>
          </div>
          <div class="ms-3 text-end">
            <p class="text-muted mb-0">
              Total Leçons
            </p>
            <p class="fs-3 fw-bold text-dark mb-0">
              40
            </p>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-xl-3">
      <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
          <div>
            <i class="fa fa-2x fa-cogs text-warning"></i>
          </div>
          <div class="ms-3 text-end">
            <p class="text-muted mb-0">
              Workflows Créés
            </p>
            <p class="fs-3 fw-bold text-dark mb-0">
              8
            </p>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-xl-3">
      <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
        <div class="block-content block-content-full d-flex align-items-center justify-content-between">
          <div>
            <i class="fa fa-2x fa-medal text-info"></i>
          </div>
          <div class="ms-3 text-end">
            <p class="text-muted mb-0">
              Badges Disponibles
            </p>
            <p class="fs-3 fw-bold text-dark mb-0">
              10
            </p>
          </div>
        </div>
      </a>
    </div>
  </div>
  <!-- END Stats Overview -->

  <!-- Course Progress -->
  <div class="row">
    <div class="col-12">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">
            <i class="fa fa-chart-line me-1 text-muted"></i>
            Progression Globale
          </h3>
          <div class="block-options">
            <button type="button" class="btn-block-option">
              <i class="si si-settings"></i>
            </button>
          </div>
        </div>
        <div class="block-content">
          <div class="row">
            <div class="col-lg-8">
              <!-- Progress Bars -->
              <div class="mb-4">
                <div class="d-flex justify-content-between mb-1">
                  <span class="fw-semibold">Workflows créés</span>
                  <span class="text-muted">8/40 (20%)</span>
                </div>
                <div class="progress" style="height: 8px;">
                  <div class="progress-bar bg-success" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <div class="mb-4">
                <div class="d-flex justify-content-between mb-1">
                  <span class="fw-semibold">Documentation rédigée</span>
                  <span class="text-muted">8/40 (20%)</span>
                </div>
                <div class="progress" style="height: 8px;">
                  <div class="progress-bar bg-info" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <div class="mb-4">
                <div class="d-flex justify-content-between mb-1">
                  <span class="fw-semibold">Scripts vidéo écrits</span>
                  <span class="text-muted">8/40 (20%)</span>
                </div>
                <div class="progress" style="height: 8px;">
                  <div class="progress-bar bg-warning" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <div class="mb-4">
                <div class="d-flex justify-content-between mb-1">
                  <span class="fw-semibold">Vidéos enregistrées</span>
                  <span class="text-muted">0/40 (0%)</span>
                </div>
                <div class="progress" style="height: 8px;">
                  <div class="progress-bar bg-danger" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <!-- Overall Progress Circle -->
              <div class="text-center">
                <div class="progress-circle" data-percent="20">
                  <span class="progress-circle-text">
                    <span class="fs-2 fw-bold text-dark">20%</span><br>
                    <span class="text-muted">Complété</span>
                  </span>
                </div>
                <p class="text-muted mt-3">
                  Modules 1 et 2 terminés<br>
                  <strong>Prêt pour Module 3</strong>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Course Progress -->

  <!-- Modules Grid -->
  <div class="row">
    <!-- Module 1 -->
    <div class="col-lg-6">
      <div class="block block-rounded">
        <div class="block-header bg-success text-white">
          <h3 class="block-title">
            🟢 Module 1 : FONDATIONS n8n
          </h3>
          <div class="block-options">
            <span class="badge bg-light text-success">✅ Complet</span>
          </div>
        </div>
        <div class="block-content">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <span class="badge bg-success">n8n Rookie 🌱</span>
              <span class="text-muted ms-2">2h00 • 4 leçons</span>
            </div>
            <div class="text-end">
              <span class="fs-3 fw-bold text-success">100%</span>
            </div>
          </div>
          
          <!-- Lessons List -->
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              <div>
                <i class="fa fa-check-circle text-success me-2"></i>
                <span class="fw-medium">1.1 Introduction à l'automatisation</span>
                <br><small class="text-muted">Workflow: 5EzMaKRFXnkgLPOU</small>
              </div>
              <div class="text-end">
                <a href="https://n8n.automatehub.fr/workflow/5EzMaKRFXnkgLPOU" target="_blank" class="btn btn-sm btn-outline-primary">
                  <i class="fa fa-external-link-alt"></i>
                </a>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              <div>
                <i class="fa fa-check-circle text-success me-2"></i>
                <span class="fw-medium">1.2 Votre premier workflow</span>
                <br><small class="text-muted">Workflow: D6Hr4ejEx0Vi4Wxv</small>
              </div>
              <div class="text-end">
                <a href="https://n8n.automatehub.fr/workflow/D6Hr4ejEx0Vi4Wxv" target="_blank" class="btn btn-sm btn-outline-primary">
                  <i class="fa fa-external-link-alt"></i>
                </a>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              <div>
                <i class="fa fa-check-circle text-success me-2"></i>
                <span class="fw-medium">1.3 Manipulation de données</span>
                <br><small class="text-muted">Workflow: ILZSwYquB03Pr6Vq</small>
              </div>
              <div class="text-end">
                <a href="https://n8n.automatehub.fr/workflow/ILZSwYquB03Pr6Vq" target="_blank" class="btn btn-sm btn-outline-primary">
                  <i class="fa fa-external-link-alt"></i>
                </a>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              <div>
                <i class="fa fa-check-circle text-success me-2"></i>
                <span class="fw-medium">1.4 Connexions et authentification</span>
                <br><small class="text-muted">Workflow: lJmYm4j8024M57rL</small>
              </div>
              <div class="text-end">
                <a href="https://n8n.automatehub.fr/workflow/lJmYm4j8024M57rL" target="_blank" class="btn btn-sm btn-outline-primary">
                  <i class="fa fa-external-link-alt"></i>
                </a>
              </div>
            </li>
          </ul>

          <div class="mt-3">
            <a href="{{ route('admin.masterclass.module', 1) }}" class="btn btn-success">
              <i class="fa fa-play me-1"></i> Voir le Module 1
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Module 2 -->
    <div class="col-lg-6">
      <div class="block block-rounded">
        <div class="block-header bg-primary text-white">
          <h3 class="block-title">
            🔵 Module 2 : INTÉGRATIONS ESSENTIELLES
          </h3>
          <div class="block-options">
            <span class="badge bg-light text-primary">✅ Complet</span>
          </div>
        </div>
        <div class="block-content">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <span class="badge bg-primary">Integration Master 🔗</span>
              <span class="text-muted ms-2">2h50 • 4 leçons</span>
            </div>
            <div class="text-end">
              <span class="fs-3 fw-bold text-primary">100%</span>
            </div>
          </div>
          
          <!-- Lessons List -->
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              <div>
                <i class="fa fa-check-circle text-success me-2"></i>
                <span class="fw-medium">2.1 Google Workspace Automation</span>
                <br><small class="text-muted">Workflow: LFrA9An9inR8YB6a</small>
              </div>
              <div class="text-end">
                <a href="https://n8n.automatehub.fr/workflow/LFrA9An9inR8YB6a" target="_blank" class="btn btn-sm btn-outline-primary">
                  <i class="fa fa-external-link-alt"></i>
                </a>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              <div>
                <i class="fa fa-check-circle text-success me-2"></i>
                <span class="fw-medium">2.2 Communication Tools</span>
                <br><small class="text-muted">Workflow: pZ66oZBrObPjY3UE</small>
              </div>
              <div class="text-end">
                <a href="https://n8n.automatehub.fr/workflow/pZ66oZBrObPjY3UE" target="_blank" class="btn btn-sm btn-outline-primary">
                  <i class="fa fa-external-link-alt"></i>
                </a>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              <div>
                <i class="fa fa-check-circle text-success me-2"></i>
                <span class="fw-medium">2.3 Email Marketing Avancé</span>
                <br><small class="text-muted">Workflow: iaIYYK7ZuIf0bKrN</small>
              </div>
              <div class="text-end">
                <a href="https://n8n.automatehub.fr/workflow/iaIYYK7ZuIf0bKrN" target="_blank" class="btn btn-sm btn-outline-primary">
                  <i class="fa fa-external-link-alt"></i>
                </a>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              <div>
                <i class="fa fa-check-circle text-success me-2"></i>
                <span class="fw-medium">2.4 Webhooks Mastery</span>
                <br><small class="text-muted">Workflow: 4hD2AmIIw77um9eV</small>
              </div>
              <div class="text-end">
                <a href="https://n8n.automatehub.fr/workflow/4hD2AmIIw77um9eV" target="_blank" class="btn btn-sm btn-outline-primary">
                  <i class="fa fa-external-link-alt"></i>
                </a>
              </div>
            </li>
          </ul>

          <div class="mt-3">
            <a href="{{ route('admin.masterclass.module', 2) }}" class="btn btn-primary">
              <i class="fa fa-play me-1"></i> Voir le Module 2
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Module 3 - Coming Soon -->
    <div class="col-lg-6">
      <div class="block block-rounded">
        <div class="block-header bg-secondary text-white">
          <h3 class="block-title">
            🟣 Module 3 : MANIPULATION DE DONNÉES
          </h3>
          <div class="block-options">
            <span class="badge bg-light text-secondary">📋 Planifié</span>
          </div>
        </div>
        <div class="block-content">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <span class="badge bg-secondary">Data Wizard 🧙‍♂️</span>
              <span class="text-muted ms-2">3h30 • 4 leçons</span>
            </div>
            <div class="text-end">
              <span class="fs-3 fw-bold text-secondary">0%</span>
            </div>
          </div>
          
          <!-- Lessons Preview -->
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              <div>
                <i class="fa fa-circle text-muted me-2"></i>
                <span class="fw-medium text-muted">3.1 JavaScript dans n8n</span>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              <div>
                <i class="fa fa-circle text-muted me-2"></i>
                <span class="fw-medium text-muted">3.2 Transformations Complexes</span>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              <div>
                <i class="fa fa-circle text-muted me-2"></i>
                <span class="fw-medium text-muted">3.3 APIs REST Avancées</span>
              </div>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
              <div>
                <i class="fa fa-circle text-muted me-2"></i>
                <span class="fw-medium text-muted">3.4 Bases de Données</span>
              </div>
            </li>
          </ul>

          <div class="mt-3">
            <button class="btn btn-secondary" disabled>
              <i class="fa fa-clock me-1"></i> Prochainement
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-6">
      <div class="block block-rounded">
        <div class="block-header block-header-default">
          <h3 class="block-title">
            <i class="fa fa-tools me-1 text-muted"></i>
            Actions Rapides
          </h3>
        </div>
        <div class="block-content">
          <div class="row">
            <div class="col-12">
              <a href="https://n8n.automatehub.fr" target="_blank" class="btn btn-lg btn-outline-primary w-100 mb-3">
                <i class="fa fa-external-link-alt me-2"></i>
                Ouvrir n8n
              </a>
            </div>
            <div class="col-6">
              <a href="{{ route('admin.masterclass.workflows') }}" class="btn btn-outline-success w-100 mb-2">
                <i class="fa fa-cogs me-1"></i>
                Workflows
              </a>
            </div>
            <div class="col-6">
              <a href="{{ route('admin.masterclass.scripts') }}" class="btn btn-outline-warning w-100 mb-2">
                <i class="fa fa-video me-1"></i>
                Scripts
              </a>
            </div>
            <div class="col-6">
              <a href="{{ route('admin.masterclass.progress') }}" class="btn btn-outline-info w-100 mb-2">
                <i class="fa fa-chart-line me-1"></i>
                Progression
              </a>
            </div>
            <div class="col-6">
              <a href="{{ route('admin.masterclass.quiz') }}" class="btn btn-outline-secondary w-100 mb-2">
                <i class="fa fa-question-circle me-1"></i>
                Quiz
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Modules Grid -->
</div>
<!-- END Page Content -->

<style>
.progress-circle {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  border: 8px solid #e9ecef;
  border-top: 8px solid #28a745;
  position: relative;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
}

.progress-circle-text {
  text-align: center;
}
</style>
@endsection