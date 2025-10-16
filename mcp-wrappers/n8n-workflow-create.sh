#!/bin/bash
# Wrapper n8n pour créer des workflows

# Vérifier les arguments
if [ $# -lt 2 ]; then
    echo "Usage: $0 \"Workflow Name\" workflow.json"
    echo "Example: $0 \"Test Workflow\" /path/to/workflow.json"
    exit 1
fi

# Configuration n8n
N8N_URL="https://n8n.automatehub.fr"
N8N_API_KEY="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI0YmNhOGZkMi02NWZhLTQ3MWItOGQ3Yi1hMzA5NjkzY2I5NDIiLCJpc3MiOiJuOG4iLCJhdWQiOiJwdWJsaWMtYXBpIiwiaWF0IjoxNzUxNzA2MDQwfQ.A30676RHfxRMNcAjIWy5y_BOweneQRGTHUb-77gioSE"

WORKFLOW_NAME="$1"
WORKFLOW_FILE="$2"

# Si c'est juste un nom, créer un workflow simple
if [ ! -f "$WORKFLOW_FILE" ]; then
    # Créer un workflow basique
    WORKFLOW_DATA="{
        \"name\": \"$WORKFLOW_NAME\",
        \"nodes\": [
            {
                \"parameters\": {},
                \"id\": \"manual-trigger\",
                \"name\": \"Manual Trigger\",
                \"type\": \"n8n-nodes-base.manualTrigger\",
                \"typeVersion\": 1,
                \"position\": [250, 300]
            }
        ],
        \"connections\": {},
        \"settings\": {
            \"executionOrder\": \"v1\"
        }
    }"
else
    WORKFLOW_DATA=$(cat "$WORKFLOW_FILE")
fi

# Créer le workflow via l'API
RESPONSE=$(curl -s -X POST "$N8N_URL/api/v1/workflows" \
    -H "X-N8N-API-KEY: $N8N_API_KEY" \
    -H "Content-Type: application/json" \
    -d "$WORKFLOW_DATA" \
    --insecure)

# Extraire l'ID du workflow
WORKFLOW_ID=$(echo "$RESPONSE" | grep -o '"id":"[^"]*' | grep -o '[^"]*$' | head -1)

if [ -n "$WORKFLOW_ID" ]; then
    echo "✅ Workflow créé avec succès !"
    echo "🆔 ID: $WORKFLOW_ID"
    echo "🔗 URL: $N8N_URL/workflow/$WORKFLOW_ID"
else
    echo "❌ Erreur lors de la création du workflow"
    echo "$RESPONSE"
fi