# 🔧 Fix Triple Message - AG Steel Workflow

## 🚨 Problème identifié

Le workflow envoie **3 messages Telegram** au lieu d'un seul car il y a 3 nodes Telegram qui s'exécutent tous :

1. **Send Draft2** (connecté à Switch2 → Brouillon)
2. **Send a text message** (connecté à Switch2 → Question)
3. **Send Draft3** (connecté à Update row in sheet1)

---

## ✅ Solution

### Architecture correcte :

```
Code2
  ↓
Get row(s) in sheet3
  ↓
Get Contact
  ↓
Switch5 (détecte Modif vs Spontané)
  ├─ Modif → AI Agent (Réponse)
  │            ↓
  │         Update row in sheet1
  │            ↓
  │         Send Draft (UNIQUE pour Réponse)
  │
  └─ Spontané → AI Agent1 (Création)
                   ↓
                Switch2 (détecte type: brouillon vs question)
                   ├─ Brouillon → Send Draft2
                   └─ Question → Send a text message
```

---

## 🔧 Actions à effectuer dans n8n

### 1. Vérifier le node "Send Draft3"

**Configuration actuelle (à corriger) :**
- Position : [880, -32]
- ID : 17f2d411-9164-4a83-8b65-a438d2b51836
- Connecté à : "Update row in sheet1"

**Problème :** Ce node utilise `$json['Email Préparé']` qui vient du Sheet, pas de l'agent.

**Correction :**

```javascript
// Text du message
📝 Brouillon de réponse :
━━━━━━━━━━━━━━━
{{ $('AI Agent').item.json.output.brouillon }}
━━━━━━━━━━━━━━━

// Callback data des boutons
edit_{{ $('AI Agent').item.json.output.mailId }}
draft_{{ $('AI Agent').item.json.output.mailId }}
send_{{ $('AI Agent').item.json.output.mailId }}
```

---

### 2. Vérifier le node "Send Draft2"

**Configuration actuelle :**
- Position : [1136, 832]
- ID : daba53b8-e851-4f7c-8e15-e4f00440dfa6
- Connecté à : Switch2 → Brouillon

**Correction :**

```javascript
// Text du message
📝 Brouillon créé :
━━━━━━━━━━━━━━━
{{ $json.output.content }}
━━━━━━━━━━━━━━━

// Callback data des boutons
edit_{{ $json.output.mailId }}
draft_{{ $json.output.mailId }}
send_{{ $json.output.mailId }}
```

---

### 3. Vérifier les connexions

**Connexions correctes :**

1. **AI Agent** → **Update row in sheet1** → **Send Draft3** ✅
2. **AI Agent1** → **Switch2** :
   - **Brouillon** → **Send Draft2** ✅
   - **Question** → **Send a text message** ✅

**Connexions à supprimer :**
- Aucune connexion directe entre "AI Agent" et "Send Draft2"
- Aucune connexion directe entre "AI Agent1" et "Send Draft3"

---

## 📋 Checklist de vérification

### Dans n8n, vérifie :

- [ ] Le node "AI Agent" est connecté UNIQUEMENT à "Update row in sheet1"
- [ ] Le node "Update row in sheet1" est connecté UNIQUEMENT à "Send Draft3"
- [ ] Le node "AI Agent1" est connecté UNIQUEMENT à "Switch2"
- [ ] Le node "Switch2" a 2 sorties :
  - [ ] "Brouillon" → "Send Draft2"
  - [ ] "Question" → "Send a text message"
- [ ] Aucun autre node Telegram n'est connecté

---

## 🧪 Test après correction

### Test 1 : Modification (Mode ChatGPT ou Emrah)
1. Envoie un message de modification via Telegram
2. Le workflow doit passer par : Code2 → Get row → Get Contact → Switch5 (Modif) → AI Agent → Update row → Send Draft3
3. Tu dois recevoir **1 seul message** avec le brouillon

### Test 2 : Création spontanée (contact trouvé)
1. Envoie un message de création via Telegram (mailId vide)
2. Le workflow doit passer par : Code2 → Get row (échoue) → Get Contact → Switch5 (Spontané) → AI Agent1 → Switch2 (Brouillon) → Send Draft2
3. Tu dois recevoir **1 seul message** avec le brouillon

### Test 3 : Création spontanée (contact introuvable)
1. Envoie un message de création avec un nom inconnu
2. Le workflow doit passer par : ... → AI Agent1 → Switch2 (Question) → Send a text message
3. Tu dois recevoir **1 seul message** avec la question de clarification

---

## 🎯 Résultat attendu

Après correction :
- **1 seul message** envoyé à chaque exécution
- Le bon message selon le mode et le type de sortie
- Pas de duplication ni de triple envoi

---

## 💡 Explication du bug

Le problème vient du fait que les 3 nodes Telegram sont tous actifs et connectés au workflow. Quand le workflow s'exécute, n8n envoie les données à tous les nodes connectés, ce qui fait que :

1. **Send Draft3** reçoit les données de "Update row in sheet1"
2. **Send Draft2** reçoit les données de "Switch2 → Brouillon"
3. **Send a text message** peut aussi recevoir des données si le Switch2 le déclenche

La solution est de **séparer complètement les flows** :
- Flow Réponse → Send Draft3 uniquement
- Flow Création → Send Draft2 ou Send a text message uniquement

---

## 📝 Notes importantes

### Structure JSON attendue

**Pour AI Agent (Réponse) :**
```json
{
  "output": {
    "brouillon": "Le texte...",
    "mailId": "abc123"
  }
}
```

**Pour AI Agent1 (Spontané) :**
```json
{
  "output": {
    "type": "brouillon",
    "content": "Le texte...",
    "mailId": "abc123"
  }
}
```
OU
```json
{
  "output": {
    "type": "question",
    "content": "La question...",
    "mailId": null
  }
}
```

### Accès aux données dans les nodes Telegram

**Send Draft3** (après AI Agent) :
- `{{ $('AI Agent').item.json.output.brouillon }}`
- `{{ $('AI Agent').item.json.output.mailId }}`

**Send Draft2** (après Switch2 → Brouillon) :
- `{{ $json.output.content }}`
- `{{ $json.output.mailId }}`

**Send a text message** (après Switch2 → Question) :
- `{{ $json.output.content }}`

---

**Bon courage pour la correction ! 🚀**
