#!/bin/bash

WORKFLOWS_DIR="/var/www/automatehub/200_automations_n8n"
FREEMIUM_DIR="/var/www/automatehub/Freemium_Workflows"
PREMIUM_DIR="/var/www/automatehub/Premium_Workflows"
LOG_FILE="/var/www/automatehub/workflow_moves.log"

echo "=== DÉPLACEMENT DES WORKFLOWS SELON CATÉGORISATION ===" > "$LOG_FILE"
echo "Date: $(date)" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"

# Compteurs
moved_freemium=0
moved_premium=0
errors=0

# Fonction pour analyser un workflow (copiée du script précédent)
analyze_workflow() {
    local file="$1"
    local filename=$(basename "$file")
    local relative_path="${file#$WORKFLOWS_DIR/}"
    
    # Extraction des métriques du workflow
    local node_count=$(grep -o '"type"' "$file" | wc -l)
    local has_complex=$(grep -i "complex" <<< "$filename" && echo "yes" || echo "no")
    local has_ai=$(grep -i -E "(openai|gpt|ai|claude|llm|langchain|rag)" "$file" && echo "yes" || echo "no")
    local has_database=$(grep -i -E "(mysql|postgres|database|sql)" "$file" && echo "yes" || echo "no")
    local has_crm=$(grep -i -E "(hubspot|salesforce|pipedrive|crm)" "$file" && echo "yes" || echo "no")
    local has_simple_apps=$(grep -i -E "(gmail|googlesheets|slack|telegram)" "$file" && echo "yes" || echo "no")
    
    # Analyse du nombre de nodes depuis le nom du fichier
    local filename_nodes=$(echo "$filename" | grep -o '[0-9]\+nodes' | grep -o '[0-9]\+' | head -1)
    if [ -n "$filename_nodes" ]; then
        node_count=$filename_nodes
    fi
    
    # Logique de catégorisation
    local category="PREMIUM"  # Par défaut Premium
    
    # Critères FREEMIUM (doit correspondre à TOUS ces critères)
    if [ "$node_count" -le 8 ] && [ "$has_complex" = "no" ] && [ "$has_simple_apps" = "yes" ]; then
        if [ "$has_database" = "no" ] && [ "$has_crm" = "no" ]; then
            category="FREEMIUM"
        fi
    fi
    
    # Force PREMIUM pour certains cas
    if [ "$node_count" -gt 15 ] || [ "$has_complex" = "yes" ] || [ "$has_database" = "yes" ] || [ "$has_crm" = "yes" ]; then
        category="PREMIUM"
    elif echo "$filename" | grep -i -E "(rag|langchain|enterprise|business)" > /dev/null; then
        category="PREMIUM"
    fi
    
    # Sous-catégorisation
    local subcategory=""
    if [ "$category" = "FREEMIUM" ]; then
        if echo "$relative_path" | grep -i "communication" > /dev/null; then
            subcategory="Email_Automation_Simple"
        elif echo "$relative_path" | grep -i "social" > /dev/null; then
            subcategory="Social_Media_Basic"
        elif echo "$filename" | grep -i -E "(gmail|email)" > /dev/null; then
            subcategory="Email_Automation_Simple"
        elif echo "$filename" | grep -i -E "(telegram|notification)" > /dev/null; then
            subcategory="Telegram_Notifications"
        elif echo "$filename" | grep -i -E "(google|sheets)" > /dev/null; then
            subcategory="Google_Workspace_Basics"
        elif echo "$filename" | grep -i -E "(ai|gpt)" > /dev/null; then
            subcategory="Quick_AI_Tasks"
        elif echo "$filename" | grep -i -E "(form|typeform)" > /dev/null; then
            subcategory="Form_to_Action"
        else
            subcategory="Personal_Productivity"
        fi
    else
        if echo "$filename" | grep -i -E "(rag|langchain)" > /dev/null; then
            subcategory="AI_Advanced_RAG"
        elif echo "$filename" | grep -i -E "(hubspot|salesforce|pipedrive|crm)" > /dev/null; then
            subcategory="CRM_Enterprise"
        elif echo "$filename" | grep -i -E "(mysql|postgres|database)" > /dev/null; then
            subcategory="Database_Management"
        elif echo "$filename" | grep -i -E "(monitor|alert|security)" > /dev/null; then
            subcategory="Security_Monitoring"
        elif echo "$filename" | grep -i -E "(shopify|ecommerce)" > /dev/null; then
            subcategory="E_commerce_Advanced"
        elif echo "$filename" | grep -i -E "(business|enterprise)" > /dev/null; then
            subcategory="Business_Intelligence"
        else
            subcategory="Multi_System_Integration"
        fi
    fi
    
    echo "$category|$subcategory"
}

# Créer la structure si elle n'existe pas
/var/www/automatehub/create_folder_structure.sh > /dev/null 2>&1

echo "Déplacement des workflows..."

# Analyser et déplacer tous les workflows JSON
find "$WORKFLOWS_DIR" -name "*.json" -type f | while read file; do
    filename=$(basename "$file")
    result=$(analyze_workflow "$file")
    category=$(echo "$result" | cut -d'|' -f1)
    subcategory=$(echo "$result" | cut -d'|' -f2)
    
    # Déterminer le dossier de destination
    if [ "$category" = "FREEMIUM" ]; then
        dest_dir="$FREEMIUM_DIR/$subcategory"
        ((moved_freemium++))
    else
        dest_dir="$PREMIUM_DIR/$subcategory"
        ((moved_premium++))
    fi
    
    # S'assurer que le dossier existe
    mkdir -p "$dest_dir"
    
    # Déplacer le fichier (copier pour sécurité)
    if cp "$file" "$dest_dir/" 2>/dev/null; then
        echo "✓ $filename -> $category/$subcategory" | tee -a "$LOG_FILE"
    else
        echo "✗ ERREUR: $filename" | tee -a "$LOG_FILE"
        ((errors++))
    fi
done

echo "" >> "$LOG_FILE"
echo "=== RÉSUMÉ DU DÉPLACEMENT ===" >> "$LOG_FILE"
echo "FREEMIUM déplacés: $moved_freemium" >> "$LOG_FILE"
echo "PREMIUM déplacés: $moved_premium" >> "$LOG_FILE"
echo "Erreurs: $errors" >> "$LOG_FILE"

echo ""
echo "Déplacement terminé !"
echo "FREEMIUM: $moved_freemium | PREMIUM: $moved_premium | Erreurs: $errors"
echo "Log détaillé: $LOG_FILE"