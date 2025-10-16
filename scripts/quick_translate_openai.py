#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Traduction rapide des éléments essentiels avec OpenAI
"""

import json
import os
import requests
import time
import sys

BASE_DIR = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"

def get_openai_key():
    """Récupérer la clé OpenAI depuis le .env"""
    env_file = '/var/www/automatehub/.env'
    if os.path.exists(env_file):
        with open(env_file, 'r') as f:
            for line in f:
                if line.strip().startswith('OPENAI_API_KEY='):
                    return line.strip().split('=', 1)[1].strip('"\'')
    return None

def translate_batch(texts, api_key):
    """Traduit un lot de textes avec OpenAI"""
    if not texts or not api_key:
        return texts
    
    # Créer un prompt avec tous les textes
    prompt = """Traduis ces éléments de workflow n8n en français. 
Retourne un JSON avec les traductions dans le même ordre.
Garde les variables {{}} et termes techniques intacts.

Textes à traduire :
"""
    
    for i, text in enumerate(texts):
        prompt += f"\n{i+1}. {text}"
    
    prompt += "\n\nRéponse JSON avec les traductions :"
    
    try:
        response = requests.post(
            'https://api.openai.com/v1/chat/completions',
            headers={
                'Authorization': f'Bearer {api_key}',
                'Content-Type': 'application/json'
            },
            json={
                'model': 'gpt-4o-mini',
                'messages': [
                    {'role': 'user', 'content': prompt}
                ],
                'max_tokens': 4000,
                'temperature': 0.3
            },
            timeout=30
        )
        
        if response.status_code == 200:
            result = response.json()['choices'][0]['message']['content']
            # Essayer d'extraire le JSON
            try:
                if '{' in result:
                    json_str = result[result.find('{'):result.rfind('}')+1]
                    translations = json.loads(json_str)
                    return [translations.get(str(i+1), text) for i, text in enumerate(texts)]
            except:
                pass
        return texts
            
    except Exception as e:
        print(f"❌ Erreur batch: {e}")
        return texts

def process_workflow(file_path, api_key):
    """Traite un workflow rapidement"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            workflow = json.load(f)
        
        changes = False
        texts_to_translate = []
        text_refs = []
        
        # 1. Collecter les textes à traduire
        
        # Nom du workflow
        name = workflow.get('name', '')
        if name and not any(fr in name.lower() for fr in ['générateur', 'agent', 'automatiser']):
            texts_to_translate.append(name)
            text_refs.append(('name', None))
        
        # Noms des nœuds et prompts importants
        for i, node in enumerate(workflow.get('nodes', [])):
            # Nom du nœud
            node_name = node.get('name', '')
            if node_name and not any(fr in node_name for fr in ['Agent IA', 'Modèle de', 'Déclencheur']):
                texts_to_translate.append(node_name)
                text_refs.append(('node_name', i))
            
            # SystemMessage (juste les 200 premiers caractères)
            params = node.get('parameters', {})
            if 'options' in params and 'systemMessage' in params['options']:
                msg = params['options']['systemMessage']
                if msg and len(msg) > 50 and ('You are' in msg or 'Your task' in msg):
                    # Traduire juste le début pour aller plus vite
                    if msg.startswith('='):
                        preview = msg[1:201]
                    else:
                        preview = msg[:200]
                    texts_to_translate.append(preview + "...")
                    text_refs.append(('system_message', i))
        
        # 2. Traduire par batch
        if texts_to_translate:
            print(f"  📝 Traduction de {len(texts_to_translate)} éléments...")
            translations = translate_batch(texts_to_translate, api_key)
            
            # 3. Appliquer les traductions
            for (ref_type, index), translation in zip(text_refs, translations):
                if ref_type == 'name':
                    workflow['name'] = translation.replace("...", "")
                    changes = True
                elif ref_type == 'node_name':
                    workflow['nodes'][index]['name'] = translation.replace("...", "")
                    changes = True
                elif ref_type == 'system_message':
                    # Pour les prompts, on garde l'original (trop long à traduire complètement)
                    # mais on ajoute une note
                    node = workflow['nodes'][index]
                    if 'options' in node['parameters']:
                        original = node['parameters']['options']['systemMessage']
                        if original.startswith('='):
                            # Ajouter une note de traduction partielle
                            node['parameters']['options']['systemMessage'] = f"=## TRADUIT PARTIELLEMENT ##\n{original[1:]}"
                        changes = True
        
        # 4. Ajouter le tag Audelalia
        tags = workflow.get('tags', [])
        if not any(tag.get('name') == 'Audelalia' for tag in tags):
            tags.append({"id": "1", "name": "Audelalia"})
            workflow['tags'] = tags
            changes = True
        
        # 5. Sauvegarder si changé
        if changes:
            with open(file_path, 'w', encoding='utf-8') as f:
                json.dump(workflow, f, indent=2, ensure_ascii=False)
            return True
        return False
        
    except Exception as e:
        print(f"❌ Erreur {os.path.basename(file_path)}: {e}")
        return False

def main():
    print("🚀 TRADUCTION RAPIDE AUTOMATIONTRIBE")
    print("=" * 60)
    
    api_key = get_openai_key()
    if not api_key:
        print("❌ Clé OpenAI manquante")
        return
    
    # Lister tous les fichiers
    files = []
    for root, dirs, filenames in os.walk(BASE_DIR):
        for filename in filenames:
            if filename.endswith('.json'):
                files.append(os.path.join(root, filename))
    
    print(f"📁 {len(files)} workflows trouvés")
    
    translated = 0
    for i, file_path in enumerate(files):
        print(f"\n[{i+1}/{len(files)}] {os.path.basename(file_path)}")
        if process_workflow(file_path, api_key):
            translated += 1
        
        # Pause entre les fichiers
        if i < len(files) - 1:
            time.sleep(1)
    
    print(f"\n✅ Résumé: {translated}/{len(files)} workflows traduits")

if __name__ == "__main__":
    main()