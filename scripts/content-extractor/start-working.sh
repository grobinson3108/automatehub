#!/bin/bash

# Script de démarrage pour la version fonctionnelle de l'API

cd /var/www/automatehub/scripts/content-extractor

echo "🛑 Arrêt des anciens processus..."
pkill -f "api-server" 2>/dev/null || true
pkill -f "secure-api-server" 2>/dev/null || true

echo "🚀 Démarrage de l'API v2.0 avec support YouTube Shorts..."
export CONTENT_EXTRACTOR_API_KEY="1ab54f24f0e313c8159aebf9cc99ebd0481e5a6275a11110600a0261f6605724"
export PORT=5682

nohup python3 api-working.py > api-working.log 2>&1 &
PID=$!

echo "✅ API démarrée (PID: $PID)"
echo "🔗 Endpoint: http://automatehub.fr:5682/api/v1/get-youtube-transcript"
echo "📝 Logs: api-working.log"

# Test rapide
sleep 2
echo ""
echo "🧪 Test de santé..."
curl -s http://localhost:5682/health | jq . || echo "❌ Health check failed"

echo ""
echo "✅ API prête pour n8n !"