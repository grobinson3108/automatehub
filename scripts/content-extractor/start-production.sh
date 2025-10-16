#!/bin/bash
# Script de démarrage en production avec ngrok ou cloudflared

echo "🚀 Démarrage de Content Extractor en production..."

# Option A : Avec Cloudflare Tunnel (gratuit et stable)
if command -v cloudflared &> /dev/null; then
    echo "📡 Utilisation de Cloudflare Tunnel..."
    
    # Démarrer l'API
    export CONTENT_EXTRACTOR_API_KEY="votre-cle-securisee-production"
    export PORT=5680
    nohup python3 api-server-simple.py > api.log 2>&1 &
    API_PID=$!
    
    sleep 2
    
    # Créer le tunnel
    cloudflared tunnel --url http://localhost:5680
    
# Option B : Avec ngrok
elif command -v ngrok &> /dev/null; then
    echo "📡 Utilisation de ngrok..."
    
    # Démarrer l'API
    export CONTENT_EXTRACTOR_API_KEY="votre-cle-securisee-production"
    export PORT=5680
    nohup python3 api-server-simple.py > api.log 2>&1 &
    API_PID=$!
    
    sleep 2
    
    # Créer le tunnel
    ngrok http 5680
    
else
    echo "❌ Installez cloudflared ou ngrok pour exposer l'API"
    echo ""
    echo "Installation cloudflared (recommandé):"
    echo "wget -q https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb"
    echo "sudo dpkg -i cloudflared-linux-amd64.deb"
    echo ""
    echo "Ou ngrok:"
    echo "curl -s https://ngrok-agent.s3.amazonaws.com/ngrok.asc | sudo tee /etc/apt/trusted.gpg.d/ngrok.asc >/dev/null"
    echo "echo 'deb https://ngrok-agent.s3.amazonaws.com buster main' | sudo tee /etc/apt/sources.list.d/ngrok.list"
    echo "sudo apt update && sudo apt install ngrok"
fi