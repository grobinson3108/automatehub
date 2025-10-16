@extends('layouts.frontend')

@section('content')
<!-- Hero Section -->
<div class="bg-warning">
    <div class="bg-black-10">
        <div class="content content-full text-center py-6">
            <i class="fa fa-lock fa-4x text-white mb-3"></i>
            <h1 class="h2 text-white mb-2">Workflow Premium</h1>
            <p class="text-white mb-0">
                Ce contenu nécessite un abonnement premium
            </p>
        </div>
    </div>
</div>

<!-- Content -->
<div class="content content-boxed">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Workflow Info -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">{{ $workflow->name }}</h3>
                    <div class="block-options">
                        <span class="badge bg-warning">
                            <i class="fa fa-star"></i> Premium
                        </span>
                    </div>
                </div>
                <div class="block-content">
                    <p class="lead">{{ $workflow->description ?? 'Ce workflow avancé fait partie de notre collection premium.' }}</p>
                    
                    <div class="row text-center mb-4">
                        <div class="col-6">
                            <div class="py-3">
                                <i class="fa fa-cube fa-2x text-primary mb-2"></i>
                                <p class="mb-0">
                                    <strong>{{ $workflow->node_count ?? 0 }}</strong><br>
                                    <small class="text-muted">Nodes</small>
                                </p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="py-3">
                                <i class="fa fa-download fa-2x text-primary mb-2"></i>
                                <p class="mb-0">
                                    <strong>{{ $workflow->download_count ?? 0 }}</strong><br>
                                    <small class="text-muted">Téléchargements</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Options -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Options d'accès</h3>
                </div>
                <div class="block-content">
                    <div class="row g-4">
                        <!-- One-time Purchase -->
                        @if($workflow->price)
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">Achat unique</h4>
                                        <div class="display-4 fw-bold text-primary mb-3">
                                            {{ $workflow->price }}€
                                        </div>
                                        <p class="text-muted mb-4">
                                            Accès permanent à ce workflow
                                        </p>
                                        <button class="btn btn-primary w-100" disabled>
                                            <i class="fa fa-shopping-cart me-2"></i>
                                            Acheter maintenant
                                        </button>
                                        <small class="text-muted mt-2 d-block">
                                            Paiement sécurisé par Stripe
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Subscription -->
                        <div class="col-md-{{ $workflow->price ? '6' : '12' }}">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <span class="badge bg-warning position-absolute top-0 end-0 m-3">Recommandé</span>
                                    <h4 class="card-title">Abonnement Premium</h4>
                                    <div class="display-4 fw-bold text-warning mb-3">
                                        39€<small class="fs-6">/mois</small>
                                    </div>
                                    <p class="text-muted mb-4">
                                        Accès à TOUS les workflows premium
                                    </p>
                                    <a href="{{ route('pricing') }}" class="btn btn-warning w-100">
                                        <i class="fa fa-star me-2"></i>
                                        Devenir Premium
                                    </a>
                                    <small class="text-muted mt-2 d-block">
                                        + Support prioritaire
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Benefits -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Avantages Premium</h3>
                </div>
                <div class="block-content">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="fa-ul">
                                <li><span class="fa-li"><i class="fa fa-check text-success"></i></span>
                                    Accès à tous les workflows premium
                                </li>
                                <li><span class="fa-li"><i class="fa fa-check text-success"></i></span>
                                    Nouveaux workflows en avant-première
                                </li>
                                <li><span class="fa-li"><i class="fa fa-check text-success"></i></span>
                                    Support prioritaire par email
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="fa-ul">
                                <li><span class="fa-li"><i class="fa fa-check text-success"></i></span>
                                    Accès aux masterclasses exclusives
                                </li>
                                <li><span class="fa-li"><i class="fa fa-check text-success"></i></span>
                                    Mises à jour gratuites des workflows
                                </li>
                                <li><span class="fa-li"><i class="fa fa-check text-success"></i></span>
                                    Communauté privée Discord
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center">
                <a href="{{ route('workflows.index') }}" class="btn btn-alt-secondary">
                    <i class="fa fa-arrow-left me-2"></i>
                    Retour aux workflows
                </a>
            </div>
        </div>
    </div>
</div>
@endsection