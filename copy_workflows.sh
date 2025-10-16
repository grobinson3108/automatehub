#!/bin/bash

# Script pour copier les meilleurs workflows dans chaque pack

# PACK 02 - IA BUSINESS DOMINATION
cp "github_workflows/n8n-workflows/workflows/Googlesheets/1106_GoogleSheets_Cron_Automate_Scheduled.json" "Packs_Workflows_Optimises/02_IA_BUSINESS_DOMINATION/"
cp "github_workflows/n8n-workflows/workflows/Googlesheets/0496_GoogleSheets_Webhook_Automate_Webhook.json" "Packs_Workflows_Optimises/02_IA_BUSINESS_DOMINATION/"
cp "github_workflows/n8n-workflows/workflows/Googlesheets/1188_GoogleSheets_Emailreadimap_Create.json" "Packs_Workflows_Optimises/02_IA_BUSINESS_DOMINATION/"
cp "github_workflows/n8n-workflows/workflows/Googlesheets/0837_GoogleSheets_Gmail_Create_Triggered.json" "Packs_Workflows_Optimises/02_IA_BUSINESS_DOMINATION/"

# Ajouter workflows de données et IA
cp "github_workflows/n8n-workflows/workflows/Automation/1250_Automation.json" "Packs_Workflows_Optimises/02_IA_BUSINESS_DOMINATION/"
cp "github_workflows/n8n-workflows/workflows/Automation/1497_Automation.json" "Packs_Workflows_Optimises/02_IA_BUSINESS_DOMINATION/"

# PACK 03 - TELEGRAM MARKETING EMPIRE
find "github_workflows/n8n-workflows/workflows/Telegram" -name "*.json" | head -10 | while read file; do
    cp "$file" "Packs_Workflows_Optimises/03_TELEGRAM_MARKETING_EMPIRE/"
done

# PACK 04 - LEAD GENERATION MONSTER
cp "github_workflows/n8n-workflows/workflows/Hubspot"/*.json "Packs_Workflows_Optimises/04_LEAD_GENERATION_MONSTER/" 2>/dev/null || true
cp "github_workflows/n8n-workflows/workflows/Mailchimp"/*.json "Packs_Workflows_Optimises/04_LEAD_GENERATION_MONSTER/" | head -5 2>/dev/null || true

# PACK 05 - EMAIL AUTOMATION KING
find "github_workflows/n8n-workflows/workflows/Gmail" -name "*.json" | head -10 | while read file; do
    cp "$file" "Packs_Workflows_Optimises/05_EMAIL_AUTOMATION_KING/"
done

# PACK 06 - SOCIAL MEDIA AUTOPILOT
cp "github_workflows/n8n-workflows/workflows/Twitter"/*.json "Packs_Workflows_Optimises/06_SOCIAL_MEDIA_AUTOPILOT/" 2>/dev/null || true
cp "github_workflows/n8n-workflows/workflows/Linkedin"/*.json "Packs_Workflows_Optimises/06_SOCIAL_MEDIA_AUTOPILOT/" 2>/dev/null || true

# PACK 07 - DATA ANALYTICS PRO
find "github_workflows/n8n-workflows/workflows/Googleanalytics" -name "*.json" | head -10 | while read file; do
    cp "$file" "Packs_Workflows_Optimises/07_DATA_ANALYTICS_PRO/"
done

# PACK 08 - ECOMMERCE CASH MACHINE
cp "github_workflows/n8n-workflows/workflows/Shopify"/*.json "Packs_Workflows_Optimises/08_ECOMMERCE_CASH_MACHINE/" 2>/dev/null || true
cp "github_workflows/n8n-workflows/workflows/Woocommerce"/*.json "Packs_Workflows_Optimises/08_ECOMMERCE_CASH_MACHINE/" 2>/dev/null || true

# PACK 09 - TEAM PRODUCTIVITY BEAST
cp "github_workflows/n8n-workflows/workflows/Slack"/*.json "Packs_Workflows_Optimises/09_TEAM_PRODUCTIVITY_BEAST/" 2>/dev/null || true
cp "github_workflows/n8n-workflows/workflows/Asana"/*.json "Packs_Workflows_Optimises/09_TEAM_PRODUCTIVITY_BEAST/" 2>/dev/null || true

# PACK 10 - CUSTOMER SUCCESS AI
cp "github_workflows/n8n-workflows/workflows/Intercom"/*.json "Packs_Workflows_Optimises/10_CUSTOMER_SUCCESS_AI/" 2>/dev/null || true
cp "github_workflows/n8n-workflows/workflows/Zendesk"/*.json "Packs_Workflows_Optimises/10_CUSTOMER_SUCCESS_AI/" 2>/dev/null || true

echo "✅ Tous les workflows ont été copiés dans les packs !"