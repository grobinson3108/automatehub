#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Traduction intelligente de TOUS les workflows avec règles spécifiques
"""

import json
import os
import requests
import time

BASE_DIR = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"

# Traductions prédéfinies pour éviter les erreurs
NODE_TRANSLATIONS = {
    "Code": "Code",
    "OpenAI": "OpenAI",
    "Google Drive": "Google Drive",
    "ExifTool": "ExifTool",
    "Airtable": "Airtable",
    "YouTube": "YouTube",
    "Slack": "Slack",
    "Gmail": "Gmail",
    "Telegram": "Telegram",
    "When clicking 'Test workflow'": "En cliquant sur 'Tester le workflow'",
    "Manual Trigger": "Déclencheur manuel",
    "HTTP Request": "Requête HTTP",
    "Set": "Définir",
    "Switch": "Interrupteur",
    "Wait": "Attendre",
    "Loop Over Items": "Boucler sur les éléments",
    "Split In Batches": "Diviser en lots",
    "Download File": "Télécharger le fichier",
    "Download Images": "Télécharger les images",
    "Search in Google Drive": "Rechercher dans Google Drive",
    "Add Rows": "Ajouter des lignes",
    "Upload Video": "Télécharger la vidéo",
    "Edit Fields": "Modifier les champs",
    "AI Agent": "Agent IA",
    "OpenAI Chat Model": "Modèle de Chat OpenAI",
    "Structured Output Parser": "Analyseur de sortie structurée",
}

def get_openai_key():
    """Récupérer la clé OpenAI depuis le .env"""
    env_file = '/var/www/automatehub/.env'
    if os.path.exists(env_file):
        with open(env_file, 'r') as f:
            for line in f:
                if line.strip().startswith('OPENAI_API_KEY='):
                    return line.strip().split('=', 1)[1].strip('"\'')
    return None

def translate_text_smart(text, api_key, max_length=500):
    """Traduit un texte avec OpenAI de manière intelligente"""
    if not text or len(text.strip()) < 10:
        return text
    
    # Si le texte est déjà en français, ne pas traduire
    if any(word in text.lower() for word in ['vous', 'votre', 'avec', 'pour', 'dans']):
        return text
    
    # Limiter la longueur pour éviter les problèmes
    if len(text) > max_length:
        # Pour les longs textes, traduire juste le début
        text_to_translate = text[:max_length] + "..."
    else:
        text_to_translate = text
    
    prompt = f"""Traduis ce texte technique en français.
Garde les variables {{{{}}}} et les termes techniques intacts.

Texte : {text_to_translate}

Traduction française concise :"""

    try:
        response = requests.post(
            'https://api.openai.com/v1/chat/completions',
            headers={'Authorization': f'Bearer {api_key}'},
            json={
                'model': 'gpt-4o-mini',
                'messages': [{'role': 'user', 'content': prompt}],
                'max_tokens': 1000,
                'temperature': 0.3
            },
            timeout=15
        )
        
        if response.status_code == 200:
            translation = response.json()['choices'][0]['message']['content'].strip()
            # Si c'était tronqué, garder l'original
            if len(text) > max_length:
                return text  # Garder l'original pour les très longs textes
            return translation
        return text
    except:
        return text

def process_workflow(file_path, api_key):
    """Traite un workflow avec traductions intelligentes"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            workflow = json.load(f)
        
        changes = False
        
        # 1. Tag Audelalia
        tags = workflow.get('tags', [])
        if not any(tag.get('name') == 'Audelalia' for tag in tags):
            tags.append({"id": "1", "name": "Audelalia"})
            workflow['tags'] = tags
            changes = True
        
        # 2. Traduire les nœuds
        for node in workflow.get('nodes', []):
            node_name = node.get('name', '')
            
            # Utiliser les traductions prédéfinies pour les noms de nœuds
            if node_name in NODE_TRANSLATIONS:
                new_name = NODE_TRANSLATIONS[node_name]
                if new_name != node_name:
                    node['name'] = new_name
                    changes = True
            
            # Traduire les prompts dans 'text' (max 500 chars)
            params = node.get('parameters', {})
            if 'text' in params and isinstance(params['text'], str):
                text = params['text']
                if len(text) > 50 and any(word in text for word in ['Generate', 'Create', 'Your task']):
                    if text.startswith('='):
                        translated = translate_text_smart(text[1:], api_key, 300)
                        if translated != text[1:]:
                            params['text'] = '=' + translated
                            changes = True
                            time.sleep(1)
        
        # 3. Sauvegarder
        if changes:
            with open(file_path, 'w', encoding='utf-8') as f:
                json.dump(workflow, f, indent=2, ensure_ascii=False)
            return True
        return False
        
    except Exception as e:
        print(f"Erreur {os.path.basename(file_path)}: {e}")
        return False

def main():
    print("🚀 TRADUCTION INTELLIGENTE FINALE")
    print("=" * 50)
    
    api_key = get_openai_key()
    if not api_key:
        print("❌ Clé OpenAI manquante")
        return
    
    # Lister tous les workflows
    workflows = []
    for root, dirs, files in os.walk(BASE_DIR):
        for file in files:
            if file.endswith('.json'):
                workflows.append(os.path.join(root, file))
    
    print(f"📁 {len(workflows)} workflows trouvés")
    
    # Traiter chaque workflow
    translated = 0
    for i, workflow_path in enumerate(workflows):
        print(f"\n[{i+1}/{len(workflows)}] {os.path.basename(workflow_path)}", end=" ")
        if process_workflow(workflow_path, api_key):
            print("✅")
            translated += 1
        else:
            print("⏭️")
    
    print(f"\n✅ Terminé : {translated} workflows modifiés")

if __name__ == "__main__":
    main()