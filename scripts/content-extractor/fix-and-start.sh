#!/bin/bash
# Script pour réparer et démarrer l'API

echo "🔧 Nettoyage des processus..."
# Arrêter tous les processus Python liés à l'API
pkill -f "api-server" 2>/dev/null
pkill -f "content-extractor" 2>/dev/null

# Arrêter le service systemd s'il tourne
sudo systemctl stop content-extractor-secure 2>/dev/null || true

sleep 3

# Vérifier que le port est libre
if lsof -Pi :5680 -sTCP:LISTEN -t >/dev/null ; then
    echo "❌ Le port 5680 est toujours utilisé. Forçage..."
    sudo fuser -k 5680/tcp 2>/dev/null || true
    sleep 2
fi

echo "✅ Port 5680 libéré"

# Démarrer l'API simple avec la bonne clé
cd /var/www/automatehub/scripts/content-extractor
source env/bin/activate

export CONTENT_EXTRACTOR_API_KEY="1ab54f24f0e313c8159aebf9cc99ebd0481e5a6275a11110600a0261f6605724"
export PORT=5680

echo "🚀 Démarrage de l'API..."
echo "🔑 Clé configurée: ${CONTENT_EXTRACTOR_API_KEY:0:20}..."
echo "📡 Port: $PORT"

# Démarrer en arrière-plan
nohup python3 api-server-simple.py > api-final.log 2>&1 &
PID=$!

echo "✅ API démarrée avec PID: $PID"

# Attendre et vérifier
sleep 5

if curl -s http://localhost:5680/health > /dev/null; then
    echo "✅ API fonctionne localement!"
    
    # Tester via nginx
    if curl -s https://automatehub.fr/api/content-extractor/health > /dev/null; then
        echo "✅ API accessible via HTTPS!"
    else
        echo "❌ Problème avec nginx/HTTPS"
    fi
else
    echo "❌ L'API ne répond pas"
    echo "Logs:"
    tail -20 api-final.log
fi