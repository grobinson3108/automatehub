#!/usr/bin/env python3
import os
import json
import shutil
import re
from collections import defaultdict

def analyze_workflow_content(file_path):
    """Analyser le contenu d'un workflow pour en extraire les métadonnées"""
    try:
        with open(file_path, 'r') as f:
            data = json.load(f)
            
        # Extraire les informations
        node_count = len(data.get('nodes', []))
        node_types = set()
        integrations = set()
        
        for node in data.get('nodes', []):
            node_type = node.get('type', '')
            node_types.add(node_type)
            
            # Détecter les intégrations
            if 'openai' in node_type.lower():
                integrations.add('OpenAI')
            elif 'telegram' in node_type.lower():
                integrations.add('Telegram')
            elif 'slack' in node_type.lower():
                integrations.add('Slack')
            elif 'google' in node_type.lower():
                integrations.add('Google')
            elif 'webhook' in node_type.lower():
                integrations.add('Webhook')
            elif 'email' in node_type.lower():
                integrations.add('Email')
            elif 'database' in node_type.lower() or 'mysql' in node_type.lower():
                integrations.add('Database')
                
        # Déterminer la complexité
        if node_count < 10:
            complexity = 'simple'
        elif node_count < 25:
            complexity = 'intermediate'
        else:
            complexity = 'complex'
            
        return {
            'node_count': node_count,
            'complexity': complexity,
            'integrations': list(integrations),
            'has_ai': any('ai' in nt.lower() or 'openai' in nt.lower() or 'gpt' in nt.lower() for nt in node_types)
        }
    except:
        return None

def categorize_workflow(filename, metadata=None):
    """Catégoriser un workflow basé sur son nom et contenu"""
    name_lower = filename.lower()
    
    # Catégories principales
    if any(word in name_lower for word in ['ai', 'gpt', 'openai', 'claude', 'gemini', 'llm', 'agent']):
        primary = 'ai_automation'
    elif any(word in name_lower for word in ['scrape', 'scraping', 'extract', 'crawl']):
        primary = 'data_scraping'
    elif any(word in name_lower for word in ['email', 'gmail', 'outlook', 'mail']):
        primary = 'email_automation'
    elif any(word in name_lower for word in ['slack', 'telegram', 'discord', 'whatsapp', 'chat']):
        primary = 'messaging'
    elif any(word in name_lower for word in ['sheets', 'excel', 'airtable', 'database', 'mysql']):
        primary = 'data_management'
    elif any(word in name_lower for word in ['wordpress', 'blog', 'content', 'seo']):
        primary = 'content_creation'
    elif any(word in name_lower for word in ['ecommerce', 'shopify', 'woocommerce', 'order']):
        primary = 'ecommerce'
    elif any(word in name_lower for word in ['social', 'twitter', 'linkedin', 'instagram', 'facebook']):
        primary = 'social_media'
    elif any(word in name_lower for word in ['crm', 'hubspot', 'salesforce', 'pipedrive']):
        primary = 'crm_sales'
    else:
        primary = 'general_automation'
    
    # Déterminer si premium
    is_premium = False
    if metadata:
        if metadata['node_count'] > 25 or metadata['has_ai']:
            is_premium = True
    
    return primary, is_premium

# Créer la structure de dossiers
base_dir = '/var/www/automatehub/Ventes_Workflows'
os.makedirs(base_dir, exist_ok=True)

# Packs à créer
packs = {
    'Pack_Decouverte_Gratuit': {
        'description': 'Les essentiels pour débuter avec n8n',
        'target_count': 10,
        'categories': ['general_automation', 'email_automation'],
        'complexity': ['simple'],
        'workflows': []
    },
    'Pack_Starter_Email_Social': {
        'description': 'Automatisations email et réseaux sociaux',
        'target_count': 20,
        'categories': ['email_automation', 'social_media', 'messaging'],
        'complexity': ['simple', 'intermediate'],
        'workflows': []
    },
    'Pack_Business_Data_Management': {
        'description': 'Gestion de données et intégrations business',
        'target_count': 30,
        'categories': ['data_management', 'crm_sales', 'ecommerce'],
        'complexity': ['intermediate'],
        'workflows': []
    },
    'Pack_Pro_AI_Automation': {
        'description': 'Automatisations IA avancées',
        'target_count': 50,
        'categories': ['ai_automation'],
        'complexity': ['intermediate', 'complex'],
        'workflows': []
    },
    'Pack_Expert_Scraping_API': {
        'description': 'Scraping et intégrations API complexes',
        'target_count': 30,
        'categories': ['data_scraping', 'api_integration'],
        'complexity': ['complex'],
        'workflows': []
    },
    'Pack_Agency_Complete': {
        'description': 'Pack complet pour agences digitales',
        'target_count': 100,
        'categories': 'all',
        'complexity': ['intermediate', 'complex'],
        'workflows': []
    },
    'Pack_Ultimate_Collection': {
        'description': 'Collection ultime - Tous les meilleurs workflows',
        'target_count': 200,
        'categories': 'all',
        'complexity': 'all',
        'workflows': []
    }
}

# Analyser tous les workflows disponibles
all_workflows = []
workflow_metadata = {}

# Parcourir les workflows existants
for root, dirs, files in os.walk('/var/www/automatehub/200_automations_n8n'):
    for file in files:
        if file.endswith('.json'):
            filepath = os.path.join(root, file)
            metadata = analyze_workflow_content(filepath)
            if metadata:
                category, is_premium = categorize_workflow(file, metadata)
                all_workflows.append({
                    'path': filepath,
                    'filename': file,
                    'category': category,
                    'metadata': metadata,
                    'is_premium': is_premium
                })

# Parcourir les workflows GitHub
for root, dirs, files in os.walk('/var/www/automatehub/github_workflows'):
    for file in files:
        if file.endswith('.json'):
            filepath = os.path.join(root, file)
            metadata = analyze_workflow_content(filepath)
            if metadata:
                category, is_premium = categorize_workflow(file, metadata)
                all_workflows.append({
                    'path': filepath,
                    'filename': file,
                    'category': category,
                    'metadata': metadata,
                    'is_premium': is_premium,
                    'source': 'github'
                })

# Trier les workflows par complexité et catégorie
all_workflows.sort(key=lambda x: (x['metadata']['node_count'], x['category']))

# Distribuer dans les packs
for workflow in all_workflows:
    for pack_name, pack_info in packs.items():
        # Vérifier si le workflow correspond aux critères du pack
        if len(pack_info['workflows']) >= pack_info['target_count']:
            continue
            
        category_match = pack_info['categories'] == 'all' or workflow['category'] in pack_info['categories']
        complexity_match = pack_info['complexity'] == 'all' or workflow['metadata']['complexity'] in pack_info['complexity']
        
        if category_match and complexity_match:
            pack_info['workflows'].append(workflow)
            
# Créer les dossiers et copier les workflows
for pack_name, pack_info in packs.items():
    pack_dir = os.path.join(base_dir, pack_name)
    os.makedirs(pack_dir, exist_ok=True)
    
    # Créer un README pour le pack
    with open(os.path.join(pack_dir, 'README.md'), 'w') as f:
        f.write(f"# {pack_name.replace('_', ' ')}\n\n")
        f.write(f"{pack_info['description']}\n\n")
        f.write(f"## Contenu du pack\n\n")
        f.write(f"- **Nombre de workflows**: {len(pack_info['workflows'])}\n")
        f.write(f"- **Catégories**: {', '.join(set(w['category'] for w in pack_info['workflows']))}\n")
        f.write(f"- **Complexité**: {', '.join(set(w['metadata']['complexity'] for w in pack_info['workflows']))}\n\n")
        f.write("## Liste des workflows\n\n")
        
        for i, workflow in enumerate(pack_info['workflows']):
            # Copier le workflow
            new_filename = f"{i+1:03d}_{workflow['filename']}"
            shutil.copy2(workflow['path'], os.path.join(pack_dir, new_filename))
            
            # Ajouter au README
            f.write(f"{i+1}. **{workflow['filename']}**\n")
            f.write(f"   - Nodes: {workflow['metadata']['node_count']}\n")
            f.write(f"   - Intégrations: {', '.join(workflow['metadata']['integrations'])}\n")
            f.write(f"   - IA: {'Oui' if workflow['metadata']['has_ai'] else 'Non'}\n\n")

print("\n=== RAPPORT DE CRÉATION DES PACKS ===")
for pack_name, pack_info in packs.items():
    print(f"\n{pack_name}:")
    print(f"  - Description: {pack_info['description']}")
    print(f"  - Workflows: {len(pack_info['workflows'])}/{pack_info['target_count']}")
    print(f"  - Dossier: {os.path.join(base_dir, pack_name)}")

print(f"\nTotal workflows organisés: {sum(len(p['workflows']) for p in packs.values())}")
print(f"Total workflows disponibles: {len(all_workflows)}")