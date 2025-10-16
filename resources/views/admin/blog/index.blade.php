@extends('layouts.backend')

@section('title', 'Gestion du Blog - Automatehub')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          Gestion du Blog
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Gérer les articles du blog de la plateforme
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Blog
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
    <div class="col-md-6 col-xl-4">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-primary mb-1">{{ $stats['total'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Total Articles</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-4">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-success mb-1">{{ $stats['published'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Publiés</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-4">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-warning mb-1">{{ $stats['draft'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Brouillons</div>
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
      <form action="{{ route('admin.blog.index') }}" method="GET" class="mb-4">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="search" class="form-label">Recherche</label>
            <input type="text" class="form-control" id="search" name="search" placeholder="Titre, contenu..." value="{{ request('search') }}">
          </div>
          <div class="col-md-4 mb-3">
            <label for="category_id" class="form-label">Catégorie</label>
            <select class="form-select" id="category_id" name="category_id">
              <option value="">Toutes</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label for="status" class="form-label">Statut</label>
            <select class="form-select" id="status" name="status">
              <option value="">Tous</option>
              <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publié</option>
              <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Brouillon</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="date_from" class="form-label">Date de début</label>
            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
          </div>
          <div class="col-md-4 mb-3">
            <label for="date_to" class="form-label">Date de fin</label>
            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
          </div>
          <div class="col-md-4 mb-3">
            <label for="sort_by" class="form-label">Trier par</label>
            <div class="d-flex">
              <select class="form-select me-2" id="sort_by" name="sort_by">
                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date de création</option>
                <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Titre</option>
                <option value="published_at" {{ request('sort_by') === 'published_at' ? 'selected' : '' }}>Date de publication</option>
              </select>
              <select class="form-select" id="sort_order" name="sort_order">
                <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>Décroissant</option>
                <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Croissant</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-filter me-1"></i> Filtrer
            </button>
            <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary">
              <i class="fa fa-times me-1"></i> Réinitialiser
            </a>
            <a href="{{ route('admin.blog.create') }}" class="btn btn-success float-end">
              <i class="fa fa-plus me-1"></i> Nouvel article
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- END Filters and Actions -->

  <!-- Blog Posts Table -->
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">Liste des Articles</h3>
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
      <form id="posts-form">
        <div class="table-responsive">
          <table class="table table-striped table-vcenter">
            <thead>
              <tr>
                <th class="text-center" style="width: 40px;">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="select-all">
                  </div>
                </th>
                <th>Article</th>
                <th>Catégorie</th>
                <th>Auteur</th>
                <th>Statut</th>
                <th class="text-center" style="width: 100px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($posts as $post)
              <tr>
                <td class="text-center">
                  <div class="form-check">
                    <input class="form-check-input post-checkbox" type="checkbox" name="post_ids[]" value="{{ $post->id }}" id="post-{{ $post->id }}">
                  </div>
                </td>
                <td class="fw-semibold">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      @if ($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="img-avatar img-avatar48">
                      @else
                        <div class="img-avatar img-avatar48 bg-primary text-white d-flex align-items-center justify-content-center">
                          <i class="fa fa-newspaper"></i>
                        </div>
                      @endif
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <a href="{{ route('admin.blog.show', $post->id) }}">{{ $post->title }}</a>
                      <div class="fs-sm text-muted">
                        {{ Str::limit($post->excerpt, 60) }}
                      </div>
                      <div class="fs-sm">
                        @if ($post->tags && count($post->tags) > 0)
                          @foreach($post->tags as $tag)
                            <span class="badge bg-light text-dark">{{ $tag->name }}</span>
                          @endforeach
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-primary">{{ $post->category->name }}</span>
                </td>
                <td>
                  <div class="fs-sm">
                    {{ $post->author->first_name }} {{ $post->author->last_name }}
                  </div>
                </td>
                <td>
                  @if ($post->is_published)
                    <span class="badge bg-success">Publié</span>
                    <div class="fs-sm text-muted mt-1">
                      {{ $post->published_at->format('d/m/Y') }}
                    </div>
                  @else
                    <span class="badge bg-warning">Brouillon</span>
                  @endif
                  <div class="fs-sm text-muted">
                    Créé le {{ $post->created_at->format('d/m/Y') }}
                  </div>
                </td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="{{ route('admin.blog.show', $post->id) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Voir">
                      <i class="fa fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.blog.edit', $post->id) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Modifier">
                      <i class="fa fa-pencil-alt"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="{{ $post->is_published ? 'Mettre en brouillon' : 'Publier' }}" 
                            onclick="togglePublish({{ $post->id }})">
                      <i class="fa {{ $post->is_published ? 'fa-times text-warning' : 'fa-check text-success' }}"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-alt-secondary text-danger" data-bs-toggle="tooltip" title="Supprimer" 
                            onclick="confirmDelete({{ $post->id }})">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center">Aucun article trouvé</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </form>
      
      <div class="d-flex justify-content-center mt-4">
        {{ $posts->withQueryString()->links() }}
      </div>
    </div>
  </div>
  <!-- END Blog Posts Table -->
</div>
<!-- END Page Content -->

<!-- Delete Post Modal -->
<div class="modal fade" id="modal-delete-post" tabindex="-1" role="dialog" aria-labelledby="modal-delete-post" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmation de suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.</p>
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
<!-- END Delete Post Modal -->

<!-- Bulk Action Modal -->
<div class="modal fade" id="modal-bulk-action" tabindex="-1" role="dialog" aria-labelledby="modal-bulk-action" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulk-action-title">Action groupée</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="bulk-action-message">Êtes-vous sûr de vouloir effectuer cette action sur les articles sélectionnés ?</p>
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
      const checkboxes = document.querySelectorAll('.post-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
    });
    
    // Bulk actions
    const bulkActionLinks = document.querySelectorAll('[data-action]');
    bulkActionLinks.forEach(link => {
      link.addEventListener('click', function() {
        const action = this.getAttribute('data-action');
        
        // Check if any posts are selected
        const selectedPosts = document.querySelectorAll('.post-checkbox:checked');
        if (selectedPosts.length === 0) {
          alert('Veuillez sélectionner au moins un article.');
          return;
        }
        
        // Set modal content based on action
        const modal = document.getElementById('modal-bulk-action');
        const title = document.getElementById('bulk-action-title');
        const message = document.getElementById('bulk-action-message');
        
        if (action === 'delete') {
          title.textContent = 'Confirmation de suppression';
          message.textContent = `Êtes-vous sûr de vouloir supprimer les ${selectedPosts.length} articles sélectionnés ? Cette action est irréversible.`;
        } else if (action === 'publish') {
          title.textContent = 'Confirmation de publication';
          message.textContent = `Êtes-vous sûr de vouloir publier les ${selectedPosts.length} articles sélectionnés ?`;
        } else if (action === 'unpublish') {
          title.textContent = 'Confirmation de mise en brouillon';
          message.textContent = `Êtes-vous sûr de vouloir mettre en brouillon les ${selectedPosts.length} articles sélectionnés ?`;
        }
        
        // Set confirm button action
        document.getElementById('confirm-bulk-action').onclick = function() {
          const postIds = Array.from(selectedPosts).map(checkbox => checkbox.value);
          
          // Prepare the form data
          const formData = new FormData();
          formData.append('_token', '{{ csrf_token() }}');
          postIds.forEach(id => formData.append('post_ids[]', id));
          
          // Determine the endpoint based on the action
          let endpoint = '';
          if (action === 'delete') {
            endpoint = '{{ route("admin.blog.index") }}/bulk-delete';
            formData.append('_method', 'DELETE');
          } else if (action === 'publish' || action === 'unpublish') {
            endpoint = '{{ route("admin.blog.index") }}/bulk-toggle-publish';
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
  
  // Delete post confirmation
  function confirmDelete(postId) {
    const form = document.getElementById('delete-form');
    form.action = "{{ url('admin/blog') }}/" + postId;
    
    const modal = document.getElementById('modal-delete-post');
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
  }
  
  // Toggle publish status
  function togglePublish(postId) {
    fetch("{{ url('admin/blog') }}/" + postId + "/toggle-publish", {
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
