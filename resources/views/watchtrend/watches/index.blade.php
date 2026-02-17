@extends('watchtrend.layouts.app')

@section('title', 'Mes Watches')
@section('page-title', 'Mes Watches')

@section('breadcrumb')
    <li class="breadcrumb-item active">Mes Watches</li>
@endsection

@section('content')
<div x-data="watchesManager()" x-init="init()">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fs-4 fw-bold mb-0">
            <i class="fa fa-binoculars me-2 text-primary"></i>Mes Watches
        </h2>
        <button class="btn btn-primary" @click="openAdd()">
            <i class="fa fa-plus me-1"></i> Nouveau Watch
        </button>
    </div>

    {{-- Watch Grid --}}
    <template x-if="watches.length > 0">
        <div class="row g-4">
            <template x-for="watch in watches" :key="watch.id">
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="block block-rounded h-100">
                        <div class="block-header block-header-default">
                            <h3 class="block-title text-truncate" style="max-width:200px;">
                                <span x-text="watch.icon || '📊'" class="me-2"></span>
                                <span x-text="watch.name"></span>
                            </h3>
                            <div class="block-options">
                                <span class="badge"
                                    :class="{
                                        'bg-success': watch.status === 'active',
                                        'bg-warning text-dark': watch.status === 'paused',
                                        'bg-secondary': watch.status === 'archived'
                                    }"
                                    x-text="watch.status === 'active' ? 'Actif' : (watch.status === 'paused' ? 'En pause' : 'Archivé')">
                                </span>
                            </div>
                        </div>
                        <div class="block-content">
                            <p class="text-muted small mb-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"
                                x-text="watch.description || 'Aucune description'"></p>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-primary-subtle text-primary">
                                    <i class="fa fa-clock me-1"></i>
                                    <span x-text="frequencyLabel(watch.collection_frequency)"></span>
                                </span>
                                <span class="badge bg-light text-dark">
                                    <i class="fa fa-bullseye me-1"></i>
                                    <span x-text="(watch.interests_count || 0) + ' intérêts'"></span>
                                </span>
                                <span class="badge bg-light text-dark">
                                    <i class="fa fa-satellite-dish me-1"></i>
                                    <span x-text="(watch.sources_count || 0) + ' sources'"></span>
                                </span>
                            </div>
                        </div>
                        <div class="block-content block-content-full bg-body-light border-top">
                            <div class="d-flex flex-wrap gap-2">
                                <a :href="'/watchtrend/watches/' + watch.id" class="btn btn-sm btn-alt-secondary">
                                    <i class="fa fa-eye me-1"></i>Voir
                                </a>
                                <button class="btn btn-sm btn-alt-primary" @click="openEdit(watch)">
                                    <i class="fa fa-pen me-1"></i>Éditer
                                </button>
                                <template x-if="watch.status === 'active'">
                                    <button class="btn btn-sm btn-alt-warning" @click="toggleStatus(watch.id, 'pause')">
                                        <i class="fa fa-pause me-1"></i>Pause
                                    </button>
                                </template>
                                <template x-if="watch.status === 'paused'">
                                    <button class="btn btn-sm btn-alt-success" @click="toggleStatus(watch.id, 'resume')">
                                        <i class="fa fa-play me-1"></i>Reprendre
                                    </button>
                                </template>
                                <template x-if="watch.status !== 'archived'">
                                    <button class="btn btn-sm btn-alt-secondary" @click="toggleStatus(watch.id, 'archive')">
                                        <i class="fa fa-archive me-1"></i>Archiver
                                    </button>
                                </template>
                                <button class="btn btn-sm btn-alt-danger" @click="deleteWatch(watch.id, watch.name)">
                                    <i class="fa fa-trash me-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </template>

    {{-- Empty State --}}
    <template x-if="watches.length === 0">
        <div class="block block-rounded">
            <div class="block-content py-5 text-center">
                <div class="mb-3">
                    <i class="fa fa-binoculars fa-3x text-muted opacity-50"></i>
                </div>
                <h4 class="text-muted">Aucun watch configuré</h4>
                <p class="text-muted mb-4">Créez votre premier watch pour commencer à surveiller des tendances.</p>
                <button class="btn btn-primary" @click="openAdd()">
                    <i class="fa fa-plus me-1"></i> Créer mon premier Watch
                </button>
            </div>
        </div>
    </template>

    {{-- Watch Modal --}}
    <div class="modal fade" id="watchModal" tabindex="-1" aria-labelledby="watchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="watchModalLabel">
                        <span x-text="editingId ? 'Modifier le Watch' : 'Nouveau Watch'"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Name --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nom du Watch <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" :class="{'is-invalid': errors.name}"
                                x-model="formData.name" placeholder="Ex: Tendances IA Générative">
                            <div class="invalid-feedback" x-text="errors.name ? errors.name[0] : ''"></div>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" :class="{'is-invalid': errors.description}"
                                x-model="formData.description" rows="3"
                                placeholder="Décrivez l'objectif de ce watch..."></textarea>
                            <div class="invalid-feedback" x-text="errors.description ? errors.description[0] : ''"></div>
                        </div>

                        {{-- Icon --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Icône (emoji)</label>
                            <input type="text" class="form-control" :class="{'is-invalid': errors.icon}"
                                x-model="formData.icon" placeholder="📊" maxlength="4">
                            <div class="form-text">Entrez un emoji pour représenter ce watch</div>
                            <div class="invalid-feedback" x-text="errors.icon ? errors.icon[0] : ''"></div>
                        </div>

                        {{-- Frequency --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fréquence de collecte</label>
                            <select class="form-select" :class="{'is-invalid': errors.collection_frequency}"
                                x-model="formData.collection_frequency">
                                <option value="daily">Quotidienne</option>
                                <option value="weekly">Hebdomadaire</option>
                                <option value="monthly">Mensuelle</option>
                                <option value="quarterly">Trimestrielle</option>
                            </select>
                            <div class="invalid-feedback" x-text="errors.collection_frequency ? errors.collection_frequency[0] : ''"></div>
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
function watchesManager() {
    return {
        watches: @json($watches),
        formData: { name: '', description: '', icon: '📊', collection_frequency: 'daily' },
        editingId: null,
        saving: false,
        errors: {},

        init() {
            // Normalize watches data
            this.watches = this.watches.map(w => ({
                ...w,
                interests_count: w.interests_count || 0,
                sources_count: w.sources_count || 0,
            }));
        },

        frequencyLabel(freq) {
            const labels = {
                daily: 'Quotidien',
                weekly: 'Hebdomadaire',
                monthly: 'Mensuel',
                quarterly: 'Trimestriel'
            };
            return labels[freq] || freq;
        },

        resetForm() {
            this.formData = { name: '', description: '', icon: '📊', collection_frequency: 'daily' };
            this.editingId = null;
            this.errors = {};
        },

        openAdd() {
            this.resetForm();
            new bootstrap.Modal(document.getElementById('watchModal')).show();
        },

        openEdit(watch) {
            this.editingId = watch.id;
            this.formData = {
                name: watch.name,
                description: watch.description || '',
                icon: watch.icon || '📊',
                collection_frequency: watch.collection_frequency || 'daily'
            };
            this.errors = {};
            new bootstrap.Modal(document.getElementById('watchModal')).show();
        },

        async save() {
            this.saving = true;
            this.errors = {};
            try {
                const url = this.editingId ? `/watchtrend/watches/${this.editingId}` : '/watchtrend/watches';
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
                bootstrap.Modal.getInstance(document.getElementById('watchModal')).hide();
                WTModal.toast('success', this.editingId ? 'Watch modifié !' : 'Watch créé !');
                location.reload();
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion');
            } finally {
                this.saving = false;
            }
        },

        async toggleStatus(id, action) {
            try {
                const res = await fetch(`/watchtrend/watches/${id}/${action}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    const labels = { pause: 'Watch mis en pause', resume: 'Watch repris', archive: 'Watch archivé' };
                    WTModal.toast('success', labels[action] || 'Statut mis à jour');
                    location.reload();
                }
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion');
            }
        },

        async deleteWatch(id, name) {
            const result = await WTModal.delete({ itemName: name });
            if (!result.isConfirmed) return;
            try {
                const res = await fetch(`/watchtrend/watches/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    WTModal.toast('success', 'Watch supprimé !');
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
