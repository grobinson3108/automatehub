#!/bin/bash

# Script pour copier et renommer les TOP 100 workflows prioritaires en français

echo "🚀 Copie et traduction des TOP 100 workflows prioritaires"
echo "========================================================"

# Créer le dossier de destination s'il n'existe pas
mkdir -p /var/www/automatehub/TOP_100_PRIORITAIRES

# Définir les workflows prioritaires avec leurs traductions
declare -A WORKFLOWS=(
    # TOP 10 MEGA VIRAUX
    ["Log_New_Gmail_Messages_to_Google_Sheets_Automatically.json"]="Gmail_vers_Google_Sheets_Auto.json"
    ["Send_Kindle_Books_via_Telegram_Bot_Commands.json"]="Bot_Telegram_Envoyer_Livres_Kindle.json"
    ["Monitor_Emails_and_Send_Telegram_Alerts.json"]="Surveillance_Email_Alertes_Telegram.json"
    ["Classify_Incoming_Emails_Using_OpenAI_Language_Model.json"]="Classification_Emails_OpenAI.json"
    ["Forward_Filtered_Gmail_Messages_to_Telegram.json"]="Gmail_Filtre_vers_Telegram.json"
    ["AI-Powered_Email_Response_with_Approval_System.json"]="Reponses_Email_IA_Approbation.json"
    ["YouTube_Video_Updates_to_Telegram_Channel.json"]="YouTube_Notifications_Telegram.json"
    ["Create_Google_Task_from_New_Gmail_Message.json"]="Gmail_vers_Google_Tasks.json"
    ["Extract_Text_from_PDF_Files.json"]="Extraction_Texte_PDF.json"
    ["Capture_Webhook_Data_to_Google_Sheets.json"]="Webhook_vers_Google_Sheets.json"
    
    # TELEGRAM & BOTS (11-25)
    ["Telegram_echo-bot.json"]="Bot_Telegram_Echo_Simple.json"
    ["Telegram_Weather_Workflow.json"]="Bot_Telegram_Meteo.json"
    ["Control_Spotify_Playback_via_Telegram_Commands.json"]="Bot_Telegram_Controle_Spotify.json"
    ["Daily_Cocktail_Recipe_via_Telegram_Bot.json"]="Bot_Telegram_Recettes_Cocktails.json"
    ["Forward_Netflix_Emails_to_Multiple_Recipients_via_Gmail.json"]="Netflix_Transfert_Emails_Multiple.json"
    ["Email_to_Nextcloud_Deck_Card_Converter.json"]="Email_vers_Nextcloud_Deck.json"
    ["send_file_to_kindle_through_telegram_bot_8nodes.json"]="Bot_Telegram_Kindle_Simple.json"
    ["Automatically_Send_Daily_Meeting_List_to_Telegram_8nodes.json"]="Telegram_Liste_Reunions_Quotidienne.json"
    ["Daily_Weather_Updates_via_Line_Messaging.json"]="Meteo_Quotidienne_Line_Messaging.json"
    ["BTC_Price_Alert_SMS_When_EUR_Value_Exceeds_€9000.json"]="Alerte_SMS_Bitcoin_9000_EUR.json"
    
    # GOOGLE WORKSPACE (26-40)
    ["Typeform_to_Sheets_with_Slack_and_Email_Notifications.json"]="Typeform_vers_Sheets_Notifications.json"
    ["webhook_automation_googlesheets_gmail.json"]="Webhook_Google_Sheets_Gmail.json"
    ["Track_Work_Hours_with_Breaks_in_Notion.json"]="Suivi_Heures_Travail_Notion.json"
    ["Convert_Google_Drive_Files_to_WordPress_Posts.json"]="Google_Drive_vers_WordPress.json"
    ["Import_Spreadsheet_Data_to_Google_Sheets.json"]="Import_Donnees_Google_Sheets.json"
    ["Extract_Email_Expenses_and_Log_in_Google_Sheets.json"]="Extraction_Depenses_Email_Sheets.json"
    ["Convert_Gmail_Messages_to_Todoist_Tasks_Automatically.json"]="Gmail_vers_Todoist_Auto.json"
    ["Create_Calendar_Events_from_Spreadsheet_Data.json"]="Sheets_vers_Google_Calendar.json"
    ["file_ops_googlesheets_gmail_complex_14nodes.json"]="Operations_Sheets_Gmail_Avance.json"
    ["Google_Drive_File_Upload_and_API_Integration.json"]="Upload_Drive_Integration_API.json"
    
    # IA SIMPLE & UTILE (41-55)
    ["AI-Powered_Gmail_Auto-Reply_Draft_Creator.json"]="Gmail_Brouillons_Reponse_IA.json"
    ["AI_Email_Summary_to_Messenger_Delivery.json"]="Resume_Email_IA_Messenger.json"
    ["AI-Powered_Email_Summary_to_Messenger_Service.json"]="Service_Resume_Email_IA.json"
    ["Classify_and_Organize_Gmail_Emails_Using_AI.json"]="Gmail_Organisation_IA.json"
    ["Gmail_AI_auto-responder_create_draft_replies_to_incoming_emails_complex_12nodes.json"]="Gmail_Reponse_Auto_IA.json"
    ["AI-Powered_Content_Refinement_and_Analysis.json"]="Amelioration_Contenu_IA.json"
    ["AI-Powered_Chatbot_Responds_to_Web_Requests.json"]="Chatbot_IA_Webhook.json"
    ["Generate_Images_with_OpenAI_Using_Form_Input.json"]="Generation_Images_OpenAI_Formulaire.json"
    ["Convert_Text_to_Speech_Using_OpenAI.json"]="Texte_vers_Audio_OpenAI.json"
    ["Auto-Tag_Google_Drive_Images_with_AI-Generated_Metadata.json"]="Tags_Auto_Images_Drive_IA.json"
    
    # FORMULAIRES & LEADS (56-70)
    ["Typeform_to_Sheets_with_Slack_and_Email_Notifications_1.json"]="Typeform_Multi_Notifications_V1.json"
    ["Typeform_to_Sheets_with_Slack_and_Email_Notifications_2.json"]="Typeform_Multi_Notifications_V2.json"
    ["webhook_communicate_slack_emailsend.json"]="Webhook_Slack_Email.json"
    ["webhook_communicate_slack_emailsend_1.json"]="Webhook_Slack_Email_V1.json"
    ["webhook_communicate_slack_emailsend_2.json"]="Webhook_Slack_Email_V2.json"
    ["Form_Submission_to_API_Request_and_Response.json"]="Formulaire_vers_API.json"
    ["Capture_Website_Screenshots_and_Save_Locally.json"]="Capture_Screenshots_Site_Web.json"
    ["Lead_Tracking_Google_Sheets_to_HubSpot_with_Email_Alerts.json"]="Suivi_Leads_Sheets_HubSpot.json"
    ["Contact_Form_Text_Classifier_for_eCommerce_complex_14nodes.json"]="Classification_Formulaire_Contact.json"
    ["webhook_communicate_slack_manual_6nodes.json"]="Webhook_Slack_Manuel.json"
    
    # SOCIAL MEDIA & CONTENU (71-85)
    ["Auto-Post_Tweets_to_Rocket.Chat_Channel.json"]="Twitter_vers_RocketChat_Auto.json"
    ["process_youtube_telegram.json"]="YouTube_vers_Telegram.json"
    ["scheduled_process_youtube_7nodes.json"]="YouTube_Traitement_Programme.json"
    ["Complete_Youtube_complex_15nodes.json"]="YouTube_Workflow_Complet.json"
    ["webhook_process_twitter_spreadsheetfile_8nodes.json"]="Twitter_vers_Spreadsheet.json"
    ["Twitter_Sentiment_Analysis_and_Database_Storage.json"]="Analyse_Sentiment_Twitter.json"
    ["Analyze_YouTube_Comments_and_Log_Sentiment_in_Sheets.json"]="YouTube_Analyse_Commentaires.json"
    ["automation_twitter_googlesheets.json"]="Twitter_Google_Sheets_Auto.json"
    ["YouTube_to_Telegram_Notifications.json"]="YouTube_Alertes_Telegram.json"
    ["Instagram_Content_from_Trending_Topics.json"]="Instagram_Contenu_Tendances.json"
    
    # PRODUCTIVITÉ PERSONNELLE (86-100)
    ["Daily_Calendar_Summary_and_Event_Reminders.json"]="Resume_Calendrier_Quotidien.json"
    ["Check_To_Do_on_Notion_and_send_message_on_Slack_6nodes.json"]="Notion_ToDo_vers_Slack.json"
    ["Add_a_datapoint_to_Beeminder_when_new_activity_is_added_to_Strava.json"]="Strava_vers_Beeminder.json"
    ["Track_and_Log_Work_Hours_with_Breaks_in_Notion.json"]="Suivi_Heures_Pauses_Notion.json"
    ["scheduled_process_todoist_complex_12nodes.json"]="Todoist_Traitement_Programme.json"
    ["Email_Processing_and_Spreadsheet_Updates.json"]="Traitement_Email_MAJ_Sheets.json"
    ["Scheduled_Server_Health_Check_and_Email_Report.json"]="Verif_Serveur_Rapport_Email.json"
    ["Weather_Updates_via_Webhook_Trigger.json"]="Meteo_Webhook_Declencheur.json"
    ["Manual_API_Request_with_Data_Transformation.json"]="Requete_API_Transformation_Donnees.json"
    ["Empty_Workflow_Ready_for_Configuration.json"]="Workflow_Vide_Pret_Configurer.json"
)

# Compteur
count=0
success=0
failed=0

echo "📂 Copie de ${#WORKFLOWS[@]} workflows prioritaires..."
echo ""

# Parcourir et copier les workflows
for original in "${!WORKFLOWS[@]}"; do
    translated="${WORKFLOWS[$original]}"
    ((count++))
    
    # Chercher le fichier dans Freemium_Workflows
    if [ -f "/var/www/automatehub/Freemium_Workflows/$original" ]; then
        cp "/var/www/automatehub/Freemium_Workflows/$original" "/var/www/automatehub/TOP_100_PRIORITAIRES/$translated"
        echo "✅ [$count/100] $original → $translated"
        ((success++))
    else
        echo "❌ [$count/100] $original (non trouvé)"
        ((failed++))
    fi
done

echo ""
echo "📊 Résumé de l'opération :"
echo "=========================="
echo "✅ Workflows copiés avec succès : $success"
echo "❌ Workflows non trouvés : $failed"
echo "📁 Total dans TOP_100_PRIORITAIRES : $(ls /var/www/automatehub/TOP_100_PRIORITAIRES/*.json 2>/dev/null | wc -l)"

# Créer un fichier de mapping pour référence
echo "# Mapping des workflows TOP 100" > /var/www/automatehub/TOP_100_PRIORITAIRES/MAPPING.md
echo "## Original → Français" >> /var/www/automatehub/TOP_100_PRIORITAIRES/MAPPING.md
echo "" >> /var/www/automatehub/TOP_100_PRIORITAIRES/MAPPING.md

for original in "${!WORKFLOWS[@]}"; do
    translated="${WORKFLOWS[$original]}"
    echo "- $original → **$translated**" >> /var/www/automatehub/TOP_100_PRIORITAIRES/MAPPING.md
done

echo ""
echo "📝 Fichier de mapping créé : TOP_100_PRIORITAIRES/MAPPING.md"
echo "✅ Opération terminée !"