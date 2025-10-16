#!/usr/bin/env python3
"""
Test simple pour voir ce qui se passe avec la traduction
"""

import json
import os

# Lire le mapping
mapping_file = "/var/www/automatehub/translation_mappings_smart/ONE_CLICK___N8N_Video_Shorts.json.mapping.json"
with open(mapping_file, 'r', encoding='utf-8') as f:
    mapping_data = json.load(f)

texts_map = mapping_data['texts_map']

print("🔍 ANALYSE DES TEXTES DÉTECTÉS")
print("=" * 50)

# Chercher les textes API
for placeholder, info in texts_map.items():
    original = info['original']
    if "ENTER" in original and "API" in original:
        print(f"\n📋 {placeholder}")
        print(f"   Texte: {original}")
        print(f"   Chemin: {info['path']}")
        print(f"   Contexte: {info['context']}")

# Chercher les problèmes potentiels
print(f"\n\n📊 STATISTIQUES")
print(f"Total textes détectés: {len(texts_map)}")

# Analyser les contextes
contexts = {}
for info in texts_map.values():
    context = info['context']
    contexts[context] = contexts.get(context, 0) + 1

print(f"\nRépartition par contexte:")
for context, count in contexts.items():
    print(f"  - {context}: {count}")

# Analyser les types de textes
problematic_texts = []
for placeholder, info in texts_map.items():
    original = info['original']
    if len(original) < 10 and original.lower() in ['start', 'done', 'and', 'or']:
        problematic_texts.append((placeholder, original))

if problematic_texts:
    print(f"\n⚠️  TEXTES POTENTIELLEMENT TECHNIQUES:")
    for placeholder, text in problematic_texts[:5]:
        print(f"  - {placeholder}: '{text}'")