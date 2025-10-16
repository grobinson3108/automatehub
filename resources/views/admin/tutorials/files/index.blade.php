@extends('layouts.backend')

@section('title', 'Gestion des Fichiers - Automatehub')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          Gestion des Fichiers
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Gérer les fichiers associés aux tutoriels
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Fichiers
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
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-primary mb-1">{{ $stats['total_files'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Total Fichiers</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-success mb-1">{{ $stats['total_size'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Espace Utilisé</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-info mb-1">{{ $stats['tutorials_with_files'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Tutoriels avec Fichiers</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-warning mb-1">{{ count($stats['file_types']) }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Types de Fichiers</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Overview -->

  <!-- File Types Chart -->
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">Répartition par Type de Fichier</h3>
    </div>
    <div class="block-content">
      <div class="row">
        <div class="col-md-6">
          <canvas id="file-types-chart" height="300"></canvas>
        </div>
        <div class="col-md-6">
          <div class="table-responsive">
            <table class="table table-striped table-vcenter">
              <thead>
                <tr>
                  <th>Extension</th>
                  <th class="text-center">Nombre</th>
                  <th class="text-center">Pourcentage</th>
                </tr>
              </thead>
              <tbody>
                @foreach($stats['file_types'] as $extension => $count)
                <tr>
                  <td>
                    <span class="fw-semibold">.{{ strtoupper($extension) }}</span>
                  </td>
                  <td class="text-center">
                    {{ $count }}
                  </td>
                  <td class="text-center">
                    {{ round(($count / $stats['total_files']) * 100, 1) }}%
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END File Types Chart -->

  <!-- Upload New File -->
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">Uploader un Nouveau Fichier</h3>
      <div class="block-options">
        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
      </div>
    </div>
    <div class="block-content block-content-full">
      <form action="{{ route('admin.tutorials.files.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mb-4">
          <div class="col-md-6">
            <label for="tutorial_id" class="form-label">Tutoriel</label>
            <select class="form-select" id="tutorial_id" name="tutorial_id" required>
              <option value="">Sélectionner un tutoriel...</option>
              @foreach($tutorials as $tutorial)
                <option value="{{ $tutorial->id }}">{{ $tutorial->title }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label for="files" class="form-label">Fichiers</label>
            <input type="file" class="form-control" id="files" name="files[]" multiple required>
            <div class="form-text">Formats acceptés: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, RAR, JSON, TXT (max 10MB)</div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-upload me-1"></i> Uploader
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- END Upload New File -->

  <!-- Files Table -->
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">Liste des Fichiers</h3>
    </div>
    <div class="block-content">
      <div class="table-responsive">
        <table class="table table-striped table-vcenter js-dataTable-files">
          <thead>
            <tr>
              <th>Fichier</th>
              <th>Tutoriel</th>
              <th class="d-none d-sm-table-cell">Type</th>
              <th class="d-none d-md-table-cell">Taille</th>
              <th class="text-center" style="width: 120px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($allFiles as $file)
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0">
                    @php
                      $extension = pathinfo($file['filename'], PATHINFO_EXTENSION);
                      $iconClass = match(strtolower($extension)) {
                        'pdf' => 'fa-file-pdf text-danger',
                        'doc', 'docx' => 'fa-file-word text-primary',
                        'xls', 'xlsx' => 'fa-file-excel text-success',
                        'ppt', 'pptx' => 'fa-file-powerpoint text-warning',
                        'zip', 'rar' => 'fa-file-archive text-info',
                        'json' => 'fa-file-code text-secondary',
                        'txt' => 'fa-file-alt text-dark',
                        default => 'fa-file text-muted'
                      };
                    @endphp
                    <i class="fa {{ $iconClass }} fa-2x"></i>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <div class="fw-semibold">{{ $file['original_name'] }}</div>
                    <div class="fs-sm text-muted">
                      Ajouté le {{ \Carbon\Carbon::parse(explode('_', $file['filename'])[1] ?? time())->format('d/m/Y') }}
                    </div>
                  </div>
                </div>
              </td>
              <td>
                <a href="{{ route('admin.tutorials.show', $file['tutorial_id']) }}">{{ $file['tutorial_title'] }}</a>
              </td>
              <td class="d-none d-sm-table-cell">
                <span class="badge bg-primary">{{ strtoupper(pathinfo($file['filename'], PATHINFO_EXTENSION)) }}</span>
              </td>
              <td class="d-none d-md-table-cell">
                @php
                  $size = $file['size'] ?? 0;
                  $units = ['B', 'KB', 'MB', 'GB'];
                  $power = $size > 0 ? floor(log($size, 1024)) : 0;
                  $formattedSize = round($size / pow(1024, $power), 2) . ' ' . $units[$power];
                @endphp
                {{ $formattedSize }}
              </td>
              <td class="text-center">
                <div class="btn-group">
                  <a href="{{ route('admin.tutorials.files.show', [$file['tutorial_id'], $file['filename']]) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Détails">
                    <i class="fa fa-eye"></i>
                  </a>
                  <a href="{{ route('admin.tutorials.files.download', [$file['tutorial_id'], $file['filename']]) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Télécharger">
                    <i class="fa fa-download"></i>
                  </a>
                  <button type="button" class="btn btn-sm btn-alt-secondary text-danger" data-bs-toggle="tooltip" title="Supprimer" 
                          onclick="confirmDelete('{{ $file['tutorial_id'] }}', '{{ $file['filename'] }}')">
                    <i class="fa fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center">Aucun fichier trouvé</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- END Files Table -->
</div>
<!-- END Page Content -->

<!-- Delete File Modal -->
<div class="modal fade" id="modal-delete-file" tabindex="-1" role="dialog" aria-labelledby="modal-delete-file" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmation de suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer ce fichier ? Cette action est irréversible.</p>
        <p class="text-danger">Note: La suppression du fichier peut affecter les utilisateurs qui ont accès à ce tutoriel.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <form id="delete-form" method="POST" action="">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Supprimer</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- END Delete File Modal -->
@endsection

@section('js_after')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // File Types Chart
    const fileTypesData = @json($stats['file_types']);
    const labels = Object.keys(fileTypesData).map(ext => '.' + ext.toUpperCase());
    const data = Object.values(fileTypesData);
    
    // Generate colors
    const colors = [];
    for (let i = 0; i < labels.length; i++) {
      const hue = (i * 137.5) % 360; // Use golden angle approximation for nice distribution
      colors.push(`hsl(${hue}, 70%, 60%)`);
    }
    
    const ctx = document.getElementById('file-types-chart').getContext('2d');
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: colors,
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
          }
        }
      }
    });
    
    // Initialize DataTable
    if (typeof jQuery.fn.dataTable !== 'undefined') {
      jQuery('.js-dataTable-files').dataTable({
        pageLength: 15,
        lengthMenu: [[5, 10, 15, 20], [5, 10, 15, 20]],
        autoWidth: false,
        language: {
          url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        }
      });
    }
  });
  
  // Delete file confirmation
  function confirmDelete(tutorialId, filename) {
    const form = document.getElementById('delete-form');
    form.action = `{{ url('admin/tutorials/files') }}/${tutorialId}/${filename}`;
    
    const modal = document.getElementById('modal-delete-file');
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
  }
</script>
@endsection
