#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Corrige manuellement le workflow Stock photos EXIF
"""

import json

workflow_path = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe/3 - 2500€ revenu passif/Download workflow - Stock Photo Metadata/Stock_photos_EXIF.json"

with open(workflow_path, 'r', encoding='utf-8') as f:
    workflow = json.load(f)

# Corriger les noms mal traduits
for node in workflow['nodes']:
    if "OpenAI\n\nTraduction française" in node.get('name', ''):
        node['name'] = 'OpenAI'
    elif node.get('name') == 'Bien sûr, je peux vous aider avec':
        node['name'] = 'Code'
    elif node.get('name') == 'Boucle sur les éléments':
        node['name'] = 'Boucler sur les éléments'

# Sauvegarder
with open(workflow_path, 'w', encoding='utf-8') as f:
    json.dump(workflow, f, indent=2, ensure_ascii=False)

print("✅ Workflow Stock photos EXIF corrigé")