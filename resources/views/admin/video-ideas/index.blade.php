@extends('layouts.backend')

@section('title', 'Gestion des Idées Vidéos - Admin')

@section('content')
<div class="container-fluid">
    <!-- Header avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-video mr-2"></i>
                        Gestion des Idées Vidéos
                    </h4>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success" onclick="generateAllSchedules()">
                            <i class="fas fa-calendar-plus mr-1"></i>
                            Générer Tous les Plannings
                        </button>
                        <a href="{{ route('admin.video-ideas.daily-tasks') }}" class="btn btn-info">
                            <i class="fas fa-tasks mr-1"></i>
                            Tâches du Jour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fas fa-lightbulb fa-2x"></i>
                                        </div>
                                        <div>
                                            <div class="h4 mb-0">{{ $stats['total_ideas'] }}</div>
                                            <div class="small">Idées Vidéos</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fas fa-project-diagram fa-2x"></i>
                                        </div>
                                        <div>
                                            <div class="h4 mb-0">{{ $stats['total_workflows'] }}</div>
                                            <div class="small">Workflows Actifs</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fas fa-fire fa-2x"></i>
                                        </div>
                                        <div>
                                            <div class="h4 mb-0">{{ $stats['avg_viral_potential'] }}/10</div>
                                            <div class="small">Potentiel Viral Moyen</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fas fa-chart-bar fa-2x"></i>
                                        </div>
                                        <div>
                                            <div class="small">Répartition par Plateforme</div>
                                            <div class="mt-1">
                                                @foreach($stats['by_platform'] as $platform => $count)
                                                    <span class="badge badge-light mr-1">{{ ucfirst($platform) }}: {{ $count }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des workflows et leurs idées vidéos -->
    <div class="row">
        @foreach($workflows as $workflow)
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas fa-project-diagram mr-2 text-primary"></i>
                                {{ $workflow->workflow_name }}
                            </h5>
                            <small class="text-muted">
                                Priorité: {{ $workflow->priority }} |
                                Potentiel viral: {{ $workflow->viral_potential }}/10 |
                                {{ $workflow->videoIdeas->count() }} vidéos
                            </small>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="generateSchedule({{ $workflow->id }})">
                                <i class="fas fa-calendar-plus mr-1"></i>
                                Générer Planning
                            </button>
                            <a href="{{ route('admin.video-content.show', $workflow) }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-eye mr-1"></i>
                                Voir Workflow
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($workflow->videoIdeas->count() > 0)
                        <div class="row">
                            @foreach($workflow->videoIdeas->groupBy('platform') as $platform => $ideas)
                            <div class="col-md-4 mb-3">
                                <div class="card border-left-{{ getPlatformColor($platform) }}">
                                    <div class="card-header py-2">
                                        <h6 class="mb-0">
                                            <i class="{{ getPlatformIcon($platform) }} mr-2"></i>
                                            {{ ucfirst($platform) }}
                                            <span class="badge badge-secondary ml-2">{{ $ideas->count() }}</span>
                                        </h6>
                                    </div>
                                    <div class="card-body py-2">
                                        @foreach($ideas as $idea)
                                        <div class="mb-2 p-2 border rounded">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <a href="{{ route('admin.video-ideas.show', $idea) }}" class="font-weight-bold text-decoration-none">
                                                        {{ $idea->title }}
                                                    </a>
                                                    <div class="small text-muted mt-1">
                                                        {{ Str::limit($idea->description, 60) }}
                                                    </div>
                                                    <div class="mt-1">
                                                        <span class="badge badge-info">Viral: {{ $idea->viral_potential }}/10</span>
                                                        @if($idea->estimated_views)
                                                            <span class="badge badge-success">{{ number_format($idea->estimated_views) }} vues</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="dropdown ml-2">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('admin.video-ideas.show', $idea) }}">
                                                            <i class="fas fa-eye mr-2"></i>Voir
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('admin.video-ideas.edit', $idea) }}">
                                                            <i class="fas fa-edit mr-2"></i>Modifier
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-video fa-3x mb-3"></i>
                            <p>Aucune idée vidéo pour ce workflow</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        @if($workflows->count() === 0)
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-project-diagram fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun workflow disponible</h5>
                    <p class="text-muted">Créez des workflows avec un potentiel viral élevé pour commencer.</p>
                    <a href="{{ route('admin.video-content.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>
                        Créer un Workflow
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Génération de Planning</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Voulez-vous générer le planning pour ce workflow ?</p>
                <div class="form-group">
                    <label for="start_date">Date de début (Lundi de tournage) :</label>
                    <input type="date" class="form-control" id="start_date" value="{{ now()->next('monday')->format('Y-m-d') }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmGenerate">Générer</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentWorkflowId = null;

function generateSchedule(workflowId) {
    currentWorkflowId = workflowId;
    $('#confirmModal').modal('show');
}

function generateAllSchedules() {
    if (!confirm('Voulez-vous générer le planning pour tous les workflows ? Cela supprimera les anciens plannings.')) {
        return;
    }

    const startDate = document.getElementById('start_date')?.value || '{{ now()->next("monday")->format("Y-m-d") }}';

    $.ajax({
        url: '{{ route("admin.video-ideas.generate-all-schedules") }}',
        method: 'POST',
        data: {
            start_date: startDate,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                toastr.error('Erreur lors de la génération');
                if (response.errors) {
                    response.errors.forEach(error => toastr.error(error));
                }
            }
        },
        error: function() {
            toastr.error('Erreur lors de la génération du planning');
        }
    });
}

$(document).ready(function() {
    $('#confirmGenerate').click(function() {
        const startDate = $('#start_date').val();

        $.ajax({
            url: `{{ route("admin.video-ideas.generate-schedule", ":id") }}`.replace(':id', currentWorkflowId),
            method: 'POST',
            data: {
                start_date: startDate,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#confirmModal').modal('hide');
                if (response.success) {
                    toastr.success(response.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.error('Erreur lors de la génération');
                }
            },
            error: function() {
                $('#confirmModal').modal('hide');
                toastr.error('Erreur lors de la génération du planning');
            }
        });
    });
});
</script>
@endpush

@php
function getPlatformIcon($platform) {
    return match($platform) {
        'youtube' => 'fab fa-youtube text-danger',
        'tiktok' => 'fab fa-tiktok text-dark',
        'linkedin' => 'fab fa-linkedin text-info',
        'instagram' => 'fab fa-instagram text-pink',
        'facebook' => 'fab fa-facebook text-primary',
        default => 'fa fa-video text-muted'
    };
}

function getPlatformColor($platform) {
    return match($platform) {
        'youtube' => 'danger',
        'tiktok' => 'dark',
        'linkedin' => 'info',
        'instagram' => 'warning',
        'facebook' => 'primary',
        default => 'secondary'
    };
}
@endphp