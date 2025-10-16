#!/bin/bash

echo "🗂️ Organisation des workflows dans leurs dossiers respectifs"
echo "=========================================================="

# Fonction pour déterminer la catégorie d'un workflow
get_category() {
    local filename="$1"
    local filename_lower=$(echo "$filename" | tr '[:upper:]' '[:lower:]')
    
    # AI & ML
    if [[ "$filename_lower" =~ (ai[-_]powered|ai[-_]assistant|ai[-_]agent|openai|gpt|claude|langchain|gemini|llm|chat[-_]bot|chatbot|ai[-_]analysis|ai[-_]generated|ai[-_]enhanced|ai[-_]driven|machine[-_]learning|neural|ai_) ]]; then
        echo "AI _ ML"
    # Communication (Email, Slack, Teams, etc.)
    elif [[ "$filename_lower" =~ (email|gmail|outlook|mailchimp|sendgrid|slack|teams|discord|telegram|whatsapp|sms|twilio|message|messaging|notification|alert|communicate) ]]; then
        echo "communication"
    # CRM
    elif [[ "$filename_lower" =~ (hubspot|salesforce|pipedrive|zoho|crm|lead|customer|contact|deal|opportunity) ]]; then
        echo "crm"
    # Data Processing
    elif [[ "$filename_lower" =~ (database|postgres|mysql|mongodb|sql|data[-_]processing|etl|transform|aggregate|analytics|bigquery) ]]; then
        echo "data_processing"
    # File Management
    elif [[ "$filename_lower" =~ (file|pdf|csv|excel|spreadsheet|document|drive|dropbox|s3|upload|download|convert|compress) ]]; then
        echo "file_management"
    # Monitoring
    elif [[ "$filename_lower" =~ (monitor|alert|health[-_]check|error|logging|tracking|analytics|metrics) ]]; then
        echo "monitoring"
    # Social Media
    elif [[ "$filename_lower" =~ (twitter|facebook|instagram|linkedin|youtube|tiktok|social|post|tweet|video) ]]; then
        echo "SOCIAL MEDIA"
    # API Integration
    elif [[ "$filename_lower" =~ (webhook|api|http[-_]request|rest|graphql|integration|zapier) ]]; then
        echo "api_integration"
    # Utilities
    elif [[ "$filename_lower" =~ (utility|tool|helper|misc|general) ]]; then
        echo "utilities"
    # Automation (par défaut pour le reste)
    else
        echo "Automation"
    fi
}

# Organiser les workflows Freemium
echo "📁 Organisation des workflows FREEMIUM..."
echo "----------------------------------------"

cd /var/www/automatehub/Freemium_Workflows/
count=0

for file in *.json; do
    if [ -f "$file" ]; then
        category=$(get_category "$file")
        
        # Créer le dossier si nécessaire
        mkdir -p "$category"
        
        # Déplacer le fichier
        mv "$file" "$category/" 2>/dev/null
        
        if [ $? -eq 0 ]; then
            ((count++))
            echo "✅ $file → $category/"
        else
            echo "❌ Erreur avec $file"
        fi
    fi
done

echo ""
echo "📊 Freemium : $count workflows organisés"
echo ""

# Organiser les workflows Premium
echo "📁 Organisation des workflows PREMIUM..."
echo "---------------------------------------"

cd /var/www/automatehub/Premium_Workflows/
count=0

for file in *.json; do
    if [ -f "$file" ]; then
        category=$(get_category "$file")
        
        # Créer le dossier si nécessaire
        mkdir -p "$category"
        
        # Déplacer le fichier
        mv "$file" "$category/" 2>/dev/null
        
        if [ $? -eq 0 ]; then
            ((count++))
            echo "✅ $file → $category/"
        else
            echo "❌ Erreur avec $file"
        fi
    fi
done

echo ""
echo "📊 Premium : $count workflows organisés"
echo ""

# Afficher les statistiques finales
echo "📊 STATISTIQUES FINALES"
echo "======================"

echo ""
echo "FREEMIUM_WORKFLOWS :"
for dir in /var/www/automatehub/Freemium_Workflows/*/; do
    if [ -d "$dir" ]; then
        dirname=$(basename "$dir")
        count=$(ls "$dir"*.json 2>/dev/null | wc -l)
        echo "  - $dirname : $count workflows"
    fi
done

echo ""
echo "PREMIUM_WORKFLOWS :"
for dir in /var/www/automatehub/Premium_Workflows/*/; do
    if [ -d "$dir" ]; then
        dirname=$(basename "$dir")
        count=$(ls "$dir"*.json 2>/dev/null | wc -l)
        echo "  - $dirname : $count workflows"
    fi
done

echo ""
echo "✅ Organisation terminée !"