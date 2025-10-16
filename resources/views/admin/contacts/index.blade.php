@extends('layouts.backend')

@section('title', 'Messages de Contact - Automatehub')

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          Messages de Contact
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          Gestion des demandes et messages des utilisateurs
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Messages Contact
          </li>
        </ol>
      </nav>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">
  @if($contacts->count() > 0)
  <!-- Messages List -->
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">Messages Reçus</h3>
      <div class="block-options">
        <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="block-option" data-action="fullscreen_toggle"></button>
      </div>
    </div>
    <div class="block-content">
      <div class="table-responsive">
        <table class="table table-borderless table-striped table-vcenter">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px;">#</th>
              <th>Nom</th>
              <th>Email</th>
              <th>Sujet</th>
              <th class="text-center">Date</th>
              <th class="text-center">Statut</th>
              <th class="text-center" style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($contacts as $contact)
            <tr>
              <td class="text-center">{{ $contact->id }}</td>
              <td class="fw-semibold">{{ $contact->name }}</td>
              <td>{{ $contact->email }}</td>
              <td>{{ Str::limit($contact->subject, 50) }}</td>
              <td class="text-center">
                <span class="fs-sm text-muted">{{ $contact->created_at->format('d/m/Y H:i') }}</span>
              </td>
              <td class="text-center">
                <span class="badge bg-{{ $contact->status === 'read' ? 'success' : 'warning' }}">
                  {{ $contact->status === 'read' ? 'Lu' : 'Non lu' }}
                </span>
              </td>
              <td class="text-center">
                <div class="btn-group" role="group" aria-label="Actions">
                  <button type="button" class="btn btn-sm btn-alt-secondary" onclick="viewMessage({{ $contact->id }})" title="Voir">
                    <i class="fa fa-eye"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-alt-danger" onclick="deleteMessage({{ $contact->id }})" title="Supprimer">
                    <i class="fa fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- END Messages List -->
  @else
  <!-- Empty State -->
  <div class="block block-rounded">
    <div class="block-content text-center py-5">
      <div class="mb-3">
        <i class="fa fa-envelope fa-3x text-muted"></i>
      </div>
      <h3 class="fw-semibold text-muted">Aucun message de contact</h3>
      <p class="text-muted">
        Les messages de contact des utilisateurs apparaîtront ici une fois qu'ils utiliseront le formulaire de contact.
      </p>
      <div class="mt-4">
        <a class="btn btn-primary" href="{{ route('contact') }}" target="_blank">
          <i class="fa fa-external-link-alt me-1"></i> Voir le formulaire de contact
        </a>
      </div>
    </div>
  </div>
  <!-- END Empty State -->
  @endif

  <!-- Statistics -->
  <div class="row">
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-primary">{{ $contacts->count() }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Total Messages</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-warning">{{ $contacts->where('status', 'unread')->count() }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Non Lus</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-success">{{ $contacts->where('status', 'read')->count() }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Lus</div>
        </div>
      </a>
    </div>
    <div class="col-6 col-lg-3">
      <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
        <div class="block-content block-content-full">
          <div class="fs-2 fw-semibold text-info">{{ $contacts->where('created_at', '>=', now()->subDays(7))->count() }}</div>
          <div class="fs-sm fw-semibold text-uppercase text-muted">Cette Semaine</div>
        </div>
      </a>
    </div>
  </div>
  <!-- END Statistics -->

  <!-- Information -->
  <div class="block block-rounded">
    <div class="block-header block-header-default">
      <h3 class="block-title">Information</h3>
    </div>
    <div class="block-content">
      <div class="alert alert-info">
        <h4 class="alert-heading">Fonctionnalité en développement</h4>
        <p class="mb-0">
          Le système de gestion des messages de contact est en cours de développement. 
          Pour l'instant, cette page affiche un aperçu de ce qui sera disponible.
        </p>
        <hr>
        <p class="mb-0">
          <strong>Fonctionnalités prévues :</strong>
        </p>
        <ul class="mb-0">
          <li>Réception et affichage des messages de contact</li>
          <li>Système de statuts (lu/non lu, traité/en attente)</li>
          <li>Réponse directe par email</li>
          <li>Catégorisation des demandes</li>
          <li>Archivage et recherche</li>
        </ul>
      </div>
    </div>
  </div>
  <!-- END Information -->
</div>
<!-- END Page Content -->

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageModalLabel">Détails du Message</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="messageContent">
        <!-- Le contenu sera chargé dynamiquement -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <button type="button" class="btn btn-primary" onclick="replyToMessage()">Répondre</button>
      </div>
    </div>
  </div>
</div>
<!-- END Message Modal -->
@endsection

@section('js_after')
<script>
function viewMessage(messageId) {
    // Simuler l'affichage d'un message
    const messageContent = `
        <div class="mb-3">
            <strong>De :</strong> utilisateur@example.com<br>
            <strong>Sujet :</strong> Demande d'information<br>
            <strong>Date :</strong> ${new Date().toLocaleDateString('fr-FR')}
        </div>
        <div class="mb-3">
            <strong>Message :</strong>
            <div class="border rounded p-3 mt-2">
                Bonjour, j'aimerais avoir plus d'informations sur vos tutoriels n8n. 
                Pouvez-vous me dire quels sont les prérequis pour commencer ?
                <br><br>
                Merci d'avance pour votre réponse.
            </div>
        </div>
    `;
    
    document.getElementById('messageContent').innerHTML = messageContent;
    
    // Afficher le modal
    const modal = new bootstrap.Modal(document.getElementById('messageModal'));
    modal.show();
}

function deleteMessage(messageId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
        alert('Message supprimé (fonctionnalité à implémenter)');
    }
}

function replyToMessage() {
    alert('Fonction de réponse à implémenter');
}

// Marquer tous les messages comme lus
function markAllAsRead() {
    if (confirm('Marquer tous les messages comme lus ?')) {
        alert('Tous les messages ont été marqués comme lus (fonctionnalité à implémenter)');
    }
}
</script>
@endsection
