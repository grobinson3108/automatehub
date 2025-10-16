# 🚀 Configuration Production pour n8n Cloud

## Option 1 : Via Nginx (Recommandé) 

### 1. Ajouter à votre configuration nginx

```bash
sudo nano /etc/nginx/sites-available/automatehub.fr.conf
```

Ajoutez avant la dernière accolade du bloc `server` HTTPS :

```nginx
    # Content Extractor API
    location /api/content-extractor/ {
        proxy_pass http://localhost:5680/;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
```

### 2. Démarrer l'API

```bash
cd /var/www/automatehub/scripts/content-extractor
source env/bin/activate

# Générer une clé sécurisée
export CONTENT_EXTRACTOR_API_KEY=$(openssl rand -hex 32)
echo "Votre clé API: $CONTENT_EXTRACTOR_API_KEY"

# Démarrer l'API
export PORT=5680
nohup python3 api-server-simple.py > api.log 2>&1 &
```

### 3. Recharger nginx

```bash
sudo nginx -t
sudo systemctl reload nginx
```

### 4. Tester

```bash
curl https://automatehub.fr/api/content-extractor/health
```

## 🔗 URLs pour n8n Cloud

Dans vos workflows n8n, utilisez :

- **YouTube** : `https://automatehub.fr/api/content-extractor/api/v1/get-youtube-transcript`
- **Scraping** : `https://automatehub.fr/api/content-extractor/api/v1/scrape`

## 🔑 Configuration n8n

1. Dans n8n Cloud, allez dans **Settings > Variables**
2. Ajoutez :
   - Name: `CONTENT_EXTRACTOR_API_KEY`
   - Value: La clé générée ci-dessus

## Option 2 : Cloudflare Tunnel (Alternative)

Si vous ne pouvez pas modifier nginx :

```bash
# Installer cloudflared
wget -q https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
sudo dpkg -i cloudflared-linux-amd64.deb

# Démarrer l'API
cd /var/www/automatehub/scripts/content-extractor
source env/bin/activate
export CONTENT_EXTRACTOR_API_KEY="votre-cle-securisee"
export PORT=5680
python3 api-server-simple.py &

# Créer le tunnel
cloudflared tunnel --url http://localhost:5680
```

Cloudflare vous donnera une URL comme : `https://random-name.trycloudflare.com`

## 📝 Test dans n8n

### Node HTTP Request

**URL** : `https://automatehub.fr/api/content-extractor/api/v1/get-youtube-transcript`

**Headers** :
```json
{
  "Authorization": "Bearer {{$vars.CONTENT_EXTRACTOR_API_KEY}}",
  "Content-Type": "application/json"
}
```

**Body** :
```json
{
  "videoUrl": "https://www.youtube.com/watch?v=VIDEO_ID",
  "preferredLanguage": "fr",
  "includeTimestamps": true,
  "timestampsToCombine": 5
}
```

## 🛡️ Sécurité

1. **Limitez les IPs** (optionnel) :
```nginx
location /api/content-extractor/ {
    # Autoriser seulement n8n Cloud
    allow 34.89.0.0/16;  # n8n Cloud EU
    allow 35.157.0.0/16; # n8n Cloud US
    deny all;
    
    proxy_pass http://localhost:5680/;
    # ... reste de la config
}
```

2. **Surveillez l'usage** :
```bash
tail -f /var/www/automatehub/scripts/content-extractor/api.log
```

## 🚨 Dépannage

### L'API ne répond pas
```bash
# Vérifier si le processus tourne
ps aux | grep api-server

# Voir les logs
tail -100 api.log

# Redémarrer
pkill -f api-server-simple.py
./start-api.sh
```

### Erreur 502 Bad Gateway
- Vérifiez que l'API est bien sur le port 5680
- Vérifiez les logs nginx : `sudo tail -f /var/log/nginx/error.log`

### Erreur d'autorisation
- Vérifiez la clé API dans n8n
- Assurez-vous d'utiliser "Bearer " avant la clé