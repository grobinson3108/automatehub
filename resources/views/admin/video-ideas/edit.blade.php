@extends('layouts.backend')

@section('title', 'Modifier Idée Vidéo - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-edit mr-2"></i>
                        Modifier l'Idée Vidéo
                    </h4>
                    <a href="{{ route('admin.video-ideas.show', $videoIdea) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Retour
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.video-ideas.update', $videoIdea) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Informations principales -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Informations Principales</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="title">Titre de la Vidéo *</label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                                   id="title" name="title" value="{{ old('title', $videoIdea->title) }}" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Description *</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror"
                                                      id="description" name="description" rows="4" required>{{ old('description', $videoIdea->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="hook">Accroche</label>
                                            <textarea class="form-control @error('hook') is-invalid @enderror"
                                                      id="hook" name="hook" rows="2"
                                                      placeholder="L'accroche qui va captiver l'audience dès les premières secondes">{{ old('hook', $videoIdea->hook) }}</textarea>
                                            @error('hook')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="hashtags">Hashtags</label>
                                            <input type="text" class="form-control @error('hashtags') is-invalid @enderror"
                                                   id="hashtags" name="hashtags"
                                                   value="{{ old('hashtags', is_array($videoIdea->hashtags) ? implode(' ', $videoIdea->hashtags) : $videoIdea->hashtags) }}"
                                                   placeholder="hashtag1 hashtag2 hashtag3">
                                            <small class="form-text text-muted">Séparez les hashtags par des espaces</small>
                                            @error('hashtags')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="thumbnail_concept">Concept de Thumbnail</label>
                                            <textarea class="form-control @error('thumbnail_concept') is-invalid @enderror"
                                                      id="thumbnail_concept" name="thumbnail_concept" rows="3"
                                                      placeholder="Décrivez le concept visuel pour la miniature">{{ old('thumbnail_concept', $videoIdea->thumbnail_concept) }}</textarea>
                                            @error('thumbnail_concept')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="call_to_action">Call-to-Action</label>
                                            <textarea class="form-control @error('call_to_action') is-invalid @enderror"
                                                      id="call_to_action" name="call_to_action" rows="2"
                                                      placeholder="L'action que vous souhaitez que les viewers effectuent">{{ old('call_to_action', $videoIdea->call_to_action) }}</textarea>
                                            @error('call_to_action')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Paramètres et métadonnées -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Paramètres</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="viral_potential">Potentiel Viral *</label>
                                            <select class="form-control @error('viral_potential') is-invalid @enderror"
                                                    id="viral_potential" name="viral_potential" required>
                                                @for($i = 1; $i <= 10; $i++)
                                                    <option value="{{ $i }}" {{ old('viral_potential', $videoIdea->viral_potential) == $i ? 'selected' : '' }}>
                                                        {{ $i }}/10 {{ $i <= 3 ? '(Faible)' : ($i <= 6 ? '(Moyen)' : ($i <= 8 ? '(Élevé)' : '(Viral)')) }}
                                                    </option>
                                                @endfor
                                            </select>
                                            @error('viral_potential')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="estimated_views">Vues Estimées</label>
                                            <input type="number" class="form-control @error('estimated_views') is-invalid @enderror"
                                                   id="estimated_views" name="estimated_views" min="0"
                                                   value="{{ old('estimated_views', $videoIdea->estimated_views) }}">
                                            @error('estimated_views')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="duration">Durée</label>
                                            <input type="text" class="form-control @error('duration') is-invalid @enderror"
                                                   id="duration" name="duration"
                                                   value="{{ old('duration', $videoIdea->duration) }}"
                                                   placeholder="Ex: 1min30, 45s, 3min">
                                            @error('duration')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="difficulty">Difficulté</label>
                                            <select class="form-control @error('difficulty') is-invalid @enderror"
                                                    id="difficulty" name="difficulty">
                                                <option value="">Sélectionner...</option>
                                                <option value="Facile" {{ old('difficulty', $videoIdea->difficulty) == 'Facile' ? 'selected' : '' }}>Facile</option>
                                                <option value="Moyen" {{ old('difficulty', $videoIdea->difficulty) == 'Moyen' ? 'selected' : '' }}>Moyen</option>
                                                <option value="Difficile" {{ old('difficulty', $videoIdea->difficulty) == 'Difficile' ? 'selected' : '' }}>Difficile</option>
                                                <option value="Expert" {{ old('difficulty', $videoIdea->difficulty) == 'Expert' ? 'selected' : '' }}>Expert</option>
                                            </select>
                                            @error('difficulty')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="video_type">Type de Vidéo</label>
                                            <select class="form-control @error('video_type') is-invalid @enderror"
                                                    id="video_type" name="video_type">
                                                <option value="">Sélectionner...</option>
                                                <option value="Tutoriel" {{ old('video_type', $videoIdea->video_type) == 'Tutoriel' ? 'selected' : '' }}>Tutoriel</option>
                                                <option value="Présentation" {{ old('video_type', $videoIdea->video_type) == 'Présentation' ? 'selected' : '' }}>Présentation</option>
                                                <option value="Démonstration" {{ old('video_type', $videoIdea->video_type) == 'Démonstration' ? 'selected' : '' }}>Démonstration</option>
                                                <option value="Storytelling" {{ old('video_type', $videoIdea->video_type) == 'Storytelling' ? 'selected' : '' }}>Storytelling</option>
                                                <option value="Q&A" {{ old('video_type', $videoIdea->video_type) == 'Q&A' ? 'selected' : '' }}>Q&A</option>
                                                <option value="Tendance" {{ old('video_type', $videoIdea->video_type) == 'Tendance' ? 'selected' : '' }}>Tendance</option>
                                            </select>
                                            @error('video_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="target_audience">Audience Cible</label>
                                            <input type="text" class="form-control @error('target_audience') is-invalid @enderror"
                                                   id="target_audience" name="target_audience"
                                                   value="{{ old('target_audience', $videoIdea->target_audience) }}"
                                                   placeholder="Ex: Développeurs débutants, Entrepreneurs">
                                            @error('target_audience')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Éléments techniques -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="mb-0">Éléments Techniques</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="music">Musique</label>
                                            <input type="text" class="form-control @error('music') is-invalid @enderror"
                                                   id="music" name="music"
                                                   value="{{ old('music', $videoIdea->music) }}"
                                                   placeholder="Style musical ou piste spécifique">
                                            @error('music')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="transitions">Transitions</label>
                                            <input type="text" class="form-control @error('transitions') is-invalid @enderror"
                                                   id="transitions" name="transitions"
                                                   value="{{ old('transitions', $videoIdea->transitions) }}"
                                                   placeholder="Type de transitions à utiliser">
                                            @error('transitions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.video-ideas.show', $videoIdea) }}" class="btn btn-secondary">
                                        <i class="fas fa-times mr-1"></i>
                                        Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-1"></i>
                                        Enregistrer les Modifications
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Validation côté client
    $('form').on('submit', function(e) {
        let valid = true;

        // Vérifier que le titre n'est pas vide
        if ($('#title').val().trim() === '') {
            valid = false;
            $('#title').addClass('is-invalid');
        } else {
            $('#title').removeClass('is-invalid');
        }

        // Vérifier que la description n'est pas vide
        if ($('#description').val().trim() === '') {
            valid = false;
            $('#description').addClass('is-invalid');
        } else {
            $('#description').removeClass('is-invalid');
        }

        if (!valid) {
            e.preventDefault();
            toastr.error('Veuillez remplir tous les champs obligatoires');
        }
    });

    // Formatage automatique des hashtags
    $('#hashtags').on('blur', function() {
        let hashtags = $(this).val().split(' ').filter(tag => tag.trim() !== '');
        hashtags = hashtags.map(tag => {
            tag = tag.trim();
            if (tag && !tag.startsWith('#')) {
                return tag;
            }
            return tag.replace('#', '');
        });
        $(this).val(hashtags.join(' '));
    });

    // Preview du potentiel viral
    $('#viral_potential').on('change', function() {
        const value = parseInt($(this).val());
        let color = 'secondary';
        if (value <= 3) color = 'warning';
        else if (value <= 6) color = 'info';
        else if (value <= 8) color = 'success';
        else color = 'danger';

        $(this).removeClass('btn-secondary btn-warning btn-info btn-success btn-danger')
               .addClass('btn-' + color);
    });
});
</script>
@endpush