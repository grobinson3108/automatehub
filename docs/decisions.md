# 🎯 Décisions d'Architecture - AutomateHub

> **IMPORTANT**: Ce fichier doit être lu par Claude au début de chaque session et mis à jour après chaque décision architecturale importante.

## 📅 Dernière mise à jour
**Date**: 2025-10-16
**Par**: Claude & Utilisateur

---

## 🏗️ Architecture Globale

### Stack Technique
- **Backend**: Laravel 12 (PHP)
- **Automatisation**: n8n (hébergé sur https://n8n.automatehub.fr)
- **Base de données**: MySQL (accès via MCP wrapper)
- **Serveur**: Linux (Ubuntu/Debian)
- **Environnement**: Claude Terminal via Cursor (pas Claude Desktop)

### Structure du Projet
```
/var/www/automatehub/
├── app/                    # Laravel application
├── mcp-wrappers/          # Wrappers pour accès MCP
│   ├── mysql-query.sh
│   ├── n8n-workflow-create.sh
│   └── n8n-workflows-list.sh
├── mcp                    # Script unifié MCP
├── docs/                  # 📚 Documentation persistante (NOUVEAU)
│   ├── decisions.md       # Ce fichier
│   ├── patterns.md        # Patterns n8n réutilisables
│   └── learnings/         # Solutions aux problèmes
└── CLAUDE.md             # Instructions pour Claude
```

---

## 🔑 Décisions Architecturales

### 1. Accès n8n via MCP Wrapper
**Décision**: Utiliser des wrappers bash pour accéder aux fonctionnalités n8n
**Raison**: Claude Terminal ne peut pas accéder directement aux MCPs comme Claude Desktop
**Commandes**:
```bash
./mcp n8n-create "Nom du workflow"
./mcp n8n-list
```

### 2. Base de Données MySQL
**Décision**: Accès MySQL via wrapper MCP plutôt que connexion directe
**Raison**: Cohérence avec l'approche MCP et centralisation des accès
**Commandes**:
```bash
./mcp mysql "SELECT * FROM users"
./mcp mysql "SHOW TABLES"
```

### 3. Documentation Persistante
**Décision**: Créer un système de documentation légère inspiré de Compound Engineering
**Raison**:
- Éviter la perte de contexte après compactation
- Maintenir la cohérence architecturale
- Documenter les "pourquoi" pas seulement les "comment"

**Structure choisie**:
- `/docs/decisions.md` : Décisions d'architecture
- `/docs/patterns.md` : Patterns réutilisables n8n
- `/docs/learnings/` : Solutions aux problèmes spécifiques

---

## 🎨 Philosophie de Développement

### Principes
1. **Simplicité avant complexité**: Éviter l'over-engineering
2. **Documentation des décisions**: Toujours expliquer le "pourquoi"
3. **Réutilisabilité**: Créer des patterns pour les workflows n8n
4. **Iteration rapide**: Privilégier les solutions qui fonctionnent puis optimiser

### Workflow
1. **Plan**: Comprendre le besoin et consulter les docs existantes
2. **Implement**: Coder en suivant les patterns établis
3. **Document**: Mettre à jour decisions.md et patterns.md si nouveau pattern
4. **Learn**: Documenter les problèmes/solutions dans learnings/

---

## 🚀 Fonctionnalités Principales

### Actuelles
- Système de wrappers MCP pour n8n et MySQL
- Interface Laravel pour gérer les automatisations
- Accès n8n web pour création visuelle de workflows
- **Landing pages des packs** (34 packs de workflows premium)
  - Page index avec filtres par catégorie et tri
  - Pages détaillées pour chaque pack
  - Design cohérent avec le thème AutomateHub

### Planifiées
- Intégration Stripe multi-devises (€/$)
- Système de sécurité (watermarking + limite 3 téléchargements)
- Séquences emails automatiques
- Pages légales (CGV, politique remboursement)
- Codes promo, bundles, affiliation
- Growth Hacking (exit-intent, scarcity, countdown)
- Workflow n8n publication réseaux sociaux

---

## 📝 Notes Importantes

### Pour Claude
- **TOUJOURS lire ce fichier au début d'une nouvelle session**
- **TOUJOURS mettre à jour après une décision architecturale**
- **TOUJOURS consulter patterns.md avant de créer un workflow**
- **TOUJOURS documenter les solutions dans learnings/**

### Limitations Connues
- Claude Terminal != Claude Desktop (pas d'accès MCP direct)
- Nécessité des wrappers bash pour les fonctionnalités MCP

---

## 🔄 Historique des Changements

### 2025-10-16

#### Design System & Frontend
- **Décision**: Adopter React + Inertia.js + ShadCN UI + Tailwind CSS v4
- **Raison**: Stack moderne, composants réutilisables, design system cohérent
- **Implémentation**:
  - Thème orange principal: `hsl(24.6 95% 53.1%)` (#FF7A1F)
  - Blur effects: `bg-primary/50 blur-3xl` pour les backgrounds
  - Ring effects: `ring-8 ring-primary/10` autour des icônes
  - Gradients: `from-primary to-orange-600 bg-clip-text text-transparent`
  - Spacing cohérent: `py-24 sm:py-32` pour sections
  - Cards avec borders subtils: `border-primary/20 hover:border-primary/40`

#### Packs de Workflows Premium
- **Création des landing pages** pour 34 packs de workflows
- **Structure**:
  - `/packs` - Liste avec filtres catégories (crypto, ia, marketing, business)
  - `/packs/{slug}` - Page détail avec pricing multi-devises
  - Backend: PackController (Inertia), Pack model, PackSeeder
  - Frontend: Index.tsx et Show.tsx avec composants ShadCN
- **Features**:
  - Filtrage par catégorie et tri (featured, popular, price)
  - Pagination
  - Currency toggle (EUR/USD)
  - FAQ avec Accordion component
  - Related packs suggestions
  - Scarcity indicators (copies limitées)
  - Trust badges (paiement sécurisé, livraison immédiate, garantie 30j)

#### Système de Documentation
- **Création du système de documentation persistante**
- Décision d'adopter une approche inspirée de Compound Engineering mais simplifiée
- Création de la structure `/docs` avec decisions.md, patterns.md, learnings/
