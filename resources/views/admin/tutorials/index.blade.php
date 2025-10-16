@extends('layouts.backend')

@section('title', 'Gestion des Tutoriels - Automatehub')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          Gestion des Tutoriels
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Gérer les tutoriels de la plateforme
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Tutoriels
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
            <div class="fs-2 fw-bold text-primary mb-1">{{ $stats['total'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Total Tutoriels</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-success mb-1">{{ $stats['published'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Publiés</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-warning mb-1">{{ $stats['draft'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Brouillons</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-info mb-1">{{ $stats['premium'] + $stats['pro'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Premium/Pro</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Overview -->

  <!-- Filters and Actions -->
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">Filtres et Actions</h3>
      <div class="block-options">
        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
      </div>
    </div>
    <div class="block-content">
      <form action="{{ route('admin.tutorials.index') }}" method="GET" class="mb-4">
        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="search" class="form-label">Recherche</label>
            <input type="text" class="form-control" id="search" name="search" placeholder="Titre, description..." value="{{ request('search') }}">
          </div>
          <div class="col-md-3 mb-3">
            <label for="category_id" class="form-label">Catégorie</label>
            <select class="form-select" id="category_id" name="category_id">
              <option value="">Toutes</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="status" class="form-label">Statut</label>
            <select class="form-select" id="status" name="status">
              <option value="">Tous</option>
              <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publié</option>
              <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="subscription_required" class="form-label">Abonnement requis</label>
            <select class="form-select" id="subscription_required" name="subscription_required">
              <option value="">Tous</option>
              <option value="free" {{ request('subscription_required') === 'free' ? 'selected' : '' }}>Gratuit</option>
              <option value="premium" {{ request('subscription_required') === 'premium' ? 'selected' : '' }}>Premium</option>
              <option value="pro" {{ request('subscription_required') === 'pro' ? 'selected' : '' }}>Pro</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="required_level" class="form-label">Niveau requis</label>
            <select class="form-select" id="required_level" name="required_level">
              <option value="">Tous</option>
              <option value="beginner" {{ request('required_level') === 'beginner' ? 'selected' : '' }}>Débutant</option>
              <option value="intermediate" {{ request('required_level') === 'intermediate' ? 'selected' : '' }}>Intermédiaire</option>
              <option value="expert" {{ request('required_level') === 'expert' ? 'selected' : '' }}>Expert</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="target_audience" class="form-label">Public cible</label>
            <select class="form-select" id="target_audience" name="target_audience">
              <option value="">Tous</option>
              <option value="all" {{ request('target_audience') === 'all' ? 'selected' : '' }}>Tous</option>
              <option value="professional" {{ request('target_audience') === 'professional' ? 'selected' : '' }}>Professionnels</option>
              <option value="personal" {{ request('target_audience') === 'personal' ? 'selected' : '' }}>Particuliers</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="sort_by" class="form-label">Trier par</label>
            <select class="form-select" id="sort_by" name="sort_by">
              <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date de création</option>
              <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Titre</option>
              <option value="published_at" {{ request('sort_by') === 'published_at' ? 'selected' : '' }}>Date de publication</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="sort_order" class="form-label">Ordre</label>
            <select class="form-select" id="sort_order" name="sort_order">
              <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>Décroissant</option>
              <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Croissant</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-filter me-1"></i> Filtrer
            </button>
            <a href="{{ route('admin.tutorials.index') }}" class="btn btn-secondary">
              <i class="fa fa-times me-1"></i> Réinitialiser
            </a>
            <a href="{{ route('admin.tutorials.create') }}" class="btn btn-success float-end">
              <i class="fa fa-plus me-1"></i> Nouveau tutoriel
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- END Filters and Actions -->

  <!-- Tutorials Table -->
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">Liste des Tutoriels</h3>
      <div class="block-options">
        <div class="dropdown">
          <button type="button" class="btn btn-sm btn-alt-secondary" id="dropdown-bulk-actions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-fw fa-bars"></i> Actions groupées
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-bulk-actions">
            <a class="dropdown-item" href="javascript:void(0)" data-action="publish">
              <i class="fa fa-fw fa-check text-success me-1"></i> Publier
            </a>
            <a class="dropdown-item" href="javascript:void(0)" data-action="unpublish">
              <i class="fa fa-fw fa-times text-warning me-1"></i> Mettre en brouillon
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="javascript:void(0)" data-action="delete">
              <i class="fa fa-fw fa-trash text-danger me-1"></i> Supprimer
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="block-content">
      <form id="tutorials-form">
        <div class="table-responsive">
          <table class="table table-striped table-vcenter">
            <thead>
              <tr>
                <th class="text-center" style="width: 40px;">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="select-all">
                  </div>
                </th>
                <th>Tutoriel</th>
                <th>Catégorie</th>
                <th>Statistiques</th>
                <th>Statut</th>
                <th class="text-center" style="width: 100px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($tutorials as $tutorial)
              <tr>
                <td class="text-center">
                  <div class="form-check">
                    <input class="form-check-input tutorial-checkbox" type="checkbox" name="tutorial_ids[]" value="{{ $tutorial->id }}" id="tutorial-{{ $tutorial->id }}">
                  </div>
                </td>
                <td class="fw-semibold">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      @if ($tutorial->thumbnail)
                        <img src="{{ asset('storage/' . $tutorial->thumbnail) }}" alt="{{ $tutorial->title }}" class="img-avatar img-avatar48">
                      @else
                        <div class="img-avatar img-avatar48 bg-primary text-white d-flex align-items-center justify-content-center">
                          <i class="fa fa-book"></i>
                        </div>
                      @endif
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <a href="{{ route('admin.tutorials.show', $tutorial->id) }}">{{ $tutorial->title }}</a>
                      <div class="fs-sm text-muted">
                        {{ Str::limit($tutorial->description, 60) }}
                      </div>
                      <div class="fs-sm">
                        <span class="badge bg-info">{{ ucfirst($tutorial->required_level) }}</span>
                        <span class="badge bg-{{ $tutorial->subscription_required === 'free' ? 'secondary' : ($tutorial->subscription_required === 'premium' ? 'success' : 'warning') }}">
                          {{ ucfirst($tutorial->subscription_required) }}
                        </span>
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-primary">{{ $tutorial->category->name }}</span>
                  <div class="fs-sm text-muted mt-1">
                    @if ($tutorial->tags && count($tutorial->tags) > 0)
                      @foreach($tutorial->tags as $tag)
                        <span class="badge bg-light text-dark">{{ $tag->name }}</span>
                      @endforeach
                    @else
                      <em>Aucun tag</em>
                    @endif
                  </div>
                </td>
                <td>
                  <div class="fs-sm">
                    <i class="fa fa-download me-1"></i> {{ $tutorial->downloads->count() ?? 0 }} téléchargements
                  </div>
                  <div class="fs-sm">
                    <i class="fa fa-heart me-1"></i> {{ $tutorial->favorites->count() ?? 0 }} favoris
                  </div>
                  <div class="fs-sm">
                    <i class="fa fa-check-circle me-1"></i> {{ $tutorial->progress->where('completed', true)->count() ?? 0 }} complétions
                  </div>
                </td>
                <td>
                  @if (!$tutorial->is_draft && $tutorial->published_at)
                    <span class="badge bg-success">Publié</span>
                    <div class="fs-sm text-muted mt-1">
                      {{ $tutorial->published_at->format('d/m/Y') }}
                    </div>
                  @else
                    <span class="badge bg-warning">Brouillon</span>
                  @endif
                  <div class="fs-sm text-muted">
                    Créé le {{ $tutorial->created_at->format('d/m/Y') }}
                  </div>
                </td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="{{ route('admin.tutorials.show', $tutorial->id) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Voir">
                      <i class="fa fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.tutorials.edit', $tutorial->id) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Modifier">
                      <i class="fa fa-pencil-alt"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="{{ !$tutorial->is_draft ? 'Mettre en brouillon' : 'Publier' }}" 
                            onclick="togglePublish({{ $tutorial->id }})">
                      <i class="fa {{ !$tutorial->is_draft ? 'fa-times text-warning' : 'fa-check text-success' }}"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-alt-secondary text-danger" data-bs-toggle="tooltip" title="Supprimer" 
                            onclick="confirmDelete({{ $tutorial->id }})">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center">Aucun tutoriel trouvé</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </form>
      
      <div class="d-flex justify-content-center mt-4">
        {{ $tutorials->withQueryString()->links() }}
      </div>
    </div>
  </div>
  <!-- END Tutorials Table -->
</div>
<!-- END Page Content -->

<!-- Delete Tutorial Modal -->
<div class="modal fade" id="modal-delete-tutorial" tabindex="-1" role="dialog" aria-labelledby="modal-delete-tutorial" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmation de suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer ce tutoriel ? Cette action est irréversible et supprimera également tous les fichiers associés.</p>
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
<!-- END Delete Tutorial Modal -->

<!-- Bulk Action Modal -->
<div class="modal fade" id="modal-bulk-action" tabindex="-1" role="dialog" aria-labelledby="modal-bulk-action" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulk-action-title">Action groupée</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="bulk-action-message">Êtes-vous sûr de vouloir effectuer cette action sur les tutoriels sélectionnés ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary" id="confirm-bulk-action">Confirmer</button>
      </div>
    </div>
  </div>
</div>
<!-- END Bulk Action Modal -->
@endsection

@section('js_after')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
      const checkboxes = document.querySelectorAll('.tutorial-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
    });
    
    // Bulk actions
    const bulkActionLinks = document.querySelectorAll('[data-action]');
    bulkActionLinks.forEach(link => {
      link.addEventListener('click', function() {
        const action = this.getAttribute('data-action');
        
        // Check if any tutorials are selected
        const selectedTutorials = document.querySelectorAll('.tutorial-checkbox:checked');
        if (selectedTutorials.length === 0) {
          alert('Veuillez sélectionner au moins un tutoriel.');
          return;
        }
        
        // Set modal content based on action
        const modal = document.getElementById('modal-bulk-action');
        const title = document.getElementById('bulk-action-title');
        const message = document.getElementById('bulk-action-message');
        
        if (action === 'delete') {
          title.textContent = 'Confirmation de suppression';
          message.textContent = `Êtes-vous sûr de vouloir supprimer les ${selectedTutorials.length} tutoriels sélectionnés ? Cette action est irréversible.`;
        } else if (action === 'publish') {
          title.textContent = 'Confirmation de publication';
          message.textContent = `Êtes-vous sûr de vouloir publier les ${selectedTutorials.length} tutoriels sélectionnés ?`;
        } else if (action === 'unpublish') {
          title.textContent = 'Confirmation de mise en brouillon';
          message.textContent = `Êtes-vous sûr de vouloir mettre en brouillon les ${selectedTutorials.length} tutoriels sélectionnés ?`;
        }
        
        // Set confirm button action
        document.getElementById('confirm-bulk-action').onclick = function() {
          const tutorialIds = Array.from(selectedTutorials).map(checkbox => checkbox.value);
          
          // Prepare the form data
          const formData = new FormData();
          formData.append('_token', '{{ csrf_token() }}');
          tutorialIds.forEach(id => formData.append('tutorial_ids[]', id));
          
          // Determine the endpoint based on the action
          let endpoint = '';
          if (action === 'delete') {
            endpoint = '{{ route("admin.tutorials.index") }}/bulk-delete';
            formData.append('_method', 'DELETE');
          } else if (action === 'publish' || action === 'unpublish') {
            endpoint = '{{ route("admin.tutorials.index") }}/bulk-toggle-publish';
            formData.append('status', action === 'publish' ? 'published' : 'draft');
          }
          
          // Send AJAX request
          fetch(endpoint, {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Close modal and reload page
              const bsModal = bootstrap.Modal.getInstance(modal);
              bsModal.hide();
              location.reload();
            } else {
              alert('Une erreur est survenue: ' + data.error);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de l\'exécution de l\'action.');
          });
        };
        
        // Show modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
      });
    });
  });
  
  // Delete tutorial confirmation
  function confirmDelete(tutorialId) {
    const form = document.getElementById('delete-form');
    form.action = "{{ url('admin/tutorials') }}/" + tutorialId;
    
    const modal = document.getElementById('modal-delete-tutorial');
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
  }
  
  // Toggle publish status
  function togglePublish(tutorialId) {
    fetch("{{ url('admin/tutorials') }}/" + tutorialId + "/toggle-publish", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert('Une erreur est survenue lors du changement de statut.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Une erreur est survenue lors du changement de statut.');
    });
  }
</script>
@endsection
