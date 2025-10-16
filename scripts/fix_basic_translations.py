#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour corriger les traductions de base sans OpenAI
"""

import json
import os

def translate_basic_text(text):
    """Traductions de base pour les termes courants"""
    if not text or not isinstance(text, str):
        return text
    
    # Traductions de base
    basic_translations = {
        # Noms de workflows courants
        "Sumobundle - Telegram Agent": "Sumobundle - Agent Telegram",
        "Sub workflow - Get Google Tasks": "Sub workflow - Récupérer les tâches Google",
        "Business Canvas Generator": "Générateur de Business Canvas",
        "Simple OpenAI Image Generator": "Générateur d'images OpenAI simple",
        
        # Noms de nœuds
        "OpenAI Chat Model": "Modèle de Chat OpenAI",
        "Google Tasks": "Tâches Google",
        "Workflow Input Trigger": "Déclencheur d'entrée du workflow",
        "AI Agent": "Agent IA",
        "Window Buffer Memory": "Mémoire tampon fenêtre",
        "Generate Audio": "Générer l'audio",
        "Set Calendar": "Définir le calendrier",
        "Get Calendar": "Récupérer le calendrier",
        "Get Emails": "Récupérer les emails",
        "Send Email": "Envoyer un email",
        "Create Draft": "Créer un brouillon",
        "Get Tasks": "Récupérer les tâches",
        "Contacts": "Contacts",
        "Translator": "Traducteur",
        "Google Search": "Recherche Google",
        
        # Termes généraux
        "Agent": "Agent",
        "Generator": "Générateur", 
        "Assistant": "Assistant",
        "Bot": "Bot",
        "Automation": "Automatisation",
        "Trigger": "Déclencheur",
        "Workflow": "Workflow",
    }
    
    result = text
    for en, fr in basic_translations.items():
        result = result.replace(en, fr)
    
    return result

def fix_basic_systemMessage(text):
    """Traduction basique du système message pour Sumobundle"""
    if not text or 'You are an intelligent' not in text:
        return text
    
    # Traductions spécifiques pour le prompt Sumobundle
    translations = {
        "## ROLE": "## RÔLE",
        "You are an intelligent and supportive assistant.": "Tu es un assistant intelligent et de soutien.",
        "Your name is **Sumobundle**, and you communicate in a warm, friendly, and helpful manner.": "Ton nom est **Sumobundle**, et tu communiques de manière chaleureuse, amicale et utile.",
        "Always provide responses in **English**.": "Fournis toujours des réponses en **français**.",
        "## Important Information": "## Informations importantes",
        "You are interacting with": "Tu interagis avec",
        "Current date and time": "Date et heure actuelles",
        "## Tools": "## Outils",
        "### Get Emails": "### Récupérer les emails",
        "Use this tool to fetch unread emails from Gmail.": "Utilise cet outil pour récupérer les emails non lus de Gmail.",
        "When responding, include:": "Lors de la réponse, inclure :",
        "A concise summary": "Un résumé concis",
        "of the email content": "du contenu de l'email",
        "The sender's email address": "L'adresse email de l'expéditeur",
        "The sender's name": "Le nom de l'expéditeur",
        "### Send Email": "### Envoyer un email",
        "This tool allows you to send an email.": "Cet outil permet d'envoyer un email.",
        "Ensure that the email includes:": "Assurer que l'email inclut :",
        "Recipient's email address": "Adresse email du destinataire",
        "Subject line": "Ligne d'objet",
        "Email body": "Corps de l'email",
        "Add my name at the end.": "Ajouter mon nom à la fin.",
        "## Create Draft": "## Créer un brouillon",
        "Use this tool to create an email draft.": "Utilise cet outil pour créer un brouillon d'email.",
        "### Get Calendar": "### Récupérer le calendrier",
        "Use this tool to retrieve upcoming calendar events.": "Utilise cet outil pour récupérer les événements de calendrier à venir.",
        "### Set Calendar": "### Définir le calendrier",
        "Use this tool to create new events in the calendar.": "Utilise cet outil pour créer de nouveaux événements dans le calendrier.",
        "Schedule the event": "Programmer l'événement",
        "on the specified date and time.": "à la date et l'heure spécifiées.",
        "Include a description.": "Inclure une description.",
        "### Check Calendar": "### Vérifier le calendrier",
        "Utilize this tool to review scheduled calendar events.": "Utilise cet outil pour examiner les événements de calendrier programmés.",
        "### Contacts": "### Contacts",
        "Use this tool to retrieve details about contacts": "Utilise cet outil pour récupérer les détails sur les contacts",
        "including their email addresses.": "y compris leurs adresses email.",
        "### Tasks": "### Tâches",
        "Use this tool to": "Utilise cet outil pour",
        "create tasks": "créer des tâches",
        "in Google Tasks.": "dans Google Tasks.",
        "### Get Tasks": "### Récupérer les tâches",
        "Retrieve existing tasks from Google Tasks using this tool.": "Récupérer les tâches existantes de Google Tasks en utilisant cet outil.",
        "### Translator": "### Traducteur",
        "Translate text from one language to another.": "Traduire du texte d'une langue à une autre.",
        "Only return the translated text.": "Retourner seulement le texte traduit.",
        "Do not include phrases": "Ne pas inclure de phrases",
        "Provide only the translated content.": "Fournir seulement le contenu traduit.",
        "## Google Search": "## Recherche Google",
        "Use this function to": "Utilise cette fonction pour",
        "search the web using Google.": "rechercher sur le web en utilisant Google.",
        "It fetches search results based on a keyword query": "Il récupère les résultats de recherche basés sur une requête de mots-clés",
        "and returns relevant links.": "et retourne des liens pertinents.",
    }
    
    result = text
    for en, fr in translations.items():
        result = result.replace(en, fr)
    
    return result

def fix_workflow_basic(workflow_path):
    """Corrige un workflow avec traductions de base"""
    print(f"\n🔧 Correction basique: {os.path.basename(workflow_path)}")
    
    # Charger le workflow
    with open(workflow_path, 'r', encoding='utf-8') as f:
        workflow = json.load(f)
    
    changes_made = []
    
    # 1. Traduire le nom du workflow
    original_name = workflow.get('name', '')
    translated_name = translate_basic_text(original_name)
    if translated_name != original_name:
        workflow['name'] = translated_name
        changes_made.append(f"Nom: '{original_name}' → '{translated_name}'")
    
    # 2. Ajouter le tag Audelalia s'il n'existe pas
    tags = workflow.get('tags', [])
    has_audelalia = any(tag.get('name') == 'Audelalia' for tag in tags)
    if not has_audelalia:
        tags.append({"id": "1", "name": "Audelalia"})
        workflow['tags'] = tags
        changes_made.append("Tag 'Audelalia' ajouté")
    
    # 3. Traiter les nœuds
    for node in workflow.get('nodes', []):
        node_name = node.get('name', '')
        params = node.get('parameters', {})
        
        # Traduire le nom du nœud
        translated_node_name = translate_basic_text(node_name)
        if translated_node_name != node_name:
            node['name'] = translated_node_name
            changes_made.append(f"Nom de nœud: '{node_name}' → '{translated_node_name}'")
        
        # Gérer systemMessage dans options
        if 'options' in params and 'systemMessage' in params['options']:
            sys_msg = params['options']['systemMessage']
            if sys_msg and isinstance(sys_msg, str):
                if sys_msg.startswith('='):
                    content_without_equal = sys_msg[1:]
                    translated_content = fix_basic_systemMessage(content_without_equal)
                    if translated_content != content_without_equal:
                        params['options']['systemMessage'] = '=' + translated_content
                        changes_made.append(f"SystemMessage du nœud '{node_name}' traduit")
                else:
                    translated_content = fix_basic_systemMessage(sys_msg)
                    if translated_content != sys_msg:
                        params['options']['systemMessage'] = translated_content
                        changes_made.append(f"SystemMessage du nœud '{node_name}' traduit")
    
    # 4. Sauvegarder si des changements ont été faits
    if changes_made:
        with open(workflow_path, 'w', encoding='utf-8') as f:
            json.dump(workflow, f, indent=2, ensure_ascii=False)
        
        print(f"✅ {len(changes_made)} corrections appliquées:")
        for change in changes_made:
            print(f"   - {change}")
        return True
    else:
        print("ℹ️  Aucune correction nécessaire")
        return False

def fix_automationtribe_basic():
    """Corrige les workflows AutomationTribe avec traductions de base"""
    print("🚀 CORRECTION BASIQUE DES WORKFLOWS AUTOMATIONTRIBE")
    print("=" * 60)
    
    base_dir = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"
    workflows_fixed = 0
    total_workflows = 0
    
    # Parcourir tous les fichiers JSON
    for root, dirs, files in os.walk(base_dir):
        for file in files:
            if file.endswith('.json'):
                workflow_path = os.path.join(root, file)
                total_workflows += 1
                
                try:
                    if fix_workflow_basic(workflow_path):
                        workflows_fixed += 1
                except Exception as e:
                    print(f"❌ Erreur avec {file}: {e}")
    
    print(f"\n🎉 RÉSUMÉ:")
    print(f"   Workflows traités: {total_workflows}")
    print(f"   Workflows corrigés: {workflows_fixed}")
    print(f"   Taux de correction: {workflows_fixed/total_workflows*100:.1f}%")

if __name__ == "__main__":
    fix_automationtribe_basic()