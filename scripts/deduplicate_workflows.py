#!/usr/bin/env python3
import os
import json
import re
from collections import defaultdict
from difflib import SequenceMatcher
import hashlib

def extract_purpose_keywords(filename):
    """Extraire les mots-clés qui indiquent le purpose du workflow"""
    # Nettoyer le nom
    name = filename.lower().replace('.json', '').replace('_', ' ').replace('-', ' ')
    
    # Mots à ignorer
    stop_words = {'the', 'a', 'an', 'and', 'or', 'with', 'for', 'to', 'from', 'in', 'on', 'using', 'via', 'by'}
    
    # Extraire les mots significatifs
    words = [w for w in name.split() if w not in stop_words and len(w) > 2]
    
    # Identifier les actions principales
    action_verbs = ['create', 'send', 'update', 'get', 'fetch', 'analyze', 'process', 
                    'convert', 'generate', 'sync', 'backup', 'scrape', 'monitor', 
                    'schedule', 'automate', 'extract', 'transform', 'notify']
    
    # Identifier les intégrations
    integrations = ['gmail', 'slack', 'telegram', 'wordpress', 'shopify', 'google',
                   'sheets', 'drive', 'airtable', 'notion', 'hubspot', 'mailchimp',
                   'twitter', 'linkedin', 'facebook', 'instagram', 'whatsapp']
    
    # Créer une signature de purpose
    actions = [w for w in words if w in action_verbs]
    tools = [w for w in words if w in integrations]
    
    return {
        'actions': actions,
        'tools': tools,
        'all_words': words,
        'purpose_string': ' '.join(actions + tools)
    }

def analyze_workflow_quality(filepath):
    """Analyser la qualité d'un workflow"""
    try:
        with open(filepath, 'r') as f:
            data = json.load(f)
        
        score = 0
        details = {}
        
        # Nombre de nodes (complexité utile)
        node_count = len(data.get('nodes', []))
        details['node_count'] = node_count
        
        # Présence de documentation
        has_notes = any(node.get('notes') for node in data.get('nodes', []))
        if has_notes:
            score += 20
            details['has_documentation'] = True
            
        # Gestion d'erreurs
        has_error_handling = any('error' in str(node).lower() for node in data.get('nodes', []))
        if has_error_handling:
            score += 15
            details['has_error_handling'] = True
            
        # Utilisation d'IA
        has_ai = any('openai' in str(node).lower() or 'gpt' in str(node).lower() 
                     for node in data.get('nodes', []))
        if has_ai:
            score += 10
            details['has_ai'] = True
            
        # Complexité appropriée (ni trop simple, ni trop complexe)
        if 5 <= node_count <= 30:
            score += 15
        elif 30 < node_count <= 50:
            score += 10
        elif node_count > 50:
            score += 5
            
        # Variété des nodes (pas juste des webhooks)
        node_types = set(node.get('type', '') for node in data.get('nodes', []))
        if len(node_types) >= 3:
            score += 10
            
        # Workflow actif/testé récemment
        if data.get('active', False):
            score += 10
            
        # Structure claire (connections bien définies)
        connections = data.get('connections', {})
        if connections and len(connections) >= node_count - 1:
            score += 10
            
        # Paramètres configurables
        has_parameters = any(node.get('parameters') for node in data.get('nodes', []))
        if has_parameters:
            score += 10
            
        details['quality_score'] = score
        return score, details
        
    except Exception as e:
        return 0, {'error': str(e)}

def find_similar_workflows(all_workflows):
    """Grouper les workflows par purpose similaire"""
    purpose_groups = defaultdict(list)
    
    # Grouper par purpose
    for workflow in all_workflows:
        purpose = extract_purpose_keywords(workflow['filename'])
        workflow['purpose'] = purpose
        
        # Créer une clé de groupement basée sur actions + tools principaux
        if purpose['actions'] and purpose['tools']:
            key = f"{sorted(purpose['actions'])[0]}_{sorted(purpose['tools'])[0]}"
        elif purpose['actions']:
            key = sorted(purpose['actions'])[0]
        elif purpose['tools']:
            key = sorted(purpose['tools'])[0]
        else:
            key = 'general'
            
        purpose_groups[key].append(workflow)
    
    # Analyser chaque groupe pour trouver les vrais doublons
    duplicates_found = 0
    kept_workflows = []
    duplicate_report = []
    
    for group_key, workflows in purpose_groups.items():
        if len(workflows) == 1:
            kept_workflows.extend(workflows)
            continue
            
        # Comparer les workflows dans le groupe
        analyzed_workflows = []
        for wf in workflows:
            score, details = analyze_workflow_quality(wf['path'])
            wf['quality_score'] = score
            wf['quality_details'] = details
            analyzed_workflows.append(wf)
        
        # Trier par score de qualité
        analyzed_workflows.sort(key=lambda x: x['quality_score'], reverse=True)
        
        # Garder le meilleur et identifier les doublons
        best = analyzed_workflows[0]
        kept_workflows.append(best)
        
        if len(analyzed_workflows) > 1:
            duplicates = analyzed_workflows[1:]
            duplicates_found += len(duplicates)
            
            duplicate_report.append({
                'group': group_key,
                'kept': best['filename'],
                'kept_score': best['quality_score'],
                'removed': [{'name': d['filename'], 'score': d['quality_score']} for d in duplicates]
            })
    
    return kept_workflows, duplicates_found, duplicate_report

# Charger tous les workflows
print("🔍 Chargement et analyse des workflows...")
all_workflows = []

# Charger workflows existants
for root, dirs, files in os.walk('/var/www/automatehub/200_automations_n8n'):
    for file in files:
        if file.endswith('.json'):
            all_workflows.append({
                'path': os.path.join(root, file),
                'filename': file,
                'source': 'existing'
            })

# Charger workflows GitHub
for root, dirs, files in os.walk('/var/www/automatehub/github_workflows'):
    for file in files:
        if file.endswith('.json'):
            all_workflows.append({
                'path': os.path.join(root, file),
                'filename': file,
                'source': 'github'
            })

print(f"📊 Total workflows avant déduplication: {len(all_workflows)}")

# Trouver et supprimer les doublons
kept_workflows, duplicates_found, duplicate_report = find_similar_workflows(all_workflows)

print(f"\n✅ Workflows conservés: {len(kept_workflows)}")
print(f"🗑️  Doublons supprimés: {duplicates_found}")

# Sauvegarder le rapport de déduplication
with open('/var/www/automatehub/deduplication_report.json', 'w') as f:
    json.dump({
        'total_before': len(all_workflows),
        'total_after': len(kept_workflows),
        'duplicates_removed': duplicates_found,
        'duplicate_groups': duplicate_report
    }, f, indent=2)

# Analyser les workflows conservés pour créer des packs par métier
print("\n📦 Création de packs par secteur/métier...")

# Catégoriser par secteur
sector_workflows = {
    'marketing': {
        'keywords': ['social', 'marketing', 'campaign', 'seo', 'content', 'blog', 'instagram', 
                     'facebook', 'twitter', 'linkedin', 'youtube', 'tiktok'],
        'workflows': []
    },
    'sales': {
        'keywords': ['crm', 'lead', 'sales', 'pipedrive', 'hubspot', 'salesforce', 'deal', 
                     'customer', 'prospect', 'opportunity'],
        'workflows': []
    },
    'hr': {
        'keywords': ['hr', 'recruitment', 'employee', 'onboarding', 'candidate', 'resume', 
                     'interview', 'bamboohr', 'hiring'],
        'workflows': []
    },
    'finance': {
        'keywords': ['invoice', 'payment', 'accounting', 'quickbooks', 'expense', 'billing', 
                     'stripe', 'paypal', 'financial'],
        'workflows': []
    },
    'it_ops': {
        'keywords': ['backup', 'monitoring', 'server', 'database', 'docker', 'git', 'deploy', 
                     'security', 'ssl', 'api'],
        'workflows': []
    },
    'customer_service': {
        'keywords': ['support', 'ticket', 'zendesk', 'helpdesk', 'customer service', 'chat', 
                     'feedback', 'satisfaction'],
        'workflows': []
    },
    'data_analytics': {
        'keywords': ['analytics', 'report', 'dashboard', 'data', 'bigquery', 'sheets', 
                     'visualization', 'metrics', 'kpi'],
        'workflows': []
    },
    'ecommerce': {
        'keywords': ['shopify', 'woocommerce', 'order', 'product', 'inventory', 'cart', 
                     'checkout', 'shipping', 'ecommerce'],
        'workflows': []
    },
    'productivity': {
        'keywords': ['task', 'todo', 'calendar', 'meeting', 'notion', 'asana', 'trello', 
                     'productivity', 'workflow'],
        'workflows': []
    },
    'communication': {
        'keywords': ['email', 'gmail', 'outlook', 'slack', 'teams', 'discord', 'telegram', 
                     'whatsapp', 'sms', 'notification'],
        'workflows': []
    }
}

# Catégoriser les workflows
for workflow in kept_workflows:
    filename_lower = workflow['filename'].lower()
    categorized = False
    
    for sector, config in sector_workflows.items():
        if any(keyword in filename_lower for keyword in config['keywords']):
            sector_workflows[sector]['workflows'].append(workflow)
            categorized = True
            break
    
    # Si non catégorisé, mettre dans général
    if not categorized:
        if 'general' not in sector_workflows:
            sector_workflows['general'] = {'keywords': [], 'workflows': []}
        sector_workflows['general']['workflows'].append(workflow)

# Créer les nouveaux packs
print("\n📁 Création des packs par secteur...")
new_packs = {
    'Pack_Marketing_Digital': {
        'workflows': sector_workflows['marketing']['workflows'][:50],
        'description': 'Automatisations marketing et réseaux sociaux'
    },
    'Pack_Sales_CRM': {
        'workflows': sector_workflows['sales']['workflows'][:40],
        'description': 'Gestion commerciale et CRM'
    },
    'Pack_RH_Recrutement': {
        'workflows': sector_workflows['hr']['workflows'][:30],
        'description': 'Processus RH et recrutement'
    },
    'Pack_Finance_Comptabilite': {
        'workflows': sector_workflows['finance']['workflows'][:30],
        'description': 'Gestion financière et comptable'
    },
    'Pack_IT_DevOps': {
        'workflows': sector_workflows['it_ops']['workflows'][:40],
        'description': 'Outils IT et DevOps'
    },
    'Pack_Service_Client': {
        'workflows': sector_workflows['customer_service']['workflows'][:35],
        'description': 'Support et service client'
    },
    'Pack_Data_Analytics': {
        'workflows': sector_workflows['data_analytics']['workflows'][:45],
        'description': 'Analyse de données et reporting'
    },
    'Pack_Ecommerce_Pro': {
        'workflows': sector_workflows['ecommerce']['workflows'][:40],
        'description': 'Solutions e-commerce complètes'
    },
    'Pack_Productivite': {
        'workflows': sector_workflows['productivity']['workflows'][:30],
        'description': 'Outils de productivité'
    },
    'Pack_Communication': {
        'workflows': sector_workflows['communication']['workflows'][:50],
        'description': 'Communication multi-canal'
    }
}

# Statistiques finales
print("\n📊 RÉSUMÉ FINAL")
print(f"Workflows uniques: {len(kept_workflows)}")
print(f"Doublons supprimés: {duplicates_found}")
print("\nPacks par secteur créés:")
for pack_name, pack_data in new_packs.items():
    print(f"- {pack_name}: {len(pack_data['workflows'])} workflows")

# Sauvegarder la nouvelle organisation
with open('/var/www/automatehub/new_pack_organization.json', 'w') as f:
    pack_summary = {}
    for pack_name, pack_data in new_packs.items():
        pack_summary[pack_name] = {
            'count': len(pack_data['workflows']),
            'description': pack_data['description']
        }
    json.dump(pack_summary, f, indent=2)