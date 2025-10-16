#!/usr/bin/env python3
"""Copie directe des TOP 100 workflows sélectionnés"""

import shutil
from pathlib import Path

# Configuration
source_dir = Path("/var/www/automatehub/Freemium_Workflows")
target_dir = Path("/var/www/automatehub/TOP_100_PRIORITAIRES")

# Créer le dossier de destination s'il n'existe pas
target_dir.mkdir(exist_ok=True)

# TOP 20 workflows les plus prioritaires pour commencer
top_priority_workflows = [
    ("Log_New_Gmail_Messages_to_Google_Sheets_Automatically.json", "Gmail_vers_Google_Sheets_Auto.json"),
    ("Send_Kindle_Books_via_Telegram_Bot_Commands.json", "Bot_Telegram_Envoyer_Livres_Kindle.json"),
    ("Monitor_Emails_and_Send_Telegram_Alerts.json", "Surveillance_Email_Alertes_Telegram.json"),
    ("Forward_Filtered_Gmail_Messages_to_Telegram.json", "Gmail_Filtre_vers_Telegram.json"),
    ("Create_Google_Task_from_New_Gmail_Message.json", "Gmail_vers_Google_Tasks.json"),
    ("Email_Attachment_Upload_to_Google_Drive.json", "Pieces_Jointes_vers_Drive.json"),
    ("YouTube_Video_Updates_to_Telegram_Channel.json", "YouTube_Notifications_Telegram.json"),
    ("Classify_Incoming_Emails_Using_OpenAI_Language_Model.json", "Classification_Emails_OpenAI.json"),
    ("AI-Powered_Email_Response_with_Approval_System.json", "Reponses_Email_IA_Approbation.json"),
    ("Extract_Text_from_PDF_Files.json", "Extraction_Texte_PDF.json"),
    ("Typeform_to_Sheets_with_Slack_and_Email_Notifications.json", "Typeform_vers_Sheets_Notifications.json"),
    ("AI_Email_Assistant_for_Gmail_Responses.json", "Assistant_IA_Reponses_Gmail.json"),
    ("Telegram-Triggered_AI_Chat_with_Gmail_Integration.json", "Chat_IA_Telegram_Gmail.json"),
    ("webhook_automation_googlesheets_gmail.json", "Webhook_Gmail_vers_Sheets.json"),
    ("Email-Triggered_Slack_Notifications_for_Delivery_Accounting.json", "Email_vers_Notifications_Slack.json"),
    ("AI-Powered_Gmail_Auto-Reply_Draft_Creator.json", "Gmail_Reponses_Auto_IA.json"),
    ("Convert_Google_Drive_Files_to_WordPress_Posts.json", "Drive_vers_WordPress_Posts.json"),
    ("Classify_and_Organize_Gmail_Emails_Using_AI.json", "Classification_Emails_Gmail_IA.json"),
    ("Email_to_Nextcloud_Deck_Card_Converter.json", "Email_vers_Cartes_Nextcloud.json"),
    ("Forward_Filtered_Gmail_Notifications_to_Telegram_Chat.json", "Gmail_Notifications_Telegram.json")
]

print("🚀 Copie des TOP 20 workflows prioritaires")
print("=" * 50)

copied = 0
errors = []

for original, french in top_priority_workflows:
    source_path = source_dir / original
    target_path = target_dir / french
    
    print(f"📂 {original}")
    print(f"   → {french}")
    
    if source_path.exists():
        try:
            shutil.copy2(source_path, target_path)
            print(f"   ✅ Copié avec succès")
            copied += 1
        except Exception as e:
            print(f"   ❌ Erreur: {e}")
            errors.append(f"{original}: {e}")
    else:
        print(f"   ❌ Fichier source introuvable")
        errors.append(f"{original}: fichier introuvable")
    
    print()

print(f"📊 Résumé: {copied}/{len(top_priority_workflows)} workflows copiés")
if errors:
    print(f"❌ {len(errors)} erreurs détectées")

print(f"📁 Destination: {target_dir}")