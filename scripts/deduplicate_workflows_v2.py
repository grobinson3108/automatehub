#!/usr/bin/env python3
import os
import json
import re
from collections import defaultdict
from difflib import SequenceMatcher
import hashlib

def extract_workflow_purpose(filepath):
    """Extraire le purpose d'un workflow en analysant son contenu et nom"""
    try:
        with open(filepath, 'r') as f:
            data = json.load(f)
        
        # Analyser le nom du fichier
        filename = os.path.basename(filepath).lower()
        
        # Analyser les nodes pour comprendre le workflow
        node_types = []
        integrations = set()
        actions = []
        
        for node in data.get('nodes', []):
            node_type = node.get('type', '').lower()
            node_types.append(node_type)
            
            # Identifier les intégrations
            integration_patterns = {
                'gmail': ['gmail', 'google mail'],
                'sheets': ['google sheets', 'spreadsheet'],
                'slack': ['slack'],
                'telegram': ['telegram'],
                'openai': ['openai', 'gpt', 'chatgpt'],
                'wordpress': ['wordpress'],
                'shopify': ['shopify'],
                'airtable': ['airtable'],
                'notion': ['notion'],
                'webhook': ['webhook'],
                'http': ['httprequest', 'http request'],
                'database': ['mysql', 'postgres', 'mongodb', 'redis'],
                'email': ['emailsend', 'smtp', 'imap']
            }
            
            for integration, patterns in integration_patterns.items():
                if any(pattern in node_type for pattern in patterns):
                    integrations.add(integration)
            
            # Identifier les actions principales
            if 'trigger' in node_type:
                actions.append('trigger')
            elif 'create' in node_type or 'add' in node_type:
                actions.append('create')
            elif 'update' in node_type or 'edit' in node_type:
                actions.append('update')
            elif 'get' in node_type or 'read' in node_type or 'fetch' in node_type:
                actions.append('read')
            elif 'delete' in node_type or 'remove' in node_type:
                actions.append('delete')
            elif 'send' in node_type or 'email' in node_type:
                actions.append('send')
            elif 'process' in node_type or 'transform' in node_type:
                actions.append('process')
        
        # Créer une signature unique du workflow
        purpose = {
            'main_integrations': list(integrations)[:3],  # Top 3 integrations
            'main_actions': list(set(actions))[:3],       # Top 3 actions
            'node_count': len(data.get('nodes', [])),
            'complexity': 'simple' if len(data.get('nodes', [])) < 10 else 'medium' if len(data.get('nodes', [])) < 25 else 'complex'
        }
        
        # Créer une clé de purpose combinée
        purpose_key = '_'.join(sorted(purpose['main_integrations'])) + '_' + '_'.join(sorted(purpose['main_actions']))
        if not purpose_key or purpose_key == '_':
            purpose_key = filename.replace('.json', '')[:30]  # Fallback au nom du fichier
            
        return purpose_key, purpose
        
    except Exception as e:
        return filename.replace('.json', ''), {'error': str(e)}

def calculate_workflow_score(filepath):
    """Calculer un score de qualité pour un workflow"""
    try:
        with open(filepath, 'r') as f:
            data = json.load(f)
        
        score = 0
        
        # Points pour le nombre de nodes (optimal entre 5 et 30)
        node_count = len(data.get('nodes', []))
        if 5 <= node_count <= 15:
            score += 30
        elif 15 < node_count <= 30:
            score += 25
        elif node_count > 30:
            score += 20
        else:
            score += 10
            
        # Points pour la documentation
        has_notes = any(node.get('notes', '').strip() for node in data.get('nodes', []))
        if has_notes:
            score += 25
            
        # Points pour la diversité des nodes
        node_types = set(node.get('type', '') for node in data.get('nodes', []))
        score += min(len(node_types) * 5, 20)
        
        # Points pour les connections (workflow bien structuré)
        connections = data.get('connections', {})
        if connections:
            score += 15
            
        # Points pour l'utilisation de fonctionnalités avancées
        advanced_features = ['If', 'Switch', 'Loop', 'ErrorTrigger', 'Wait', 'Code']
        if any(feature in str(node_types) for feature in advanced_features):
            score += 10
            
        return score
        
    except:
        return 0

print("🔍 Analyse des workflows pour déduplication intelligente...")

# Collecter tous les workflows
all_workflows = []
for root, dirs, files in os.walk('/var/www/automatehub/200_automations_n8n'):
    for file in files:
        if file.endswith('.json'):
            all_workflows.append(os.path.join(root, file))

for root, dirs, files in os.walk('/var/www/automatehub/github_workflows'):
    for file in files:
        if file.endswith('.json'):
            all_workflows.append(os.path.join(root, file))

print(f"📊 Total workflows trouvés: {len(all_workflows)}")

# Grouper par purpose
purpose_groups = defaultdict(list)
for filepath in all_workflows:
    purpose_key, purpose_details = extract_workflow_purpose(filepath)
    purpose_groups[purpose_key].append({
        'path': filepath,
        'filename': os.path.basename(filepath),
        'purpose': purpose_details,
        'score': calculate_workflow_score(filepath)
    })

# Identifier et traiter les doublons
kept_workflows = []
duplicates_removed = 0
duplicate_details = []

for purpose_key, workflows in purpose_groups.items():
    if len(workflows) == 1:
        kept_workflows.append(workflows[0])
    else:
        # Trier par score (meilleur en premier)
        workflows.sort(key=lambda x: x['score'], reverse=True)
        
        # Garder le meilleur
        best = workflows[0]
        kept_workflows.append(best)
        
        # Compter les doublons
        duplicates = workflows[1:]
        duplicates_removed += len(duplicates)
        
        # Sauvegarder les détails pour le rapport
        if len(duplicates) > 0:
            duplicate_details.append({
                'purpose': purpose_key,
                'kept': {
                    'filename': best['filename'],
                    'score': best['score']
                },
                'removed': [{'filename': d['filename'], 'score': d['score']} for d in duplicates]
            })

print(f"\n✅ Workflows uniques conservés: {len(kept_workflows)}")
print(f"🗑️  Doublons supprimés: {duplicates_removed}")
print(f"📈 Taux de déduplication: {duplicates_removed/len(all_workflows)*100:.1f}%")

# Analyser les workflows conservés par catégorie
categories = defaultdict(list)
for wf in kept_workflows:
    filename = wf['filename'].lower()
    
    # Marketing & Social Media
    if any(kw in filename for kw in ['social', 'marketing', 'campaign', 'seo', 'content', 'blog', 
                                      'instagram', 'facebook', 'twitter', 'linkedin', 'youtube', 'tiktok']):
        categories['marketing'].append(wf)
    # Sales & CRM
    elif any(kw in filename for kw in ['crm', 'lead', 'sales', 'pipedrive', 'hubspot', 'salesforce', 
                                        'deal', 'customer', 'prospect']):
        categories['sales'].append(wf)
    # HR & Recruitment
    elif any(kw in filename for kw in ['hr', 'recruitment', 'employee', 'onboarding', 'candidate', 
                                        'resume', 'interview', 'bamboohr']):
        categories['hr'].append(wf)
    # Finance & Accounting
    elif any(kw in filename for kw in ['invoice', 'payment', 'accounting', 'quickbooks', 'expense', 
                                        'billing', 'stripe', 'paypal']):
        categories['finance'].append(wf)
    # IT & DevOps
    elif any(kw in filename for kw in ['backup', 'monitoring', 'server', 'database', 'docker', 
                                        'git', 'deploy', 'security', 'ssl']):
        categories['it_ops'].append(wf)
    # Customer Service
    elif any(kw in filename for kw in ['support', 'ticket', 'zendesk', 'helpdesk', 'chat', 'feedback']):
        categories['customer_service'].append(wf)
    # Data & Analytics
    elif any(kw in filename for kw in ['analytics', 'report', 'dashboard', 'data', 'bigquery', 
                                        'visualization', 'metrics']):
        categories['data_analytics'].append(wf)
    # E-commerce
    elif any(kw in filename for kw in ['shopify', 'woocommerce', 'order', 'product', 'inventory', 
                                        'cart', 'checkout']):
        categories['ecommerce'].append(wf)
    # AI & Automation
    elif any(kw in filename for kw in ['ai', 'gpt', 'openai', 'claude', 'gemini', 'llm', 'agent', 
                                        'rag', 'chatbot']):
        categories['ai_automation'].append(wf)
    # Productivity
    elif any(kw in filename for kw in ['task', 'todo', 'calendar', 'meeting', 'notion', 'asana', 
                                        'trello', 'productivity']):
        categories['productivity'].append(wf)
    # Communication
    elif any(kw in filename for kw in ['email', 'gmail', 'outlook', 'slack', 'teams', 'discord', 
                                        'telegram', 'whatsapp']):
        categories['communication'].append(wf)
    # General
    else:
        categories['general'].append(wf)

# Afficher les statistiques par catégorie
print("\n📊 Répartition par catégorie:")
total_categorized = 0
for cat, workflows in sorted(categories.items(), key=lambda x: len(x[1]), reverse=True):
    count = len(workflows)
    total_categorized += count
    print(f"  {cat.ljust(20)}: {count:4d} workflows")

# Sauvegarder le rapport de déduplication
report = {
    'total_before': len(all_workflows),
    'total_after': len(kept_workflows),
    'duplicates_removed': duplicates_removed,
    'deduplication_rate': f"{duplicates_removed/len(all_workflows)*100:.1f}%",
    'categories': {cat: len(workflows) for cat, workflows in categories.items()},
    'duplicate_details': duplicate_details[:20]  # Top 20 pour ne pas surcharger
}

with open('/var/www/automatehub/deduplication_report.json', 'w') as f:
    json.dump(report, f, indent=2)

# Créer la nouvelle structure de workflows dédupliqués
deduplicated_dir = '/var/www/automatehub/workflows_deduplicated'
os.makedirs(deduplicated_dir, exist_ok=True)

# Copier les workflows par catégorie
import shutil
for cat, workflows in categories.items():
    cat_dir = os.path.join(deduplicated_dir, cat)
    os.makedirs(cat_dir, exist_ok=True)
    
    for i, wf in enumerate(workflows):
        src = wf['path']
        dst = os.path.join(cat_dir, f"{i+1:04d}_{wf['filename']}")
        shutil.copy2(src, dst)

print(f"\n✅ Workflows dédupliqués copiés dans: {deduplicated_dir}")
print(f"📁 Organisés en {len(categories)} catégories")