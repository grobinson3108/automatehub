# Système de Documentation Persistante

**Date**: 2025-10-16
**Contexte**: Setup initial du projet AutomateHub
**Sévérité**: 🟢 Mineure (Amélioration)

## 🐛 Problème

Claude perdait le contexte après compactation, ce qui causait :
- Perte des décisions d'architecture
- Re-exploration du code à chaque session
- Incohérences dans les approches
- Temps perdu à réapprendre la structure du projet

## 🔍 Cause Racine

Pas de système de mémoire persistante entre les sessions Claude. Le contexte de conversation est effacé lors de la compactation, et seuls les fichiers du projet restent.

## ✅ Solution

Création d'un système de documentation légère inspiré de Compound Engineering :

### Structure créée

```
/docs
  ├── decisions.md         # Décisions d'architecture
  ├── patterns.md          # Patterns n8n réutilisables
  └── learnings/           # Solutions aux problèmes
      ├── README.md        # Template et guide
      └── [fichiers].md    # Learnings individuels
```

### Instructions ajoutées à CLAUDE.md

```markdown
## 📚 Documentation Persistante (SYSTÈME AUTOMATIQUE)

### 🤖 INSTRUCTIONS AUTOMATIQUES POUR CLAUDE

**AU DÉBUT DE CHAQUE SESSION** :
1. Lire `/docs/decisions.md` pour comprendre l'architecture
2. Consulter `/docs/patterns.md` avant de créer un workflow n8n
3. Parcourir `/docs/learnings/` si tu rencontres un problème similaire

**PENDANT LE TRAVAIL** :
- Quand tu crées un pattern n8n réutilisable → MAJ `/docs/patterns.md`
- Quand tu prends une décision architecturale → MAJ `/docs/decisions.md`
- Quand tu résous un problème non-trivial → Créer `/docs/learnings/YYYY-MM-DD-titre.md`

**IMPORTANT** : Ne JAMAIS demander à l'utilisateur si tu dois mettre à jour ces docs.
Le faire AUTOMATIQUEMENT quand c'est pertinent.
```

### Code/Commandes

```bash
# Création de la structure
mkdir -p /var/www/automatehub/docs/learnings

# Création des fichiers de base
touch /var/www/automatehub/docs/decisions.md
touch /var/www/automatehub/docs/patterns.md
touch /var/www/automatehub/docs/learnings/README.md
```

## 🎓 Leçons Apprises

1. **La simplicité est essentielle** : Pas besoin d'un framework complet comme Compound Engineering, une structure simple suffit
2. **Automatiser l'utilisation** : Les docs sont inutiles si Claude ne les utilise pas automatiquement
3. **Documentation = Code** : La doc doit être traitée comme du code, avec les mêmes standards de qualité
4. **Patterns > Code** : Documenter les patterns est plus utile que documenter du code spécifique
5. **Contexte persistant** : La vraie valeur est dans le "pourquoi", pas le "comment"

## 🔗 Références

- Vidéo Compound Engineering (transcription dans `/var/www/automatehub/NewClaudeCodeSystème.md`)
- CLAUDE.md pour les instructions complètes
