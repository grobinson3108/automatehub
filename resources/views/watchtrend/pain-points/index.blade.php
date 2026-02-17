@extends('watchtrend.layouts.app')

@section('title', 'Pain Points')
@section('page-title', 'Pain Points')

@section('breadcrumb')
    <li class="breadcrumb-item active">Pain Points</li>
@endsection

@section('content')
<div x-data="painPointsManager()" x-init="init()">

    {{-- Filters Row --}}
    <div class="row g-3 mb-4">
        {{-- Watch Filter --}}
        <div class="col-md-4">
            <label class="form-label text-muted small fw-semibold text-uppercase mb-1">Filtrer par Watch</label>
            <select class="form-select" x-model="selectedWatchId">
                <option value="all">Tous les watches</option>
                @foreach($watches as $w)
                    <option value="{{ $w->id }}">{{ $w->icon ?? '📊' }} {{ $w->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Main Block --}}
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <i class="fa fa-exclamation-triangle me-2 text-danger"></i>Pain Points
                <span class="badge bg-secondary ms-2">
                    <span x-text="activeCount"></span>/10 actifs
                </span>
            </h3>
            <div class="block-options">
                <button class="btn btn-sm btn-alt-primary" @click="openAdd()">
                    <i class="fa fa-plus me-1"></i> Nouveau Pain Point
                </button>
            </div>
        </div>

        {{-- Status Tabs --}}
        <div class="block-content pb-0 border-bottom">
            <ul class="nav nav-tabs nav-tabs-block">
                <li class="nav-item">
                    <button class="nav-link" :class="{'active': filterStatus === 'active'}" @click="filterStatus = 'active'">
                        <i class="fa fa-fire me-1 text-danger"></i>Actifs
                        <span class="badge bg-danger ms-1 small" x-text="painPoints.filter(p => p.status !== 'resolved').length"></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" :class="{'active': filterStatus === 'resolved'}" @click="filterStatus = 'resolved'">
                        <i class="fa fa-check-circle me-1 text-success"></i>Résolus
                        <span class="badge bg-success ms-1 small" x-text="painPoints.filter(p => p.status === 'resolved').length"></span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="block-content">

            {{-- Pain Point List --}}
            <template x-if="filteredPainPoints().length > 0">
                <div class="d-flex flex-column gap-3">
                    <template x-for="pp in filteredPainPoints()" :key="pp.id">
                        <div class="block block-rounded border mb-0">
                            <div class="block-header block-header-default py-2">
                                <h3 class="block-title fs-6 text-truncate" style="max-width:300px;" x-text="pp.title"></h3>
                                <div class="block-options d-flex align-items-center gap-2">
                                    {{-- Priority badge --}}
                                    <span class="badge"
                                        :class="{
                                            'bg-danger': pp.priority === 'high',
                                            'bg-warning text-dark': pp.priority === 'medium',
                                            'bg-info': pp.priority === 'low'
                                        }"
                                        x-text="pp.priority === 'high' ? 'Haute' : (pp.priority === 'medium' ? 'Moyenne' : 'Basse')">
                                    </span>
                                    {{-- Status badge --}}
                                    <span class="badge"
                                        :class="pp.status === 'resolved' ? 'bg-info' : 'bg-danger'"
                                        x-text="pp.status === 'resolved' ? 'Résolu' : 'Actif'">
                                    </span>
                                </div>
                            </div>
                            <div class="block-content py-2">
                                {{-- Watch name --}}
                                <template x-if="pp.watch_name">
                                    <div class="mb-2">
                                        <span class="badge bg-primary-subtle text-primary small">
                                            <i class="fa fa-binoculars me-1"></i>
                                            <span x-text="pp.watch_name"></span>
                                        </span>
                                    </div>
                                </template>
                                {{-- Description --}}
                                <template x-if="pp.description">
                                    <p class="text-muted small mb-0"
                                        style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"
                                        x-text="pp.description"></p>
                                </template>
                            </div>
                            <div class="block-content block-content-full bg-body-light border-top py-2">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-alt-primary" @click="openEdit(pp)">
                                        <i class="fa fa-pen me-1"></i>Éditer
                                    </button>
                                    <template x-if="pp.status !== 'resolved'">
                                        <button class="btn btn-sm btn-alt-success" @click="resolve(pp.id)">
                                            <i class="fa fa-check me-1"></i>Résoudre
                                        </button>
                                    </template>
                                    <button class="btn btn-sm btn-alt-danger" @click="deletePainPoint(pp.id, pp.title)">
                                        <i class="fa fa-trash me-1"></i>Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="filteredPainPoints().length === 0">
                <div class="py-5 text-center">
                    <i class="fa fa-exclamation-triangle fa-3x text-muted opacity-50 mb-3"></i>
                    <h5 class="text-muted" x-text="filterStatus === 'resolved' ? 'Aucun pain point résolu' : 'Aucun pain point actif'"></h5>
                    <p class="text-muted mb-4" x-show="filterStatus === 'active'">Documentez les problèmes identifiés dans vos watches.</p>
                    <button class="btn btn-primary" x-show="filterStatus === 'active'" @click="openAdd()">
                        <i class="fa fa-plus me-1"></i> Nouveau Pain Point
                    </button>
                </div>
            </template>

        </div>
    </div>

    {{-- Pain Point Modal --}}
    <div class="modal fade" id="painPointModal" tabindex="-1" aria-labelledby="painPointModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="painPointModalLabel">
                        <span x-text="editingId ? 'Modifier le Pain Point' : 'Nouveau Pain Point'"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Watch --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Watch <span class="text-danger">*</span></label>
                            <select class="form-select" :class="{'is-invalid': errors.watch_id}" x-model="formData.watch_id">
                                <option value="">-- Sélectionner un watch --</option>
                                @foreach($watches as $w)
                                    <option value="{{ $w->id }}">{{ $w->icon ?? '📊' }} {{ $w->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" x-text="errors.watch_id ? errors.watch_id[0] : ''"></div>
                        </div>

                        {{-- Priority --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Priorité</label>
                            <select class="form-select" :class="{'is-invalid': errors.priority}" x-model="formData.priority">
                                <option value="high">Haute</option>
                                <option value="medium">Moyenne</option>
                                <option value="low">Basse</option>
                            </select>
                            <div class="invalid-feedback" x-text="errors.priority ? errors.priority[0] : ''"></div>
                        </div>

                        {{-- Title --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" :class="{'is-invalid': errors.title}"
                                x-model="formData.title" placeholder="Ex: Manque de visibilité sur les concurrents IA">
                            <div class="invalid-feedback" x-text="errors.title ? errors.title[0] : ''"></div>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" :class="{'is-invalid': errors.description}"
                                x-model="formData.description" rows="4"
                                placeholder="Décrivez le problème en détail..."></textarea>
                            <div class="invalid-feedback" x-text="errors.description ? errors.description[0] : ''"></div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" @click="save()" :disabled="saving">
                        <span x-show="saving" class="spinner-border spinner-border-sm me-1" role="status"></span>
                        <i x-show="!saving" class="fa fa-save me-1"></i>
                        <span x-text="saving ? 'Enregistrement...' : 'Enregistrer'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function painPointsManager() {
    return {
        painPoints: @json($painPoints),
        selectedWatchId: 'all',
        filterStatus: 'active',
        formData: { watch_id: '', title: '', description: '', priority: 'medium' },
        editingId: null,
        saving: false,
        errors: {},

        init() {
            // Nothing extra needed
        },

        get activeCount() {
            return this.painPoints.filter(p => p.status !== 'resolved').length;
        },

        filteredPainPoints() {
            return this.painPoints.filter(pp => {
                const matchWatch = this.selectedWatchId === 'all' || String(pp.watch_id) === String(this.selectedWatchId);
                const matchStatus = this.filterStatus === 'resolved'
                    ? pp.status === 'resolved'
                    : pp.status !== 'resolved';
                return matchWatch && matchStatus;
            });
        },

        resetForm() {
            this.formData = { watch_id: '', title: '', description: '', priority: 'medium' };
            this.editingId = null;
            this.errors = {};
        },

        openAdd() {
            this.resetForm();
            new bootstrap.Modal(document.getElementById('painPointModal')).show();
        },

        openEdit(pp) {
            this.editingId = pp.id;
            this.formData = {
                watch_id: pp.watch_id || '',
                title: pp.title,
                description: pp.description || '',
                priority: pp.priority || 'medium'
            };
            this.errors = {};
            new bootstrap.Modal(document.getElementById('painPointModal')).show();
        },

        async save() {
            this.saving = true;
            this.errors = {};
            try {
                const url = this.editingId ? `/watchtrend/pain-points/${this.editingId}` : '/watchtrend/pain-points';
                const method = this.editingId ? 'PUT' : 'POST';
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });
                const data = await res.json();
                if (!res.ok) {
                    this.errors = data.errors || {};
                    WTModal.toast('error', data.message || 'Erreur de validation');
                    return;
                }
                bootstrap.Modal.getInstance(document.getElementById('painPointModal')).hide();
                WTModal.toast('success', this.editingId ? 'Pain Point modifié !' : 'Pain Point créé !');
                location.reload();
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion');
            } finally {
                this.saving = false;
            }
        },

        async resolve(id) {
            const result = await WTModal.confirm({
                title: 'Résoudre ce Pain Point ?',
                text: 'Ce pain point sera marqué comme résolu.'
            });
            if (!result.isConfirmed) return;
            try {
                const res = await fetch(`/watchtrend/pain-points/${id}/resolve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    WTModal.toast('success', 'Pain Point résolu !');
                    location.reload();
                }
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion');
            }
        },

        async deletePainPoint(id, title) {
            const result = await WTModal.delete({ itemName: title });
            if (!result.isConfirmed) return;
            try {
                const res = await fetch(`/watchtrend/pain-points/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    WTModal.toast('success', 'Pain Point supprimé !');
                    location.reload();
                }
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion');
            }
        }
    }
}
</script>
@endpush
