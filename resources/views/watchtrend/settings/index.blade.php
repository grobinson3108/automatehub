@extends('watchtrend.layouts.app')

@section('title', 'Paramètres')
@section('page-title', 'Paramètres')

@section('breadcrumb')
    <li class="breadcrumb-item active">Paramètres</li>
@endsection

@section('content')
<div x-data="settingsManager()" x-init="init()">

    <div class="row g-4">
        <div class="col-12 col-xl-8">

            {{-- Block 1: Plan & Abonnement --}}
            <div class="block block-rounded mb-4">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-crown me-2 text-warning"></i>Plan & Abonnement
                    </h3>
                </div>
                <div class="block-content">
                    <div class="d-flex align-items-center justify-content-between p-3 bg-body-light rounded border">
                        <div>
                            <div class="fw-semibold text-muted">Plan actuel</div>
                            <div class="text-muted fst-italic">Aucun plan actif</div>
                        </div>
                        <button class="btn btn-alt-secondary" disabled title="Bientôt disponible">
                            <i class="fa fa-external-link-alt me-1"></i>Gérer mon plan
                        </button>
                    </div>
                    <p class="text-muted small mt-3 mb-0">
                        <i class="fa fa-info-circle me-1"></i>
                        La gestion des plans sera disponible prochainement.
                    </p>
                </div>
            </div>

            {{-- Block 2: Mode IA --}}
            <div class="block block-rounded mb-4">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-robot me-2 text-primary"></i>Mode IA
                    </h3>
                </div>
                <div class="block-content">
                    <div class="row g-3">
                        {{-- BYOK Card --}}
                        <div class="col-md-6">
                            <div class="p-3 rounded border h-100"
                                :class="settings.ai_mode === 'byok' ? 'border-primary bg-primary-subtle' : 'bg-body-light'">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fa fa-key fa-lg text-primary"></i>
                                    <div class="fw-semibold">BYOK</div>
                                    <span class="badge bg-primary ms-auto small">Apportez votre clé</span>
                                </div>
                                <p class="text-muted small mb-0">
                                    Utilisez votre propre clé API OpenAI. Vous maîtrisez vos coûts directement.
                                </p>
                            </div>
                        </div>
                        {{-- Managed Card --}}
                        <div class="col-md-6">
                            <div class="p-3 rounded border h-100"
                                :class="settings.ai_mode === 'managed' ? 'border-success bg-success-subtle' : 'bg-body-light'">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fa fa-bolt fa-lg text-success"></i>
                                    <div class="fw-semibold">Crédits AutomateHub</div>
                                    <span class="badge bg-secondary ms-auto small">Bientôt</span>
                                </div>
                                <p class="text-muted small mb-0">
                                    Utilisez les crédits inclus dans votre plan AutomateHub. Simple et sans configuration.
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted small mt-3 mb-0">
                        <i class="fa fa-info-circle me-1"></i>
                        Le changement de mode sera disponible dans une prochaine version.
                    </p>
                </div>
            </div>

            {{-- Block 3: Clé API OpenAI --}}
            <div class="block block-rounded mb-4">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-key me-2 text-success"></i>Clé API OpenAI (BYOK)
                    </h3>
                </div>
                <div class="block-content">

                    {{-- Current key status --}}
                    <template x-if="hasApiKey">
                        <div class="d-flex align-items-center gap-3 p-3 bg-success-subtle border border-success rounded mb-3">
                            <i class="fa fa-check-circle text-success fa-lg"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-success">Clé API configurée</div>
                                <div class="text-muted small font-monospace" x-text="apiKeyMasked"></div>
                            </div>
                            <button class="btn btn-sm btn-alt-danger" @click="deleteApiKey()" :disabled="savingApiKey">
                                <i class="fa fa-trash me-1"></i>Supprimer
                            </button>
                        </div>
                    </template>

                    {{-- API Key input --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <span x-text="hasApiKey ? 'Remplacer la clé API' : 'Entrer votre clé API'"></span>
                        </label>
                        <div class="input-group">
                            <input :type="showApiKey ? 'text' : 'password'"
                                class="form-control font-monospace"
                                x-model="apiKey"
                                placeholder="sk-proj-...">
                            <button class="btn btn-alt-secondary" type="button" @click="showApiKey = !showApiKey"
                                :title="showApiKey ? 'Masquer' : 'Afficher'">
                                <i :class="showApiKey ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                            </button>
                        </div>
                        <div class="form-text">
                            <i class="fa fa-shield-alt me-1 text-success"></i>
                            Votre clé est chiffrée et stockée de manière sécurisée. Elle ne sera jamais affichée en clair.
                        </div>
                    </div>

                    <button class="btn btn-primary" @click="saveApiKey()"
                        :disabled="savingApiKey || !apiKey.trim()">
                        <span x-show="savingApiKey" class="spinner-border spinner-border-sm me-1" role="status"></span>
                        <i x-show="!savingApiKey" class="fa fa-save me-1"></i>
                        <span x-text="savingApiKey ? 'Enregistrement...' : 'Sauvegarder la clé'"></span>
                    </button>

                </div>
            </div>

            {{-- Block 4: Préférences --}}
            <div class="block block-rounded mb-4">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-sliders me-2 text-info"></i>Préférences
                    </h3>
                </div>
                <div class="block-content">
                    <div class="row g-3">

                        {{-- Language --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Langue des résumés</label>
                            <select class="form-select" x-model="settings.summary_language">
                                <option value="fr">Français</option>
                                <option value="en">English</option>
                            </select>
                            <div class="form-text">Langue utilisée pour les résumés générés par l'IA</div>
                        </div>

                        {{-- Items per page --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Éléments par page</label>
                            <input type="number" class="form-control"
                                x-model.number="settings.items_per_page"
                                min="5" max="100" step="5">
                            <div class="form-text">Entre 5 et 100 éléments par page</div>
                        </div>

                    </div>

                    <div class="mt-4">
                        <button class="btn btn-primary" @click="savePreferences()" :disabled="savingPreferences">
                            <span x-show="savingPreferences" class="spinner-border spinner-border-sm me-1" role="status"></span>
                            <i x-show="!savingPreferences" class="fa fa-save me-1"></i>
                            <span x-text="savingPreferences ? 'Enregistrement...' : 'Sauvegarder les préférences'"></span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Block 5: Zone de danger --}}
            <div class="block block-rounded border-danger">
                <div class="block-header block-header-default bg-danger-subtle">
                    <h3 class="block-title text-danger">
                        <i class="fa fa-triangle-exclamation me-2"></i>Zone de danger
                    </h3>
                </div>
                <div class="block-content">
                    <div class="d-flex align-items-center justify-content-between p-3 bg-danger-subtle rounded border border-danger">
                        <div>
                            <div class="fw-semibold text-danger">Supprimer toutes mes données WatchTrend</div>
                            <div class="text-muted small">Cette action est irréversible. Toutes vos watches, sources, intérêts et pain points seront supprimés.</div>
                        </div>
                        <button class="btn btn-danger ms-3" disabled title="Bientôt disponible">
                            <i class="fa fa-trash me-1"></i>Supprimer tout
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar Info --}}
        <div class="col-12 col-xl-4">
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-circle-info me-2 text-info"></i>Informations
                    </h3>
                </div>
                <div class="block-content">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <div class="text-muted small fw-semibold text-uppercase mb-1">Version</div>
                            <div>WatchTrend Beta</div>
                        </li>
                        <li class="mb-3">
                            <div class="text-muted small fw-semibold text-uppercase mb-1">Mode IA actuel</div>
                            <span class="badge" :class="settings.ai_mode === 'byok' ? 'bg-primary' : 'bg-success'"
                                x-text="settings.ai_mode === 'byok' ? 'BYOK (Clé personnelle)' : 'Crédits AutomateHub'">
                            </span>
                        </li>
                        <li class="mb-3">
                            <div class="text-muted small fw-semibold text-uppercase mb-1">Statut clé API</div>
                            <span x-show="hasApiKey" class="badge bg-success">
                                <i class="fa fa-check me-1"></i>Configurée
                            </span>
                            <span x-show="!hasApiKey" class="badge bg-warning text-dark">
                                <i class="fa fa-exclamation me-1"></i>Non configurée
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="block block-rounded mt-4">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        <i class="fa fa-question-circle me-2 text-primary"></i>Aide
                    </h3>
                </div>
                <div class="block-content">
                    <p class="text-muted small mb-2">
                        <strong>BYOK</strong> : Bring Your Own Key. Vous utilisez votre propre clé OpenAI et payez directement OpenAI pour l'usage.
                    </p>
                    <p class="text-muted small mb-2">
                        Pour obtenir une clé API OpenAI, rendez-vous sur <span class="font-monospace">platform.openai.com</span>.
                    </p>
                    <p class="text-muted small mb-0">
                        <i class="fa fa-shield-alt me-1 text-success"></i>
                        Votre clé API est chiffrée en AES-256 avant stockage.
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function settingsManager() {
    return {
        settings: @json($settings),
        hasApiKey: {{ $hasApiKey ? 'true' : 'false' }},
        apiKeyMasked: '{{ $apiKeyMasked ?? '' }}',
        apiKey: '',
        showApiKey: false,
        savingPreferences: false,
        savingApiKey: false,

        init() {
            // Ensure defaults
            if (!this.settings.summary_language) this.settings.summary_language = 'fr';
            if (!this.settings.items_per_page) this.settings.items_per_page = 20;
            if (!this.settings.ai_mode) this.settings.ai_mode = 'byok';
        },

        async savePreferences() {
            this.savingPreferences = true;
            try {
                const res = await fetch('/watchtrend/settings/preferences', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        summary_language: this.settings.summary_language,
                        items_per_page: this.settings.items_per_page
                    })
                });
                const data = await res.json();
                if (res.ok) {
                    WTModal.toast('success', 'Préférences sauvegardées !');
                } else {
                    WTModal.toast('error', data.message || 'Erreur lors de la sauvegarde');
                }
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion');
            } finally {
                this.savingPreferences = false;
            }
        },

        async saveApiKey() {
            if (!this.apiKey.trim()) return;
            this.savingApiKey = true;
            try {
                const res = await fetch('/watchtrend/settings/api-key', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ api_key: this.apiKey })
                });
                const data = await res.json();
                if (res.ok) {
                    this.hasApiKey = true;
                    this.apiKeyMasked = data.masked || this.apiKey.substring(0, 7) + '...' + this.apiKey.slice(-4);
                    this.apiKey = '';
                    this.showApiKey = false;
                    WTModal.toast('success', 'Clé API sauvegardée !');
                } else {
                    WTModal.toast('error', data.message || 'Erreur lors de la sauvegarde');
                }
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion');
            } finally {
                this.savingApiKey = false;
            }
        },

        async deleteApiKey() {
            const result = await WTModal.confirm({
                title: 'Supprimer la clé API ?',
                text: 'Les fonctionnalités IA ne seront plus disponibles sans clé API.'
            });
            if (!result.isConfirmed) return;
            this.savingApiKey = true;
            try {
                const res = await fetch('/watchtrend/settings/api-key', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    this.hasApiKey = false;
                    this.apiKeyMasked = '';
                    WTModal.toast('success', 'Clé API supprimée');
                }
            } catch (e) {
                WTModal.toast('error', 'Erreur de connexion');
            } finally {
                this.savingApiKey = false;
            }
        }
    }
}
</script>
@endpush
