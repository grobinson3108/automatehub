#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour corriger les traductions avec OpenAI proprement
"""

import json
import os
import requests
import time
from pathlib import Path

# Configuration OpenAI
def get_openai_key():
    """Récupérer la clé OpenAI"""
    key = os.environ.get('OPENAI_API_KEY')
    
    if not key:
        env_file = '/var/www/automatehub/.env'
        if os.path.exists(env_file):
            with open(env_file, 'r') as f:
                for line in f:
                    if line.strip().startswith('OPENAI_API_KEY='):
                        key = line.strip().split('=', 1)[1].strip('"\'')
                        break
    
    # Utiliser une clé par défaut pour le test (sera masquée)
    if not key:
        key = "sk-proj-test"  # Clé factice pour le test
    
    return key

OPENAI_API_KEY = get_openai_key()

def translate_with_openai(text, context="workflow"):
    """Traduit un texte avec OpenAI en gardant le contexte technique"""
    if not OPENAI_API_KEY:
        print("❌ Clé OpenAI manquante")
        return text
    
    if not text or len(text.strip()) < 3:
        return text
    
    # Si c'est déjà en français ou contient du code, ne pas traduire
    french_indicators = ['vous êtes', 'tu es', 'votre', 'cette', 'dans', 'avec', 'pour', 'sur']
    if any(indicator in text.lower() for indicator in french_indicators):
        return text
    
    # Si c'est du code technique pur, ne pas traduire
    technical_indicators = ['{{', '}}', 'json.', 'item.', '$.', 'parameters.', 'node.', 'API']
    if any(indicator in text for indicator in technical_indicators) and len(text) < 50:
        return text
    
    prompt = f"""Traduis ce texte en français en gardant le contexte technique d'un workflow n8n.

RÈGLES IMPORTANTES :
1. Garde les variables/expressions entre {{ }} intactes
2. Garde les termes techniques API, JSON, etc.
3. Traduis le contenu descriptif et les instructions
4. Si c'est un prompt système d'IA, traduis-le complètement en français
5. Garde un ton professionnel et technique
6. Ne traduis PAS les noms de services (Gmail, OpenAI, etc.)

Texte à traduire :
{text}

Traduction française :"""

    try:
        response = requests.post(
            'https://api.openai.com/v1/chat/completions',
            headers={'Authorization': f'Bearer {OPENAI_API_KEY}'},
            json={
                'model': 'gpt-4o-mini',
                'messages': [{'role': 'user', 'content': prompt}],
                'max_tokens': len(text) * 2,
                'temperature': 0.3
            },
            timeout=30
        )
        
        if response.status_code == 200:
            result = response.json()['choices'][0]['message']['content'].strip()
            return result
        else:
            print(f"❌ Erreur OpenAI: {response.status_code}")
            return text
            
    except Exception as e:
        print(f"❌ Erreur traduction: {e}")
        return text

def translate_workflow_name(name):
    """Traduit le nom du workflow avec des règles spécifiques"""
    translations = {
        "Sumobundle - Telegram Agent": "Sumobundle - Agent Telegram",
        "Sub workflow - Get Google Tasks": "Sub workflow - Récupérer les tâches Google",
        "Business Canvas Generator": "Générateur de Business Canvas",
        "Simple OpenAI Image Generator": "Générateur d'images OpenAI simple",
        "Agent": "Agent",
        "Generator": "Générateur", 
        "Assistant": "Assistant",
        "Bot": "Bot",
        "Workflow": "Workflow",
        "Automation": "Automatisation"
    }
    
    result = name
    for en, fr in translations.items():
        if en in result:
            result = result.replace(en, fr)
    
    return result

def fix_workflow_with_openai(workflow_path):
    """Corrige un workflow avec OpenAI pour les traductions"""
    print(f"\n🔧 Correction OpenAI: {os.path.basename(workflow_path)}")
    
    # Charger le workflow
    with open(workflow_path, 'r', encoding='utf-8') as f:
        workflow = json.load(f)
    
    changes_made = []
    
    # 1. Traduire le nom du workflow
    original_name = workflow.get('name', '')
    if original_name and not any(word in original_name.lower() for word in ['sub', 'workflow', 'récupérer', 'générateur']):
        translated_name = translate_workflow_name(original_name)
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
        
        # Traduire le nom du nœud si nécessaire
        if node_name and 'OpenAI Chat Model' in node_name:
            node['name'] = node_name.replace('OpenAI Chat Model', 'Modèle de Chat OpenAI')
            changes_made.append(f"Nom de nœud traduit: {node_name}")
        
        # Gérer systemMessage dans options
        if 'options' in params and 'systemMessage' in params['options']:
            sys_msg = params['options']['systemMessage']
            if sys_msg and isinstance(sys_msg, str):
                # Vérifier si c'est en anglais et nécessite une traduction
                if 'You are' in sys_msg or 'Your name is' in sys_msg or 'English' in sys_msg:
                    if sys_msg.startswith('='):
                        content_without_equal = sys_msg[1:]
                        translated_content = translate_with_openai(content_without_equal, "system_prompt")
                        params['options']['systemMessage'] = '=' + translated_content
                        changes_made.append(f"SystemMessage du nœud '{node_name}' traduit avec OpenAI")
                        # Pause pour éviter la limite de taux
                        time.sleep(1)
                    else:
                        translated_content = translate_with_openai(sys_msg, "system_prompt")
                        params['options']['systemMessage'] = translated_content
                        changes_made.append(f"SystemMessage du nœud '{node_name}' traduit avec OpenAI")
                        time.sleep(1)
        
        # Gérer les messages dans les nœuds OpenAI
        if 'messages' in params and 'values' in params['messages']:
            for message in params['messages']['values']:
                if 'content' in message and isinstance(message['content'], str):
                    content = message['content']
                    # Si c'est un long prompt en anglais, le traduire
                    if len(content) > 50 and ('You are' in content or 'Please' in content or 'Create' in content):
                        if content.startswith('='):
                            content_without_equal = content[1:]
                            translated_content = translate_with_openai(content_without_equal, "prompt")
                            message['content'] = '=' + translated_content
                            changes_made.append(f"Message OpenAI du nœud '{node_name}' traduit")
                            time.sleep(1)
                        else:
                            translated_content = translate_with_openai(content, "prompt")
                            message['content'] = translated_content
                            changes_made.append(f"Message OpenAI du nœud '{node_name}' traduit")
                            time.sleep(1)
    
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

def fix_automationtribe_workflows():
    """Corrige tous les workflows AutomationTribe avec OpenAI"""
    print("🚀 CORRECTION OPENAI DES WORKFLOWS AUTOMATIONTRIBE")
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
                    if fix_workflow_with_openai(workflow_path):
                        workflows_fixed += 1
                except Exception as e:
                    print(f"❌ Erreur avec {file}: {e}")
    
    print(f"\n🎉 RÉSUMÉ:")
    print(f"   Workflows traités: {total_workflows}")
    print(f"   Workflows corrigés: {workflows_fixed}")
    print(f"   Taux de correction: {workflows_fixed/total_workflows*100:.1f}%")

if __name__ == "__main__":
    if not OPENAI_API_KEY:
        print("❌ Clé OpenAI non trouvée. Veuillez configurer OPENAI_API_KEY dans .env")
        exit(1)
    
    fix_automationtribe_workflows()