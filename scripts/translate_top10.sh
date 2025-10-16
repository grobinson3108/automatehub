#!/bin/bash

# Script de traduction des TOP 10 workflows prioritaires
echo "🎯 TRADUCTION TOP 10 WORKFLOWS VIRAL 🎯"
echo "======================================="

# Répertoires
OUTPUT_DIR="/var/www/automatehub/workflows_traduits/TOP_10_VIRAL"
SCRIPTS_DIR="/var/www/automatehub/scripts/workflow_translator"

# Créer le dossier de sortie
mkdir -p "$OUTPUT_DIR"

# Liste des 10 workflows prioritaires
declare -a workflows=(
    "/var/www/automatehub/workflows/Nate Plus/28 - Jarvis/JARVIS.json"
    "/var/www/automatehub/workflows/Automation Tribe Primium/16 - Clone Vidéo TikTok/AT30___Clone_any_Tiktok_Video_and_Repurpose_it.json"
    "/var/www/automatehub/workflows/Automation Tribe Primium/8 - Posts sur Tous les réseaux/Post_to_ALL_social_networks.json"
    "/var/www/automatehub/workflows/Automation Tribe Primium/14 - 30 jours contenu 1 min/Card_auto_poster.json"
    "/var/www/automatehub/workflows/Automation Tribe Primium/15 - Shorts Auto Hauts-de-Gamme/BIG_VIDEO_GENERATION.json"
    "/var/www/automatehub/workflows/Nate Plus/22 - Agent de Voyage Vocal/Travel_Agent.json"
    "/var/www/automatehub/workflows/Automation Tribe Primium/11 - Extracteur Web Intellignet/AI_Powered_Web_Intel_Extractor.json"
    "/var/www/automatehub/workflows/Automation Tribe Primium/5 - Produits Prennent Vie/Life_Style_Product_Photo_Generator.json"
    "/var/www/automatehub/workflows/Automation Tribe Primium/13 - Transfo URL en Image/Create_banners_from_your_URL_s___ChatGPT.json"
)

# Noms correspondants pour les fichiers de sortie
declare -a names=(
    "JARVIS_Assistant_Personnel_Ultime"
    "Clone_Video_TikTok_Repurpose"
    "Post_Tous_Reseaux_Sociaux"
    "30_Jours_Contenu_1_Minute"
    "Shorts_Auto_Haut_Gamme"
    "Agent_Voyage_Vocal"
    "Extracteur_Web_Intelligent"
    "Produits_Prennent_Vie"
    "Createur_Bannieres_URL"
)

total_workflows=${#workflows[@]}
translated=0

echo "📊 ${total_workflows} workflows prioritaires à traduire"
echo ""

for i in "${!workflows[@]}"; do
    workflow_file="${workflows[$i]}"
    workflow_name="${names[$i]}"

    echo "🔥 [$((i+1))/${total_workflows}] Traitement: $workflow_name"

    # Vérifier si le fichier existe
    if [[ ! -f "$workflow_file" ]]; then
        echo "⚠️ Fichier non trouvé: $workflow_file"
        continue
    fi

    # Nom du fichier de sortie
    output_file="$OUTPUT_DIR/${workflow_name}_FR.json"

    # Si déjà traduit, ignorer
    if [[ -f "$output_file" ]]; then
        echo "✅ Déjà traduit: $workflow_name"
        ((translated++))
        continue
    fi

    # Copier dans /tmp pour traitement
    temp_file="/tmp/current_workflow_top10.json"
    cp "$workflow_file" "$temp_file"

    echo "   🔍 Extraction des textes..."
    if python3 "$SCRIPTS_DIR/extract_texts.py" "$temp_file"; then
        echo "   🌐 Traduction avec OpenAI..."
        if python3 "$SCRIPTS_DIR/translate_with_openai.py" "/tmp/current_workflow_top10_texts_to_translate.json"; then
            echo "   ✅ Application des traductions..."
            if python3 "$SCRIPTS_DIR/apply_translations.py" "$temp_file" "/tmp/current_workflow_top10_texts_translated.json"; then
                # Déplacer le résultat
                mv "/tmp/current_workflow_top10_FR.json" "$output_file"
                echo "   🎉 SUCCÈS: $output_file"
                ((translated++))
            else
                echo "   ❌ Erreur lors de l'application"
            fi
        else
            echo "   ❌ Erreur lors de la traduction"
        fi
    else
        echo "   ❌ Erreur lors de l'extraction"
    fi

    echo ""
    # Pause pour éviter de surcharger l'API
    sleep 3
done

echo "🎊 TRADUCTION TOP 10 TERMINÉE !"
echo "📊 $translated/$total_workflows workflows traduits"
echo "📁 Workflows disponibles dans: $OUTPUT_DIR"
echo ""
echo "🎬 PRÊT POUR LA CRÉATION DE CONTENU VIRAL !"
echo ""
echo "📋 Fichiers traduits :"
ls -la "$OUTPUT_DIR"