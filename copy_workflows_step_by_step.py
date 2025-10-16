#!/usr/bin/env python3
"""
Script simple pour copier les workflows step by step
"""

import shutil
import json
from pathlib import Path

def main():
    print("🚀 COPIE DES TOP 100 WORKFLOWS - VERSION SIMPLIFIÉE")
    print("=" * 60)
    
    source_dir = Path("/var/www/automatehub/Freemium_Workflows")
    target_dir = Path("/var/www/automatehub/TOP_100_PRIORITAIRES")
    
    # S'assurer que le dossier de destination existe
    target_dir.mkdir(exist_ok=True)
    
    # Top 10 workflows les plus viraux pour commencer
    top_10_viral = [
        ("Monitor_Emails_and_Send_Telegram_Alerts.json", "Surveillance_Email_Alertes_Telegram.json"),
        ("Forward_Filtered_Gmail_Messages_to_Telegram.json", "Gmail_Filtre_vers_Telegram.json"),
        ("Create_Google_Task_from_New_Gmail_Message.json", "Gmail_vers_Google_Tasks.json"),
        ("Email_Attachment_Upload_to_Google_Drive.json", "Pieces_Jointes_vers_Drive.json"),
        ("YouTube_Video_Updates_to_Telegram_Channel.json", "YouTube_Notifications_Telegram.json"),
        ("Classify_Incoming_Emails_Using_OpenAI_Language_Model.json", "Classification_Emails_OpenAI.json"),
        ("AI-Powered_Email_Response_with_Approval_System.json", "Reponses_Email_IA_Approbation.json"),
        ("Extract_Text_from_PDF_Files.json", "Extraction_Texte_PDF.json"),
        ("webhook_automation_googlesheets_gmail.json", "Webhook_Gmail_vers_Sheets.json"),
        ("AI_Email_Assistant_for_Gmail_Responses.json", "Assistant_IA_Reponses_Gmail.json")
    ]
    
    copied = 2  # On a déjà les 2 premiers
    
    print("📂 Copie des 10 workflows les plus viraux...")
    
    for i, (original, french) in enumerate(top_10_viral, 3):
        source_file = source_dir / original
        target_file = target_dir / french
        
        print(f"{i:2d}. {original[:60]}...")
        print(f"    → {french}")
        
        if source_file.exists():
            try:
                shutil.copy2(source_file, target_file)
                print(f"    ✅ Copié")
                copied += 1
            except Exception as e:
                print(f"    ❌ Erreur: {e}")
        else:
            print(f"    ❌ Fichier source introuvable")
        
        print()
    
    print(f"📊 Statut actuel: {copied}/100 workflows copiés")
    
    # Liste des workflows restants les plus importants
    remaining_important = [
        "Telegram-Triggered_AI_Chat_with_Gmail_Integration.json",
        "Email-Triggered_Slack_Notifications_for_Delivery_Accounting.json",
        "AI-Powered_Gmail_Auto-Reply_Draft_Creator.json",
        "Convert_Google_Drive_Files_to_WordPress_Posts.json",
        "Typeform_to_Sheets_with_Slack_and_Email_Notifications.json",
        "Email_to_Nextcloud_Deck_Card_Converter.json",
        "Forward_Filtered_Gmail_Notifications_to_Telegram_Chat.json",
        "Email_Processing_and_AI-Powered_Content_Analysis.json",
        "process_gmail_googledrive.json",
        "AI_Email_Summarizer_with_Messenger_Notification.json"
    ]
    
    print("📂 Copie des 10 workflows suivants les plus importants...")
    
    for i, filename in enumerate(remaining_important, copied + 1):
        if copied >= 100:
            break
            
        source_file = source_dir / filename
        # Générer nom français simple
        french_name = filename.replace('.json', '').replace('_', '_').replace('-', '_')
        french_name = french_name[:50] + '_FR.json'  # Nom simple
        target_file = target_dir / french_name
        
        print(f"{i:2d}. {filename[:50]}...")
        print(f"    → {french_name}")
        
        if source_file.exists():
            try:
                shutil.copy2(source_file, target_file)
                print(f"    ✅ Copié")
                copied += 1
            except Exception as e:
                print(f"    ❌ Erreur: {e}")
        else:
            print(f"    ❌ Fichier source introuvable")
        
        print()
    
    print(f"🎯 Total final: {copied}/100 workflows copiés dans {target_dir}")
    print("✅ Phase initiale terminée - Les workflows les plus viraux sont prêts!")

if __name__ == "__main__":
    main()