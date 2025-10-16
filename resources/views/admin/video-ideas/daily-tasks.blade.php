@extends('layouts.backend')

@section('title', 'Tâches Quotidiennes - Admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-tasks mr-2"></i>
                        Tâches Quotidiennes - {{ \Carbon\Carbon::parse($date)->format('l j F Y') }}
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('admin.video-ideas.daily-tasks', \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d')) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-chevron-left mr-1"></i>
                            Jour Précédent
                        </a>
                        <a href="{{ route('admin.video-ideas.daily-tasks', \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d')) }}" class="btn btn-outline-secondary">
                            Jour Suivant
                            <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                        <a href="{{ route('admin.video-ideas.index') }}" class="btn btn-info">
                            <i class="fas fa-video mr-1"></i>
                            Retour aux Idées
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ count($tasks['filming']) }}</h3>
                                    <p class="mb-0">Tournages Prévus</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ count($tasks['editing']) }}</h3>
                                    <p class="mb-0">Montages à Faire</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ count($tasks['publishing']) }}</h3>
                                    <p class="mb-0">Publications Prévues</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tournages du jour -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-video mr-2"></i>
                        Tournages Prévus
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($tasks['filming']) > 0)
                        @foreach($tasks['filming'] as $publication)
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <i class="{{ getPlatformIcon($publication->platform) }} mr-2"></i>
                                        {{ $publication->title }}
                                    </h6>
                                    <p class="text-muted mb-2 small">
                                        {{ Str::limit($publication->description, 80) }}
                                    </p>
                                    <div class="small">
                                        <strong>Workflow:</strong> {{ $publication->videoContentPlan->workflow_name }}<br>
                                        <strong>Heure prévue:</strong> {{ $publication->scheduled_time }}<br>
                                        @if($publication->videoIdea->hooks)
                                            <strong>Accroche:</strong> {{ $publication->videoIdea->hooks }}
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <button class="btn btn-sm btn-warning" onclick="markAsFilmed({{ $publication->id }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-video fa-3x mb-3"></i>
                            <p>Aucun tournage prévu aujourd'hui</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Montages du jour -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cut mr-2"></i>
                        Montages à Faire
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($tasks['editing']) > 0)
                        @foreach($tasks['editing'] as $publication)
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <i class="{{ getPlatformIcon($publication->platform) }} mr-2"></i>
                                        {{ $publication->title }}
                                    </h6>
                                    <p class="text-muted mb-2 small">
                                        {{ Str::limit($publication->description, 80) }}
                                    </p>
                                    <div class="small">
                                        <strong>Workflow:</strong> {{ $publication->videoContentPlan->workflow_name }}<br>
                                        <strong>Tournage:</strong> {{ $publication->filming_date->format('d/m/Y') }}<br>
                                        <strong>Publication prévue:</strong> {{ $publication->scheduled_date->format('d/m/Y') }} à {{ $publication->scheduled_time }}
                                    </div>
                                    @if($publication->videoIdea->thumbnail_concept)
                                        <div class="mt-2 p-2 bg-light rounded">
                                            <small><strong>Concept thumbnail:</strong> {{ $publication->videoIdea->thumbnail_concept }}</small>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-2">
                                    <button class="btn btn-sm btn-info" onclick="markAsEdited({{ $publication->id }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-cut fa-3x mb-3"></i>
                            <p>Aucun montage prévu aujourd'hui</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Publications du jour -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-upload mr-2"></i>
                        Publications Prévues
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($tasks['publishing']) > 0)
                        @php
                            $sortedPublications = collect($tasks['publishing'])->sortBy('scheduled_time');
                        @endphp
                        @foreach($sortedPublications as $publication)
                        <div class="mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge badge-success mr-2">{{ $publication->scheduled_time }}</span>
                                        <i class="{{ getPlatformIcon($publication->platform) }} mr-2"></i>
                                        <h6 class="mb-0">{{ $publication->title }}</h6>
                                    </div>
                                    <p class="text-muted mb-2 small">
                                        {{ Str::limit($publication->description, 80) }}
                                    </p>
                                    <div class="small">
                                        <strong>Workflow:</strong> {{ $publication->videoContentPlan->workflow_name }}<br>
                                        @if($publication->hashtags)
                                            <strong>Hashtags:</strong>
                                            @foreach($publication->hashtags as $tag)
                                                <span class="badge badge-secondary badge-sm mr-1">#{{ $tag }}</span>
                                            @endforeach
                                            <br>
                                        @endif
                                        @if($publication->call_to_action)
                                            <strong>CTA:</strong> {{ $publication->call_to_action }}
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <div class="btn-group-vertical">
                                        <button class="btn btn-sm btn-success" onclick="markAsPublished({{ $publication->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <a href="{{ route('admin.publication-calendar.show', $publication) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-upload fa-3x mb-3"></i>
                            <p>Aucune publication prévue aujourd'hui</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(count($tasks['filming']) === 0 && count($tasks['editing']) === 0 && count($tasks['publishing']) === 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-check fa-4x text-success mb-3"></i>
                    <h5 class="text-muted">Aucune tâche prévue aujourd'hui</h5>
                    <p class="text-muted">Profitez de cette journée libre ou planifiez de nouvelles créations !</p>
                    <a href="{{ route('admin.video-ideas.index') }}" class="btn btn-primary">
                        <i class="fas fa-video mr-2"></i>
                        Gérer les Idées Vidéos
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function markAsFilmed(publicationId) {
    if (!confirm('Marquer cette vidéo comme filmée ?')) return;

    $.ajax({
        url: `{{ route("admin.publication-calendar.update-status", ":id") }}`.replace(':id', publicationId),
        method: 'PATCH',
        data: {
            status: 'filmed',
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            toastr.success('Vidéo marquée comme filmée');
            location.reload();
        },
        error: function() {
            toastr.error('Erreur lors de la mise à jour');
        }
    });
}

function markAsEdited(publicationId) {
    if (!confirm('Marquer cette vidéo comme montée ?')) return;

    $.ajax({
        url: `{{ route("admin.publication-calendar.update-status", ":id") }}`.replace(':id', publicationId),
        method: 'PATCH',
        data: {
            status: 'edited',
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            toastr.success('Vidéo marquée comme montée');
            location.reload();
        },
        error: function() {
            toastr.error('Erreur lors de la mise à jour');
        }
    });
}

function markAsPublished(publicationId) {
    const url = prompt('URL de la publication (optionnel):');

    $.ajax({
        url: `{{ route("admin.publication-calendar.mark-published", ":id") }}`.replace(':id', publicationId),
        method: 'POST',
        data: {
            published_url: url,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            toastr.success('Publication marquée comme publiée');
            location.reload();
        },
        error: function() {
            toastr.error('Erreur lors de la mise à jour');
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
@endphp