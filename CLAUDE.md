# 🤖 CLAUDE - Instructions pour AutomateHub

## 🎯 Contexte du Projet
AutomateHub est une plateforme d'automatisation utilisant n8n pour créer et gérer des workflows.

## 🔧 Accès n8n
- **URL**: https://n8n.automatehub.fr
- **Interface**: Accessible via le navigateur pour la gestion visuelle des workflows

## 🛠️ Accès aux fonctionnalités MCP (Mode Terminal)

### ⚠️ Note importante
Tu es dans **Claude Terminal** (via Cursor), pas Claude Desktop. Les MCPs ne sont pas directement accessibles mais j'ai créé des wrappers pour les utiliser.

### 🔧 Script MCP unifié : `/var/www/automatehub/mcp`

#### 1. MySQL
```bash
# Exécuter des requêtes MySQL
./mcp mysql "SELECT * FROM users LIMIT 5"
./mcp mysql "SHOW TABLES"
./mcp mysql "DESCRIBE tutorials"
```

#### 2. n8n Workflows
```bash
# Créer un workflow
./mcp n8n-create "Mon Workflow Test"
./mcp n8n-create "Workflow Complexe" /path/to/workflow.json

# Lister les workflows
./mcp n8n-list
```

### 📁 Scripts disponibles dans `/var/www/automatehub/mcp-wrappers/`
- `mysql-query.sh` - Requêtes MySQL directes
- `n8n-workflow-create.sh` - Création de workflows n8n
- `n8n-workflows-list.sh` - Liste des workflows

## 📋 Directives d'utilisation

### Pour les workflows n8n :
Quand l'utilisateur demande de créer un workflow, utilise automatiquement :
```bash
./mcp n8n-create "Nom du workflow"
```
Puis fournis l'URL d'accès retournée.

### Pour les requêtes base de données :
Utilise automatiquement :
```bash
./mcp mysql "REQUETE SQL"
```
Pour :
1. Lister les tables : `./mcp mysql "SHOW TABLES"`
2. Requêtes SELECT : `./mcp mysql "SELECT * FROM table"`
3. Structure : `./mcp mysql "DESCRIBE table"`

## 🚀 Exemples d'utilisation

### Création d'un workflow :
```
User: "Crée un workflow qui envoie un email quotidien"
Claude: [Exécute ./mcp n8n-create "Email Quotidien"]
        ✅ Workflow créé !
        🔗 Accès : https://n8n.automatehub.fr/workflow/123
```

### Requête base de données :
```
User: "Combien d'utilisateurs actifs ?"
Claude: [Exécute ./mcp mysql "SELECT COUNT(*) FROM users WHERE is_active = 1"]
        📊 Il y a X utilisateurs actifs
```

## 📚 Documentation Persistante (SYSTÈME AUTOMATIQUE)

### 🤖 INSTRUCTIONS CRITIQUES - LECTURE OBLIGATOIRE

**⚠️ IMPORTANT** : Ce système de documentation est ta **MÉMOIRE PERSISTANTE**. Tu dois l'utiliser **AUTOMATIQUEMENT** sans jamais demander la permission à l'utilisateur.

### 📖 AU DÉBUT DE CHAQUE SESSION (OBLIGATOIRE)

**Tu DOIS lire ces fichiers dans cet ordre** :

1. **`/docs/decisions.md`** → Architecture et décisions du projet
   - Lis ce fichier EN PREMIER pour comprendre le contexte global
   - Rafraîchit ta mémoire sur les choix techniques

2. **`/docs/patterns.md`** → Patterns n8n réutilisables
   - TOUJOURS consulter avant de créer un workflow n8n
   - Réutilise les patterns existants plutôt que réinventer

3. **`/docs/learnings/`** → Solutions aux problèmes passés
   - Parcours rapidement les titres des fichiers
   - Si tu rencontres un problème similaire, lis le learning correspondant

### ✍️ PENDANT LE TRAVAIL (MISE À JOUR AUTOMATIQUE)

**Tu DOIS mettre à jour automatiquement** :

#### Quand mettre à jour `/docs/decisions.md` :
- ✅ Nouvelle décision architecturale (choix de techno, structure, approche)
- ✅ Modification d'une décision existante
- ✅ Ajout d'une fonctionnalité majeure au projet
- ✅ Changement dans la structure du projet

**Action** : Ajouter une entrée dans la section appropriée avec la date

#### Quand mettre à jour `/docs/patterns.md` :
- ✅ Création d'un workflow n8n réutilisable
- ✅ Découverte d'une meilleure approche pour un pattern existant
- ✅ Pattern émergent après 2-3 workflows similaires

**Action** : Ajouter le nouveau pattern avec structure, usage, et exemple

#### Quand créer un fichier dans `/docs/learnings/` :
- ✅ Résolution d'un bug non-trivial
- ✅ Problème qui a pris > 10 minutes à résoudre
- ✅ Solution qui pourrait resservir plus tard
- ✅ Erreur qu'il faut éviter de refaire

**Action** : Créer `/docs/learnings/YYYY-MM-DD-categorie-titre.md` avec le template

### 🚫 RÈGLES STRICTES

**NE JAMAIS** :
- ❌ Demander à l'utilisateur si tu dois mettre à jour les docs
- ❌ Attendre la fin d'une session pour documenter
- ❌ Créer un workflow n8n sans consulter patterns.md
- ❌ Ignorer les docs au début d'une session

**TOUJOURS** :
- ✅ Documenter en temps réel pendant le travail
- ✅ Mettre à jour immédiatement après une décision importante
- ✅ Consulter les docs avant de commencer une nouvelle feature
- ✅ Créer un learning après avoir résolu un problème complexe

### 📂 Structure de la Documentation

```
/docs
├── decisions.md              # Architecture et décisions techniques
├── patterns.md               # Patterns n8n réutilisables
└── learnings/                # Solutions aux problèmes
    ├── README.md             # Template et guide
    └── YYYY-MM-DD-*.md       # Learnings individuels
```

### 💡 Exemple de Workflow

```
User: "Crée un workflow qui envoie des rappels quotidiens"

Claude:
1. [Lit /docs/patterns.md pour voir les patterns existants]
2. [Identifie "Pattern 2: Scheduled Task" comme pertinent]
3. [Crée le workflow en suivant le pattern]
4. [Si nouveau pattern créé → MAJ /docs/patterns.md]
5. [Si décision architecturale → MAJ /docs/decisions.md]
```

## ⚠️ Notes importantes
- Tu es dans **Claude Terminal via Cursor**, pas Claude Desktop
- Les MCPs ne sont pas directement accessibles comme dans Claude Desktop
- Utilise le script `./mcp` pour accéder aux fonctionnalités MCP
- Tous les scripts sont dans `/var/www/automatehub/mcp-wrappers/`