/**
 * TARS Tracker - JavaScript client pour tracking des conversions
 */

class TARSTracker {
    constructor(config = {}) {
        this.apiEndpoint = config.apiEndpoint || '/tracking/tars-analytics.php';
        this.sessionId = this.getOrCreateSessionId();
        this.startTime = Date.now();
        this.currentPage = this.getCurrentPage();

        // Auto-track page view
        this.trackEvent('page_view', { page: this.currentPage });

        // Track engagement
        this.setupEngagementTracking();

        console.log('🤖 TARS Tracker initialized - Session:', this.sessionId);
    }

    getOrCreateSessionId() {
        let sessionId = localStorage.getItem('tars_session_id');
        if (!sessionId) {
            sessionId = this.generateId();
            localStorage.setItem('tars_session_id', sessionId);
        }
        return sessionId;
    }

    generateId() {
        return 'tars_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    getCurrentPage() {
        const path = window.location.pathname;

        if (path.includes('tars-pack1')) return 'pack1_landing';
        if (path.includes('tars-upsell1')) return 'upsell1';
        if (path.includes('tars-crossell')) return 'crossell';
        if (path.includes('tars-community')) return 'community';
        if (path.includes('tars-thank-you')) return 'thank_you';

        return 'unknown';
    }

    async trackEvent(eventType, data = {}) {
        const payload = {
            action: 'track_event',
            session_id: this.sessionId,
            event_type: eventType,
            page: this.currentPage,
            data: {
                ...data,
                timestamp: Date.now(),
                url: window.location.href,
                referrer: document.referrer
            }
        };

        return this.sendRequest(payload);
    }

    async trackConversion(conversionType, amount = 0, data = {}) {
        const payload = {
            action: 'track_conversion',
            session_id: this.sessionId,
            conversion_type: conversionType,
            amount: amount,
            currency: 'EUR',
            source: this.currentPage,
            data: {
                ...data,
                timestamp: Date.now(),
                time_to_convert: Date.now() - this.startTime
            }
        };

        return this.sendRequest(payload);
    }

    async trackFunnelStep(stepName, stepOrder, timeSpent = null) {
        const spent = timeSpent || (Date.now() - this.startTime) / 1000;

        const payload = {
            action: 'track_funnel_step',
            session_id: this.sessionId,
            step_name: stepName,
            step_order: stepOrder,
            time_spent: Math.round(spent),
            data: {
                page: this.currentPage,
                timestamp: Date.now()
            }
        };

        return this.sendRequest(payload);
    }

    async sendRequest(payload) {
        try {
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('TARS Tracker error:', error);
            return { error: error.message };
        }
    }

    setupEngagementTracking() {
        // Track time on page
        let timeOnPage = 0;
        const interval = setInterval(() => {
            timeOnPage += 5;

            // Track engagement milestones
            if (timeOnPage === 30) {
                this.trackEvent('engaged_30s');
            } else if (timeOnPage === 60) {
                this.trackEvent('engaged_1min');
            } else if (timeOnPage === 120) {
                this.trackEvent('engaged_2min');
            }
        }, 5000);

        // Track scroll depth
        let maxScroll = 0;
        const trackScroll = () => {
            const scrollPercent = Math.round(
                (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100
            );

            if (scrollPercent > maxScroll) {
                maxScroll = scrollPercent;

                if (maxScroll >= 25 && maxScroll < 50) {
                    this.trackEvent('scroll_25');
                } else if (maxScroll >= 50 && maxScroll < 75) {
                    this.trackEvent('scroll_50');
                } else if (maxScroll >= 75) {
                    this.trackEvent('scroll_75');
                }
            }
        };

        window.addEventListener('scroll', trackScroll, { passive: true });

        // Track exit intent
        let exitIntentTracked = false;
        const trackExitIntent = (e) => {
            if (e.clientY <= 0 && !exitIntentTracked) {
                exitIntentTracked = true;
                this.trackEvent('exit_intent');
            }
        };

        document.addEventListener('mouseout', trackExitIntent);

        // Track before unload
        window.addEventListener('beforeunload', () => {
            clearInterval(interval);
            this.trackEvent('page_exit', {
                time_on_page: timeOnPage,
                max_scroll: maxScroll
            });
        });
    }

    // Méthodes pour les conversions spécifiques TARS
    trackPack1Purchase(amount = 29) {
        return this.trackConversion('pack1_purchase', amount, {
            pack_type: 'basic',
            original_price: 94
        });
    }

    trackUpsell1Purchase(amount = 67) {
        return this.trackConversion('upsell1_purchase', amount, {
            pack_type: 'premium',
            original_price: 149
        });
    }

    trackCrossSellPurchase(amount = 47) {
        return this.trackConversion('crosssell_purchase', amount, {
            pack_type: 'ai',
            original_price: 94
        });
    }

    trackCommunitySubscription(amount = 27) {
        return this.trackConversion('community_subscription', amount, {
            subscription_type: 'monthly',
            regular_price: 67
        });
    }

    // Méthodes pour tracking des étapes du funnel
    trackFunnelPack1() {
        return this.trackFunnelStep('pack1_landing', 1);
    }

    trackFunnelUpsell1() {
        return this.trackFunnelStep('upsell1', 2);
    }

    trackFunnelCrossSell() {
        return this.trackFunnelStep('crosssell', 3);
    }

    trackFunnelCommunity() {
        return this.trackFunnelStep('community', 4);
    }

    trackFunnelThankYou() {
        return this.trackFunnelStep('thank_you', 5);
    }

    // Méthodes pour tracking des interactions
    trackButtonClick(buttonName, data = {}) {
        return this.trackEvent('button_click', {
            button_name: buttonName,
            ...data
        });
    }

    trackVideoPlay(videoName, data = {}) {
        return this.trackEvent('video_play', {
            video_name: videoName,
            ...data
        });
    }

    trackFormSubmit(formName, data = {}) {
        return this.trackEvent('form_submit', {
            form_name: formName,
            ...data
        });
    }

    trackDownload(fileName, data = {}) {
        return this.trackEvent('download', {
            file_name: fileName,
            ...data
        });
    }

    // Récupération des analytics (pour tableau de bord)
    async getAnalytics(period = '7d') {
        try {
            const response = await fetch(`${this.apiEndpoint}?action=analytics&period=${period}`);
            return await response.json();
        } catch (error) {
            console.error('Error fetching analytics:', error);
            return { error: error.message };
        }
    }

    async getConversions(date = null) {
        const dateParam = date ? `&date=${date}` : '';
        try {
            const response = await fetch(`${this.apiEndpoint}?action=conversions${dateParam}`);
            return await response.json();
        } catch (error) {
            console.error('Error fetching conversions:', error);
            return { error: error.message };
        }
    }

    async getFunnelAnalytics() {
        try {
            const response = await fetch(`${this.apiEndpoint}?action=funnel`);
            return await response.json();
        } catch (error) {
            console.error('Error fetching funnel analytics:', error);
            return { error: error.message };
        }
    }
}

// Auto-initialize if not in module context
if (typeof module === 'undefined') {
    window.TARSTracker = TARSTracker;

    // Auto-start tracking when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.tarsTracker = new TARSTracker();
        });
    } else {
        window.tarsTracker = new TARSTracker();
    }
}

// Export for module environments
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TARSTracker;
}