#!/bin/bash
# Script de démarrage simple pour production

# Arrêter tous les processus existants
pkill -f "api-server.*\.py" 2>/dev/null
sleep 2

# Configuration
export CONTENT_EXTRACTOR_API_KEY="1ab54f24f0e313c8159aebf9cc99ebd0481e5a6275a11110600a0261f6605724"
export PORT=5682

# Démarrer l'API
cd /var/www/automatehub/scripts/content-extractor
source env/bin/activate
echo "🚀 Démarrage de l'API sur le port $PORT..."
echo "🔑 Clé API: ${CONTENT_EXTRACTOR_API_KEY:0:10}..."
python3 api-server-simple.py