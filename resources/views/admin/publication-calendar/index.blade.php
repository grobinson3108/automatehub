@extends('layouts.backend')

@section('title', 'Calendrier de Publication')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
<style>
.fc-event {
    border-radius: 4px !important;
    font-size: 12px !important;
    padding: 2px 4px !important;
    margin: 1px 0 !important;
}

.fc-event.published {
    opacity: 1 !important;
    font-weight: bold !important;
}

.fc-event.scheduled {
    opacity: 0.8 !important;
}

.fc-event.filming {
    opacity: 0.9 !important;
    background: repeating-linear-gradient(
        45deg,
        transparent,
        transparent 2px,
        rgba(255,255,255,0.1) 2px,
        rgba(255,255,255,0.1) 4px
    ) !important;
}

.fc-event.editing {
    opacity: 0.85 !important;
    background: repeating-linear-gradient(
        90deg,
        transparent,
        transparent 3px,
        rgba(255,255,255,0.15) 3px,
        rgba(255,255,255,0.15) 6px
    ) !important;
}

.fc-event:hover {
    opacity: 1 !important;
    transform: scale(1.02);
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3) !important;
}

/* Publications futures grisées */
.fc-event.future {
    opacity: 0.5 !important;
    filter: grayscale(30%);
}

/* Améliorer la lisibilité */
.fc-daygrid-event-harness {
    margin-bottom: 1px !important;
}

.fc-event-title {
    font-weight: 500 !important;
}

/* Couleurs par plateforme avec opacité */
.fc-event.youtube {
    background-color: rgba(255, 0, 0, 0.8) !important;
    border-color: #FF0000 !important;
}

.fc-event.tiktok {
    background-color: rgba(0, 0, 0, 0.8) !important;
    border-color: #000000 !important;
}

.fc-event.instagram {
    background-color: rgba(228, 64, 95, 0.8) !important;
    border-color: #E4405F !important;
}

.fc-event.linkedin {
    background-color: rgba(0, 119, 181, 0.8) !important;
    border-color: #0077B5 !important;
}

.fc-event.facebook {
    background-color: rgba(24, 119, 242, 0.8) !important;
    border-color: #1877F2 !important;
}
</style>
@endsection

@section('content')
<div class="bg-body-light">
    <div class="content content-full">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
            <div class="flex-grow-1">
                <h1 class="h3 fw-bold mb-1">
                    📅 Calendrier de Publication
                </h1>
                <h2 class="fs-base lh-base fw-medium text-muted mb-0">
                    Quand et où publier vos contenus multi-plateformes
                </h2>
            </div>
            <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-alt">
                    <li class="breadcrumb-item">
                        <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">Calendrier Publication</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="content">
    <!-- Actions rapides -->
    <div class="row pb-4">
        <div class="col-md-8">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-fw fa-bolt me-1"></i>
                        Actions rapides
                    </h3>
                </div>
                <div class="block-content pb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('admin.publication-calendar.today') }}" class="btn btn-primary w-100 mb-2">
                                <i class="fa fa-tasks me-1"></i>
                                Planning 4 Jours
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.video-ideas.index') }}" class="btn btn-info w-100 mb-2">
                                <i class="fa fa-lightbulb me-1"></i>
                                Idées Vidéos
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.video-content.index') }}" class="btn btn-warning w-100 mb-2">
                                <i class="fa fa-list me-1"></i>
                                Workflows
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-success w-100 mb-2" onclick="generateAllSchedules()">
                                <i class="fa fa-magic me-1"></i>
                                Générer Planning
                            </button>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.publication-calendar.export') }}" class="btn btn-info w-100 mb-2">
                                <i class="fa fa-download me-1"></i>
                                Exporter CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-fw fa-chart-pie me-1"></i>
                        Statistiques
                    </h3>
                </div>
                <div class="block-content pb-4">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="py-2">
                                <div class="fs-3 fw-bold text-primary">{{ $stats['total'] }}</div>
                                <div class="fs-sm fw-medium text-muted">
                                    <i class="fa fa-video me-1"></i>Total
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="py-2">
                                <div class="fs-3 fw-bold text-success">{{ $stats['thisWeek'] }}</div>
                                <div class="fs-sm fw-medium text-muted">
                                    <i class="fa fa-calendar-week me-1"></i>Cette semaine
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="py-2">
                                <div class="fs-3 fw-bold text-info">{{ $stats['published'] }}</div>
                                <div class="fs-sm fw-medium text-muted">
                                    <i class="fa fa-check-circle me-1"></i>Publiées
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="py-2">
                                <div class="fs-3 fw-bold text-warning">{{ $stats['scheduled'] }}</div>
                                <div class="fs-sm fw-medium text-muted">
                                    <i class="fa fa-clock me-1"></i>Planifiées
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="block block-rounded mb-4">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <i class="fa fa-fw fa-filter me-1"></i>
                Filtres
            </h3>
        </div>
        <div class="block-content pb-4">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fa fa-globe me-1"></i>Plateforme
                    </label>
                    <select class="form-select" id="platform-filter">
                        <option value="">Toutes les plateformes</option>
                        <option value="youtube">🔴 YouTube</option>
                        <option value="tiktok">⚫ TikTok</option>
                        <option value="linkedin">🔵 LinkedIn</option>
                        <option value="instagram">🟣 Instagram</option>
                        <option value="facebook">🔵 Facebook</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fa fa-tasks me-1"></i>Statut
                    </label>
                    <select class="form-select" id="status-filter">
                        <option value="">Tous les statuts</option>
                        <option value="scheduled">📅 Planifié</option>
                        <option value="filming">🎬 En tournage</option>
                        <option value="editing">✂️ En montage</option>
                        <option value="published">✅ Publié</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fa fa-video me-1"></i>Workflow
                    </label>
                    <select class="form-select" id="workflow-filter">
                        <option value="">Tous les workflows</option>
                        @foreach($workflows as $workflow)
                            <option value="{{ $workflow->id }}">{{ $workflow->workflow_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-alt-secondary w-100" onclick="resetFilters()">
                        <i class="fa fa-refresh me-1"></i>
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendrier -->
    <div class="block block-rounded mb-4">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <i class="fa fa-fw fa-calendar me-1"></i>
                Vue Calendrier
            </h3>
            <div class="block-options">
                <button type="button" class="btn-block-option" onclick="calendar.changeView('dayGridMonth')">
                    <i class="fa fa-calendar-alt me-1"></i>Mois
                </button>
                <button type="button" class="btn-block-option" onclick="calendar.changeView('timeGridWeek')">
                    <i class="fa fa-calendar-week me-1"></i>Semaine
                </button>
                <button type="button" class="btn-block-option" onclick="calendar.changeView('listWeek')">
                    <i class="fa fa-list me-1"></i>Liste
                </button>
            </div>
        </div>
        <div class="block-content pb-4">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Liste des publications -->
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <i class="fa fa-fw fa-list me-1"></i>
                Liste des Publications
            </h3>
        </div>
        <div class="block-content pb-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="fa fa-calendar me-1"></i>Date & Heure</th>
                            <th><i class="fa fa-file-text me-1"></i>Titre</th>
                            <th><i class="fa fa-globe me-1"></i>Plateforme</th>
                            <th><i class="fa fa-video me-1"></i>Workflow</th>
                            <th><i class="fa fa-tasks me-1"></i>Statut</th>
                            <th><i class="fa fa-eye me-1"></i>Vues estimées</th>
                            <th class="text-center" style="width: 100px;"><i class="fa fa-cogs"></i></th>
                        </tr>
                    </thead>
                    <tbody id="publications-table-body">
                        @foreach($publications as $publication)
                            <tr data-platform="{{ $publication->platform }}" data-status="{{ $publication->status }}" data-workflow="{{ $publication->video_content_plan_id }}">
                                <td>
                                    <div class="fw-semibold">
                                        <i class="fa fa-calendar-day me-1 text-primary"></i>
                                        {{ $publication->scheduled_date->format('l j F Y') }}
                                    </div>
                                    <div class="fs-sm text-muted">
                                        <i class="fa fa-clock me-1"></i>
                                        <strong>{{ $publication->scheduled_time_formatted }}</strong>
                                    </div>
                                    @if($publication->scheduled_date->isToday())
                                        <span class="badge bg-warning text-dark">
                                            <i class="fa fa-exclamation me-1"></i>Aujourd'hui
                                        </span>
                                    @elseif($publication->scheduled_date->isTomorrow())
                                        <span class="badge bg-info">
                                            <i class="fa fa-arrow-right me-1"></i>Demain
                                        </span>
                                    @elseif($publication->scheduled_date->isPast())
                                        <span class="badge bg-danger">
                                            <i class="fa fa-exclamation-triangle me-1"></i>En retard
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ Str::limit($publication->title, 50) }}</div>
                                    <div class="fs-sm text-muted">{{ Str::limit($publication->description, 80) }}</div>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: {{ $publication->platform === 'youtube' ? '#FF0000' : ($publication->platform === 'tiktok' ? '#000000' : ($publication->platform === 'instagram' ? '#E4405F' : ($publication->platform === 'linkedin' ? '#0077B5' : '#1877F2'))) }}; color: white; font-size: 13px; padding: 6px 12px;">
                                        <i class="{{ $publication->platform_icon }} me-1"></i>
                                        {{ ucfirst($publication->platform) }}
                                    </span>
                                    @php
                                        $optimalTimes = [
                                            'youtube' => '18:00',
                                            'tiktok' => '19:00',
                                            'linkedin' => '08:00',
                                            'instagram' => '17:00',
                                            'facebook' => '15:00'
                                        ];
                                        $isOptimalTime = ($publication->scheduled_time_formatted === ($optimalTimes[$publication->platform] ?? ''));
                                    @endphp
                                    @if($isOptimalTime)
                                        <div class="fs-xs text-success mt-1">
                                            <i class="fa fa-check me-1"></i>Heure optimale
                                        </div>
                                    @else
                                        <div class="fs-xs text-muted mt-1">
                                            <i class="fa fa-info-circle me-1"></i>Optimal: {{ $optimalTimes[$publication->platform] ?? '12:00' }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fs-sm">
                                        <i class="fa fa-play-circle me-1 text-muted"></i>
                                        {{ Str::limit($publication->videoContentPlan->workflow_name, 30) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $publication->status === 'scheduled' ? 'secondary' : ($publication->status === 'filming' ? 'warning' : ($publication->status === 'editing' ? 'info' : 'success')) }}">
                                        <i class="fa fa-{{ $publication->status === 'scheduled' ? 'calendar' : ($publication->status === 'filming' ? 'video' : ($publication->status === 'editing' ? 'cut' : 'check')) }} me-1"></i>
                                        {{ $publication->status_text }}
                                    </span>
                                    @if($publication->status === 'scheduled' && $publication->scheduled_date->isToday())
                                        <div class="fs-xs text-warning mt-1">
                                            <i class="fa fa-bell me-1"></i>À publier aujourd'hui
                                        </div>
                                    @elseif($publication->status === 'scheduled' && $publication->scheduled_date->isTomorrow())
                                        <div class="fs-xs text-info mt-1">
                                            <i class="fa fa-clock me-1"></i>À publier demain
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($publication->estimated_views)
                                        <span class="fw-semibold text-primary">
                                            {{ number_format($publication->estimated_views) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.publication-calendar.show', $publication) }}" class="btn btn-sm btn-alt-secondary" title="Voir">
                                            <i class="fa fa-fw fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.publication-calendar.edit', $publication) }}" class="btn btn-sm btn-alt-secondary" title="Modifier">
                                            <i class="fa fa-fw fa-pencil-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
let calendar;

document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
    setupFilters();
});

function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        locale: 'fr',
        firstDay: 1, // Commencer par lundi (0 = dimanche, 1 = lundi)
        events: '{{ route("admin.publication-calendar.json") }}',
        eventClick: function(info) {
            window.location.href = `/admin/publication-calendar/${info.event.id}`;
        },
        eventDidMount: function(info) {
            // Ajouter les classes pour le styling
            const extendedProps = info.event.extendedProps;

            // Ajouter les classes de statut et plateforme
            info.el.classList.add(extendedProps.status);
            info.el.classList.add(extendedProps.platform);

            // Ajouter classe future si applicable
            if (extendedProps.isFuture) {
                info.el.classList.add('future');
            }

            // Ajouter une icône selon la plateforme
            const icons = {
                'youtube': 'fab fa-youtube',
                'tiktok': 'fab fa-tiktok',
                'instagram': 'fab fa-instagram',
                'linkedin': 'fab fa-linkedin',
                'facebook': 'fab fa-facebook'
            };

            const icon = icons[extendedProps.platform] || 'fa fa-video';
            const iconEl = document.createElement('i');
            iconEl.className = icon + ' me-1';
            iconEl.style.fontSize = '10px';

            // Insérer l'icône au début du titre
            const titleEl = info.el.querySelector('.fc-event-title');
            if (titleEl) {
                titleEl.insertBefore(iconEl, titleEl.firstChild);
            }
        }
    });

    calendar.render();
}

function setupFilters() {
    const platformFilter = document.getElementById('platform-filter');
    const statusFilter = document.getElementById('status-filter');
    const workflowFilter = document.getElementById('workflow-filter');

    [platformFilter, statusFilter, workflowFilter].forEach(filter => {
        filter.addEventListener('change', applyFilters);
    });
}

function applyFilters() {
    const platform = document.getElementById('platform-filter').value;
    const status = document.getElementById('status-filter').value;
    const workflow = document.getElementById('workflow-filter').value;

    const rows = document.querySelectorAll('#publications-table-body tr');

    rows.forEach(row => {
        let show = true;

        if (platform && row.dataset.platform !== platform) show = false;
        if (status && row.dataset.status !== status) show = false;
        if (workflow && row.dataset.workflow !== workflow) show = false;

        row.style.display = show ? '' : 'none';
    });

    // Mettre à jour le calendrier avec les filtres
    calendar.refetchEvents();
}

function resetFilters() {
    document.getElementById('platform-filter').value = '';
    document.getElementById('status-filter').value = '';
    document.getElementById('workflow-filter').value = '';
    applyFilters();
}

function generateAllSchedules() {
    if (confirm('Générer automatiquement les plannings pour tous les workflows ?')) {
        fetch('{{ route("admin.publication-calendar.generate-all") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la génération: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la génération');
        });
    }
}
</script>
@endsection