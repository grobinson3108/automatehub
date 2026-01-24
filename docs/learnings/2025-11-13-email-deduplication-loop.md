# Email Deduplication avec Loop - AG-STEEL-MAILS

**Date**: 2025-11-13
**Workflow**: AG-STEEL-MAILS (Bw9xmE6oktHopPZY)
**Catégorie**: n8n, Email Processing, Deduplication

---

## 🎯 Problème

Le client AG Steel a un workflow qui récupère des emails non lus via IMAP Trigger. Le problème :

1. **Email Trigger récupère TOUS les emails non lus** en une seule fois (100+ mails)
2. **Pas de marquage "lu"** (demande du client) → risque de retraitement infini
3. **Traitement en masse** → tous les emails passent dans le workflow en même temps
4. **Pas de déduplication** → les mêmes emails sont traités à chaque exécution

### Impact
- Notifications Telegram en double/triple
- Surcharge de l'API OpenAI (GPT-4.1-mini)
- Coûts inutiles
- Confusion pour le client

---

## 💡 Solution Implémentée

### Architecture Modifiée

```
[Email Trigger (IMAP)]
    ↓
[Format Email1]
    ↓
[Get row(s) in sheet] ← Récupère les emails déjà traités (AGSteel New Mail)
    ↓
[Filter New Emails] ← 🆕 Function node - Compare et filtre
    ↓
[IF: Has New Emails?] ← 🆕 Vérifie s'il y a des nouveaux
    ↓ (OUI)
[Split In Batches] ← 🆕 Traite un par un (batch size = 1)
    ↓
[Sheet Spam] ← Vérifie spam
    ↓
[Vérif Spam] ← Switch
    ↓
[Envoyer Mails] ← Agent AI
    ↓
[Append row in sheet1] ← Log dans AGSteel New Mail (messageId)
    ↓
[Append row in sheet] ← Log dans AG Steel (données complètes)
    ↓
[Send to Telegram2]
```

### 3 Nouveaux Nœuds

#### 1. **Filter New Emails** (Function Node)

**Position**: Après `Get row(s) in sheet`

**Code**:
```javascript
// 🔄 Filter New Emails - Déduplication
const emails = $('Format Email1').all().map(item => item.json);
const processedSheet = $('Get row(s) in sheet').all();
const processedIds = processedSheet.map(item => item.json.email).filter(id => id);

console.log('📊 Emails reçus:', emails.length);
console.log('📋 Emails déjà traités:', processedIds.length);

const newEmails = emails.filter(email => {
  const emailId = email.messageId;
  const isProcessed = processedIds.includes(emailId);

  if (isProcessed) {
    console.log('⏭️  Email déjà traité:', emailId);
  } else {
    console.log('✨ Nouvel email:', emailId);
  }

  return !isProcessed;
});

console.log('🆕 Nouveaux emails à traiter:', newEmails.length);

if (newEmails.length === 0) {
  return [];
}

return newEmails.map(email => ({ json: email }));
```

**Rôle**:
- Compare les `messageId` des emails reçus avec ceux du Sheet
- Ne garde que les emails NON présents dans le Sheet
- Logs détaillés pour debugging

#### 2. **IF: Has New Emails?** (IF Node)

**Position**: Après `Filter New Emails`

**Condition**: `{{ $json }}` exists

**Rôle**:
- Vérifie s'il y a des nouveaux emails à traiter
- Si NON → Stop le workflow (pas d'exécution inutile)
- Si OUI → Continue vers le loop

#### 3. **Split In Batches** (Split In Batches Node)

**Position**: Après `IF: Has New Emails?`

**Configuration**:
- Batch Size: `1` (traite un email à la fois)
- Mode: `automatic`

**Rôle**:
- Traite les emails séquentiellement (un par un)
- Évite la surcharge de l'API
- Permet un meilleur contrôle des erreurs

---

## 📋 Modifications des Nœuds Existants

### 1. **Envoyer Mails** (Agent AI)

**Avant**:
```
ID : {{ $('Format Email1').item.json.id }}
```

**Après**:
```
ID : {{ $('Split In Batches').item.json.id }}
```

**Raison**: Le contexte change, on utilise maintenant le batch

### 2. **Append row in sheet** (Google Sheets)

**Avant**:
```
Nom: {{ $('Format Email1').item.json.cleanedFromName }}
```

**Après**:
```
Nom: {{ $('Split In Batches').item.json.cleanedFromName }}
```

**Raison**: Même raison, référence au batch

### 3. **Append row in sheet1** (Google Sheets - AGSteel New Mail)

**Colonnes**:
- `email`: `{{ $('Split In Batches').item.json.messageId }}`
- `Date`: `{{ $now.format('dd/MM/yyyy') }}`
- `Heure`: `{{ $now.format('HH:mm:ss') }}`
- `Envoyée`: `OUI`

**Rôle**: Enregistre le `messageId` pour éviter le retraitement

---

## 🔑 Points Clés

### 1. Utilisation du `messageId`
- **Clé unique** pour chaque email
- Plus fiable que `id` (généré par le workflow)
- Permet une déduplication précise

### 2. Structure du Sheet "AGSteel New Mail"

| Colonne | Type | Exemple | Rôle |
|---------|------|---------|------|
| email | messageId | `<CAB...@mail.gmail.com>` | Clé de déduplication |
| Date | dd/MM/yyyy | `13/11/2025` | Traçabilité |
| Heure | HH:mm:ss | `14:32:10` | Traçabilité |
| Envoyée | OUI/NON | `OUI` | Statut |

### 3. Ordre d'exécution

**Important**: L'ordre des nœuds est crucial :
1. Récupérer TOUS les emails (Format Email1)
2. Récupérer la liste des traités (Get row(s) in sheet)
3. Filtrer (Filter New Emails)
4. Vérifier s'il y a des nouveaux (IF)
5. Looper (Split In Batches)
6. Traiter chaque email
7. Logger dans le Sheet (Append row in sheet1)

⚠️ Le logging doit se faire **APRÈS** le traitement réussi, sinon un email en erreur sera marqué comme traité !

---

## 🎬 Déploiement

### Étapes

1. **Sauvegarder le workflow actuel** (backup)
2. **Télécharger le JSON modifié**: `/var/www/automatehub/AG-STEEL-MAILS-FIXED.json`
3. **Importer dans n8n**:
   - Ouvrir https://n8n.automatehub.fr
   - Workflow "AG-STEEL-MAILS"
   - Trois points → **Import from File**
   - Choisir `AG-STEEL-MAILS-FIXED.json`
4. **Vérifier les credentials** (Google Sheets, Telegram, OpenAI, IMAP)
5. **Tester** avec quelques emails
6. **Activer** le workflow

### Vérification

```bash
# Logs n8n à surveiller
console.log('📊 Emails reçus:', ...)
console.log('📋 Emails déjà traités:', ...)
console.log('🆕 Nouveaux emails à traiter:', ...)
```

---

## 📊 Résultats Attendus

### Avant
- 100 emails non lus → 100 notifications Telegram
- Retraitement à chaque exécution
- Coûts API élevés

### Après
- 100 emails non lus → 100 notifications (première fois)
- Puis 0 notification (déjà traités)
- Coûts optimisés
- Traitement séquentiel (évite les timeouts)

---

## 🚨 Points d'Attention

### 1. Performance
- Si 100+ nouveaux emails → le workflow prendra du temps
- Considérer une limite (ex: traiter max 50 emails par run)

### 2. Gestion d'Erreur
- Si un email échoue, il ne sera PAS ajouté au Sheet
- Il sera retraité au prochain run
- **C'est voulu** → retry automatique

### 3. Nettoyage du Sheet
- Le Sheet "AGSteel New Mail" peut grossir indéfiniment
- Considérer un nettoyage périodique (supprimer mails > 30 jours)

### 4. Limitation API IMAP
- Certains providers IMAP limitent le nombre de requêtes
- Considérer un délai entre les runs du workflow

---

## 🔗 Ressources

- **Pattern documenté**: `/docs/patterns.md` - Pattern 9
- **Workflow modifié**: `/var/www/automatehub/AG-STEEL-MAILS-FIXED.json`
- **Interface n8n**: https://n8n.automatehub.fr
- **Google Sheet**: AGSteel New Mail (ID: 11Q1iV4ksrRNOR9_Ag6YXprsM9ZAmQT0CfTpFMNS2dp0)

---

## 📝 Notes

### Alternative: Marquer les emails comme lus

Si le client accepte finalement de marquer les emails comme lus, on peut :
1. Supprimer le système de Sheet
2. Utiliser le flag `\Seen` IMAP
3. Simplifier le workflow

### Amélioration Future: Batch Processing

Au lieu de `batch size = 1`, on pourrait :
- Traiter par lots de 10
- Paralléliser certaines étapes
- Gain de temps significatif

Mais pour l'instant, la solution séquentielle est plus sûre et plus simple.
