#!/usr/bin/env python3
"""Debug de la traduction Sumobundle"""

import json
import os

def analyze_workflow(workflow_path, label):
    print(f"\n{'=' * 60}")
    print(f"ANALYSE: {label}")
    print(f"Fichier: {os.path.basename(workflow_path)}")
    print('=' * 60)
    
    with open(workflow_path, 'r', encoding='utf-8') as f:
        workflow = json.load(f)
    
    print(f"Nom du workflow: {workflow['name']}")
    print(f"Tags: {workflow.get('tags', [])}")
    
    # Chercher le nœud AI Agent
    ai_agent_found = False
    for node in workflow['nodes']:
        if 'Agent' in node.get('name', ''):
            ai_agent_found = True
            print(f"\nNœud trouvé: {node['name']}")
            params = node.get('parameters', {})
            
            # Vérifier text
            if 'text' in params:
                print(f"  - text: {params['text']}")
            
            # Vérifier options.systemMessage
            if 'options' in params and 'systemMessage' in params['options']:
                sys_msg = params['options']['systemMessage']
                print(f"  - systemMessage longueur: {len(sys_msg)} caractères")
                print(f"  - Commence par '=': {sys_msg.startswith('=')}")
                print(f"  - Début du message: {sys_msg[:200]}...")
                
                # Analyser le contenu
                if "You are an intelligent" in sys_msg:
                    print("  ❌ Message système en ANGLAIS")
                elif "Tu es un assistant intelligent" in sys_msg or "Vous êtes un assistant" in sys_msg:
                    print("  ✅ Message système en FRANÇAIS")
    
    if not ai_agent_found:
        print("\n⚠️ Aucun nœud AI Agent trouvé!")

# Analyser le workflow original
original = '/var/www/automatehub/AutomationTribe/2-Tout automatiser/N8N Ai Agent/Main workflow/Sumobundle___Telegram_Agent.json'
translated = '/var/www/automatehub/workflows_traduits/FR/AutomationTribe/2-Tout automatiser/N8N Ai Agent/Main workflow/sumobundle_telegram_agent.json'

analyze_workflow(original, "WORKFLOW ORIGINAL")
analyze_workflow(translated, "WORKFLOW TRADUIT")

# Vérifier le script de traduction
print(f"\n{'=' * 60}")
print("VÉRIFICATION DU SCRIPT DE TRADUCTION")
print('=' * 60)

script_path = '/var/www/automatehub/scripts/translate_workflow_complete.py'
with open(script_path, 'r', encoding='utf-8') as f:
    content = f.read()
    
# Chercher la gestion des messages système
if "'systemMessage'" in content:
    print("✅ Le script gère 'systemMessage'")
    # Trouver les lignes pertinentes
    lines = content.split('\n')
    for i, line in enumerate(lines):
        if 'systemMessage' in line:
            print(f"  Ligne {i+1}: {line.strip()}")
else:
    print("❌ Le script NE GÈRE PAS 'systemMessage'")

# Chercher la gestion des options
if "'options'" in content and "node['parameters']['options']" in content:
    print("\n✅ Le script gère les options dans parameters")
else:
    print("\n❌ Le script NE GÈRE PAS les options dans parameters")