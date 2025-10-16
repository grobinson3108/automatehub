@extends('layouts.backend')

@section('title', 'Gestion du Contenu Vidéo - Admin')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          <i class="fa fa-video me-1 text-primary"></i>
          Gestion du Contenu Vidéo
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Planifiez et suivez vos vidéos de workflows à tourner
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Contenu Vidéo
          </li>
        </ol>
      </nav>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">
  <!-- Stats -->
  <div class="row">
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-bold text-dark">{{ $stats['total'] }}</div>
          <div class="fs-sm fw-medium text-muted text-uppercase">Total Workflows</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-bold text-warning">{{ $stats['todo'] }}</div>
          <div class="fs-sm fw-medium text-muted text-uppercase">À faire</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-bold text-info">{{ $stats['in_progress'] }}</div>
          <div class="fs-sm fw-medium text-muted text-uppercase">En cours</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-bold text-success">{{ $stats['done'] }}</div>
          <div class="fs-sm fw-medium text-muted text-uppercase">Terminés</div>
        </div>
      </a>
    </div>
  </div>
  <!-- END Stats -->

  <!-- Actions -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <span class="badge bg-primary">{{ $stats['total_videos'] }} vidéos au total</span>
    </div>
    <div>
      <a href="{{ route('admin.video-content.create') }}" class="btn btn-primary">
        <i class="fa fa-plus me-1"></i> Nouveau Plan
      </a>
      <form method="POST" action="{{ route('admin.video-content.generate-from-workflows') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-primary">
          <i class="fa fa-sync me-1"></i> Générer des workflows
        </button>
      </form>
    </div>
  </div>

  <!-- Plans Table -->
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">
        <i class="fa fa-list me-1"></i>
        Workflows à tourner
      </h3>
    </div>
    <div class="block-content">
      @if($videoPlans->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-vcenter">
            <thead>
              <tr>
                <th>Workflow</th>
                <th>Plateformes</th>
                <th class="text-center">Vidéos</th>
                <th class="text-center">Priorité</th>
                <th class="text-center">Viral</th>
                <th class="text-center">Publications</th>
                <th class="text-center">Statut</th>
                <th class="text-center">Dates</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($videoPlans as $plan)
                <tr>
                  <td>
                    <div>
                      <div class="fw-semibold">
                        <a href="{{ route('admin.video-content.show', $plan['id']) }}" class="text-decoration-none">
                          {{ $plan['workflow_name'] }}
                        </a>
                      </div>
                      <div class="fs-sm text-muted">{{ Str::limit($plan['workflow_description'], 60) }}</div>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex flex-wrap gap-1">
                      @foreach($plan['platforms'] as $platform)
                        @php
                          $platformColors = [
                            'youtube' => '#FF0000',
                            'tiktok' => '#000000',
                            'linkedin' => '#0077B5',
                            'instagram' => '#E4405F',
                            'facebook' => '#1877F2',
                          ];
                          $platformIcons = [
                            'youtube' => 'fab fa-youtube',
                            'tiktok' => 'fab fa-tiktok',
                            'linkedin' => 'fab fa-linkedin',
                            'instagram' => 'fab fa-instagram',
                            'facebook' => 'fab fa-facebook',
                          ];
                          $color = $platformColors[$platform] ?? '#6c757d';
                          $icon = $platformIcons[$platform] ?? 'fa fa-video';
                        @endphp
                        <span class="badge" style="background-color: {{ $color }}; color: white;" title="{{ ucfirst($platform) }}">
                          <i class="{{ $icon }}"></i>
                        </span>
                      @endforeach
                    </div>
                    <div class="fs-xs text-muted mt-1">
                      {{ count($plan['platforms']) }} plateformes
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-primary">{{ $plan['total_videos'] }}</span>
                  </td>
                  <td class="text-center">
                    <span class="badge bg-warning">#{{ $plan['priority'] }}</span>
                  </td>
                  <td class="text-center">
                    <div class="text-warning">
                      @for($i = 1; $i <= 5; $i++)
                        <i class="fa fa-star{{ $i <= $plan['viral_potential'] ? '' : '-o' }}"></i>
                      @endfor
                    </div>
                  </td>
                  <td class="text-center">
                    @php
                      $publicationsCount = \App\Models\VideoPublication::where('video_content_plan_id', $plan['id'])->get()->groupBy('platform');
                    @endphp
                    @if($publicationsCount->count() > 0)
                      <div class="d-flex flex-wrap justify-content-center gap-1">
                        @foreach($publicationsCount as $platform => $pubs)
                          @php
                            $platformIcons = [
                              'youtube' => ['fab fa-youtube', '#FF0000'],
                              'tiktok' => ['fab fa-tiktok', '#000000'],
                              'linkedin' => ['fab fa-linkedin', '#0077B5'],
                              'instagram' => ['fab fa-instagram', '#E4405F'],
                              'facebook' => ['fab fa-facebook', '#1877F2']
                            ];
                            $icon = $platformIcons[$platform] ?? ['fa fa-video', '#6c757d'];
                          @endphp
                          <span class="badge" style="background-color: {{ $icon[1] }}; color: white; font-size: 10px;">
                            <i class="{{ $icon[0] }} me-1"></i>{{ $pubs->count() }}
                          </span>
                        @endforeach
                      </div>
                      <div class="fs-xs text-muted mt-1">
                        <i class="fa fa-calendar me-1"></i>{{ $publicationsCount->flatten()->count() }} total
                      </div>
                    @else
                      <span class="text-muted fs-sm">
                        <i class="fa fa-plus-circle me-1"></i>Aucune
                      </span>
                      <div class="mt-1">
                        <a href="{{ route('admin.publication-calendar.generate', $plan['id']) }}"
                           class="btn btn-xs btn-outline-primary">
                          <i class="fa fa-magic me-1"></i>Générer
                        </a>
                      </div>
                    @endif
                  </td>
                  <td class="text-center">
                    @php
                      $statusConfig = [
                        'todo' => ['bg-secondary', 'À faire', 'fa-clock'],
                        'in_progress' => ['bg-info', 'En cours', 'fa-play'],
                        'done' => ['bg-success', 'Terminé', 'fa-check-circle'],
                      ];
                      $config = $statusConfig[$plan['status']] ?? $statusConfig['todo'];
                    @endphp
                    <span class="badge {{ $config[0] }}">
                      <i class="fa {{ $config[2] }} me-1"></i>{{ $config[1] }}
                    </span>
                  </td>
                  <td class="text-center">
                    <div class="fs-sm">
                      @if($plan['planned_date'])
                        <div class="text-muted">
                          <i class="fa fa-calendar me-1"></i>{{ $plan['planned_date'] }}
                        </div>
                      @endif
                      @if($plan['completed_date'])
                        <div class="text-success">
                          <i class="fa fa-check me-1"></i>{{ $plan['completed_date'] }}
                        </div>
                      @endif
                    </div>
                  </td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <a href="{{ route('admin.video-content.show', $plan['id']) }}"
                         class="btn btn-sm btn-outline-primary" title="Voir">
                        <i class="fa fa-eye"></i>
                      </a>
                      <a href="{{ route('admin.video-content.edit', $plan['id']) }}"
                         class="btn btn-sm btn-outline-info" title="Modifier">
                        <i class="fa fa-edit"></i>
                      </a>
                      @if($plan['status'] !== 'done')
                        <form method="POST" action="{{ route('admin.video-content.mark-as-done', $plan['id']) }}" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-success" title="Marquer terminé">
                            <i class="fa fa-check"></i>
                          </button>
                        </form>
                      @endif
                      @if($plan['status'] === 'todo')
                        <form method="POST" action="{{ route('admin.video-content.mark-as-in-progress', $plan['id']) }}" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-info" title="Commencer">
                            <i class="fa fa-play"></i>
                          </button>
                        </form>
                      @endif
                      <button type="button" class="btn btn-sm btn-outline-danger"
                              onclick="confirmDelete({{ $plan['id'] }}, '{{ $plan['workflow_name'] }}')" title="Supprimer">
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="text-center py-5">
          <div class="display-4 text-muted mb-3">
            <i class="fa fa-video"></i>
          </div>
          <h3 class="fw-bold">Aucun plan de contenu</h3>
          <p class="text-muted mb-4">Commencez par créer votre premier plan de contenu vidéo</p>
          <a href="{{ route('admin.video-content.create') }}" class="btn btn-primary">
            <i class="fa fa-plus me-1"></i> Créer un plan
          </a>
        </div>
      @endif
    </div>
  </div>
  <!-- END Plans Table -->
</div>
<!-- END Page Content -->

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmer la suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer le plan de contenu <strong id="deletePlanName"></strong> ?</p>
        <p class="text-danger">Cette action est irréversible.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <form method="POST" id="deleteForm" class="d-inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Supprimer</button>
        </form>
      </div>
    </div>
  </div>
</div>

@if(session('success'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      One.helpers('jq-notify', {
        type: 'success',
        icon: 'fa fa-check me-1',
        message: '{{ session('success') }}'
      });
    });
  </script>
@endif

<script>
function confirmDelete(planId, planName) {
  document.getElementById('deletePlanName').textContent = planName;
  document.getElementById('deleteForm').action = `/admin/video-content/${planId}`;

  const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
  deleteModal.show();
}
</script>

@endsection