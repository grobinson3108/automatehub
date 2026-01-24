# 🎨 Patterns n8n - AutomateHub

> **IMPORTANT**: Ce fichier contient les patterns réutilisables pour les workflows n8n. Claude doit le consulter avant de créer un nouveau workflow et le mettre à jour quand un nouveau pattern est créé.

## 📅 Dernière mise à jour
**Date**: 2025-11-13

---

## 🧩 Patterns de Base

### Pattern 1: Webhook Trigger → Action
**Usage**: Workflows déclenchés par un webhook externe
**Structure**:
```
[Webhook Trigger] → [Validation Data] → [Action Node] → [Response]
```
**Cas d'usage**:
- Réception de données depuis une application externe
- Webhooks GitHub, Stripe, etc.

**Exemple**:
```json
{
  "nodes": [
    {"type": "n8n-nodes-base.webhook", "name": "Webhook"},
    {"type": "n8n-nodes-base.function", "name": "Validate"},
    {"type": "n8n-nodes-base.http", "name": "Action"},
    {"type": "n8n-nodes-base.respondToWebhook", "name": "Response"}
  ]
}
```

---

### Pattern 2: Scheduled Task
**Usage**: Tâches planifiées récurrentes
**Structure**:
```
[Cron Trigger] → [Fetch Data] → [Process] → [Store/Notify]
```
**Cas d'usage**:
- Envoi d'emails quotidiens
- Synchronisation de données
- Rapports automatiques

**Exemple**:
```json
{
  "nodes": [
    {"type": "n8n-nodes-base.cron", "name": "Schedule", "cron": "0 9 * * *"},
    {"type": "n8n-nodes-base.mysql", "name": "Fetch Data"},
    {"type": "n8n-nodes-base.function", "name": "Process"},
    {"type": "n8n-nodes-base.emailSend", "name": "Send Email"}
  ]
}
```

---

### Pattern 3: Database Sync
**Usage**: Synchronisation entre bases de données ou systèmes
**Structure**:
```
[Trigger] → [Fetch Source] → [Transform] → [Update Target] → [Log]
```
**Cas d'usage**:
- Sync CRM → Base locale
- Import/Export de données
- Migration de données

---

## 🔧 Patterns Avancés

### Pattern 4: Error Handling
**Usage**: Gestion d'erreurs robuste
**Structure**:
```
[Action Node] → [On Success] → [Success Path]
              ↓ [On Error] → [Log Error] → [Notify Admin] → [Retry/Fallback]
```
**Best Practices**:
- Toujours logger les erreurs
- Implémenter un système de retry avec backoff
- Notifier les admins pour les erreurs critiques

---

### Pattern 5: API Rate Limiting
**Usage**: Respect des limites d'API externes
**Structure**:
```
[Trigger] → [Queue Management] → [Rate Limiter] → [API Call] → [Process Response]
```
**Best Practices**:
- Utiliser des delays entre les appels
- Implémenter un système de queue
- Logger les retry attempts

---

## 🏪 Patterns Métier (Commerce Local)

### Pattern 6: Google Business Auto-Post
**Usage**: Publication automatique sur Google Business
**Structure**:
```
[Schedule] → [Generate Content] → [Google Business API] → [Log Success]
```
**Variables à configurer**:
- Fréquence de publication
- Type de contenu (promo, événement, info)
- Location ID (pour multi-locations)

---

### Pattern 7: Review Response Automation
**Usage**: Réponse automatique aux avis clients
**Structure**:
```
[Review Webhook] → [Sentiment Analysis] → [Generate Response] → [Approval?] → [Post Response]
```
**Sécurité**:
- Toujours inclure une étape d'approbation humaine pour les avis négatifs
- Logger toutes les réponses

---

### Pattern 8: Pharmacy Reminder System
**Usage**: Rappels de renouvellement d'ordonnances
**Structure**:
```
[Daily Check] → [Check Expiry Dates] → [Filter Due Soon] → [Send SMS/Email] → [Update Status]
```
**Compliance**:
- Respecter les règles RGPD
- Sécuriser les données médicales
- Permettre opt-out facile

---

### Pattern 9: Email Processing with Deduplication
**Usage**: Traiter des emails sans les marquer comme lus, avec déduplication via Google Sheets
**Structure**:
```
[Email Trigger] → [Get Processed IDs from Sheet] → [Filter New Only] → [Loop Over Items] → [Process] → [Log to Sheet]
```
**Cas d'usage**:
- Client ne veut pas marquer les emails comme lus
- Traitement d'un volume important de mails
- Besoin de traçabilité des mails traités

**Implémentation**:
```javascript
// Function Node: Filter New Emails
const emails = $input.item.json;
const processedIds = $('Get row(s) in sheet').all().map(item => item.json.emailId);
const newEmails = emails.filter(email => !processedIds.includes(email.id));
return newEmails.map(email => ({ json: email }));
```

**Best Practices**:
- Utiliser l'ID unique du mail (messageId) comme clé
- Logger la date de traitement dans le Sheet
- Ajouter un nœud IF pour vérifier s'il y a de nouveaux mails avant le loop
- Utiliser "Loop Over Items" avec batch size = 1 pour traitement séquentiel
- Considérer un TTL dans le Sheet (nettoyer les vieux mails après X jours)

**Sheet Structure**:
```
| emailId | subject | receivedDate | processedDate | status |
```

---

## 📋 Checklist Création de Workflow

Avant de créer un workflow, vérifier :
- [ ] Est-ce qu'un pattern existant correspond ?
- [ ] Gestion d'erreurs implémentée ?
- [ ] Logging suffisant ?
- [ ] Variables d'environnement configurées ?
- [ ] Tests effectués ?
- [ ] Documentation ajoutée ?

---

## 🎯 Conventions de Nommage

### Workflows
- **Format**: `[Catégorie] - [Action] - [Cible]`
- **Exemples**:
  - `Marketing - Auto Post - Google Business`
  - `Sync - Import - Customer Data`
  - `Notification - Reminder - Prescription`

### Nodes
- **Format**: Descriptif et court
- **Exemples**:
  - `Fetch Users`
  - `Validate Email`
  - `Send Notification`

---

## 🔄 Template de Base

```json
{
  "name": "[Nom du Workflow]",
  "nodes": [
    {
      "type": "trigger-node",
      "name": "Trigger",
      "parameters": {}
    },
    {
      "type": "function-node",
      "name": "Process",
      "parameters": {
        "functionCode": "// Code here"
      }
    },
    {
      "type": "action-node",
      "name": "Action",
      "parameters": {}
    }
  ],
  "connections": {
    "Trigger": {"main": [[{"node": "Process", "type": "main", "index": 0}]]},
    "Process": {"main": [[{"node": "Action", "type": "main", "index": 0}]]}
  }
}
```

---

## 📝 Notes

### Quand ajouter un nouveau pattern ?
- Quand un workflow est créé et qu'il est réutilisable
- Quand une solution innovante est trouvée
- Quand un pattern générique émerge de plusieurs workflows similaires

### Comment documenter un pattern ?
1. Donner un nom clair
2. Décrire l'usage
3. Montrer la structure
4. Donner des cas d'usage concrets
5. Ajouter des exemples de code/config si pertinent
