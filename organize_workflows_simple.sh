#!/bin/bash

# Script simplifié pour organiser les workflows en Freemium et Premium

echo "🚀 Organisation des workflows n8n - Version simplifiée"
echo "===================================================="

# Créer les dossiers principaux s'ils n'existent pas
mkdir -p Freemium_Workflows Premium_Workflows

# Fonction pour analyser et déplacer un workflow
analyze_workflow() {
    local file="$1"
    local filename=$(basename "$file")
    local filesize=$(stat -c%s "$file" 2>/dev/null || echo 0)
    
    # Critères simplifiés basés sur le nom et la taille
    if [[ "$filename" =~ (simple|basic|trigger|notification|to_sheets|to_gmail|telegram_bot) ]] || \
       [[ "$filename" =~ ^(Send_|Notify_|Alert_|Log_|Track_|Monitor_) ]] || \
       [[ $filesize -lt 10000 ]]; then
        echo "FREEMIUM: $filename"
        cp "$file" "Freemium_Workflows/" 2>/dev/null
    else
        # Premium : workflows complexes, IA avancée, entreprise
        if [[ "$filename" =~ (complex|advanced|enterprise|ai_powered|rag|langchain|crm|database) ]] || \
           [[ "$filename" =~ (multi_|integration|pipeline|system) ]] || \
           [[ $filesize -gt 20000 ]]; then
            echo "PREMIUM: $filename"
            cp "$file" "Premium_Workflows/" 2>/dev/null
        else
            # Par défaut, mettre dans Freemium
            echo "FREEMIUM (default): $filename"
            cp "$file" "Freemium_Workflows/" 2>/dev/null
        fi
    fi
}

# Traiter tous les workflows JSON dans le dossier source
echo "📂 Analyse et copie des workflows..."
echo "------------------------------------"

# Compter le total de fichiers
total_files=$(find 200_automations_n8n -name "*.json" -type f 2>/dev/null | wc -l)
echo "Total de workflows trouvés : $total_files"

# Traiter par lots de 100 fichiers
count=0
find 200_automations_n8n -name "*.json" -type f 2>/dev/null | while read -r file; do
    analyze_workflow "$file"
    ((count++))
    
    # Afficher la progression tous les 100 fichiers
    if ((count % 100 == 0)); then
        echo "📊 Progression : $count/$total_files fichiers traités..."
    fi
done

echo ""
echo "✅ Organisation terminée !"
echo "========================"

# Afficher les statistiques
freemium_count=$(ls Freemium_Workflows/*.json 2>/dev/null | wc -l)
premium_count=$(ls Premium_Workflows/*.json 2>/dev/null | wc -l)

echo "📊 Statistiques finales :"
echo "- Workflows Freemium : $freemium_count"
echo "- Workflows Premium : $premium_count"
echo "- Total organisé : $((freemium_count + premium_count))"

# Créer un fichier de statistiques
cat > WORKFLOW_STATS.txt << EOF
STATISTIQUES D'ORGANISATION DES WORKFLOWS
========================================
Date : $(date)
Total de workflows source : $total_files
Workflows Freemium : $freemium_count
Workflows Premium : $premium_count
Total organisé : $((freemium_count + premium_count))

TOP 10 FREEMIUM (par ordre alphabétique) :
$(ls Freemium_Workflows/*.json 2>/dev/null | head -10 | xargs -n1 basename)

TOP 10 PREMIUM (par ordre alphabétique) :
$(ls Premium_Workflows/*.json 2>/dev/null | head -10 | xargs -n1 basename)
EOF

echo ""
echo "📄 Fichier de statistiques créé : WORKFLOW_STATS.txt"
echo ""
echo "🎯 Prochaines étapes :"
echo "1. Vérifier les workflows dans Freemium_Workflows/"
echo "2. Sélectionner les meilleurs pour vos vidéos YouTube"
echo "3. Garder les workflows Premium pour votre Skool payant"