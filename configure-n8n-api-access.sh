#!/bin/bash

# Script pour configurer l'accès API à n8n
# Usage: bash configure-n8n-api-access.sh YOUR_API_KEY

if [ $# -eq 0 ]; then
    echo "❌ Usage: $0 <N8N_API_KEY>"
    echo ""
    echo "📋 Pour obtenir votre clé API :"
    echo "1. Connectez-vous à https://n8n.automatehub.fr"
    echo "2. Allez dans Settings > API"
    echo "3. Créez une nouvelle clé API"
    echo "4. Exécutez: $0 VOTRE_CLE_API"
    exit 1
fi

API_KEY="$1"
ENV_FILE="/var/www/automatehub/.env"

echo "🔧 Configuration de l'accès API n8n..."

# Mettre à jour le fichier .env
if grep -q "N8N_API_KEY=" "$ENV_FILE"; then
    # Remplacer la ligne existante
    sed -i "s/N8N_API_KEY=.*/N8N_API_KEY=$API_KEY/" "$ENV_FILE"
    echo "✅ Clé API mise à jour dans $ENV_FILE"
else
    # Ajouter la ligne
    echo "N8N_API_KEY=$API_KEY" >> "$ENV_FILE"
    echo "✅ Clé API ajoutée dans $ENV_FILE"
fi

# Redémarrer n8n-mcp avec la nouvelle configuration
echo "🔄 Redémarrage de n8n-mcp..."
docker stop n8n-mcp > /dev/null 2>&1
docker rm n8n-mcp > /dev/null 2>&1

docker run -d --name n8n-mcp --network host \
    -v /var/www/automatehub/n8n-mcp:/workspace \
    -e N8N_API_URL=https://n8n.automatehub.fr \
    -e N8N_API_KEY="$API_KEY" \
    ghcr.io/czlonkowski/n8n-mcp:latest

sleep 3

# Tester la connexion
echo "🧪 Test de la connexion API..."
if docker logs n8n-mcp | grep -q "n8n API: configured"; then
    echo "✅ n8n-mcp configuré avec succès !"
    echo "🎉 Claude peut maintenant accéder directement à votre n8n !"
else
    echo "⚠️  Configuration en cours... Vérifiez les logs : docker logs n8n-mcp"
fi

echo ""
echo "📋 Commandes utiles :"
echo "docker logs n8n-mcp          # Voir les logs"
echo "docker exec n8n-mcp env       # Voir la configuration"