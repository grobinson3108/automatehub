# 🚀 GUIDE D'INSTALLATION - Marketing Automation n8n Cloud

## 📋 PRÉREQUIS

### 1. **Comptes nécessaires** ✅
- ✅ n8n Cloud (https://n8n.io)
- ✅ Google Account (Sheets + Drive)
- ✅ OpenAI Account (https://platform.openai.com)
- ✅ Blotato Account (https://blotato.com)
- ✅ Supabase Account (https://supabase.com) - GRATUIT

### 2. **APIs à récupérer** 🔑
- OpenAI API Key
- Blotato API Key + Account IDs (LinkedIn, Facebook, Instagram, TikTok)
- Supabase Database URL + Password

---

## 🗂️ ÉTAPE 1: CRÉER LA GOOGLE SHEET

### Structure de la feuille (colonnes exactes) :

| ID | Pack_Name | Platform | Post_Idea_Number | Title | Description | Hook_Angle | Benefits | CTA | Price | Target_Use_Case | Complexity | Published | Publication_Date |
|----|-----------|----------|------------------|-------|-------------|------------|----------|-----|-------|-----------------|------------|-----------|------------------|

### Instructions :
1. Va sur https://sheets.google.com
2. Crée une nouvelle feuille : "Marketing Posts Database"
3. Nomme la première feuille "Sheet1"
4. Ajoute les 14 colonnes ci-dessus en ligne 1
5. Remplis avec tes posts marketing (voir exemple ci-dessous)

### Exemple de ligne :
```
1 | CRYPTO_DEXSCREENER_MILLIONAIRE | LinkedIn | 1 | Devenez pro du trading crypto | Automatisez votre analyse DexScreener avec n8n | 🚀 Imaginez analyser 1000 tokens en 5 minutes | ✅ Alertes temps réel ✅ Analyse automatique ✅ Gains maximisés | Découvrez le pack maintenant ! | 67 | Traders crypto | Avancé | Non |
```

---

## 🔐 ÉTAPE 2: CONFIGURER LES CREDENTIALS DANS N8N CLOUD

### 2.1 Google Sheets OAuth2

1. Va dans **Settings → Credentials** dans n8n Cloud
2. Clique sur **Create New**
3. Cherche "Google Sheets OAuth2 API"
4. Clique sur **Connect my account**
5. Autorise l'accès à Google Sheets
6. Sauvegarde le credential

### 2.2 Google Drive OAuth2

1. **Create New → Google Drive OAuth2 API**
2. **Connect my account**
3. Autorise l'accès à Google Drive
4. Sauvegarde

### 2.3 OpenAI API

1. Va sur https://platform.openai.com/api-keys
2. Crée une nouvelle clé API
3. **Copie la clé** (tu ne pourras plus la revoir !)
4. Dans n8n: **Create New → OpenAI**
5. Colle ton API Key
6. Sauvegarde

### 2.4 Blotato API (Custom Credential)

**Important** : Crée un credential **HTTP Header Auth** personnalisé

1. **Create New → HTTP Header Auth**
2. **Name**: `blotato`
3. **Header Name**: `Authorization`
4. **Header Value**: `Bearer VOTRE_CLE_API_BLOTATO`
5. Ajoute des **Additional Fields** pour les Account IDs :
   - `linkedinAccountId` : ton ID compte LinkedIn Blotato
   - `facebookAccountId` : ton ID compte Facebook Blotato
   - `instagramAccountId` : ton ID compte Instagram Blotato
   - `tiktokAccountId` : ton ID compte TikTok Blotato

**Comment récupérer les Account IDs Blotato ?**
- Va sur https://app.blotato.com/accounts
- Clique sur chaque compte social
- L'ID est dans l'URL : `https://app.blotato.com/accounts/[ID_ICI]`

### 2.5 Supabase PostgreSQL

1. Va sur https://supabase.com
2. Crée un nouveau projet (GRATUIT)
3. Note les infos de connexion :
   - **Host** : `db.[PROJECT-REF].supabase.co`
   - **Database** : `postgres`
   - **User** : `postgres`
   - **Password** : celui que tu as défini
   - **Port** : `5432`
   - **SSL** : Activé

4. Dans n8n: **Create New → Postgres**
5. Entre les informations
6. **SSL** → Activé
7. Teste la connexion
8. Sauvegarde

---

## 🗄️ ÉTAPE 3: CRÉER LA TABLE SUPABASE

### 3.1 Ouvre le SQL Editor de Supabase

1. Va dans ton projet Supabase
2. Clique sur **SQL Editor** (menu gauche)
3. Clique sur **New query**

### 3.2 Copie-colle ce SQL :

```sql
-- Table pour l'historique des publications
CREATE TABLE IF NOT EXISTS n8n_posts_history (
  id SERIAL PRIMARY KEY,
  post_id VARCHAR(50) NOT NULL,
  pack_name VARCHAR(255),
  platform VARCHAR(50) NOT NULL,
  title TEXT,
  publication_date TIMESTAMP DEFAULT NOW(),
  image_url TEXT,
  blotato_response JSONB,
  content TEXT,
  created_at TIMESTAMP DEFAULT NOW()
);

-- Index pour recherches rapides
CREATE INDEX idx_post_id ON n8n_posts_history(post_id);
CREATE INDEX idx_platform ON n8n_posts_history(platform);
CREATE INDEX idx_publication_date ON n8n_posts_history(publication_date);

-- Commentaire
COMMENT ON TABLE n8n_posts_history IS 'Historique des publications automatiques n8n';
```

### 3.3 Exécute la requête

- Clique sur **Run** (ou Ctrl+Enter)
- Vérifie que "Success. No rows returned" s'affiche
- ✅ Ta table est créée !

---

## 📥 ÉTAPE 4: IMPORTER LE WORKFLOW DANS N8N CLOUD

### 4.1 Télécharge le workflow

Le fichier JSON est ici : `/var/www/automatehub/n8n_cloud_marketing_automation.json`

### 4.2 Importe dans n8n Cloud

1. Va sur https://app.n8n.cloud
2. Clique sur **Workflows** (menu gauche)
3. Clique sur **Import from File**
4. Sélectionne le fichier `n8n_cloud_marketing_automation.json`
5. Le workflow s'ouvre automatiquement

### 4.3 Configure les credentials dans chaque node

**Important** : Tu dois remplacer les credentials dans ces nodes :

#### Node "Read Google Sheets Posts"
- Clique sur le node
- **Credential for Google Sheets** → Sélectionne ton credential Google Sheets
- **Document** → Choisis "Marketing Posts Database"
- **Sheet** → Sélectionne "Sheet1"

#### Node "Generate Image (DALL-E 3)"
- **Credential for OpenAI** → Sélectionne ton credential OpenAI

#### Node "Upload to Google Drive"
- **Credential for Google Drive** → Sélectionne ton credential Google Drive
- **Drive** → Sélectionne "My Drive"
- **Folder** → Sélectionne le dossier de destination

#### Node "Make Image Public"
- **Credential for Google Drive** → Même credential que Upload

#### Nodes "Publish to [Platform]"
- Ces nodes utilisent HTTP Request avec l'auth Blotato
- Vérifie que `$credentials.blotato.apiKey` fonctionne
- Vérifie les Account IDs : `$credentials.blotato.linkedinAccountId`, etc.

#### Node "Update Google Sheets Status"
- **Credential for Google Sheets** → Même que Read
- **Document** → "Marketing Posts Database"
- **Sheet** → "Sheet1"

#### Node "Log to Supabase"
- **Credential for Postgres** → Sélectionne ton credential Supabase

---

## ⚙️ ÉTAPE 5: CONFIGURATION DU SCHEDULE

### Modifier le planning de publication

Le workflow est configuré pour publier **3 fois par jour** :
- 10h00
- 14h00
- 18h00

Pour changer :
1. Clique sur le node **"Schedule Trigger"**
2. Modifie l'expression cron : `0 10,14,18 * * *`
   - `0 10 * * *` → Une fois à 10h
   - `0 */2 * * *` → Toutes les 2 heures
   - `0 9,12,15,18 * * *` → 4 fois par jour (9h, 12h, 15h, 18h)

---

## ✅ ÉTAPE 6: TESTER LE WORKFLOW

### Test manuel complet

1. Clique sur **Test workflow** (en haut à droite)
2. Clique sur **Execute Workflow**
3. Observe chaque node s'exécuter
4. Vérifie :
   - ✅ Un post est sélectionné
   - ✅ Une image est générée
   - ✅ L'image est uploadée sur Drive
   - ✅ Le post est publié sur la bonne plateforme
   - ✅ Le statut est mis à jour dans Sheets
   - ✅ L'historique est enregistré dans Supabase

### Vérifications finales

1. **Google Sheets** : La colonne "Published" est passée à "Oui" ?
2. **Google Drive** : L'image est bien uploadée ?
3. **Plateforme sociale** : Le post est visible ?
4. **Supabase** : Une ligne a été ajoutée dans `n8n_posts_history` ?

---

## 🚀 ÉTAPE 7: ACTIVER LE WORKFLOW

1. Clique sur **Active** (toggle en haut à droite)
2. Le workflow devient vert
3. ✅ Il s'exécutera automatiquement selon le schedule !

---

## 🔧 DÉPANNAGE

### Erreur "No posts available"
→ Vérifie que ta Google Sheet contient des posts avec `Published = Non`

### Erreur OpenAI
→ Vérifie ton crédit OpenAI sur https://platform.openai.com/account/billing

### Erreur Blotato
→ Vérifie que :
- Ton API Key est valide
- Les Account IDs sont corrects
- Tes comptes sociaux sont bien connectés sur Blotato

### Erreur Supabase
→ Vérifie que :
- La table `n8n_posts_history` existe
- Les credentials PostgreSQL sont corrects
- SSL est activé

### Erreur Google Drive
→ Vérifie que le dossier de destination existe et que tu as les permissions

---

## 📊 STRUCTURE DONNÉES RECOMMANDÉE

### Google Sheets (exemples de posts)

```csv
ID,Pack_Name,Platform,Post_Idea_Number,Title,Description,Hook_Angle,Benefits,CTA,Price,Target_Use_Case,Complexity,Published,Publication_Date
1,CRYPTO_DEXSCREENER,LinkedIn,1,Automatisez votre trading crypto,Analysez 1000 tokens en 5 minutes avec n8n,🚀 Stop aux analyses manuelles !,✅ Alertes temps réel ✅ Analyse IA ✅ Profits optimisés,Téléchargez maintenant →,67,Traders crypto,Avancé,Non,
2,EMAIL_MARKETING,Facebook,1,Emails qui convertissent à 47%,IA qui rédige et envoie vos campagnes,💰 Imaginez 10000€/mois en automatique,✅ Rédaction IA ✅ Envoi auto ✅ Suivi temps réel,Découvrez le secret →,42,Marketeurs,Intermédiaire,Non,
3,TELEGRAM_BOT,Instagram,1,Bot Telegram qui vend pendant que tu dors,Automatisez vos ventes 24h/24,😴 Gagnez même en dormant,✅ Réponses IA ✅ Paiements auto ✅ Support H24,Essayez gratuitement →,52,Entrepreneurs,Avancé,Non,
```

---

## 🎯 RÉSULTAT ATTENDU

Toutes les 6 heures (ou selon ton schedule) :
1. ✅ Le workflow lit ta Google Sheet
2. ✅ Sélectionne intelligemment un post selon la rotation des plateformes
3. ✅ Génère un prompt optimisé pour l'image
4. ✅ Crée une image marketing professionnelle avec DALL-E 3
5. ✅ Upload l'image sur Google Drive
6. ✅ Publie le post avec l'image sur la plateforme choisie
7. ✅ Met à jour le statut "Published" dans Sheets
8. ✅ Log tout dans Supabase pour historique

---

## 💡 CONSEILS PRO

1. **Commence avec 10-20 posts** pour tester
2. **Vérifie les publications** les premiers jours
3. **Ajuste les prompts images** selon les résultats
4. **Surveille tes crédits OpenAI** (environ 0.04$ par image DALL-E 3 HD)
5. **Backup ta Google Sheet** régulièrement
6. **Utilise Supabase** pour analyser les performances par plateforme

---

## 📈 SCALING

Une fois que ça fonctionne :
- Ajoute plus de posts dans Sheets
- Augmente la fréquence de publication
- Clone le workflow pour d'autres projets
- Ajoute des variantes pour A/B testing

---

🎊 **Félicitations ! Ton système de marketing automation est prêt !** 🎊

Questions ? Vérifie les logs dans n8n Cloud → Executions
