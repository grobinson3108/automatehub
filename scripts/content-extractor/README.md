# Content Extractor API - Alternative à Dumpling AI

## 🎯 Description
Une alternative gratuite et auto-hébergée à Dumpling AI (49€/mois) pour extraire du contenu depuis YouTube et des pages web.

## 💰 Modèles économiques proposés

### Pour AutomateHub (vous)
1. **Inclus dans l'abonnement Premium** (67€/mois) - Usage illimité
2. **Pay-as-you-go** : 0,10€ par extraction
3. **Packs de crédits** :
   - 100 extractions : 8€ (0,08€/extraction)
   - 500 extractions : 35€ (0,07€/extraction)
   - 1000 extractions : 60€ (0,06€/extraction)

### Comparaison avec Dumpling AI
- Dumpling AI : 49€/mois
- Content Extractor avec 500 extractions/mois : 35€ (économie de 14€)
- Inclus dans Premium AutomateHub : 67€/mois avec TOUTES les fonctionnalités

## 🚀 Fonctionnalités

### 1. Extraction YouTube (`/api/v1/get-youtube-transcript`)
- Transcriptions avec timestamps
- Support multi-langues
- Regroupement intelligent des segments
- Compatible avec tous les formats d'URL YouTube

### 2. Scraping Web (`/api/v1/scrape`)
- Extraction du contenu principal
- Conversion en Markdown propre
- Support JavaScript (pages dynamiques)
- Métadonnées (titre, auteur, date)

### 3. Gestion des crédits
- Système de clés API
- Tracking d'usage
- Historique détaillé
- Gestion des crédits

## 📦 Installation

```bash
cd /var/www/automatehub/scripts/content-extractor
sudo ./setup.sh
```

## 🔧 Configuration dans n8n

### 1. Remplacer les appels Dumpling

Dans le node HTTP Request, remplacez :
- URL : `https://app.dumplingai.com/api/v1/get-youtube-transcript`
- Par : `http://localhost:5678/api/v1/get-youtube-transcript`

### 2. Headers
```json
{
  "Authorization": "Bearer VOTRE_CLE_API"
}
```

### 3. Body (identique à Dumpling)
```json
{
  "videoUrl": "https://youtube.com/watch?v=...",
  "includeTimestamps": true,
  "timestampsToCombine": 5,
  "preferredLanguage": "fr"
}
```

## 🔑 Gestion des clés API

### Créer une nouvelle clé
```bash
curl -X POST http://localhost:5678/api/v1/admin/create-api-key \
  -H "X-Admin-Token: ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "client@example.com",
    "name": "Nom Client",
    "initial_credits": 100,
    "subscription_type": "pay-as-you-go"
  }'
```

### Vérifier les crédits
```bash
curl http://localhost:5678/api/v1/credits \
  -H "Authorization: Bearer CLE_API"
```

## 📊 Intégration facturation

Le système enregistre automatiquement :
- Chaque appel API
- Les crédits consommés
- L'historique d'usage

Vous pouvez intégrer ces données avec votre système de facturation existant via la table SQLite `usage_logs`.

## 🛡️ Sécurité

1. **Changez les tokens par défaut** dans `/etc/systemd/system/content-extractor.service`
2. **Utilisez HTTPS** en production (via nginx reverse proxy)
3. **Limitez les accès** par IP si nécessaire
4. **Surveillez l'usage** pour détecter les abus

## 🤝 Support

Pour vos clients Premium :
- Documentation complète
- Support prioritaire
- Mises à jour incluses
- Usage illimité

## 💡 Exemples d'usage

### YouTube
```python
import requests

response = requests.post(
    "http://localhost:5678/api/v1/get-youtube-transcript",
    headers={"Authorization": "Bearer YOUR_API_KEY"},
    json={
        "videoUrl": "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
        "includeTimestamps": True,
        "preferredLanguage": "fr"
    }
)
print(response.json())
```

### Web Scraping
```python
response = requests.post(
    "http://localhost:5678/api/v1/scrape",
    headers={"Authorization": "Bearer YOUR_API_KEY"},
    json={
        "url": "https://example.com/article",
        "format": "markdown",
        "cleaned": True
    }
)
print(response.json())