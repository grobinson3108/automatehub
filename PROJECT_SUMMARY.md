# AutomateHub

**Première plateforme française spécialisée dans l'automatisation n8n pour entrepreneurs**

**Statut**: Production (MVP lancé, Phase 2 en cours)

---

## Résumé Exécutif

**AutomateHub** résout le problème du temps perdu sur des tâches répétitives pour les entrepreneurs français (pharmacies, commerces locaux, e-commerce, services). La plateforme démocratise **n8n** en proposant **34 packs de workflows prêts à l'emploi**, spécialisés par secteur d'activité, avec une documentation en français et une communauté de 500+ entrepreneurs.

La proposition de valeur unique repose sur trois piliers : **workflows métier spécialisés** (pas de solutions génériques), **accompagnement complet** (tutoriels, communauté Skool, support FR), et **ROI immédiat** (10h économisées/semaine, rentable en 48h). Le business model combine ventes one-time (packs 29-49€) et abonnement récurrent (communauté Skool).

---

## Stack Technique

| Catégorie | Technologie | Version | Détails |
|-----------|-------------|---------|---------|
| **Backend** | Laravel | 12.x | Framework PHP, API REST |
| **Language** | PHP | 8.2+ | PSR-4 autoload, Composer |
| **Frontend** | React | 19.0 | SPA avec TypeScript |
| **Framework JS** | Inertia.js | 2.0 | Bridge Laravel-React |
| **UI Library** | ShadCN UI | Latest | Components React réutilisables |
| **CSS** | Tailwind CSS | 4.0 | Design system personnalisé |
| **Database** | MySQL | Latest | Base de données relationnelle |
| **Automation** | n8n | Latest | Hébergé sur n8n.automatehub.fr |
| **Payments** | Stripe | Latest | Multi-devises (EUR/USD) |
| **Auth** | Laravel Socialite | 5.23 | Google OAuth |
| **Email** | Postmark/Resend | - | Services d'emailing |
| **Notifications** | Slack | - | Alertes et monitoring |
| **Activity Log** | Spatie ActivityLog | 4.10 | Traçabilité des actions |
| **Build** | Vite | 6.0 | Bundler moderne |
| **Package Manager** | npm / Composer | Latest | Frontend / Backend |

---

## Statistiques du Code

### Vue d'ensemble

| Métrique | Valeur |
|----------|--------|
| **Total fichiers code** | 158 fichiers |
| **Total lignes de code** | 31,731 lignes |
| **Fichiers PHP** | 130 fichiers |
| **Fichiers TypeScript/React** | 28 fichiers |
| **Lignes PHP** | 28,436 lignes |
| **Lignes TypeScript/React** | 3,295 lignes |

### Composants Backend (Laravel)

| Composant | Nombre | Description |
|-----------|--------|-------------|
| **Models** | 25 | Eloquent ORM models |
| **Controllers** | 44 | Logique métier + API |
| **Services** | 14 | Business logic layer |
| **Migrations** | 34 | Schema de base de données |
| **Middlewares** | 7 | Auth, Security, API rate limiting |
| **Jobs** | 5 | Queue workers (emails, analytics) |
| **Events** | 3 | Event-driven architecture |
| **Listeners** | 3 | Event handlers |
| **Commands** | 27 | Artisan CLI commands |

### Composants Frontend (React)

| Composant | Nombre | Description |
|-----------|--------|-------------|
| **Pages (TSX)** | 7 | Pages Inertia.js |
| **Components (TSX)** | 12 | Composants UI réutilisables |
| **UI Components** | 8 | ShadCN UI primitives |

---

## Fonctionnalités Clés

### 📦 **Marketplace de Workflows Premium**
34 packs de workflows n8n organisés par secteur (pharmacie, e-commerce, marketing local, services) avec filtres avancés, tri dynamique, et pages détaillées incluant FAQ et trust badges.

### 💳 **Système de Paiement Stripe**
Intégration complète multi-devises (EUR/USD), paiement sécurisé, livraison immédiate par téléchargement, garantie 30 jours avec système de watermarking anti-piratage (limite 3 téléchargements).

### 🤖 **Intégration n8n Native**
API directe avec instance n8n hébergée (n8n.automatehub.fr), import/export de workflows JSON, synchronisation automatique, et bibliothèque de patterns réutilisables.

### 👥 **Communauté Skool Active**
500+ entrepreneurs francophones, système de gamification avec badges, partage de workflows personnalisés, support mutuel et événements exclusifs.

### 🎓 **Système de Tutoriels**
Bibliothèque de tutoriels vidéo (gratuits et premium), progression trackée par utilisateur, système de badges pour engagement, et formation de débutant à expert.

### 🔐 **Authentification OAuth**
Google Sign-In avec Laravel Socialite, gestion des rôles (admin/premium/user), middleware de protection des routes, et onboarding personnalisé.

### 📊 **Analytics & Reporting**
Dashboard admin complet, tracking des ventes et conversions, analytics utilisateurs, logs d'activité (Spatie ActivityLog), et rapports automatisés par email.

### 📝 **Blog Automatisé**
Système de publication d'articles avec génération de contenu IA, calendrier éditorial, publication sur réseaux sociaux automatique (via workflow n8n).

---

## Architecture

### Pattern Architectural

```
┌─────────────────────────────────────────────────────────┐
│                    Frontend Layer                       │
│  React 19 + TypeScript + Inertia.js + ShadCN UI        │
│  (Pages, Components, UI Primitives)                     │
└──────────────────┬──────────────────────────────────────┘
                   │ Inertia Protocol
┌──────────────────▼──────────────────────────────────────┐
│                 Laravel Backend                         │
│  ┌──────────────────────────────────────────────────┐  │
│  │  Controllers → Services → Models → Database      │  │
│  │  (MVC + Service Layer Pattern)                   │  │
│  └──────────────────────────────────────────────────┘  │
│                                                          │
│  ┌──────────────────────────────────────────────────┐  │
│  │  Queue System (Jobs + Events + Listeners)        │  │
│  │  - Emails, Analytics, Badges, Workflows sync     │  │
│  └──────────────────────────────────────────────────┘  │
└──────────┬───────────────────────────┬─────────────────┘
           │                           │
           ▼                           ▼
    ┌──────────┐              ┌─────────────────┐
    │  MySQL   │              │  External APIs   │
    │ Database │              │  - Stripe        │
    └──────────┘              │  - n8n API       │
                              │  - Google OAuth  │
                              │  - Postmark      │
                              └─────────────────┘
```

### Patterns Utilisés

- **MVC (Model-View-Controller)**: Architecture Laravel standard
- **Service Layer Pattern**: Business logic isolée dans Services
- **Repository Pattern**: Abstraction data access (implicit via Eloquent)
- **Event-Driven Architecture**: Jobs + Events + Listeners pour async tasks
- **Middleware Pattern**: Auth, Security headers, API rate limiting
- **Inertia Protocol**: SPA sans API REST explicite (server-side routing)
- **Component-Based UI**: React components réutilisables (ShadCN)

---

## Intégrations Externes

| Service | Utilisation | Documentation |
|---------|-------------|---------------|
| **Stripe** | Paiements multi-devises (EUR/USD), webhooks, gestion abonnements | [stripe.com/docs](https://stripe.com/docs) |
| **n8n API** | Synchronisation workflows, import/export, gestion workspace | Instance: n8n.automatehub.fr |
| **Google OAuth** | Authentification utilisateurs via Google Sign-In | Laravel Socialite |
| **Postmark** | Emails transactionnels (confirmations, factures) | [postmarkapp.com](https://postmarkapp.com) |
| **Resend** | Alternative emailing (fallback Postmark) | [resend.com](https://resend.com) |
| **Slack** | Notifications admin, alertes système, monitoring | Webhook notifications |

---

## Points Forts Techniques

### 🚀 **Architecture Full-Stack Moderne**
Stack Laravel 12 + React 19 + Inertia.js offrant performance SPA avec SEO server-side, TypeScript pour type safety, et Tailwind CSS v4 pour design system cohérent.

### ⚡ **Performance Optimisée**
Vite 6.0 pour build ultra-rapide, code splitting automatique, lazy loading des composants, et optimisation des assets (Rollup + Lightning CSS).

### 🔒 **Sécurité Renforcée**
Middlewares de sécurité (CSRF, XSS, headers HTTP), rate limiting API, watermarking anti-piratage, activity logging complet (Spatie), et conformité RGPD.

### 🎨 **Design System Professionnel**
ShadCN UI avec thème orange personnalisé (#FF7A1F), glassmorphism effects, gradients dynamiques, composants accessibles (Radix UI), et mobile-first responsive.

### 🤖 **Automatisation Native**
Intégration n8n API pour synchronisation workflows, bibliothèque de 9 patterns réutilisables documentés, système de templates JSON, et CLI wrappers pour MCP.

---

## Catégorie Portfolio

**automation**

---

## Informations Complémentaires

### Déploiement
- **Hébergement**: VPS Linux (Ubuntu/Debian)
- **Serveur Web**: Nginx
- **SSL**: Certbot (Let's Encrypt)
- **Domaine**: automatehub.fr
- **Instance n8n**: n8n.automatehub.fr

### Documentation Persistante
- `/docs/decisions.md` - Architecture et décisions techniques
- `/docs/patterns.md` - 9 patterns n8n réutilisables documentés
- `/docs/learnings/` - Solutions aux problèmes techniques rencontrés
- `/CLAUDE.md` - Instructions pour IA et développement

### Chiffres Business
- **500+ entrepreneurs** dans la communauté Skool
- **34 packs premium** disponibles
- **50+ workflows** dans la bibliothèque
- **5000+ workflows** déployés chez les clients
- **4.9/5** note moyenne clients
- **10h/semaine** économisées en moyenne
- **48h** pour ROI moyen

### Roadmap
- **Phase 1 (✅ Complétée)**: MVP avec 34 packs et design system
- **Phase 2 (🚧 En cours)**: Intégration Stripe complète + sécurité workflows
- **Phase 3 (📅 Planifié)**: Marketing automation (emails, promos, affiliation)
- **Phase 4 (📅 Planifié)**: Tutoriels vidéo complets + marketplace communautaire
- **Phase 5 (📅 Vision)**: API publique, white-label, expansion internationale

---

**Développé en 2025 | Made in France 🇫🇷**
