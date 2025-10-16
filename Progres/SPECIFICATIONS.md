# 📋 CAHIER DES CHARGES - AUTOMATEHUB.FR

## 🎯 CONTEXTE DU PROJET

**Nom :** Automatehub  
**Domaine :** https://automatehub.fr  
**Tech Stack :** Laravel 12 + React + MySQL + OneUI (backend) + Style libre (frontend)  
**Objectif :** Plateforme d'apprentissage n8n avec système freemium

## 🏗️ ARCHITECTURE TECHNIQUE

### Base de données MySQL
- **Nom :** automatehub
- **User :** automatehub_user
- **Localisation :** /var/www/automatehub/

### Structure des dossiers
/var/www/automatehub/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Frontend/
│   │   │   │   ├── HomeController.php
│   │   │   │   ├── TutorialController.php
│   │   │   │   ├── DownloadController.php
│   │   │   │   ├── BlogController.php
│   │   │   │   └── ContactController.php
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── UserManagementController.php
│   │   │   │   ├── TutorialManagementController.php
│   │   │   │   ├── BlogManagementController.php
│   │   │   │   ├── AnalyticsController.php
│   │   │   │   └── FinanceController.php
│   │   │   └── User/
│   │   │       ├── DashboardController.php
│   │   │       ├── TutorialController.php
│   │   │       ├── DownloadController.php
│   │   │       ├── BadgeController.php
│   │   │       ├── NotificationController.php
│   │   │       └── SubscriptionController.php
│   │   └── Middleware/
│   │       ├── IsAdmin.php
│   │       └── IsPremium.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Tutorial.php
│   │   ├── Category.php
│   │   ├── Tag.php
│   │   ├── Download.php
│   │   ├── Badge.php
│   │   ├── Subscription.php
│   │   ├── ActivityLog.php
│   │   └── N8nLevel.php
│   └── Services/
│       ├── AnalyticsService.php
│       ├── BadgeService.php
│       ├── TutorialService.php
│       ├── RestrictionService.php
│       ├── NotificationService.php
│       └── N8nLevelService.php
├── database/
│   └── migrations/
├── public/
│   └── oneui/              # Thème OneUI5 installé
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── frontend/
│   │   │   ├── backend/
│   │   │   └── auth/
│   │   ├── frontend/
│   │   ├── admin/
│   │   ├── user/
│   │   └── auth/
│   └── js/
│       ├── components/
│       └── pages/
└── routes/
├── web.php
├── admin.php
└── user.php

## 🔐 STRATÉGIE D'ACCÈS

### Frontend Public
- Accessible à tous (visiteurs non inscrits)
- Aperçus des tutoriels pour inciter à l'inscription
- Call-to-action inscription partout

### Espace User (Inscription obligatoire)
- **TOUS** les utilisateurs doivent s'inscrire (même pour le gratuit)
- Accès selon subscription_type :
  - **Free** : tutoriels gratuits + téléchargements limités
  - **Premium** : tout le contenu free + tutoriels premium
  - **Pro** : accès complet + tutoriels "sur demande" + compte entreprise

### Objectif Marketing
- Récupération d'emails pour nurturing
- Parcours de conversion free → premium → pro
- Analytics précises sur l'engagement utilisateur

## 🧭 NAVIGATION BACKEND

### Admin Sidebar
📊 Dashboard
👥 Gestion Utilisateurs
├── Tous les utilisateurs
├── Abonnements
├── Activités
└── Niveaux n8n
📚 Gestion Contenu
├── Tutoriels
├── Articles Blog
├── Catégories
├── Tags
└── Fichiers
💰 Finances
├── Tableau de bord
├── Transactions
├── Factures
└── Rapports
✉️ Messages Contact
⚙️ Paramètres

### User Sidebar
🏠 Dashboard
📚 Tutoriels
├── Gratuits
├── Premium
├── Sur demande
├── Mes favoris
└── Historique
📥 Téléchargements
🏆 Niveau & Badges
├── Mon niveau n8n
├── Mes badges
├── Progression
└── Quiz d'évaluation
--- Footer Links ---
🔔 Notifications
💳 Abonnement
⚙️ Préférences

## 📋 PHASES DE DÉVELOPPEMENT

### Phase 1 : Infrastructure de base ✅
- [x] Configuration serveur nginx + SSL
- [x] Installation Laravel 12
- [x] Configuration base de données MySQL
- [x] Installation thème OneUI
- [x] Structure des dossiers
- [x] Configuration des routes
- [x] Middlewares de base

### Phase 2 : Modèles et Migrations ✅
- [x] Création des modèles
- [x] Migrations base de données
- [x] Seeders de test

### Phase 3 : Authentification ✅
- [x] Système login/register avec OneUI
- [x] Option "Professionnel" dans le formulaire d'inscription avec champs supplémentaires (entreprise, adresse, etc.)
- [x] Quiz niveau n8n
- [x] Gestion des rôles

### Phase 4 : Services métier ✅
- [x] AnalyticsService complet
- [x] BadgeService avec système de progression
- [x] TutorialService avec recommandations
- [x] RestrictionService pour système freemium
- [x] NotificationService pour emails
- [x] Event Listeners configurés

### Phase 5 : Frontend Public ✅
- [x] Layout principal
- [x] Pages statiques
- [x] Système de blog
- [x] Affichage tutoriels

### Phase 6 : Backend Admin ✅
- [x] Dashboard analytics
- [x] Gestion utilisateurs
- [x] Gestion contenu
- [x] Système de badges

### Phase 7 : Backend User ✅
- [x] Dashboard personnalisé
- [x] Accès tutoriels
- [x] Téléchargements
- [x] Progression

### Phase 8 : Système Premium
- [ ] Intégration paiements
- [ ] Gestion abonnements
- [ ] Restrictions d'accès

## ✅ HISTORIQUE DES RÉALISATIONS

**Date : 23/05/2025**
- [x] Configuration serveur nginx + SSL
- [x] Installation Laravel 12
- [x] Configuration base de données MySQL
- [x] Installation thème OneUI dans /public/oneui/
- [x] Création de la structure des routes (web.php, admin.php, user.php)
- [x] Création des middlewares (IsAdmin.php, IsPremium.php)
- [x] Création des contrôleurs de base (Frontend, Admin, User)
- [x] Décision d'ajouter une option "Professionnel" dans le formulaire d'inscription avec champs supplémentaires (entreprise, adresse, code postal, ville, pays, n° TVA)
- [x] Implémentation de l'option "Professionnel" dans le formulaire d'inscription
- [x] Création des migrations pour ajouter les champs professionnels à la table users
- [x] Mise à jour du modèle User pour inclure les champs professionnels
- [x] Création des templates Blade pour les pages publiques (accueil, tutoriels, blog, contact, à propos)
- [x] Adaptation des routes pour utiliser les templates Blade au lieu d'Inertia.js

**Date : 26/05/2025**
- [x] Base de données complète créée
- [x] Modèles avec relations configurés
- [x] Distinction pro/particulier implémentée
- [x] Seeders de base exécutés
- [x] Système d'authentification personnalisé
- [x] Quiz niveau n8n intégré
- [x] Distinction pro/particulier à l'inscription
- [x] QuizService opérationnel
- [x] Attribution automatique des niveaux n8n selon quiz
- [x] Attribution automatique des badges de départ
- [x] Redirections personnalisées après login (admin/user)
- [x] Mise à jour last_activity_at à chaque connexion
- [x] Validation personnalisée pour quiz obligatoire
- [x] Company_name obligatoire si is_professional = true
- [x] Services métier principaux créés
- [x] Système de restrictions free/premium
- [x] Analytics et tracking implémentés
- [x] Event listeners configurés
- [x] Controllers Admin complets avec toutes méthodes
- [x] CRUD tutoriels avec upload fichiers
- [x] Gestion utilisateurs complète
- [x] Analytics admin opérationnelles
- [x] Form Requests de validation créées
- [x] Système de gestion des fichiers tutoriels
- [x] Controllers User complets avec restrictions
- [x] Système favoris et historique
- [x] Gestion profil avec pro/particulier
- [x] Téléchargements sécurisés avec limites
- [x] Controllers frontend publics complets
- [x] Blog avec gestion articles
- [x] Call-to-action inscription partout
- [x] SEO et performance optimisés

**Date : 27/05/2025**
- [x] Correction complète des erreurs SQL dans tous les controllers
- [x] Mise à jour des références aux colonnes de base de données
- [x] Gestion robuste des erreurs avec try/catch et logging
- [x] Création des vues manquantes pour éviter les erreurs 404
- [x] Dashboard admin fonctionnel avec analytics
- [x] Dashboard user personnalisé avec progression
- [x] Interface de paramètres admin complète
- [x] Gestion des messages de contact (structure)
- [x] Analytics dashboard avec graphiques et métriques
- [x] Méthodes manquantes ajoutées (financeDashboard, contacts)
- [x] Correction des références aux colonnes (status → is_draft, difficulty_level → required_level, etc.)
- [x] États vides gérés avec messages informatifs
- [x] Navigation backend sécurisée et fonctionnelle

---
⚠️ **Note importante :** Ce fichier doit être lu au début de chaque session et mis à jour après chaque réalisation.
