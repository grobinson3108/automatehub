# Commandes de Développement - AutomateHub

## Commandes Laravel Essentielles

### Développement Local
```bash
# Démarrer le serveur de développement complet
composer dev
# Équivalent: php artisan serve + queue + logs + npm run dev

# Tests
composer test
# Équivalent: php artisan test

# Artisan commands
php artisan migrate
php artisan db:seed
php artisan queue:work
php artisan cache:clear
```

### Frontend (React/Vite)
```bash
# Développement
npm run dev

# Build production
npm run build
npm run build:ssr

# Linting & Formatting
npm run lint
npm run format
npm run format:check
npm run types
```

### MCP & n8n Integration
```bash
# Scripts MCP wrapper
./mcp mysql "SELECT * FROM workflows"
./mcp n8n-list
./mcp n8n-create "New Workflow"

# n8n Direct
./n8n-start.sh
```

### Workflow Translation
```bash
# Scripts de traduction
python3 scripts/workflow_translator/extract_texts.py <workflow.json>
python3 scripts/workflow_translator/translate_with_openai.py <texts.json>
python3 scripts/workflow_translator/apply_translations.py <translations.json>
```

## Structure des Répertoires

### Backend
- `app/Models/` - Modèles Eloquent
- `app/Http/Controllers/` - Contrôleurs
- `app/Services/` - Logique métier
- `routes/` - Définition des routes
- `database/` - Migrations et seeders

### Frontend  
- `resources/js/` - Code React/TypeScript
- `resources/js/pages/` - Pages Inertia.js
- `resources/css/` - Styles TailwindCSS

### Scripts Utilitaires
- `scripts/workflow_translator/` - Traduction workflows
- `mcp-wrappers/` - Scripts MCP
- `workflows/` - Collections workflows

## Variables d'Environnement

### Développement
```bash
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=automatehub

# APIs
OPENAI_API_KEY=sk-...
N8N_API_URL=https://n8n.automatehub.fr
STRIPE_SECRET=sk_test_...
```

### Production
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://automatehub.fr

# Queue & Cache
QUEUE_CONNECTION=database
CACHE_DRIVER=redis
```

## Conventions de Code

### PHP (Laravel)
- PSR-12 coding standard
- Noms de méthodes en camelCase
- Noms de classes en PascalCase
- Docblocks pour méthodes publiques

### TypeScript/React
- Functional components avec hooks
- Props interfaces définies
- Naming en camelCase
- Prettier pour formatage

### Base de Données
- Noms de tables en snake_case pluriel
- Clés étrangères: `model_id`
- Timestamps: `created_at`, `updated_at`
- Soft deletes: `deleted_at`