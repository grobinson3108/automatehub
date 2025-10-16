#!/bin/bash
# Script de configuration sécurisée pour production

echo "🔐 Configuration sécurisée de Content Extractor pour production"
echo ""

# 1. Générer une clé API sécurisée
API_KEY=$(openssl rand -hex 32)
echo "✅ Clé API générée: $API_KEY"
echo ""
echo "⚠️  IMPORTANT: Notez cette clé et gardez-la en sécurité!"
echo ""

# 2. Créer le fichier de configuration
cat > /var/www/automatehub/scripts/content-extractor/.env << EOF
# Configuration Content Extractor
CONTENT_EXTRACTOR_API_KEYS=$API_KEY
PORT=5680

# Vous pouvez ajouter plusieurs clés séparées par des virgules:
# CONTENT_EXTRACTOR_API_KEYS=key1,key2,key3
EOF

echo "✅ Fichier .env créé"

# 3. Créer le service systemd sécurisé
sudo tee /etc/systemd/system/content-extractor-secure.service > /dev/null << EOF
[Unit]
Description=Content Extractor API Sécurisé
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/automatehub/scripts/content-extractor
EnvironmentFile=/var/www/automatehub/scripts/content-extractor/.env
ExecStart=/var/www/automatehub/scripts/content-extractor/env/bin/python3 secure-api-server.py
Restart=always
RestartSec=10

# Sécurité
NoNewPrivileges=true
PrivateTmp=true
ProtectSystem=strict
ProtectHome=true
ReadWritePaths=/var/www/automatehub/data

[Install]
WantedBy=multi-user.target
EOF

echo "✅ Service systemd créé"

# 4. Configuration nginx
echo ""
echo "📝 Configuration nginx à ajouter dans /etc/nginx/sites-available/automatehub.fr.conf :"
echo ""
cat nginx-secure-config.conf
echo ""
echo "Copiez cette configuration et ajoutez-la avant la dernière accolade du bloc server HTTPS"
echo ""

# 5. Instructions finales
echo "🚀 Prochaines étapes :"
echo ""
echo "1. Éditez nginx :"
echo "   sudo nano /etc/nginx/sites-available/automatehub.fr.conf"
echo ""
echo "2. Testez et rechargez nginx :"
echo "   sudo nginx -t"
echo "   sudo systemctl reload nginx"
echo ""
echo "3. Démarrez le service :"
echo "   sudo systemctl daemon-reload"
echo "   sudo systemctl start content-extractor-secure"
echo "   sudo systemctl enable content-extractor-secure"
echo ""
echo "4. Dans n8n Cloud, ajoutez la variable :"
echo "   Name: CONTENT_EXTRACTOR_API_KEY"
echo "   Value: $API_KEY"
echo ""
echo "5. URLs à utiliser dans n8n :"
echo "   YouTube: https://automatehub.fr/api/content-extractor/api/v1/get-youtube-transcript"
echo "   Scraping: https://automatehub.fr/api/content-extractor/api/v1/scrape"
echo ""
echo "📊 Monitoring :"
echo "   Logs nginx: sudo tail -f /var/log/nginx/content-extractor-*.log"
echo "   Logs API: sudo journalctl -u content-extractor-secure -f"
echo "   Stats: curl https://automatehub.fr/api/content-extractor/api/v1/stats -H 'Authorization: Bearer $API_KEY'"