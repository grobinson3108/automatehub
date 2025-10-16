#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour vérifier les traductions effectuées
"""
import json
import os

def verify_workflow_translation(original_path, translated_path):
    """Vérifier la qualité d'une traduction"""
    print(f"\n📋 Vérification: {os.path.basename(original_path)}")
    print(f"   → {os.path.basename(translated_path)}")
    
    if not os.path.exists(translated_path):
        print("   ❌ Fichier traduit non trouvé")
        return False
    
    # Charger les deux workflows
    try:
        with open(original_path, 'r', encoding='utf-8') as f:
            original = json.load(f)
        with open(translated_path, 'r', encoding='utf-8') as f:
            translated = json.load(f)
    except Exception as e:
        print(f"   ❌ Erreur lecture: {str(e)}")
        return False
    
    # Vérifications
    checks = {
        "Nom traduit": original.get('name', '') != translated.get('name', ''),
        "Même nombre de nodes": len(original.get('nodes', [])) == len(translated.get('nodes', [])),
        "Tag Audelalia": any(tag.get('name') == 'Audelalia' for tag in translated.get('tags', [])),
        "Connexions préservées": len(original.get('connections', {})) == len(translated.get('connections', {}))
    }
    
    # Vérifier les traductions des nodes
    nodes_translated = 0
    prompts_translated = 0
    
    for i, (orig_node, trans_node) in enumerate(zip(original.get('nodes', []), translated.get('nodes', []))):
        if orig_node.get('name', '') != trans_node.get('name', ''):
            nodes_translated += 1
        
        # Vérifier les prompts OpenAI
        if orig_node.get('type') in ['@n8n/n8n-nodes-langchain.openAi', 'n8n-nodes-base.openAi']:
            if 'parameters' in orig_node and 'messages' in orig_node['parameters']:
                if 'values' in orig_node['parameters']['messages']:
                    for j, msg in enumerate(orig_node['parameters']['messages']['values']):
                        if 'content' in msg:
                            orig_content = msg['content']
                            if j < len(trans_node.get('parameters', {}).get('messages', {}).get('values', [])):
                                trans_content = trans_node['parameters']['messages']['values'][j].get('content', '')
                                if orig_content != trans_content:
                                    prompts_translated += 1
    
    # Afficher les résultats
    print("\n   📊 Résultats:")
    for check, result in checks.items():
        print(f"   {'✅' if result else '❌'} {check}")
    
    print(f"\n   📈 Statistiques:")
    print(f"   - Nom original: {original.get('name', 'Sans nom')}")
    print(f"   - Nom traduit: {translated.get('name', 'Sans nom')}")
    print(f"   - Nodes traduits: {nodes_translated}/{len(original.get('nodes', []))}")
    if prompts_translated > 0:
        print(f"   - Prompts OpenAI traduits: {prompts_translated}")
    
    # Quelques exemples de traductions
    if nodes_translated > 0:
        print("\n   📝 Exemples de traductions:")
        examples = 0
        for orig_node, trans_node in zip(original.get('nodes', []), translated.get('nodes', [])):
            if orig_node.get('name', '') != trans_node.get('name', '') and examples < 3:
                print(f"      • {orig_node.get('name', '')} → {trans_node.get('name', '')}")
                examples += 1
    
    return all(checks.values())

# Workflows à vérifier
workflows_to_check = [
    {
        "original": "/var/www/automatehub/AutomationTribe/Generate_social_post_ideas_or_summaries.json",
        "translated": "/var/www/automatehub/workflows_traduits/FR/AutomationTribe/generer_social_post_idees_or_resumes.json"
    },
    {
        "original": "/var/www/automatehub/AutomationTribe/5 - Produits Prennent Vie/Life_Style_Product_Photo_Generator.json",
        "translated": "/var/www/automatehub/workflows_traduits/FR/AutomationTribe/life_style_produit_photo_generator.json"
    }
]

print("🔍 VÉRIFICATION DES TRADUCTIONS")
print("="*60)

success = 0
for workflow in workflows_to_check:
    if os.path.exists(workflow['original']) and os.path.exists(workflow['translated']):
        if verify_workflow_translation(workflow['original'], workflow['translated']):
            success += 1

print("\n" + "="*60)
print(f"\n✅ Résultat global: {success}/{len(workflows_to_check)} workflows correctement traduits")

# Vérifier un prompt traduit
print("\n🔍 Vérification d'un prompt OpenAI traduit:")
translated_file = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe/generer_social_post_idees_or_resumes.json"
if os.path.exists(translated_file):
    with open(translated_file, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    for node in data.get('nodes', []):
        if node.get('type') == '@n8n/n8n-nodes-langchain.openAi' and node.get('name') == 'OpenAI':
            if 'parameters' in node and 'messages' in node['parameters']:
                if 'values' in node['parameters']['messages'] and len(node['parameters']['messages']['values']) > 0:
                    content = node['parameters']['messages']['values'][0].get('content', '')
                    if content:
                        # Afficher les 300 premiers caractères
                        if content.startswith('='):
                            content = content[1:]
                        print(f"\nPrompt traduit (extrait):\n{content[:300]}...")
                        break