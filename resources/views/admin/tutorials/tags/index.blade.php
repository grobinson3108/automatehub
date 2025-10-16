@extends('layouts.backend')

@section('title', 'Gestion des Tags - Automatehub')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          Gestion des Tags
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Gérer les tags de tutoriels et d'articles
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Tags
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
            <div class="fs-sm fw-semibold text-uppercase text-muted">Total Tags</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-success mb-1">{{ $stats['with_tutorials'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Avec Tutoriels</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-info mb-1">{{ $stats['with_blog_posts'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Avec Articles</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-warning mb-1">{{ $stats['empty'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Non utilisés</div>
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
      <form action="{{ route('admin.tutorials.tags') }}" method="GET" class="mb-4">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="search" class="form-label">Recherche</label>
            <input type="text" class="form-control" id="search" name="search" placeholder="Nom, description..." value="{{ request('search') }}">
          </div>
          <div class="col-md-3 mb-3">
            <label for="sort_by" class="form-label">Trier par</label>
            <select class="form-select" id="sort_by" name="sort_by">
              <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Nom</option>
              <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date de création</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="sort_order" class="form-label">Ordre</label>
            <select class="form-select" id="sort_order" name="sort_order">
              <option value="asc" {{ request('sort_order', 'asc') === 'asc' ? 'selected' : '' }}>Croissant</option>
              <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Décroissant</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-filter me-1"></i> Filtrer
            </button>
            <a href="{{ route('admin.tutorials.tags') }}" class="btn btn-secondary">
              <i class="fa fa-times me-1"></i> Réinitialiser
            </a>
            <a href="{{ route('admin.tutorials.tags.create') }}" class="btn btn-success float-end">
              <i class="fa fa-plus me-1"></i> Nouveau tag
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- END Filters and Actions -->

  <!-- Tags Table -->
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">Liste des Tags</h3>
      <div class="block-options">
        <button type="button" class="btn btn-sm btn-alt-danger" id="bulk-delete-btn" style="display: none;">
          <i class="fa fa-trash me-1"></i> Supprimer la sélection
        </button>
      </div>
    </div>
    <div class="block-content">
      <form id="tags-form">
        <div class="table-responsive">
          <table class="table table-striped table-vcenter">
            <thead>
              <tr>
                <th class="text-center" style="width: 40px;">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="select-all">
                  </div>
                </th>
                <th>Tag</th>
                <th>Statistiques</th>
                <th class="text-center" style="width: 100px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($tags as $tag)
              <tr>
                <td class="text-center">
                  <div class="form-check">
                    <input class="form-check-input tag-checkbox" type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" id="tag-{{ $tag->id }}">
                  </div>
                </td>
                <td class="fw-semibold">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <span class="tag-badge" style="background-color: {{ $tag->color ?? '#6c757d' }}">
                        {{ strtoupper(substr($tag->name, 0, 1)) }}
                      </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <div class="fw-semibold">{{ $tag->name }}</div>
                      @if ($tag->description)
                        <div class="fs-sm text-muted">
                          {{ Str::limit($tag->description, 60) }}
                        </div>
                      @endif
                    </div>
                  </div>
                </td>
                <td>
                  <div class="fs-sm">
                    <i class="fa fa-book me-1"></i> {{ $tag->tutorials_count }} tutoriels
                  </div>
                  <div class="fs-sm">
                    <i class="fa fa-newspaper me-1"></i> {{ $tag->blog_posts_count }} articles
                  </div>
                </td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="{{ route('admin.tutorials.tags.edit', $tag->id) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Modifier">
                      <i class="fa fa-pencil-alt"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-alt-secondary text-danger" data-bs-toggle="tooltip" title="Supprimer" 
                            onclick="confirmDelete({{ $tag->id }})" {{ ($tag->tutorials_count > 0 || $tag->blog_posts_count > 0) ? 'disabled' : '' }}>
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center">Aucun tag trouvé</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </form>
      
      <div class="d-flex justify-content-center mt-4">
        {{ $tags->withQueryString()->links() }}
      </div>
    </div>
  </div>
  <!-- END Tags Table -->
</div>
<!-- END Page Content -->

<!-- Delete Tag Modal -->
<div class="modal fade" id="modal-delete-tag" tabindex="-1" role="dialog" aria-labelledby="modal-delete-tag" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmation de suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer ce tag ? Cette action est irréversible.</p>
        <p class="text-danger">Note: Vous ne pouvez supprimer que les tags qui ne sont pas utilisés par des tutoriels ou des articles.</p>
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
<!-- END Delete Tag Modal -->

<!-- Bulk Delete Modal -->
<div class="modal fade" id="modal-bulk-delete" tabindex="-1" role="dialog" aria-labelledby="modal-bulk-delete" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmation de suppression multiple</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer les tags sélectionnés ? Cette action est irréversible.</p>
        <p class="text-danger">Note: Seuls les tags qui ne sont pas utilisés par des tutoriels ou des articles seront supprimés.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-danger" id="confirm-bulk-delete">Supprimer</button>
      </div>
    </div>
  </div>
</div>
<!-- END Bulk Delete Modal -->
@endsection

@section('css_after')
<style>
  .tag-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 4px;
    color: white;
    font-size: 14px;
    font-weight: bold;
  }
</style>
@endsection

@section('js_after')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox
    const selectAllCheckbox = document.getElementById('select-all');
    const tagCheckboxes = document.querySelectorAll('.tag-checkbox');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    
    // Show/hide bulk delete button based on selection
    function updateBulkDeleteButton() {
      const checkedCount = document.querySelectorAll('.tag-checkbox:checked').length;
      bulkDeleteBtn.style.display = checkedCount > 0 ? 'inline-block' : 'none';
    }
    
    // Select all checkbox functionality
    if (selectAllCheckbox) {
      selectAllCheckbox.addEventListener('change', function() {
        tagCheckboxes.forEach(checkbox => {
          // Only check if the tag is not used (button not disabled)
          const row = checkbox.closest('tr');
          const deleteButton = row.querySelector('button[title="Supprimer"]');
          if (!deleteButton.disabled) {
            checkbox.checked = this.checked;
          }
        });
        updateBulkDeleteButton();
      });
    }
    
    // Individual checkbox change
    tagCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', updateBulkDeleteButton);
    });
    
    // Bulk delete button click
    if (bulkDeleteBtn) {
      bulkDeleteBtn.addEventListener('click', function() {
        const selectedTags = document.querySelectorAll('.tag-checkbox:checked');
        if (selectedTags.length === 0) {
          alert('Veuillez sélectionner au moins un tag.');
          return;
        }
        
        // Show confirmation modal
        const modal = document.getElementById('modal-bulk-delete');
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
      });
    }
    
    // Confirm bulk delete
    const confirmBulkDeleteBtn = document.getElementById('confirm-bulk-delete');
    if (confirmBulkDeleteBtn) {
      confirmBulkDeleteBtn.addEventListener('click', function() {
        const selectedTags = document.querySelectorAll('.tag-checkbox:checked');
        const tagIds = Array.from(selectedTags).map(checkbox => checkbox.value);
        
        // Send AJAX request
        fetch('{{ route("admin.tutorials.tags.bulk-delete") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ tag_ids: tagIds })
        })
        .then(response => response.json())
        .then(data => {
          // Close modal
          const modal = document.getElementById('modal-bulk-delete');
          const bsModal = bootstrap.Modal.getInstance(modal);
          bsModal.hide();
          
          if (data.success) {
            // Show success message and reload page
            alert(data.message);
            location.reload();
          } else {
            // Show error message
            alert(data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Une erreur est survenue lors de la suppression des tags.');
        });
      });
    }
  });
  
  // Delete tag confirmation
  function confirmDelete(tagId) {
    const form = document.getElementById('delete-form');
    form.action = "{{ url('admin/tutorials/tags') }}/" + tagId;
    
    const modal = document.getElementById('modal-delete-tag');
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
  }
</script>
@endsection
