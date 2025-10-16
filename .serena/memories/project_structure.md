# Structure du Projet AutomateHub

## Vue d'ensemble
AutomateHub est une plateforme d'automatisation utilisant n8n pour créer et gérer des workflows. Le projet combine Laravel (backend) avec React/Inertia.js (frontend) et inclut des fonctionnalités complètes de gestion de workflows, tutoriels, et monétisation.

## Tech Stack
- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: React 19, TypeScript, Inertia.js 2.0
- **UI**: TailwindCSS 4.0, Radix UI, Shadcn/ui
- **Base de données**: MySQL (avec Laravel Eloquent)
- **Build**: Vite 6.0
- **Automation**: n8n (https://n8n.automatehub.fr)
- **AI Integration**: OpenAI API pour traduction workflows

## Structure du Code

### Backend (Laravel)
- **Models**: Workflow, Tutorial, User, Category, etc.
- **Services**: Scripts de traduction, gestion workflows
- **API**: Intégrations n8n, MCPs (Model Context Protocol)

### Frontend (React)
- **Pages**: Structure Inertia.js dans resources/js/Pages/
- **Components**: Composants Radix UI + TailwindCSS
- **Types**: TypeScript pour type safety

## Fonctionnalités Principales
1. **Gestion Workflows n8n**
   - Import/export workflows
   - Traduction automatique (FR/EN)
   - Catégorisation (Freemium/Premium)

2. **Système de Tutoriels**
   - Niveaux: gratuit, premium, pro
   - Progression utilisateur
   - Téléchargements trackés

3. **Monétisation**
   - Abonnements Skool
   - Packs de crédits
   - API payantes

4. **Gestion Utilisateurs**
   - Authentification Laravel
   - Badges et progression
   - Analytics détaillées