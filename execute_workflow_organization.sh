#!/bin/bash

echo "🚀 ORGANISATION AUTOMATIQUE DES WORKFLOWS N8N - FREEMIUM vs PREMIUM"
echo "=================================================================="
echo ""

# Rendre les scripts exécutables
chmod +x /var/www/automatehub/create_folder_structure.sh
chmod +x /var/www/automatehub/categorize_workflows.sh  
chmod +x /var/www/automatehub/move_workflows.sh
chmod +x /var/www/automatehub/extract_categories.sh

echo "📁 ÉTAPE 1/4 : Création de la structure de dossiers..."
echo "----------------------------------------------------"
/var/www/automatehub/create_folder_structure.sh

echo ""
echo "🔍 ÉTAPE 2/4 : Analyse des catégories existantes..."
echo "---------------------------------------------------"
/var/www/automatehub/extract_categories.sh

echo ""
echo "🤖 ÉTAPE 3/4 : Catégorisation intelligente des workflows..."
echo "----------------------------------------------------------"
/var/www/automatehub/categorize_workflows.sh

echo ""
echo "📦 ÉTAPE 4/4 : Déplacement des fichiers dans les bonnes catégories..."
echo "---------------------------------------------------------------------"
/var/www/automatehub/move_workflows.sh

echo ""
echo "✅ ORGANISATION TERMINÉE !"
echo "========================="
echo ""
echo "📊 Résultats disponibles dans :"
echo "   • /var/www/automatehub/WORKFLOW_DISTRIBUTION.md (Guide complet)"
echo "   • /var/www/automatehub/categorization_results.txt (Analyse détaillée)"
echo "   • /var/www/automatehub/workflow_moves.log (Log des déplacements)"
echo ""
echo "📁 Workflows organisés dans :"
echo "   • /var/www/automatehub/Freemium_Workflows/ (Contenu YouTube/Skool gratuit)"
echo "   • /var/www/automatehub/Premium_Workflows/ (Contenu Skool payant)"
echo ""
echo "🎬 TOP 10 VIDÉOS YOUTUBE PRIORITAIRES :"
echo "   1. Gmail to Telegram en 3 minutes"
echo "   2. ChatGPT + Google Sheets = Magic"  
echo "   3. Track Twitter avec Google Sheets"
echo "   4. Bot Telegram Personnel en 5min"
echo "   5. Auto-Reply Intelligent Gmail"
echo "   6. Cross-Post Social Media Auto"
echo "   7. Website Down = Telegram Alert"
echo "   8. Typeform to Action Automation"
echo "   9. Content Ideas Generator"
echo "   10. Personal Dashboard Automation"
echo ""
echo "🚀 Prêt pour le lancement de votre stratégie FREEMIUM → PREMIUM !"