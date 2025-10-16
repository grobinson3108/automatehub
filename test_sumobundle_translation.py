#!/usr/bin/env python3
"""Test de traduction du workflow Sumobundle pour identifier le problème"""

import json
import sys
sys.path.append('/var/www/automatehub/scripts')
from workflow_translator import extract_texts, apply_translations

def test_sumobundle_translation():
    # Charger le workflow original
    with open('/var/www/automatehub/AutomationTribe/2-Tout automatiser/N8N Ai Agent/Main workflow/Sumobundle___Telegram_Agent.json', 'r', encoding='utf-8') as f:
        workflow = json.load(f)
    
    print("ANALYSE DU WORKFLOW SUMOBUNDLE")
    print("=" * 50)
    print(f"Nom original: {workflow['name']}")
    
    # Extraire les textes
    texts = extract_texts.extract_translatable_texts(workflow)
    print(f"\nTextes extraits: {len(texts)} éléments")
    
    # Afficher les textes extraits
    print("\nDÉTAILS DES TEXTES EXTRAITS:")
    print("-" * 50)
    for i, (path, text) in enumerate(texts.items()):
        preview = text[:100] + "..." if len(text) > 100 else text
        print(f"\n{i+1}. {path}")
        print(f"   Texte: {preview}")
    
    # Charger le workflow traduit pour comparaison
    try:
        with open('/var/www/automatehub/workflows_traduits/FR/AutomationTribe/2-Tout automatiser/N8N Ai Agent/Main workflow/sumobundle_telegram_agent.json', 'r', encoding='utf-8') as f:
            translated = json.load(f)
        
        print("\n" + "=" * 50)
        print("COMPARAISON AVEC LA VERSION TRADUITE")
        print("-" * 50)
        print(f"Nom traduit: {translated['name']}")
        print(f"Tag Audelalia présent: {'Audelalia' in str(translated.get('tags', []))}")
        
        # Vérifier le prompt du AI Agent
        for node in translated['nodes']:
            if node.get('name') == 'AI Agent' or node.get('name') == 'Agent IA':
                print("\nNœud AI Agent trouvé:")
                if 'text' in node.get('parameters', {}):
                    print(f"  Paramètre 'text': {node['parameters']['text']}")
                if 'options' in node.get('parameters', {}):
                    if 'systemMessage' in node['parameters']['options']:
                        sys_msg = node['parameters']['options']['systemMessage']
                        print(f"  Message système: {sys_msg[:100]}...")
                        if sys_msg.startswith('='):
                            print("  ⚠️  Le message système commence par '=' - il devrait être traduit!")
    except Exception as e:
        print(f"\nErreur lors du chargement du fichier traduit: {e}")

if __name__ == "__main__":
    test_sumobundle_translation()