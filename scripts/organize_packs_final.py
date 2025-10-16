#!/usr/bin/env python3
import os
import json
import shutil
from collections import defaultdict

# Configuration des packs par métier/secteur
PACKS_CONFIG = {
    'Pack_01_Marketing_ReseauxSociaux': {
        'prix': 79,
        'keywords': ['marketing', 'social', 'twitter', 'linkedin', 'facebook', 'instagram', 'youtube', 'tiktok', 'post', 'content'],
        'max_workflows': 50,
        'description': '50 workflows pour automatiser votre marketing digital et réseaux sociaux'
    },
    'Pack_02_Email_Communication': {
        'prix': 69,
        'keywords': ['email', 'gmail', 'outlook', 'newsletter', 'mail', 'smtp', 'imap'],
        'max_workflows': 40,
        'description': '40 workflows pour gérer vos emails et communications automatiquement'
    },
    'Pack_03_IA_ChatGPT_Automation': {
        'prix': 149,
        'keywords': ['ai', 'gpt', 'openai', 'claude', 'llm', 'chatgpt', 'agent', 'chatbot', 'artificial'],
        'max_workflows': 75,
        'description': '75 workflows IA avancés avec ChatGPT, Claude et autres LLMs'
    },
    'Pack_04_CRM_Ventes': {
        'prix': 99,
        'keywords': ['crm', 'sales', 'lead', 'customer', 'hubspot', 'salesforce', 'pipedrive', 'deal'],
        'max_workflows': 45,
        'description': '45 workflows pour automatiser votre CRM et processus de vente'
    },
    'Pack_05_Ecommerce_Shopify': {
        'prix': 119,
        'keywords': ['shopify', 'woocommerce', 'ecommerce', 'order', 'product', 'cart', 'shop'],
        'max_workflows': 50,
        'description': '50 workflows pour automatiser votre boutique en ligne'
    },
    'Pack_06_Productivite_Organisation': {
        'prix': 59,
        'keywords': ['task', 'todo', 'notion', 'asana', 'trello', 'calendar', 'productivity', 'project'],
        'max_workflows': 35,
        'description': '35 workflows pour booster votre productivité quotidienne'
    },
    'Pack_07_Data_Analytics': {
        'prix': 89,
        'keywords': ['data', 'analytics', 'report', 'dashboard', 'sheets', 'excel', 'bigquery', 'metrics'],
        'max_workflows': 40,
        'description': '40 workflows pour analyser vos données et créer des rapports'
    },
    'Pack_08_Messagerie_Instantanee': {
        'prix': 79,
        'keywords': ['slack', 'telegram', 'whatsapp', 'discord', 'teams', 'chat', 'message'],
        'max_workflows': 45,
        'description': '45 workflows pour automatiser Slack, Telegram, WhatsApp et plus'
    },
    'Pack_09_Support_Client': {
        'prix': 89,
        'keywords': ['support', 'ticket', 'zendesk', 'helpdesk', 'customer service', 'feedback'],
        'max_workflows': 35,
        'description': '35 workflows pour un support client automatisé et efficace'
    },
    'Pack_10_API_Webhooks': {
        'prix': 69,
        'keywords': ['api', 'webhook', 'http', 'rest', 'integration', 'request'],
        'max_workflows': 40,
        'description': '40 workflows pour intégrations API et webhooks'
    },
    'Pack_11_RH_Recrutement': {
        'prix': 99,
        'keywords': ['hr', 'recruitment', 'employee', 'candidate', 'interview', 'resume', 'hiring'],
        'max_workflows': 30,
        'description': '30 workflows pour automatiser vos processus RH'
    },
    'Pack_12_Finance_Comptabilite': {
        'prix': 109,
        'keywords': ['invoice', 'payment', 'accounting', 'expense', 'billing', 'stripe', 'paypal'],
        'max_workflows': 35,
        'description': '35 workflows pour la gestion financière automatisée'
    }
}

# Packs spéciaux
SPECIAL_PACKS = {
    'Pack_00_Decouverte_Gratuit': {
        'prix': 0,
        'max_workflows': 10,
        'description': '10 workflows essentiels pour découvrir n8n - GRATUIT'
    },
    'Pack_99_Ultimate_Collection': {
        'prix': 499,
        'max_workflows': 500,
        'description': 'Collection ULTIMATE - 500 meilleurs workflows toutes catégories'
    }
}

print("🚀 Organisation finale des workflows en packs...")

# Collecter tous les workflows disponibles
all_workflows = []
for base_dir in ['/var/www/automatehub/200_automations_n8n', '/var/www/automatehub/github_workflows']:
    if os.path.exists(base_dir):
        for root, dirs, files in os.walk(base_dir):
            for file in files:
                if file.endswith('.json') and os.path.getsize(os.path.join(root, file)) > 100:
                    all_workflows.append({
                        'filename': file,
                        'path': os.path.join(root, file),
                        'category': 'uncategorized'
                    })

print(f"📊 Total workflows disponibles: {len(all_workflows)}")

# Catégoriser les workflows
categorized_workflows = defaultdict(list)
uncategorized = []

for wf in all_workflows:
    filename_lower = wf['filename'].lower()
    found_category = False
    
    # Chercher dans les packs normaux
    for pack_name, pack_config in PACKS_CONFIG.items():
        if any(keyword in filename_lower for keyword in pack_config['keywords']):
            categorized_workflows[pack_name].append(wf)
            found_category = True
            break
    
    if not found_category:
        uncategorized.append(wf)

# Créer les dossiers de packs
output_dir = '/var/www/automatehub/Packs_AutomateHub'
os.makedirs(output_dir, exist_ok=True)

# Créer les packs normaux
total_workflows_used = 0
pack_report = []

for pack_name, pack_config in PACKS_CONFIG.items():
    pack_workflows = categorized_workflows[pack_name][:pack_config['max_workflows']]
    
    if len(pack_workflows) > 0:
        pack_dir = os.path.join(output_dir, pack_name)
        os.makedirs(pack_dir, exist_ok=True)
        
        # Copier les workflows
        for i, wf in enumerate(pack_workflows, 1):
            src = wf['path']
            dst = os.path.join(pack_dir, f"{i:03d}_{wf['filename']}")
            try:
                shutil.copy2(src, dst)
            except:
                pass
        
        # Créer le fichier info du pack
        pack_info = {
            'name': pack_name.replace('_', ' '),
            'description': pack_config['description'],
            'prix': pack_config['prix'],
            'workflows_count': len(pack_workflows),
            'keywords': pack_config['keywords']
        }
        
        with open(os.path.join(pack_dir, 'pack_info.json'), 'w') as f:
            json.dump(pack_info, f, indent=2)
        
        total_workflows_used += len(pack_workflows)
        pack_report.append({
            'pack': pack_name,
            'count': len(pack_workflows),
            'prix': pack_config['prix']
        })
        
        print(f"✅ {pack_name}: {len(pack_workflows)} workflows - {pack_config['prix']}€")

# Créer le pack découverte (10 workflows variés)
discovery_pack = []
for cat_workflows in categorized_workflows.values():
    if len(discovery_pack) < 10 and cat_workflows:
        discovery_pack.append(cat_workflows[0])

if discovery_pack:
    pack_dir = os.path.join(output_dir, 'Pack_00_Decouverte_Gratuit')
    os.makedirs(pack_dir, exist_ok=True)
    
    for i, wf in enumerate(discovery_pack[:10], 1):
        src = wf['path']
        dst = os.path.join(pack_dir, f"{i:03d}_{wf['filename']}")
        try:
            shutil.copy2(src, dst)
        except:
            pass
    
    print(f"✅ Pack_00_Decouverte_Gratuit: {len(discovery_pack)} workflows - GRATUIT")

# Créer le pack Ultimate (meilleurs workflows de chaque catégorie)
ultimate_workflows = []
for cat_workflows in categorized_workflows.values():
    ultimate_workflows.extend(cat_workflows[:50])  # Top 50 de chaque catégorie

if len(ultimate_workflows) > 500:
    ultimate_workflows = ultimate_workflows[:500]

if ultimate_workflows:
    pack_dir = os.path.join(output_dir, 'Pack_99_Ultimate_Collection')
    os.makedirs(pack_dir, exist_ok=True)
    
    for i, wf in enumerate(ultimate_workflows, 1):
        src = wf['path']
        dst = os.path.join(pack_dir, f"{i:03d}_{wf['filename']}")
        try:
            shutil.copy2(src, dst)
        except:
            pass
    
    print(f"✅ Pack_99_Ultimate_Collection: {len(ultimate_workflows)} workflows - 499€")

# Créer le catalogue des packs
catalog = {
    'total_workflows': len(all_workflows),
    'workflows_organized': total_workflows_used,
    'packs': []
}

# Ajouter tous les packs au catalogue
for pack in pack_report:
    catalog['packs'].append(pack)

catalog['packs'].insert(0, {
    'pack': 'Pack_00_Decouverte_Gratuit',
    'count': 10,
    'prix': 0
})

catalog['packs'].append({
    'pack': 'Pack_99_Ultimate_Collection',
    'count': len(ultimate_workflows),
    'prix': 499
})

# Calculer les totaux
total_value = sum(p['prix'] * p['count'] for p in catalog['packs'] if p['prix'] > 0)
catalog['total_market_value'] = total_value

# Sauvegarder le catalogue
with open(os.path.join(output_dir, 'CATALOGUE_PACKS.json'), 'w') as f:
    json.dump(catalog, f, indent=2)

# Créer un README général
readme_content = f"""# 📦 Catalogue des Packs AutomateHub

## 🎯 Résumé
- **Total workflows disponibles**: {len(all_workflows)}
- **Workflows organisés**: {total_workflows_used}
- **Nombre de packs**: {len(catalog['packs'])}

## 💰 Packs Disponibles

"""

for pack in catalog['packs']:
    prix_text = "GRATUIT" if pack['prix'] == 0 else f"{pack['prix']}€"
    readme_content += f"### {pack['pack'].replace('_', ' ')}\n"
    readme_content += f"- **Workflows**: {pack['count']}\n"
    readme_content += f"- **Prix**: {prix_text}\n\n"

readme_content += f"""
## 🚀 Offres Spéciales

1. **Pack Découverte**: GRATUIT - Parfait pour débuter
2. **Bundle 3 Packs**: -20% de réduction
3. **Bundle 5 Packs**: -30% de réduction
4. **Pack Ultimate + 3 mois Skool**: 599€ (au lieu de 700€)

## 📈 Valeur Totale
Valeur marchande totale de l'inventaire: **{total_value:,.0f}€**
"""

with open(os.path.join(output_dir, 'README.md'), 'w') as f:
    f.write(readme_content)

print(f"\n🎉 Organisation terminée avec succès!")
print(f"📁 Packs créés dans: {output_dir}")
print(f"📊 Catalogue sauvegardé: {output_dir}/CATALOGUE_PACKS.json")