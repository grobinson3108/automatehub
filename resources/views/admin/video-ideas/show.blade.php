@extends('layouts.backend')

@section('title', 'Détails Idée Vidéo - Admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="{{ getPlatformIcon($videoIdea->platform) }} mr-2"></i>
                        {{ $videoIdea->title }}
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('admin.video-ideas.edit', $videoIdea) }}" class="btn btn-warning">
                            <i class="fas fa-edit mr-1"></i>
                            Modifier
                        </a>
                        <a href="{{ route('admin.video-ideas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>{{ $videoIdea->title }}</h5>
                            <p class="text-muted">{{ $videoIdea->description }}</p>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-6">
                                    <div class="card bg-info text-white text-center">
                                        <div class="card-body py-2">
                                            <h4>{{ $videoIdea->viral_potential }}/10</h4>
                                            <small>Potentiel Viral</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-success text-white text-center">
                                        <div class="card-body py-2">
                                            <h4>{{ number_format($videoIdea->estimated_views ?? 0) }}</h4>
                                            <small>Vues Estimées</small>
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

    <div class="row">
        <!-- Détails de l'idée -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle mr-2"></i>
                        Détails de l'Idée Vidéo
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Plateforme:</strong></td>
                                    <td>
                                        <i class="{{ getPlatformIcon($videoIdea->platform) }} mr-2"></i>
                                        {{ ucfirst($videoIdea->platform) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Workflow:</strong></td>
                                    <td>
                                        <a href="{{ route('admin.video-content.show', $videoIdea->videoContentPlan) }}">
                                            {{ $videoIdea->videoContentPlan->workflow_name }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Index Vidéo:</strong></td>
                                    <td>{{ $videoIdea->video_index }}</td>
                                </tr>
                                @if($videoIdea->duration)
                                <tr>
                                    <td><strong>Durée:</strong></td>
                                    <td>{{ $videoIdea->duration }}</td>
                                </tr>
                                @endif
                                @if($videoIdea->difficulty)
                                <tr>
                                    <td><strong>Difficulté:</strong></td>
                                    <td>{{ $videoIdea->difficulty }}</td>
                                </tr>
                                @endif
                                @if($videoIdea->video_type)
                                <tr>
                                    <td><strong>Type de Vidéo:</strong></td>
                                    <td>{{ $videoIdea->video_type }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($videoIdea->target_audience)
                            <div class="mb-3">
                                <strong>Audience Cible:</strong>
                                <p class="text-muted mb-0">{{ $videoIdea->target_audience }}</p>
                            </div>
                            @endif

                            @if($videoIdea->hashtags)
                            <div class="mb-3">
                                <strong>Hashtags:</strong>
                                <div class="mt-1">
                                    @foreach($videoIdea->hashtags as $tag)
                                        <span class="badge badge-primary mr-1">#{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if($videoIdea->music)
                            <div class="mb-3">
                                <strong>Musique:</strong>
                                <p class="text-muted mb-0">{{ $videoIdea->music }}</p>
                            </div>
                            @endif

                            @if($videoIdea->transitions)
                            <div class="mb-3">
                                <strong>Transitions:</strong>
                                <p class="text-muted mb-0">{{ $videoIdea->transitions }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($videoIdea->hook)
                    <div class="mt-4 p-3 bg-warning bg-opacity-10 border border-warning rounded">
                        <h6 class="text-warning">
                            <i class="fas fa-hook mr-2"></i>
                            Accroche
                        </h6>
                        <p class="mb-0">{{ $videoIdea->hook }}</p>
                    </div>
                    @endif

                    @if($videoIdea->thumbnail_concept)
                    <div class="mt-4 p-3 bg-info bg-opacity-10 border border-info rounded">
                        <h6 class="text-info">
                            <i class="fas fa-image mr-2"></i>
                            Concept de Thumbnail
                        </h6>
                        <p class="mb-0">{{ $videoIdea->thumbnail_concept }}</p>
                    </div>
                    @endif

                    @if($videoIdea->call_to_action)
                    <div class="mt-4 p-3 bg-success bg-opacity-10 border border-success rounded">
                        <h6 class="text-success">
                            <i class="fas fa-bullhorn mr-2"></i>
                            Call-to-Action
                        </h6>
                        <p class="mb-0">{{ $videoIdea->call_to_action }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Publications liées -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar mr-2"></i>
                        Publications Planifiées
                    </h5>
                </div>
                <div class="card-body">
                    @if($videoIdea->publications->count() > 0)
                        @foreach($videoIdea->publications as $publication)
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <span class="badge {{ getStatusBadge($publication->status) }}">
                                            {{ getStatusText($publication->status) }}
                                        </span>
                                    </h6>
                                    <div class="small text-muted">
                                        @if($publication->filming_date)
                                            <strong>Tournage:</strong> {{ $publication->filming_date->format('d/m/Y') }}<br>
                                        @endif
                                        @if($publication->editing_date)
                                            <strong>Montage:</strong> {{ $publication->editing_date->format('d/m/Y') }}<br>
                                        @endif
                                        <strong>Publication:</strong> {{ $publication->scheduled_date->format('d/m/Y') }} à {{ $publication->scheduled_time }}
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <a href="{{ route('admin.publication-calendar.show', $publication) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-calendar-plus fa-3x mb-3"></i>
                            <p>Aucune publication planifiée</p>
                            <button class="btn btn-primary btn-sm" onclick="generateScheduleForIdea()">
                                <i class="fas fa-plus mr-1"></i>
                                Créer Planning
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-info">{{ $videoIdea->viral_potential }}</h4>
                            <small class="text-muted">Potentiel Viral</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ number_format($videoIdea->estimated_views ?? 0) }}</h4>
                            <small class="text-muted">Vues Estimées</small>
                        </div>
                    </div>

                    @if($videoIdea->publications->where('status', 'published')->count() > 0)
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ number_format($videoIdea->publications->sum('actual_views')) }}</h4>
                            <small class="text-muted">Vues Réelles</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ number_format($videoIdea->publications->avg('actual_engagement_rate'), 1) }}%</h4>
                            <small class="text-muted">Engagement Moyen</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateScheduleForIdea() {
    if (!confirm('Générer un planning pour cette idée vidéo ?')) return;

    $.ajax({
        url: '{{ route("admin.video-ideas.generate-schedule", $videoIdea->videoContentPlan) }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                location.reload();
            } else {
                toastr.error('Erreur lors de la génération');
            }
        },
        error: function() {
            toastr.error('Erreur lors de la génération du planning');
        }
    });
}
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

function getStatusBadge($status) {
    return match($status) {
        'planned' => 'badge-secondary',
        'filmed' => 'badge-info',
        'edited' => 'badge-warning',
        'published' => 'badge-success',
        'cancelled' => 'badge-danger',
        default => 'badge-secondary'
    };
}

function getStatusText($status) {
    return match($status) {
        'planned' => 'Planifié',
        'filmed' => 'Filmé',
        'edited' => 'Monté',
        'published' => 'Publié',
        'cancelled' => 'Annulé',
        default => 'Planifié'
    };
}
@endphp