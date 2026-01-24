# AutomateHub V2 - Roadmap Sprints & Vidéos

**Marketplace Mini-Apps Verticale SaaS**
**Période** : Janvier - Juin 2026 (6 mois)
**Format Vidéos** : 1 vidéo FR + 1 vidéo EN par sprint

---

## Sprint 0 : Setup & Architecture (Semaine 1-2) ✅ DONE

**Durée** : 2 semaines
**Statut** : Complété (24 janvier 2026)

### Objectifs
- Nettoyer legacy n8n (V1 → V2)
- Créer squelette V2 complet (migrations, models, routes)
- Centre paramètres fonctionnel
- Seeder 4 apps prioritaires

### Deliverables ✅
- [x] Migration `apps` table (6 tables total)
- [x] 6 Models V2 (App, AppPricingPlan, UserAppSubscription, UserAppCredential, AppUsageLog, AppReview)
- [x] CredentialManager service (encryption Laravel Crypt)
- [x] Routes V2 (`routes/apps.php`)
- [x] AppSettingsController + vues Blade
- [x] AppSeeder (PostMaid, VideoPlan, ReferAIl, EmailScan)
- [x] Backup DB V1
- [x] Git : 3 commits clean

### Contenu Vidéo
**Titre** : "AutomateHub V2 - De 0 à Marketplace SaaS en 2 semaines"
**Durée** : 15-20 min
**Chapitres** :
1. Vision : pourquoi pivoter de n8n vers mini-apps verticales
2. Architecture technique (Laravel 12 + Supabase + Stripe)
3. Demo : structure DB, models, routes
4. Centre paramètres credentials (OAuth + API keys)
5. Seeder : 4 apps prioritaires live
6. Roadmap 6 mois

---

## Sprint 1 : PostMaid MVP Backend (Semaine 3-4)

**Durée** : 2 semaines
**App** : PostMaid (priorité #1)

### Objectifs
- Architecture PostMaid backend complet
- OpenAI integration (GPT-4o captions)
- Instagram API integration
- Timeline ML (basic algorithm)

### Tasks
**Semaine 3** :
- [ ] Models : `Post`, `PostTemplate`, `ScheduledPost`, `SocialAccount`
- [ ] Services : `AIService`, `InstagramService`, `SchedulingService`
- [ ] Jobs : `GeneratePostJob`, `PublishPostJob`
- [ ] Migration : tables PostMaid

**Semaine 4** :
- [ ] OpenAI integration : generateCaption(), generateHashtags()
- [ ] Instagram Graph API : connectAccount(), publishPost()
- [ ] Timeline ML : bestTimeToPost() (algorithm v1)
- [ ] Tests unitaires + feature tests

### Deliverables
- [ ] PostMaid backend 100% fonctionnel
- [ ] Tests passing
- [ ] Seeder : 10 posts examples

### Contenu Vidéo
**Titre** : "PostMaid Backend - IA + Instagram API + Timeline ML"
**Durée** : 20-25 min
**Chapitres** :
1. Architecture PostMaid (models, services, jobs)
2. OpenAI GPT-4o : génération captions + hashtags (demo live)
3. Instagram Graph API : connexion + publication (demo live)
4. Timeline ML : algorithme prédiction meilleur timing
5. Queue Horizon : jobs background
6. Tests : garantir 0 bugs

---

## Sprint 2 : PostMaid Frontend + UX (Semaine 5-6)

**Durée** : 2 semaines
**App** : PostMaid (suite)

### Objectifs
- Dashboard Filament PostMaid
- Formulaire création post
- Calendar timeline ML
- Preview post avant publication

### Tasks
**Semaine 5** :
- [ ] Filament Resource : PostResource
- [ ] Formulaire : upload image, prompt caption, select platforms
- [ ] AI generation : real-time preview captions/hashtags
- [ ] Image generation : DALL-E integration (optionnel)

**Semaine 6** :
- [ ] Calendar view (FullCalendar.js)
- [ ] Timeline ML : affichage best times
- [ ] Preview modal : Instagram preview exact
- [ ] Drag & drop scheduling

### Deliverables
- [ ] Dashboard PostMaid complet
- [ ] Formulaire création post UX fluide
- [ ] Calendar fonctionnel
- [ ] Preview Instagram pixel-perfect

### Contenu Vidéo
**Titre** : "PostMaid Frontend - UX Parfaite pour Créateurs"
**Durée** : 20-25 min
**Chapitres** :
1. Filament 4 : dashboard customisé
2. Formulaire création : upload + AI generation live
3. Calendar : timeline ML visuelle
4. Preview Instagram : design system
5. Demo E2E : créer un post de A à Z

---

## Sprint 3 : PostMaid Beta Launch (Semaine 7-8)

**Durée** : 2 semaines
**App** : PostMaid (launch)

### Objectifs
- Recruter 50 beta-testers
- Landing page PostMaid
- Onboarding video
- Feedback iterations

### Tasks
**Semaine 7** :
- [ ] Landing page : /apps/postmaid (SEO optimized)
- [ ] Onboarding video (3-5 min)
- [ ] Email campaign : recruter beta-testers
- [ ] Discord community setup

**Semaine 8** :
- [ ] Onboarding 50 beta-testers
- [ ] Collecter feedback (surveys + interviews)
- [ ] Bug fixes
- [ ] Itérations features demandées

### Deliverables
- [ ] 50 beta-testers actifs
- [ ] Landing page live
- [ ] Feedback rapport
- [ ] Metrics : usage, NPS, feature requests

### Contenu Vidéo
**Titre** : "PostMaid Beta Launch - 50 Utilisateurs en 2 Semaines"
**Durée** : 15-20 min
**Chapitres** :
1. Landing page : copywriting + SEO
2. Stratégie recrutement beta-testers
3. Onboarding : première connexion magique
4. Feedback : insights utilisateurs
5. Metrics : traction early

---

## Sprint 4 : VideoPlan MVP (Semaine 9-12)

**Durée** : 4 semaines
**App** : VideoPlan (priorité #2)

### Objectifs
- MVP VideoPlan complet (backend + frontend)
- AI script generation
- Equipment recommendations
- SEO title/description generator

### Tasks
**Semaine 9-10 (Backend)** :
- [ ] Models : `VideoIdea`, `VideoScript`, `ShotList`
- [ ] Services : `ScriptGeneratorService`, `EquipmentService`, `SEOService`
- [ ] OpenAI : generateScript(), generateShotList()
- [ ] Amazon API : equipment recommendations

**Semaine 11-12 (Frontend)** :
- [ ] Filament dashboard VideoPlan
- [ ] Formulaire : video idea → script generation
- [ ] Shot list editor (drag & drop)
- [ ] Export PDF (script + shot list + equipment)

### Deliverables
- [ ] VideoPlan MVP fonctionnel
- [ ] 3 scénarios complets (tutorial, vlog, ad)
- [ ] Tests passing
- [ ] Landing page

### Contenu Vidéo
**Titre** : "VideoPlan - Plan Vidéo Viral en 5 Minutes avec IA"
**Durée** : 20-25 min
**Chapitres** :
1. Architecture VideoPlan
2. AI script generation (demo live)
3. Shot list : storyboard automatique
4. Equipment recommendations (Amazon API)
5. Export PDF professionnel

---

## Sprint 5 : Multi-Platform PostMaid (Semaine 13-14)

**Durée** : 2 semaines
**App** : PostMaid (expansion)

### Objectifs
- Ajouter TikTok + LinkedIn
- Multi-compte support
- Analytics dashboard

### Tasks
**Semaine 13** :
- [ ] TikTok API integration
- [ ] LinkedIn API integration
- [ ] Multi-account management

**Semaine 14** :
- [ ] Analytics : engagement tracking
- [ ] Dashboard metrics (followers, likes, comments)
- [ ] A/B testing captions

### Deliverables
- [ ] PostMaid multi-plateforme (Instagram, TikTok, LinkedIn)
- [ ] Analytics dashboard
- [ ] Tests passing

### Contenu Vidéo
**Titre** : "PostMaid Multi-Platform - 1 Post → 3 Plateformes"
**Durée** : 15-20 min
**Chapitres** :
1. TikTok API : particularités vs Instagram
2. LinkedIn API : B2B content optimization
3. Multi-compte : gestion centralisée
4. Analytics : mesurer engagement

---

## Sprint 6 : Marketplace V2 Public (Semaine 15-16)

**Durée** : 2 semaines
**Focus** : Marketplace globale

### Objectifs
- Page marketplace (/apps)
- Stripe checkout multi-apps
- Bundles pricing
- SEO global

### Tasks
**Semaine 15** :
- [ ] Page /apps : catalogue apps (cards, filters, search)
- [ ] Page /apps/{slug} : détails app (features, pricing, reviews)
- [ ] Stripe checkout : flow complet
- [ ] Bundles : 3 apps = 99€/mois

**Semaine 16** :
- [ ] SEO : meta tags, sitemap, schema.org
- [ ] Marketing pages : about, pricing, legal
- [ ] Launch Product Hunt
- [ ] Launch Indie Hackers

### Deliverables
- [ ] Marketplace public live
- [ ] Stripe production mode
- [ ] SEO optimized
- [ ] Product Hunt launch

### Contenu Vidéo
**Titre** : "Marketplace V2 - Launch Public AutomateHub"
**Durée** : 20-25 min
**Chapitres** :
1. Architecture marketplace (catalogue, search, filters)
2. Stripe checkout : abonnements multi-apps
3. Bundles : stratégie pricing
4. SEO : techniques 2026
5. Product Hunt : launch strategy

---

## Sprint 7 : ReferAIl ou App #4 (Semaine 17-20)

**Durée** : 4 semaines
**App** : ReferAIl (priorité #3) OU nouvelle app vertical gagnant

### Décision Sprint 6
Basé sur analytics PostMaid + VideoPlan :
- Si vertical "créateurs contenu" performe → développer app #4 créateurs
- Si vertical "pharma/B2B" émerge → développer ReferAIl
- Si surprise → pivoter vers vertical gagnant

### Objectifs (si ReferAIl)
- AI email sequences persuasives
- Gamification rewards
- CRM integrations
- Analytics referral tracking

### Deliverables
- [ ] App #3 MVP complet
- [ ] Tests beta
- [ ] Landing page
- [ ] Integration 1 CRM (HubSpot ou Salesforce)

### Contenu Vidéo
**Titre** : "ReferAIl - Automatiser Croissance Word-of-Mouth"
**Durée** : 20-25 min
**Chapitres** :
1. Architecture referral program
2. AI email sequences (persuasion techniques)
3. Gamification : badges + rewards
4. CRM integration (demo live)
5. Analytics : tracking referrals

---

## Sprint 8 : Optimisation & Scale (Semaine 21-24)

**Durée** : 4 semaines
**Focus** : Performance + Business

### Objectifs
- Performance optimizations
- Security audit
- Monitoring (Sentry + analytics)
- Revenue optimization

### Tasks
**Semaine 21-22 (Tech)** :
- [ ] Performance : cache Redis, query optimization
- [ ] Security audit : OWASP top 10
- [ ] Monitoring : Sentry, logs, alerts
- [ ] Tests E2E Playwright

**Semaine 23-24 (Business)** :
- [ ] Pricing optimization (A/B tests)
- [ ] Customer success : onboarding improvements
- [ ] Marketing automation (Brevo)
- [ ] Metrics dashboard : MRR, churn, LTV/CAC

### Deliverables
- [ ] Performance +50% (page load, API response)
- [ ] Security audit passed
- [ ] Monitoring production
- [ ] MRR tracking automated

### Contenu Vidéo
**Titre** : "Scale SaaS - Performance + Security + Business Metrics"
**Durée** : 20-25 min
**Chapitres** :
1. Performance : Redis cache + query optimization
2. Security : OWASP audit complet
3. Monitoring : Sentry + alerts
4. Business metrics : MRR dashboard

---

## Métriques Success (End of 6 Months)

### Produit
- [x] 3-4 mini-apps en production
- [ ] 200+ users actifs (MAU)
- [ ] NPS > 40
- [ ] Churn < 10%/mois

### Business
- [ ] MRR : 5 000 - 15 000€/mois
- [ ] ARR : 60 - 180K€
- [ ] LTV/CAC > 3
- [ ] 2-3 verticaux identifiés

### Technique
- [ ] Uptime 99.9%
- [ ] Tests coverage > 70%
- [ ] API response < 200ms
- [ ] 0 critical bugs

### Marketing
- [ ] 24 vidéos (12 sprints × 2 langues)
- [ ] 10K+ views YouTube cumulées
- [ ] 500+ subs newsletter
- [ ] Product Hunt top 5 de la semaine

---

## Stack Technique Final

**Backend** : Laravel 12, Filament 4
**Database** : Supabase (PostgreSQL) + Redis
**Frontend** : Blade + Alpine.js (+ React Inertia si needed)
**AI** : OpenAI GPT-4o, DALL-E
**Payments** : Stripe (multi-apps subscriptions)
**Monitoring** : Sentry
**Email** : Brevo
**Queue** : Laravel Horizon
**Serveur** : IONOS M/XL
**CI/CD** : GitHub Actions (future)

---

**Roadmap créée** : 24 janvier 2026
**Prochaine action** : Sprint 1 (PostMaid Backend)
**Contact** : greg@gregrobinson.dev
