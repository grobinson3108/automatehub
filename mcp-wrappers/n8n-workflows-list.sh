#!/bin/bash
# Liste les workflows n8n existants

# Configuration n8n
N8N_URL="https://n8n.automatehub.fr"
N8N_API_KEY="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI0YmNhOGZkMi02NWZhLTQ3MWItOGQ3Yi1hMzA5NjkzY2I5NDIiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwiaWF0IjoxNzUxNzA2MDQwfQ.A30676RHfxRMNcAjIWy5y_BOweneQRGTHUb-77gioSE"

# Récupérer la liste des workflows
echo "📋 Workflows n8n disponibles :"
echo "==============================="

curl -s -X GET "$N8N_URL/api/v1/workflows" \
    -H "X-N8N-API-KEY: $N8N_API_KEY" \
    --insecure | \
    jq -r '.data[] | "ID: \(.id) | Name: \(.name) | Active: \(.active) | Tags: \(.tags | join(", "))"' 2>/dev/null || \
    echo "❌ Erreur lors de la récupération des workflows"