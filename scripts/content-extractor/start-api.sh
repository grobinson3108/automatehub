#!/bin/bash
# Script de démarrage temporaire de l'API

echo "🚀 Démarrage de Content Extractor API (version simple)..."
echo "⚠️  Note: Cette version nécessite l'installation manuelle des dépendances Python"
echo ""
echo "Pour installer les dépendances:"
echo "sudo apt-get update"
echo "sudo apt-get install python3-pip"
echo "pip3 install youtube-transcript-api requests beautifulsoup4 html2text"
echo ""
echo "L'API démarre quand même en mode basique sur le port 5679..."

export CONTENT_EXTRACTOR_API_KEY="test-key-automatehub"
export PORT=5679

# Démarrer l'API simple
python3 /var/www/automatehub/scripts/content-extractor/api-server-simple.py