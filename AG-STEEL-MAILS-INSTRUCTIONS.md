# 🚀 AG-STEEL-MAILS - Instructions d'Import et Test

## 📦 Fichiers Générés

1. **`AG-STEEL-MAILS-FIXED.json`** → Workflow modifié avec loop et déduplication
2. **`docs/learnings/2025-11-13-email-deduplication-loop.md`** → Documentation complète

---

## 🎯 Ce Qui a Été Modifié

### ✅ 3 Nouveaux Nœuds

1. **Filter New Emails** (Function) → Compare et filtre les emails déjà traités
2. **IF: Has New Emails?** → Vérifie s'il y a des nouveaux emails
3. **Split In Batches** → Traite les emails un par un (batch size = 1)

### 🔧 Modifications des Nœuds Existants

- **Envoyer Mails**: Référence maintenant `Split In Batches` au lieu de `Format Email1`
- **Append row in sheet**: Idem
- **Append row in sheet1**: Enregistre le `messageId` pour la déduplication

---

## 📋 Comment Importer le Workflow

### Option 1 : Import Total (Recommandé)

1. **Sauvegarder l'ancien workflow** (backup)
   - Va sur https://n8n.automatehub.fr
   - Ouvre "AG-STEEL-MAILS"
   - Trois points → **Download**
   - Sauvegarde le fichier (backup)

2. **Désactiver le workflow actuel**
   - Clique sur le toggle "Active" pour le désactiver

3. **Supprimer l'ancien workflow**
   - Trois points → **Delete**

4. **Importer le nouveau**
   - Workflows → **Import from File**
   - Choisis `/var/www/automatehub/AG-STEEL-MAILS-FIXED.json`
   - Clique sur **Import**

5. **Vérifier les credentials**
   - Google Sheets account
   - Telegram account
   - OpenAI account
   - IMAP account

6. **Renommer si nécessaire**
   - Le workflow sera importé avec le nom original
   - Tu peux le renommer si besoin

7. **Activer le workflow**
   - Clique sur le toggle "Active"

### Option 2 : Ajout Manuel (Plus Long)

Si tu préfères ajouter les nœuds manuellement :

1. **Ajouter "Filter New Emails"** (Code node)
   - Position : après "Get row(s) in sheet"
   - Copier le code depuis le JSON

2. **Ajouter "IF: Has New Emails?"** (IF node)
   - Position : après "Filter New Emails"
   - Condition : `{{ $json }}` exists

3. **Ajouter "Split In Batches"**
   - Position : après "IF"
   - Batch Size : 1

4. **Reconnecter tous les nœuds** selon le schéma dans le learning

5. **Modifier les expressions** dans "Envoyer Mails" et "Append"

---

## 🧪 Comment Tester

### Test 1 : Vérifier la Déduplication

1. **Vider le Sheet "AGSteel New Mail"** (optionnel, pour test propre)

2. **Exécuter le workflow manuellement**
   - Va sur le workflow
   - Clique sur **Execute Workflow**

3. **Vérifier les logs**
   - Ouvre la console du navigateur (F12)
   - Cherche les logs :
     ```
     📊 Emails reçus: X
     📋 Emails déjà traités: Y
     🆕 Nouveaux emails à traiter: Z
     ```

4. **Vérifier le Sheet "AGSteel New Mail"**
   - Il doit contenir les `messageId` des emails traités
   - Colonnes : email | Date | Heure | Envoyée

5. **Réexécuter le workflow**
   - Les mêmes emails ne doivent PAS être retraités
   - Logs : `🆕 Nouveaux emails à traiter: 0`

### Test 2 : Vérifier le Loop

1. **Vérifier que les emails sont traités un par un**
   - Dans n8n, clique sur "Split In Batches"
   - Vérifie qu'il n'y a qu'un seul email à la fois

2. **Vérifier les notifications Telegram**
   - Tu dois recevoir UNE notification par email (pas toutes d'un coup)

### Test 3 : Vérifier la Gestion des Spams

1. **Ajouter un email spam** dans le Sheet "AG Steel Spams"

2. **Envoyer un email depuis cette adresse**

3. **Vérifier que le workflow** filtre bien le spam

---

## 🔍 Points de Vérification

### ✅ Checklist Avant Activation

- [ ] Workflow importé sans erreur
- [ ] Toutes les credentials sont valides
- [ ] Le nœud "Filter New Emails" est bien connecté
- [ ] Le nœud "IF: Has New Emails?" est bien connecté
- [ ] Le nœud "Split In Batches" est bien connecté (batch size = 1)
- [ ] Les expressions dans "Envoyer Mails" référencent `Split In Batches`
- [ ] Les expressions dans "Append row in sheet" référencent `Split In Batches`
- [ ] Le Sheet "AGSteel New Mail" existe et a les bonnes colonnes

### ✅ Checklist Après Premier Run

- [ ] Emails traités un par un (pas tous en masse)
- [ ] Sheet "AGSteel New Mail" contient les `messageId`
- [ ] Pas de doublons dans Telegram
- [ ] Réexécution → 0 nouveaux emails (si pas de nouveaux mails)

---

## 🔧 Structure du Sheet "AGSteel New Mail"

Si tu n'as pas encore créé le Sheet, voici la structure :

| Colonne | Type | Exemple |
|---------|------|---------|
| email | Texte | `<CAB123...@mail.gmail.com>` |
| Date | Texte | `13/11/2025` |
| Heure | Texte | `14:32:10` |
| Envoyée | Texte | `OUI` |

**ID du Sheet** : `11Q1iV4ksrRNOR9_Ag6YXprsM9ZAmQT0CfTpFMNS2dp0`

---

## 🚨 Troubleshooting

### Problème 1 : "Filter New Emails" ne filtre pas

**Solution** :
- Vérifier que "Get row(s) in sheet" récupère bien les données
- Vérifier la colonne "email" dans le Sheet
- Vérifier les logs console (`console.log`)

### Problème 2 : Tous les emails sont traités en masse

**Solution** :
- Vérifier que "Split In Batches" a bien `Batch Size = 1`
- Vérifier que le nœud est bien connecté après "IF"

### Problème 3 : Emails traités en double

**Solution** :
- Vérifier que "Append row in sheet1" est bien APRÈS le traitement
- Vérifier que le `messageId` est bien enregistré

### Problème 4 : Le workflow ne s'arrête jamais

**Solution** :
- Vérifier que "IF: Has New Emails?" est bien configuré
- Vérifier la condition : `{{ $json }}` exists

---

## 📊 Schéma du Workflow Final

```
[Email Trigger (IMAP)] → Récupère tous les mails non lus
    ↓
[Format Email1] → Formate et nettoie
    ↓
[Get row(s) in sheet] → Récupère les IDs déjà traités
    ↓
[Filter New Emails] 🆕 → Compare et filtre
    ↓
[IF: Has New Emails?] 🆕 → Vérifie s'il y a des nouveaux
    ↓ (OUI)
[Split In Batches] 🆕 → Traite un par un
    ↓
[Sheet Spam] → Vérifie spam
    ↓
[Vérif Spam] → Switch
    ↓
[Envoyer Mails] → Agent AI
    ↓
[Append row in sheet1] → Log dans AGSteel New Mail (messageId)
    ↓
[Append row in sheet] → Log dans AG Steel (données complètes)
    ↓
[Send to Telegram2] → Notif Telegram
```

---

## 💡 Conseils

### Performance

- Si tu as 100+ nouveaux emails, le premier run sera long
- Considère limiter à 50 emails max par run si nécessaire

### Nettoyage

- Le Sheet "AGSteel New Mail" peut grossir
- Pense à nettoyer les vieux emails (> 30 jours) périodiquement

### Monitoring

- Surveille les logs console pour détecter les anomalies
- Vérifie régulièrement le Sheet pour voir les emails traités

---

## 📞 Support

Si tu rencontres un problème :
1. Vérifie les logs console (F12)
2. Vérifie les logs n8n (Executions)
3. Consulte `/docs/learnings/2025-11-13-email-deduplication-loop.md`

---

**Bonne chance ! 🚀**
