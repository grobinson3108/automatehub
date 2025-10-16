# Analyse des Workflows n8n
==================================================

## Statistiques Générales
- Termes uniques identifiés: 1100
- Noms de nodes différents: 342
- Workflows analysés: 51
- Expressions n8n trouvées: 300

## Top 20 des Noms de Nodes
- Sticky Note: 38 fois
- Sticky Note1: 31 fois
- Sticky Note2: 18 fois
- Sticky Note3: 13 fois
- IF: 13 fois
- On clicking 'execute': 12 fois
- When clicking ‘Test workflow’: 8 fois
- Google Sheets: 8 fois
- Send Email: 8 fois
- Slack: 8 fois
- Typeform Trigger: 7 fois
- Gmail Trigger: 7 fois
- Cron: 7 fois
- Set: 7 fois
- HTTP Request: 6 fois
- OpenAI Chat Model: 6 fois
- Sticky Note4: 5 fois
- Schedule Trigger: 5 fois
- NoOp: 5 fois
- Write Binary File: 4 fois

## Exemples d'Expressions n8n (à préserver)
- ## 2. Advanced way: Using Expressions
In this `Set` node, we set dates using [Luxon expressions](https://docs.n8n.io/code-examples/expressions/luxon/) for the following formats:

Now - `{{$now}}`
Current time with seconds - `{{$now.toLocaleString(DateTime.TIME_WITH_SECONDS)}}`
Today - `{{$today}}`
Tomorrow - `{{$today.plus({days: 1})}}`
One hour ago - `{{$now.minus({hours: 1})}}`
Weekday name - `{{$today.weekdayLong}}`


- ## Handle Retell's Inbound call webhooks

## Overview
- This workflow provides Retell agent builders with a simple way to populate [dynamic variables](https://docs.retellai.com/build/dynamic-variables) using n8n.
- The workflow fetches user information from a Google Sheet based on the phone number and sends it back to Retell.
- It is based on Retell's [Inbound Webhook Call](https://docs.retellai.com/features/inbound-call-webhook).
- Retell is a service that lets you create Voice Agents that handle voice calls simply, based on a prompt or using a conversational flow builder.

## Prerequisites
- Have a [Retell AI Account](https://www.retellai.com/)
- [Create a Retell agent](https://docs.retellai.com/get-started/quick-start)
- [Purchase a phone number](https://docs.retellai.com/deploy/purchase-number) and associate it with your agent
- Create a Google Sheets - for example, [make a copy of this one](https://docs.google.com/spreadsheets/d/1TYgk8PK5w2l8Q5NtepdyLvgtuHXBHcODy-2hXOPP6AU/edit?usp=sharing).
- Your Google Sheet must have at least one column with the phone number. The remaining columns will be used to populate your Retell agent’s dynamic variables.
- All fields are returned as strings to Retell (variables are replaced as text)

## How it works
- The webhook call is received from Retell. We filter the call using their whitelisted IP address.
- It extracts data from the webhook call and uses it to retrieve the user from Google Sheets.
- It formats the data in the response to match Retell's expected format.
- Retell uses this data to replace [dynamic variables](https://docs.retellai.com/build/dynamic-variables#dynamic-variables) in the prompts.


## How to use it
See the description for screenshots!
- Set the webhook name (keep it as POST).
- Copy the Webhook URL (e.g., `https://your-instance.app.n8n.cloud/webhook/retell-dynamic-variables`) and paste it into Retell's interface. Navigate to "Phone Numbers", click on the phone number, and enable "Add an inbound webhook".
- In your prompt (e.g., "welcome message"), use the variable with this syntax: `{{variable_name}}` (see [Retell's documentation](https://docs.retellai.com/build/dynamic-variables)).
- These variables will be dynamically replaced by the data in your Google Sheet.


## Notes
- In Google Sheets, the phone number must start with `'+'`.
- Phone numbers must be formatted like the example: with the `+`, extension, and no spaces.
- You can use any database—just replace Google Sheets with your own, making sure to keep the phone number formatting consistent.

- ### 2.1 Working with an existing time string
As items pass between nodes, n8n saves dates as ISO strings. This means that in order to work with the data as a date again, we need to convert it back using `DateTime.fromISO('yyyy-mm-dd')`
. Once doing that, we are able to apply date and time function again such as : `{{DateTime.fromISO($json["Now"]).toFormat('yyyy LLL dd')}}`
- // Loop over input items and add a new field called 'myNewField' to the JSON of each one
for (const item of $input.all()) {
  item.binary.data.fileName = item.json.message.document.file_name;
}

return $input.all();
- // Loop over input items and process the 'references' field
for (let item of $input.all()) {
  // Check if 'references' exists and is an array
  if (Array.isArray(item.json.references)) {
    item.json.references = item.json.references.join('; ');
  } else {
    // Handle cases where 'references' is missing or not an array
    item.json.references = 'John 3:16';
  }
}

// Return the modified items
return $input.all();
- =
- =## Task
You are a helpful assistant. Provide concise replies as the user receives them via voice on their mobile phone. Avoid using symbols like "\n" to prevent them from being narrated.

## Context
- Today is {{ $now.format('dd LLL yy') }}.
- Current time: {{ $now.format('h:mm a') }} in Berlin, Germany.
- When asked, you are an AI Agent running as an n8n workflow.

## Output
Keep responses short and clear, optimized for voice delivery. Don't hallucinate, if you don't know the answer, say you don't know. 
- =#OnThisDay
- =*
- =*Date:* {{$node["Check for new emails"].json["date"]}}

## Noms de Workflows
- (G) - Email Classification
- AI Email processing autoresponder with approval (Yes/No)
- AccountCraft WhatsApp Automation - Infridet
- Add a datapoint to Beeminder when new activity is added to Strava
- Add new incoming emails to a Google Sheets spreadsheet as a new row.
- Automate Event Creation in Google Calendar from Google Sheets
- Automated Image Metadata Tagging (Community Node)
- Automatically Send Daily Meeting List to Telegram
- CFP Selection 1
- Calculate the Centroid of a Set of Vectors
- Capture Website Screenshots with Bright Data Web Unlocker and Save to Disk
- Check To Do on Notion and send message on Slack
- Create Nextcloud Deck card from email
- ETL pipeline
- Email mailbox as Todoist tasks
- Extract expenses from emails and add to Google Sheet
- Forward Filtered Gmail Notifications to Telegram Chat
- Forward Netflix emails to multiple email addresses with GMail and Mailjet
- Generate audio from text using OpenAI - text-to-speech Workflow
- Get the price of BTC in EUR and send an SMS when the price is larger than EUR 9000
- Gmail AI auto-responder: create draft replies to incoming emails
- N_01_Simple_Lead_Tracker_Automation_v4
- New invoice email notification
- On new Stripe Invoice Payment update Hubspot and notify the team in Slack
- OpenAI e-mail classification - application
- Play with Spotify from Telegram
- Receive messages from a topic and send an SMS
- Receive updates when a sale is made in Gumroad
- Receive updates when an event occurs in TheHive
- Send a cocktail recipe every day via a Telegram
- Send an SMS using MSG91
- Send an SMS using the Mocean node
- Send daily weather updates via a message in Line
- Simple OpenAI Image Generator
- Stripe Payment Order Sync – Auto Retrieve Customer & Product Purchased
- Summarize emails with A.I. then send to messenger
- Telegram Weather Workflow
- Telegram echo-bot
- Track Working Time and Pauses
- TwitterWorkflow
- Updating Shopify tags on Onfleet events
- Verify phone numbers
- Write a file to the host machine
- getBible Query v1.0
- send file to kindle through telegram bot
- 外送記帳
- 📦 New Email ➔ Create Google Task