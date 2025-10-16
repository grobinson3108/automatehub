<!-- Bannière Cookies RGPD -->
<div id="cookie-banner" class="cookie-banner d-none">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <i class="fas fa-cookie-bite text-primary me-3 fs-4"></i>
                    <div>
                        <h6 class="mb-1">🍪 Gestion des cookies</h6>
                        <p class="mb-0 small text-muted">
                            Nous utilisons des cookies pour améliorer votre expérience. En continuant, vous acceptez notre 
                            <a href="{{ route('privacy-policy') }}" class="text-primary">politique de confidentialité</a>.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary btn-sm me-2" onclick="cookieManager.showPreferences()">
                        <i class="fas fa-cog"></i> Préférences
                    </button>
                    <button type="button" class="btn btn-success btn-sm me-2" onclick="cookieManager.acceptAll()">
                        <i class="fas fa-check"></i> Tout accepter
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="cookieManager.acceptEssential()">
                        Essentiels seulement
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Préférences Cookies -->
<div class="modal fade" id="cookiePreferencesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-cookie-bite text-primary me-2"></i>
                    Préférences des cookies
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">
                    Personnalisez vos préférences cookies. Vous pouvez modifier ces paramètres à tout moment.
                </p>

                <!-- Cookies Essentiels -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">
                                    <i class="fas fa-shield-alt text-success me-2"></i>
                                    Cookies essentiels
                                </h6>
                                <p class="card-text small text-muted mb-0">
                                    Nécessaires au fonctionnement du site (authentification, sécurité, panier).
                                </p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="essential-cookies" checked disabled>
                                <label class="form-check-label text-muted small" for="essential-cookies">
                                    Toujours actif
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cookies de Performance -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">
                                    <i class="fas fa-chart-line text-info me-2"></i>
                                    Cookies de performance
                                </h6>
                                <p class="card-text small text-muted mb-0">
                                    Nous aident à analyser l'utilisation du site pour l'améliorer (Google Analytics).
                                </p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="analytics-cookies">
                                <label class="form-check-label" for="analytics-cookies"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cookies Marketing -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">
                                    <i class="fas fa-bullhorn text-warning me-2"></i>
                                    Cookies marketing
                                </h6>
                                <p class="card-text small text-muted mb-0">
                                    Permettent de personnaliser les publicités et mesurer l'efficacité des campagnes.
                                </p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="marketing-cookies">
                                <label class="form-check-label" for="marketing-cookies"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cookies de Préférences -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">
                                    <i class="fas fa-user-cog text-primary me-2"></i>
                                    Cookies de préférences
                                </h6>
                                <p class="card-text small text-muted mb-0">
                                    Mémorisent vos choix (langue, thème, préférences d'interface).
                                </p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="preferences-cookies">
                                <label class="form-check-label" for="preferences-cookies"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Annuler
                </button>
                <button type="button" class="btn btn-primary" onclick="cookieManager.savePreferences()">
                    <i class="fas fa-save me-1"></i>
                    Sauvegarder mes préférences
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.cookie-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 15px 0;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
    z-index: 9999;
    border-top: 3px solid #3498db;
}

.cookie-banner .btn {
    font-size: 0.875rem;
    padding: 8px 16px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.cookie-banner .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.cookie-banner a {
    color: #74b9ff;
    text-decoration: none;
}

.cookie-banner a:hover {
    color: #0984e3;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .cookie-banner .col-lg-4 {
        margin-top: 15px;
        text-align: center !important;
    }
    
    .cookie-banner .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .cookie-banner .btn {
        margin: 2px 0 !important;
        width: 100%;
    }
}

.form-switch .form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.card {
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #dee2e6;
}
</style>