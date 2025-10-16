@extends('layouts.backend')

@section('title', 'Détails - ' . $videoContentPlan->workflow_name)

@section('content')
<!-- Hero -->
<div class="bg-body-light">
  <div class="content content-full">
    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
      <div class="flex-grow-1">
        <h1 class="h3 fw-bold mb-1">
          <i class="fa fa-video me-1 text-primary"></i>
          {{ $videoContentPlan->workflow_name }}
        </h1>
        <h2 class="fs-base lh-base fw-medium text-muted mb-0">
          {{ $videoContentPlan->workflow_description }}
        </h2>
      </div>
      <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-alt">
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
          </li>
          <li class="breadcrumb-item">
            <a class="link-fx" href="{{ route('admin.video-content.index') }}">Contenu Vidéo</a>
          </li>
          <li class="breadcrumb-item" aria-current="page">
            Détails
          </li>
        </ol>
      </nav>
    </div>
  </div>
</div>
<!-- END Hero -->

<!-- Page Content -->
<div class="content">
  <!-- Actions -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admin.video-content.index') }}" class="btn btn-outline-secondary">
      <i class="fa fa-arrow-left me-1"></i> Retour
    </a>
    <div>
      @if($videoContentPlan->status !== 'done')
        <form method="POST" action="{{ route('admin.video-content.mark-as-done', $videoContentPlan->id) }}" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-success">
            <i class="fa fa-check me-1"></i> Marquer terminé
          </button>
        </form>
      @endif
      @if($videoContentPlan->status === 'todo')
        <form method="POST" action="{{ route('admin.video-content.mark-as-in-progress', $videoContentPlan->id) }}" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-info">
            <i class="fa fa-play me-1"></i> Commencer
          </button>
        </form>
      @endif
      <a href="{{ route('admin.video-content.edit', $videoContentPlan->id) }}" class="btn btn-primary">
        <i class="fa fa-edit me-1"></i> Modifier
      </a>
    </div>
  </div>

  <!-- Contrôles des plateformes -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h5 class="mb-0">
        <i class="fa fa-list me-1"></i> Détail par plateforme
        <small class="text-muted">(ordre chronologique)</small>
      </h5>
    </div>
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAllPlatforms(true)">
        <i class="fa fa-chevron-down me-1"></i> Tout ouvrir
      </button>
      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllPlatforms(false)">
        <i class="fa fa-chevron-up me-1"></i> Tout fermer
      </button>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="row mb-4">
    <div class="col-6 col-lg-3">
      <div class="block block-rounded text-center">
        <div class="block-content block-content-full">
          @php
            $statusConfig = [
              'todo' => ['bg-secondary', 'À faire', 'fa-clock'],
              'in_progress' => ['bg-info', 'En cours', 'fa-play'],
              'done' => ['bg-success', 'Terminé', 'fa-check-circle'],
            ];
            $config = $statusConfig[$videoContentPlan->status] ?? $statusConfig['todo'];
          @endphp
          <span class="badge {{ $config[0] }} p-2">
            <i class="fa {{ $config[2] }} me-1"></i>{{ $config[1] }}
          </span>
          <div class="fs-sm fw-medium text-muted text-uppercase mt-1">Statut</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="block block-rounded text-center">
        <div class="block-content block-content-full">
          <span class="badge bg-warning p-2">#{{ $videoContentPlan->priority }}</span>
          <div class="fs-sm fw-medium text-muted text-uppercase mt-1">Priorité</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="block block-rounded text-center">
        <div class="block-content block-content-full">
          <div class="text-warning">
            @for($i = 1; $i <= 5; $i++)
              <i class="fa fa-star{{ $i <= $videoContentPlan->viral_potential ? '' : '-o' }}"></i>
            @endfor
          </div>
          <div class="fs-sm fw-medium text-muted text-uppercase mt-1">Potentiel viral</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="block block-rounded text-center">
        <div class="block-content block-content-full">
          <span class="badge bg-primary p-2">{{ $videoContentPlan->total_videos }}</span>
          <div class="fs-sm fw-medium text-muted text-uppercase mt-1">Total vidéos</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Dates -->
  @if($videoContentPlan->planned_date || $videoContentPlan->completed_date)
    <div class="block block-rounded mb-4">
      <div class="block-header block-header-default">
        <h3 class="block-title">
          <i class="fa fa-calendar me-1"></i> Planification
        </h3>
      </div>
      <div class="block-content">
        <div class="row">
          @if($videoContentPlan->planned_date)
            <div class="col-md-6">
              <strong>Date prévue :</strong>
              <div class="text-muted">{{ $videoContentPlan->planned_date->format('d/m/Y') }}</div>
            </div>
          @endif
          @if($videoContentPlan->completed_date)
            <div class="col-md-6">
              <strong>Date de finalisation :</strong>
              <div class="text-success">{{ $videoContentPlan->completed_date->format('d/m/Y') }}</div>
            </div>
          @endif
        </div>
      </div>
    </div>
  @endif

  <!-- Notes -->
  @if($videoContentPlan->notes)
    <div class="block block-rounded mb-4">
      <div class="block-header block-header-default">
        <h3 class="block-title">
          <i class="fa fa-sticky-note me-1"></i> Notes
        </h3>
      </div>
      <div class="block-content">
        <p class="text-muted">{{ $videoContentPlan->notes }}</p>
      </div>
    </div>
  @endif

  <!-- Platform Details -->
  <div class="block block-rounded">
    <div class="block-content">
      @foreach($videoContentPlan->platforms as $platform)
        @php
          $platformDetail = $platformDetails[$platform] ?? null;
          $videos = $platformDetail['videos'] ?? [];
          $platformIcons = [
            'youtube' => ['fab fa-youtube', 'text-danger', '📺'],
            'tiktok' => ['fab fa-tiktok', 'text-dark', '🎵'],
            'linkedin' => ['fab fa-linkedin', 'text-info', '💼'],
            'instagram' => ['fab fa-instagram', 'text-pink', '📸'],
            'facebook' => ['fab fa-facebook', 'text-primary', '👥'],
          ];
          $icon = $platformIcons[$platform] ?? ['fa fa-video', 'text-muted', '🎥'];
        @endphp

        <div class="block block-rounded border-2 border-{{ str_replace('text-', '', $icon[1]) }} mb-3 platform-section" data-platform="{{ $platform }}">
          <div class="block-header block-header-default platform-header" style="cursor: pointer; transition: background-color 0.2s ease;" onclick="togglePlatform('{{ $platform }}')"
               onmouseover="this.style.backgroundColor='#f8f9fa'"
               onmouseout="this.style.backgroundColor=''">
            <h4 class="block-title d-flex align-items-center">
              <span class="me-2">{{ $icon[2] }}</span>
              <i class="{{ $icon[0] }} {{ $icon[1] }} me-2"></i>
              {{ ucfirst($platform) }}
              <span class="badge bg-light text-dark ms-2">{{ count($videos) }} vidéo{{ count($videos) > 1 ? 's' : '' }}</span>
              <i class="fa fa-chevron-down ms-auto platform-toggle-icon" id="toggle-icon-{{ $platform }}"></i>
            </h4>
          </div>
          <div class="block-content platform-content" id="content-{{ $platform }}" style="display: none;">
            @if(count($videos) > 0)
              @foreach($videos as $index => $video)
                <div class="border rounded p-4 mb-4 bg-light">
                  <!-- Titre et description -->
                  <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <h5 class="fw-bold mb-0 text-primary">{{ $video['title'] ?? 'Vidéo #' . ($index + 1) }}</h5>
                      @php
                        $videoIdea = $videoContentPlan->videoIdeas()
                          ->where('platform', $platform)
                          ->where('video_index', $index)
                          ->first();
                      @endphp
                      @if($videoIdea && $video['filming_date'])
                        <div class="text-end">
                          <small class="text-muted d-block">🎬 Tournage</small>
                          <div class="editable-filming-date" data-video-idea-id="{{ $videoIdea->id }}">
                            <input type="date"
                                   class="form-control form-control-sm d-inline-block w-auto filming-date-input"
                                   value="{{ $video['filming_date']->format('Y-m-d') }}"
                                   min="{{ today()->format('Y-m-d') }}"
                                   style="font-size: 12px;">
                          </div>
                          <div class="editable-filming-time mt-1" data-video-idea-id="{{ $videoIdea->id }}">
                            <div class="d-flex gap-1">
                              <input type="time"
                                     class="form-control form-control-sm filming-start-time"
                                     value="{{ explode(' - ', $video['filming_time'])[0] ?? '09:00' }}"
                                     style="font-size: 12px; width: 80px;">
                              <span class="align-self-center" style="font-size: 12px;">-</span>
                              <input type="time"
                                     class="form-control form-control-sm filming-end-time"
                                     value="{{ explode(' - ', $video['filming_time'])[1] ?? '11:00' }}"
                                     style="font-size: 12px; width: 80px;">
                            </div>
                          </div>
                        </div>
                      @elseif(isset($video['filming_date']))
                        <div class="text-end">
                          <small class="text-muted d-block">🎬 Tournage</small>
                          <span class="badge bg-warning">Lecture seule</span>
                          <small class="text-muted d-block">{{ $video['filming_time'] ?? 'Horaire non défini' }}</small>
                        </div>
                      @endif
                    </div>
                    <p class="text-muted mb-2">{{ $video['description'] ?? 'Aucune description' }}</p>
                  </div>

                  <!-- Métadonnées principales -->
                  <div class="row mb-3">
                    <div class="col-md-3">
                      <strong><i class="fa fa-clock me-1"></i> Durée :</strong><br>
                      <span class="badge bg-info">{{ $video['duration'] ?? 'Non définie' }}</span>
                    </div>
                    <div class="col-md-3">
                      <strong><i class="fa fa-bullhorn me-1"></i> Hook :</strong><br>
                      <span class="text-primary fw-medium">{{ $video['hook'] ?? 'Non défini' }}</span>
                    </div>
                    <div class="col-md-3">
                      <strong><i class="fa fa-video me-1"></i> Type :</strong><br>
                      <span class="badge bg-secondary">{{ $video['video_type'] ?? 'Non défini' }}</span>
                    </div>
                    <div class="col-md-3">
                      <strong><i class="fa fa-chart-line me-1"></i> Difficulté :</strong><br>
                      <span class="badge bg-warning">{{ $video['difficulty'] ?? 'Non définie' }}</span>
                    </div>
                  </div>

                  <!-- Tags -->
                  @if(isset($video['tags']) && is_array($video['tags']))
                    <div class="mb-3">
                      <strong><i class="fa fa-tags me-1"></i> Tags :</strong><br>
                      @foreach($video['tags'] as $tag)
                        <span class="badge bg-primary me-1 mb-1">{{ $tag }}</span>
                      @endforeach
                    </div>
                  @endif

                  <!-- Audience et CTA -->
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <strong><i class="fa fa-users me-1"></i> Audience cible :</strong><br>
                      <span class="text-muted">{{ $video['target_audience'] ?? 'Non définie' }}</span>
                    </div>
                    <div class="col-md-6">
                      <strong><i class="fa fa-mouse-pointer me-1"></i> Call-to-Action :</strong><br>
                      <span class="text-success fw-medium">{{ $video['call_to_action'] ?? 'Non défini' }}</span>
                    </div>
                  </div>

                  <!-- Dates calculées automatiquement -->
                  @if($videoIdea)
                    <div class="alert alert-info">
                      <h6 class="alert-heading"><i class="fa fa-clock me-1"></i> Planning automatique</h6>
                      <div class="row" id="calculated-dates-{{ $videoIdea->id }}">
                        <div class="col-md-4">
                          <strong>🎬 Tournage :</strong><br>
                          <span class="filming-date-display">{{ $video['filming_date']?->format('d/m/Y') ?? 'Non planifié' }}</span><br>
                          <small class="text-muted filming-time-display">{{ $video['filming_time'] ?? 'Horaire non défini' }}</small>
                        </div>
                        <div class="col-md-4">
                          <strong>✂️ Montage :</strong><br>
                          <span class="editing-date-display">{{ $video['editing_date']?->format('d/m/Y') ?? 'À calculer' }}</span><br>
                          <small class="text-muted editing-time-display">{{ isset($video['editing_time']) ? $video['editing_time'] : 'Même créneau' }}</small>
                        </div>
                        <div class="col-md-4">
                          <strong>📤 Publication :</strong><br>
                          <span class="publication-date-display">{{ $video['publication_date']?->format('d/m/Y') ?? 'À calculer' }}</span><br>
                          <small class="text-muted publication-time-display">{{ isset($video['publication_time']) ? $video['publication_time'] : 'Heure optimale' }}</small>
                        </div>
                      </div>
                    </div>
                  @endif

                  <!-- Détails techniques (TikTok) -->
                  @if($platform === 'tiktok')
                    <div class="row mb-3">
                      @if(isset($video['music']))
                        <div class="col-md-6">
                          <strong><i class="fa fa-music me-1"></i> Musique :</strong><br>
                          <span class="text-muted">{{ $video['music'] }}</span>
                        </div>
                      @endif
                      @if(isset($video['transitions']))
                        <div class="col-md-6">
                          <strong><i class="fa fa-magic me-1"></i> Transitions :</strong><br>
                          <span class="text-muted">{{ $video['transitions'] }}</span>
                        </div>
                      @endif
                    </div>
                  @endif

                  <!-- Idées thumbnail -->
                  @if(isset($video['thumbnail_ideas']))
                    <div class="alert alert-info mb-0">
                      <strong><i class="fa fa-image me-1"></i> Idées thumbnail :</strong><br>
                      {{ $video['thumbnail_ideas'] }}
                    </div>
                  @endif
                </div>
              @endforeach
            @else
              <div class="text-center py-4">
                <div class="text-muted">
                  <i class="fa fa-video fa-2x mb-2"></i>
                  <p>Aucune vidéo planifiée pour cette plateforme</p>
                </div>
              </div>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <!-- Scripts de tournage -->
  <div class="block block-rounded border-dashed">
    <div class="block-content text-center py-4">
      <div class="mb-3">
        <i class="fa fa-external-link-alt fa-2x text-muted"></i>
      </div>
      <h4 class="fw-bold">Scripts de tournage</h4>
      <p class="text-muted mb-3">
        Consultez les scripts détaillés pour ce workflow dans le fichier SCRIPTS_TOURNAGE_TOP4.md
      </p>
      <a href="/SCRIPTS_TOURNAGE_TOP4.md" target="_blank" class="btn btn-outline-primary">
        <i class="fa fa-external-link-alt me-1"></i> Voir les scripts
      </a>
    </div>
  </div>
</div>
<!-- END Page Content -->

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
// === Gestion des plateformes collapsables ===
function togglePlatform(platform) {
    const content = document.getElementById(`content-${platform}`);
    const icon = document.getElementById(`toggle-icon-${platform}`);

    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
        // Sauvegarder l'état ouvert
        localStorage.setItem(`platform-${platform}-open`, 'true');
    } else {
        content.style.display = 'none';
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
        // Supprimer l'état ouvert (défaut fermé)
        localStorage.removeItem(`platform-${platform}-open`);
    }
}

// Fonction pour ouvrir/fermer toutes les plateformes
function toggleAllPlatforms(open = null) {
    const platforms = document.querySelectorAll('.platform-section');

    platforms.forEach(section => {
        const platform = section.dataset.platform;
        const content = document.getElementById(`content-${platform}`);
        const icon = document.getElementById(`toggle-icon-${platform}`);

        if (open === true || (open === null && content.style.display === 'none')) {
            content.style.display = 'block';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
            localStorage.setItem(`platform-${platform}-open`, 'true');
        } else {
            content.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
            localStorage.removeItem(`platform-${platform}-open`);
        }
    });
}

// Fonction pour initialiser l'état des plateformes au chargement
function initializePlatformStates() {
    const platforms = document.querySelectorAll('.platform-section');

    platforms.forEach(section => {
        const platform = section.dataset.platform;
        const content = document.getElementById(`content-${platform}`);
        const icon = document.getElementById(`toggle-icon-${platform}`);

        // Par défaut tout fermé, sauf si explicitement ouvert dans localStorage
        const isOpen = localStorage.getItem(`platform-${platform}-open`) === 'true';

        if (isOpen) {
            content.style.display = 'block';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            content.style.display = 'none';
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser l'état des plateformes
    initializePlatformStates();

    // Configuration CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Fonction utilitaire pour faire des requêtes AJAX
    function makeAjaxRequest(url, method, data = {}) {
        return fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
    }

    // Fonction pour afficher les notifications
    function showNotification(type, message) {
        One.helpers('jq-notify', {
            type: type,
            icon: type === 'success' ? 'fa fa-check me-1' : 'fa fa-exclamation-triangle me-1',
            message: message
        });
    }

    // Fonction pour mettre à jour l'affichage des dates calculées
    function updateCalculatedDates(videoIdeaId, data) {
        const container = document.getElementById(`calculated-dates-${videoIdeaId}`);
        if (!container) return;

        // Mettre à jour les dates d'affichage
        const filmingDateDisplay = container.querySelector('.filming-date-display');
        const filmingTimeDisplay = container.querySelector('.filming-time-display');
        const editingDateDisplay = container.querySelector('.editing-date-display');
        const editingTimeDisplay = container.querySelector('.editing-time-display');
        const publicationDateDisplay = container.querySelector('.publication-date-display');
        const publicationTimeDisplay = container.querySelector('.publication-time-display');

        if (filmingDateDisplay && data.filming_date_formatted) {
            filmingDateDisplay.textContent = data.filming_date_formatted;
        }
        if (filmingTimeDisplay && data.filming_time) {
            filmingTimeDisplay.textContent = data.filming_time;
        }
        if (editingDateDisplay && data.editing_date_formatted) {
            editingDateDisplay.textContent = data.editing_date_formatted;
        }
        if (editingTimeDisplay && data.editing_time) {
            editingTimeDisplay.textContent = data.editing_time;
        }
        if (publicationDateDisplay && data.publication_date_formatted) {
            publicationDateDisplay.textContent = data.publication_date_formatted;
        }
        if (publicationTimeDisplay && data.publication_time) {
            publicationTimeDisplay.textContent = data.publication_time;
        }
    }

    // Gérer les changements de date de tournage
    document.querySelectorAll('.filming-date-input').forEach(input => {
        input.addEventListener('change', function() {
            const videoIdeaId = this.closest('[data-video-idea-id]').dataset.videoIdeaId;
            const newDate = this.value;

            if (!newDate) return;

            // Montrer un indicateur de chargement
            this.style.opacity = '0.5';
            this.disabled = true;

            makeAjaxRequest(`/admin/video-ideas/${videoIdeaId}/update-filming-date`, 'PATCH', {
                filming_date: newDate
            })
            .then(response => response.json())
            .then(data => {
                this.style.opacity = '1';
                this.disabled = false;

                if (data.success) {
                    showNotification('success', data.message);
                    updateCalculatedDates(videoIdeaId, data.video_idea);
                } else {
                    showNotification('error', data.message);
                    // Revenir à la valeur précédente
                    this.value = this.defaultValue;
                }
            })
            .catch(error => {
                this.style.opacity = '1';
                this.disabled = false;
                showNotification('error', 'Erreur de connexion');
                this.value = this.defaultValue;
            });
        });
    });

    // Gérer les changements d'heure de tournage
    function handleTimeChange(videoIdeaId, startInput, endInput) {
        const startTime = startInput.value;
        const endTime = endInput.value;

        if (!startTime || !endTime) return;

        // Vérifier que l'heure de fin est après l'heure de début
        if (startTime >= endTime) {
            showNotification('error', 'L\'heure de fin doit être après l\'heure de début');
            return;
        }

        // Montrer un indicateur de chargement
        startInput.style.opacity = '0.5';
        endInput.style.opacity = '0.5';
        startInput.disabled = true;
        endInput.disabled = true;

        makeAjaxRequest(`/admin/video-ideas/${videoIdeaId}/update-filming-time`, 'PATCH', {
            filming_start_time: startTime,
            filming_end_time: endTime
        })
        .then(response => response.json())
        .then(data => {
            startInput.style.opacity = '1';
            endInput.style.opacity = '1';
            startInput.disabled = false;
            endInput.disabled = false;

            if (data.success) {
                showNotification('success', data.message);
                updateCalculatedDates(videoIdeaId, data.video_idea);
            } else {
                showNotification('error', data.message);
                // Revenir aux valeurs précédentes
                startInput.value = startInput.defaultValue;
                endInput.value = endInput.defaultValue;
            }
        })
        .catch(error => {
            startInput.style.opacity = '1';
            endInput.style.opacity = '1';
            startInput.disabled = false;
            endInput.disabled = false;
            showNotification('error', 'Erreur de connexion');
            startInput.value = startInput.defaultValue;
            endInput.value = endInput.defaultValue;
        });
    }

    // Écouter les changements d'heures avec debounce
    document.querySelectorAll('.editable-filming-time').forEach(container => {
        const videoIdeaId = container.dataset.videoIdeaId;
        const startInput = container.querySelector('.filming-start-time');
        const endInput = container.querySelector('.filming-end-time');

        let timeoutId;

        function onTimeChange() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                handleTimeChange(videoIdeaId, startInput, endInput);
            }, 1000); // Attendre 1 seconde après le dernier changement
        }

        startInput.addEventListener('change', onTimeChange);
        endInput.addEventListener('change', onTimeChange);
    });

    // Vérification des conflits en temps réel (optionnel)
    document.querySelectorAll('.filming-date-input').forEach(input => {
        input.addEventListener('input', function() {
            const videoIdeaId = this.closest('[data-video-idea-id]').dataset.videoIdeaId;
            const container = this.closest('[data-video-idea-id]');
            const startTimeInput = container.querySelector('.filming-start-time');
            const endTimeInput = container.querySelector('.filming-end-time');

            // Vérifier les conflits (sans debounce pour un feedback immédiat)
            if (this.value && startTimeInput.value && endTimeInput.value) {
                fetch(`/admin/video-ideas/${videoIdeaId}/check-conflicts?date=${this.value}&start_time=${startTimeInput.value}&end_time=${endTimeInput.value}`)
                .then(response => response.json())
                .then(data => {
                    if (data.has_conflicts) {
                        this.style.borderColor = '#dc3545';
                        this.title = `Conflit détecté avec ${data.conflicts.length} autre(s) tournage(s)`;
                    } else {
                        this.style.borderColor = '#28a745';
                        this.title = 'Créneau disponible';
                    }
                })
                .catch(() => {
                    this.style.borderColor = '';
                    this.title = '';
                });
            }
        });
    });
});
</script>

@endsection