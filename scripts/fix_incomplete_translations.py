#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour corriger les traductions incomplètes dans les workflows n8n
Corrige les problèmes identifiés :
1. Noms de workflows non traduits 
2. Messages système dans options.systemMessage non traduits
3. Autres champs dans options non traduits
"""

import json
import os
import re
import sys
from pathlib import Path

# Ajouter le répertoire parent au path pour les imports
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

def translate_long_prompt(text):
    """Traduit un prompt long en utilisant des règles simples"""
    if not text or len(text.strip()) == 0:
        return text
    
    # Traductions spécifiques pour les prompts AI
    translations = {
        # Rôles et instructions générales
        "You are an intelligent and supportive assistant": "Tu es un assistant intelligent et de soutien",
        "Your name is": "Ton nom est",
        "and you communicate in a warm, friendly, and helpful manner": "et tu communiques de manière chaleureuse, amicale et utile",
        "Always provide responses in": "Fournis toujours des réponses en",
        "English": "français",
        
        # Sections
        "## ROLE": "## RÔLE",
        "## Important Information": "## Informations importantes", 
        "## Tools": "## Outils",
        "### Get Emails": "### Récupérer les emails",
        "### Send Email": "### Envoyer un email",
        "### Create Draft": "### Créer un brouillon",
        "### Get Calendar": "### Récupérer le calendrier",
        "### Set Calendar": "### Définir le calendrier",
        "### Check Calendar": "### Vérifier le calendrier",
        "### Contacts": "### Contacts",
        "### Tasks": "### Tâches",
        "### Get Tasks": "### Récupérer les tâches",
        "### Translator": "### Traducteur",
        "### Google Search": "### Recherche Google",
        
        # Instructions spécifiques
        "Use this tool to fetch unread emails from Gmail": "Utilise cet outil pour récupérer les emails non lus de Gmail",
        "When responding, include": "Lors de la réponse, inclure",
        "A concise summary": "Un résumé concis",
        "of the email content": "du contenu de l'email",
        "The sender's email address": "L'adresse email de l'expéditeur",
        "The sender's name": "Le nom de l'expéditeur",
        
        "This tool allows you to send an email": "Cet outil permet d'envoyer un email",
        "Ensure that the email includes": "Assurer que l'email inclut",
        "Recipient's email address": "Adresse email du destinataire",
        "Subject line": "Ligne d'objet",
        "Email body": "Corps de l'email",
        "Add my name at the end": "Ajouter mon nom à la fin",
        
        "Use this tool to create an email draft": "Utilise cet outil pour créer un brouillon d'email",
        "It requires the recipient's email address, subject, and email body": "Il nécessite l'adresse email du destinataire, l'objet et le corps de l'email",
        "The draft will be saved but not sent": "Le brouillon sera sauvegardé mais pas envoyé",
        "allowing for further review and edits before sending": "permettant une révision et des modifications supplémentaires avant l'envoi",
        
        "Use this tool to retrieve upcoming calendar events": "Utilise cet outil pour récupérer les événements de calendrier à venir",
        "Use this tool to create new events in the calendar": "Utilise cet outil pour créer de nouveaux événements dans le calendrier",
        "Schedule the event": "Programmer l'événement",
        "on the specified date and time": "à la date et l'heure spécifiées",
        "Include a description": "Inclure une description",
        "Ensure meetings are set in": "Assurer que les réunions sont définies en",
        "Europe/Bucharest time": "heure Europe/Bucharest",
        
        "Utilize this tool to review scheduled calendar events": "Utilise cet outil pour examiner les événements de calendrier programmés",
        "All times are based on Europe/Bucharest time": "Toutes les heures sont basées sur l'heure Europe/Bucharest",
        
        "Use this tool to retrieve details about contacts": "Utilise cet outil pour récupérer les détails sur les contacts",
        "including their email addresses": "y compris leurs adresses email",
        
        "Use this tool to create tasks": "Utilise cet outil pour créer des tâches",
        "in Google Tasks": "dans Google Tasks",
        "Retrieve existing tasks from Google Tasks using this tool": "Récupérer les tâches existantes de Google Tasks en utilisant cet outil",
        
        "Translate text from one language to another": "Traduire du texte d'une langue à une autre",
        "Only return the translated text": "Retourner seulement le texte traduit",
        "Do not include phrases": "Ne pas inclure de phrases",
        "like": "comme",
        "The translation of": "La traduction de",
        "into": "en",
        "is": "est",
        "Provide only the translated content": "Fournir seulement le contenu traduit",
        
        "Use this function to search the web using Google": "Utilise cette fonction pour rechercher sur le web en utilisant Google",
        "It fetches search results based on a keyword query": "Il récupère les résultats de recherche basés sur une requête de mots-clés",
        "and returns relevant links": "et retourne des liens pertinents",
        
        # Variables et contexte
        "You are interacting with": "Tu interagis avec",
        "Current date and time": "Date et heure actuelles",
    }
    
    # Appliquer les traductions
    result = text
    for en, fr in translations.items():
        result = re.sub(re.escape(en), fr, result, flags=re.IGNORECASE)
    
    return result

def translate_workflow_name(name):
    """Traduit le nom du workflow"""
    translations = {
        "Sumobundle - Telegram Agent": "Sumobundle - Agent Telegram",
        "Generate social post ideas or summaries": "Générer des idées de publications sociales ou des résumés",
        "Life Style Product Photo Generator": "Générateur de Photos Lifestyle de Produits",
        "Gmail Phishing Email Detection and Processing": "Détection et traitement des emails de phishing Gmail",
        "Automation": "Automatisation",
        "Agent": "Agent",
        "Generator": "Générateur",
        "Trigger": "Déclencheur",
        "Workflow": "Workflow",
        "Email": "Email",
        "Chat": "Chat",
        "Bot": "Bot",
        "Assistant": "Assistant",
    }
    
    result = name
    for en, fr in translations.items():
        result = re.sub(re.escape(en), fr, result, flags=re.IGNORECASE)
    
    return result

def fix_workflow_translation(workflow_path):
    """Corrige la traduction d'un workflow spécifique"""
    print(f"\n🔧 Correction de: {os.path.basename(workflow_path)}")
    
    # Charger le workflow
    with open(workflow_path, 'r', encoding='utf-8') as f:
        workflow = json.load(f)
    
    changes_made = []
    
    # 1. Corriger le nom du workflow s'il n'est pas traduit
    original_name = workflow.get('name', '')
    translated_name = translate_workflow_name(original_name)
    if translated_name != original_name:
        workflow['name'] = translated_name
        changes_made.append(f"Nom: '{original_name}' → '{translated_name}'")
    
    # 2. Corriger les nœuds
    for node in workflow.get('nodes', []):
        node_name = node.get('name', '')
        params = node.get('parameters', {})
        
        # Vérifier et corriger options.systemMessage
        if 'options' in params and 'systemMessage' in params['options']:
            sys_msg = params['options']['systemMessage']
            if sys_msg and sys_msg.startswith('='):
                # Retirer le =, traduire, et remettre le =
                content_without_equal = sys_msg[1:]
                if "You are an intelligent" in content_without_equal or "English" in content_without_equal:
                    translated_content = translate_long_prompt(content_without_equal)
                    params['options']['systemMessage'] = '=' + translated_content
                    changes_made.append(f"SystemMessage du nœud '{node_name}' traduit")
        
        # Vérifier et corriger autres champs dans options
        if 'options' in params:
            for key, value in params['options'].items():
                if isinstance(value, str) and key != 'systemMessage':
                    # Traductions simples pour d'autres champs
                    simple_translations = {
                        "Generate social post ideas": "Générer des idées de publications sociales",
                        "Blog post": "Article de blog",
                        "Youtube link": "Lien Youtube",
                    }
                    for en, fr in simple_translations.items():
                        if en in value:
                            new_value = value.replace(en, fr)
                            params['options'][key] = new_value
                            changes_made.append(f"Option '{key}' du nœud '{node_name}' traduite")
    
    # 3. Sauvegarder si des changements ont été faits
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

def fix_all_incomplete_translations(base_dir):
    """Corrige toutes les traductions incomplètes dans le répertoire"""
    print("🚀 CORRECTION DES TRADUCTIONS INCOMPLÈTES")
    print("=" * 50)
    
    workflows_fixed = 0
    total_workflows = 0
    
    # Parcourir tous les fichiers JSON
    for root, dirs, files in os.walk(base_dir):
        for file in files:
            if file.endswith('.json'):
                workflow_path = os.path.join(root, file)
                total_workflows += 1
                
                try:
                    if fix_workflow_translation(workflow_path):
                        workflows_fixed += 1
                except Exception as e:
                    print(f"❌ Erreur avec {file}: {e}")
    
    print(f"\n🎉 RÉSUMÉ:")
    print(f"   Workflows traités: {total_workflows}")
    print(f"   Workflows corrigés: {workflows_fixed}")
    print(f"   Taux de correction: {workflows_fixed/total_workflows*100:.1f}%")

if __name__ == "__main__":
    # Lancer la correction sur tous les workflows
    base_dir = "/var/www/automatehub/workflows_traduits/FR"
    fix_all_incomplete_translations(base_dir)