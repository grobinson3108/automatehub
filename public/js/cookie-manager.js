/**
 * Cookie Manager - Gestion RGPD des cookies
 * Compatible avec AutomateHub
 */

class CookieManager {
    constructor() {
        this.cookieName = 'automatehub_cookie_consent';
        this.cookieExpiry = 365; // jours
        this.init();
    }

    init() {
        // Vérifier si l'utilisateur a déjà donné son consentement
        const consent = this.getConsent();
        
        if (!consent) {
            // Attendre que la page soit chargée pour afficher la bannière
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.showBanner());
            } else {
                this.showBanner();
            }
        } else {
            // Appliquer les préférences existantes
            this.applyConsent(consent);
        }

        // Écouter les changements de préférences
        this.attachEventListeners();
    }

    showBanner() {
        const banner = document.getElementById('cookie-banner');
        if (banner) {
            banner.classList.remove('d-none');
            // Animation d'entrée
            setTimeout(() => {
                banner.style.opacity = '0';
                banner.style.transform = 'translateY(100%)';
                banner.style.transition = 'all 0.5s ease';
                banner.style.opacity = '1';
                banner.style.transform = 'translateY(0)';
            }, 100);
        }
    }

    hideBanner() {
        const banner = document.getElementById('cookie-banner');
        if (banner) {
            banner.style.transform = 'translateY(100%)';
            banner.style.opacity = '0';
            setTimeout(() => {
                banner.classList.add('d-none');
            }, 500);
        }
    }

    acceptAll() {
        const consent = {
            essential: true,
            analytics: true,
            marketing: true,
            preferences: true,
            timestamp: new Date().toISOString()
        };
        
        this.saveConsent(consent);
        this.applyConsent(consent);
        this.hideBanner();
        
        this.showToast('✅ Tous les cookies ont été acceptés', 'success');
    }

    acceptEssential() {
        const consent = {
            essential: true,
            analytics: false,
            marketing: false,
            preferences: false,
            timestamp: new Date().toISOString()
        };
        
        this.saveConsent(consent);
        this.applyConsent(consent);
        this.hideBanner();
        
        this.showToast('✅ Seuls les cookies essentiels ont été acceptés', 'info');
    }

    showPreferences() {
        // Charger les préférences actuelles
        const consent = this.getConsent() || {
            essential: true,
            analytics: false,
            marketing: false,
            preferences: false
        };

        // Mettre à jour les checkboxes
        document.getElementById('analytics-cookies').checked = consent.analytics;
        document.getElementById('marketing-cookies').checked = consent.marketing;
        document.getElementById('preferences-cookies').checked = consent.preferences;

        // Afficher le modal
        const modal = new bootstrap.Modal(document.getElementById('cookiePreferencesModal'));
        modal.show();
    }

    savePreferences() {
        const consent = {
            essential: true, // Toujours true
            analytics: document.getElementById('analytics-cookies').checked,
            marketing: document.getElementById('marketing-cookies').checked,
            preferences: document.getElementById('preferences-cookies').checked,
            timestamp: new Date().toISOString()
        };

        this.saveConsent(consent);
        this.applyConsent(consent);
        this.hideBanner();

        // Fermer le modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('cookiePreferencesModal'));
        if (modal) modal.hide();

        this.showToast('✅ Vos préférences ont été sauvegardées', 'success');
    }

    saveConsent(consent) {
        const consentString = JSON.stringify(consent);
        const expiryDate = new Date();
        expiryDate.setTime(expiryDate.getTime() + (this.cookieExpiry * 24 * 60 * 60 * 1000));
        
        document.cookie = `${this.cookieName}=${consentString}; expires=${expiryDate.toUTCString()}; path=/; secure; samesite=strict`;
        
        // Envoyer à Laravel pour logging/analytics
        this.sendConsentToServer(consent);
    }

    getConsent() {
        const name = this.cookieName + "=";
        const decodedCookie = decodeURIComponent(document.cookie);
        const ca = decodedCookie.split(';');
        
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) === 0) {
                try {
                    return JSON.parse(c.substring(name.length, c.length));
                } catch (e) {
                    return null;
                }
            }
        }
        return null;
    }

    applyConsent(consent) {
        // Appliquer Google Analytics si autorisé
        if (consent.analytics) {
            this.enableGoogleAnalytics();
        } else {
            this.disableGoogleAnalytics();
        }

        // Appliquer les cookies marketing si autorisés
        if (consent.marketing) {
            this.enableMarketingCookies();
        } else {
            this.disableMarketingCookies();
        }

        // Appliquer les cookies de préférences si autorisés
        if (consent.preferences) {
            this.enablePreferencesCookies();
        }

        // Émettre un événement personnalisé pour les autres scripts
        window.dispatchEvent(new CustomEvent('cookieConsentUpdated', {
            detail: consent
        }));
    }

    enableGoogleAnalytics() {
        // Si Google Analytics est configuré
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted'
            });
        }
    }

    disableGoogleAnalytics() {
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'denied'
            });
        }
    }

    enableMarketingCookies() {
        // Activer les pixels de suivi, Facebook Pixel, etc.
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'ad_storage': 'granted'
            });
        }
    }

    disableMarketingCookies() {
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'ad_storage': 'denied'
            });
        }
    }

    enablePreferencesCookies() {
        // Autoriser les cookies de préférences utilisateur
        // Par exemple, sauvegarder le thème, la langue, etc.
    }

    sendConsentToServer(consent) {
        // Envoyer les données de consentement à Laravel pour compliance
        fetch('/api/cookie-consent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                consent: consent,
                user_agent: navigator.userAgent,
                ip: null, // Sera récupéré côté serveur
                timestamp: new Date().toISOString()
            })
        }).catch(error => {
            console.warn('Could not send consent to server:', error);
        });
    }

    revokeConsent() {
        // Supprimer le cookie de consentement
        document.cookie = `${this.cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
        
        // Recharger la page pour redemander le consentement
        window.location.reload();
    }

    showToast(message, type = 'info') {
        // Créer un toast Bootstrap
        const toastContainer = document.getElementById('toast-container') || this.createToastContainer();
        
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = toastContainer.lastElementChild;
        const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
        toast.show();
        
        // Supprimer l'élément après fermeture
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }

    attachEventListeners() {
        // Bouton pour rouvrir les préférences (peut être ajouté ailleurs sur le site)
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-cookie-preferences]')) {
                e.preventDefault();
                this.showPreferences();
            }
            
            if (e.target.matches('[data-cookie-revoke]')) {
                e.preventDefault();
                if (confirm('Êtes-vous sûr de vouloir révoquer votre consentement aux cookies ?')) {
                    this.revokeConsent();
                }
            }
        });
    }

    // Méthode utilitaire pour vérifier si un type de cookie est autorisé
    isAllowed(type) {
        const consent = this.getConsent();
        return consent ? consent[type] : false;
    }
}

// Initialiser le gestionnaire de cookies
window.cookieManager = new CookieManager();

// Exposer les méthodes principales globalement pour faciliter l'utilisation
window.acceptAllCookies = () => window.cookieManager.acceptAll();
window.showCookiePreferences = () => window.cookieManager.showPreferences();