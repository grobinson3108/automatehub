#!/bin/bash

# Script d'export automatique des workflows n8n vers AutomateHub
# Usage: ./export-n8n-workflows.sh

N8N_URL="http://localhost:5678"
EXPORT_DIR="/var/www/automatehub/storage/app/tutorials/n8n-exports"
CREDENTIALS_FILE="/tmp/n8n_auth.txt"

echo "Export des workflows n8n vers AutomateHub..."

# Créer le répertoire d'export s'il n'existe pas
mkdir -p "$EXPORT_DIR"

# Note: Pour utiliser l'API, vous devez d'abord créer une API key dans n8n
# Allez dans Settings > API Keys et créez une nouvelle clé

echo "⚠️  Pour utiliser ce script, vous devez :"
echo "1. Créer une API Key dans n8n (Settings > API Keys)"
echo "2. Remplacer 'YOUR_API_KEY' dans ce script par votre vraie clé"
echo "3. Relancer le script"

# Exemple de commande pour lister les workflows (à adapter avec votre API key)
# curl -H "X-N8N-API-KEY: YOUR_API_KEY" "$N8N_URL/api/v1/workflows"

echo "✅ Répertoire d'export prêt : $EXPORT_DIR"