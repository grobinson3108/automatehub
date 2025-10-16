#!/usr/bin/env python3
import os
import json
import re
from collections import defaultdict

def analyze_workflows(directory):
    """Analyse tous les workflows dans le dossier"""
    stats = {
        'total': 0,
        'by_category': defaultdict(int),
        'by_complexity': defaultdict(int),
        'by_node_count': defaultdict(int),
        'by_integrations': defaultdict(int),
        'ai_workflows': 0,
        'premium_candidates': []
    }
    
    # Parcourir tous les fichiers JSON
    for root, dirs, files in os.walk(directory):
        category = os.path.basename(root)
        for file in files:
            if file.endswith('.json'):
                stats['total'] += 1
                stats['by_category'][category] += 1
                
                # Analyser le nom du fichier
                if 'complex' in file:
                    # Extraire le nombre de nodes
                    match = re.search(r'(\d+)nodes', file)
                    if match:
                        node_count = int(match.group(1))
                        if node_count < 10:
                            stats['by_complexity']['simple'] += 1
                        elif node_count < 25:
                            stats['by_complexity']['intermediate'] += 1
                        else:
                            stats['by_complexity']['complex'] += 1
                        stats['by_node_count'][f'{(node_count // 10) * 10}-{(node_count // 10 + 1) * 10} nodes'] += 1
                
                # Détecter workflows IA
                if any(keyword in file.lower() for keyword in ['ai', 'gpt', 'openai', 'claude', 'gemini', 'llm']):
                    stats['ai_workflows'] += 1
                
                # Identifier candidats premium
                if any(premium in file.lower() for premium in ['rag', 'agent', 'automation', 'scraper', 'api']):
                    stats['premium_candidates'].append(file)
    
    return stats

# Analyser les workflows
base_dir = '/var/www/automatehub/200_automations_n8n'
results = analyze_workflows(base_dir)

# Afficher les résultats
print(f"\n=== ANALYSE DES WORKFLOWS ===")
print(f"Total workflows: {results['total']}")

print(f"\n--- Par catégorie ---")
for cat, count in sorted(results['by_category'].items(), key=lambda x: x[1], reverse=True):
    if cat != '200_automations_n8n':
        print(f"{cat}: {count}")

print(f"\n--- Par complexité ---")
for complexity, count in results['by_complexity'].items():
    print(f"{complexity}: {count}")

print(f"\n--- Distribution nodes ---")
for nodes, count in sorted(results['by_node_count'].items()):
    print(f"{nodes}: {count}")

print(f"\n--- Workflows IA ---")
print(f"Total IA: {results['ai_workflows']}")
print(f"Premium candidats: {len(results['premium_candidates'])}")

# Sauvegarder le rapport
with open('/var/www/automatehub/workflow_analysis.json', 'w') as f:
    json.dump(results, f, indent=2)