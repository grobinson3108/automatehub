# Mise à jour nginx pour le port 5682

## Éditez le fichier nginx :
```bash
sudo nano /etc/nginx/sites-available/automatehub.fr.conf
```

## Changez la ligne proxy_pass dans le bloc location :

**Remplacez :**
```nginx
proxy_pass http://localhost:5680/;
```

**Par :**
```nginx
proxy_pass http://localhost:5682/;
```

## Puis rechargez nginx :
```bash
sudo nginx -t
sudo systemctl reload nginx
```

## Test final :
```bash
curl https://automatehub.fr/api/content-extractor/health
```