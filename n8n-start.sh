#!/bin/bash
# Script to start n8n with Node.js 20

export PATH="/var/www/automatehub/node-v20.19.0-linux-x64/bin:$PATH"
export N8N_RUNNERS_ENABLED=true
export WEBHOOK_URL=https://n8n.automatehub.fr
export N8N_EDITOR_BASE_URL=https://n8n.automatehub.fr

exec /usr/local/bin/n8n start