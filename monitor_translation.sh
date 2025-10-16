#!/bin/bash
# Monitor translation progress and create archive when done

MAPPING_DIR="/var/www/automatehub/translation_mappings"
WORKFLOWS_DIR="/var/www/automatehub/workflows_traduits/FR/AutomationTribe"
ARCHIVE_NAME="AutomationTribe_FR_COMPLETE.tar.gz"

echo "🔍 Surveillance de la progression de traduction..."
echo "================================================"

while true; do
    # Compter les mappings et workflows
    MAPPING_COUNT=$(ls -1 $MAPPING_DIR/*.mapping.json 2>/dev/null | wc -l)
    WORKFLOW_COUNT=$(find $WORKFLOWS_DIR -name "*.json" -type f | wc -l)
    
    # Afficher la progression
    echo -ne "\r📊 Progression: $MAPPING_COUNT/$WORKFLOW_COUNT workflows traduits ($(($MAPPING_COUNT * 100 / $WORKFLOW_COUNT))%)"
    
    # Vérifier si le processus est toujours actif
    if ! pgrep -f "translate_workflow_mapping.py" > /dev/null; then
        echo -e "\n\n✅ Processus de traduction terminé!"
        break
    fi
    
    # Attendre 30 secondes avant la prochaine vérification
    sleep 30
done

# Afficher le résumé final
echo -e "\n\n📋 RÉSUMÉ FINAL"
echo "==============="
echo "✅ Workflows traduits: $MAPPING_COUNT"
echo "📁 Workflows totaux: $WORKFLOW_COUNT"

# Créer l'archive
echo -e "\n📦 Création de l'archive..."
cd /var/www/automatehub/workflows_traduits/FR
tar -czf "/var/www/automatehub/$ARCHIVE_NAME" AutomationTribe/

echo "✅ Archive créée: /var/www/automatehub/$ARCHIVE_NAME"

# Afficher la taille de l'archive
SIZE=$(ls -lh "/var/www/automatehub/$ARCHIVE_NAME" | awk '{print $5}')
echo "📦 Taille de l'archive: $SIZE"

echo -e "\n🎉 Traduction complète! L'archive est prête pour le téléchargement."