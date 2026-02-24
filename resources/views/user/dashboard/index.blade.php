@extends('layouts.backend')

@section('title', 'Mon Espace - AutomateHub')

@section('content')
<!-- ===== HERO ===== -->
<div class="bg-body-light">
    <div class="content content-full">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
            <div class="flex-grow-1">
                <h1 class="h3 fw-bold mb-1">
                    Bienvenue, {{ auth()->user()->first_name ?? auth()->user()->name }} !
                </h1>
                <h2 class="fs-base lh-base fw-medium text-muted mb-0">
                    Votre hub d'applications IA — automatisez, publiez, prospectez
                </h2>
            </div>
            <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-alt">
                    <li class="breadcrumb-item">
                        <a class="link-fx" href="{{ route('user.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">Accueil</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<!-- END Hero -->

<!-- ===== PAGE CONTENT ===== -->
<div class="content">

    {{-- ===== STATS RAPIDES ===== --}}
    <div class="row">
        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center">
                <div class="block-content block-content-full py-3">
                    <div class="fs-2 fw-bold text-primary">{{ $stats['active_apps'] }}</div>
                    <div class="fs-sm fw-semibold text-uppercase text-muted">Apps actives</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center">
                <div class="block-content block-content-full py-3">
                    <div class="fs-2 fw-bold text-success">{{ number_format($billing['monthly_total'], 0) }}€</div>
                    <div class="fs-sm fw-semibold text-uppercase text-muted">Coût mensuel</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center">
                <div class="block-content block-content-full py-3">
                    <div class="fs-2 fw-bold text-warning">{{ $billing['trial_count'] }}</div>
                    <div class="fs-sm fw-semibold text-uppercase text-muted">Essais en cours</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="block block-rounded text-center">
                <div class="block-content block-content-full py-3">
                    <div class="fs-2 fw-bold text-info">{{ $stats['credits_used_month'] }}</div>
                    <div class="fs-sm fw-semibold text-uppercase text-muted">Crédits ce mois</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MES ABONNEMENTS ===== --}}
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <i class="fas fa-credit-card text-primary me-2"></i>
                Mes Abonnements
            </h3>
            <div class="block-options">
                <a class="btn btn-sm btn-alt-primary" href="{{ route('apps.index') }}">
                    <i class="fas fa-plus me-1"></i> Ajouter une app
                </a>
            </div>
        </div>
        <div class="block-content">
            @if($userApps->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                    <h4 class="fw-bold mb-2">Aucun abonnement actif</h4>
                    <p class="text-muted mb-4">Découvrez nos mini-apps IA et commencez avec 14 jours d'essai gratuit.</p>
                    <a href="{{ route('apps.index') }}" class="btn btn-primary">
                        <i class="fas fa-rocket me-1"></i> Explorer la marketplace
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter mb-0">
                        <thead>
                            <tr>
                                <th>App</th>
                                <th>Forfait</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center">Facturation</th>
                                <th class="text-center">Échéance</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userApps as $subscription)
                                @php
                                    $app = $subscription->app;
                                    $plan = $subscription->pricingPlan;
                                    $color = $app->color_primary ?? '#0665d0';
                                    $isTrial = $subscription->onTrial();
                                    $trialDaysLeft = $isTrial && $subscription->trial_ends_at
                                        ? max(0, now()->diffInDays($subscription->trial_ends_at, false))
                                        : null;
                                    $monthlyPrice = $plan
                                        ? ($subscription->billing_period === 'yearly'
                                            ? round($plan->yearly_price / 12, 2)
                                            : $plan->monthly_price)
                                        : 0;
                                @endphp
                                @if($app)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <div class="d-flex align-items-center justify-content-center rounded"
                                                     style="width:36px;height:36px;background:{{ $color }}1a;">
                                                    @if($app->icon)
                                                        <i class="{{ $app->icon }}" style="color:{{ $color }};"></i>
                                                    @else
                                                        <i class="fas fa-puzzle-piece" style="color:{{ $color }};"></i>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $app->name }}</div>
                                                @if($app->tagline)
                                                    <div class="fs-xs text-muted">{{ Str::limit($app->tagline, 40) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $plan->name ?? '—' }}</span>
                                        <div class="fs-xs text-muted">{{ number_format($monthlyPrice, 0) }}€/mois</div>
                                    </td>
                                    <td class="text-center">
                                        @if($isTrial)
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i>
                                                Essai{{ $trialDaysLeft !== null ? ' · ' . $trialDaysLeft . 'j' : '' }}
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i> Actif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="fs-sm">{{ $subscription->billing_period === 'yearly' ? 'Annuel' : 'Mensuel' }}</span>
                                        @if($subscription->billing_period === 'yearly' && $plan && $plan->yearly_discount)
                                            <div class="fs-xs text-success">-{{ $plan->yearly_discount }}%</div>
                                        @endif
                                    </td>
                                    <td class="text-center fs-sm">
                                        @if($isTrial && $subscription->trial_ends_at)
                                            {{ $subscription->trial_ends_at->format('d/m/Y') }}
                                        @elseif($subscription->subscription_ends_at)
                                            {{ $subscription->subscription_ends_at->format('d/m/Y') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('my-apps.dashboard', $app->slug) }}"
                                           class="btn btn-sm btn-alt-primary">
                                            <i class="fas fa-external-link-alt me-1"></i> Ouvrir
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Résumé facturation --}}
                @if($billing['paid_count'] > 0 || $billing['trial_count'] > 0)
                <div class="block-content block-content-full bg-body-light border-top">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="fs-sm text-muted">Total mensuel estimé</div>
                            <div class="fs-5 fw-bold text-primary">{{ number_format($billing['monthly_total'], 0) }}€<span class="fs-sm fw-normal text-muted">/mois</span></div>
                        </div>
                        <div class="col-4">
                            <div class="fs-sm text-muted">Prochain renouvellement</div>
                            <div class="fs-5 fw-bold">
                                {{ $billing['next_renewal'] ? $billing['next_renewal']->format('d/m/Y') : '—' }}
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="fs-sm text-muted">Forfaits actifs / essais</div>
                            <div class="fs-5 fw-bold">
                                <span class="text-success">{{ $billing['paid_count'] }}</span>
                                <span class="text-muted">/</span>
                                <span class="text-warning">{{ $billing['trial_count'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ===== UPSELL + DÉCOUVRIR ===== --}}
    <div class="row">

        {{-- Booster vos apps (upsell) --}}
        @if($upsellOpportunities->isNotEmpty())
        <div class="col-lg-5">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fas fa-arrow-up text-success me-2"></i>
                        Booster vos apps
                    </h3>
                </div>
                <div class="block-content">
                    @foreach($upsellOpportunities as $upsell)
                        @php
                            $uColor = $upsell['app']->color_primary ?? '#0665d0';
                        @endphp
                        <div class="d-flex align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-shrink-0 me-3">
                                <div class="d-flex align-items-center justify-content-center rounded"
                                     style="width:40px;height:40px;background:{{ $uColor }}1a;">
                                    @if($upsell['app']->icon)
                                        <i class="{{ $upsell['app']->icon }}" style="color:{{ $uColor }};"></i>
                                    @else
                                        <i class="fas fa-puzzle-piece" style="color:{{ $uColor }};"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold fs-sm">{{ $upsell['app']->name }}</div>
                                <div class="fs-xs text-muted">
                                    {{ $upsell['current_plan']->name }}
                                    <i class="fas fa-arrow-right mx-1"></i>
                                    <span class="text-success fw-semibold">{{ $upsell['next_plan']->name }}</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <div class="fs-sm fw-bold text-success">{{ number_format($upsell['next_plan']->monthly_price, 0) }}€/mois</div>
                                <a href="{{ route('apps.show', $upsell['app']->slug) }}" class="btn btn-sm btn-outline-success mt-1 fs-xs">
                                    Upgrader
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Activité récente --}}
        <div class="col-lg-{{ $upsellOpportunities->isNotEmpty() ? '7' : '5' }}">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fas fa-history text-muted me-2"></i>
                        Activité récente
                    </h3>
                </div>
                <div class="block-content">
                    @if($recentActivity->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Aucune activité ces 7 derniers jours</p>
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($recentActivity as $log)
                                <li class="list-group-item px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <span class="badge bg-primary-light text-primary rounded-circle p-2">
                                                <i class="fas fa-bolt fa-xs"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold fs-sm">
                                                {{ $log->app->name ?? '—' }}
                                                <span class="text-muted fw-normal">— {{ $log->action }}</span>
                                            </div>
                                            <div class="fs-xs text-muted">
                                                {{ $log->created_at->diffForHumans() }}
                                                @if($log->credits_used)
                                                    · <span class="text-primary">{{ $log->credits_used }} crédit(s)</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        {{-- Découvrir les apps disponibles --}}
        @if($upsellOpportunities->isEmpty())
        <div class="col-lg-7">
        @else
        <div class="col-12">
        @endif
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fas fa-store text-muted me-2"></i>
                        Découvrir de nouvelles apps
                    </h3>
                    <div class="block-options">
                        <a class="btn-block-option fs-sm" href="{{ route('apps.index') }}">
                            Voir tout <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="block-content">
                    @if($availableApps->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted mb-0">Vous avez déjà toutes les apps disponibles !</p>
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($availableApps->take(6) as $app)
                                @php
                                    $color = $app->color_primary ?? '#6c757d';
                                    $lowestPrice = $app->pricingPlans->min('monthly_price');
                                    $isAvailable = in_array($app->status, ['beta', 'active']);
                                @endphp
                                <div class="col-sm-6 col-lg-4">
                                    <div class="block block-rounded mb-0{{ !$isAvailable ? ' opacity-50' : '' }}"
                                         style="border-top: 3px solid {{ $color }};">
                                        <div class="block-content py-3">
                                            <div class="d-flex align-items-start mb-2">
                                                <div class="flex-shrink-0 me-2">
                                                    <div class="d-flex align-items-center justify-content-center rounded"
                                                         style="width:36px;height:36px;background:{{ $color }}1a;">
                                                        @if($app->icon)
                                                            <i class="{{ $app->icon }}" style="color:{{ $color }};"></i>
                                                        @else
                                                            <i class="fas fa-cube" style="color:{{ $color }};"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold fs-sm">{{ $app->name }}</div>
                                                    <div class="fs-xs text-muted">
                                                        @if($app->status === 'beta')
                                                            <span class="badge bg-info py-0 px-1" style="font-size:10px;">BETA</span>
                                                        @elseif($app->status === 'coming_soon')
                                                            <span class="badge bg-secondary py-0 px-1" style="font-size:10px;">BIENTÔT</span>
                                                        @endif
                                                        @if($lowestPrice)
                                                            <span class="ms-1">dès {{ number_format($lowestPrice, 0) }}€/mois</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if($app->tagline)
                                                <p class="fs-xs text-muted mb-2 fst-italic lh-sm">{{ Str::limit($app->tagline, 60) }}</p>
                                            @endif
                                            @if($isAvailable)
                                                <a href="{{ route('apps.show', $app->slug) }}"
                                                   class="btn btn-sm btn-alt-secondary w-100 fs-xs">
                                                    <i class="fas fa-eye me-1"></i> Découvrir
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-alt-secondary w-100 fs-xs" disabled>
                                                    <i class="fas fa-clock me-1"></i> Bientôt dispo
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
    {{-- END row --}}

</div>
{{-- END Page Content --}}

{{-- ===== MODAL ONBOARDING ===== --}}
@if(isset($showOnboarding) && $showOnboarding)
<div class="onboarding-overlay"
     style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.75);z-index:1040;display:flex;align-items:center;justify-content:center;">
    <div style="position:relative;width:90%;max-width:560px;z-index:1050;">
        <div class="block block-rounded mb-0">
            <div class="block-header bg-primary-dark">
                <h3 class="block-title text-white">
                    <i class="fas fa-rocket me-2"></i> Bienvenue sur AutomateHub !
                </h3>
            </div>
            <div class="block-content bg-white text-center py-5">
                <i class="fas fa-boxes fa-3x text-primary mb-3"></i>
                <h4 class="fw-bold mb-2">Votre hub d'apps IA est prêt</h4>
                <p class="text-muted mb-4">
                    Commencez par explorer nos mini-apps et lancez votre premier essai gratuit de 14 jours.
                </p>
                <a href="{{ route('apps.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-store me-2"></i> Explorer la marketplace
                </a>
            </div>
            <div class="block-content block-content-full bg-body text-end">
                <form method="POST" action="{{ route('onboarding.update-preferences') }}">
                    @csrf
                    <button type="submit" class="btn btn-alt-secondary btn-sm">
                        Ignorer pour l'instant
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
