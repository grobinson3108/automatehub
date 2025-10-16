#!/bin/bash
echo "🚀 Démarrage de Claude avec MCPs pour automatehub"
echo "📍 Répertoire: /var/www/automatehub"
echo "🔧 Configuration: .claude-mcp-config.json"
echo ""
cd /var/www/automatehub
claude --mcp-config .claude-mcp-config.json "$@"
