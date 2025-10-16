#!/usr/bin/env python3
import os
import json
import shutil
from collections import defaultdict

# Analyser tous les workflows disponibles
def analyze_workflow(filepath):
    """Analyser un workflow pour extraire ses caractéristiques"""
    filename = os.path.basename(filepath).lower()
    
    features = {
        'path': filepath,
        'filename': os.path.basename(filepath),
        'categories': [],
        'benefits': [],
        'integrations': [],
        'use_cases': []
    }
    
    # Identifier les catégories
    category_keywords = {
        'marketing': ['marketing', 'campaign', 'seo', 'content', 'blog'],
        'social': ['twitter', 'linkedin', 'facebook', 'instagram', 'social', 'youtube', 'tiktok'],
        'email': ['email', 'gmail', 'outlook', 'newsletter', 'mail'],
        'ai': ['ai', 'gpt', 'openai', 'claude', 'chatgpt', 'llm'],
        'sales': ['sales', 'lead', 'crm', 'deal', 'customer', 'prospect'],
        'ecommerce': ['shopify', 'woocommerce', 'order', 'product', 'cart', 'ecommerce'],
        'productivity': ['task', 'todo', 'calendar', 'notion', 'asana', 'productivity'],
        'data': ['data', 'analytics', 'report', 'dashboard', 'sheets', 'metrics'],
        'communication': ['slack', 'telegram', 'whatsapp', 'discord', 'chat', 'message'],
        'automation': ['automate', 'webhook', 'api', 'integration', 'sync'],
        'support': ['support', 'ticket', 'helpdesk', 'zendesk', 'feedback'],
        'finance': ['invoice', 'payment', 'billing', 'expense', 'accounting']
    }
    
    for cat, keywords in category_keywords.items():
        if any(kw in filename for kw in keywords):
            features['categories'].append(cat)
    
    # Identifier les bénéfices
    benefit_keywords = {
        'time_saving': ['automate', 'automatic', 'schedule', 'daily', 'recurring'],
        'lead_generation': ['lead', 'prospect', 'customer', 'acquisition', 'growth'],
        'customer_satisfaction': ['support', 'feedback', 'response', 'reply', 'customer'],
        'revenue_increase': ['sales', 'conversion', 'upsell', 'payment', 'invoice'],
        'visibility': ['social', 'post', 'share', 'publish', 'marketing'],
        'efficiency': ['sync', 'integrate', 'connect', 'streamline', 'optimize']
    }
    
    for benefit, keywords in benefit_keywords.items():
        if any(kw in filename for kw in keywords):
            features['benefits'].append(benefit)
    
    # Identifier les intégrations principales
    integrations = {
        'google': ['gmail', 'sheets', 'drive', 'calendar', 'google'],
        'microsoft': ['outlook', 'teams', 'office'],
        'social_media': ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'],
        'messaging': ['slack', 'telegram', 'whatsapp', 'discord'],
        'ai_tools': ['openai', 'gpt', 'claude', 'ai'],
        'ecommerce': ['shopify', 'woocommerce', 'stripe', 'paypal'],
        'crm': ['hubspot', 'salesforce', 'pipedrive', 'airtable'],
        'productivity': ['notion', 'asana', 'trello', 'todoist']
    }
    
    for integ, keywords in integrations.items():
        if any(kw in filename for kw in keywords):
            features['integrations'].append(integ)
    
    return features

print("🚀 Création de packs intelligents avec réutilisation...")

# Collecter et analyser tous les workflows
all_workflows = []
for base_dir in ['/var/www/automatehub/200_automations_n8n', '/var/www/automatehub/github_workflows']:
    if os.path.exists(base_dir):
        for root, dirs, files in os.walk(base_dir):
            for file in files:
                if file.endswith('.json') and os.path.getsize(os.path.join(root, file)) > 100:
                    filepath = os.path.join(root, file)
                    workflow_info = analyze_workflow(filepath)
                    all_workflows.append(workflow_info)

print(f"📊 Total workflows analysés: {len(all_workflows)}")

# Définir les packs par métier/secteur
PACKS_METIER = {
    'Pack_Marketing_Complet': {
        'prix': 149,
        'description': 'Suite complète marketing digital : réseaux sociaux, email, contenu, SEO',
        'filters': lambda w: any(cat in w['categories'] for cat in ['marketing', 'social', 'email', 'ai']) and 'visibility' in w['benefits'],
        'target_count': 100
    },
    'Pack_Vente_CRM': {
        'prix': 129,
        'description': 'Gestion commerciale complète : leads, CRM, suivi clients',
        'filters': lambda w: 'sales' in w['categories'] or 'crm' in w['integrations'],
        'target_count': 80
    },
    'Pack_Ecommerce_Pro': {
        'prix': 159,
        'description': 'Solution e-commerce complète : boutique, commandes, clients',
        'filters': lambda w: 'ecommerce' in w['categories'] or 'ecommerce' in w['integrations'],
        'target_count': 90
    },
    'Pack_Service_Client': {
        'prix': 99,
        'description': 'Support client automatisé : tickets, réponses, satisfaction',
        'filters': lambda w: 'support' in w['categories'] or 'customer_satisfaction' in w['benefits'],
        'target_count': 60
    },
    'Pack_Productivite_Entreprise': {
        'prix': 89,
        'description': 'Outils de productivité pour équipes',
        'filters': lambda w: 'productivity' in w['categories'] or 'efficiency' in w['benefits'],
        'target_count': 70
    }
}

# Définir les packs par bénéfice
PACKS_BENEFICE = {
    'Pack_Gain_de_Temps': {
        'prix': 119,
        'description': '🕐 Économisez 10h par semaine avec ces automatisations',
        'filters': lambda w: 'time_saving' in w['benefits'] or 'automation' in w['categories'],
        'target_count': 100
    },
    'Pack_Acquisition_Clients': {
        'prix': 179,
        'description': '🎯 Attirez et convertissez plus de clients automatiquement',
        'filters': lambda w: 'lead_generation' in w['benefits'] or ('marketing' in w['categories'] and 'social' in w['categories']),
        'target_count': 120
    },
    'Pack_Augmenter_Revenus': {
        'prix': 199,
        'description': '💰 Boostez votre CA avec l\'automatisation intelligente',
        'filters': lambda w: 'revenue_increase' in w['benefits'] or 'sales' in w['categories'],
        'target_count': 100
    },
    'Pack_Visibilite_Maximum': {
        'prix': 139,
        'description': '📢 Faites-vous connaître sur tous les canaux',
        'filters': lambda w: 'visibility' in w['benefits'] or any(cat in w['categories'] for cat in ['social', 'marketing']),
        'target_count': 110
    },
    'Pack_Satisfaction_Client': {
        'prix': 109,
        'description': '⭐ Clients heureux = Business florissant',
        'filters': lambda w: 'customer_satisfaction' in w['benefits'] or 'support' in w['categories'],
        'target_count': 80
    }
}

# Définir les packs par cas d'usage
PACKS_USAGE = {
    'Pack_Startup_Essential': {
        'prix': 89,
        'description': '🚀 L\'essentiel pour lancer votre startup',
        'filters': lambda w: any(cat in w['categories'] for cat in ['email', 'social', 'productivity']),
        'target_count': 60
    },
    'Pack_Freelance_Pro': {
        'prix': 79,
        'description': '💼 Kit complet du freelance productif',
        'filters': lambda w: any(ben in w['benefits'] for ben in ['time_saving', 'efficiency']) and 'productivity' in w['categories'],
        'target_count': 50
    },
    'Pack_PME_Digital': {
        'prix': 169,
        'description': '🏢 Transformation digitale pour PME',
        'filters': lambda w: len(w['categories']) >= 2,  # Workflows polyvalents
        'target_count': 130
    },
    'Pack_Agency_Tools': {
        'prix': 249,
        'description': '🎨 Outils pro pour agences digitales',
        'filters': lambda w: any(cat in w['categories'] for cat in ['marketing', 'social', 'ai']) and len(w['integrations']) >= 2,
        'target_count': 150
    }
}

# Définir les packs spéciaux/bundles
PACKS_SPECIAUX = {
    'Pack_IA_Revolution': {
        'prix': 199,
        'description': '🤖 La révolution IA pour votre business',
        'filters': lambda w: 'ai' in w['categories'] or 'ai_tools' in w['integrations'],
        'target_count': 150
    },
    'Pack_NoCode_Master': {
        'prix': 299,
        'description': '🔥 MEGA PACK - 300 automations sans coder',
        'filters': lambda w: True,  # Prendre les meilleurs de chaque catégorie
        'target_count': 300
    },
    'Pack_Integration_Pro': {
        'prix': 149,
        'description': '🔗 Connectez tous vos outils ensemble',
        'filters': lambda w: len(w['integrations']) >= 2 or 'api' in w['filename'].lower(),
        'target_count': 100
    }
}

# Définir les bundles de packs
BUNDLES = {
    'Bundle_Entrepreneur': {
        'prix': 349,
        'description': '📦 BUNDLE ENTREPRENEUR : Marketing + Vente + Productivité',
        'included_packs': ['Pack_Marketing_Complet', 'Pack_Vente_CRM', 'Pack_Productivite_Entreprise']
    },
    'Bundle_Croissance': {
        'prix': 449,
        'description': '📦 BUNDLE CROISSANCE : Acquisition + Revenus + Visibilité',
        'included_packs': ['Pack_Acquisition_Clients', 'Pack_Augmenter_Revenus', 'Pack_Visibilite_Maximum']
    },
    'Bundle_Ultimate': {
        'prix': 999,
        'description': '📦 BUNDLE ULTIMATE : TOUS les packs + Support VIP',
        'included_packs': 'all'
    }
}

# Créer la structure de sortie
output_dir = '/var/www/automatehub/AutomateHub_Packs'
os.makedirs(output_dir, exist_ok=True)

# Fonction pour créer un pack
def create_pack(pack_name, pack_config, workflows, pack_type):
    pack_dir = os.path.join(output_dir, pack_type, pack_name)
    os.makedirs(pack_dir, exist_ok=True)
    
    # Filtrer les workflows selon les critères du pack
    selected_workflows = []
    for wf in workflows:
        if pack_config['filters'](wf) and len(selected_workflows) < pack_config['target_count']:
            selected_workflows.append(wf)
    
    # Copier les workflows
    for i, wf in enumerate(selected_workflows, 1):
        src = wf['path']
        dst = os.path.join(pack_dir, f"{i:04d}_{wf['filename']}")
        try:
            shutil.copy2(src, dst)
        except:
            pass
    
    # Créer le fichier info
    pack_info = {
        'name': pack_name,
        'type': pack_type,
        'description': pack_config['description'],
        'prix': pack_config['prix'],
        'workflow_count': len(selected_workflows),
        'workflows': [w['filename'] for w in selected_workflows[:10]]  # Aperçu des 10 premiers
    }
    
    with open(os.path.join(pack_dir, 'pack_info.json'), 'w') as f:
        json.dump(pack_info, f, indent=2)
    
    return len(selected_workflows), pack_info

# Créer tous les packs
all_packs_created = {}
total_workflows_used = set()  # Pour compter les workflows uniques utilisés

# Packs par métier
print("\n📂 Création des packs MÉTIER...")
for pack_name, config in PACKS_METIER.items():
    count, info = create_pack(pack_name, config, all_workflows, 'Metier')
    all_packs_created[pack_name] = info
    print(f"✅ {pack_name}: {count} workflows - {config['prix']}€")

# Packs par bénéfice
print("\n💡 Création des packs BÉNÉFICE...")
for pack_name, config in PACKS_BENEFICE.items():
    count, info = create_pack(pack_name, config, all_workflows, 'Benefice')
    all_packs_created[pack_name] = info
    print(f"✅ {pack_name}: {count} workflows - {config['prix']}€")

# Packs par usage
print("\n🎯 Création des packs USAGE...")
for pack_name, config in PACKS_USAGE.items():
    count, info = create_pack(pack_name, config, all_workflows, 'Usage')
    all_packs_created[pack_name] = info
    print(f"✅ {pack_name}: {count} workflows - {config['prix']}€")

# Packs spéciaux
print("\n⭐ Création des packs SPÉCIAUX...")
for pack_name, config in PACKS_SPECIAUX.items():
    count, info = create_pack(pack_name, config, all_workflows, 'Special')
    all_packs_created[pack_name] = info
    print(f"✅ {pack_name}: {count} workflows - {config['prix']}€")

# Créer les bundles
print("\n📦 Création des BUNDLES...")
bundles_dir = os.path.join(output_dir, 'Bundles')
os.makedirs(bundles_dir, exist_ok=True)

for bundle_name, bundle_config in BUNDLES.items():
    bundle_info = {
        'name': bundle_name,
        'description': bundle_config['description'],
        'prix': bundle_config['prix'],
        'included_packs': bundle_config['included_packs'],
        'total_workflows': 0
    }
    
    if bundle_config['included_packs'] == 'all':
        bundle_info['total_workflows'] = sum(p['workflow_count'] for p in all_packs_created.values())
    else:
        bundle_info['total_workflows'] = sum(
            all_packs_created.get(pack, {}).get('workflow_count', 0) 
            for pack in bundle_config['included_packs']
        )
    
    with open(os.path.join(bundles_dir, f"{bundle_name}.json"), 'w') as f:
        json.dump(bundle_info, f, indent=2)
    
    print(f"✅ {bundle_name}: ~{bundle_info['total_workflows']} workflows - {bundle_config['prix']}€")

# Créer le catalogue principal
catalog = {
    'total_workflows_available': len(all_workflows),
    'total_packs': len(all_packs_created),
    'total_bundles': len(BUNDLES),
    'categories': {
        'metier': len(PACKS_METIER),
        'benefice': len(PACKS_BENEFICE),
        'usage': len(PACKS_USAGE),
        'special': len(PACKS_SPECIAUX)
    },
    'packs': all_packs_created
}

with open(os.path.join(output_dir, 'CATALOG_COMPLETE.json'), 'w') as f:
    json.dump(catalog, f, indent=2)

print(f"\n🎉 Création terminée!")
print(f"📊 {len(all_packs_created)} packs créés")
print(f"📦 {len(BUNDLES)} bundles créés")
print(f"📁 Tout est dans: {output_dir}")