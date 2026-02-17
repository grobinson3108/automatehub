@extends('watchtrend.layouts.app')

@section('title', 'Sources')
@section('page-title', 'Sources')

@section('breadcrumb')
    <li class="breadcrumb-item active">Sources</li>
@endsection

@section('content')
<div x-data="sourcesManager()" x-init="init()">

    {{-- Header --}}
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">
                <i class="fa fa-satellite-dish me-2 text-info"></i>Sources
                <span class="badge bg-secondary ms-2" x-text="filteredSources().length + ' source' + (filteredSources().length > 1 ? 's' : '')"></span>
            </h3>
            <div class="block-options">
                <button class="btn btn-sm btn-alt-primary" @click="openAdd()">
                    <i class="fa fa-plus me-1"></i> Ajouter une source
                </button>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="block-content pb-0 border-bottom">
            <ul class="nav nav-tabs nav-tabs-block">
                <li class="nav-item">
                    <button class="nav-link" :class="{'active': filterType === 'all'}" @click="filterType = 'all'">
                        <i class="fa fa-list me-1"></i>Toutes
                        <span class="badge bg-secondary ms-1 small" x-text="sources.length"></span>
                    </button>
                </li>
                <template x-for="type in sourceTypes" :key="type.value">
                    <li class="nav-item">
                        <button class="nav-link" :class="{'active': filterType === type.value}"
                            @click="filterType = type.value">
                            <i :class="type.icon"></i>
                            <span class="ms-1" x-text="type.label"></span>
                            <span class="badge bg-secondary ms-1 small"
                                x-text="sources.filter(s => s.type === type.value).length"></span>
                        </button>
                    </li>
                </template>
            </ul>
        </div>

        <div class="block-content">

            {{-- Source Grid --}}
            <template x-if="filteredSources().length > 0">
                <div class="row g-3">
                    <template x-for="source in filteredSources()" :key="source.id">
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="block block-rounded border mb-0 h-100">
                                <div class="block-header block-header-default py-2">
                                    <h3 class="block-title fs-6 text-truncate" style="max-width:180px;">
                                        <i :class="getTypeIcon(source.type)" class="me-2"></i>
                                        <span x-text="source.name"></span>
                                    </h3>
                                    <div class="block-options d-flex align-items-center gap-1">
                                        <span class="badge small"
                                            :class="{
                                                'bg-success': source.status === 'active',
                                                'bg-warning text-dark': source.status === 'paused',
                                                'bg-danger': source.status === 'error',
                                                'bg-secondary': !['active','paused','error'].includes(source.status)
                                            }"
                                            x-text="source.status === 'active' ? 'Active' : (source.status === 'paused' ? 'Pause' : (source.status === 'error' ? 'Erreur' : source.status))">
                                        </span>
                                    </div>
                                </div>
                                <div class="block-content py-2">
                                    {{-- Watch badge --}}
                                    <template x-if="source.watch_name">
                                        <div class="mb-2">
                                            <span class="badge bg-primary-subtle text-primary small">
                                                <i class="fa fa-binoculars me-1"></i>
                                                <span x-text="source.watch_name"></span>
                                            </span>
                                        </div>
                                    </template>
                                    {{-- Config summary --}}
                                    <div class="text-muted small mb-2" x-text="getConfigSummary(source)"></div>
                                    {{-- Stats --}}
                                    <div class="d-flex gap-3 text-muted small">
                                        <span>
                                            <i class="fa fa-box me-1"></i>
                                            <span x-text="(source.items_count || 0) + ' éléments'"></span>
                                        </span>
                                        <template x-if="source.last_collected_at">
                                            <span>
                                                <i class="fa fa-clock me-1"></i>
                                                <span x-text="formatDate(source.last_collected_at)"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                                <div class="block-content block-content-full bg-body-light border-top py-2">
                                    <div class="d-flex flex-wrap gap-1">
                                        <button class="btn btn-sm"
                                            :class="source.status === 'active' ? 'btn-alt-warning' : 'btn-alt-success'"
                                            @click="toggleSource(source.id, source.status)"
                                            :title="source.status === 'active' ? 'Mettre en pause' : 'Activer'">
                                            <i :class="source.status === 'active' ? 'fa fa-pause' : 'fa fa-play'"></i>
                                        </button>
                                        <button class="btn btn-sm btn-alt-info" @click="testSource(source.id)" title="Tester">
                                            <i class="fa fa-vial"></i>
                                        </button>
                                        <button class="btn btn-sm btn-alt-primary" @click="openEdit(source)">
                                            <i class="fa fa-pen me-1"></i>Éditer
                                        </button>
                                        <button class="btn btn-sm btn-alt-danger" @click="deleteSource(source.id, source.name)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="filteredSources().length === 0">
                <div class="py-5 text-center">
                    <i class="fa fa-satellite-dish fa-3x text-muted opacity-50 mb-3"></i>
                    <h5 class="text-muted">Aucune source</h5>
                    <p class="text-muted mb-4">Ajoutez des sources pour commencer à collecter des données.</p>
                    <button class="btn btn-primary" @click="openAdd()">
                        <i class="fa fa-plus me-1"></i> Ajouter une source
                    </button>
                </div>
            </template>

        </div>
    </div>

    {{-- Source Modal --}}
    <div class="modal fade" id="sourceModal" tabindex="-1" aria-labelledby="sourceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sourceModalLabel">
                        <span x-text="editingId ? 'Modifier la source' : 'Nouvelle source'"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Watch selector --}}
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

                        {{-- Name (optional) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom <span class="text-muted small">(optionnel)</span></label>
                            <input type="text" class="form-control" :class="{'is-invalid': errors.name}"
                                x-model="formData.name" placeholder="Généré automatiquement si vide">
                            <div class="invalid-feedback" x-text="errors.name ? errors.name[0] : ''"></div>
                        </div>

                        {{-- Type selector --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Type de source <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-2">
                                <template x-for="type in sourceTypes" :key="type.value">
                                    <button type="button" class="btn"
                                        :class="formData.type === type.value ? 'btn-primary' : 'btn-alt-secondary'"
                                        @click="selectType(type.value)">
                                        <i :class="type.icon" class="me-1"></i>
                                        <span x-text="type.label"></span>
                                    </button>
                                </template>
                            </div>
                            <div x-show="errors.type" class="text-danger small mt-1" x-text="errors.type ? errors.type[0] : ''"></div>
                        </div>

                        {{-- Dynamic config fields --}}
                        <div class="col-12" x-show="formData.type !== ''">
                            <div x-show="formData.type === 'youtube'">
                                <label class="form-label fw-semibold">ID de la chaîne YouTube</label>
                                <input type="text" class="form-control" :class="{'is-invalid': errors['config.channel_id']}"
                                    x-model="formData.config.channel_id" placeholder="UCxxxxxx">
                                <div class="form-text">L'ID se trouve dans l'URL de la chaîne YouTube</div>
                                <div class="invalid-feedback" x-text="errors['config.channel_id'] ? errors['config.channel_id'][0] : ''"></div>
                            </div>

                            <div x-show="formData.type === 'reddit'">
                                <label class="form-label fw-semibold">Subreddit</label>
                                <input type="text" class="form-control" :class="{'is-invalid': errors['config.subreddit']}"
                                    x-model="formData.config.subreddit" placeholder="r/programming">
                                <div class="form-text">Ex: r/MachineLearning, r/webdev</div>
                                <div class="invalid-feedback" x-text="errors['config.subreddit'] ? errors['config.subreddit'][0] : ''"></div>
                            </div>

                            <div x-show="formData.type === 'rss'">
                                <label class="form-label fw-semibold">URL du flux RSS</label>
                                <input type="url" class="form-control" :class="{'is-invalid': errors['config.feed_url']}"
                                    x-model="formData.config.feed_url" placeholder="https://example.com/feed.xml">
                                <div class="invalid-feedback" x-text="errors['config.feed_url'] ? errors['config.feed_url'][0] : ''"></div>
                            </div>

                            <div x-show="formData.type === 'hackernews'">
                                <label class="form-label fw-semibold">Requête de recherche</label>
                                <input type="text" class="form-control" :class="{'is-invalid': errors['config.query']}"
                                    x-model="formData.config.query" placeholder="artificial intelligence, GPT">
                                <div class="form-text">Mots-clés à surveiller sur Hacker News</div>
                                <div class="invalid-feedback" x-text="errors['config.query'] ? errors['config.query'][0] : ''"></div>
                            </div>

                            <div x-show="formData.type === 'github'">
                                <label class="form-label fw-semibold">Repository</label>
                                <input type="text" class="form-control" :class="{'is-invalid': errors['config.repo']}"
                                    x-model="formData.config.repo" placeholder="owner/repo">
                                <div class="form-text">Ex: openai/gpt-4, vercel/next.js</div>
                                <div class="invalid-feedback" x-text="errors['config.repo'] ? errors['config.repo'][0] : ''"></div>
                            </div>

                            <div x-show="formData.type === 'twitter'">
                                <label class="form-label fw-semibold">Nom d'utilisateur</label>
                                <input type="text" class="form-control" :class="{'is-invalid': errors['config.username']}"
                                    x-model="formData.config.username" placeholder="@username">
                                <div class="invalid-feedback" x-text="errors['config.username'] ? errors['config.username'][0] : ''"></div>
                            </div>
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
function sourcesManager() {
    return {
        sources: @json($sources),
        filterType: 'all',
        formData: { watch_id: '', type: '', name: '', config: {} },
        editingId: null,
        saving: false,
        errors: {},
        sourceTypes: [
            { value: 'youtube',    label: 'YouTube',      icon: 'fab fa-youtube text-danger' },
            { value: 'reddit',     label: 'Reddit',       icon: 'fab fa-reddit' },
            { value: 'rss',        label: 'RSS',          icon: 'fa fa-rss text-warning' },
            { value: 'hackernews', label: 'Hacker News',  icon: 'fab fa-hacker-news text-warning' },
            { value: 'github',     label: 'GitHub',       icon: 'fab fa-github text-dark' },
            { value: 'twitter',    label: 'Twitter',      icon: 'fab fa-twitter text-info' },
        ],

        init() {
            // Normalize config to object
            this.sources = this.sources.map(s => ({
                ...s,
                config: (typeof s.config === 'string') ? JSON.parse(s.config || '{}') : (s.config || {})
            }));
        },

        filteredSources() {
            if (this.filterType === 'all') return this.sources;
            return this.sources.filter(s => s.type === this.filterType);
        },

        getTypeIcon(type) {
            const found = this.sourceTypes.find(t => t.value === type);
            return found ? found.icon : 'fa fa-globe text-muted';
        },

        getConfigSummary(source) {
            const cfg = source.config || {};
            switch (source.type) {
                case 'youtube':    return cfg.channel_id ? `Chaîne: ${cfg.channel_id}` : '';
                case 'reddit':     return cfg.subreddit ? `${cfg.subreddit}` : '';
                case 'rss':        return cfg.feed_url ? cfg.feed_url : '';
                case 'hackernews': return cfg.query ? `Recherche: ${cfg.query}` : '';
                case 'github':     return cfg.repo ? cfg.repo : '';
                case 'twitter':    return cfg.username ? `@${cfg.username.replace('@','')}` : '';
                default:           return '';
            }
        },

        formatDate(dateStr) {
            if (!dateStr) return '';
            return new Date(dateStr).toLocaleDateString('fr-FR', { day:'2-digit', month:'2-digit', year:'numeric' });
        },

        selectType(type) {
            this.formData.type = type;
            this.formData.config = {};
        },

        resetForm() {
            this.formData = { watch_id: '', type: '', name: '', config: {} };
            this.editingId = null;
            this.errors = {};
        },

        openAdd() {
            this.resetForm();
            new bootstrap.Modal(document.getElementById('sourceModal')).show();
        },

        openEdit(source) {
            this.editingId = source.id;
            this.formData = {
                watch_id: source.watch_id || '',
                type: source.type || '',
                name: source.name || '',
                config: { ...(source.config || {}) }
            };
            this.errors = {};
            new bootstrap.Modal(document.getElementById('sourceModal')).show();
        },

        async save() {
            this.saving = true;
            this.errors = {};
            try {
                const url = this.editingId ? `/watchtrend/sources/${this.editingId}` : '/watchtrend/sources';
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
                bootstrap.Modal.getInstance(document.getElementById('sourceModal')).hide();
                WTModal.toast('success', this.editingId ? 'Source modifiée !' : 'Source créée !');
                location.reload();
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion');
            } finally {
                this.saving = false;
            }
        },

        async toggleSource(id, currentStatus) {
            const action = currentStatus === 'active' ? 'pause' : 'resume';
            // Use toggle endpoint
            try {
                const res = await fetch(`/watchtrend/sources/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    WTModal.toast('success', currentStatus === 'active' ? 'Source mise en pause' : 'Source activée');
                    location.reload();
                }
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion');
            }
        },

        async testSource(id) {
            WTModal.toast('info', 'Test en cours...');
            try {
                const res = await fetch(`/watchtrend/sources/${id}/test`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (res.ok) {
                    WTModal.toast('success', data.message || 'Test réussi !');
                } else {
                    WTModal.toast('error', data.message || 'Test échoué');
                }
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion');
            }
        },

        async deleteSource(id, name) {
            const result = await WTModal.delete({ itemName: name });
            if (!result.isConfirmed) return;
            try {
                const res = await fetch(`/watchtrend/sources/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    WTModal.toast('success', 'Source supprimée !');
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
