#!/bin/bash

echo "=== CRÉATION DE LA STRUCTURE FREEMIUM/PREMIUM ==="

# Créer les dossiers principaux
mkdir -p /var/www/automatehub/Freemium_Workflows
mkdir -p /var/www/automatehub/Premium_Workflows

# Créer les sous-catégories FREEMIUM (workflows simples et attractifs)
mkdir -p /var/www/automatehub/Freemium_Workflows/Social_Media_Basic
mkdir -p /var/www/automatehub/Freemium_Workflows/Email_Automation_Simple
mkdir -p /var/www/automatehub/Freemium_Workflows/Google_Workspace_Basics
mkdir -p /var/www/automatehub/Freemium_Workflows/Telegram_Notifications
mkdir -p /var/www/automatehub/Freemium_Workflows/Quick_AI_Tasks
mkdir -p /var/www/automatehub/Freemium_Workflows/File_Management_Basic
mkdir -p /var/www/automatehub/Freemium_Workflows/Form_to_Action
mkdir -p /var/www/automatehub/Freemium_Workflows/Personal_Productivity

# Créer les sous-catégories PREMIUM (workflows complexes)
mkdir -p /var/www/automatehub/Premium_Workflows/AI_Advanced_RAG
mkdir -p /var/www/automatehub/Premium_Workflows/CRM_Enterprise
mkdir -p /var/www/automatehub/Premium_Workflows/Database_Management
mkdir -p /var/www/automatehub/Premium_Workflows/Business_Intelligence
mkdir -p /var/www/automatehub/Premium_Workflows/Multi_System_Integration
mkdir -p /var/www/automatehub/Premium_Workflows/Security_Monitoring
mkdir -p /var/www/automatehub/Premium_Workflows/E_commerce_Advanced
mkdir -p /var/www/automatehub/Premium_Workflows/Custom_Development

echo "Structure des dossiers créée avec succès !"
echo ""
echo "FREEMIUM Categories:"
ls -la /var/www/automatehub/Freemium_Workflows/
echo ""
echo "PREMIUM Categories:"
ls -la /var/www/automatehub/Premium_Workflows/