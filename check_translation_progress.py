#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Vérifier la progression de la traduction
"""

import os
import json

# Compter les mappings créés
mapping_dir = "/var/www/automatehub/translation_mappings"
mappings = [f for f in os.listdir(mapping_dir) if f.endswith('.mapping.json')]

# Compter les workflows totaux
workflows_dir = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"
total_workflows = 0
for root, dirs, files in os.walk(workflows_dir):
    for file in files:
        if file.endswith('.json'):
            total_workflows += 1

print(f"📊 PROGRESSION DE LA TRADUCTION")
print(f"=" * 50)
print(f"📁 Workflows totaux : {total_workflows}")
print(f"✅ Workflows traduits : {len(mappings)}")
print(f"📈 Progression : {len(mappings)/total_workflows*100:.1f}%")
print(f"⏳ Restants : {total_workflows - len(mappings)}")

# Afficher les derniers fichiers traduits
print(f"\n📝 Derniers workflows traduits :")
sorted_mappings = sorted(mappings, 
                        key=lambda x: os.path.getmtime(os.path.join(mapping_dir, x)), 
                        reverse=True)[:5]
for mapping in sorted_mappings:
    workflow_name = mapping.replace('.mapping.json', '')
    print(f"  - {workflow_name}")

# Vérifier un workflow traduit
if mappings:
    sample_mapping_path = os.path.join(mapping_dir, mappings[-1])
    with open(sample_mapping_path, 'r', encoding='utf-8') as f:
        mapping_data = json.load(f)
    
    print(f"\n🔍 Exemple de traduction ({mappings[-1]}):")
    texts = mapping_data.get('texts_map', {})
    if texts:
        # Afficher jusqu'à 3 exemples
        for i, (placeholder, info) in enumerate(list(texts.items())[:3]):
            print(f"  {placeholder}: {info['original'][:50]}...")