@extends('watchtrend.layouts.app')

@section('title', $watch->name)
@section('page-title', $watch->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('watchtrend.watches.index') }}">Mes Watches</a></li>
    <li class="breadcrumb-item active">{{ $watch->name }}</li>
@endsection

@section('content')

<div x-data="sharesManager({{ $watch->id }})">

    {{-- Watch Info Card --}}
    <div class="block block-rounded mb-4">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <span class="me-2">{{ $watch->icon ?? '📊' }}</span>
                {{ $watch->name }}
            </h3>
            <div class="block-options d-flex align-items-center gap-2">
                <span class="badge
                    @if($watch->status === 'active') bg-success
                    @elseif($watch->status === 'paused') bg-warning text-dark
                    @else bg-secondary
                    @endif">
                    @if($watch->status === 'active') Actif
                    @elseif($watch->status === 'paused') En pause
                    @else Archivé
                    @endif
                </span>
                <button type="button" class="btn btn-sm btn-alt-primary" @click="openModal()">
                    <i class="fa fa-share-nodes me-1"></i>Partager
                </button>
                <a href="{{ route('watchtrend.watches.index') }}" class="btn btn-sm btn-alt-secondary">
                    <i class="fa fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
        <div class="block-content">
            <div class="row g-4">
                <div class="col-md-6">
                    @if($watch->description)
                        <p class="mb-3">{{ $watch->description }}</p>
                    @else
                        <p class="text-muted mb-3"><em>Aucune description</em></p>
                    @endif
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary-subtle text-primary fs-sm">
                            <i class="fa fa-clock me-1"></i>
                            @switch($watch->collection_frequency)
                                @case('daily') Collecte quotidienne @break
                                @case('weekly') Collecte hebdomadaire @break
                                @case('monthly') Collecte mensuelle @break
                                @case('quarterly') Collecte trimestrielle @break
                                @default {{ $watch->collection_frequency }}
                            @endswitch
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Créé le</dt>
                        <dd class="col-sm-7">{{ $watch->created_at->format('d/m/Y') }}</dd>
                        <dt class="col-sm-5 text-muted">Modifié le</dt>
                        <dd class="col-sm-7">{{ $watch->updated_at->format('d/m/Y') }}</dd>
                        @if($lastCollectedAt)
                            <dt class="col-sm-5 text-muted">Dernière collecte</dt>
                            <dd class="col-sm-7">{{ \Carbon\Carbon::parse($lastCollectedAt)->format('d/m/Y H:i') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-4 mb-4">
        <div class="col-4">
            <div class="block block-rounded text-center py-3">
                <div class="fs-2 fw-bold text-primary">{{ $itemsCount ?? 0 }}</div>
                <div class="text-muted small">Éléments collectés</div>
            </div>
        </div>
        <div class="col-4">
            <div class="block block-rounded text-center py-3">
                <div class="fs-2 fw-bold text-success">{{ $watch->interests->count() }}</div>
                <div class="text-muted small">Centres d'intérêt</div>
            </div>
        </div>
        <div class="col-4">
            <div class="block block-rounded text-center py-3">
                <div class="fs-2 fw-bold text-info">{{ $watch->sources->count() }}</div>
                <div class="text-muted small">Sources actives</div>
            </div>
        </div>
    </div>

    {{-- Interests & Sources --}}
    <div class="row g-4">

        {{-- Interests Mini-List --}}
        <div class="col-md-6">
            <div class="block block-rounded h-100">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-bullseye me-2 text-warning"></i>Centres d'intérêt
                    </h3>
                    <div class="block-options">
                        <a href="{{ route('watchtrend.interests.index', $watch) }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-arrow-right me-1"></i>Gérer
                        </a>
                    </div>
                </div>
                <div class="block-content p-0">
                    @if($watch->interests->isNotEmpty())
                        <ul class="list-group list-group-flush">
                            @foreach($watch->interests->take(5) as $interest)
                                <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2">
                                    <span>{{ $interest->name }}</span>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($interest->keywords && count($interest->keywords) > 0)
                                            <span class="badge bg-light text-dark small">
                                                {{ count($interest->keywords) }} mots-clés
                                            </span>
                                        @endif
                                        <span class="badge
                                            @if($interest->priority === 'high') bg-danger
                                            @elseif($interest->priority === 'medium') bg-warning text-dark
                                            @else bg-info
                                            @endif small">
                                            @if($interest->priority === 'high') Haute
                                            @elseif($interest->priority === 'medium') Moyenne
                                            @else Basse
                                            @endif
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                            @if($watch->interests->count() > 5)
                                <li class="list-group-item text-center text-muted small py-2">
                                    + {{ $watch->interests->count() - 5 }} autre(s)
                                </li>
                            @endif
                        </ul>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fa fa-bullseye fa-2x mb-2 opacity-50"></i>
                            <p class="mb-2">Aucun centre d'intérêt</p>
                            <a href="{{ route('watchtrend.interests.index', $watch) }}" class="btn btn-sm btn-alt-primary">
                                <i class="fa fa-plus me-1"></i>Ajouter
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sources Mini-List --}}
        <div class="col-md-6">
            <div class="block block-rounded h-100">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-satellite-dish me-2 text-info"></i>Sources
                    </h3>
                    <div class="block-options">
                        <a href="{{ route('watchtrend.sources.index') }}" class="btn btn-sm btn-alt-primary">
                            <i class="fa fa-arrow-right me-1"></i>Gérer
                        </a>
                    </div>
                </div>
                <div class="block-content p-0">
                    @if($watch->sources->isNotEmpty())
                        <ul class="list-group list-group-flush">
                            @foreach($watch->sources->take(5) as $source)
                                <li class="list-group-item d-flex align-items-center justify-content-between px-3 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        @switch($source->type)
                                            @case('youtube') <i class="fab fa-youtube text-danger"></i> @break
                                            @case('reddit') <i class="fab fa-reddit text-orange"></i> @break
                                            @case('rss') <i class="fa fa-rss text-warning"></i> @break
                                            @case('hackernews') <i class="fab fa-hacker-news text-warning"></i> @break
                                            @case('github') <i class="fab fa-github text-dark"></i> @break
                                            @case('twitter') <i class="fab fa-twitter text-info"></i> @break
                                            @default <i class="fa fa-globe text-muted"></i>
                                        @endswitch
                                        <span>{{ $source->name }}</span>
                                    </div>
                                    <span class="badge
                                        @if($source->status === 'active') bg-success
                                        @elseif($source->status === 'paused') bg-warning text-dark
                                        @elseif($source->status === 'error') bg-danger
                                        @else bg-secondary
                                        @endif small">
                                        @if($source->status === 'active') Active
                                        @elseif($source->status === 'paused') En pause
                                        @elseif($source->status === 'error') Erreur
                                        @else {{ $source->status }}
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                            @if($watch->sources->count() > 5)
                                <li class="list-group-item text-center text-muted small py-2">
                                    + {{ $watch->sources->count() - 5 }} autre(s)
                                </li>
                            @endif
                        </ul>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fa fa-satellite-dish fa-2x mb-2 opacity-50"></i>
                            <p class="mb-2">Aucune source configurée</p>
                            <a href="{{ route('watchtrend.sources.index') }}" class="btn btn-sm btn-alt-primary">
                                <i class="fa fa-plus me-1"></i>Ajouter
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Share Modal --}}
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shareModalLabel">
                        <i class="fa fa-share-nodes me-2 text-primary"></i>Partager la veille
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">

                    {{-- Invite Form --}}
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-3">Inviter un collaborateur</h6>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Adresse email</label>
                                <input type="email" class="form-control" x-model="inviteEmail"
                                    placeholder="collaborateur@exemple.com"
                                    @keydown.enter.prevent="sendInvite()">
                                <template x-if="inviteError">
                                    <div class="text-danger small mt-1" x-text="inviteError"></div>
                                </template>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted mb-1">Permission</label>
                                <select class="form-select" x-model="invitePermission">
                                    <option value="view">Voir</option>
                                    <option value="edit">Modifier</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary w-100"
                                    @click="sendInvite()"
                                    :disabled="inviting">
                                    <template x-if="inviting">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                    </template>
                                    <i class="fa fa-paper-plane me-1" x-show="!inviting"></i>
                                    Inviter
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Shares List --}}
                    <div>
                        <h6 class="fw-semibold mb-3">
                            Partages actifs
                            <template x-if="loading">
                                <span class="spinner-border spinner-border-sm ms-2 text-muted"></span>
                            </template>
                        </h6>

                        <template x-if="!loading && shares.length === 0">
                            <div class="text-center text-muted py-3">
                                <i class="fa fa-users fa-2x mb-2 opacity-40"></i>
                                <p class="mb-0 small">Aucun partage pour le moment.</p>
                            </div>
                        </template>

                        <ul class="list-group list-group-flush" x-show="shares.length > 0">
                            <template x-for="share in shares" :key="share.id">
                                <li class="list-group-item d-flex align-items-center justify-content-between px-0 py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa fa-user-circle text-muted fa-lg"></i>
                                        <div>
                                            <div class="fw-medium small" x-text="share.user_name || share.shared_with_email"></div>
                                            <div class="text-muted" style="font-size:.75rem" x-show="share.user_name" x-text="share.shared_with_email"></div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge"
                                            :class="share.accepted_at ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'"
                                            x-text="share.accepted_at ? 'Accepté' : 'En attente'">
                                        </span>
                                        <select class="form-select form-select-sm" style="width:auto"
                                            x-model="share.permission"
                                            @change="updatePermission(share)">
                                            <option value="view">Voir</option>
                                            <option value="edit">Modifier</option>
                                        </select>
                                        <button type="button" class="btn btn-sm btn-alt-danger"
                                            @click="revoke(share)"
                                            title="Révoquer">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-alt-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

</div>{{-- end x-data --}}

@endsection

@push('scripts')
<script>
function sharesManager(watchId) {
    return {
        watchId: watchId,
        shares: [],
        loading: false,
        inviteEmail: '',
        invitePermission: 'view',
        inviting: false,
        inviteError: null,
        modalInstance: null,

        init() {
            const el = document.getElementById('shareModal');
            this.modalInstance = new bootstrap.Modal(el);
            el.addEventListener('show.bs.modal', () => this.loadShares());
        },

        openModal() {
            this.inviteEmail = '';
            this.inviteError = null;
            this.modalInstance.show();
        },

        async loadShares() {
            this.loading = true;
            try {
                const res = await fetch(`/watchtrend/watches/${this.watchId}/shares`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.success) {
                    this.shares = data.shares;
                }
            } catch (e) {
                WTModal.toast('error', 'Impossible de charger les partages.');
            } finally {
                this.loading = false;
            }
        },

        async sendInvite() {
            this.inviteError = null;
            if (!this.inviteEmail) {
                this.inviteError = 'Veuillez saisir une adresse email.';
                return;
            }
            this.inviting = true;
            try {
                const res = await fetch(`/watchtrend/watches/${this.watchId}/shares/invite`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ email: this.inviteEmail, permission: this.invitePermission }),
                });
                const data = await res.json();
                if (!res.ok) {
                    this.inviteError = data.message || 'Erreur lors de l\'invitation.';
                    return;
                }
                this.shares.unshift(data.share);
                this.inviteEmail = '';
                WTModal.toast('success', 'Invitation envoyée !');
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion.');
            } finally {
                this.inviting = false;
            }
        },

        async updatePermission(share) {
            try {
                const res = await fetch(`/watchtrend/watches/${this.watchId}/shares/${share.id}/permission`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ permission: share.permission }),
                });
                const data = await res.json();
                if (!res.ok) {
                    WTModal.toast('error', data.message || 'Erreur lors de la mise à jour.');
                    return;
                }
                WTModal.toast('success', 'Permission mise à jour.');
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion.');
            }
        },

        async revoke(share) {
            if (!confirm('Révoquer ce partage ?')) return;
            try {
                const res = await fetch(`/watchtrend/watches/${this.watchId}/shares/${share.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await res.json();
                if (res.ok) {
                    this.shares = this.shares.filter(s => s.id !== share.id);
                    WTModal.toast('success', 'Partage révoqué.');
                } else {
                    WTModal.toast('error', data.message || 'Erreur lors de la révocation.');
                }
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion.');
            }
        },
    };
}
</script>
@endpush
