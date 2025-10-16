#!/usr/bin/env python3
import os
import json
import re
import shutil
from collections import defaultdict
from difflib import SequenceMatcher

def clean_filename(filename):
    """Nettoyer le nom de fichier pour comparaison"""
    # Retirer l'extension et les patterns communs
    name = filename.lower().replace('.json', '')
    # Retirer les patterns de complexité
    name = re.sub(r'_complex_\d+nodes', '', name)
    name = re.sub(r'_\d+nodes', '', name)
    # Retirer les numéros à la fin
    name = re.sub(r'_\d+$', '', name)
    # Remplacer les séparateurs
    name = name.replace('_', ' ').replace('-', ' ')
    return name.strip()

def extract_core_purpose(filepath):
    """Extraire le but principal du workflow"""
    filename = os.path.basename(filepath)
    cleaned_name = clean_filename(filename)
    
    # Lire le contenu si possible
    integrations = []
    node_count = 0
    
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            data = json.load(f)
            node_count = len(data.get('nodes', []))
            
            # Extraire les types de nodes uniques
            node_types = set()
            for node in data.get('nodes', []):
                node_type = node.get('type', '').lower()
                if node_type:
                    node_types.add(node_type.split('.')[0])  # Prendre la partie principale
            
            integrations = list(node_types)[:5]  # Top 5 integrations
            
    except:
        pass
    
    return {
        'cleaned_name': cleaned_name,
        'integrations': sorted(integrations),
        'node_count': node_count,
        'filepath': filepath,
        'filename': filename
    }

def are_duplicates(wf1, wf2):
    """Déterminer si deux workflows sont des doublons"""
    # Similarité du nom
    name_similarity = SequenceMatcher(None, wf1['cleaned_name'], wf2['cleaned_name']).ratio()
    
    # Si les noms sont très similaires (>85%), c'est probablement un doublon
    if name_similarity > 0.85:
        return True
    
    # Si les noms sont moyennement similaires ET les intégrations sont identiques
    if name_similarity > 0.6 and wf1['integrations'] == wf2['integrations']:
        return True
    
    # Si c'est exactement le même workflow (même nom nettoyé)
    if wf1['cleaned_name'] == wf2['cleaned_name']:
        return True
    
    return False

def choose_best_workflow(workflows):
    """Choisir le meilleur workflow parmi les doublons"""
    # Trier par : 1) node_count (complexité optimale 10-30), 2) longueur du nom (plus descriptif)
    def score_workflow(wf):
        score = 0
        
        # Préférer les workflows avec 10-30 nodes
        if 10 <= wf['node_count'] <= 30:
            score += 100
        elif 5 <= wf['node_count'] < 10:
            score += 80
        elif 30 < wf['node_count'] <= 50:
            score += 70
        else:
            score += 50
        
        # Préférer les noms plus descriptifs
        score += len(wf['cleaned_name'].split())
        
        # Bonus pour certains mots-clés de qualité
        quality_keywords = ['complete', 'advanced', 'pro', 'full', 'automated', 'ai']
        for kw in quality_keywords:
            if kw in wf['cleaned_name']:
                score += 10
                
        return score
    
    return max(workflows, key=score_workflow)

print("🔍 Analyse intelligente des workflows...")

# Collecter tous les workflows
all_workflows = []
for base_dir in ['/var/www/automatehub/200_automations_n8n', '/var/www/automatehub/github_workflows']:
    if os.path.exists(base_dir):
        for root, dirs, files in os.walk(base_dir):
            for file in files:
                if file.endswith('.json'):
                    filepath = os.path.join(root, file)
                    wf_info = extract_core_purpose(filepath)
                    all_workflows.append(wf_info)

print(f"📊 Total workflows analysés: {len(all_workflows)}")

# Grouper les workflows similaires
groups = []
processed = set()

for i, wf1 in enumerate(all_workflows):
    if i in processed:
        continue
    
    # Créer un nouveau groupe avec ce workflow
    group = [wf1]
    processed.add(i)
    
    # Chercher les doublons
    for j, wf2 in enumerate(all_workflows[i+1:], start=i+1):
        if j in processed:
            continue
            
        if are_duplicates(wf1, wf2):
            group.append(wf2)
            processed.add(j)
    
    groups.append(group)

# Sélectionner le meilleur de chaque groupe
unique_workflows = []
total_duplicates = 0

for group in groups:
    best = choose_best_workflow(group)
    unique_workflows.append(best)
    
    if len(group) > 1:
        total_duplicates += len(group) - 1

print(f"\n✅ Workflows uniques: {len(unique_workflows)}")
print(f"🔄 Doublons détectés: {total_duplicates}")
print(f"📉 Réduction: {total_duplicates/len(all_workflows)*100:.1f}%")

# Organiser par catégorie métier/secteur
categories = {
    'Pack_Marketing_Digital': {
        'keywords': ['marketing', 'social', 'seo', 'content', 'blog', 'post', 'campaign', 
                    'twitter', 'linkedin', 'facebook', 'instagram', 'youtube'],
        'workflows': [],
        'description': 'Automatisations marketing et réseaux sociaux'
    },
    'Pack_Ventes_CRM': {
        'keywords': ['sales', 'crm', 'lead', 'customer', 'deal', 'pipeline', 'hubspot', 
                    'salesforce', 'pipedrive', 'contact', 'opportunity'],
        'workflows': [],
        'description': 'Gestion commerciale et relation client'
    },
    'Pack_RH_Recrutement': {
        'keywords': ['hr', 'recruitment', 'employee', 'candidate', 'interview', 'onboarding',
                    'resume', 'cv', 'hiring', 'bamboohr', 'applicant'],
        'workflows': [],
        'description': 'Processus RH et gestion des talents'
    },
    'Pack_Finance_Compta': {
        'keywords': ['invoice', 'payment', 'accounting', 'expense', 'billing', 'finance',
                    'quickbooks', 'stripe', 'paypal', 'subscription'],
        'workflows': [],
        'description': 'Gestion financière et comptable'
    },
    'Pack_IT_DevOps': {
        'keywords': ['backup', 'deploy', 'docker', 'server', 'monitoring', 'security',
                    'git', 'github', 'gitlab', 'ssl', 'database', 'api'],
        'workflows': [],
        'description': 'Outils IT et automatisation DevOps'
    },
    'Pack_Service_Client': {
        'keywords': ['support', 'ticket', 'helpdesk', 'zendesk', 'customer service',
                    'feedback', 'satisfaction', 'complaint', 'freshdesk'],
        'workflows': [],
        'description': 'Support client et gestion des tickets'
    },
    'Pack_Data_Analytics': {
        'keywords': ['analytics', 'report', 'dashboard', 'data', 'metrics', 'kpi',
                    'bigquery', 'sheets', 'excel', 'visualization', 'chart'],
        'workflows': [],
        'description': 'Analyse de données et reporting'
    },
    'Pack_Ecommerce': {
        'keywords': ['shopify', 'woocommerce', 'order', 'product', 'cart', 'inventory',
                    'ecommerce', 'shipping', 'fulfillment', 'catalog'],
        'workflows': [],
        'description': 'Solutions e-commerce complètes'
    },
    'Pack_IA_Automation': {
        'keywords': ['ai', 'gpt', 'openai', 'claude', 'llm', 'agent', 'chatbot',
                    'machine learning', 'nlp', 'rag', 'vector', 'embedding'],
        'workflows': [],
        'description': 'Automatisations avec intelligence artificielle'
    },
    'Pack_Productivite': {
        'keywords': ['task', 'todo', 'calendar', 'notion', 'asana', 'productivity',
                    'trello', 'jira', 'project', 'planning', 'schedule'],
        'workflows': [],
        'description': 'Outils de productivité et gestion de projets'
    },
    'Pack_Communication': {
        'keywords': ['email', 'gmail', 'outlook', 'slack', 'teams', 'discord',
                    'telegram', 'whatsapp', 'sms', 'notification', 'message'],
        'workflows': [],
        'description': 'Communication et messagerie multi-canal'
    },
    'Pack_Automation_Web': {
        'keywords': ['webhook', 'http', 'scrape', 'crawl', 'extract', 'api',
                    'rest', 'graphql', 'fetch', 'request'],
        'workflows': [],
        'description': 'Intégrations web et API'
    }
}

# Catégoriser les workflows uniques
uncategorized = []
for wf in unique_workflows:
    name_lower = wf['cleaned_name'].lower()
    categorized = False
    
    for cat_name, cat_info in categories.items():
        if any(kw in name_lower for kw in cat_info['keywords']):
            cat_info['workflows'].append(wf)
            categorized = True
            break
    
    if not categorized:
        uncategorized.append(wf)

# Ajouter les non-catégorisés au pack général
if uncategorized:
    categories['Pack_General'] = {
        'workflows': uncategorized,
        'description': 'Automatisations diverses et générales'
    }

# Créer les packs
output_dir = '/var/www/automatehub/Packs_Pro'
os.makedirs(output_dir, exist_ok=True)

print("\n📦 Création des packs professionnels:")
pack_summary = []

for pack_name, pack_info in categories.items():
    if len(pack_info['workflows']) > 0:
        pack_dir = os.path.join(output_dir, pack_name)
        os.makedirs(pack_dir, exist_ok=True)
        
        # Créer README du pack
        readme_content = f"# {pack_name.replace('_', ' ')}\n\n"
        readme_content += f"{pack_info['description']}\n\n"
        readme_content += f"## Contenu du pack\n\n"
        readme_content += f"**{len(pack_info['workflows'])} workflows professionnels**\n\n"
        
        # Copier les workflows
        for i, wf in enumerate(pack_info['workflows'], 1):
            try:
                src = wf['filepath']
                dst = os.path.join(pack_dir, f"{i:04d}_{wf['filename']}")
                shutil.copy2(src, dst)
                
                readme_content += f"{i}. **{wf['filename']}**\n"
                if wf['node_count'] > 0:
                    readme_content += f"   - Complexité: {wf['node_count']} étapes\n"
                readme_content += f"   - Purpose: {wf['cleaned_name']}\n\n"
            except:
                pass
        
        # Sauvegarder README
        with open(os.path.join(pack_dir, 'README.md'), 'w', encoding='utf-8') as f:
            f.write(readme_content)
        
        pack_summary.append({
            'name': pack_name,
            'count': len(pack_info['workflows']),
            'description': pack_info['description']
        })
        
        print(f"   ✅ {pack_name}: {len(pack_info['workflows'])} workflows")

# Créer le rapport final
report = {
    'total_analyzed': len(all_workflows),
    'unique_workflows': len(unique_workflows),
    'duplicates_removed': total_duplicates,
    'reduction_rate': f"{total_duplicates/len(all_workflows)*100:.1f}%",
    'packs_created': len(pack_summary),
    'pack_details': pack_summary
}

with open('/var/www/automatehub/smart_deduplication_report.json', 'w') as f:
    json.dump(report, f, indent=2)

print(f"\n🎉 Organisation terminée!")
print(f"📁 {len(pack_summary)} packs créés dans: {output_dir}")
print(f"📊 Rapport sauvegardé: /var/www/automatehub/smart_deduplication_report.json")