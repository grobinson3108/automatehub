# 📦 Guide d'installation Content Extractor

## ⚠️ Prérequis

### 1. Installer Python pip
```bash
sudo apt-get update
sudo apt-get install -y python3-pip python3-venv
```

### 2. Installer les dépendances Python
```bash
# Option 1: Installation globale (plus simple)
sudo pip3 install youtube-transcript-api requests beautifulsoup4 html2text

# Option 2: Avec environnement virtuel (recommandé)
cd /var/www/automatehub/scripts/content-extractor
python3 -m venv venv
source venv/bin/activate
pip install youtube-transcript-api requests beautifulsoup4 html2text
```

## 🚀 Démarrage rapide (sans service)

### 1. Test simple
```bash
cd /var/www/automatehub/scripts/content-extractor

# Définir les variables
export CONTENT_EXTRACTOR_API_KEY="votre-cle-api-securisee"
export PORT=5679  # ou tout autre port libre

# Démarrer l'API
python3 api-server-simple.py
```

### 2. Tester l'API
```bash
# Health check
curl http://localhost:5679/health

# Tester l'extraction YouTube
curl -X POST http://localhost:5679/api/v1/get-youtube-transcript \
  -H "Authorization: Bearer votre-cle-api-securisee" \
  -H "Content-Type: application/json" \
  -d '{
    "videoUrl": "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
    "preferredLanguage": "fr"
  }'
```

## 🔧 Installation complète avec service

### 1. Installer toutes les dépendances
```bash
# Flask et dépendances avancées
sudo pip3 install flask flask-cors selenium

# Chrome pour Selenium (optionnel)
wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
sudo sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list'
sudo apt-get update
sudo apt-get install -y google-chrome-stable

# ChromeDriver
sudo apt-get install -y chromium-chromedriver
```

### 2. Configurer le service
```bash
# Éditer le service
sudo nano /etc/systemd/system/content-extractor.service

# Modifier les variables d'environnement :
Environment="CONTENT_EXTRACTOR_API_KEY=votre-cle-securisee"
Environment="ADMIN_TOKEN=votre-token-admin"
Environment="PORT=5679"

# Sauvegarder et quitter (Ctrl+X, Y, Enter)
```

### 3. Démarrer le service
```bash
sudo systemctl daemon-reload
sudo systemctl start content-extractor
sudo systemctl enable content-extractor
sudo systemctl status content-extractor
```

## 🔌 Configuration n8n

### 1. Ajouter la variable d'environnement dans n8n
```bash
# Éditer la configuration n8n
sudo nano /etc/systemd/system/n8n.service

# Ajouter :
Environment="CONTENT_EXTRACTOR_API_KEY=votre-cle-securisee"

# Redémarrer n8n
sudo systemctl restart n8n
```

### 2. Dans le workflow n8n

Remplacez les URLs Dumpling par :
- YouTube : `http://localhost:5679/api/v1/get-youtube-transcript`
- Scraping : `http://localhost:5679/api/v1/scrape`

Header d'authentification :
```
Authorization: Bearer {{$env.CONTENT_EXTRACTOR_API_KEY}}
```

## 🐛 Dépannage

### Port déjà utilisé
```bash
# Voir ce qui utilise un port
sudo lsof -i :5679

# Changer le port dans les variables d'environnement
```

### Permissions
```bash
# Donner les permissions au user www-data
sudo chown -R www-data:www-data /var/www/automatehub/scripts/content-extractor
sudo chown -R www-data:www-data /var/www/automatehub/data
```

### Logs
```bash
# Voir les logs du service
sudo journalctl -u content-extractor -f

# Logs de n8n
sudo journalctl -u n8n -f
```

## 💰 Monétisation

### Pour vos clients
1. **Premium** : Inclus dans l'abonnement 67€/mois
2. **Pay-as-you-go** : 0,10€ par extraction
3. **Packs** : 100 extractions pour 8€

### Créer une clé API pour un client
```bash
curl -X POST http://localhost:5679/api/v1/admin/create-api-key \
  -H "X-Admin-Token: votre-token-admin" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "client@example.com",
    "name": "Nom Client",
    "initial_credits": 100
  }'
```

## ✅ Checklist d'installation

- [ ] Python 3 et pip installés
- [ ] Dépendances Python installées
- [ ] Port configuré et libre
- [ ] Clé API sécurisée générée
- [ ] Service systemd configuré (optionnel)
- [ ] Variable n8n ajoutée
- [ ] Workflow importé et testé

## 📞 Support

Pour toute question : contact@automatehub.fr