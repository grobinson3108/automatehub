@extends('layouts.backend')

@section('title', 'Détail Publication')

@section('content')
<div class="bg-body-light">
    <div class="content content-full">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
            <div class="flex-grow-1">
                <h1 class="h3 fw-bold mb-1">
                    📱 {{ ucfirst($publication->platform) }} • {{ $publication->scheduled_date->format('d/m/Y') }}
                </h1>
                <h2 class="fs-base lh-base fw-medium text-muted mb-0">
                    {{ $publication->title }}
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
                    <li class="breadcrumb-item" aria-current="page">Publication</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="content">
    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-fw fa-info-circle me-1"></i>
                        Informations Publication
                    </h3>
                    <div class="block-options">
                        <a href="{{ route('admin.publication-calendar.edit', $publication) }}" class="btn btn-sm btn-alt-secondary">
                            <i class="fa fa-fw fa-pencil-alt"></i> Modifier
                        </a>
                    </div>
                </div>
                <div class="block-content">
                    <!-- Titre et description -->
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Titre</label>
                                <div class="p-3 bg-body-light rounded">
                                    {{ $publication->title }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Description</label>
                                <div class="p-3 bg-body-light rounded" style="white-space: pre-wrap;">{{ $publication->description }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Hashtags -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Hashtags</label>
                        <div class="p-3 bg-body-light rounded">
                            @if($publication->hashtags)
                                @if(is_array($publication->hashtags))
                                    @foreach($publication->hashtags as $tag)
                                        @if(trim($tag))
                                            <span class="badge bg-primary me-1 mb-1">{{ trim($tag) }}</span>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach(explode(' ', $publication->hashtags) as $tag)
                                        @if(trim($tag))
                                            <span class="badge bg-primary me-1 mb-1">{{ trim($tag) }}</span>
                                        @endif
                                    @endforeach
                                @endif
                            @else
                                <span class="text-muted">Aucun hashtag</span>
                            @endif
                        </div>
                    </div>

                    <!-- Call to Action -->
                    @if($publication->call_to_action)
                        <div class="mb-4">
                            <label class="form-label fw-bold">Call to Action</label>
                            <div class="p-3 bg-body-light rounded">
                                {{ $publication->call_to_action }}
                            </div>
                        </div>
                    @endif

                    <!-- URL de publication -->
                    @if($publication->published_url)
                        <div class="mb-4">
                            <label class="form-label fw-bold">URL de publication</label>
                            <div class="p-3 bg-body-light rounded">
                                <a href="{{ $publication->published_url }}" target="_blank" class="link-fx">
                                    {{ $publication->published_url }}
                                    <i class="fa fa-external-link-alt ms-1"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Métriques -->
            @if($publication->status === 'published' && ($publication->actual_views || $publication->actual_likes || $publication->actual_comments || $publication->actual_shares))
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            <i class="fa fa-fw fa-chart-bar me-1"></i>
                            Métriques de Performance
                        </h3>
                        <div class="block-options">
                            <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-toggle="modal" data-bs-target="#modal-update-metrics">
                                <i class="fa fa-fw fa-edit"></i> Mettre à jour
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="py-3">
                                    <div class="fs-2 fw-bold text-primary">{{ number_format($publication->actual_views ?? 0) }}</div>
                                    <div class="fs-sm fw-medium text-muted">Vues</div>
                                    @if($publication->estimated_views)
                                        <div class="fs-xs text-muted">
                                            Estimé: {{ number_format($publication->estimated_views) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="py-3">
                                    <div class="fs-2 fw-bold text-success">{{ number_format($publication->actual_likes ?? 0) }}</div>
                                    <div class="fs-sm fw-medium text-muted">Likes</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="py-3">
                                    <div class="fs-2 fw-bold text-info">{{ number_format($publication->actual_comments ?? 0) }}</div>
                                    <div class="fs-sm fw-medium text-muted">Commentaires</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="py-3">
                                    <div class="fs-2 fw-bold text-warning">{{ number_format($publication->actual_shares ?? 0) }}</div>
                                    <div class="fs-sm fw-medium text-muted">Partages</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Détails planification -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-fw fa-calendar me-1"></i>
                        Planification
                    </h3>
                </div>
                <div class="block-content">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Plateforme</label>
                        <div>
                            <span class="badge bg-{{ $publication->platform_color }} fs-6">
                                <i class="fa fa-{{ $publication->platform_icon }} me-1"></i>
                                {{ ucfirst($publication->platform) }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Date et heure</label>
                        <div>
                            <i class="fa fa-calendar me-2 text-muted"></i>
                            {{ $publication->scheduled_date->translatedFormat('l j F Y') }}
                        </div>
                        <div>
                            <i class="fa fa-clock me-2 text-muted"></i>
                            {{ $publication->scheduled_time_formatted }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Statut</label>
                        <div>
                            <span class="badge bg-{{ $publication->status_color }} fs-6">
                                {{ $publication->status_text }}
                            </span>
                        </div>
                    </div>
                    @if($publication->published_at)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Publié le</label>
                            <div>
                                <i class="fa fa-check-circle me-2 text-success"></i>
                                {{ $publication->published_at->translatedFormat('j F Y à H:i') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Workflow source -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-fw fa-video me-1"></i>
                        Workflow Source
                    </h3>
                </div>
                <div class="block-content">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom du workflow</label>
                        <div>{{ $publication->videoContentPlan->workflow_name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Potentiel viral</label>
                        <div>
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa fa-star {{ $i <= $publication->videoContentPlan->viral_potential ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                            <span class="ms-2">{{ $publication->videoContentPlan->viral_potential }}/5</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Priorité</label>
                        <div>
                            <span class="badge bg-{{ $publication->videoContentPlan->priority <= 20 ? 'danger' : ($publication->videoContentPlan->priority <= 50 ? 'warning' : 'success') }}">
                                {{ $publication->videoContentPlan->priority }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.video-content.show', $publication->videoContentPlan) }}" class="btn btn-alt-secondary btn-sm">
                            <i class="fa fa-eye me-1"></i>
                            Voir le plan vidéo
                        </a>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-fw fa-cogs me-1"></i>
                        Actions
                    </h3>
                </div>
                <div class="block-content">
                    <div class="d-grid gap-2">
                        @if($publication->status === 'scheduled')
                            <button type="button" class="btn btn-warning" onclick="updateStatus('filming')">
                                <i class="fa fa-film me-1"></i>
                                Marquer en tournage
                            </button>
                            <button type="button" class="btn btn-info" onclick="updateStatus('editing')">
                                <i class="fa fa-cut me-1"></i>
                                Marquer en montage
                            </button>
                            <button type="button" class="btn btn-success" onclick="markAsPublished()">
                                <i class="fa fa-check me-1"></i>
                                Marquer comme publié
                            </button>
                        @elseif($publication->status === 'filming')
                            <button type="button" class="btn btn-info" onclick="updateStatus('editing')">
                                <i class="fa fa-cut me-1"></i>
                                Marquer en montage
                            </button>
                            <button type="button" class="btn btn-success" onclick="markAsPublished()">
                                <i class="fa fa-check me-1"></i>
                                Marquer comme publié
                            </button>
                        @elseif($publication->status === 'editing')
                            <button type="button" class="btn btn-success" onclick="markAsPublished()">
                                <i class="fa fa-check me-1"></i>
                                Marquer comme publié
                            </button>
                        @endif

                        <hr>

                        <button type="button" class="btn btn-alt-secondary" onclick="duplicatePublication()">
                            <i class="fa fa-copy me-1"></i>
                            Dupliquer
                        </button>

                        <a href="{{ route('admin.publication-calendar.edit', $publication) }}" class="btn btn-alt-primary">
                            <i class="fa fa-edit me-1"></i>
                            Modifier
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Mise à jour métriques -->
<div class="modal fade" id="modal-update-metrics" tabindex="-1" aria-labelledby="modal-update-metrics-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-update-metrics-label">
                    <i class="fa fa-chart-bar me-2"></i>
                    Mettre à jour les métriques
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-update-metrics">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Vues</label>
                                <input type="number" class="form-control" name="views" value="{{ $publication->actual_views ?? 0 }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Likes</label>
                                <input type="number" class="form-control" name="likes" value="{{ $publication->actual_likes ?? 0 }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Commentaires</label>
                                <input type="number" class="form-control" name="comments" value="{{ $publication->actual_comments ?? 0 }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Partages</label>
                                <input type="number" class="form-control" name="shares" value="{{ $publication->actual_shares ?? 0 }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
function updateStatus(status) {
    fetch(`{{ route('admin.publication-calendar.update-status', $publication) }}`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAsPublished() {
    const url = prompt('URL de la publication (optionnel):');

    fetch(`{{ route('admin.publication-calendar.mark-published', $publication) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ published_url: url })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function duplicatePublication() {
    if (confirm('Dupliquer cette publication ?')) {
        fetch(`{{ route('admin.publication-calendar.duplicate', $publication) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `/admin/publication-calendar/${data.id}`;
            }
        });
    }
}

// Formulaire mise à jour métriques
document.getElementById('form-update-metrics').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(`{{ route('admin.publication-calendar.update-metrics', $publication) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            views: formData.get('views'),
            likes: formData.get('likes'),
            comments: formData.get('comments'),
            shares: formData.get('shares')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
});
</script>
@endsection