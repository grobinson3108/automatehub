#!/bin/bash

WORKFLOWS_DIR="/var/www/automatehub/200_automations_n8n"
FREEMIUM_DIR="/var/www/automatehub/Freemium_Workflows"
PREMIUM_DIR="/var/www/automatehub/Premium_Workflows"
RESULTS_FILE="/var/www/automatehub/categorization_results.txt"

echo "=== ANALYSE ET CATÉGORISATION DES WORKFLOWS N8N ===" > "$RESULTS_FILE"
echo "Date: $(date)" >> "$RESULTS_FILE"
echo "" >> "$RESULTS_FILE"

# Compteurs
freemium_count=0
premium_count=0
total_count=0

# Fonction pour analyser un workflow
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
    local reason=""
    
    # Critères FREEMIUM (doit correspondre à TOUS ces critères)
    if [ "$node_count" -le 8 ] && [ "$has_complex" = "no" ] && [ "$has_simple_apps" = "yes" ]; then
        if [ "$has_database" = "no" ] && [ "$has_crm" = "no" ]; then
            category="FREEMIUM"
            reason="Simple workflow (≤8 nodes) avec apps populaires, sans DB/CRM"
        fi
    fi
    
    # Force PREMIUM pour certains cas
    if [ "$node_count" -gt 15 ]; then
        category="PREMIUM"
        reason="Trop complexe (>15 nodes)"
    elif [ "$has_complex" = "yes" ]; then
        category="PREMIUM"
        reason="Marqué comme 'complex' dans le nom"
    elif [ "$has_database" = "yes" ] || [ "$has_crm" = "yes" ]; then
        category="PREMIUM"
        reason="Intégration DB/CRM"
    elif echo "$filename" | grep -i -E "(rag|langchain|enterprise|business)" > /dev/null; then
        category="PREMIUM"
        reason="Keywords enterprise/IA avancée"
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
    
    # Log des résultats
    echo "FILE: $relative_path" >> "$RESULTS_FILE"
    echo "  Category: $category -> $subcategory" >> "$RESULTS_FILE"
    echo "  Nodes: $node_count | Complex: $has_complex | AI: $has_ai | DB: $has_database | CRM: $has_crm" >> "$RESULTS_FILE"
    echo "  Reason: $reason" >> "$RESULTS_FILE"
    echo "" >> "$RESULTS_FILE"
    
    # Compter
    if [ "$category" = "FREEMIUM" ]; then
        ((freemium_count++))
    else
        ((premium_count++))
    fi
    ((total_count++))
    
    # Retourner la catégorie pour le script appelant
    echo "$category|$subcategory"
}

# Analyser tous les workflows JSON
echo "Analyse en cours..."
find "$WORKFLOWS_DIR" -name "*.json" -type f | while read file; do
    result=$(analyze_workflow "$file")
    category=$(echo "$result" | cut -d'|' -f1)
    subcategory=$(echo "$result" | cut -d'|' -f2)
    
    echo "$(basename "$file"): $category -> $subcategory"
done

echo "" >> "$RESULTS_FILE"
echo "=== STATISTIQUES ===" >> "$RESULTS_FILE"
echo "FREEMIUM: $freemium_count workflows" >> "$RESULTS_FILE"
echo "PREMIUM: $premium_count workflows" >> "$RESULTS_FILE"
echo "TOTAL: $total_count workflows" >> "$RESULTS_FILE"

echo ""
echo "Analyse terminée ! Résultats dans: $RESULTS_FILE"
echo "FREEMIUM: $freemium_count | PREMIUM: $premium_count | TOTAL: $total_count"