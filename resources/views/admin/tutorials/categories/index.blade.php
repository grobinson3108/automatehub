@extends('layouts.backend')

@section('title', 'Gestion des Catégories - Automatehub')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          Gestion des Catégories
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Gérer les catégories de tutoriels et d'articles
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Catégories
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
            <div class="fs-sm fw-semibold text-uppercase text-muted">Total Catégories</div>
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
            <div class="fs-sm fw-semibold text-uppercase text-muted">Vides</div>
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
      <form action="{{ route('admin.tutorials.categories') }}" method="GET" class="mb-4">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="search" class="form-label">Recherche</label>
            <input type="text" class="form-control" id="search" name="search" placeholder="Nom, description..." value="{{ request('search') }}">
          </div>
          <div class="col-md-4 mb-3">
            <label for="sort_by" class="form-label">Trier par</label>
            <select class="form-select" id="sort_by" name="sort_by">
              <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Nom</option>
              <option value="display_order" {{ request('sort_by') === 'display_order' ? 'selected' : '' }}>Ordre d'affichage</option>
              <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date de création</option>
            </select>
          </div>
          <div class="col-md-4 mb-3">
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
            <a href="{{ route('admin.tutorials.categories') }}" class="btn btn-secondary">
              <i class="fa fa-times me-1"></i> Réinitialiser
            </a>
            <a href="{{ route('admin.tutorials.categories.create') }}" class="btn btn-success float-end">
              <i class="fa fa-plus me-1"></i> Nouvelle catégorie
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- END Filters and Actions -->

  <!-- Categories Table -->
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">Liste des Catégories</h3>
      <div class="block-options">
        <button type="button" class="btn btn-sm btn-alt-secondary" id="save-order-btn" style="display: none;">
          <i class="fa fa-save me-1"></i> Enregistrer l'ordre
        </button>
        <button type="button" class="btn btn-sm btn-alt-primary" id="reorder-btn">
          <i class="fa fa-sort me-1"></i> Réorganiser
        </button>
      </div>
    </div>
    <div class="block-content">
      <div class="table-responsive">
        <table class="table table-striped table-vcenter" id="categories-table">
          <thead>
            <tr>
              <th style="width: 40px;" class="text-center order-handle-th" style="display: none;">
                <i class="fa fa-arrows-alt"></i>
              </th>
              <th>Catégorie</th>
              <th>Statistiques</th>
              <th>Statut</th>
              <th class="text-center" style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody id="categories-tbody">
            @forelse ($categories as $category)
            <tr data-id="{{ $category->id }}">
              <td class="text-center order-handle" style="display: none; cursor: move;">
                <i class="fa fa-arrows-alt"></i>
                <input type="hidden" name="order[{{ $category->id }}]" value="{{ $category->display_order }}" class="order-input">
              </td>
              <td class="fw-semibold">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0">
                    @if ($category->icon)
                      <span class="item-icon" style="background-color: {{ $category->color ?? '#3B5998' }}">
                        <i class="{{ $category->icon }}"></i>
                      </span>
                    @else
                      <span class="item-icon" style="background-color: {{ $category->color ?? '#3B5998' }}">
                        <i class="fa fa-folder"></i>
                      </span>
                    @endif
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <div class="fw-semibold">{{ $category->name }}</div>
                    <div class="fs-sm text-muted">
                      {{ Str::limit($category->description, 60) }}
                    </div>
                  </div>
                </div>
              </td>
              <td>
                <div class="fs-sm">
                  <i class="fa fa-book me-1"></i> {{ $category->tutorials_count }} tutoriels
                </div>
                <div class="fs-sm">
                  <i class="fa fa-newspaper me-1"></i> {{ $category->blog_posts_count }} articles
                </div>
              </td>
              <td>
                @if ($category->is_active)
                  <span class="badge bg-success">Actif</span>
                @else
                  <span class="badge bg-warning">Inactif</span>
                @endif
                <div class="fs-sm text-muted">
                  Ordre: {{ $category->display_order }}
                </div>
              </td>
              <td class="text-center">
                <div class="btn-group">
                  <a href="{{ route('admin.tutorials.categories.edit', $category->id) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Modifier">
                    <i class="fa fa-pencil-alt"></i>
                  </a>
                  <button type="button" class="btn btn-sm btn-alt-secondary text-danger" data-bs-toggle="tooltip" title="Supprimer" 
                          onclick="confirmDelete({{ $category->id }})">
                    <i class="fa fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center">Aucune catégorie trouvée</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      <div class="d-flex justify-content-center mt-4">
        {{ $categories->withQueryString()->links() }}
      </div>
    </div>
  </div>
  <!-- END Categories Table -->
</div>
<!-- END Page Content -->

<!-- Delete Category Modal -->
<div class="modal fade" id="modal-delete-category" tabindex="-1" role="dialog" aria-labelledby="modal-delete-category" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmation de suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est irréversible.</p>
        <p class="text-danger">Note: Vous ne pouvez supprimer que les catégories qui ne sont pas utilisées par des tutoriels ou des articles.</p>
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
<!-- END Delete Category Modal -->
@endsection

@section('css_after')
<style>
  .item-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: white;
    font-size: 18px;
  }
  
  .order-handle-th, .order-handle {
    width: 40px;
    text-align: center;
  }
</style>
@endsection

@section('js_after')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Reorder functionality
    const reorderBtn = document.getElementById('reorder-btn');
    const saveOrderBtn = document.getElementById('save-order-btn');
    const orderHandles = document.querySelectorAll('.order-handle');
    const orderHandleTh = document.querySelector('.order-handle-th');
    const categoriesTable = document.getElementById('categories-tbody');
    
    let sortable = null;
    
    reorderBtn.addEventListener('click', function() {
      // Show order handles
      orderHandleTh.style.display = 'table-cell';
      orderHandles.forEach(handle => {
        handle.style.display = 'table-cell';
      });
      
      // Show save button
      saveOrderBtn.style.display = 'inline-block';
      reorderBtn.style.display = 'none';
      
      // Initialize Sortable
      sortable = new Sortable(categoriesTable, {
        handle: '.order-handle',
        animation: 150,
        onEnd: function() {
          // Update order inputs
          const rows = categoriesTable.querySelectorAll('tr');
          rows.forEach((row, index) => {
            const orderInput = row.querySelector('.order-input');
            if (orderInput) {
              orderInput.value = index;
            }
          });
        }
      });
    });
    
    saveOrderBtn.addEventListener('click', function() {
      // Collect category IDs and their new order
      const categories = [];
      const rows = categoriesTable.querySelectorAll('tr');
      
      rows.forEach((row, index) => {
        const categoryId = row.getAttribute('data-id');
        if (categoryId) {
          categories.push({
            id: categoryId,
            display_order: index
          });
        }
      });
      
      // Send AJAX request to update order
      fetch('{{ route("admin.tutorials.categories.update-order") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ categories: categories })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Hide order handles
          orderHandleTh.style.display = 'none';
          orderHandles.forEach(handle => {
            handle.style.display = 'none';
          });
          
          // Hide save button
          saveOrderBtn.style.display = 'none';
          reorderBtn.style.display = 'inline-block';
          
          // Destroy Sortable
          if (sortable) {
            sortable.destroy();
            sortable = null;
          }
          
          // Show success message
          alert('Ordre des catégories mis à jour avec succès.');
          
          // Reload page to show updated order
          location.reload();
        } else {
          alert('Erreur lors de la mise à jour de l\'ordre : ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue lors de la mise à jour de l\'ordre.');
      });
    });
  });
  
  // Delete category confirmation
  function confirmDelete(categoryId) {
    const form = document.getElementById('delete-form');
    form.action = "{{ url('admin/tutorials/categories') }}/" + categoryId;
    
    const modal = document.getElementById('modal-delete-category');
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
  }
</script>
@endsection
