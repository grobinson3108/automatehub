#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Traduit UN SEUL workflow complètement avec OpenAI
"""

import json
import os
import requests
import sys
import time

def get_openai_key():
    """Récupérer la clé OpenAI depuis le .env"""
    env_file = '/var/www/automatehub/.env'
    if os.path.exists(env_file):
        with open(env_file, 'r') as f:
            for line in f:
                if line.strip().startswith('OPENAI_API_KEY='):
                    return line.strip().split('=', 1)[1].strip('"\'')
    return None

def translate_with_openai(text, api_key, context="general"):
    """Traduit un texte avec OpenAI"""
    if not text or len(text.strip()) < 3:
        return text
    
    prompt = f"""Traduis ce texte en français pour un workflow n8n.
Contexte : {context}
Règles :
- Garde les variables {{{{ }}}} intactes
- Traduis tout le contenu descriptif
- Garde un style professionnel

Texte : {text}

Traduction française :"""

    try:
        response = requests.post(
            'https://api.openai.com/v1/chat/completions',
            headers={'Authorization': f'Bearer {api_key}'},
            json={
                'model': 'gpt-4o-mini',
                'messages': [{'role': 'user', 'content': prompt}],
                'max_tokens': min(4000, len(text) * 2),
                'temperature': 0.3
            },
            timeout=30
        )
        
        if response.status_code == 200:
            return response.json()['choices'][0]['message']['content'].strip()
        else:
            print(f"Erreur API: {response.status_code}")
            return text
    except Exception as e:
        print(f"Erreur: {e}")
        return text

def translate_workflow_complete(workflow_path):
    """Traduit complètement un workflow"""
    api_key = get_openai_key()
    if not api_key:
        print("❌ Clé OpenAI manquante")
        return
    
    print(f"\n📄 Traduction de : {os.path.basename(workflow_path)}")
    
    with open(workflow_path, 'r', encoding='utf-8') as f:
        workflow = json.load(f)
    
    changes = []
    
    # 1. Traduire le nom si nécessaire
    name = workflow.get('name', '')
    if name and 'Photos de stock EXIF' not in name:
        translated_name = translate_with_openai(name, api_key, "workflow_name")
        if translated_name != name:
            workflow['name'] = translated_name
            changes.append(f"Nom: {name} → {translated_name}")
            time.sleep(1)
    
    # 2. Tag Audelalia
    tags = workflow.get('tags', [])
    if not any(tag.get('name') == 'Audelalia' for tag in tags):
        tags.append({"id": "1", "name": "Audelalia"})
        workflow['tags'] = tags
        changes.append("Tag Audelalia ajouté")
    
    # 3. Traduire TOUS les nœuds
    for node in workflow.get('nodes', []):
        node_name = node.get('name', '')
        
        # Traduire le nom du nœud
        if node_name:
            print(f"  🔄 Nœud : {node_name}")
            translated = translate_with_openai(node_name, api_key, "node_name")
            if translated != node_name:
                node['name'] = translated
                changes.append(f"Nœud: {node_name} → {translated}")
                time.sleep(0.5)
        
        # Traduire les paramètres
        params = node.get('parameters', {})
        
        # Texte principal
        if 'text' in params and isinstance(params['text'], str):
            text = params['text']
            if len(text) > 20 and any(word in text for word in ['Generate', 'Create', 'Analyze']):
                print(f"  📝 Traduction du texte principal...")
                if text.startswith('='):
                    translated = translate_with_openai(text[1:], api_key, "prompt")
                    params['text'] = '=' + translated
                else:
                    params['text'] = translate_with_openai(text, api_key, "prompt")
                changes.append("Texte principal traduit")
                time.sleep(1)
        
        # Messages
        if 'messages' in params and 'values' in params['messages']:
            for i, msg in enumerate(params['messages']['values']):
                if 'content' in msg and isinstance(msg['content'], str):
                    content = msg['content']
                    if len(content) > 20:
                        print(f"  💬 Traduction message {i+1}...")
                        if content.startswith('='):
                            translated = translate_with_openai(content[1:], api_key, "message")
                            msg['content'] = '=' + translated
                        else:
                            msg['content'] = translate_with_openai(content, api_key, "message")
                        changes.append(f"Message {i+1} traduit")
                        time.sleep(1)
        
        # Options systemMessage
        if 'options' in params and 'systemMessage' in params['options']:
            sys_msg = params['options']['systemMessage']
            if sys_msg and len(sys_msg) > 20:
                print(f"  🤖 Traduction systemMessage...")
                if sys_msg.startswith('='):
                    translated = translate_with_openai(sys_msg[1:], api_key, "system_prompt")
                    params['options']['systemMessage'] = '=' + translated
                else:
                    params['options']['systemMessage'] = translate_with_openai(sys_msg, api_key, "system_prompt")
                changes.append("SystemMessage traduit")
                time.sleep(1)
    
    # 4. Sauvegarder
    if changes:
        with open(workflow_path, 'w', encoding='utf-8') as f:
            json.dump(workflow, f, indent=2, ensure_ascii=False)
        print(f"\n✅ {len(changes)} modifications appliquées")
        for change in changes[:10]:
            print(f"  - {change}")
    else:
        print("\n❌ Aucune modification nécessaire")

if __name__ == "__main__":
    if len(sys.argv) < 2:
        # Traduire le workflow Stock photos EXIF
        workflow_path = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe/3 - 2500€ revenu passif/Download workflow - Stock Photo Metadata/Stock_photos_EXIF.json"
    else:
        workflow_path = sys.argv[1]
    
    translate_workflow_complete(workflow_path)