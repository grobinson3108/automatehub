@extends('layouts.frontend')

@section('content')
<!-- Hero Section -->
<div class="bg-primary">
    <div class="bg-black-25">
        <div class="content content-full py-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('workflows.index') }}" class="btn btn-alt-primary me-3">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <div class="flex-grow-1">
                    <h1 class="h3 text-white mb-1">{{ $workflow->name }}</h1>
                    <p class="text-white-75 mb-0">
                        @if($workflow->is_premium)
                            <span class="badge bg-warning me-2">
                                <i class="fa fa-star"></i> Premium
                            </span>
                        @else
                            <span class="badge bg-success me-2">
                                <i class="fa fa-check"></i> Gratuit
                            </span>
                        @endif
                        Catégorie : {{ ucfirst($workflow->category ?? 'general') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content -->
<div class="content content-boxed">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Description -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Description</h3>
                </div>
                <div class="block-content">
                    <p>{{ $workflow->description ?? 'Ce workflow n8n permet d\'automatiser vos processus de manière efficace.' }}</p>
                    
                    @if($workflow->tags && count($workflow->tags) > 0)
                        <div class="mb-3">
                            <strong>Tags :</strong>
                            @foreach($workflow->tags as $tag)
                                <span class="badge bg-body-light text-body me-1">#{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Workflow Details -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Détails techniques</h3>
                </div>
                <div class="block-content">
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong>Nombre de nodes :</strong> {{ $workflow->node_count ?? 0 }}</p>
                            <p><strong>Créé le :</strong> {{ $workflow->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong>Téléchargements :</strong> {{ $workflow->download_count ?? 0 }}</p>
                            <p><strong>Dernière mise à jour :</strong> {{ $workflow->updated_at->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    @if(isset($workflow->json_data['nodes']) && count($workflow->json_data['nodes']) > 0)
                        <h4 class="h5 mt-4">Nodes utilisés :</h4>
                        <div class="row">
                            @php
                                $nodeTypes = collect($workflow->json_data['nodes'])
                                    ->pluck('type')
                                    ->unique()
                                    ->map(function($type) {
                                        return str_replace('n8n-nodes-base.', '', $type);
                                    })
                                    ->sort();
                            @endphp
                            @foreach($nodeTypes as $nodeType)
                                <div class="col-md-6 mb-2">
                                    <i class="fa fa-cube text-primary me-2"></i>{{ ucfirst($nodeType) }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Preview Image (if available) -->
            @if($workflow->preview_image)
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Aperçu</h3>
                    </div>
                    <div class="block-content">
                        <img src="{{ $workflow->preview_image }}" alt="Aperçu du workflow" class="img-fluid rounded">
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Download Card -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Téléchargement</h3>
                </div>
                <div class="block-content">
                    @auth
                        @if(!$workflow->is_premium || auth()->user()->subscription_type !== 'free')
                            <div class="d-grid gap-2">
                                <a href="{{ route('workflows.download', $workflow) }}" 
                                   class="btn btn-primary"
                                   onclick="trackDownload({{ $workflow->id }})">
                                    <i class="fa fa-download me-2"></i>
                                    Télécharger le JSON
                                </a>
                                
                                <button type="button" 
                                        class="btn btn-alt-primary"
                                        onclick="getImportUrl({{ $workflow->id }})">
                                    <i class="fa fa-external-link-alt me-2"></i>
                                    Importer dans n8n
                                </button>
                            </div>
                            
                            <div class="mt-3 text-center text-muted">
                                <small>
                                    <i class="fa fa-info-circle"></i> 
                                    Compatible avec n8n v0.210.0+
                                </small>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fa fa-lock fa-3x text-warning mb-3"></i>
                                <h4>Workflow Premium</h4>
                                <p class="text-muted">
                                    Ce workflow nécessite un abonnement premium pour être téléchargé.
                                </p>
                                <a href="{{ route('pricing') }}" class="btn btn-warning">
                                    <i class="fa fa-star me-2"></i>
                                    Voir les offres
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-user-lock fa-3x text-muted mb-3"></i>
                            <h4>Connexion requise</h4>
                            <p class="text-muted">
                                Connectez-vous pour télécharger ce workflow.
                            </p>
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="fa fa-sign-in-alt me-2"></i>
                                Se connecter
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Related Workflows -->
            @if($relatedWorkflows ?? false)
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Workflows similaires</h3>
                    </div>
                    <div class="block-content">
                        <ul class="list-unstyled">
                            @foreach($relatedWorkflows as $related)
                                <li class="mb-2">
                                    <a href="{{ route('workflows.show', $related) }}">
                                        {{ Str::limit($related->name, 30) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Importer dans n8n</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Cliquez sur le bouton ci-dessous pour ouvrir n8n avec ce workflow :</p>
                <div class="d-grid">
                    <a href="#" id="n8nImportLink" target="_blank" class="btn btn-primary">
                        <i class="fa fa-external-link-alt me-2"></i>
                        Ouvrir dans n8n
                    </a>
                </div>
                <hr>
                <p class="text-muted mb-0">
                    <small>Assurez-vous que votre instance n8n est accessible et que vous êtes connecté.</small>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function trackDownload(workflowId) {
    // Track download event
    console.log('Download workflow:', workflowId);
}

function getImportUrl(workflowId) {
    fetch(`/workflows/${workflowId}/import-url`)
        .then(response => response.json())
        .then(data => {
            if (data.n8n_url) {
                document.getElementById('n8nImportLink').href = data.n8n_url;
                new bootstrap.Modal(document.getElementById('importModal')).show();
            }
        })
        .catch(error => {
            alert('Erreur lors de la génération du lien d\'import');
        });
}
</script>
@endpush