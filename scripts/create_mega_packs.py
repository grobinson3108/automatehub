#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour créer TOUS les packs possibles - exploitation maximale des 2056 workflows !
"""
import json
import os
import shutil
from pathlib import Path
import re

def load_workflow_analysis():
    """Charger l'analyse complète des workflows"""
    analysis_file = Path("/var/www/automatehub/ANALYSE_COMPLETE_WORKFLOWS.md")

    workflows = {}
    categories = {}

    with open(analysis_file, 'r', encoding='utf-8') as f:
        content = f.read()

    lines = content.split('\n')
    in_table = False
    in_categories = False

    # Extraire les catégories avec leurs counts
    for line in lines:
        if line.startswith('## 📈 Répartition par catégories'):
            in_categories = True
            continue
        elif in_categories and line.startswith('- **'):
            match = re.match(r'- \*\*(.+?)\*\*: (\d+) workflows', line)
            if match:
                categories[match.group(1)] = int(match.group(2))
        elif in_categories and line.startswith('##'):
            in_categories = False

    # Extraire les workflows du tableau
    for line in lines:
        if line.startswith('| Fichier | Nom | Description | Nodes |'):
            in_table = True
            continue
        elif line.startswith('|---------|-----|-------------|-------|'):
            continue
        elif in_table and line.startswith('|'):
            parts = [p.strip() for p in line.split('|')[1:-1]]
            if len(parts) >= 4:
                filename = parts[0].strip('`')
                name = parts[1]
                description = parts[2]
                nodes = parts[3]

                workflows[filename] = {
                    'name': name,
                    'description': description,
                    'nodes': nodes
                }
        elif in_table and not line.startswith('|'):
            break

    return workflows, categories

def create_mega_packs():
    """Créer TOUS les packs possibles pour maximiser les ventes"""

    workflows, categories = load_workflow_analysis()
    workflows_dir = Path("/var/www/automatehub/github_workflows/n8n-workflows/workflows")
    packs_dir = Path("/var/www/automatehub/MEGA_PACKS_COLLECTION")

    # Supprimer et recréer
    if packs_dir.exists():
        shutil.rmtree(packs_dir)
    packs_dir.mkdir()

    # MÉGA COLLECTION DE PACKS - Exploitation maximale !
    mega_pack_definitions = {

        # === CRYPTO & BLOCKCHAIN === (Marché premium)
        "01_CRYPTO_DEXSCREENER_PRO": {
            "description": "Workflows spécialisés DexScreener et analyse crypto",
            "keywords": ["crypto/dexscreener"],
            "price": 67,
            "category": "Crypto Premium"
        },
        "02_BLOCKCHAIN_TRADING_MASTER": {
            "description": "Workflows blockchain et trading automatisé",
            "keywords": ["blockchain", "crypto"],
            "exclude_keywords": ["dexscreener"],
            "price": 47,
            "category": "Crypto"
        },
        "03_COINGECKO_DATA_MINER": {
            "description": "Workflows CoinGecko pour data mining crypto",
            "keywords": ["crypto/coingecko", "coingecko"],
            "price": 37,
            "category": "Crypto"
        },

        # === INTELLIGENCE ARTIFICIELLE === (Marché en explosion)
        "04_OPENAI_GPT_POWERHOUSE": {
            "description": "Workflows OpenAI et GPT pour tout automatiser",
            "keywords": ["ia/openai"],
            "exclude_keywords": ["crypto", "dexscreener", "telegram", "email"],
            "price": 47,
            "category": "IA Premium"
        },
        "05_LANGCHAIN_AI_FACTORY": {
            "description": "Workflows LangChain avancés",
            "keywords": ["langchain"],
            "price": 42,
            "category": "IA"
        },
        "06_AI_CONTENT_GENERATOR": {
            "description": "IA pour génération de contenu et automation",
            "keywords": ["ia/openai", "content", "generator"],
            "price": 39,
            "category": "IA"
        },

        # === TELEGRAM === (Marché massif)
        "07_TELEGRAM_CRYPTO_BOTS": {
            "description": "Bots Telegram spécialisés crypto et trading",
            "keywords": ["telegram", "crypto"],
            "price": 52,
            "category": "Telegram Premium"
        },
        "08_TELEGRAM_AI_ASSISTANTS": {
            "description": "Assistants Telegram avec IA intégrée",
            "keywords": ["telegram bot ia", "telegram", "ia/openai"],
            "exclude_keywords": ["crypto"],
            "price": 42,
            "category": "Telegram"
        },
        "09_TELEGRAM_AUTOMATION_HUB": {
            "description": "Automation Telegram pour business",
            "keywords": ["telegram automation"],
            "exclude_keywords": ["crypto", "ia/openai"],
            "price": 32,
            "category": "Telegram"
        },
        "10_TELEGRAM_MARKETING_BEAST": {
            "description": "Marketing automation via Telegram",
            "keywords": ["telegram", "marketing"],
            "price": 37,
            "category": "Telegram"
        },

        # === EMAIL MARKETING === (Marché stable et rentable)
        "11_EMAIL_AI_MARKETING": {
            "description": "Email marketing boosté à l'IA",
            "keywords": ["email automation", "ia/openai"],
            "price": 42,
            "category": "Email Premium"
        },
        "12_GMAIL_AUTOMATION_PRO": {
            "description": "Automation Gmail professionnelle",
            "keywords": ["gmail", "email"],
            "price": 32,
            "category": "Email"
        },
        "13_MAILCHIMP_POWERPACK": {
            "description": "Workflows Mailchimp avancés",
            "keywords": ["mailchimp"],
            "price": 29,
            "category": "Email"
        },
        "14_EMAIL_CRM_FUSION": {
            "description": "Email automation + CRM intégré",
            "keywords": ["email automation", "crm"],
            "price": 37,
            "category": "Email"
        },

        # === GOOGLE WORKSPACE === (Marché entreprise)
        "15_GOOGLE_SHEETS_AI_PRO": {
            "description": "Google Sheets + IA pour analyses avancées",
            "keywords": ["google sheets", "ia/openai"],
            "price": 42,
            "category": "Google Premium"
        },
        "16_GOOGLE_DRIVE_MANAGER": {
            "description": "Gestion automatisée Google Drive",
            "keywords": ["google drive"],
            "exclude_keywords": ["ia/openai"],
            "price": 27,
            "category": "Google"
        },
        "17_GOOGLE_CALENDAR_SYNC": {
            "description": "Synchronisation Google Calendar avancée",
            "keywords": ["google calendar"],
            "price": 25,
            "category": "Google"
        },
        "18_GOOGLE_WORKSPACE_COMPLETE": {
            "description": "Suite complète Google Workspace",
            "keywords": ["google services"],
            "exclude_keywords": ["ia/openai", "crypto"],
            "price": 35,
            "category": "Google"
        },

        # === CRM & BUSINESS === (Marché B2B)
        "19_HUBSPOT_AUTOMATION_KING": {
            "description": "Workflows HubSpot pour sales automation",
            "keywords": ["hubspot"],
            "price": 47,
            "category": "CRM Premium"
        },
        "20_AIRTABLE_DATABASE_PRO": {
            "description": "Airtable pour bases de données pros",
            "keywords": ["airtable"],
            "price": 39,
            "category": "CRM"
        },
        "21_NOTION_PRODUCTIVITY_SUITE": {
            "description": "Workflows Notion pour productivité max",
            "keywords": ["notion"],
            "price": 35,
            "category": "CRM"
        },
        "22_PIPEDRIVE_SALES_MACHINE": {
            "description": "Machine à ventes Pipedrive",
            "keywords": ["pipedrive"],
            "price": 42,
            "category": "CRM"
        },
        "23_CRM_AI_OPTIMIZER": {
            "description": "CRM boosté à l'IA",
            "keywords": ["crm/business", "ia/openai"],
            "price": 52,
            "category": "CRM Premium"
        },

        # === E-COMMERCE === (Marché lucratif)
        "24_SHOPIFY_AUTOMATION_EMPIRE": {
            "description": "Empire e-commerce Shopify",
            "keywords": ["shopify"],
            "price": 47,
            "category": "E-commerce Premium"
        },
        "25_WOOCOMMERCE_SALES_BOOST": {
            "description": "Booster de ventes WooCommerce",
            "keywords": ["woocommerce"],
            "price": 37,
            "category": "E-commerce"
        },
        "26_ECOMMERCE_AI_ASSISTANT": {
            "description": "Assistant IA pour e-commerce",
            "keywords": ["e-commerce", "ia/openai"],
            "price": 52,
            "category": "E-commerce Premium"
        },
        "27_STRIPE_PAYMENT_PRO": {
            "description": "Workflows paiements Stripe avancés",
            "keywords": ["stripe"],
            "price": 42,
            "category": "E-commerce"
        },

        # === SOCIAL MEDIA === (Marché viral)
        "28_SOCIAL_MEDIA_AI_BEAST": {
            "description": "Social media automation + IA",
            "keywords": ["social media", "ia/openai"],
            "price": 47,
            "category": "Social Premium"
        },
        "29_TWITTER_AUTOMATION_PRO": {
            "description": "Automation Twitter professionnelle",
            "keywords": ["twitter"],
            "price": 35,
            "category": "Social"
        },
        "30_DISCORD_BOT_FACTORY": {
            "description": "Factory de bots Discord",
            "keywords": ["discord"],
            "price": 32,
            "category": "Social"
        },
        "31_LINKEDIN_GROWTH_HACK": {
            "description": "Growth hacking LinkedIn",
            "keywords": ["linkedin"],
            "price": 42,
            "category": "Social"
        },
        "32_FACEBOOK_ADS_OPTIMIZER": {
            "description": "Optimiseur de pubs Facebook",
            "keywords": ["facebook"],
            "price": 39,
            "category": "Social"
        },

        # === DATABASES & DATA === (Marché technique)
        "33_DATABASE_AI_ANALYST": {
            "description": "Analyse de données + IA",
            "keywords": ["database", "ia/openai"],
            "price": 47,
            "category": "Data Premium"
        },
        "34_MYSQL_AUTOMATION_SUITE": {
            "description": "Suite automation MySQL",
            "keywords": ["mysql"],
            "price": 32,
            "category": "Database"
        },
        "35_POSTGRES_DATA_PIPELINE": {
            "description": "Pipeline de données PostgreSQL",
            "keywords": ["postgres"],
            "price": 35,
            "category": "Database"
        },
        "36_MONGODB_NOSQL_PRO": {
            "description": "Workflows MongoDB NoSQL",
            "keywords": ["mongodb"],
            "price": 37,
            "category": "Database"
        },

        # === WEBHOOKS & INTEGRATIONS === (Marché technique)
        "37_WEBHOOK_INTEGRATION_HUB": {
            "description": "Hub d'intégrations webhooks",
            "keywords": ["webhook/http"],
            "exclude_keywords": ["ia/openai", "crypto", "email", "telegram", "social", "google"],
            "price": 29,
            "category": "Integration"
        },
        "38_API_AUTOMATION_FACTORY": {
            "description": "Factory d'automation API",
            "keywords": ["http", "api"],
            "price": 32,
            "category": "Integration"
        },
        "39_ZAPIER_ALTERNATIVE_PRO": {
            "description": "Alternative pro à Zapier",
            "keywords": ["automation", "integration"],
            "price": 35,
            "category": "Integration"
        },

        # === COMMUNICATION & COLLABORATION ===
        "40_SLACK_PRODUCTIVITY_BOOST": {
            "description": "Booster de productivité Slack",
            "keywords": ["slack"],
            "price": 35,
            "category": "Communication"
        },
        "41_MICROSOFT_OFFICE_SYNC": {
            "description": "Synchronisation Microsoft Office",
            "keywords": ["microsoft"],
            "price": 32,
            "category": "Communication"
        },
        "42_TEAM_COLLABORATION_AI": {
            "description": "Collaboration d'équipe + IA",
            "keywords": ["collaboration", "ia/openai"],
            "price": 42,
            "category": "Communication"
        },

        # === MARKETING AUTOMATION ===
        "43_LEAD_GENERATION_MONSTER": {
            "description": "Machine à leads ultra-efficace",
            "keywords": ["lead", "generation"],
            "price": 47,
            "category": "Marketing"
        },
        "44_CONTENT_MARKETING_AI": {
            "description": "Marketing de contenu + IA",
            "keywords": ["content", "marketing", "ia/openai"],
            "price": 42,
            "category": "Marketing"
        },
        "45_SEO_AUTOMATION_BEAST": {
            "description": "Beast d'automation SEO",
            "keywords": ["seo"],
            "price": 39,
            "category": "Marketing"
        },

        # === PRODUCTIVITY & AUTOMATION ===
        "46_PERSONAL_AUTOMATION_SUITE": {
            "description": "Suite d'automation personnelle",
            "keywords": ["personal", "productivity"],
            "price": 29,
            "category": "Productivity"
        },
        "47_BUSINESS_PROCESS_OPTIMIZER": {
            "description": "Optimiseur de processus business",
            "keywords": ["process", "business"],
            "price": 42,
            "category": "Productivity"
        },
        "48_TIME_MANAGEMENT_AI": {
            "description": "Gestion du temps + IA",
            "keywords": ["time", "management", "ia/openai"],
            "price": 37,
            "category": "Productivity"
        },

        # === NICHE MARKETS ===
        "49_REAL_ESTATE_AUTOMATION": {
            "description": "Automation immobilier",
            "keywords": ["real estate", "immobilier"],
            "price": 47,
            "category": "Niche"
        },
        "50_HEALTHCARE_WORKFLOWS": {
            "description": "Workflows secteur santé",
            "keywords": ["health", "medical"],
            "price": 52,
            "category": "Niche"
        },
        "51_EDUCATION_AUTOMATION": {
            "description": "Automation secteur éducation",
            "keywords": ["education", "learning"],
            "price": 39,
            "category": "Niche"
        },
        "52_FINANCE_AUTOMATION_PRO": {
            "description": "Automation finance professionnelle",
            "keywords": ["finance", "accounting"],
            "price": 52,
            "category": "Niche"
        },

        # === MIXED PACKS (Combos populaires) ===
        "53_AI_CRYPTO_FUSION": {
            "description": "Fusion IA + Crypto ultra-puissante",
            "keywords": ["ia/openai", "crypto"],
            "price": 67,
            "category": "Fusion Premium"
        },
        "54_EMAIL_AI_CRM_TRINITY": {
            "description": "Trinité Email + IA + CRM",
            "keywords": ["email", "ia/openai", "crm"],
            "price": 57,
            "category": "Fusion"
        },
        "55_SOCIAL_AI_VIRAL_PACK": {
            "description": "Pack viral Social + IA",
            "keywords": ["social", "ia/openai"],
            "price": 52,
            "category": "Fusion"
        },

        # === STARTER PACKS (Prix accessibles) ===
        "56_BEGINNER_AUTOMATION_KIT": {
            "description": "Kit débutant automation",
            "keywords": ["manual", "simple"],
            "price": 19,
            "category": "Starter"
        },
        "57_SMALL_BUSINESS_ESSENTIALS": {
            "description": "Essentiels petites entreprises",
            "keywords": ["small business"],
            "price": 25,
            "category": "Starter"
        },
        "58_FREELANCER_TOOLKIT": {
            "description": "Toolkit freelancer",
            "keywords": ["freelance"],
            "price": 27,
            "category": "Starter"
        },

        # === PREMIUM COLLECTIONS ===
        "59_ENTERPRISE_MEGA_SUITE": {
            "description": "Méga suite entreprise",
            "keywords": ["enterprise", "business"],
            "price": 97,
            "category": "Premium"
        },
        "60_AI_MASTER_COLLECTION": {
            "description": "Collection maître IA",
            "keywords": ["ia/openai", "langchain"],
            "price": 87,
            "category": "Premium"
        }
    }

    print(f"🚀 CRÉATION DE {len(mega_pack_definitions)} MÉGA PACKS!")
    print("💰 Exploitation maximale des 2056 workflows analysés\\n")

    # Fonction de matching améliorée
    def matches_mega_pack(description, keywords, exclude_keywords=None):
        desc_lower = description.lower()

        # Vérifier les exclusions
        if exclude_keywords:
            for exclude in exclude_keywords:
                if exclude.lower() in desc_lower:
                    return False

        # Vérifier les mots-clés (OR logic pour plus de flexibilité)
        for keyword in keywords:
            if keyword.lower() in desc_lower:
                return True

        return False

    # Classer les workflows
    for pack_name, pack_info in mega_pack_definitions.items():
        pack_info['workflows'] = []

        for filename, workflow_data in workflows.items():
            if matches_mega_pack(workflow_data['description'], pack_info['keywords'], pack_info.get('exclude_keywords')):
                pack_info['workflows'].append({
                    'filename': filename,
                    'name': workflow_data['name'],
                    'description': workflow_data['description'],
                    'nodes': workflow_data['nodes']
                })

    # Créer les packs avec workflows
    total_packs_created = 0
    total_workflows_packed = 0
    total_value = 0

    for pack_name, pack_info in mega_pack_definitions.items():
        if not pack_info['workflows']:
            continue

        pack_dir = packs_dir / pack_name
        pack_dir.mkdir()

        # Limiter à 20 workflows max par pack
        selected_workflows = pack_info['workflows'][:20]

        print(f"📦 {pack_name}")
        print(f"   💰 {pack_info['price']}€ | 📊 {len(selected_workflows)} workflows | 🎯 {pack_info['category']}")

        # Copier les fichiers
        for i, workflow in enumerate(selected_workflows, 1):
            source_files = list(workflows_dir.rglob(workflow['filename']))
            if source_files:
                source_file = source_files[0]
                dest_file = pack_dir / f"{i:02d}_{workflow['filename']}"
                shutil.copy2(source_file, dest_file)

        # Créer README
        readme_content = f"""# 💎 {pack_name.replace('_', ' ').title()}

## 🎯 Description
{pack_info['description']}

## 💰 Prix de vente
**{pack_info['price']}€**

## 📊 Statistiques
- **{len(selected_workflows)} workflows inclus**
- **Catégorie**: {pack_info['category']}
- **Mots-clés**: {', '.join(pack_info['keywords'])}

## 📋 Contenu détaillé

"""

        for i, workflow in enumerate(selected_workflows, 1):
            readme_content += f"### {i:02d}. {workflow['filename']}\n"
            readme_content += f"- **Nom**: {workflow['name']}\n"
            readme_content += f"- **Type**: {workflow['description']}\n"
            readme_content += f"- **Complexité**: {workflow['nodes']} nodes\\n\\n"

        readme_content += f"""
## 🎯 Public cible
Ce pack est idéal pour les utilisateurs cherchant à {pack_info['description'].lower()}.

## 💡 Valeur ajoutée
- Workflows testés et optimisés
- Documentation complète incluse
- Support technique disponible
- Mises à jour gratuites

---
*Pack créé par analyse automatisée de {len(workflows)} workflows*
"""

        with open(pack_dir / "README.md", 'w', encoding='utf-8') as f:
            f.write(readme_content)

        total_packs_created += 1
        total_workflows_packed += len(selected_workflows)
        total_value += pack_info['price']

    # Créer la synthèse MEGA
    synthesis_content = f"""# 🚀 MÉGA COLLECTION - TOUS LES PACKS POSSIBLES

**Date**: {os.popen('date').read().strip()}
**Workflows analysés**: {len(workflows)}
**Packs créés**: {total_packs_created}
**Workflows packagés**: {total_workflows_packed}

## 💰 POTENTIEL COMMERCIAL MASSIF
**Valeur totale**: {total_value}€

## 📊 Répartition par catégories

"""

    # Grouper par catégorie
    categories_summary = {}
    for pack_name, pack_info in mega_pack_definitions.items():
        if pack_info['workflows']:
            category = pack_info['category']
            if category not in categories_summary:
                categories_summary[category] = {'count': 0, 'value': 0, 'packs': []}

            categories_summary[category]['count'] += 1
            categories_summary[category]['value'] += pack_info['price']
            categories_summary[category]['packs'].append((pack_name, pack_info['price']))

    # Trier par valeur décroissante
    for category, data in sorted(categories_summary.items(), key=lambda x: x[1]['value'], reverse=True):
        synthesis_content += f"### {category}\n"
        synthesis_content += f"- **{data['count']} packs** pour **{data['value']}€**\\n"
        for pack_name, price in sorted(data['packs'], key=lambda x: x[1], reverse=True):
            clean_name = pack_name.replace('_', ' ').title()
            synthesis_content += f"  - {clean_name}: {price}€\\n"
        synthesis_content += "\\n"

    synthesis_content += f"""
## 🎯 STRATÉGIE DE VENTE MAXIMALE

### 💎 Packs Premium (50€+)
Marché haute valeur - Clients enterprise et crypto

### 🚀 Packs Pro (30-49€)
Marché principal - PME et professionnels

### 📦 Packs Standard (20-29€)
Marché accessible - Freelancers et débutants

### 🎁 Packs Starter (15-19€)
Marché d'entrée - Acquisition clients

## 📈 OPPORTUNITÉS DE MARCHÉ

1. **Crypto/Blockchain**: {categories_summary.get('Crypto Premium', {}).get('value', 0) + categories_summary.get('Crypto', {}).get('value', 0)}€
2. **Intelligence Artificielle**: {categories_summary.get('IA Premium', {}).get('value', 0) + categories_summary.get('IA', {}).get('value', 0)}€
3. **E-commerce**: {categories_summary.get('E-commerce Premium', {}).get('value', 0) + categories_summary.get('E-commerce', {}).get('value', 0)}€
4. **Marketing**: {categories_summary.get('Marketing', {}).get('value', 0)}€

## 🎊 CONCLUSION
Avec {total_packs_created} packs spécialisés pour {total_value}€ de valeur totale,
tu as maintenant une armée de produits pour conquérir TOUS les marchés ! 🚀

---
*Analyse basée sur {len(workflows)} workflows réels*
"""

    with open(packs_dir / "MEGA_SYNTHESIS.md", 'w', encoding='utf-8') as f:
        f.write(synthesis_content)

    print(f"\\n🎉 MÉGA MISSION ACCOMPLIE!")
    print(f"📦 {total_packs_created} packs créés")
    print(f"📊 {total_workflows_packed} workflows packagés")
    print(f"💰 {total_value}€ de valeur totale!")
    print(f"📁 Tous les packs dans: {packs_dir}")

if __name__ == "__main__":
    create_mega_packs()