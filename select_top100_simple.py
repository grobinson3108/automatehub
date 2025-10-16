#!/usr/bin/env python3
"""
Version simplifiée pour sélectionner les TOP 100 workflows
basée sur l'analyse des noms de fichiers et critères de viralité
"""

import json
import os
import shutil
from pathlib import Path
import re

def analyze_filename_for_score(filename):
    """Analyse le nom du fichier pour déterminer son potentiel viral"""
    filename_lower = filename.lower()
    
    # Score de base
    score = 0
    
    # Apps populaires (score élevé)
    popular_apps = {
        'gmail': 100, 'telegram': 95, 'sheets': 90, 'slack': 85,
        'openai': 95, 'drive': 80, 'webhook': 75, 'email': 90,
        'discord': 70, 'youtube': 80, 'ai': 90, 'typeform': 65
    }
    
    for app, points in popular_apps.items():
        if app in filename_lower:
            score += points
    
    # Actions virales (problèmes courants)
    viral_actions = {
        'automatic': 80, 'auto': 70, 'log': 60, 'monitor': 65,
        'alert': 70, 'notification': 75, 'forward': 60, 'sync': 65,
        'backup': 50, 'schedule': 55, 'daily': 45, 'reminder': 60,
        'create': 40, 'send': 45, 'track': 50, 'analyze': 55,
        'classify': 60, 'extract': 45, 'convert': 40, 'upload': 45
    }
    
    for action, points in viral_actions.items():
        if action in filename_lower:
            score += points
    
    # Bonus pour simplicité (éviter les complex)
    if 'complex' in filename_lower:
        score -= 100
    
    # Bonus pour intégrations populaires
    integrations = ['gmail_to_sheets', 'telegram_bot', 'email_to_slack', 'ai_assistant']
    for integration in integrations:
        if any(word in filename_lower for word in integration.split('_')):
            score += 30
    
    return score

def translate_filename(filename):
    """Traduit le nom anglais vers français optimisé"""
    
    # Dictionnaire de traductions directes pour les meilleurs
    direct_translations = {
        'Log_New_Gmail_Messages_to_Google_Sheets_Automatically.json': 'Gmail_vers_Google_Sheets_Auto.json',
        'Send_Kindle_Books_via_Telegram_Bot_Commands.json': 'Bot_Telegram_Envoyer_Livres_Kindle.json',
        'Monitor_Emails_and_Send_Telegram_Alerts.json': 'Surveillance_Email_Alertes_Telegram.json',
        'Forward_Filtered_Gmail_Messages_to_Telegram.json': 'Gmail_Filtre_vers_Telegram.json',
        'Create_Google_Task_from_New_Gmail_Message.json': 'Gmail_vers_Google_Tasks.json',
        'Email_Attachment_Upload_to_Google_Drive.json': 'Pieces_Jointes_vers_Drive.json',
        'YouTube_Video_Updates_to_Telegram_Channel.json': 'YouTube_Notifications_Telegram.json',
        'Classify_Incoming_Emails_Using_OpenAI_Language_Model.json': 'Classification_Emails_OpenAI.json',
        'AI-Powered_Email_Response_with_Approval_System.json': 'Reponses_Email_IA_Approbation.json',
        'Extract_Text_from_PDF_Files.json': 'Extraction_Texte_PDF.json',
        'Typeform_to_Sheets_with_Slack_and_Email_Notifications.json': 'Typeform_vers_Sheets_Notifications.json',
        'AI_Email_Assistant_for_Gmail_Responses.json': 'Assistant_IA_Reponses_Gmail.json',
        'Telegram-Triggered_AI_Chat_with_Gmail_Integration.json': 'Chat_IA_Telegram_Gmail.json',
        'webhook_automation_googlesheets_gmail.json': 'Webhook_Gmail_vers_Sheets.json',
        'Email-Triggered_Slack_Notifications_for_Delivery_Accounting.json': 'Email_vers_Notifications_Slack.json',
        'AI-Powered_Gmail_Auto-Reply_Draft_Creator.json': 'Gmail_Reponses_Auto_IA.json',
        'Convert_Google_Drive_Files_to_WordPress_Posts.json': 'Drive_vers_WordPress_Posts.json',
        'Process_Invoices_and_Send_Notifications_via_Email_and_Slack.json': 'Traitement_Factures_Notifications.json',
        'Email_Processing_and_AI-Powered_Content_Analysis.json': 'Traitement_Email_Analyse_IA.json'
    }
    
    if filename in direct_translations:
        return direct_translations[filename]
    
    # Auto-traduction pour les autres
    name = filename.replace('.json', '')
    
    # Remplacements simples
    replacements = {
        'gmail': 'Gmail', 'telegram': 'Telegram', 'slack': 'Slack',
        'sheets': 'Sheets', 'drive': 'Drive', 'email': 'Email',
        'webhook': 'Webhook', 'ai': 'IA', 'openai': 'OpenAI',
        'automatic': 'Auto', 'auto': 'Auto', 'send': 'Envoyer',
        'create': 'Creer', 'monitor': 'Surveillance', 'alert': 'Alerte',
        'notification': 'Notification', 'forward': 'Transfert',
        'extract': 'Extraction', 'convert': 'Conversion',
        'upload': 'Upload', 'download': 'Download', 'sync': 'Sync',
        'analyze': 'Analyser', 'process': 'Traitement'
    }
    
    name_lower = name.lower()
    for en, fr in replacements.items():
        name_lower = name_lower.replace(en, fr)
    
    # Nettoyer et capitaliser
    name_clean = re.sub(r'[^a-zA-Z0-9_]', '_', name_lower)
    name_clean = re.sub(r'_+', '_', name_clean).strip('_')
    
    parts = [part.capitalize() for part in name_clean.split('_') if part]
    result = '_'.join(parts[:7]) + '.json'  # Max 7 mots
    
    return result

def get_top_100_workflows():
    """Sélectionne les TOP 100 workflows basé sur l'analyse des noms"""
    
    # Workflows prioritaires identifiés manuellement
    high_priority = [
        'Log_New_Gmail_Messages_to_Google_Sheets_Automatically.json',
        'Send_Kindle_Books_via_Telegram_Bot_Commands.json',
        'Monitor_Emails_and_Send_Telegram_Alerts.json',
        'Forward_Filtered_Gmail_Messages_to_Telegram.json',
        'Create_Google_Task_from_New_Gmail_Message.json',
        'Email_Attachment_Upload_to_Google_Drive.json',
        'YouTube_Video_Updates_to_Telegram_Channel.json',
        'Classify_Incoming_Emails_Using_OpenAI_Language_Model.json',
        'AI-Powered_Email_Response_with_Approval_System.json',
        'Extract_Text_from_PDF_Files.json',
        'Typeform_to_Sheets_with_Slack_and_Email_Notifications.json',
        'AI_Email_Assistant_for_Gmail_Responses.json',
        'Telegram-Triggered_AI_Chat_with_Gmail_Integration.json',
        'webhook_automation_googlesheets_gmail.json',
        'Email-Triggered_Slack_Notifications_for_Delivery_Accounting.json',
        'AI-Powered_Gmail_Auto-Reply_Draft_Creator.json',
        'Convert_Google_Drive_Files_to_WordPress_Posts.json',
        'Classify_and_Organize_Gmail_Emails_Using_AI.json',
        'Email_to_Nextcloud_Deck_Card_Converter.json',
        'Forward_Filtered_Gmail_Notifications_to_Telegram_Chat.json',
        'Email_Summary_and_AI-Assisted_Response_System.json',
        'AI-Powered_Email_Response_and_Management_Assistant.json',
        'Email_Processing_and_AI-Powered_Content_Analysis.json',
        'process_gmail_googledrive.json',
        'AI_Email_Summarizer_with_Messenger_Notification.json',
        'Email_Responder_with_AI-Powered_Telegram_Notifications.json',
        'Validate_HubSpot_Contact_Emails_and_Notify_via_Slack.json',
        'HubSpot_Lead_Enrichment_from_Email_Analysis.json',
        'Lead_Tracking_Google_Sheets_to_HubSpot_with_Email_Alerts.json',
        'Extract_Email_Expenses_and_Log_in_Google_Sheets.json',
        '📦_New_Email_➔_Create_Google_Task.json',
        'Create_Nextcloud_Deck_card_from_email.json',
        'Auto-Unsubscribe_Contacts_from_Mautic_via_Gmail_Trigger.json',
        'Convert_Spreadsheet_to_Email_Attachment.json',
        'Email_Distribution_from_Obsidian_Notes.json',
        'Forward_Netflix_Emails_to_Multiple_Recipients_via_Gmail.json',
        'Telegram_AI_Assistant_for_Calendar_and_Email_Management.json',
        'process_youtube_telegram.json',
        'Scrape_Book_Data,_Clean,_and_Email_as_CSV_Report.json',
        'Extract_and_Process_File_Content_with_AI_Assistance.json',
        'Extract_PDF_Content_and_Publish_as_Blog_Post.json',
        'Spreadsheet_Data_Processing_and_API_Integration.json',
        'Create_Delivery_Tasks_from_Spreadsheet_Data.json',
        'Email_Processing_with_Spreadsheet_and_Slack_Updates.json',
        'Slack_Channel_Creation_and_Team_Communication_Setup.json',
        'Extract_and_Analyze_PDF_Images_Using_AI_and_Google_Drive.json'
    ]
    
    source_dir = Path("/var/www/automatehub/Freemium_Workflows")
    all_files = list(source_dir.glob("*.json"))
    
    # Analyser tous les fichiers
    scored_workflows = []
    for filepath in all_files:
        filename = filepath.name
        score = analyze_filename_for_score(filename)
        
        # Bonus pour les prioritaires identifiés
        if filename in high_priority:
            score += 150
        
        scored_workflows.append({
            'filename': filename,
            'filepath': filepath,
            'score': score,
            'french_name': translate_filename(filename)
        })
    
    # Trier par score décroissant
    scored_workflows.sort(key=lambda x: x['score'], reverse=True)
    
    return scored_workflows[:100]

def main():
    print("🚀 Sélection des TOP 100 workflows pour YouTube")
    print("=" * 60)
    
    # Créer le dossier de destination
    target_dir = Path("/var/www/automatehub/TOP_100_PRIORITAIRES")
    target_dir.mkdir(exist_ok=True)
    
    # Sélectionner les TOP 100
    top_100 = get_top_100_workflows()
    
    print(f"✅ {len(top_100)} workflows sélectionnés")
    
    # Copier les fichiers avec les nouveaux noms
    print("\n📂 Copie des fichiers...")
    for i, workflow in enumerate(top_100, 1):
        source_path = workflow['filepath']
        target_filename = workflow['french_name']
        target_path = target_dir / target_filename
        
        try:
            shutil.copy2(source_path, target_path)
            if i <= 10:  # Afficher les 10 premiers
                print(f"{i:2d}. {workflow['filename']} → {target_filename} (Score: {workflow['score']:.0f})")
            elif i % 10 == 0:  # Afficher progression
                print(f"... {i}/100 workflows copiés")
        except Exception as e:
            print(f"❌ Erreur {workflow['filename']}: {e}")
    
    # Créer un rapport simple
    report_lines = [
        "# 🎯 TOP 100 WORKFLOWS PRIORITAIRES\n",
        "## 🏆 Liste complète des workflows sélectionnés\n",
        "| Rang | Score | Nom Original | Nom Français |",
        "|------|-------|--------------|--------------|"
    ]
    
    for i, workflow in enumerate(top_100, 1):
        report_lines.append(
            f"| {i:2d} | {workflow['score']:.0f} | "
            f"{workflow['filename'][:50]} | {workflow['french_name']} |"
        )
    
    # Sauvegarder le rapport
    report_path = Path("/var/www/automatehub/TOP_100_RAPPORT.md")
    with open(report_path, 'w', encoding='utf-8') as f:
        f.write('\n'.join(report_lines))
    
    print(f"\n📋 Rapport généré: {report_path}")
    print(f"📁 Workflows dans: {target_dir}")
    print("\n🎉 Sélection terminée!")
    
    print("\n🏆 TOP 10:")
    for i, workflow in enumerate(top_100[:10], 1):
        print(f"{i:2d}. {workflow['french_name']} (Score: {workflow['score']:.0f})")

if __name__ == "__main__":
    main()