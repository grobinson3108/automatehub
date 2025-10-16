#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Corriger les traductions partielles détectées
"""

import json
import os
import requests
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

def translate_text_clean(text, api_key):
    """Traduit un texte en s'assurant qu'il est complètement traduit"""
    if not text or len(text.strip()) < 10:
        return text
    
    # Supprimer le marqueur de traduction partielle
    text = text.replace("## TRADUIT PARTIELLEMENT ##\n", "")
    
    prompt = f"""Traduis INTÉGRALEMENT ce texte anglais en français. 
TRÈS IMPORTANT : 
- Traduis TOUT le texte, pas seulement une partie
- Garde TOUTES les variables entre {{{{ }}}} exactement comme elles sont
- Garde tous les formats et structures
- Ne mets AUCUN marqueur comme "## TRADUIT PARTIELLEMENT ##"

Texte à traduire :
{text}

Traduction française COMPLÈTE :"""

    try:
        response = requests.post(
            'https://api.openai.com/v1/chat/completions',
            headers={'Authorization': f'Bearer {api_key}'},
            json={
                'model': 'gpt-4o-mini',
                'messages': [
                    {
                        'role': 'system',
                        'content': 'Tu es un traducteur expert. Tu traduis INTÉGRALEMENT les textes anglais en français sans aucune omission.'
                    },
                    {
                        'role': 'user',
                        'content': prompt
                    }
                ],
                'max_tokens': 4000,
                'temperature': 0.3
            },
            timeout=60
        )
        
        if response.status_code == 200:
            translation = response.json()['choices'][0]['message']['content'].strip()
            return translation
        else:
            print(f"  ❌ Erreur API: {response.status_code}")
            return text
    except Exception as e:
        print(f"  ❌ Erreur: {e}")
        return text

def fix_workflow_translations(workflow_path, api_key):
    """Corriger les traductions partielles dans un workflow"""
    print(f"\n🔧 Correction: {os.path.basename(workflow_path)}")
    
    try:
        with open(workflow_path, 'r', encoding='utf-8') as f:
            workflow = json.load(f)
        
        changes = False
        
        # Parcourir tous les nœuds
        for node in workflow.get('nodes', []):
            params = node.get('parameters', {})
            
            # Vérifier systemMessage
            if 'options' in params and 'systemMessage' in params['options']:
                sys_msg = params['options']['systemMessage']
                if isinstance(sys_msg, str) and "TRADUIT PARTIELLEMENT" in sys_msg:
                    print(f"  🔍 Traduction partielle détectée dans systemMessage")
                    
                    # Enlever le préfixe = si présent
                    has_prefix = sys_msg.startswith('=')
                    actual_msg = sys_msg[1:] if has_prefix else sys_msg
                    
                    # Traduire complètement
                    translated = translate_text_clean(actual_msg, api_key)
                    
                    # Remettre le préfixe si nécessaire
                    params['options']['systemMessage'] = ('=' if has_prefix else '') + translated
                    changes = True
                    print(f"  ✅ SystemMessage corrigé")
                    time.sleep(2)  # Pause pour éviter rate limit
            
            # Vérifier messages content
            if 'messages' in params and 'values' in params['messages']:
                for message in params['messages']['values']:
                    if 'content' in message and isinstance(message['content'], str):
                        content = message['content']
                        if "TRADUIT PARTIELLEMENT" in content:
                            print(f"  🔍 Traduction partielle détectée dans message content")
                            
                            # Enlever le préfixe = si présent
                            has_prefix = content.startswith('=')
                            actual_content = content[1:] if has_prefix else content
                            
                            # Traduire complètement
                            translated = translate_text_clean(actual_content, api_key)
                            
                            # Remettre le préfixe si nécessaire
                            message['content'] = ('=' if has_prefix else '') + translated
                            changes = True
                            print(f"  ✅ Message content corrigé")
                            time.sleep(2)  # Pause pour éviter rate limit
        
        # Sauvegarder si des changements ont été faits
        if changes:
            with open(workflow_path, 'w', encoding='utf-8') as f:
                json.dump(workflow, f, indent=2, ensure_ascii=False)
            print(f"  💾 Workflow sauvegardé avec corrections")
            return True
        else:
            print(f"  ✅ Aucune traduction partielle trouvée")
            return False
            
    except Exception as e:
        print(f"  ❌ Erreur: {e}")
        return False

def main():
    print("🔧 CORRECTION DES TRADUCTIONS PARTIELLES")
    print("=" * 60)
    
    api_key = get_openai_key()
    if not api_key:
        print("❌ Clé OpenAI non trouvée")
        return
    
    print("✅ Clé OpenAI trouvée")
    
    # Chercher les workflows avec traductions partielles
    base_dir = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"
    
    workflows_with_issues = []
    for root, dirs, files in os.walk(base_dir):
        for file in files:
            if file.endswith('.json'):
                filepath = os.path.join(root, file)
                try:
                    with open(filepath, 'r', encoding='utf-8') as f:
                        content = f.read()
                        if "TRADUIT PARTIELLEMENT" in content:
                            workflows_with_issues.append(filepath)
                except:
                    pass
    
    print(f"\n📊 {len(workflows_with_issues)} workflows avec traductions partielles détectés")
    
    if not workflows_with_issues:
        print("✅ Aucune traduction partielle trouvée !")
        return
    
    # Corriger chaque workflow
    fixed_count = 0
    for i, workflow_path in enumerate(workflows_with_issues):
        print(f"\n[{i+1}/{len(workflows_with_issues)}]", end="")
        if fix_workflow_translations(workflow_path, api_key):
            fixed_count += 1
    
    print(f"\n" + "=" * 60)
    print(f"📊 RÉSUMÉ:")
    print(f"  🔍 Workflows analysés: {len(workflows_with_issues)}")
    print(f"  ✅ Workflows corrigés: {fixed_count}")
    print("=" * 60)

if __name__ == "__main__":
    main()