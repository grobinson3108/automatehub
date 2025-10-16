@extends('layouts.backend')

@section('title', 'Modifier Publication')

@section('content')
<div class="bg-body-light">
    <div class="content content-full">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
            <div class="flex-grow-1">
                <h1 class="h3 fw-bold mb-1">
                    ✏️ Modifier Publication
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
                    <li class="breadcrumb-item">
                        <a class="link-fx" href="{{ route('admin.publication-calendar.show', $publication) }}">Publication</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">Modifier</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="content">
    <form action="{{ route('admin.publication-calendar.update', $publication) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <!-- Contenu principal -->
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            <i class="fa fa-fw fa-edit me-1"></i>
                            Contenu de la Publication
                        </h3>
                    </div>
                    <div class="block-content">
                        <!-- Titre -->
                        <div class="mb-4">
                            <label class="form-label fw-bold" for="title">Titre *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $publication->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="form-label fw-bold" for="description">Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="6" required>{{ old('description', $publication->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optimisé pour {{ ucfirst($publication->platform) }}</div>
                        </div>

                        <!-- Hashtags -->
                        <div class="mb-4">
                            <label class="form-label fw-bold" for="hashtags">Hashtags</label>
                            <input type="text" class="form-control @error('hashtags') is-invalid @enderror"
                                   id="hashtags" name="hashtags"
                                   value="{{ old('hashtags', is_array($publication->hashtags) ? implode(' ', $publication->hashtags) : $publication->hashtags) }}">
                            @error('hashtags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Séparez les hashtags par des espaces (#automation #n8n #workflow)</div>
                        </div>

                        <!-- Call to Action -->
                        <div class="mb-4">
                            <label class="form-label fw-bold" for="call_to_action">Call to Action</label>
                            <input type="text" class="form-control @error('call_to_action') is-invalid @enderror"
                                   id="call_to_action" name="call_to_action"
                                   value="{{ old('call_to_action', $publication->call_to_action) }}">
                            @error('call_to_action')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Caption/Légende -->
                        <div class="mb-4">
                            <label class="form-label fw-bold" for="caption">Légende/Caption</label>
                            <textarea class="form-control @error('caption') is-invalid @enderror"
                                      id="caption" name="caption" rows="3">{{ old('caption', $publication->caption) }}</textarea>
                            @error('caption')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label class="form-label fw-bold" for="notes">Notes internes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3">{{ old('notes', $publication->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Paramètres -->
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            <i class="fa fa-fw fa-cogs me-1"></i>
                            Paramètres
                        </h3>
                    </div>
                    <div class="block-content">
                        <!-- Plateforme (read-only) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Plateforme</label>
                            <div class="p-2 bg-body-light rounded">
                                <span class="badge bg-{{ $publication->platform_color }} fs-6">
                                    <i class="fa fa-{{ $publication->platform_icon }} me-1"></i>
                                    {{ ucfirst($publication->platform) }}
                                </span>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="scheduled_date">Date de publication *</label>
                            <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror"
                                   id="scheduled_date" name="scheduled_date"
                                   value="{{ old('scheduled_date', $publication->scheduled_date->format('Y-m-d')) }}" required>
                            @error('scheduled_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Heure -->
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="scheduled_time">Heure de publication</label>
                            <input type="time" class="form-control @error('scheduled_time') is-invalid @enderror"
                                   id="scheduled_time" name="scheduled_time"
                                   value="{{ old('scheduled_time', $publication->scheduled_time ? \Carbon\Carbon::parse($publication->scheduled_time)->format('H:i') : '') }}">
                            @error('scheduled_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Audience cible -->
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="target_audience">Audience cible</label>
                            <input type="text" class="form-control @error('target_audience') is-invalid @enderror"
                                   id="target_audience" name="target_audience"
                                   value="{{ old('target_audience', $publication->target_audience) }}">
                            @error('target_audience')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Concept thumbnail -->
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="thumbnail_concept">Concept thumbnail</label>
                            <textarea class="form-control @error('thumbnail_concept') is-invalid @enderror"
                                      id="thumbnail_concept" name="thumbnail_concept" rows="3">{{ old('thumbnail_concept', $publication->thumbnail_concept) }}</textarea>
                            @error('thumbnail_concept')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informations du workflow -->
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            <i class="fa fa-fw fa-video me-1"></i>
                            Workflow Source
                        </h3>
                    </div>
                    <div class="block-content">
                        <div class="mb-2">
                            <strong>{{ $publication->videoContentPlan->workflow_name }}</strong>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">
                                Potentiel viral:
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa fa-star {{ $i <= $publication->videoContentPlan->viral_potential ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </small>
                        </div>
                        <div>
                            <a href="{{ route('admin.video-content.show', $publication->videoContentPlan) }}"
                               class="btn btn-alt-secondary btn-sm">
                                <i class="fa fa-eye me-1"></i>
                                Voir le plan vidéo
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="block block-rounded">
                    <div class="block-content">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save me-1"></i>
                                Enregistrer les modifications
                            </button>

                            <a href="{{ route('admin.publication-calendar.show', $publication) }}"
                               class="btn btn-alt-secondary">
                                <i class="fa fa-arrow-left me-1"></i>
                                Retour au détail
                            </a>

                            <a href="{{ route('admin.publication-calendar.index') }}"
                               class="btn btn-alt-secondary">
                                <i class="fa fa-calendar me-1"></i>
                                Retour au calendrier
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('js')
<script>
// Auto-resize textareas
document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});

// Preview hashtags
document.getElementById('hashtags').addEventListener('input', function() {
    const value = this.value;
    const preview = document.getElementById('hashtags-preview');

    if (preview) {
        if (value.trim()) {
            const tags = value.split(' ').filter(tag => tag.trim());
            preview.innerHTML = tags.map(tag =>
                `<span class="badge bg-primary me-1">${tag.startsWith('#') ? tag : '#' + tag}</span>`
            ).join('');
        } else {
            preview.innerHTML = '<span class="text-muted">Aucun hashtag</span>';
        }
    }
});
</script>
@endsection