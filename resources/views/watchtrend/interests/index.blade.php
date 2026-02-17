@extends('watchtrend.layouts.app')

@section('title', "Centres d'intérêt")
@section('page-title', "Centres d'intérêt")

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('watchtrend.watches.index') }}">Mes Watches</a></li>
    <li class="breadcrumb-item"><a href="{{ route('watchtrend.watches.show', $watch) }}">{{ $watch->name }}</a></li>
    <li class="breadcrumb-item active">Intérêts</li>
@endsection

@section('content')
<div x-data="interestsManager()" x-init="init()">

    {{-- Watch Selector --}}
    <div class="mb-4">
        <label class="form-label text-muted small fw-semibold text-uppercase mb-1">Watch actif</label>
        <select class="form-select w-auto" @change="switchWatch($event.target.value)">
            @foreach($watches as $w)
                <option value="{{ $w->id }}" {{ $w->id === $watch->id ? 'selected' : '' }}>
                    {{ $w->icon ?? '📊' }} {{ $w->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Header --}}
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <i class="fa fa-bullseye me-2 text-warning"></i>Centres d'intérêt
                <span class="badge bg-secondary ms-2" x-text="interests.length + ' intérêt' + (interests.length > 1 ? 's' : '')"></span>
            </h3>
            <div class="block-options">
                <button class="btn btn-sm btn-alt-primary" @click="openAdd()">
                    <i class="fa fa-plus me-1"></i> Ajouter un intérêt
                </button>
            </div>
        </div>
        <div class="block-content">

            {{-- Interest Cards --}}
            <template x-if="interests.length > 0">
                <div class="row g-3">
                    <template x-for="interest in interests" :key="interest.id">
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="block block-rounded border mb-0 h-100">
                                <div class="block-header block-header-default py-2">
                                    <h3 class="block-title fs-6" x-text="interest.name"></h3>
                                    <div class="block-options">
                                        <span class="badge"
                                            :class="{
                                                'bg-danger': interest.priority === 'high',
                                                'bg-warning text-dark': interest.priority === 'medium',
                                                'bg-info': interest.priority === 'low'
                                            }"
                                            x-text="interest.priority === 'high' ? 'Haute' : (interest.priority === 'medium' ? 'Moyenne' : 'Basse')">
                                        </span>
                                    </div>
                                </div>
                                <div class="block-content py-2">
                                    {{-- Keywords --}}
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <template x-if="interest.keywords && interest.keywords.length > 0">
                                            <template x-for="(kw, i) in interest.keywords" :key="i">
                                                <span class="badge bg-primary-subtle text-primary" x-text="kw"></span>
                                            </template>
                                        </template>
                                        <template x-if="!interest.keywords || interest.keywords.length === 0">
                                            <span class="text-muted small fst-italic">Aucun mot-clé</span>
                                        </template>
                                    </div>
                                    {{-- Context Description --}}
                                    <template x-if="interest.context_description">
                                        <p class="text-muted small mb-0"
                                            style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;"
                                            x-text="interest.context_description"></p>
                                    </template>
                                </div>
                                <div class="block-content block-content-full bg-body-light border-top py-2">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-alt-primary" @click="openEdit(interest)">
                                            <i class="fa fa-pen me-1"></i>Éditer
                                        </button>
                                        <button class="btn btn-sm btn-alt-danger" @click="deleteInterest(interest.id, interest.name)">
                                            <i class="fa fa-trash me-1"></i>Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="interests.length === 0">
                <div class="py-5 text-center">
                    <i class="fa fa-bullseye fa-3x text-muted opacity-50 mb-3"></i>
                    <h5 class="text-muted">Aucun centre d'intérêt</h5>
                    <p class="text-muted mb-4">Ajoutez des centres d'intérêt pour affiner la collecte de ce watch.</p>
                    <button class="btn btn-primary" @click="openAdd()">
                        <i class="fa fa-plus me-1"></i> Ajouter un intérêt
                    </button>
                </div>
            </template>

        </div>
    </div>

    {{-- Interest Modal --}}
    <div class="modal fade" id="interestModal" tabindex="-1" aria-labelledby="interestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="interestModalLabel">
                        <span x-text="editingId ? 'Modifier l\'intérêt' : 'Nouvel intérêt'"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Name --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" :class="{'is-invalid': errors.name}"
                                x-model="formData.name" placeholder="Ex: Outils IA no-code">
                            <div class="invalid-feedback" x-text="errors.name ? errors.name[0] : ''"></div>
                        </div>

                        {{-- Priority --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Priorité</label>
                            <select class="form-select" :class="{'is-invalid': errors.priority}" x-model="formData.priority">
                                <option value="high">Haute</option>
                                <option value="medium">Moyenne</option>
                                <option value="low">Basse</option>
                            </select>
                            <div class="invalid-feedback" x-text="errors.priority ? errors.priority[0] : ''"></div>
                        </div>

                        {{-- Keywords Tag Input --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mots-clés</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" x-model="newKeyword"
                                    placeholder="Ajouter un mot-clé..."
                                    @keydown.enter.prevent="addKeyword()"
                                    @keydown.comma.prevent="addKeyword()">
                                <button class="btn btn-alt-primary" type="button" @click="addKeyword()">
                                    <i class="fa fa-plus me-1"></i>Ajouter
                                </button>
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                <template x-for="(kw, index) in formData.keywords" :key="index">
                                    <span class="badge bg-primary-subtle text-primary d-flex align-items-center gap-1 py-2 px-2">
                                        <span x-text="kw"></span>
                                        <button type="button" class="btn-close btn-close-sm p-0" style="font-size:0.6rem;"
                                            @click="removeKeyword(index)" aria-label="Supprimer"></button>
                                    </span>
                                </template>
                                <template x-if="formData.keywords.length === 0">
                                    <span class="text-muted small fst-italic">Aucun mot-clé ajouté</span>
                                </template>
                            </div>
                            <div x-show="errors.keywords" class="text-danger small mt-1" x-text="errors.keywords ? errors.keywords[0] : ''"></div>
                        </div>

                        {{-- Context Description --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description contextuelle</label>
                            <textarea class="form-control" :class="{'is-invalid': errors.context_description}"
                                x-model="formData.context_description" rows="3"
                                placeholder="Décrivez le contexte de cet intérêt pour affiner la collecte..."></textarea>
                            <div class="invalid-feedback" x-text="errors.context_description ? errors.context_description[0] : ''"></div>
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
function interestsManager() {
    return {
        interests: @json($interests),
        selectedWatchId: {{ $watch->id }},
        formData: { name: '', keywords: [], priority: 'medium', context_description: '' },
        newKeyword: '',
        editingId: null,
        saving: false,
        errors: {},

        init() {
            // Normalize keywords to array
            this.interests = this.interests.map(i => ({
                ...i,
                keywords: Array.isArray(i.keywords) ? i.keywords : (i.keywords ? JSON.parse(i.keywords) : [])
            }));
        },

        switchWatch(watchId) {
            window.location = `/watchtrend/watches/${watchId}/interests`;
        },

        resetForm() {
            this.formData = { name: '', keywords: [], priority: 'medium', context_description: '' };
            this.newKeyword = '';
            this.editingId = null;
            this.errors = {};
        },

        addKeyword() {
            const kw = this.newKeyword.trim().replace(/,$/, '');
            if (kw && !this.formData.keywords.includes(kw)) {
                this.formData.keywords.push(kw);
            }
            this.newKeyword = '';
        },

        removeKeyword(index) {
            this.formData.keywords.splice(index, 1);
        },

        openAdd() {
            this.resetForm();
            new bootstrap.Modal(document.getElementById('interestModal')).show();
        },

        openEdit(interest) {
            this.editingId = interest.id;
            this.formData = {
                name: interest.name,
                keywords: Array.isArray(interest.keywords) ? [...interest.keywords] : [],
                priority: interest.priority || 'medium',
                context_description: interest.context_description || ''
            };
            this.newKeyword = '';
            this.errors = {};
            new bootstrap.Modal(document.getElementById('interestModal')).show();
        },

        async save() {
            this.saving = true;
            this.errors = {};
            try {
                const watchId = this.selectedWatchId;
                const url = this.editingId
                    ? `/watchtrend/watches/${watchId}/interests/${this.editingId}`
                    : `/watchtrend/watches/${watchId}/interests`;
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
                bootstrap.Modal.getInstance(document.getElementById('interestModal')).hide();
                WTModal.toast('success', this.editingId ? 'Intérêt modifié !' : 'Intérêt créé !');
                location.reload();
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion');
            } finally {
                this.saving = false;
            }
        },

        async deleteInterest(id, name) {
            const result = await WTModal.delete({ itemName: name });
            if (!result.isConfirmed) return;
            try {
                const res = await fetch(`/watchtrend/watches/${this.selectedWatchId}/interests/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    WTModal.toast('success', 'Intérêt supprimé !');
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
