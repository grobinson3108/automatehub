# Rapport de Validation des Traductions n8n
==================================================

## Résumé Global
- Fichiers validés: 100
- Fichiers avec problèmes: 88
- Total des problèmes: 605

## Statistiques
- Workflows traduits: 100
- Nodes au total: 650
- Éléments traduits: 0
- Expressions n8n préservées: 1074
- Textes non traduits: 42

## Détails par Fichier

### Generation_Images_OpenAI_Formulaire.json
- Variable n8n malformée dans Node OpenAI Image Generation.bodyParameters.parameters[1].value: {{ $json.Prompt }}
- Variable n8n malformée dans Node OpenAI Image Generation.bodyParameters.parameters[3].value: {{ $json['Image size'] }}

### Requete_API_Transformation_Donnees.json
- Variable n8n malformée dans Node Clean Sortie.assignments.assignments[0].value: {{ $json.choices[0].message.content }}
- Variable n8n malformée dans Node Clean Sortie.assignments.assignments[1].value: {{ $json.citations }}
- Variable n8n malformée dans Node Perplexity Requête.jsonBody: {{ $json.system_prompt }}
- Variable n8n malformée dans Node Perplexity Requête.jsonBody: {{ $json.user_prompt }}
- Variable n8n malformée dans Node Perplexity Requête.jsonBody: {{ (JSON.stringify($json.domains.split(','))) }}

### Typeform_vers_Airtable_CFP.json
- Texte non traduit dans workflow name: 'CFP Selection 1'

### Suivi_Heures_Pauses_Notion.json
- Texte non traduit dans workflow name: 'Track Working Time and Pauses'
- Variable n8n malformée dans Node Créer new page.propertiesUi.propertyValues[0].date: {{ $now }}
- Variable n8n malformée dans Node Mettre à jour page with end date.pageId.value: {{ $json.id }}
- Variable n8n malformée dans Node Mettre à jour page with end date.propertiesUi.propertyValues[0].date: {{ $now }}
- Variable n8n malformée dans Node Si pause_in_minuten is empty.conditions.conditions[0].leftValue: {{ $json.property_break }}
- Variable n8n malformée dans Node Si page responded.conditions.conditions[0].leftValue: {{ $json }}
- Variable n8n malformée dans Node Si page exist.conditions.conditions[0].leftValue: {{ $json }}
- Variable n8n malformée dans Node Si page exist1.conditions.conditions[0].leftValue: {{ $json }}
- Variable n8n malformée dans Node Si.conditions.conditions[0].leftValue: {{ $json.property_end }}
- Variable n8n malformée dans Node Définir Break Duration.assignments.assignments[0].value: {{ $json.body.duration }}
- Variable n8n malformée dans Node Mettre à jour break duration for current day.pageId.value: {{ $json.id }}
- Variable n8n malformée dans Node Mettre à jour break duration for current day.propertiesUi.propertyValues[0].numberValue: {{ $('Set Break Duration').item.json.break_duration }}
- Variable n8n malformée dans Node Définir break duration for current day.pageId.value: {{ $json.id }}
- Variable n8n malformée dans Node Définir break duration for current day.propertiesUi.propertyValues[0].numberValue: {{ $('Set Break Duration').item.json.break_duration }}
- Variable n8n malformée dans Node Get notion page by date.filters.conditions[0].textValue: {{ $now.format('dd.MM.yyyy') }}
- Variable n8n malformée dans Node Définir Message - Break time tracked.assignments.assignments[0].value: {{ $('Set Break Duration').item.json.break_duration }}
- Variable n8n malformée dans Node Définir Message - Break time updated.assignments.assignments[0].value: {{ $('Set Break Duration').item.json.break_duration }}
- Variable n8n malformée dans Node Get notion page with todays date.filters.conditions[0].textValue: {{ $now.format('dd.MM.yyyy') }}
- Variable n8n malformée dans Node Aiguillage.rules.values[0].conditions.conditions[0].leftValue: {{ $json.body.method }}
- Variable n8n malformée dans Node Aiguillage.rules.values[1].conditions.conditions[0].leftValue: {{ $json.body.method }}
- Variable n8n malformée dans Node Aiguillage.rules.values[2].conditions.conditions[0].leftValue: {{ $json.body.method }}
- Variable n8n malformée dans Node Get notion page with todays date1.filters.conditions[0].textValue: {{ $now.format('dd.MM.yyyy') }}
- Variable n8n malformée dans Node Répondre au Webhook.responseBody: {{ $json.message }}

### Webhook_Slack_Email_V2.json
- Variable n8n malformée dans Node IF.conditions.number[0].value1: {{$node["Google Sheets"].data["Severity"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Email"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Name"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Severity"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Problem"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Email"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Name"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Severity"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Problem"]}}

### Formulaire_vers_API.json
- Variable n8n malformée dans Node Create Stripe Product.bodyParameters.parameters[0].value: {{ $json.title }}
- Variable n8n malformée dans Node Create Stripe Product.bodyParameters.parameters[1].value: {{ $json.price }}
- Variable n8n malformée dans Node Create Stripe Product.bodyParameters.parameters[2].value: {{ $json.currency }}
- Variable n8n malformée dans Node Créer payment link.bodyParameters.parameters[0].value: {{ $json.default_price }}
- Variable n8n malformée dans Node Config.assignments.assignments[1].value: {{ $json.price * 100}}
- Variable n8n malformée dans Node Répondre au Webhook.redirectURL: {{ $json.url }}

### Todoist_Traitement_Programme.json
- Variable n8n malformée dans Node Mettre à jour priority in todoist.taskId: {{ $('Get inbox tasks').item.json.id }}
- Variable n8n malformée dans Node Mettre à jour priority in todoist.updateFields.priority: {{ $('Your Projects').first().json.projects[$json.message.content] }}
- Variable n8n malformée dans Node Categorize.messages.values[1].content: {{ $('Your Projects').first().json.projects.keys().join('\n') }}
- Variable n8n malformée dans Node Categorize.messages.values[1].content: {{ $('Get inbox tasks').item.json.content }}
- Variable n8n malformée dans Node Si task is not a subtask.conditions.conditions[0].leftValue: {{ $json.parent_id }}
- Variable n8n malformée dans Node Si other or ai hallucinates.conditions.conditions[0].leftValue: {{ $('Your Projects').first().json.projects.keys() }}
- Variable n8n malformée dans Node Si other or ai hallucinates.conditions.conditions[0].rightValue: {{ $json.message.content }}

### Tags_Auto_Images_Drive_IA.json
- Texte non traduit dans workflow name: 'Automated Image Metadata Tagging (Community Node)'
- Variable n8n malformée dans Node Download Image Fichier.fileId.value: {{ $json.id }}
- Variable n8n malformée dans Node Write Metadata into Image.exifMetadata.metadataValues[0].value: {{$json.content}}
- Variable n8n malformée dans Node Write Metadata into Image.exifMetadata.metadataValues[1].value: {{$json.content}}
- Variable n8n malformée dans Node Mettre à jour Image Fichier.fileId.value: {{ $('Download Image File').item.json.id }}
- Variable n8n malformée dans Node Mettre à jour Image Fichier.newUpdatedFileName: {{ $('Download Image File').item.json.name }}

### Webhook_Google_Sheets_Gmail.json
- Texte non traduit dans workflow name: 'Add new incoming emails to a Google Sheets spreadsheet as a new row.'
- Variable n8n malformée dans Node Google Sheets.columns.value.body: {{ $json.snippet }}
- Variable n8n malformée dans Node Google Sheets.columns.value.Subject: {{ $json.Subject }}
- Variable n8n malformée dans Node Google Sheets.columns.value.Sender Email: {{ $json.From }}

### GitHub_Alertes_Slack.json
- Variable n8n malformée dans Node IF.conditions.string[0].value1: {{$node["Github Trigger"].data["body"]["action"]}}
- Variable n8n malformée dans Node Slack - Add.attachments[0].text: {{$node["Github Trigger"].data["body"]["repository"]["stargazers_count"]}}
- Variable n8n malformée dans Node Slack - Add.attachments[0].title: {{$node["Github Trigger"].data["body"]["sender"]["login"]}}
- Variable n8n malformée dans Node Slack - Add.attachments[0].image_url: {{$node["Github Trigger"].data["body"]["sender"]["avatar_url"]}}
- Variable n8n malformée dans Node Slack - Add.attachments[0].title_link: {{$node["Github Trigger"].data["body"]["sender"]["html_url"]}}
- Variable n8n malformée dans Node Slack - Remove.attachments[0].text: {{$node["Github Trigger"].data["body"]["repository"]["stargazers_count"]}}
- Variable n8n malformée dans Node Slack - Remove.attachments[0].title: {{$node["Github Trigger"].data["body"]["sender"]["login"]}}
- Variable n8n malformée dans Node Slack - Remove.attachments[0].image_url: {{$node["Github Trigger"].data["body"]["sender"]["avatar_url"]}}
- Variable n8n malformée dans Node Slack - Remove.attachments[0].title_link: {{$node["Github Trigger"].data["body"]["sender"]["html_url"]}}

### Classification_Emails_OpenAI.json
- Variable n8n malformée dans Node Classify email.inputText: {{ $('Email trigger').first().json.text }}
- Variable n8n malformée dans Node Classify email.inputText: {{ $('Extract data from attachment').first().json.text }}
- Variable n8n malformée dans Node Extract variables - email & attachment.text: {{ $('Email trigger').first().json.text }}
- Variable n8n malformée dans Node Extract variables - email & attachment.text: {{ $('Extract data from attachment').first().json.text }}

### Alerte_SMS_Bitcoin_9000_EUR.json
- Texte non traduit dans workflow name: 'Get the price of BTC in EUR and send an SMS when the price is larger than EUR 9000'
- Variable n8n malformée dans Node IF.conditions.number[0].value1: {{$node["CoinGecko"].json["bitcoin"]["eur"]}}
- Variable n8n malformée dans Node Twilio.message: {{$node["CoinGecko"].json["bitcoin"]["eur"]}}

### Versets_Bible_Auto.json
- Texte non traduit dans workflow name: 'getBible Query v1.0'
- Variable n8n malformée dans Node API Query to GetBible.url: {{ $json.version || 'v2' }}
- Variable n8n malformée dans Node API Query to GetBible.url: {{ $json.translation || 'kjv' }}
- Variable n8n malformée dans Node API Query to GetBible.url: {{ $json.references }}
- Variable n8n malformée dans Node Map API Respons to Result.assignments.assignments[0].value: {{ $json }}

### Texte_vers_Audio_OpenAI.json
- Variable n8n malformée dans Node OpenAI.input: {{ $json.body.text_to_convert }}

### Webhook_Slack_Email.json
- Variable n8n malformée dans Node IF.conditions.number[0].value1: {{$node["Google Sheets"].data["Severity"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Email"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Name"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Severity"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Problem"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Email"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Name"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Severity"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Problem"]}}

### HubSpot_Programmation_Cron.json
- Variable n8n malformée dans Node Obtenir new contacts.filterGroupsUi.filterGroupsValues[0].filtersUi.filterValues[0].value: {{$today.minus({day:1}).toMillis()}}
- Variable n8n malformée dans Node Obtenir new contacts.filterGroupsUi.filterGroupsValues[0].filtersUi.filterValues[1].value: {{$today.toMillis()}}
- Variable n8n malformée dans Node Créer member.email: {{ $json["properties"].email }}
- Variable n8n malformée dans Node Créer member.mergeFieldsUi.mergeFieldsValues[0].value: {{ $json["properties"].firstname }}
- Variable n8n malformée dans Node Créer member.mergeFieldsUi.mergeFieldsValues[1].value: {{ $json["properties"].lastname }}

### Monitoring_Kafka_SMS.json
- Texte non traduit dans workflow name: 'Receive messages from a topic and send an SMS'
- Variable n8n malformée dans Node IF.conditions.number[0].value1: {{$node["Kafka Trigger"].json["message"]["temp"]}}
- Variable n8n malformée dans Node Vonage.message: {{$node["Kafka Trigger"].json["message"]["temp"]}}

### Twitter_vers_RocketChat_Auto.json
- Texte non traduit dans workflow name: 'TwitterWorkflow'
- Variable n8n malformée dans Node Filtrer Tweet Données.values.string[0].value: {{$node["n8n.io mentions"].json["text"]}}
- Variable n8n malformée dans Node Filtrer Tweet Données.values.string[1].value: {{$node["n8n.io mentions"].json["id"]}}
- Variable n8n malformée dans Node Filtrer Tweet Données.values.string[2].value: {{$node["n8n.io mentions"].json["user"]["screen_name"]}}
- Variable n8n malformée dans Node Filtrer Tweet Données.values.string[2].value: {{$node["n8n.io mentions"].json["id_str"]}}
- Variable n8n malformée dans Node RocketChat.text: {{$node["Filter Tweet Data"].json["Tweet"]}}
- Variable n8n malformée dans Node RocketChat.text: {{$node["Only get new tweets"].json["Tweet URL"]}}

### Google_Drive_vers_WordPress.json
- Variable n8n malformée dans Node Adjust Fields.assignments.assignments[0].value: {{ $json.id }}
- Variable n8n malformée dans Node Adjust Fields.assignments.assignments[1].value: {{ $json.title.rendered }}
- Variable n8n malformée dans Node Adjust Fields.assignments.assignments[2].value: {{ $json.link }}
- Variable n8n malformée dans Node Adjust Fields.assignments.assignments[3].value: {{ $json.content.rendered }}

### Shopify_Commandes_CRM.json
- Variable n8n malformée dans Node Zoho.lastName: {{$json["customer_lastname"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.Email: {{$json["customer_email"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.Phone: {{$json["customer_phone"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.First_Name: {{$json["customer_firstname"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.Mailing_Address.address_fields.Mailing_Zip: {{$json["customer_zipcode"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.Mailing_Address.address_fields.Mailing_City: {{$json["customer_city"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.Mailing_Address.address_fields.Mailing_Street: {{$json["customer_street"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.Mailing_Address.address_fields.Mailing_Country: {{$json["customer_country"]}}
- Variable n8n malformée dans Node Trello.name: {{$node["order created"].json["order_number"]}}
- Variable n8n malformée dans Node Trello.additionalFields.urlSource: {{$node["order created"].json["order_status_url"]}}
- Variable n8n malformée dans Node Définir fields.values.number[0].value: {{$json["customer"]["default_address"]["phone"]}}
- Variable n8n malformée dans Node Définir fields.values.number[1].value: {{$json["shipping_address"]["zip"]}}
- Variable n8n malformée dans Node Définir fields.values.number[2].value: {{$json["current_total_price"]}}
- Variable n8n malformée dans Node Définir fields.values.string[0].value: {{$json["customer"]["first_name"]}}
- Variable n8n malformée dans Node Définir fields.values.string[1].value: {{$json["customer"]["last_name"]}}
- Variable n8n malformée dans Node Définir fields.values.string[2].value: {{$json["customer"]["email"]}}
- Variable n8n malformée dans Node Définir fields.values.string[3].value: {{$json["shipping_address"]["country"]}}
- Variable n8n malformée dans Node Définir fields.values.string[4].value: {{$json["shipping_address"]["address1"]}}
- Variable n8n malformée dans Node Définir fields.values.string[5].value: {{$json["shipping_address"]["city"]}}
- Variable n8n malformée dans Node Définir fields.values.string[6].value: {{$json["shipping_address"]["province"]}}
- Variable n8n malformée dans Node IF.conditions.number[0].value1: {{$json["order_value"]}}
- Variable n8n malformée dans Node Gmail - coupon.toList[0]: {{$node["Set fields"].json["customer_email"]}}
- Variable n8n malformée dans Node Gmail - coupon.message: {{$json["customer_firstname"]}}
- Variable n8n malformée dans Node Gmail - thankyou.toList[0]: {{$node["Set fields"].json["customer_email"]}}
- Variable n8n malformée dans Node Gmail - thankyou.message: {{$node["Set fields"].json["customer_firstname"]}}
- Variable n8n malformée dans Node Mailchimp.email: {{$node["Set fields"].json["customer_email"]}}
- Variable n8n malformée dans Node Harvest.additionalFields.currency: {{$node["order created"].json["currency"]}}
- Variable n8n malformée dans Node Harvest.additionalFields.issue_date: {{$node["order created"].json["processed_at"]}}
- Variable n8n malformée dans Node Harvest.additionalFields.purchase_order: {{$node["order created"].json["order_number"]}}

### Gmail_vers_Google_Sheets_Auto.json
- Texte non traduit dans workflow name: 'Add new incoming emails to a Google Sheets spreadsheet as a new row.'
- Variable n8n malformée dans Node Google Sheets.columns.value.body: {{ $json.snippet }}
- Variable n8n malformée dans Node Google Sheets.columns.value.Subject: {{ $json.Subject }}
- Variable n8n malformée dans Node Google Sheets.columns.value.Sender Email: {{ $json.From }}

### Strava_vers_Beeminder.json
- Texte non traduit dans workflow name: 'Add a datapoint to Beeminder when new activity is added to Strava'
- Variable n8n malformée dans Node Beeminder.additionalFields.comment: {{$json["object_data"]["name"]}}

### Traitement_Fonction_Simple.json
- Texte non traduit dans workflow name: 'Verify phone numbers'
- Variable n8n malformée dans Node Parser and Valider Phone.phone: {{$node["Create Phone Item"].json["phone"]}}
- Variable n8n malformée dans Node Phone is Valid?.conditions.string[0].value1: {{$node["Parse and Validate Phone"].json["message"]["valid"]+""}}

### SMS_Notification_MSG91.json
- Texte non traduit dans workflow name: 'Send an SMS using MSG91'

### Bot_Telegram_Recettes_Cocktails.json
- Texte non traduit dans workflow name: 'Send a cocktail recipe every day via a Telegram'
- Variable n8n malformée dans Node Telegram.file: {{$node["HTTP Request"].json["drinks"][0]["strDrinkThumb"]}}
- Variable n8n malformée dans Node Telegram.additionalFields.caption: {{$node["HTTP Request"].json["drinks"][0]["strInstructions"]}}

### Surveillance_Email_Alertes_Telegram.json
- Variable n8n malformée dans Node Telegram.text: {{ $node["Email Trigger (IMAP)"].json["from"]["value"]["0"]["address"] }}
- Variable n8n malformée dans Node Telegram.inlineKeyboard.rows[0].row.buttons[0].text: {{ $('Github Gist').item.json.files["email.html"].filename }}
- Variable n8n malformée dans Node Telegram.inlineKeyboard.rows[0].row.buttons[0].additionalFields.url: {{'http://emails.nskha.com/?iloven8n=nskha&id='+ $('Github Gist').item.json.id}}
- Variable n8n malformée dans Node Github Gist.jsonBody: {{ $json.date }}
- Variable n8n malformée dans Node Github Gist.jsonBody: {{ JSON.stringify($json.from.value[0].address).slice(1, -1) }}
- Variable n8n malformée dans Node Github Gist.jsonBody: {{ JSON.stringify($json.to.value[0].address).slice(1, -1) }}
- Variable n8n malformée dans Node Github Gist.jsonBody: {{ JSON.stringify($json.html).slice(1, -1) }}
- Variable n8n malformée dans Node Telegram ‌.messageId: {{ $('Telegram').item.json.result.message_id }}
- Variable n8n malformée dans Node Github Gist ‌.url: {{ $item("0").$node["Github Gist"].json["id"] }}

### Webhook_Requete_HTTP_Simple.json
- Texte non traduit dans workflow name: 'AccountCraft WhatsApp Automation - Infridet'
- Variable n8n malformée dans Node FluentCRM - Add Contact.bodyParametersJson: {{$json["email"]}}
- Variable n8n malformée dans Node FluentCRM - Add Contact.bodyParametersJson: {{$json["name"]}}
- Variable n8n malformée dans Node Envoyer Warmup Email.text: {{$json["name"]}}
- Variable n8n malformée dans Node Envoyer Warmup Email.toEmail: {{$json["email"]}}
- Variable n8n malformée dans Node Send WhatsApp via Whinta.bodyParametersJson: {{$json["phone"]}}
- Variable n8n malformée dans Node Send WhatsApp via Whinta.bodyParametersJson: {{$json["name"]}}
- Variable n8n malformée dans Node Mettre à jour CRM Tag to Customer.bodyParametersJson: {{$json["email"]}}

### Site_Web_vers_PDF_Drive.json
- Variable n8n malformée dans Node Take a screenshot of a website.urlInput: {{ $json.Url }}
- Variable n8n malformée dans Node Store Screenshots.name: {{ $json.Title }}

### Reponses_Email_IA_Approbation.json
- Texte non traduit dans workflow name: 'AI Email processing autoresponder with approval (Yes/No)'
- Variable n8n malformée dans Node Markdown.html: {{ $json.textHtml }}
- Variable n8n malformée dans Node Envoyer Email.html: {{ $('Write email').item.json.output }}
- Variable n8n malformée dans Node Envoyer Email.subject: {{ $('Email Trigger (IMAP)').item.json.subject }}
- Variable n8n malformée dans Node Envoyer Email.toEmail: {{ $('Email Trigger (IMAP)').item.json.from }}
- Variable n8n malformée dans Node Envoyer Email.fromEmail: {{ $('Email Trigger (IMAP)').item.json.to }}
- Variable n8n malformée dans Node Email Summarization Chain.options.binaryDataKey: {{ $json.data }}
- Variable n8n malformée dans Node Email Summarization Chain.options.summarizationMethodAndPrompts.values.prompt: {{ $json.data }}
- Variable n8n malformée dans Node Email Summarization Chain.options.summarizationMethodAndPrompts.values.combineMapPrompt: {{ $json.data }}
- Variable n8n malformée dans Node Write email.text: {{ $('Email Summarization Chain').item.json.response.text }}
- Variable n8n malformée dans Node Définir Email.assignments.assignments[0].value: {{ $json.response.text }}
- Variable n8n malformée dans Node Approve?.conditions.conditions[0].leftValue: {{ $json.data.approved }}
- Variable n8n malformée dans Node Envoyer Draft.message: {{ $('Email Trigger (IMAP)').item.json.textHtml }}
- Variable n8n malformée dans Node Envoyer Draft.message: {{ $json.output }}
- Variable n8n malformée dans Node Envoyer Draft.subject: {{ $('Email Trigger (IMAP)').item.json.subject }}

### Stripe_HubSpot_Slack.json
- Variable n8n malformée dans Node Mettre à jour Deal to Paid.dealId: {{$json["id"]}}
- Variable n8n malformée dans Node Find Deal based on PO Number.filterGroupsUi.filterGroupsValues[0].filtersUi.filterValues[0].value: {{$json["data"]["object"]["custom_fields"][0]["value"]}}
- Variable n8n malformée dans Node Si no PO Number.conditions.string[0].value1: {{$json["data"]["object"]["custom_fields"]}}
- Variable n8n malformée dans Node Si no deal found for PO.conditions.string[0].value1: {{$json["id"]}}
- Variable n8n malformée dans Node Envoyer invoice paid message.attachments[0].fields.item[0].value: {{$node["When Invoice Paid"].json["data"]["object"]["amount_paid"]/100}}
- Variable n8n malformée dans Node Envoyer invoice paid message.attachments[0].fields.item[1].value: {{$node["When Invoice Paid"].json["data"]["object"]["currency"]}}
- Variable n8n malformée dans Node Envoyer invoice paid message.attachments[0].fields.item[2].value: {{$node["When Invoice Paid"].json["data"]["object"]["customer_name"]}}
- Variable n8n malformée dans Node Envoyer invoice paid message.attachments[0].fields.item[3].value: {{$node["When Invoice Paid"].json["data"]["object"]["customer_email"]}}
- Variable n8n malformée dans Node Envoyer invoice paid message.attachments[0].fields.item[4].value: {{$node["When Invoice Paid"].json["data"]["object"]["custom_fields"][0]["value"]}}
- Variable n8n malformée dans Node Envoyer invoice paid message.attachments[0].footer: {{$node["When Invoice Paid"].json["id"]}}
- Variable n8n malformée dans Node Envoyer no PO Message.attachments[0].fields.item[0].value: {{$json["data"]["object"]["amount_paid"] / 100}}
- Variable n8n malformée dans Node Envoyer no PO Message.attachments[0].fields.item[1].value: {{$json["data"]["object"]["currency"]}}
- Variable n8n malformée dans Node Envoyer no PO Message.attachments[0].fields.item[2].value: {{$json["data"]["object"]["customer_name"]}}
- Variable n8n malformée dans Node Envoyer no PO Message.attachments[0].fields.item[3].value: {{$json["data"]["object"]["customer_email"]}}
- Variable n8n malformée dans Node Envoyer no PO Message.attachments[0].footer: {{$json["id"]}}
- Variable n8n malformée dans Node Envoyer Deal not found message.attachments[0].fields.item[0].value: {{$node["When Invoice Paid"].json["data"]["object"]["amount_paid"]/100}}
- Variable n8n malformée dans Node Envoyer Deal not found message.attachments[0].fields.item[1].value: {{$node["When Invoice Paid"].json["data"]["object"]["currency"]}}
- Variable n8n malformée dans Node Envoyer Deal not found message.attachments[0].fields.item[2].value: {{$node["When Invoice Paid"].json["data"]["object"]["customer_name"]}}
- Variable n8n malformée dans Node Envoyer Deal not found message.attachments[0].fields.item[3].value: {{$node["When Invoice Paid"].json["data"]["object"]["customer_email"]}}
- Variable n8n malformée dans Node Envoyer Deal not found message.attachments[0].fields.item[4].value: {{$node["When Invoice Paid"].json["data"]["object"]["custom_fields"][0]["value"]}}
- Variable n8n malformée dans Node Envoyer Deal not found message.attachments[0].footer: {{$node["When Invoice Paid"].json["id"]}}

### Sheets_vers_Google_Calendar.json
- Texte non traduit dans workflow name: 'Automate Event Creation in Google Calendar from Google Sheets'
- Variable n8n malformée dans Node Google Calendar Event Creator.end: {{ $json.startDate }}
- Variable n8n malformée dans Node Google Calendar Event Creator.start: {{ $json.startDate }}
- Variable n8n malformée dans Node Google Calendar Event Creator.additionalFields.summary: {{ $json.eventName }}
- Variable n8n malformée dans Node Google Calendar Event Creator.additionalFields.location: {{ $json.location }}
- Variable n8n malformée dans Node Google Calendar Event Creator.additionalFields.description: {{ $json.eventDescription }}

### Gmail_Brouillons_Reponse_IA.json
- Variable n8n malformée dans Node Si Needs Reply.conditions.conditions[0].leftValue: {{ $json.needsReply }}
- Variable n8n malformée dans Node Gmail - Create Draft.message: {{ $json.text.replace(/\n/g, "<br />\n") }}
- Variable n8n malformée dans Node Gmail - Create Draft.options.sendTo: {{ $('Gmail Trigger').item.json.headers.from }}
- Variable n8n malformée dans Node Gmail - Create Draft.options.threadId: {{ $('Gmail Trigger').item.json.threadId }}
- Variable n8n malformée dans Node Gmail - Create Draft.subject: {{ $('Gmail Trigger').item.json.headers.subject }}
- Variable n8n malformée dans Node Assess if message needs a reply.prompt: {{ $json.subject }}
- Variable n8n malformée dans Node Assess if message needs a reply.prompt: {{ $json.textAsHtml }}
- Variable n8n malformée dans Node Generate email reply.text: {{ $('Gmail Trigger').item.json.subject }}
- Variable n8n malformée dans Node Generate email reply.text: {{ $('Gmail Trigger').item.json.textAsHtml }}

### Meteo_Webhook_Declencheur.json
- Variable n8n malformée dans Node Définir.values.string[0].value: {{$json["main"]["temp"]}}
- Variable n8n malformée dans Node Définir.values.string[1].value: {{$json["main"]["humidity"]}}
- Variable n8n malformée dans Node Définir.values.string[2].value: {{$json["wind"]["speed"]}}
- Variable n8n malformée dans Node Définir.values.string[3].value: {{$json["weather"][0]["description"]}}
- Variable n8n malformée dans Node Définir.values.string[4].value: {{$json["name"]}}
- Variable n8n malformée dans Node Définir.values.string[4].value: {{$json["sys"]["country"]}}
- Variable n8n malformée dans Node OpenWeatherMap.cityName: {{$json["body"]["city"]}}

### Email_vers_Nextcloud_Deck.json
- Texte non traduit dans workflow name: 'Create Nextcloud Deck card from email'
- Variable n8n malformée dans Node HTTP Request.bodyParametersJson: {{$json["subject"]}}
- Variable n8n malformée dans Node HTTP Request.bodyParametersJson: {{$json["body"]}}

### Typeform_Multi_Notifications_V2.json
- Variable n8n malformée dans Node IF.conditions.number[0].value1: {{$node["Google Sheets"].data["Severity"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Email"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Name"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Severity"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Problem"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Email"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Name"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Severity"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Problem"]}}

### Traitement_Date_Heure.json
- Variable n8n malformée dans Node Note.content: {{$now}}
- Variable n8n malformée dans Node Note.content: {{$now.toLocaleString(DateTime.TIME_WITH_SECONDS)}}
- Variable n8n malformée dans Node Note.content: {{$today}}
- Variable n8n malformée dans Node Note.content: {{$today.plus({days: 1})}}
- Variable n8n malformée dans Node Note.content: {{$now.minus({hours: 1})}}
- Variable n8n malformée dans Node Note.content: {{$today.weekdayLong}}
- Variable n8n malformée dans Node 12 Hours from now.value: {{$now}}
- Variable n8n malformée dans Node Note4.content: {{DateTime.fromISO($json["Now"]).toFormat('yyyy LLL dd')}}
- Variable n8n malformée dans Node Définir times.values.string[0].value: {{$now}}
- Variable n8n malformée dans Node Définir times.values.string[1].value: {{$now.toLocaleString(DateTime.TIME_WITH_SECONDS)}}
- Variable n8n malformée dans Node Définir times.values.string[2].value: {{$today}}
- Variable n8n malformée dans Node Définir times.values.string[3].value: {{$today.plus({days: 1})}}
- Variable n8n malformée dans Node Définir times.values.string[4].value: {{$now.minus({hours: 1})}}
- Variable n8n malformée dans Node Définir times.values.string[5].value: {{$today.weekdayLong}}
- Variable n8n malformée dans Node Edit times.values.string[0].value: {{DateTime.fromISO($json["Now"])}}
- Variable n8n malformée dans Node Edit times.values.string[1].value: {{DateTime.fromISO($json["Now"]).toFormat('yyyy LLL dd')}}
- Variable n8n malformée dans Node Formater - MMMM DD YY.value: {{$now}}

### Gumroad_Notifications_Ventes.json
- Texte non traduit dans workflow name: 'Receive updates when a sale is made in Gumroad'

### TheHive_Notifications_Events.json
- Texte non traduit dans workflow name: 'Receive updates when an event occurs in TheHive'

### Bot_Telegram_Meteo.json
- Texte non traduit dans workflow name: 'Telegram Weather Workflow'
- Variable n8n malformée dans Node Telegram.text: {{$node["OpenWeatherMap"].json["weather"][0]["description"]}}
- Variable n8n malformée dans Node Telegram.text: {{$node["OpenWeatherMap"].json["main"]["temp"]}}
- Variable n8n malformée dans Node Telegram.text: {{$node["OpenWeatherMap"].json["main"]["feels_like"]}}
- Variable n8n malformée dans Node Telegram.chatId: {{$node["Telegram Trigger"].json["message"]["chat"]["id"]}}

### Typeform_vers_Sheets_Notifications.json
- Variable n8n malformée dans Node IF.conditions.number[0].value1: {{$node["Google Sheets"].data["Severity"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Email"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Name"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Severity"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Problem"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Email"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Name"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Severity"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Problem"]}}

### Stripe_Sync_Commandes.json
- Texte non traduit dans workflow name: 'Stripe Payment Order Sync – Auto Retrieve Customer & Product Purchased'
- Variable n8n malformée dans Node Extract Session Information.url: {{ $json.data.object.id }}
- Variable n8n malformée dans Node Filtrer Information.assignments.assignments[0].value: {{ $json.customer_details.name }}
- Variable n8n malformée dans Node Filtrer Information.assignments.assignments[1].value: {{ $json.customer_details.email }}
- Variable n8n malformée dans Node Filtrer Information.assignments.assignments[2].value: {{ $json.line_items.data[0].description }}

### Mailchimp_Gmail_Integration.json
- Variable n8n malformée dans Node Zoho.lastName: {{$json["customer_lastname"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.Email: {{$json["customer_email"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.Phone: {{$json["customer_phone"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.First_Name: {{$json["customer_firstname"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.Mailing_Address.address_fields.Mailing_Zip: {{$json["customer_zipcode"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.Mailing_Address.address_fields.Mailing_City: {{$json["customer_city"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.Mailing_Address.address_fields.Mailing_Street: {{$json["customer_street"]}}
- Variable n8n malformée dans Node Zoho.additionalFields.Mailing_Address.address_fields.Mailing_Country: {{$json["customer_country"]}}
- Variable n8n malformée dans Node Trello.name: {{$node["order created"].json["order_number"]}}
- Variable n8n malformée dans Node Trello.additionalFields.urlSource: {{$node["order created"].json["order_status_url"]}}
- Variable n8n malformée dans Node Définir fields.values.number[0].value: {{$json["customer"]["default_address"]["phone"]}}
- Variable n8n malformée dans Node Définir fields.values.number[1].value: {{$json["shipping_address"]["zip"]}}
- Variable n8n malformée dans Node Définir fields.values.number[2].value: {{$json["current_total_price"]}}
- Variable n8n malformée dans Node Définir fields.values.string[0].value: {{$json["customer"]["first_name"]}}
- Variable n8n malformée dans Node Définir fields.values.string[1].value: {{$json["customer"]["last_name"]}}
- Variable n8n malformée dans Node Définir fields.values.string[2].value: {{$json["customer"]["email"]}}
- Variable n8n malformée dans Node Définir fields.values.string[3].value: {{$json["shipping_address"]["country"]}}
- Variable n8n malformée dans Node Définir fields.values.string[4].value: {{$json["shipping_address"]["address1"]}}
- Variable n8n malformée dans Node Définir fields.values.string[5].value: {{$json["shipping_address"]["city"]}}
- Variable n8n malformée dans Node Définir fields.values.string[6].value: {{$json["shipping_address"]["province"]}}
- Variable n8n malformée dans Node IF.conditions.number[0].value1: {{$json["order_value"]}}
- Variable n8n malformée dans Node Gmail - coupon.toList[0]: {{$node["Set fields"].json["customer_email"]}}
- Variable n8n malformée dans Node Gmail - coupon.message: {{$json["customer_firstname"]}}
- Variable n8n malformée dans Node Gmail - thankyou.toList[0]: {{$node["Set fields"].json["customer_email"]}}
- Variable n8n malformée dans Node Gmail - thankyou.message: {{$node["Set fields"].json["customer_firstname"]}}
- Variable n8n malformée dans Node Mailchimp.email: {{$node["Set fields"].json["customer_email"]}}
- Variable n8n malformée dans Node Harvest.additionalFields.currency: {{$node["order created"].json["currency"]}}
- Variable n8n malformée dans Node Harvest.additionalFields.issue_date: {{$node["order created"].json["processed_at"]}}
- Variable n8n malformée dans Node Harvest.additionalFields.purchase_order: {{$node["order created"].json["order_number"]}}

### Bot_Telegram_Envoyer_Livres_Kindle.json
- Texte non traduit dans workflow name: 'send file to kindle through telegram bot'
- Variable n8n malformée dans Node check if there is a file in the message.conditions.conditions[0].leftValue: {{ $json.message.document }}
- Variable n8n malformée dans Node reply to warn that file is missing.chatId: {{ $('receive file message from telegram bot').item.json.message.chat.id }}
- Variable n8n malformée dans Node reply to warn that file is missing.additionalFields.reply_to_message_id: {{ $('receive file message from telegram bot').item.json.message.message_id }}
- Variable n8n malformée dans Node send email with the file as attchament to kindle.bodyContent: {{ $json.message.document.file_name }}
- Variable n8n malformée dans Node reply to telegram chat that the file has been sent successfully.chatId: {{ $('receive file message from telegram bot').item.json.message.chat.id }}
- Variable n8n malformée dans Node reply to telegram chat that the file has been sent successfully.additionalFields.reply_to_message_id: {{ $('receive file message from telegram bot').item.json.message.message_id }}

### Typeform_Multi_Notifications_V1.json
- Variable n8n malformée dans Node IF.conditions.number[0].value1: {{$node["Google Sheets"].data["Severity"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Email"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Name"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Severity"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Problem"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Email"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Name"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Severity"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Problem"]}}

### Agregation_Donnees_Clients.json
- Variable n8n malformée dans Node insert into variable.assignments.assignments[0].value: {{ $json }}
- Variable n8n malformée dans Node Respond to flutterflow.responseBody: {{ $json }}

### Gmail_Filtre_vers_Telegram.json
- Variable n8n malformée dans Node Email Validation Check.conditions.conditions[0].leftValue: {{ $json.Subject }}
- Variable n8n malformée dans Node Email Validation Check.conditions.conditions[1].leftValue: {{ $json.Subject }}
- Variable n8n malformée dans Node Send Telegram Message.text: {{ $json.From }}
- Variable n8n malformée dans Node Send Telegram Message.text: {{ $json.Subject }}
- Variable n8n malformée dans Node Send Telegram Message.text: {{ $json.snippet }}

### Import_Donnees_Google_Sheets.json
- Variable n8n malformée dans Node Définir.values.string[0].value: {{$json["results"][0]["name"]["first"]}}
- Variable n8n malformée dans Node Définir.values.string[0].value: {{$json["results"][0]["name"]["last"]}}
- Variable n8n malformée dans Node Définir.values.string[1].value: {{$json["results"][0]["location"]["country"]}}

### Gmail_Organisation_IA.json
- Texte non traduit dans workflow name: '(G) - Email Classification'
- Variable n8n malformée dans Node AI Agent.text: {{ $('Classification Agent').item.json.text }}
- Variable n8n malformée dans Node Gmail3.message: {{ $json.output }}
- Variable n8n malformée dans Node Gmail3.subject: {{ $('Gmail Trigger').item.json.headers.subject }}
- Variable n8n malformée dans Node Classification Agent.inputText: {{ $json.text || $json.html }}
- Variable n8n malformée dans Node Add Label Promotion.messageId: {{ $json.id }}
- Variable n8n malformée dans Node Add Label (KS Work Related).messageId: {{ $json.id }}
- Variable n8n malformée dans Node Add Label (High Priority).messageId: {{ $json.id }}

### Communication_HTTP_Programmee.json
- Variable n8n malformée dans Node Requête For Upwork Job Posts.bodyParameters.parameters[0].value: {{ $json.startUrls }}
- Variable n8n malformée dans Node Requête For Upwork Job Posts.bodyParameters.parameters[1].value: {{ $json.proxyCountryCode }}
- Variable n8n malformée dans Node Si Working Hours.conditions.conditions[0].leftValue: {{ $json.Hour }}
- Variable n8n malformée dans Node Si Working Hours.conditions.conditions[1].leftValue: {{ $json.Hour }}
- Variable n8n malformée dans Node Find Existing Entries.query: {{ $json.title }}
- Variable n8n malformée dans Node Find Existing Entries.query: {{ $json.budget }}
- Variable n8n malformée dans Node Envoyer message in #general.text: {{ $json.title }}
- Variable n8n malformée dans Node Envoyer message in #general.text: {{ $json.publishedDate }}
- Variable n8n malformée dans Node Envoyer message in #general.text: {{ $json.link }}
- Variable n8n malformée dans Node Envoyer message in #general.text: {{ $json.paymentType }}
- Variable n8n malformée dans Node Envoyer message in #general.text: {{ $json.budget }}
- Variable n8n malformée dans Node Envoyer message in #general.text: {{ $json.skills }}
- Variable n8n malformée dans Node Envoyer message in #general.text: {{ $json.shortBio }}

### Service_Resume_Email_IA.json
- Texte non traduit dans workflow name: 'Summarize emails with A.I. then send to messenger'
- Variable n8n malformée dans Node Envoyer email to A.I. to summarize.jsonBody: {{ encodeURIComponent($json.from) }}
- Variable n8n malformée dans Node Envoyer email to A.I. to summarize.jsonBody: {{ encodeURIComponent($json.subject) }}
- Variable n8n malformée dans Node Envoyer email to A.I. to summarize.jsonBody: {{ encodeURIComponent($json.textHtml) }}
- Variable n8n malformée dans Node Envoyer summarized content to messenger.jsonBody: {{ $json.choices[0].message.content.replace(/\n/g, "\\n") }}

### Extraction_Depenses_Email_Sheets.json
- Texte non traduit dans workflow name: 'Extract expenses from emails and add to Google Sheet'
- Variable n8n malformée dans Node Check subject.conditions.string[0].value1: {{$json["subject"].toLowerCase()}}
- Variable n8n malformée dans Node Check subject.conditions.string[0].value2: {{$json["subjectPatterns"].toLowerCase()}}
- Variable n8n malformée dans Node Définir column data.values.string[0].value: {{$json["date"]}}
- Variable n8n malformée dans Node Définir column data.values.string[1].value: {{$node["Check for new emails"].json["subject"].split("-")[1]}}
- Variable n8n malformée dans Node Définir column data.values.string[2].value: {{$json["category"]}}
- Variable n8n malformée dans Node Définir column data.values.string[3].value: {{$json["currency"]}}
- Variable n8n malformée dans Node Définir column data.values.string[4].value: {{$json["total"]}}

### Requete_HTTP_Programmee.json
- Variable n8n malformée dans Node Scrape website with Scrappey.bodyParameters.parameters[1].value: {{ $json.url }}

### Calcul_Centre_Coordonnees.json
- Texte non traduit dans workflow name: 'Calculate the Centroid of a Set of Vectors'
- Variable n8n malformée dans Node Extract & Parser Vectors.assignments.assignments[0].value: {{ $json.query.vectors }}

### Bot_Telegram_Kindle_Simple.json
- Texte non traduit dans workflow name: 'send file to kindle through telegram bot'
- Variable n8n malformée dans Node check if there is a file in the message.conditions.conditions[0].leftValue: {{ $json.message.document }}
- Variable n8n malformée dans Node reply to warn that file is missing.chatId: {{ $('receive file message from telegram bot').item.json.message.chat.id }}
- Variable n8n malformée dans Node reply to warn that file is missing.additionalFields.reply_to_message_id: {{ $('receive file message from telegram bot').item.json.message.message_id }}
- Variable n8n malformée dans Node send email with the file as attchament to kindle.bodyContent: {{ $json.message.document.file_name }}
- Variable n8n malformée dans Node reply to telegram chat that the file has been sent successfully.chatId: {{ $('receive file message from telegram bot').item.json.message.chat.id }}
- Variable n8n malformée dans Node reply to telegram chat that the file has been sent successfully.additionalFields.reply_to_message_id: {{ $('receive file message from telegram bot').item.json.message.message_id }}

### Bot_Telegram_Controle_Spotify.json
- Texte non traduit dans workflow name: 'Play with Spotify from Telegram'
- Variable n8n malformée dans Node OpenAI - Ask about a track.messages.values[0].content: {{ $json.message.text }}
- Variable n8n malformée dans Node Rechercher track.query: {{ $json.message.content }}
- Variable n8n malformée dans Node Add song.id: {{ $json.id }}
- Variable n8n malformée dans Node Si.conditions.conditions[0].leftValue: {{ $json?.id }}
- Variable n8n malformée dans Node Message parser.assignments.assignments[0].value: {{ $json.error }}
- Variable n8n malformée dans Node Return message to Telegram.text: {{ $('Message parser').item.json.message }}
- Variable n8n malformée dans Node Return message to Telegram.chatId: {{ $json.message.chat.id }}
- Variable n8n malformée dans Node Define Now Playing.jsonOutput: {{ $json.item.name }}
- Variable n8n malformée dans Node Define Now Playing.jsonOutput: {{ $json.item.artists[0].name }}
- Variable n8n malformée dans Node Define Now Playing.jsonOutput: {{ $json.item.album.name }}

### Teams_Message_Manuel.json
- Variable n8n malformée dans Node Microsoft Teams1.teamId: {{$node["Microsoft Teams"].parameter["teamId"]}}
- Variable n8n malformée dans Node Microsoft Teams1.channelId: {{$node["Microsoft Teams"].json["id"]}}
- Variable n8n malformée dans Node Microsoft Teams2.teamId: {{$node["Microsoft Teams"].parameter["teamId"]}}
- Variable n8n malformée dans Node Microsoft Teams2.channelId: {{$node["Microsoft Teams"].json["id"]}}

### Upload_Drive_Integration_API.json
- Variable n8n malformée dans Node Find Image ID in Docx.url: {{$json.documentId}}
- Variable n8n malformée dans Node Make file shareable publically (optional).fileId.value: {{ $json.documentId }}
- Variable n8n malformée dans Node Replace Image in Docx.url: {{$json.documentId}}
- Variable n8n malformée dans Node Replace Image in Docx.jsonBody: {{ $json.body.content[1].paragraph.elements[0].inlineObjectElement.inlineObjectId }}
- Variable n8n malformée dans Node Replace Image in Docx.jsonBody: {{ $('Image URL').item.json.url }}
- Variable n8n malformée dans Node Sticky Note46.content: {{ $json.body.content[1].paragraph.elements[0].inlineObjectElement.inlineObjectId }}
- Variable n8n malformée dans Node Download Fichier - Docx.fileId.value: {{ $json.documentId }}
- Variable n8n malformée dans Node Download File - PDF.fileId.value: {{ $json.documentId }}

### Gmail_vers_Todoist_Auto.json
- Texte non traduit dans workflow name: 'Email mailbox as Todoist tasks'
- Variable n8n malformée dans Node Mark As Lire.messageId: {{ $json.id }}
- Variable n8n malformée dans Node Star.messageId: {{ $json.id }}
- Variable n8n malformée dans Node Si Task Not Exist.conditions.conditions[0].leftValue: {{ $json.content }}
- Variable n8n malformée dans Node Si AI responded properly.conditions.conditions[0].leftValue: {{ $json.output.content }}
- Variable n8n malformée dans Node Si AI responded properly.conditions.conditions[1].leftValue: {{ $json.output.description }}
- Variable n8n malformée dans Node Créer Todoist Task.content: {{ $json.output.content }}
- Variable n8n malformée dans Node Créer Todoist Task.options.description: {{ $json.output.description }}
- Variable n8n malformée dans Node Créer Todoist Task.options.description: {{ $json.output.actions }}
- Variable n8n malformée dans Node Créer Todoist Task.options.description: {{ $json.output.answer }}
- Variable n8n malformée dans Node Obtenir Full Message.messageId: {{ $json.id }}
- Variable n8n malformée dans Node Summarize Message.text: {{ $json.subject }}
- Variable n8n malformée dans Node Summarize Message.text: {{ $json.html }}
- Variable n8n malformée dans Node Si Email Unstarred (Not Exist).conditions.conditions[0].leftValue: {{ $json.Subject }}
- Variable n8n malformée dans Node Close Task.taskId: {{ $json.id }}

### HTTP_Ecriture_Fichier.json
- Texte non traduit dans workflow name: 'Write a file to the host machine'

### Shopify_Tags_Onfleet.json
- Texte non traduit dans workflow name: 'Updating Shopify tags on Onfleet events'

### MySQL_Export_Fichier.json
- Variable n8n malformée dans Node Define file structure.values.string[0].value: {{ $json.productCode }}
- Variable n8n malformée dans Node Define file structure.values.string[1].value: {{ $json.productName }}
- Variable n8n malformée dans Node Define file structure.values.string[2].value: {{ $json.productLine }}
- Variable n8n malformée dans Node Define file structure.values.string[3].value: {{ $json.productScale }}
- Variable n8n malformée dans Node Define file structure.values.string[4].value: {{ $json.MSRP }}
- Variable n8n malformée dans Node Define file structure1.values.string[0].value: {{ $json.productName }}
- Variable n8n malformée dans Node Define file structure1.values.string[1].value: {{ $json.productLine }}
- Variable n8n malformée dans Node Define file structure1.values.string[2].value: {{ $json.productScale }}
- Variable n8n malformée dans Node Define file structure1.values.string[3].value: {{ $json.MSRP }}
- Variable n8n malformée dans Node Define file structure1.values.string[4].value: {{ $json.productCode }}
- Variable n8n malformée dans Node Define file structure1.values.string[5].value: {{ $json.productDescription }}
- Variable n8n malformée dans Node Write Binary Fichier.fileName: {{ $binary.data.fileName }}

### Webhook_Slack_Manuel.json
- Texte non traduit dans workflow name: '外送記帳'
- Variable n8n malformée dans Node Extract Price, Shop, Date, TIme.assignments.assignments[0].value: {{ $json["text"].match(/\$(\d+(\.\d{2})?)/)[1] }}
- Variable n8n malformée dans Node Extract Price, Shop, Date, TIme.assignments.assignments[1].value: {{ $json["text"].match(/以下是您在([\u4e00-\u9fa5a-zA-Z0-9\s]+)訂購/)[1] }}
- Variable n8n malformée dans Node Extract Price, Shop, Date, TIme.assignments.assignments[2].value: {{ $json["text"].match(/Date: (\d{4}年\d{1,2}月\d{1,2}日)/)[1].replace("年", ".").replace("月", ".").replace("日", "") }}
- Variable n8n malformée dans Node Send to Slack with Block.text: {{ $json.shop }}
- Variable n8n malformée dans Node Send to Slack with Block.text: {{ $json.price }}
- Variable n8n malformée dans Node Send to Slack with Block.text: {{ $json.date }}
- Variable n8n malformée dans Node Send to Slack with Block.text: {{ $json.price }}
- Variable n8n malformée dans Node Send to Slack with Block.text: {{ $json.shop }}
- Variable n8n malformée dans Node Send to Slack with Block.text: {{ $json.date }}
- Variable n8n malformée dans Node Send to Slack with Block.blocksUi: {{ $json.shop }}
- Variable n8n malformée dans Node Send to Slack with Block.blocksUi: {{ $json.price }}
- Variable n8n malformée dans Node Send to Slack with Block.blocksUi: {{ $json.date }}
- Variable n8n malformée dans Node Send to Slack with Block.blocksUi: {{ $json.price }}
- Variable n8n malformée dans Node Send to Slack with Block.blocksUi: {{ $json.shop }}
- Variable n8n malformée dans Node Send to Slack with Block.blocksUi: {{ $json.date }}
- Variable n8n malformée dans Node Send to Slack with Block.blocksUi: {{ $json.time }}

### Webhook_Slack_Email_V1.json
- Variable n8n malformée dans Node IF.conditions.number[0].value1: {{$node["Google Sheets"].data["Severity"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Email"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Name"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Severity"]}}
- Variable n8n malformée dans Node Envoyer Email.text: {{$node["IF"].data["Problem"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Email"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Name"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Severity"]}}
- Variable n8n malformée dans Node Slack.text: {{$node["IF"].data["Problem"]}}

### Webhook_vers_Google_Sheets.json
- Variable n8n malformée dans Node Répondre au Webhook.responseBody: {{ $json['First Name'] }}
- Variable n8n malformée dans Node Répondre au Webhook.responseBody: {{ $json['Last name'] }}
- Variable n8n malformée dans Node Répondre au Webhook.responseBody: {{ $json['E-Mail'] }}
- Variable n8n malformée dans Node Répondre au Webhook.responseBody: {{ $json['User Variable 1'] }}
- Variable n8n malformée dans Node Répondre au Webhook.responseBody: {{ $json['User Variable 2']}}
- Variable n8n malformée dans Node Obtenir user in DB by Phone Number.filtersUI.values[0].lookupValue: {{ $json.body.call_inbound.from_number }}

### Operations_Sheets_Gmail_Avance.json
- Variable n8n malformée dans Node Définir.values.string[0].value: {{$json["results"][0]["name"]["first"]}}
- Variable n8n malformée dans Node Définir.values.string[0].value: {{$json["results"][0]["name"]["last"]}}
- Variable n8n malformée dans Node Définir.values.string[1].value: {{$json["results"][0]["location"]["country"]}}

### Traitement_HTTP_Manuel.json
- Variable n8n malformée dans Node Clean Sortie.assignments.assignments[0].value: {{ $json.choices[0].message.content }}
- Variable n8n malformée dans Node Clean Sortie.assignments.assignments[1].value: {{ $json.citations }}
- Variable n8n malformée dans Node Perplexity Requête.jsonBody: {{ $json.system_prompt }}
- Variable n8n malformée dans Node Perplexity Requête.jsonBody: {{ $json.user_prompt }}
- Variable n8n malformée dans Node Perplexity Requête.jsonBody: {{ (JSON.stringify($json.domains.split(','))) }}

### Gmail_vers_Google_Tasks.json
- Texte non traduit dans workflow name: '📦 New Email ➔ Create Google Task'
- Variable n8n malformée dans Node Google Tasks.title: {{$json["subject"]}}
- Variable n8n malformée dans Node Google Tasks.additionalFields.notes: {{$json["snippet"]}}
- Variable n8n malformée dans Node Google Tasks.additionalFields.dueDate: {{ $now.plus(1, day).toLocaleString() }}

### Verif_Serveur_Rapport_Email.json
- Variable n8n malformée dans Node Envoyer Email.text: {{ $json.CPU.toNumber().round(2) }}
- Variable n8n malformée dans Node Envoyer Email.text: {{ $json.RAM.toNumber().round(2) }}
- Variable n8n malformée dans Node Envoyer Email.text: {{ $json.Disk.toNumber().round(2) }}
- Variable n8n malformée dans Node Check results against thresholds.conditions.number[0].value1: {{ $json.CPU }}
- Variable n8n malformée dans Node Check results against thresholds.conditions.number[1].value1: {{ $json.Disk }}
- Variable n8n malformée dans Node Check results against thresholds.conditions.number[2].value1: {{ $json.RAM }}

### Telegram_Liste_Reunions_Quotidienne.json
- Texte non traduit dans workflow name: 'Automatically Send Daily Meeting List to Telegram'
- Variable n8n malformée dans Node Obtenir meetings for today.options.timeMax: {{ $today.plus({ days: 1 }) }}
- Variable n8n malformée dans Node Obtenir meetings for today.options.timeMin: {{ $today }}
- Variable n8n malformée dans Node Définir.values.string[0].value: {{ $json.summary }}
- Variable n8n malformée dans Node Définir.values.string[1].value: {{ $json.start }}
- Variable n8n malformée dans Node Définir.values.string[2].value: {{ $json.attendees }}
- Variable n8n malformée dans Node Telegram.text: {{$json["message"]}}

### Gmail_Reponse_Auto_IA.json
- Variable n8n malformée dans Node Si Needs Reply.conditions.conditions[0].leftValue: {{ $json.needsReply }}
- Variable n8n malformée dans Node Gmail - Create Draft.message: {{ $json.text.replace(/\n/g, "<br />\n") }}
- Variable n8n malformée dans Node Gmail - Create Draft.options.sendTo: {{ $('Gmail Trigger').item.json.headers.from }}
- Variable n8n malformée dans Node Gmail - Create Draft.options.threadId: {{ $('Gmail Trigger').item.json.threadId }}
- Variable n8n malformée dans Node Gmail - Create Draft.subject: {{ $('Gmail Trigger').item.json.headers.subject }}
- Variable n8n malformée dans Node Assess if message needs a reply.prompt: {{ $json.subject }}
- Variable n8n malformée dans Node Assess if message needs a reply.prompt: {{ $json.textAsHtml }}
- Variable n8n malformée dans Node Generate email reply.text: {{ $('Gmail Trigger').item.json.subject }}
- Variable n8n malformée dans Node Generate email reply.text: {{ $('Gmail Trigger').item.json.textAsHtml }}

### Resume_Email_IA_Messenger.json
- Texte non traduit dans workflow name: 'Summarize emails with A.I. then send to messenger'
- Variable n8n malformée dans Node Envoyer email to A.I. to summarize.jsonBody: {{ encodeURIComponent($json.from) }}
- Variable n8n malformée dans Node Envoyer email to A.I. to summarize.jsonBody: {{ encodeURIComponent($json.subject) }}
- Variable n8n malformée dans Node Envoyer email to A.I. to summarize.jsonBody: {{ encodeURIComponent($json.textHtml) }}
- Variable n8n malformée dans Node Envoyer summarized content to messenger.jsonBody: {{ $json.choices[0].message.content.replace(/\n/g, "\\n") }}

### Notion_ToDo_vers_Slack.json
- Variable n8n malformée dans Node Si task assigned to Harshil?.conditions.string[0].value1: {{$json["to_do"]["text"][1]["mention"]["user"]["name"]}}
- Variable n8n malformée dans Node Si task assigned to Harshil?.conditions.boolean[0].value1: {{$json["to_do"]["checked"]}}
- Variable n8n malformée dans Node Envoyer a Direct Message.channel: {{$json["id"]}}
- Variable n8n malformée dans Node Envoyer a Direct Message.attachments[0].title: {{$node["If task assigned to Harshil?"].json["to_do"]["text"][0]["text"]["content"]}}

### YouTube_vers_Telegram.json
- Variable n8n malformée dans Node Définir.values.string[0].value: {{$node["GetVideosYT"].json["id"]["videoId"]}}
- Variable n8n malformée dans Node Définir.values.string[1].value: {{$node["GetVideosYT"].json["id"]["videoId"]}}
- Variable n8n malformée dans Node Définir.values.string[2].value: {{$node["GetVideosYT"].json["snippet"]["title"]}}
- Variable n8n malformée dans Node SendVideo.text: {{$node["Function"].json["title"]}}
- Variable n8n malformée dans Node SendVideo.text: {{$node["Function"].json["url"]}}

### Twitter_Google_Sheets_Auto.json
- Variable n8n malformée dans Node Generate Post with OpenAI.prompt: {{$node["Get Content Ideas"].json["Platform"]}}
- Variable n8n malformée dans Node Generate Post with OpenAI.prompt: {{$node["Get Content Ideas"].json["Idea"]}}
- Variable n8n malformée dans Node Check Platform.conditions.string[0].value1: {{$node["Get Content Ideas"].json["Platform"]}}
- Variable n8n malformée dans Node Post to Twitter.text: {{$node["Generate Post with OpenAI"].json["text"]}}
- Variable n8n malformée dans Node Update Google Sheet.values: {{$node["Generate Post with OpenAI"].json["text"]}}

### Pipedrive_Programmation_Cron.json
- Variable n8n malformée dans Node HubSpot2.email: {{$json["email"][0]["value"]}}
- Variable n8n malformée dans Node HubSpot2.additionalFields.firstName: {{$json["first_name"]}}

### Capture_Screenshots_Site_Web.json
- Texte non traduit dans workflow name: 'Capture Website Screenshots with Bright Data Web Unlocker and Save to Disk'
- Variable n8n malformée dans Node Write a file to disk.fileName: {{ "c:\\"+ $json.filename }}
- Variable n8n malformée dans Node Write a file to disk.dataPropertyName: {{ $json.filename }}
- Variable n8n malformée dans Node Capture a screenshot.options.response.response.outputPropertyName: {{ $json.filename }}
- Variable n8n malformée dans Node Capture a screenshot.bodyParameters.parameters[0].value: {{ $json.zone }}
- Variable n8n malformée dans Node Capture a screenshot.bodyParameters.parameters[1].value: {{ $json.url }}

### Shopify_vers_Zendesk.json
- Variable n8n malformée dans Node Keep only UserId and email.values.number[0].value: {{ $json["id"] }}
- Variable n8n malformée dans Node Keep only UserId and email.values.string[0].value: {{ $json["email"] }}
- Variable n8n malformée dans Node Keep only UserId and email.values.string[1].value: {{ $json["phone"]  }}
- Variable n8n malformée dans Node User exists in Zendesk.conditions.number[0].value1: {{ $json["ZendeskUserId"] }}
- Variable n8n malformée dans Node Contact data is modified.conditions.string[0].value1: {{ $json["phone"] }}
- Variable n8n malformée dans Node Contact data is modified.conditions.string[0].value2: {{ $json["ZendeskPhone"] }}
- Variable n8n malformée dans Node Créer contact in Zendesk.name: {{ $json["first_name"] }}
- Variable n8n malformée dans Node Créer contact in Zendesk.name: {{ $json["last_name"] }}
- Variable n8n malformée dans Node Créer contact in Zendesk.additionalFields.email: {{ $json["email"] }}
- Variable n8n malformée dans Node Créer contact in Zendesk.additionalFields.phone: {{ $json["phone"] ?? ' ' }}
- Variable n8n malformée dans Node Mettre à jour contact in Zendesk.id: {{ $json["ZendeskUserId"] }}
- Variable n8n malformée dans Node Mettre à jour contact in Zendesk.updateFields.phone: {{ $json["phone"] ?? 0}}
- Variable n8n malformée dans Node Rechercher contact by email adress.filters.query: {{ $json["email"] }}

### YouTube_Notifications_Telegram.json
- Variable n8n malformée dans Node Définir.values.string[0].value: {{$node["GetVideosYT"].json["id"]["videoId"]}}
- Variable n8n malformée dans Node Définir.values.string[1].value: {{$node["GetVideosYT"].json["id"]["videoId"]}}
- Variable n8n malformée dans Node Définir.values.string[2].value: {{$node["GetVideosYT"].json["snippet"]["title"]}}
- Variable n8n malformée dans Node SendVideo.text: {{$node["Function"].json["title"]}}
- Variable n8n malformée dans Node SendVideo.text: {{$node["Function"].json["url"]}}

### Suivi_Leads_Sheets_HubSpot.json
- Texte non traduit dans workflow name: 'N_01_Simple_Lead_Tracker_Automation_v4'
- Variable n8n malformée dans Node Slack.text: {{ $json['Name Surname'] }}
- Variable n8n malformée dans Node Slack.text: {{ $json['E-Mail'] }}
- Variable n8n malformée dans Node Slack.text: {{$json["Phone"]}}
- Variable n8n malformée dans Node Slack.text: {{ $json['  Interest Level  '] }}
- Variable n8n malformée dans Node Slack.text: {{ $json['  Lead Source  '] }}
- Variable n8n malformée dans Node Slack.text: {{ $json['Notes '] }}
- Variable n8n malformée dans Node Gmail.message: {{ $json['Name Surname'] }}
- Variable n8n malformée dans Node Gmail.message: {{ $json['E-Mail'] }}
- Variable n8n malformée dans Node Gmail.message: {{$json["Phone"]}}
- Variable n8n malformée dans Node Gmail.message: {{ $json['  Interest Level  '] }}
- Variable n8n malformée dans Node Gmail.message: {{ $json['  Lead Source  '] }}
- Variable n8n malformée dans Node Gmail.message: {{ $json['Notes '] }}
- Variable n8n malformée dans Node Gmail.subject: {{ $json['Name Surname'] }}
- Variable n8n malformée dans Node HubSpot.email: {{ $json['E-Mail'] }}
- Variable n8n malformée dans Node HubSpot.additionalFields.message: {{ $json['Notes '] }}
- Variable n8n malformée dans Node HubSpot.additionalFields.salutation: {{ $json['  Lead Source  '] }}
- Variable n8n malformée dans Node HubSpot.additionalFields.phoneNumber: {{ $json.Phone }}
- Variable n8n malformée dans Node HubSpot.additionalFields.relationshipStatus: {{ $json['  Interest Level  '] }}
- Variable n8n malformée dans Node Si.conditions.conditions[0].leftValue: {{ $json['Followed Up?'] }}
- Variable n8n malformée dans Node Si.conditions.conditions[1].leftValue: {{ $json['  Interest Level  '] }}
- Variable n8n malformée dans Node Gmail_Reminder.message: {{ $json['Name Surname'] }}
- Variable n8n malformée dans Node Gmail_Reminder.message: {{ $json['E-Mail'] }}
- Variable n8n malformée dans Node Gmail_Reminder.message: {{ $json['  Interest Level  '] }}

### Meteo_Quotidienne_Line_Messaging.json
- Texte non traduit dans workflow name: 'Send daily weather updates via a message in Line'
- Variable n8n malformée dans Node Line.message: {{$node["OpenWeatherMap"].json["main"]["temp"]}}

### Bot_Telegram_Echo_Simple.json
- Texte non traduit dans workflow name: 'Telegram echo-bot'
- Variable n8n malformée dans Node Send back the JSON content of the message.text: {{ JSON.stringify($json, null, 2) }}
- Variable n8n malformée dans Node Send back the JSON content of the message.chatId: {{ $json.message.from.id }}

### Facebook_Leads_Auto.json
- Variable n8n malformée dans Node Subscribe lead in KlickTipp.email: {{ $json.data.email }}
- Variable n8n malformée dans Node Subscribe lead in KlickTipp.fields.dataFields[2].fieldValue: {{ $json.data['hast_du_zusätzliche_kommentare_für_uns?'] }}
- Variable n8n malformée dans Node Subscribe lead in KlickTipp.fields.dataFields[3].fieldValue: {{ $json.data['welcher_kurs_interessiert_dich?'] }}
- Variable n8n malformée dans Node Subscribe lead in KlickTipp.fields.dataFields[4].fieldValue: {{ $json.data['was_ist_deine_bevorzugte_zahlungsweise?'] }}

### GitLab_Webhook_Conditionnel.json
- Variable n8n malformée dans Node HTTP Request.bodyParametersJson: {{JSON.stringify("Release " + $json.body.name)}}
- Variable n8n malformée dans Node HTTP Request.bodyParametersJson: {{JSON.stringify($json.body.description + '\n\n\\\n[More info](' + $json.body.url + ')')}}
- Variable n8n malformée dans Node IF.conditions.string[0].value1: {{$json.body.object_kind}}

### Netflix_Transfert_Emails_Multiple.json
- Variable n8n malformée dans Node Mailjet.html: {{ $json.html }}
- Variable n8n malformée dans Node Mailjet.text: {{ $json.text }}
- Variable n8n malformée dans Node Mailjet.subject: {{ $json.subject }}
- Variable n8n malformée dans Node Mailjet.toEmail: {{ $json.recipient }}

### SMS_Notification_Mocean.json
- Texte non traduit dans workflow name: 'Send an SMS using the Mocean node'

### Analyse_Sentiment_Twitter.json
- Texte non traduit dans workflow name: 'ETL pipeline'
- Variable n8n malformée dans Node Slack.text: {{$json["score"]}}
- Variable n8n malformée dans Node Slack.text: {{$json["magnitude"]}}
- Variable n8n malformée dans Node Slack.text: {{$json["text"]}}
- Variable n8n malformée dans Node IF.conditions.number[0].value1: {{$json["score"]}}
- Variable n8n malformée dans Node Google Cloud Natural Language.content: {{$node["MongoDB"].json["text"]}}
- Variable n8n malformée dans Node Définir.values.number[0].value: {{$json["documentSentiment"]["score"]}}
- Variable n8n malformée dans Node Définir.values.number[1].value: {{$json["documentSentiment"]["magnitude"]}}
- Variable n8n malformée dans Node Définir.values.string[0].value: {{$node["Twitter"].json["text"]}}

### Chatbot_IA_Webhook.json
- Variable n8n malformée dans Node Respond to Apple Shortcut.responseBody: {{ $json.output }}
- Variable n8n malformée dans Node AI Agent.text: {{ $json.body.input }}
- Variable n8n malformée dans Node AI Agent.options.systemMessage: {{ $now.format('dd LLL yy') }}
- Variable n8n malformée dans Node AI Agent.options.systemMessage: {{ $now.format('h:mm a') }}

### Notification_Email_Mindee.json
- Texte non traduit dans workflow name: 'New invoice email notification'
- Variable n8n malformée dans Node Si email body contains invoice.conditions.string[0].value1: {{$json["text"].toLowerCase()}}
- Variable n8n malformée dans Node Envoyer new invoice notification.attachments[0].fields.item[0].value: {{$node["If Amount > 1000"].json["predictions"][0]["total_incl"]["amount"]}}
- Variable n8n malformée dans Node Envoyer new invoice notification.attachments[0].fields.item[1].value: {{$node["Check for new emails"].json["from"]["value"][0]["address"]}}
- Variable n8n malformée dans Node Envoyer new invoice notification.attachments[0].fields.item[2].value: {{$node["Check for new emails"].json["subject"]}}
- Variable n8n malformée dans Node Envoyer new invoice notification.attachments[0].footer: {{$node["Check for new emails"].json["date"]}}
- Variable n8n malformée dans Node Si Amount > 1000.conditions.number[0].value1: {{$json["predictions"][0]["total_incl"]["amount"]}}
