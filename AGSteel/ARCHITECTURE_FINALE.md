# 🏗️ Architecture Finale - AG Steel Email System

## 📋 Vue d'ensemble

Le système est divisé en **2 agents distincts** avec leurs propres outils et workflows.

```
┌─────────────────────────────────────────────────────────────┐
│                    TELEGRAM TRIGGER                          │
│                   (Message utilisateur)                      │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
              ┌────────────────┐
              │  CODE: Parse   │
              │    Message     │
              └────────┬───────┘
                       │
                       ▼
              ┌────────────────┐
              │   SWITCH sur   │
              │  Champ "Mode"  │
              └────────┬───────┘
                       │
        ┌──────────────┴──────────────┐
        │                             │
        ▼                             ▼
┌───────────────┐            ┌────────────────┐
│ AgentReponse  │            │ AgentSpontane  │
│ (ChatGPT +    │            │ (Création      │
│  Emrah)       │            │  spontanée)    │
└───────┬───────┘            └────────┬───────┘
        │                             │
        └──────────────┬──────────────┘
                       │
                       ▼
              ┌────────────────┐
              │ CODE: Normalize│
              │    Output      │
              └────────┬───────┘
                       │
                       ▼
              ┌────────────────┐
              │ GOOGLE SHEETS  │
              │  (Save Draft)  │
              └────────┬───────┘
                       │
                       ▼
              ┌────────────────┐
              │    TELEGRAM    │
              │  (Send Draft)  │
              └────────────────┘
```

---

## 🔧 Agent 1 : AgentReponse (Mode ChatGPT + Emrah)

### Fichier prompt
`/var/www/automatehub/AGSteel/AgentReponse.md`

### Outils connectés
1. **GetContacts** (Google Sheets Tool)
   - Operation: Search
   - Sheet: AGSteelContacts
   - Returns: All contacts

2. **MakeContacts** (Google Sheets Tool)
   - Operation: Append
   - Sheet: AGSteelContacts
   - Columns: ID, Appellation, Nom, Adresse Mail, Tel, Société, Pays, Produit, Tu/Vous

### Input attendu (depuis Code node)
```javascript
{
  "Mode": "ChatGPT" | "Emrah",
  "ID_Mail": "mggto1et1tv",
  "Email Client": "greg@meep.fr",
  "Nom Contact": "Gregory Robinson",
  "Email Reçu": "Le contenu du mail original OU 'Email généré et non reçu'",
  "Réponse User": "Instructions d'Emrah",
  "Iterations": "0" | "1" | "2" ...,
  "Mail préparé": "Brouillon existant (vide si Iterations=0)"
}
```

### Output attendu
```json
{
  "brouillon": "Le texte complet du brouillon email avec signature",
  "mailId": "mggto1et1tv"
}
```

### Comportement
- **Mode ChatGPT** : Ton professionnel, signature courte
- **Mode Emrah** : Ton direct, signature complète
- **Iterations = 0** : Premier brouillon (avec ou sans historique email)
- **Iterations ≥ 1** : Modification chirurgicale du brouillon existant
- **Gestion contacts** : Appelle GetContacts, crée via MakeContacts si manquant

---

## 🔧 Agent 2 : AgentSpontane (Mode Spontané)

### Fichier prompt
`/var/www/automatehub/AGSteel/AgentSpontane.md`

### Outils connectés
1. **Simple Memory** (BufferWindow)
   - Garde contexte de conversation
   - Window Size: 5 messages

2. **FindContacts** (Google Sheets Tool)
   - Operation: Search
   - Sheet: AGSteelContacts
   - Filters: Nom OR ID
   - Usage: Recherche ciblée par nom

3. **AllContacts** (Google Sheets Tool)
   - Operation: Get All
   - Sheet: AGSteelContacts
   - Usage: Recherche exhaustive si FindContacts échoue

4. **CreateBrouillons** (Google Sheets Tool)
   - Operation: Append
   - Sheet: AGSteel (Feuille 1)
   - Columns: ID, Nom, Email Client, Email Reçu, Sujet, Mode, Iterations, Email Préparé, Date

### Input attendu
```javascript
{
  "Demande du user": "Crée un mail pour Greg pour...",
  "Date/Heure": "07/10/2025 14:30",
  "Entreprise": "AG Steel Trading",
  "Expéditeur": "Emrah GULER"
}
```

### Output attendu (2 types possibles)

**Type 1 - Brouillon créé avec succès :**
```json
{
  "type": "brouillon",
  "content": "Le texte complet du brouillon email avec signature",
  "mailId": "abc123def45g"
}
```

**Type 2 - Question/Clarification nécessaire :**
```json
{
  "type": "question",
  "content": "J'ai trouvé 3 contacts nommés Philippe. Lequel souhaitez-vous contacter ?",
  "mailId": null
}
```

### Comportement
- **Phase 1** : Recherche contact via FindContacts (nom/prénom)
- **Phase 2** : Si échec, recherche via AllContacts (exhaustive)
- **Si 1 contact trouvé** : Rédaction du brouillon + CreateBrouillons
- **Si plusieurs contacts** : Retourne question de clarification
- **Si aucun contact** : Retourne question pour plus d'infos
- **Détection Tu/Vous** : Basé sur indicateurs dans la demande

---

## 📦 Nodes supplémentaires requis

### 1. CODE: Parse Message (après Telegram Trigger)
```javascript
// Extraire les données du message Telegram
const message = $input.first().json.message.text;

// Parser selon le format attendu
// Exemple: "/reponse ID_Mail Instructions..."
const [command, mailId, ...instructions] = message.split(' ');

if (command === '/spontane') {
  return {
    json: {
      Mode: 'Spontané',
      'Demande du user': instructions.join(' '),
      'Date/Heure': new Date().toLocaleString('fr-FR'),
      'Entreprise': 'AG Steel Trading',
      'Expéditeur': 'Emrah GULER'
    }
  };
} else {
  // Récupérer les données du mail depuis Sheet
  return {
    json: {
      Mode: command === '/pro' ? 'ChatGPT' : 'Emrah',
      ID_Mail: mailId,
      // ... autres champs depuis Sheet lookup
    }
  };
}
```

### 2. SWITCH: Route by Mode
- **Condition 1** : `{{ $json.Mode === 'Spontané' }}` → AgentSpontane
- **Condition 2** : `{{ $json.Mode === 'ChatGPT' || $json.Mode === 'Emrah' }}` → AgentReponse

### 3. CODE: Normalize Output (après agents)
```javascript
// Récupérer la sortie de l'agent
const output = $input.first().json;

// Normaliser selon le type d'agent
let brouillon, mailId;

// Format AgentSpontane
if (output.type === 'brouillon') {
  brouillon = output.content;
  mailId = output.mailId;
}
// Format AgentSpontane (question)
else if (output.type === 'question') {
  return {
    json: {
      isQuestion: true,
      content: output.content
    }
  };
}
// Format AgentReponse (avec nested output possible)
else if (output.output?.output?.brouillon) {
  brouillon = output.output.output.brouillon;
  mailId = output.mailId;
} else if (output.output?.brouillon) {
  brouillon = output.output.brouillon;
  mailId = output.output.mailId || output.mailId;
} else if (output.brouillon) {
  brouillon = output.brouillon;
  mailId = output.mailId;
}

// Calculer les nouvelles iterations (si applicable)
let newIterations = '1';
if ($('Get row(s) in sheet3').first()) {
  const mailData = $('Get row(s) in sheet3').first().json;
  const currentIterations = parseInt(mailData.Iterations || '0');
  newIterations = (currentIterations + 1).toString();
}

return {
  json: {
    // Pour Telegram
    output: {
      brouillon: brouillon,
      mailId: mailId
    },
    // Pour Google Sheets
    sheetData: {
      ID: mailId,
      'Email Préparé': brouillon,
      'Iterations': newIterations
    }
  }
};
```

### 4. GOOGLE SHEETS: Save Draft (direct node, pas tool)
- **Operation** : Append or Update
- **Document** : AG Steel
- **Sheet** : Feuille 1
- **Column to Match On** : ID
- **Mapping** :
  - ID : `{{ $json.sheetData.ID }}`
  - Email Préparé : `{{ $json.sheetData['Email Préparé'] }}`
  - Iterations : `{{ $json.sheetData.Iterations }}`

### 5. IF: Is Question? (après Normalize Output)
- **Condition** : `{{ $json.isQuestion === true }}`
- **TRUE** → Send Question (Telegram)
- **FALSE** → Save Draft → Send Draft (Telegram)

### 6. TELEGRAM: Send Draft
```
✅ Brouillon préparé !

{{ $json.output.brouillon }}

MailID: {{ $json.output.mailId }}
```

### 7. TELEGRAM: Send Question
```
❓ {{ $json.content }}
```

---

## 🔄 Flows complets

### Flow 1 : Réponse (ChatGPT/Emrah)
```
Telegram Trigger
  ↓
CODE: Parse Message (détecte /pro ou /emrah)
  ↓
Get row(s) in sheet (récupère mail data)
  ↓
SWITCH (Mode = ChatGPT ou Emrah)
  ↓
AgentReponse (génère brouillon)
  ↓
CODE: Normalize Output
  ↓
GOOGLE SHEETS: Save Draft (direct)
  ↓
TELEGRAM: Send Draft
```

### Flow 2 : Création spontanée
```
Telegram Trigger
  ↓
CODE: Parse Message (détecte /spontane)
  ↓
SWITCH (Mode = Spontané)
  ↓
AgentSpontane (recherche contact + génère brouillon)
  ↓
CODE: Normalize Output
  ↓
IF: Is Question?
  ├─ TRUE → TELEGRAM: Send Question
  └─ FALSE → GOOGLE SHEETS: Save Draft → TELEGRAM: Send Draft
```

---

## ✅ Checklist d'implémentation

### Phase 1 : Préparation
- [ ] Lire AgentReponse.md
- [ ] Lire AgentSpontane.md
- [ ] Vérifier structure des Sheets (AGSteel, AGSteelContacts)

### Phase 2 : AgentReponse
- [ ] Créer agent LangChain "AgentReponse"
- [ ] Connecter outils : GetContacts, MakeContacts
- [ ] Copier prompt depuis AgentReponse.md
- [ ] Ajouter Structured Output Parser (optionnel)

### Phase 3 : AgentSpontane
- [ ] Créer agent LangChain "AgentSpontane"
- [ ] Ajouter Simple Memory (BufferWindow, size=5)
- [ ] Connecter outils : FindContacts, AllContacts, CreateBrouillons
- [ ] Copier prompt depuis AgentSpontane.md
- [ ] Ajouter Structured Output Parser avec schema { type, content, mailId }

### Phase 4 : Nodes de support
- [ ] CODE: Parse Message
- [ ] SWITCH: Route by Mode
- [ ] CODE: Normalize Output
- [ ] GOOGLE SHEETS: Save Draft (direct)
- [ ] IF: Is Question?
- [ ] TELEGRAM: Send Draft
- [ ] TELEGRAM: Send Question

### Phase 5 : Connexions
- [ ] Telegram → CODE Parse → SWITCH
- [ ] SWITCH → AgentReponse (ChatGPT/Emrah)
- [ ] SWITCH → AgentSpontane (Spontané)
- [ ] Agents → CODE Normalize → IF Question
- [ ] IF FALSE → GOOGLE SHEETS → TELEGRAM Send Draft
- [ ] IF TRUE → TELEGRAM Send Question

### Phase 6 : Tests
- [ ] Test création spontanée (contact trouvé)
- [ ] Test création spontanée (contact introuvable)
- [ ] Test création spontanée (plusieurs contacts)
- [ ] Test réponse mode ChatGPT (Iterations=0)
- [ ] Test réponse mode ChatGPT (Iterations≥1)
- [ ] Test réponse mode Emrah (Iterations=0)
- [ ] Test réponse mode Emrah (Iterations≥1)
- [ ] Vérifier sauvegarde dans Sheet après chaque test
- [ ] Vérifier incrémentation des Iterations

---

## 🎯 Avantages de cette architecture

| Avant | Après |
|-------|-------|
| ~89 nodes | ~15 nodes |
| Agent fait tout (génération + sauvegarde + contacts) | Agent génère, nodes sauvegardent |
| Bugs aléatoires (agent oublie de sauvegarder) | Sauvegarde garantie (node direct) |
| Difficile à debugger | Facile à debugger |
| Tools appelés par agent (peu fiable) | Nodes directs (100% fiable) |
| Prompt complexe (~1000 lignes) | Prompts ciblés (~400 lignes chacun) |

---

## 📌 Notes importantes

### Gestion des accents
Les accents sont gérés automatiquement par le système n8n UTF-8. Si problème persiste, ajouter un node Code avec `fixEncoding()`.

### Structured Output Parser
**Optionnel** pour AgentReponse (Code node peut normaliser).
**Recommandé** pour AgentSpontane (gère 2 types de sortie).

### Mémoire de conversation
Uniquement AgentSpontane a la mémoire (BufferWindow). AgentReponse n'en a pas besoin car tout le contexte est dans les champs reçus.

### Tu/Vous
- **AgentReponse** : Détecte via champ contact.Tu/Vous + analyse instructions
- **AgentSpontane** : Détecte via analyse demande user

### Signature
- **Mode ChatGPT** : Courte (Cordialement, Emrah GULER, AG Steel Trading)
- **Mode Emrah/Spontané** : Complète (avec Gsm, Email, Website)

---

## 🚀 Prochaines étapes recommandées

1. **Créer les 2 agents** dans n8n (AgentReponse + AgentSpontane)
2. **Créer les nodes de support** (CODE, SWITCH, IF, SHEETS)
3. **Tester Flow 2 en premier** (Création spontanée - plus simple)
4. **Puis tester Flow 1** (Réponses - plus complexe avec iterations)
5. **Valider la sauvegarde** dans Sheet après chaque action
6. **Déployer en production** une fois tous les tests OK

---

**Bon courage pour l'implémentation ! 💪**
