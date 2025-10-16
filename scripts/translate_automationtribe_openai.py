#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script de traduction COMPLÈTE des workflows AutomationTribe avec OpenAI
Traduit TOUT : noms, prompts système, descriptions, etc.
"""

import json
import os
import requests
import time
from pathlib import Path

# Configuration
BASE_DIR = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"
OPENAI_API_KEY = None

def get_openai_key():
    """Récupérer la clé OpenAI depuis le .env"""
    env_file = '/var/www/automatehub/.env'
    if os.path.exists(env_file):
        with open(env_file, 'r') as f:
            for line in f:
                if line.strip().startswith('OPENAI_API_KEY='):
                    key = line.strip().split('=', 1)[1].strip('"\'')
                    return key
    return None

def translate_with_openai(text, context="général"):
    """Traduit un texte avec OpenAI GPT-4"""
    global OPENAI_API_KEY
    
    if not OPENAI_API_KEY:
        return text
    
    if not text or len(text.strip()) < 3:
        return text
    
    # Ne pas traduire si déjà en français
    french_indicators = ['tu es', 'vous êtes', 'votre', 'cette', 'dans le', 'avec le', 'pour le']
    if any(indicator in text.lower() for indicator in french_indicators):
        return text
    
    # Prompt adapté au contexte
    if context == "workflow_name":
        prompt = f"""Traduis ce nom de workflow n8n en français. 
Garde un style professionnel et technique.

Nom anglais : {text}
Nom français :"""
    
    elif context == "system_prompt":
        prompt = f"""Tu es un traducteur expert spécialisé dans la traduction de prompts d'IA.
Traduis ce prompt système en français en gardant :
- Les variables entre {{{{ }}}} intactes
- Le formatage markdown (###, **, etc.)
- Les termes techniques (JSON, API, etc.)
- Un ton professionnel et précis

Texte original :
{text}

Traduction française :"""
    
    else:  # context général
        prompt = f"""Traduis ce texte en français pour un workflow n8n.
Garde les variables {{{{ }}}} et les termes techniques intacts.

Texte : {text}
Traduction :"""

    try:
        response = requests.post(
            'https://api.openai.com/v1/chat/completions',
            headers={
                'Authorization': f'Bearer {OPENAI_API_KEY}',
                'Content-Type': 'application/json'
            },
            json={
                'model': 'gpt-4o-mini',
                'messages': [
                    {
                        'role': 'system',
                        'content': 'Tu es un traducteur expert français-anglais spécialisé dans la traduction technique et les workflows d\'automatisation.'
                    },
                    {
                        'role': 'user',
                        'content': prompt
                    }
                ],
                'max_tokens': min(4000, len(text) * 3),
                'temperature': 0.3
            },
            timeout=60
        )
        
        if response.status_code == 200:
            result = response.json()['choices'][0]['message']['content'].strip()
            return result
        else:
            print(f"❌ Erreur OpenAI {response.status_code}: {response.text}")
            return text
            
    except Exception as e:
        print(f"❌ Erreur traduction: {e}")
        return text

def update_node_connections(workflow, old_name, new_name):
    """Met à jour toutes les références au nom du nœud"""
    for node in workflow.get('nodes', []):
        params = node.get('parameters', {})
        
        # Mettre à jour les références dans les paramètres
        def update_references(obj):
            if isinstance(obj, dict):
                for key, value in obj.items():
                    if isinstance(value, str):
                        if f"$('{old_name}')" in value:
                            obj[key] = value.replace(f"$('{old_name}')", f"$('{new_name}')")
                        elif f'$("{old_name}")' in value:
                            obj[key] = value.replace(f'$("{old_name}")', f'$("{new_name}")')
                    elif isinstance(value, (dict, list)):
                        update_references(value)
            elif isinstance(obj, list):
                for item in obj:
                    update_references(item)
        
        update_references(params)

def translate_workflow_complete(workflow_path):
    """Traduit complètement un workflow avec OpenAI"""
    print(f"\n🔧 Traduction OpenAI : {os.path.basename(workflow_path)}")
    
    with open(workflow_path, 'r', encoding='utf-8') as f:
        workflow = json.load(f)
    
    changes = []
    node_name_mapping = {}
    
    # 1. Traduire le nom du workflow
    original_name = workflow.get('name', '')
    if original_name and 'Vidéos Longues' not in original_name:  # Skip déjà traduits
        print(f"  📝 Traduction du nom : {original_name}")
        translated_name = translate_with_openai(original_name, "workflow_name")
        if translated_name != original_name:
            workflow['name'] = translated_name
            changes.append(f"Nom workflow: '{original_name}' → '{translated_name}'")
            time.sleep(0.5)  # Éviter rate limit
    
    # 2. Ajouter tag Audelalia
    tags = workflow.get('tags', [])
    if not any(tag.get('name') == 'Audelalia' for tag in tags):
        tags.append({"id": "1", "name": "Audelalia"})
        workflow['tags'] = tags
        changes.append("Tag 'Audelalia' ajouté")
    
    # 3. Traduire les nœuds
    for node in workflow.get('nodes', []):
        node_id = node.get('id')
        old_name = node.get('name', '')
        
        # Traduire le nom du nœud si nécessaire
        if old_name and not any(fr in old_name for fr in ['Agent IA', 'Modèle de Chat', 'Déclencheur']):
            translated_node_name = translate_with_openai(old_name, "général")
            if translated_node_name != old_name:
                node['name'] = translated_node_name
                node_name_mapping[old_name] = translated_node_name
                changes.append(f"Nœud: '{old_name}' → '{translated_node_name}'")
                time.sleep(0.5)
        
        # Traduire les paramètres complexes
        params = node.get('parameters', {})
        
        # Gérer systemMessage dans options
        if 'options' in params and 'systemMessage' in params['options']:
            sys_msg = params['options']['systemMessage']
            if sys_msg and isinstance(sys_msg, str) and len(sys_msg) > 50:
                if 'You are' in sys_msg or 'Your task' in sys_msg:
                    print(f"  🤖 Traduction du prompt système ({len(sys_msg)} caractères)...")
                    if sys_msg.startswith('='):
                        content = sys_msg[1:]
                        translated = translate_with_openai(content, "system_prompt")
                        params['options']['systemMessage'] = '=' + translated
                    else:
                        translated = translate_with_openai(sys_msg, "system_prompt")
                        params['options']['systemMessage'] = translated
                    changes.append(f"Prompt système du nœud '{node.get('name')}' traduit")
                    time.sleep(1)  # Pause plus longue pour gros prompts
        
        # Gérer les messages dans values
        if 'messages' in params and 'values' in params['messages']:
            for i, message in enumerate(params['messages']['values']):
                if 'content' in message and isinstance(message['content'], str):
                    content = message['content']
                    if len(content) > 30 and any(word in content for word in ['Please', 'Create', 'Generate', 'You are']):
                        print(f"  💬 Traduction du message {i+1}...")
                        if content.startswith('='):
                            translated = translate_with_openai(content[1:], "général")
                            message['content'] = '=' + translated
                        else:
                            translated = translate_with_openai(content, "général")
                            message['content'] = translated
                        changes.append(f"Message {i+1} traduit")
                        time.sleep(0.5)
        
        # Gérer les prompts dans 'text'
        if 'text' in params and isinstance(params['text'], str):
            text = params['text']
            if len(text) > 30 and not text.startswith('={{') and any(word in text for word in ['Please', 'Create', 'Generate']):
                print(f"  📄 Traduction du texte...")
                translated = translate_with_openai(text, "général")
                params['text'] = translated
                changes.append("Texte traduit")
                time.sleep(0.5)
    
    # 4. Mettre à jour les connexions si des noms ont changé
    for old_name, new_name in node_name_mapping.items():
        update_node_connections(workflow, old_name, new_name)
    
    # 5. Sauvegarder
    if changes:
        with open(workflow_path, 'w', encoding='utf-8') as f:
            json.dump(workflow, f, indent=2, ensure_ascii=False)
        
        print(f"✅ {len(changes)} modifications appliquées:")
        for change in changes[:5]:  # Montrer max 5 changements
            print(f"   - {change}")
        if len(changes) > 5:
            print(f"   ... et {len(changes) - 5} autres")
        return True
    else:
        print("ℹ️  Déjà traduit")
        return False

def main():
    global OPENAI_API_KEY
    
    print("🚀 TRADUCTION COMPLÈTE AVEC OPENAI")
    print("=" * 60)
    
    # Obtenir la clé
    OPENAI_API_KEY = get_openai_key()
    if not OPENAI_API_KEY:
        print("❌ Clé OpenAI non trouvée dans .env")
        return
    
    print(f"✅ Clé OpenAI trouvée")
    print(f"📁 Dossier : {BASE_DIR}")
    
    workflows_translated = 0
    total = 0
    
    # D'abord restaurer les fichiers originaux
    print("\n📥 Restauration des fichiers originaux...")
    os.system(f"rm -rf {BASE_DIR} && cp -r /var/www/automatehub/AutomationTribe {BASE_DIR}")
    print("✅ Fichiers originaux restaurés")
    
    # Parcourir et traduire (avec limite pour éviter timeout)
    print("\n🔄 Traduction en cours...")
    files_to_translate = []
    for root, dirs, files in os.walk(BASE_DIR):
        for file in files:
            if file.endswith('.json'):
                files_to_translate.append(os.path.join(root, file))
    
    # Traiter par batch
    batch_size = 5  # Traiter 5 fichiers à la fois
    for i in range(0, len(files_to_translate), batch_size):
        batch = files_to_translate[i:i+batch_size]
        print(f"\n📦 Batch {i//batch_size + 1}/{(len(files_to_translate) + batch_size - 1)//batch_size}")
        
        for workflow_path in batch:
            total += 1
            try:
                if translate_workflow_complete(workflow_path):
                    workflows_translated += 1
            except Exception as e:
                print(f"❌ Erreur avec {os.path.basename(workflow_path)}: {e}")
    
    print(f"\n🎉 RÉSUMÉ FINAL:")
    print(f"   Total workflows : {total}")
    print(f"   Traduits : {workflows_translated}")
    print(f"   Déjà traduits : {total - workflows_translated}")

if __name__ == "__main__":
    main()