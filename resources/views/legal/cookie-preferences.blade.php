@extends('layouts.app')

@section('title', 'Préférences Cookies - AutomateHub')
@section('description', 'Gérez vos préférences de cookies pour AutomateHub. Contrôlez quelles données vous partagez avec nous.')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <i class="fas fa-cookie-bite text-primary fs-1 mb-3"></i>
                <h1 class="h2 mb-3">Gestion des Cookies</h1>
                <p class="text-muted">
                    Contrôlez vos préférences de cookies et protégez votre vie privée.
                </p>
            </div>

            <!-- Statut actuel -->
            <div class="card shadow-sm mb-4" id="current-status">
                <div class="card-body text-center">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Statut actuel
                    </h5>
                    <p class="mb-3" id="consent-status">Chargement de vos préférences...</p>
                    <button class="btn btn-outline-primary btn-sm" onclick="showCookiePreferences()">
                        <i class="fas fa-cog me-1"></i>
                        Modifier mes préférences
                    </button>
                </div>
            </div>

            <!-- Informations détaillées -->
            <div class="row">
                <!-- Cookies Essentiels -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-shield-alt text-success me-2"></i>
                                Cookies Essentiels
                            </h6>
                            <p class="card-text small text-muted">
                                Ces cookies sont nécessaires au fonctionnement du site et ne peuvent pas être désactivés.
                            </p>
                            <div class="mt-3">
                                <strong>Exemples :</strong>
                                <ul class="small">
                                    <li>Session d'authentification</li>
                                    <li>Panier d'achat</li>
                                    <li>Préférences de sécurité</li>
                                    <li>Protection CSRF</li>
                                </ul>
                            </div>
                            <span class="badge bg-success">Toujours actif</span>
                        </div>
                    </div>
                </div>

                <!-- Cookies de Performance -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-chart-line text-info me-2"></i>
                                Cookies de Performance
                            </h6>
                            <p class="card-text small text-muted">
                                Ces cookies nous aident à comprendre comment vous utilisez notre site.
                            </p>
                            <div class="mt-3">
                                <strong>Exemples :</strong>
                                <ul class="small">
                                    <li>Google Analytics</li>
                                    <li>Statistiques de visite</li>
                                    <li>Pages populaires</li>
                                    <li>Temps de chargement</li>
                                </ul>
                            </div>
                            <span class="badge bg-info" id="analytics-status">Vérifié...</span>
                        </div>
                    </div>
                </div>

                <!-- Cookies Marketing -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-bullhorn text-warning me-2"></i>
                                Cookies Marketing
                            </h6>
                            <p class="card-text small text-muted">
                                Utilisés pour personnaliser les publicités et mesurer leur efficacité.
                            </p>
                            <div class="mt-3">
                                <strong>Exemples :</strong>
                                <ul class="small">
                                    <li>Publicités ciblées</li>
                                    <li>Pixels de suivi</li>
                                    <li>Campagnes email</li>
                                    <li>Réseaux sociaux</li>
                                </ul>
                            </div>
                            <span class="badge bg-warning" id="marketing-status">Vérifié...</span>
                        </div>
                    </div>
                </div>

                <!-- Cookies de Préférences -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-user-cog text-primary me-2"></i>
                                Cookies de Préférences
                            </h6>
                            <p class="card-text small text-muted">
                                Mémorisent vos choix pour personnaliser votre expérience.
                            </p>
                            <div class="mt-3">
                                <strong>Exemples :</strong>
                                <ul class="small">
                                    <li>Langue préférée</li>
                                    <li>Thème sombre/clair</li>
                                    <li>Taille du texte</li>
                                    <li>Paramètres d'interface</li>
                                </ul>
                            </div>
                            <span class="badge bg-primary" id="preferences-status">Vérifié...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow-sm mt-4">
                <div class="card-body text-center">
                    <h6 class="card-title mb-3">Actions disponibles</h6>
                    <div class="btn-group-vertical d-block d-md-inline-block">
                        <button class="btn btn-primary mb-2 me-md-2" onclick="showCookiePreferences()">
                            <i class="fas fa-cog me-1"></i>
                            Modifier mes préférences
                        </button>
                        <button class="btn btn-success mb-2 me-md-2" onclick="acceptAllCookies()">
                            <i class="fas fa-check me-1"></i>
                            Accepter tous les cookies
                        </button>
                        <button class="btn btn-outline-danger mb-2" onclick="revokeCookies()">
                            <i class="fas fa-times me-1"></i>
                            Révoquer tous les cookies
                        </button>
                    </div>
                </div>
            </div>

            <!-- Informations légales -->
            <div class="mt-5">
                <h5>Vos droits</h5>
                <p class="text-muted">
                    Conformément au RGPD, vous avez le droit de contrôler l'utilisation de vos données personnelles. 
                    Vous pouvez modifier vos préférences à tout moment ou nous contacter pour exercer vos droits.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('privacy-policy') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-shield-alt me-1"></i>
                        Politique de confidentialité
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-envelope me-1"></i>
                        Nous contacter
                    </a>
                    <a href="{{ route('legal') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-gavel me-1"></i>
                        Mentions légales
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    updateCurrentStatus();
});

function updateCurrentStatus() {
    const consent = window.cookieManager ? window.cookieManager.getConsent() : null;
    
    if (consent) {
        const statusText = `
            Dernière mise à jour : ${new Date(consent.timestamp).toLocaleDateString('fr-FR')}<br>
            <small class="text-muted">
                Analytics: ${consent.analytics ? '✅ Accepté' : '❌ Refusé'} • 
                Marketing: ${consent.marketing ? '✅ Accepté' : '❌ Refusé'} • 
                Préférences: ${consent.preferences ? '✅ Accepté' : '❌ Refusé'}
            </small>
        `;
        document.getElementById('consent-status').innerHTML = statusText;
        
        // Mettre à jour les badges
        document.getElementById('analytics-status').textContent = consent.analytics ? 'Activé' : 'Désactivé';
        document.getElementById('analytics-status').className = `badge bg-${consent.analytics ? 'success' : 'secondary'}`;
        
        document.getElementById('marketing-status').textContent = consent.marketing ? 'Activé' : 'Désactivé';
        document.getElementById('marketing-status').className = `badge bg-${consent.marketing ? 'success' : 'secondary'}`;
        
        document.getElementById('preferences-status').textContent = consent.preferences ? 'Activé' : 'Désactivé';
        document.getElementById('preferences-status').className = `badge bg-${consent.preferences ? 'success' : 'secondary'}`;
    } else {
        document.getElementById('consent-status').innerHTML = 'Aucune préférence enregistrée. <small class="text-muted">Vous verrez la bannière de cookies.</small>';
        
        // Réinitialiser les badges
        ['analytics-status', 'marketing-status', 'preferences-status'].forEach(id => {
            document.getElementById(id).textContent = 'Non défini';
            document.getElementById(id).className = 'badge bg-secondary';
        });
    }
}

function revokeCookies() {
    if (confirm('Êtes-vous sûr de vouloir révoquer tous vos consentements ? Cela rechargera la page.')) {
        if (window.cookieManager) {
            window.cookieManager.revokeConsent();
        }
    }
}

// Écouter les changements de consentement
window.addEventListener('cookieConsentUpdated', function(event) {
    setTimeout(updateCurrentStatus, 500);
});
</script>
@endsection