#!/bin/bash

echo "🔍 Recherche et ajout de workflows supplémentaires pour atteindre 100"
echo "===================================================================="

# Compter les workflows existants
existing=$(ls /var/www/automatehub/TOP_100_PRIORITAIRES/*.json 2>/dev/null | wc -l)
needed=$((100 - existing))

echo "📊 Workflows actuels : $existing"
echo "📊 Workflows manquants : $needed"
echo ""

# Workflows supplémentaires à fort potentiel viral
declare -A EXTRA_WORKFLOWS=(
    # WORKFLOWS SIMPLES ET VIRAUX
    ["automation.json"]="Automatisation_Simple.json"
    ["communicate_emailreadimap.json"]="Lecture_Emails_IMAP.json"
    ["notify_emailreadimap_mindee_6nodes.json"]="Notification_Email_Mindee.json"
    ["webhook_communicate_httprequest_6nodes.json"]="Webhook_Requete_HTTP_Simple.json"
    ["file_ops_httprequest_manual_7nodes.json"]="Operations_Fichiers_HTTP.json"
    ["file_ops_readpdf_manual.json"]="Lecture_PDF_Manuel.json"
    ["file_ops_converttofile_manual_7nodes.json"]="Conversion_Fichiers_Manuel.json"
    ["file_ops_writebinaryfile.json"]="Ecriture_Fichier_Binaire.json"
    ["database_postgres_spreadsheetfile.json"]="PostgreSQL_vers_Spreadsheet.json"
    ["database_mysql_writebinaryfile_complex_13nodes.json"]="MySQL_Export_Fichier.json"
    ["process_httprequest_manual_6nodes_3.json"]="Traitement_HTTP_Manuel.json"
    ["process_manual_4.json"]="Processus_Manuel_Simple.json"
    ["scheduled_process_httprequest_2.json"]="Requete_HTTP_Programmee.json"
    ["webhook_process_respondtowebhook_1.json"]="Reponse_Webhook_Simple.json"
    ["webhook_automation_facebookleadads.json"]="Facebook_Leads_Auto.json"
    
    # INTÉGRATIONS POPULAIRES
    ["scheduled_automation_hubspot_pipedrive_7nodes.json"]="HubSpot_Pipedrive_Sync.json"
    ["scheduled_automation_hubspot_cron.json"]="HubSpot_Programmation_Cron.json"
    ["scheduled_automation_pipedrive_cron.json"]="Pipedrive_Programmation_Cron.json"
    ["webhook_process_mailchimp_gmail_9nodes.json"]="Mailchimp_Gmail_Integration.json"
    ["process_uproc_functionitem_2.json"]="Traitement_Fonction_Simple.json"
    ["automation_manual_box.json"]="Box_Integration_Manuel.json"
    ["process_datetime_manual_9nodes.json"]="Traitement_Date_Heure.json"
    ["scheduled_communicate_httprequest_9nodes.json"]="Communication_HTTP_Programmee.json"
    ["file_ops_httprequest_writebinaryfile.json"]="HTTP_Ecriture_Fichier.json"
    ["Kafka_Topic_Monitoring_with_SMS_Alerts.json"]="Monitoring_Kafka_SMS.json"
    ["Update_HubSpot_and_Notify_Slack_on_Stripe_Invoice_Payment.json"]="Stripe_HubSpot_Slack.json"
    ["GitHub_Issue_Alerts_to_Slack_Based_on_Conditions.json"]="GitHub_Alertes_Slack.json"
    ["GitLab_Event_Triggers_Conditional_HTTP_Request.json"]="GitLab_Webhook_Conditionnel.json"
    ["GitHub_to_Travis_CI_Build_Trigger.json"]="GitHub_Travis_CI_Build.json"
    
    # PRODUCTIVITÉ ET OUTILS
    ["Shopify_Order_Processing_with_CRM_and_Task_Updates.json"]="Shopify_Commandes_CRM.json"
    ["Shopify_Order_to_Zendesk_Ticket_Creation.json"]="Shopify_vers_Zendesk.json"
    ["Update_Shopify_Tags_When_Onfleet_Events_Occur.json"]="Shopify_Tags_Onfleet.json"
    ["Stripe_Payment_Order_Sync_–_Auto_Retrieve_Customer_&_Product_Purchased.json"]="Stripe_Sync_Commandes.json"
    ["Create,_update,_and_get_a_post_in_Ghost.json"]="Ghost_Blog_CRUD.json"
    ["Manually_Trigger_Microsoft_Teams_Message.json"]="Teams_Message_Manuel.json"
    ["NextCloud_File_Upload_and_HTTP_Request_Automation.json"]="NextCloud_Upload_Auto.json"
    ["Website_to_PDF_Google_Sheets_Trigger_to_Drive_Upload.json"]="Site_Web_vers_PDF_Drive.json"
    ["Send_SMS_Notification_Using_Mocean_Service.json"]="SMS_Notification_Mocean.json"
    ["Send_SMS_Notification_Using_MSG91_Service.json"]="SMS_Notification_MSG91.json"
    
    # AUTOMATISATIONS AVANCÉES MAIS ACCESSIBLES
    ["Receive_updates_when_a_sale_is_made_in_Gumroad.json"]="Gumroad_Notifications_Ventes.json"
    ["Receive_updates_when_an_event_occurs_in_TheHive.json"]="TheHive_Notifications_Events.json"
    ["Receive_a_Mattermost_message_when_new_data_gets_added_to_Airtable.json"]="Airtable_vers_Mattermost.json"
    ["Manually_Trigger_WordPress_Post_Creation.json"]="WordPress_Post_Manuel.json"
    ["Aggregate_Customer_Data_and_Generate_Response_Report.json"]="Agregation_Donnees_Clients.json"
    ["Bible_Verse_Retrieval_and_Processing.json"]="Versets_Bible_Auto.json"
    ["Calculate_Center_Point_of_Multiple_Coordinates.json"]="Calcul_Centre_Coordonnees.json"
    ["Capture_Typeform_Responses_to_Airtable_for_CFP_Selection.json"]="Typeform_vers_Airtable_CFP.json"
)

# Compteur pour les nouveaux ajouts
count=$existing
added=0

echo "📂 Ajout de workflows supplémentaires..."
echo ""

# Chercher d'abord dans Freemium_Workflows
for original in "${!EXTRA_WORKFLOWS[@]}"; do
    if [ $added -ge $needed ]; then
        break
    fi
    
    translated="${EXTRA_WORKFLOWS[$original]}"
    
    # Vérifier si le workflow existe déjà
    if [ -f "/var/www/automatehub/TOP_100_PRIORITAIRES/$translated" ]; then
        continue
    fi
    
    # Chercher dans Freemium_Workflows
    if [ -f "/var/www/automatehub/Freemium_Workflows/$original" ]; then
        cp "/var/www/automatehub/Freemium_Workflows/$original" "/var/www/automatehub/TOP_100_PRIORITAIRES/$translated"
        ((count++))
        ((added++))
        echo "✅ [$count/100] $original → $translated"
    else
        # Chercher dans tout le dossier source
        found=$(find /var/www/automatehub/200_automations_n8n -name "$original" -type f 2>/dev/null | head -1)
        if [ -n "$found" ]; then
            cp "$found" "/var/www/automatehub/TOP_100_PRIORITAIRES/$translated"
            ((count++))
            ((added++))
            echo "✅ [$count/100] $original → $translated (trouvé dans $(dirname $found))"
        fi
    fi
done

# Si on n'a toujours pas 100, chercher des workflows simples supplémentaires
if [ $count -lt 100 ]; then
    echo ""
    echo "🔍 Recherche de workflows simples supplémentaires..."
    
    # Chercher des workflows simples (petite taille)
    find /var/www/automatehub/Freemium_Workflows -name "*.json" -size -10k -type f 2>/dev/null | sort | while read -r file; do
        if [ $count -ge 100 ]; then
            break
        fi
        
        filename=$(basename "$file")
        # Éviter les doublons
        if ! ls /var/www/automatehub/TOP_100_PRIORITAIRES/*.json 2>/dev/null | grep -q "$filename"; then
            # Traduire le nom
            translated=$(echo "$filename" | sed \
                -e 's/webhook/Webhook/g' \
                -e 's/process/Processus/g' \
                -e 's/automation/Automatisation/g' \
                -e 's/scheduled/Programme/g' \
                -e 's/manual/Manuel/g' \
                -e 's/gmail/Gmail/g' \
                -e 's/telegram/Telegram/g' \
                -e 's/slack/Slack/g' \
                -e 's/sheets/Sheets/g' \
                -e 's/email/Email/g' \
                -e 's/send/Envoyer/g' \
                -e 's/notify/Notifier/g' \
                -e 's/_/ /g' \
                -e 's/ /_/g' \
                -e 's/\.json$/\.json/')
            
            cp "$file" "/var/www/automatehub/TOP_100_PRIORITAIRES/Auto_$count_$translated"
            ((count++))
            echo "✅ [$count/100] $filename → Auto_${count}_$translated"
        fi
    done
fi

echo ""
echo "📊 Résumé final :"
echo "================"
echo "✅ Total de workflows dans TOP_100_PRIORITAIRES : $(ls /var/www/automatehub/TOP_100_PRIORITAIRES/*.json 2>/dev/null | wc -l)"
echo "📄 Workflows ajoutés : $added"

# Mettre à jour le fichier de mapping
echo "" >> /var/www/automatehub/TOP_100_PRIORITAIRES/MAPPING.md
echo "## Ajouts supplémentaires" >> /var/www/automatehub/TOP_100_PRIORITAIRES/MAPPING.md
echo "" >> /var/www/automatehub/TOP_100_PRIORITAIRES/MAPPING.md

for original in "${!EXTRA_WORKFLOWS[@]}"; do
    translated="${EXTRA_WORKFLOWS[$original]}"
    if [ -f "/var/www/automatehub/TOP_100_PRIORITAIRES/$translated" ]; then
        echo "- $original → **$translated**" >> /var/www/automatehub/TOP_100_PRIORITAIRES/MAPPING.md
    fi
done

echo ""
echo "✅ Opération terminée !"