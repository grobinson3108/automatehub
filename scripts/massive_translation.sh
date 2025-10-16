#!/bin/bash

# Script de traduction massive des workflows AutomationTribe
# Usage: ./massive_translation.sh

echo "🚀 TRADUCTION MASSIVE AUTOMATEHUB 🚀"
echo "====================================="

WORKFLOWS_DIR="/var/www/automatehub/AutomationTribe"
OUTPUT_DIR="/var/www/automatehub/workflows_traduits/AutomationTribe_FR"
SCRIPTS_DIR="/var/www/automatehub/scripts/workflow_translator"

# Créer le dossier de sortie
mkdir -p "$OUTPUT_DIR"

total_files=0
translated_files=0

# Trouver tous les workflows JSON
while IFS= read -r -d '' workflow_file; do
    echo ""
    echo "📁 Traitement: $(basename "$workflow_file")"

    # Créer nom de fichier de sortie
    base_name=$(basename "$workflow_file" .json)
    output_file="$OUTPUT_DIR/${base_name}_FR.json"

    # Si déjà traduit, ignorer
    if [[ -f "$output_file" ]]; then
        echo "⚠️ Déjà traduit, ignoré"
        continue
    fi

    # Copier dans /tmp pour traitement
    temp_file="/tmp/current_workflow.json"
    cp "$workflow_file" "$temp_file"

    echo "🔍 Extraction des textes..."
    if python3 "$SCRIPTS_DIR/extract_texts.py" "$temp_file"; then
        echo "🌐 Traduction avec OpenAI..."
        if python3 "$SCRIPTS_DIR/translate_with_openai.py" "/tmp/current_workflow_texts_to_translate.json"; then
            echo "✅ Application des traductions..."
            if python3 "$SCRIPTS_DIR/apply_translations.py" "$temp_file" "/tmp/current_workflow_texts_translated.json"; then
                # Déplacer le résultat
                mv "/tmp/current_workflow_FR.json" "$output_file"
                echo "✅ Traduit avec succès: $output_file"
                ((translated_files++))
            else
                echo "❌ Erreur lors de l'application"
            fi
        else
            echo "❌ Erreur lors de la traduction"
        fi
    else
        echo "❌ Erreur lors de l'extraction"
    fi

    ((total_files++))

    # Pause pour éviter de surcharger l'API
    sleep 2

done < <(find "$WORKFLOWS_DIR" -name "*.json" -type f -print0)

echo ""
echo "🎉 TRADUCTION TERMINÉE !"
echo "📊 $translated_files/$total_files workflows traduits"
echo "📁 Fichiers disponibles dans: $OUTPUT_DIR"
echo ""
echo "🎬 PRÊT POUR LA CRÉATION DE CONTENU !"