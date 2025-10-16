#!/usr/bin/env python3
import os
import json
import re
from collections import defaultdict
import shutil

def is_valid_json(filepath):
    """Vérifier si le fichier JSON est valide"""
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            json.load(f)
        return True
    except:
        return False

def extract_workflow_info(filepath):
    """Extraire les informations d'un workflow"""
    filename = os.path.basename(filepath).lower()
    
    # Extraire les infos du nom de fichier
    info = {
        'filename': os.path.basename(filepath),
        'path': filepath,
        'valid_json': False,
        'node_count': 0,
        'integrations': set(),
        'purpose': 'unknown',
        'complexity': 'unknown'
    }
    
    # Si le JSON est valide, analyser le contenu
    if is_valid_json(filepath):
        info['valid_json'] = True
        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                data = json.load(f)
            
            # Compter les nodes
            info['node_count'] = len(data.get('nodes', []))
            
            # Déterminer la complexité
            if info['node_count'] < 10:
                info['complexity'] = 'simple'
            elif info['node_count'] < 25:
                info['complexity'] = 'medium'
            else:
                info['complexity'] = 'complex'
            
            # Extraire les intégrations
            for node in data.get('nodes', []):
                node_type = node.get('type', '').lower()
                
                # Détecter les intégrations communes
                if 'gmail' in node_type:
                    info['integrations'].add('gmail')
                elif 'slack' in node_type:
                    info['integrations'].add('slack')
                elif 'telegram' in node_type:
                    info['integrations'].add('telegram')
                elif 'sheets' in node_type:
                    info['integrations'].add('sheets')
                elif 'openai' in node_type:
                    info['integrations'].add('openai')
                elif 'webhook' in node_type:
                    info['integrations'].add('webhook')
        except:
            pass
    
    # Déterminer le purpose depuis le nom du fichier
    name_lower = filename.replace('.json', '').replace('_', ' ').replace('-', ' ')
    
    # Patterns pour identifier le purpose
    purpose_patterns = {
        'email_automation': ['email', 'gmail', 'outlook', 'mail'],
        'social_media': ['social', 'twitter', 'facebook', 'instagram', 'linkedin'],
        'ai_automation': ['ai', 'gpt', 'openai', 'claude', 'llm', 'agent'],
        'data_processing': ['data', 'process', 'transform', 'etl'],
        'crm_sales': ['crm', 'sales', 'lead', 'customer', 'deal'],
        'webhook_api': ['webhook', 'api', 'http'],
        'messaging': ['slack', 'telegram', 'discord', 'whatsapp', 'chat'],
        'analytics': ['analytics', 'report', 'dashboard', 'metrics'],
        'productivity': ['task', 'todo', 'calendar', 'notion', 'asana'],
        'ecommerce': ['shopify', 'woocommerce', 'order', 'product']
    }
    
    for purpose, keywords in purpose_patterns.items():
        if any(kw in name_lower for kw in keywords):
            info['purpose'] = purpose
            break
    
    return info

def calculate_similarity(wf1, wf2):
    """Calculer la similarité entre deux workflows"""
    score = 0
    
    # Même purpose = haute similarité
    if wf1['purpose'] == wf2['purpose']:
        score += 50
    
    # Intégrations communes
    common_integrations = wf1['integrations'].intersection(wf2['integrations'])
    if common_integrations:
        score += len(common_integrations) * 10
    
    # Complexité similaire
    if wf1['complexity'] == wf2['complexity']:
        score += 20
    
    # Nombre de nodes similaire
    if wf1['node_count'] > 0 and wf2['node_count'] > 0:
        node_diff = abs(wf1['node_count'] - wf2['node_count'])
        if node_diff <= 3:
            score += 20
        elif node_diff <= 5:
            score += 10
    
    return score

print("🔍 Analyse des workflows pour déduplication...")

# Collecter tous les workflows
all_workflows = []
invalid_workflows = []

# Scanner tous les dossiers
dirs_to_scan = [
    '/var/www/automatehub/200_automations_n8n',
    '/var/www/automatehub/github_workflows'
]

for base_dir in dirs_to_scan:
    if os.path.exists(base_dir):
        for root, dirs, files in os.walk(base_dir):
            for file in files:
                if file.endswith('.json'):
                    filepath = os.path.join(root, file)
                    info = extract_workflow_info(filepath)
                    
                    if info['valid_json']:
                        all_workflows.append(info)
                    else:
                        invalid_workflows.append(filepath)

print(f"✅ Workflows valides trouvés: {len(all_workflows)}")
print(f"❌ Workflows invalides (JSON corrompu): {len(invalid_workflows)}")

# Grouper par purpose pour détecter les doublons
purpose_groups = defaultdict(list)
for wf in all_workflows:
    purpose_groups[wf['purpose']].append(wf)

# Traiter les doublons
kept_workflows = []
duplicates = []

for purpose, workflows in purpose_groups.items():
    if len(workflows) == 1:
        kept_workflows.append(workflows[0])
        continue
    
    # Trier par qualité (node_count comme proxy)
    workflows.sort(key=lambda x: x['node_count'], reverse=True)
    
    # Process workflows pour trouver les vrais doublons
    processed = []
    
    for wf in workflows:
        is_duplicate = False
        
        # Comparer avec les workflows déjà traités
        for kept in processed:
            similarity = calculate_similarity(wf, kept)
            if similarity >= 70:  # Seuil de similarité pour considérer comme doublon
                is_duplicate = True
                duplicates.append({
                    'removed': wf['filename'],
                    'kept': kept['filename'],
                    'similarity': similarity
                })
                break
        
        if not is_duplicate:
            processed.append(wf)
            kept_workflows.append(wf)

print(f"\n📊 Résultats de déduplication:")
print(f"   Workflows uniques: {len(kept_workflows)}")
print(f"   Doublons détectés: {len(duplicates)}")

# Organiser par catégorie métier
categories = {
    'Marketing_Digital': {
        'keywords': ['marketing', 'social', 'seo', 'content', 'blog', 'campaign'],
        'workflows': []
    },
    'Sales_CRM': {
        'keywords': ['sales', 'crm', 'lead', 'customer', 'deal', 'pipeline'],
        'workflows': []
    },
    'RH_Recrutement': {
        'keywords': ['hr', 'recruitment', 'employee', 'candidate', 'interview', 'onboarding'],
        'workflows': []
    },
    'Finance_Comptabilite': {
        'keywords': ['invoice', 'payment', 'accounting', 'expense', 'billing', 'finance'],
        'workflows': []
    },
    'IT_DevOps': {
        'keywords': ['backup', 'deploy', 'docker', 'server', 'monitoring', 'security'],
        'workflows': []
    },
    'Service_Client': {
        'keywords': ['support', 'ticket', 'helpdesk', 'customer service', 'feedback'],
        'workflows': []
    },
    'Data_Analytics': {
        'keywords': ['analytics', 'report', 'dashboard', 'data', 'metrics', 'kpi'],
        'workflows': []
    },
    'Ecommerce': {
        'keywords': ['shopify', 'woocommerce', 'order', 'product', 'cart', 'inventory'],
        'workflows': []
    },
    'AI_Automation': {
        'keywords': ['ai', 'gpt', 'openai', 'claude', 'llm', 'agent', 'chatbot'],
        'workflows': []
    },
    'Productivite': {
        'keywords': ['task', 'todo', 'calendar', 'notion', 'asana', 'productivity'],
        'workflows': []
    },
    'Communication': {
        'keywords': ['email', 'gmail', 'slack', 'telegram', 'discord', 'whatsapp'],
        'workflows': []
    },
    'General': {
        'keywords': [],
        'workflows': []
    }
}

# Catégoriser les workflows
for wf in kept_workflows:
    filename_lower = wf['filename'].lower()
    categorized = False
    
    for cat_name, cat_info in categories.items():
        if cat_name == 'General':
            continue
            
        if any(kw in filename_lower for kw in cat_info['keywords']):
            cat_info['workflows'].append(wf)
            categorized = True
            break
    
    if not categorized:
        categories['General']['workflows'].append(wf)

# Créer la structure de packs
print("\n📦 Création des packs par métier:")
packs_dir = '/var/www/automatehub/Packs_Metiers'
os.makedirs(packs_dir, exist_ok=True)

pack_summary = {}
for cat_name, cat_info in categories.items():
    if len(cat_info['workflows']) > 0:
        pack_dir = os.path.join(packs_dir, f"Pack_{cat_name}")
        os.makedirs(pack_dir, exist_ok=True)
        
        # Copier les workflows dans le pack
        for i, wf in enumerate(cat_info['workflows']):
            src = wf['path']
            dst = os.path.join(pack_dir, f"{i+1:04d}_{wf['filename']}")
            try:
                shutil.copy2(src, dst)
            except:
                pass
        
        pack_summary[cat_name] = len(cat_info['workflows'])
        print(f"   Pack_{cat_name}: {len(cat_info['workflows'])} workflows")

# Sauvegarder le rapport
report = {
    'total_analyzed': len(all_workflows) + len(invalid_workflows),
    'valid_workflows': len(all_workflows),
    'invalid_workflows': len(invalid_workflows),
    'unique_workflows': len(kept_workflows),
    'duplicates_found': len(duplicates),
    'packs_created': pack_summary,
    'duplicate_examples': duplicates[:10]
}

with open('/var/www/automatehub/deduplication_report_final.json', 'w') as f:
    json.dump(report, f, indent=2)

print(f"\n✅ Déduplication terminée!")
print(f"📁 Packs créés dans: {packs_dir}")