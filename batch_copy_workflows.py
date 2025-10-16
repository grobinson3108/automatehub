#!/usr/bin/env python3
"""
Script pour copier en lot les workflows restants du TOP 100
"""

import shutil
import json
from pathlib import Path

def copy_workflow_files():
    source_dir = Path("/var/www/automatehub/Freemium_Workflows")
    target_dir = Path("/var/www/automatehub/TOP_100_PRIORITAIRES")
    
    # Les 98 workflows restants à copier
    remaining_workflows = [
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
        ("Forward_Filtered_Gmail_Notifications_to_Telegram_Chat.json", "Gmail_Notifications_Telegram.json"),
        ("Email_Summary_and_AI-Assisted_Response_System.json", "Resume_Email_IA_Reponses.json"),
        ("AI-Powered_Email_Response_and_Management_Assistant.json", "Assistant_IA_Gestion_Email.json"),
        ("Email_Processing_and_AI-Powered_Content_Analysis.json", "Traitement_Email_Analyse_IA.json"),
        ("process_gmail_googledrive.json", "Traitement_Gmail_Drive.json"),
        ("AI_Email_Summarizer_with_Messenger_Notification.json", "Resume_Email_IA_Notifications.json"),
        ("Email_Responder_with_AI-Powered_Telegram_Notifications.json", "Reponses_Email_IA_Telegram.json"),
        ("Validate_HubSpot_Contact_Emails_and_Notify_via_Slack.json", "Validation_Emails_HubSpot_Slack.json"),
        ("HubSpot_Lead_Enrichment_from_Email_Analysis.json", "Enrichissement_Leads_HubSpot_Email.json"),
        ("Lead_Tracking_Google_Sheets_to_HubSpot_with_Email_Alerts.json", "Suivi_Leads_Sheets_HubSpot.json"),
        ("Extract_Email_Expenses_and_Log_in_Google_Sheets.json", "Extraction_Depenses_Email_Sheets.json"),
        ("📦_New_Email_➔_Create_Google_Task.json", "Email_vers_Google_Tasks.json"),
        ("Create_Nextcloud_Deck_card_from_email.json", "Creation_Cartes_Nextcloud_Email.json"),
        ("Auto-Unsubscribe_Contacts_from_Mautic_via_Gmail_Trigger.json", "Desabonnement_Auto_Mautic_Gmail.json"),
        ("Convert_Spreadsheet_to_Email_Attachment.json", "Conversion_Tableur_PJ_Email.json"),
        ("Email_Distribution_from_Obsidian_Notes.json", "Distribution_Email_Notes_Obsidian.json"),
        ("Forward_Netflix_Emails_to_Multiple_Recipients_via_Gmail.json", "Transfert_Emails_Netflix_Gmail.json"),
        ("Telegram_AI_Assistant_for_Calendar_and_Email_Management.json", "Assistant_IA_Telegram_Calendrier.json"),
        ("process_youtube_telegram.json", "YouTube_vers_Telegram.json"),
        ("Scrape_Book_Data,_Clean,_and_Email_as_CSV_Report.json", "Extraction_Donnees_Livres_Rapport.json"),
        ("Extract_and_Process_File_Content_with_AI_Assistance.json", "Extraction_Fichiers_IA.json"),
        ("Extract_PDF_Content_and_Publish_as_Blog_Post.json", "PDF_vers_Article_Blog.json"),
        ("Spreadsheet_Data_Processing_and_API_Integration.json", "Traitement_Donnees_Tableur_API.json"),
        ("Create_Delivery_Tasks_from_Spreadsheet_Data.json", "Creation_Taches_Livraison_Sheets.json"),
        ("Email_Processing_with_Spreadsheet_and_Slack_Updates.json", "Traitement_Email_Sheets_Slack.json"),
        ("Slack_Channel_Creation_and_Team_Communication_Setup.json", "Creation_Canaux_Slack_Equipe.json"),
        ("Extract_and_Analyze_PDF_Images_Using_AI_and_Google_Drive.json", "Analyse_Images_PDF_IA_Drive.json"),
        ("Monitor_Incoming_Emails_via_IMAP.json", "Surveillance_Emails_IMAP.json"),
        ("Process_Invoices_and_Send_Notifications_via_Email_and_Slack.json", "Traitement_Factures_Notifications.json"),
        ("webhook_communicate_slack_emailsend.json", "Webhook_Communication_Slack_Email.json"),
        ("Webhook_Triggers_Email_and_API_Call_with_Custom_Logic.json", "Webhook_Email_API_Logique.json"),
        ("Email_Trigger_to_AI-Enhanced_Web_Request_and_Response.json", "Email_vers_Requete_Web_IA.json"),
        ("Email_Trigger_to_AI-Powered_Response_System.json", "Email_vers_Systeme_Reponse_IA.json"),
        ("communicate_emailreadimap.json", "Communication_Lecture_Email_IMAP.json"),
        ("communicate_emailreadimap_nextcloud.json", "Email_IMAP_vers_Nextcloud.json"),
        ("communicate_emailreadimap_mindee_6nodes.json", "Email_IMAP_Mindee_OCR.json"),
        ("notify_emailreadimap_mindee_6nodes.json", "Notifications_Email_IMAP_Mindee.json"),
        ("AI_Email_Summary_to_Messenger_Delivery.json", "Resume_Email_IA_Livraison.json"),
        ("Email-Triggered_AI_Assistant_with_Slack_Notifications.json", "Assistant_IA_Email_Slack.json"),
        ("file_ops_movebinarydata_spreadsheetfile.json", "Operations_Fichiers_Donnees_Tableur.json"),
        ("file_ops_readpdf_manual.json", "Lecture_PDF_Manuel.json"),
        ("file_ops_movebinarydata_readbinaryfile.json", "Operations_Fichiers_Binaires.json"),
        ("webhook_file_ops_converttofile_6nodes.json", "Webhook_Conversion_Fichiers.json"),
        ("webhook_communicate_slack_onesimpleapi.json", "Webhook_Slack_API_Simple.json"),
        ("communicate_emailreadimap_httprequest_7nodes.json", "Email_IMAP_Requetes_HTTP.json"),
        ("communicate_emailreadimap_wait_7nodes.json", "Email_IMAP_Attente_Temporisee.json"),
        ("Validate_Mautic_Contact_Emails_and_Send_Alerts.json", "Validation_Emails_Mautic_Alertes.json"),
        ("Form_to_Discord_Email_Leads_and_Update_Sheets.json", "Formulaire_Discord_Leads_Sheets.json"),
        ("Email_Threat_Analysis_and_Alert_Creation_in_TheHive.json", "Analyse_Menaces_Email_TheHive.json"),
        ("Parse_and_Store_DMARC_Email_Reports_in_Database.json", "Analyse_Rapports_DMARC_BDD.json"),
        ("Slack_Alerts_for_Quarantined_Emails_with_Jira_Ticketing.json", "Alertes_Slack_Emails_Quarantaine.json"),
        ("Empty_Workflow_Ready_for_Configuration.json", "Workflow_Vide_Configuration.json"),
        ("file_ops_httprequest_spreadsheetfile_6nodes.json", "Requetes_HTTP_Fichiers_Tableur.json"),
        ("file_ops_converttofile_manual_7nodes.json", "Conversion_Fichiers_Manuel.json"),
        ("file_ops_slack_emailreadimap_9nodes.json", "Fichiers_Slack_Email_IMAP.json"),
        ("Typeform_to_Sheets_with_Slack_and_Email_Notifications_1.json", "Typeform_Sheets_Notifications_V1.json"),
        ("Typeform_to_Sheets_with_Slack_and_Email_Notifications_2.json", "Typeform_Sheets_Notifications_V2.json"),
        ("webhook_communicate_slack_emailsend_1.json", "Webhook_Slack_Email_V1.json"),
        ("webhook_communicate_slack_emailsend_2.json", "Webhook_Slack_Email_V2.json"),
        ("Email_Processing_and_AI-Powered_Content_Analysis_1.json", "Traitement_Email_IA_Contenu_V1.json"),
        ("Email_Summary_and_AI-Assisted_Response_System_1.json", "Resume_Email_IA_Reponses_V1.json"),
        ("communicate_emailreadimap_httprequest_7nodes_1.json", "Email_IMAP_HTTP_V1.json"),
        ("communicate_emailreadimap_httprequest_7nodes_2.json", "Email_IMAP_HTTP_V2.json"),
        ("Telegram_AI_Assistant_for_Calendar_and_Email_Management_1.json", "Assistant_IA_Telegram_V1.json"),
        ("send_file_to_kindle_through_telegram_bot_8nodes.json", "Envoi_Fichiers_Kindle_Telegram.json"),
        ("file_ops_emailreadimap_extractfromfile_10nodes.json", "Email_IMAP_Extraction_Fichiers.json"),
        ("Forward_Netflix_emails_to_multiple_email_addresses_with_GMail_and_Mailjet_7nodes.json", "Netflix_Multi_Emails_Gmail.json"),
        ("Strava_Activity_Email_Summary_with_AI_Analysis.json", "Strava_Resume_IA_Email.json"),
        ("Strava_Activity_to_Personalized_Email_Summary.json", "Activite_Strava_Resume_Email.json"),
        ("webhook_process_gmailtool_executeworkflow_complex_12nodes.json", "Webhook_Gmail_Execution_Workflow.json"),
        ("Notify_user_in_Slack_of_quarantined_email_and_create_Jira_ticket_if_opened_complex_13nodes.json", "Slack_Email_Quarantaine_Jira.json"),
        ("AI-Powered_Email_Response_with_Approval_Workflow.json", "Workflow_Approbation_Email_IA.json"),
        ("webhook_communicate_slack_manual_6nodes.json", "Webhook_Slack_Manuel.json"),
        ("webhook_communicate_slack_onesimpleapi_6nodes.json", "Webhook_Slack_API_V6.json"),
        ("Email_Attachment_Upload_to_NextCloud.json", "PJ_Email_vers_NextCloud.json"),
        ("file_ops_movebinarydata_readbinaryfile_1.json", "Operations_Fichiers_Binaires_V1.json"),
        ("file_ops_googlesheets_gmail_complex_14nodes.json", "Google_Sheets_Gmail_Avance.json"),
        ("Extract,_Split_and_Process_File_Content_with_AI.json", "Extraction_Division_Fichiers_IA.json"),
        ("AI-Powered_Email_Summary_to_Messenger_Service.json", "Resume_Email_IA_Messenger.json"),
        ("Gmail-Triggered_AI_Analysis_and_Email_Response.json", "Gmail_Analyse_IA_Reponse.json")
    ]
    
    print(f"🚀 Copie de {len(remaining_workflows)} workflows restants...")
    
    success_count = 0
    error_count = 0
    errors = []
    
    for i, (original, french) in enumerate(remaining_workflows, 3):  # Commencer à 3 car on a déjà copié 2
        source_file = source_dir / original
        target_file = target_dir / french
        
        if source_file.exists():
            try:
                # Vérifier que c'est un JSON valide
                with open(source_file, 'r', encoding='utf-8') as f:
                    data = json.load(f)
                
                # Copier le fichier
                shutil.copy2(source_file, target_file)
                success_count += 1
                
                if i <= 12:  # Afficher les 10 premiers
                    print(f"✅ {i:2d}. {french}")
                elif i % 10 == 0:  # Progression
                    print(f"... {i}/100 workflows traités")
                
            except Exception as e:
                error_count += 1
                errors.append(f"❌ {original}: {e}")
        else:
            error_count += 1
            errors.append(f"❌ {original}: fichier introuvable")
    
    print(f"\n📊 Résumé final:")
    print(f"✅ {success_count + 2} workflows copiés avec succès (incluant les 2 premiers)")
    print(f"❌ {error_count} erreurs")
    
    if errors:
        print(f"\n🚨 Première erreurs:")
        for error in errors[:5]:
            print(f"  {error}")
    
    return success_count + 2  # +2 pour les 2 premiers déjà copiés

if __name__ == "__main__":
    copied = copy_workflow_files()
    print(f"\n🎉 Total: {copied}/100 workflows dans TOP_100_PRIORITAIRES")