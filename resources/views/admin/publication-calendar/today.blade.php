@extends('layouts.backend')

@section('title', 'Planning des 4 Prochains Jours')

@section('css')
<style>
.hover-shadow:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.transition-all {
    transition: all 0.2s ease;
}

.task-card {
    cursor: pointer;
}
</style>
@endsection

@section('content')
<div class="bg-body-light">
    <div class="content content-full">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
            <div class="flex-grow-1">
                <h1 class="h3 fw-bold mb-1">
                    🎬 Planning Vidéo - 4 Prochains Jours
                </h1>
                <h2 class="fs-base lh-base fw-medium text-muted mb-0">
                    Tournages, Montages et Publications à venir
                </h2>
            </div>
            <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-alt">
                    <li class="breadcrumb-item">
                        <a class="link-fx" href="{{ route('admin.dashboard') }}">Admin</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a class="link-fx" href="{{ route('admin.publication-calendar.index') }}">Calendrier</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">Planning</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="content">
    <!-- Statistiques globales -->
    <div class="row">
        <div class="col-6 col-lg-3">
            <div class="block block-rounded block-link-shadow text-center">
                <div class="block-content block-content-full">
                    <div class="fs-2 fw-bold text-primary">{{ $stats['total_scheduled'] }}</div>
                    <div class="fs-sm fw-medium text-muted text-uppercase">Publications Planifiées</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="block block-rounded block-link-shadow text-center">
                <div class="block-content block-content-full">
                    <div class="fs-2 fw-bold text-warning">{{ $stats['in_production'] }}</div>
                    <div class="fs-sm fw-medium text-muted text-uppercase">En Production</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="block block-rounded block-link-shadow text-center">
                <div class="block-content block-content-full">
                    <div class="fs-2 fw-bold text-info">{{ $stats['this_week'] }}</div>
                    <div class="fs-sm fw-medium text-muted text-uppercase">Cette Semaine</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="block block-rounded block-link-shadow text-center">
                <div class="block-content block-content-full">
                    <div class="fs-2 fw-bold text-success">{{ $stats['published_this_month'] }}</div>
                    <div class="fs-sm fw-medium text-muted text-uppercase">Publiées ce Mois</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Planning jour par jour -->
    @foreach($dailyTasks as $dateKey => $dayData)
        @php
            $date = $dayData['date'];
            $tasks = $dayData['tasks'];
            $isToday = $date->isToday();
            $dayClass = $isToday ? 'border-primary' : 'border-light';
        @endphp

        <div class="block block-rounded {{ $dayClass }} mb-4">
            <div class="block-header {{ $isToday ? 'bg-primary-light' : 'block-header-default' }}">
                <h3 class="block-title {{ $isToday ? 'text-primary' : '' }}">
                    @if($isToday)
                        <i class="fa fa-star me-2 text-warning"></i>
                    @endif
                    {{ $date->translatedFormat('l j F Y') }}
                    @if($isToday)
                        <span class="badge bg-primary ms-2">Aujourd'hui</span>
                    @endif
                </h3>
                <div class="block-options">
                    <span class="badge bg-info">
                        {{ count($tasks['filming']) + count($tasks['editing']) + count($tasks['publishing']) }} tâches
                    </span>
                </div>
            </div>
            <div class="block-content">
                @if(count($tasks['filming']) + count($tasks['editing']) + count($tasks['publishing']) === 0)
                    <div class="text-center py-4">
                        <i class="fa fa-calendar-check fa-2x text-success mb-2"></i>
                        <p class="text-muted">Aucune tâche prévue ce jour</p>
                    </div>
                @else
                    <div class="row">
                        <!-- Tournages -->
                        @if(count($tasks['filming']) > 0)
                        <div class="col-lg-4 mb-3">
                            <div class="border rounded p-3 h-100" style="border-color: #ffc107 !important;">
                                <h5 class="text-warning mb-3">
                                    <i class="fa fa-video me-2"></i>
                                    Tournages ({{ count($tasks['filming']) }})
                                </h5>
                                @foreach($tasks['filming'] as $publication)
                                <a href="{{ route('admin.video-content.show', $publication['workflow_id']) }}" class="text-decoration-none">
                                    <div class="mb-3 p-2 bg-light rounded border-0 shadow-sm hover-shadow transition-all">
                                        <div class="d-flex align-items-start">
                                            <div class="me-2">
                                                <span class="badge" style="background-color: {{ getPlatformColor($publication['platform']) }}; color: white;">
                                                    {{ getPlatformIcon($publication['platform']) }} {{ ucfirst($publication['platform']) }}
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 text-dark">{{ $publication['title'] }}</h6>
                                                <p class="fs-sm text-muted mb-2">{{ $publication['description'] ? Str::limit($publication['description'], 80) : 'Pas de description' }}</p>
                                                <div class="fs-xs text-muted">
                                                    <strong>Workflow:</strong> {{ $publication['workflow'] }}<br>
                                                    <strong>Horaire:</strong> {{ $publication['start_time'] }} - {{ $publication['end_time'] }}<br>
                                                    <strong>Durée:</strong> {{ $publication['duration'] }}
                                                </div>
                                                @if(isset($publication['notes']) && $publication['notes'])
                                                    <div class="mt-2 p-2 bg-warning bg-opacity-10 border border-warning rounded">
                                                        <small><strong>Notes:</strong> {{ $publication['notes'] }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ms-2">
                                                <i class="fa fa-external-link-alt text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Montages -->
                        @if(count($tasks['editing']) > 0)
                        <div class="col-lg-4 mb-3">
                            <div class="border rounded p-3 h-100" style="border-color: #17a2b8 !important;">
                                <h5 class="text-info mb-3">
                                    <i class="fa fa-cut me-2"></i>
                                    Montages ({{ count($tasks['editing']) }})
                                </h5>
                                @foreach($tasks['editing'] as $publication)
                                <a href="{{ route('admin.video-content.show', $publication['workflow_id']) }}" class="text-decoration-none">
                                    <div class="mb-3 p-2 bg-light rounded border-0 shadow-sm hover-shadow transition-all">
                                        <div class="d-flex align-items-start">
                                            <div class="me-2">
                                                <span class="badge" style="background-color: {{ getPlatformColor($publication['platform']) }}; color: white;">
                                                    {{ getPlatformIcon($publication['platform']) }} {{ ucfirst($publication['platform']) }}
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 text-dark">{{ $publication['title'] }}</h6>
                                                <p class="fs-sm text-muted mb-2">{{ $publication['description'] ? Str::limit($publication['description'], 80) : 'Pas de description' }}</p>
                                                <div class="fs-xs text-muted">
                                                    <strong>Workflow:</strong> {{ $publication['workflow'] }}<br>
                                                    <strong>Horaire:</strong> {{ $publication['start_time'] }} - {{ $publication['end_time'] }}<br>
                                                    <strong>Durée:</strong> {{ $publication['duration'] }}
                                                </div>
                                                @if(isset($publication['notes']) && $publication['notes'])
                                                    <div class="mt-2 p-2 bg-info bg-opacity-10 border border-info rounded">
                                                        <small><strong>Notes:</strong> {{ $publication['notes'] }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ms-2">
                                                <i class="fa fa-external-link-alt text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Publications -->
                        @if(count($tasks['publishing']) > 0)
                        <div class="col-lg-4 mb-3">
                            <div class="border rounded p-3 h-100" style="border-color: #28a745 !important;">
                                <h5 class="text-success mb-3">
                                    <i class="fa fa-upload me-2"></i>
                                    Publications ({{ count($tasks['publishing']) }})
                                </h5>
                                @php
                                    $sortedPublications = collect($tasks['publishing'])->sortBy('start_time');
                                    $grouped = collect($sortedPublications)->groupBy('workflow');
                                @endphp
                                @foreach($grouped as $workflow => $videos)
                                    @php
                                        $firstVideo = $videos->first();
                                        // Récupérer l'ID du workflow à partir du premier vidéo
                                        $workflowId = \App\Models\VideoIdea::find($firstVideo['id'])->video_content_plan_id ?? 0;
                                    @endphp
                                    <div class="mb-3">
                                        <h6 class="fw-bold mb-2">
                                            <a href="https://automatehub.fr/admin/video-content/{{ $workflowId }}" class="text-decoration-none">
                                                <i class="fa fa-play-circle me-1"></i>{{ $workflow }}
                                            </a>
                                        </h6>
                                        <ul class="list-unstyled ms-3">
                                            @foreach($videos as $publication)
                                            <li class="mb-2">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-success me-2">{{ $publication['start_time'] }}</span>
                                                    <span class="badge me-2" style="background-color: {{ getPlatformColor($publication['platform']) }}; color: white;">
                                                        {{ getPlatformIcon($publication['platform']) }} {{ ucfirst($publication['platform']) }}
                                                    </span>
                                                    <span class="fs-sm">{{ $publication['title'] }}</span>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    <!-- Actions rapides -->
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <i class="fa fa-bolt me-2"></i>
                Actions Rapides
            </h3>
        </div>
        <div class="block-content">
            <div class="row">
                <div class="col-md-3">
                    <button type="button" class="btn btn-warning w-100 mb-2" onclick="bulkUpdateStatus('filming', 'today')">
                        <i class="fa fa-video me-1"></i> Démarrer Tournages du Jour
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-info w-100 mb-2" onclick="bulkUpdateStatus('editing', 'today')">
                        <i class="fa fa-cut me-1"></i> Passer en Montage
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-success w-100 mb-2" onclick="bulkUpdateStatus('published', 'today')">
                        <i class="fa fa-upload me-1"></i> Publier du Jour
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.publication-calendar.index') }}" class="btn btn-primary w-100 mb-2">
                        <i class="fa fa-calendar me-1"></i> Voir Calendrier Complet
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
function markAsFilmed(publicationId) {
    if (confirm('Marquer cette vidéo comme filmée ?')) {
        updateStatus(publicationId, 'filmed');
    }
}

function markAsEdited(publicationId) {
    if (confirm('Marquer cette vidéo comme montée ?')) {
        updateStatus(publicationId, 'edited');
    }
}

function markAsPublished(publicationId) {
    const url = prompt('URL de la publication (optionnel):');

    fetch(`/admin/publication-calendar/${publicationId}/mark-published`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            published_url: url
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function updateStatus(publicationId, status) {
    fetch(`/admin/publication-calendar/${publicationId}/update-status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function bulkUpdateStatus(status, period) {
    const statusText = status === 'filming' ? 'en tournage' : (status === 'editing' ? 'en montage' : 'publiées');
    const periodText = period === 'today' ? "d'aujourd'hui" : 'sélectionnées';

    if (confirm(`Marquer toutes les vidéos ${periodText} comme ${statusText} ?`)) {
        // Implementation for bulk update can be added here
        alert('Fonctionnalité en cours de développement');
    }
}
</script>
@endsection

@php
function getPlatformColor($platform) {
    return match($platform) {
        'youtube' => '#FF0000',
        'youtube_shorts' => '#FF4444',
        'tiktok' => '#000000',
        'linkedin' => '#0077B5',
        'instagram' => '#E4405F',
        'facebook' => '#1877F2',
        default => '#6c757d'
    };
}

function getPlatformIcon($platform) {
    return match($platform) {
        'youtube' => '🎬',
        'youtube_shorts' => '🎬',
        'tiktok' => '🎵',
        'linkedin' => '💼',
        'instagram' => '📸',
        'facebook' => '👥',
        default => '📹'
    };
}
@endphp