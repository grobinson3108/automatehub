@extends('layouts.backend')

@section('title', 'Gestion des Utilisateurs - Automatehub')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          Gestion des Utilisateurs
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Gérer les utilisateurs de la plateforme
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Utilisateurs
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
            <div class="fs-sm fw-semibold text-uppercase text-muted">Total Utilisateurs</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-success mb-1">{{ $stats['premium'] + $stats['pro'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Abonnés Payants</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-info mb-1">{{ $stats['professional'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Professionnels</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="block block-rounded">
        <div class="block-content block-content-full">
          <div class="py-3 text-center">
            <div class="fs-2 fw-bold text-warning mb-1">{{ $stats['free'] }}</div>
            <div class="fs-sm fw-semibold text-uppercase text-muted">Utilisateurs Gratuits</div>
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
      <form action="{{ route('admin.users.index') }}" method="GET" class="mb-4">
        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="search" class="form-label">Recherche</label>
            <input type="text" class="form-control" id="search" name="search" placeholder="Nom, email, entreprise..." value="{{ request('search') }}">
          </div>
          <div class="col-md-3 mb-3">
            <label for="subscription_type" class="form-label">Type d'abonnement</label>
            <select class="form-select" id="subscription_type" name="subscription_type">
              <option value="">Tous</option>
              <option value="free" {{ request('subscription_type') === 'free' ? 'selected' : '' }}>Gratuit</option>
              <option value="premium" {{ request('subscription_type') === 'premium' ? 'selected' : '' }}>Premium</option>
              <option value="pro" {{ request('subscription_type') === 'pro' ? 'selected' : '' }}>Pro</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="level_n8n" class="form-label">Niveau n8n</label>
            <select class="form-select" id="level_n8n" name="level_n8n">
              <option value="">Tous</option>
              <option value="beginner" {{ request('level_n8n') === 'beginner' ? 'selected' : '' }}>Débutant</option>
              <option value="intermediate" {{ request('level_n8n') === 'intermediate' ? 'selected' : '' }}>Intermédiaire</option>
              <option value="expert" {{ request('level_n8n') === 'expert' ? 'selected' : '' }}>Expert</option>
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="is_professional" class="form-label">Type d'utilisateur</label>
            <select class="form-select" id="is_professional" name="is_professional">
              <option value="">Tous</option>
              <option value="1" {{ request('is_professional') === '1' ? 'selected' : '' }}>Professionnel</option>
              <option value="0" {{ request('is_professional') === '0' ? 'selected' : '' }}>Particulier</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="date_from" class="form-label">Date d'inscription (début)</label>
            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
          </div>
          <div class="col-md-3 mb-3">
            <label for="date_to" class="form-label">Date d'inscription (fin)</label>
            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
          </div>
          <div class="col-md-3 mb-3">
            <label for="sort_by" class="form-label">Trier par</label>
            <select class="form-select" id="sort_by" name="sort_by">
              <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date d'inscription</option>
              <option value="last_activity_at" {{ request('sort_by') === 'last_activity_at' ? 'selected' : '' }}>Dernière activité</option>
              <option value="last_name" {{ request('sort_by') === 'last_name' ? 'selected' : '' }}>Nom</option>
              <option value="subscription_type" {{ request('sort_by') === 'subscription_type' ? 'selected' : '' }}>Type d'abonnement</option>
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
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
              <i class="fa fa-times me-1"></i> Réinitialiser
            </a>
            <a href="{{ route('admin.users.export', request()->all()) }}" class="btn btn-success float-end">
              <i class="fa fa-download me-1"></i> Exporter CSV
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- END Filters and Actions -->

  <!-- Users Table -->
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">Liste des Utilisateurs</h3>
      <div class="block-options">
        <div class="dropdown">
          <button type="button" class="btn btn-sm btn-alt-secondary" id="dropdown-bulk-actions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-fw fa-bars"></i> Actions groupées
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-bulk-actions">
            <a class="dropdown-item" href="javascript:void(0)" data-action="upgrade" data-subscription="premium">
              <i class="fa fa-fw fa-arrow-up text-success me-1"></i> Passer en Premium
            </a>
            <a class="dropdown-item" href="javascript:void(0)" data-action="upgrade" data-subscription="pro">
              <i class="fa fa-fw fa-arrow-up text-warning me-1"></i> Passer en Pro
            </a>
            <a class="dropdown-item" href="javascript:void(0)" data-action="downgrade" data-subscription="free">
              <i class="fa fa-fw fa-arrow-down text-danger me-1"></i> Passer en Gratuit
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
      <form id="users-form">
        <div class="table-responsive">
          <table class="table table-striped table-vcenter">
            <thead>
              <tr>
                <th class="text-center" style="width: 40px;">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="select-all">
                  </div>
                </th>
                <th>Utilisateur</th>
                <th>Contact</th>
                <th>Abonnement</th>
                <th>Activité</th>
                <th class="text-center" style="width: 100px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($users as $user)
              <tr>
                <td class="text-center">
                  <div class="form-check">
                    <input class="form-check-input user-checkbox" type="checkbox" name="user_ids[]" value="{{ $user->id }}" id="user-{{ $user->id }}">
                  </div>
                </td>
                <td class="fw-semibold">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="img-avatar img-avatar48 bg-primary text-white d-flex align-items-center justify-content-center fw-bold">
                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <a href="{{ route('admin.users.show', $user->id) }}">{{ $user->first_name }} {{ $user->last_name }}</a>
                      <div class="fs-sm text-muted">
                        @if ($user->is_professional)
                          <i class="fa fa-building me-1"></i> {{ $user->company_name }}
                        @else
                          <i class="fa fa-user me-1"></i> Particulier
                        @endif
                      </div>
                      <div class="fs-sm">
                        @if ($user->is_admin)
                          <span class="badge bg-danger">Admin</span>
                        @endif
                        @if ($user->level_n8n)
                          <span class="badge bg-info">{{ ucfirst($user->level_n8n) }}</span>
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="fs-sm">
                    <i class="fa fa-envelope me-1"></i> {{ $user->email }}
                  </div>
                  @if ($user->is_professional && $user->country)
                  <div class="fs-sm text-muted">
                    <i class="fa fa-map-marker-alt me-1"></i> {{ $user->city }}, {{ $user->country }}
                  </div>
                  @endif
                </td>
                <td>
                  <span class="badge bg-{{ $user->subscription_type === 'free' ? 'secondary' : ($user->subscription_type === 'premium' ? 'success' : 'warning') }}">
                    {{ ucfirst($user->subscription_type) }}
                  </span>
                  <div class="fs-sm text-muted mt-1">
                    Inscrit le {{ $user->created_at->format('d/m/Y') }}
                  </div>
                </td>
                <td>
                  <div class="fs-sm">
                    <i class="fa fa-download me-1"></i> {{ $user->downloads_count ?? 0 }} téléchargements
                  </div>
                  <div class="fs-sm">
                    <i class="fa fa-trophy me-1"></i> {{ $user->badges_count ?? 0 }} badges
                  </div>
                  <div class="fs-sm text-muted">
                    <i class="fa fa-clock me-1"></i> 
                    @if ($user->last_activity_at)
                      {{ $user->last_activity_at->diffForHumans() }}
                    @else
                      Jamais connecté
                    @endif
                  </div>
                </td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Voir">
                      <i class="fa fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="Modifier">
                      <i class="fa fa-pencil-alt"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-alt-secondary text-danger" data-bs-toggle="tooltip" title="Supprimer" 
                            onclick="confirmDelete({{ $user->id }})">
                      <i class="fa fa-times"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center">Aucun utilisateur trouvé</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </form>
      
      <div class="d-flex justify-content-center mt-4">
        {{ $users->withQueryString()->links() }}
      </div>
    </div>
  </div>
  <!-- END Users Table -->
</div>
<!-- END Page Content -->

<!-- Delete User Modal -->
<div class="modal fade" id="modal-delete-user" tabindex="-1" role="dialog" aria-labelledby="modal-delete-user" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmation de suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.</p>
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
<!-- END Delete User Modal -->

<!-- Bulk Action Modal -->
<div class="modal fade" id="modal-bulk-action" tabindex="-1" role="dialog" aria-labelledby="modal-bulk-action" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulk-action-title">Action groupée</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="bulk-action-message">Êtes-vous sûr de vouloir effectuer cette action sur les utilisateurs sélectionnés ?</p>
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
      const checkboxes = document.querySelectorAll('.user-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
    });
    
    // Bulk actions
    const bulkActionLinks = document.querySelectorAll('[data-action]');
    bulkActionLinks.forEach(link => {
      link.addEventListener('click', function() {
        const action = this.getAttribute('data-action');
        const subscription = this.getAttribute('data-subscription');
        
        // Check if any users are selected
        const selectedUsers = document.querySelectorAll('.user-checkbox:checked');
        if (selectedUsers.length === 0) {
          alert('Veuillez sélectionner au moins un utilisateur.');
          return;
        }
        
        // Set modal content based on action
        const modal = document.getElementById('modal-bulk-action');
        const title = document.getElementById('bulk-action-title');
        const message = document.getElementById('bulk-action-message');
        
        if (action === 'delete') {
          title.textContent = 'Confirmation de suppression';
          message.textContent = `Êtes-vous sûr de vouloir supprimer les ${selectedUsers.length} utilisateurs sélectionnés ? Cette action est irréversible.`;
        } else if (action === 'upgrade') {
          title.textContent = 'Confirmation de mise à niveau';
          message.textContent = `Êtes-vous sûr de vouloir passer les ${selectedUsers.length} utilisateurs sélectionnés en abonnement ${subscription.toUpperCase()} ?`;
        } else if (action === 'downgrade') {
          title.textContent = 'Confirmation de rétrogradation';
          message.textContent = `Êtes-vous sûr de vouloir passer les ${selectedUsers.length} utilisateurs sélectionnés en abonnement ${subscription.toUpperCase()} ?`;
        }
        
        // Set confirm button action
        document.getElementById('confirm-bulk-action').onclick = function() {
          const userIds = Array.from(selectedUsers).map(checkbox => checkbox.value);
          
          // Send AJAX request
          fetch('{{ route("admin.users.bulk-action") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
              action: action,
              user_ids: userIds,
              subscription_type: subscription
            })
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
  
  // Delete user confirmation
  function confirmDelete(userId) {
    const form = document.getElementById('delete-form');
    form.action = "{{ url('admin/users') }}/" + userId;
    
    const modal = document.getElementById('modal-delete-user');
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
  }
</script>
@endsection
