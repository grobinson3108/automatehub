#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour créer des packs thématiques pertinents basés sur l'analyse réelle des workflows
"""
import json
import os
import shutil
from pathlib import Path

def load_workflow_analysis():
    """Charger l'analyse des workflows depuis le fichier markdown"""
    analysis_file = Path("/var/www/automatehub/ANALYSE_COMPLETE_WORKFLOWS.md")

    workflows = {}

    # Lire le fichier markdown et extraire les données du tableau
    with open(analysis_file, 'r', encoding='utf-8') as f:
        content = f.read()

    # Trouver le tableau principal
    lines = content.split('\n')
    in_table = False

    for line in lines:
        if line.startswith('| Fichier | Nom | Description | Nodes |'):
            in_table = True
            continue
        elif line.startswith('|---------|-----|-------------|-------|'):
            continue
        elif in_table and line.startswith('|'):
            parts = [p.strip() for p in line.split('|')[1:-1]]  # Enlever les | vides
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

    return workflows

def create_thematic_packs():
    """Créer les packs thématiques basés sur l'analyse"""

    workflows = load_workflow_analysis()
    workflows_dir = Path("/var/www/automatehub/github_workflows/n8n-workflows/workflows")
    packs_dir = Path("/var/www/automatehub/Packs_Workflows_Thematiques")

    # Supprimer et recréer le répertoire des packs
    if packs_dir.exists():
        shutil.rmtree(packs_dir)
    packs_dir.mkdir()

    # Définir les packs thématiques avec critères
    pack_definitions = {
        "01_CRYPTO_TRADING_MASTER": {
            "description": "Workflows spécialisés dans la crypto et le trading",
            "keywords": ["crypto", "dexscreener", "coingecko", "blockchain", "token"],
            "price": 47,
            "workflows": []
        },
        "02_IA_AUTOMATION_PRO": {
            "description": "Workflows avec intelligence artificielle et OpenAI",
            "keywords": ["ia/openai", "langchain", "openai"],
            "exclude_keywords": ["crypto", "dexscreener"],
            "price": 37,
            "workflows": []
        },
        "03_EMAIL_MARKETING_KING": {
            "description": "Automation complète d'email marketing",
            "keywords": ["email automation", "mailchimp", "gmail"],
            "exclude_keywords": ["crypto"],
            "price": 27,
            "workflows": []
        },
        "04_TELEGRAM_BOT_EMPIRE": {
            "description": "Bots Telegram avancés et automation",
            "keywords": ["telegram", "bot"],
            "exclude_keywords": ["crypto", "dexscreener"],
            "price": 32,
            "workflows": []
        },
        "05_CRM_BUSINESS_DOMINATION": {
            "description": "Workflows CRM et gestion business",
            "keywords": ["crm/business", "hubspot", "pipedrive", "airtable", "notion"],
            "exclude_keywords": ["crypto"],
            "price": 35,
            "workflows": []
        },
        "06_GOOGLE_WORKSPACE_MASTER": {
            "description": "Automation Google Workspace complète",
            "keywords": ["google sheets", "google drive", "google calendar", "google services"],
            "exclude_keywords": ["crypto", "dexscreener"],
            "price": 29,
            "workflows": []
        },
        "07_SOCIAL_MEDIA_AUTOPILOT": {
            "description": "Automation réseaux sociaux",
            "keywords": ["social media", "twitter", "facebook", "linkedin", "discord"],
            "exclude_keywords": ["crypto"],
            "price": 31,
            "workflows": []
        },
        "08_ECOMMERCE_CASH_MACHINE": {
            "description": "Workflows e-commerce et ventes",
            "keywords": ["e-commerce", "shopify", "woocommerce", "stripe"],
            "price": 33,
            "workflows": []
        },
        "09_DATABASE_DATA_PRO": {
            "description": "Gestion et automation de bases de données",
            "keywords": ["database", "mysql", "postgres", "mongodb"],
            "price": 28,
            "workflows": []
        },
        "10_WEBHOOK_HTTP_MASTER": {
            "description": "Intégrations webhooks et API",
            "keywords": ["webhook/http"],
            "exclude_keywords": ["crypto", "dexscreener", "ia/openai", "email", "telegram", "crm", "google", "social"],
            "price": 25,
            "workflows": []
        }
    }

    # Fonction pour vérifier si un workflow correspond à un pack
    def matches_pack(description, keywords, exclude_keywords=None):
        desc_lower = description.lower()

        # Vérifier les exclusions d'abord
        if exclude_keywords:
            for exclude in exclude_keywords:
                if exclude.lower() in desc_lower:
                    return False

        # Vérifier les mots-clés requis
        for keyword in keywords:
            if keyword.lower() in desc_lower:
                return True

        return False

    # Classer les workflows dans les packs
    for filename, workflow_data in workflows.items():
        description = workflow_data['description']

        # Essayer de classer dans un pack
        classified = False
        for pack_name, pack_info in pack_definitions.items():
            if matches_pack(description, pack_info['keywords'], pack_info.get('exclude_keywords')):
                pack_info['workflows'].append({
                    'filename': filename,
                    'name': workflow_data['name'],
                    'description': description,
                    'nodes': workflow_data['nodes']
                })
                classified = True
                break

    # Créer les répertoires et copier les fichiers
    for pack_name, pack_info in pack_definitions.items():
        if not pack_info['workflows']:
            continue

        pack_dir = packs_dir / pack_name
        pack_dir.mkdir()

        print(f"🎯 Création du pack {pack_name}")
        print(f"   📝 {pack_info['description']}")
        print(f"   💰 Prix: {pack_info['price']}€")
        print(f"   📊 {len(pack_info['workflows'])} workflows")

        # Limiter à 15 workflows par pack (pour éviter les packs trop gros)
        selected_workflows = pack_info['workflows'][:15]

        # Copier les fichiers workflows
        for i, workflow in enumerate(selected_workflows, 1):
            # Trouver le fichier source
            source_files = list(workflows_dir.rglob(workflow['filename']))
            if source_files:
                source_file = source_files[0]
                dest_file = pack_dir / f"{i:02d}_{workflow['filename']}"
                shutil.copy2(source_file, dest_file)
                print(f"   ✅ {workflow['filename']} -> {dest_file.name}")

        # Créer le fichier README du pack
        readme_content = f"""# 🎯 {pack_name.replace('_', ' ').title()}

## 📝 Description
{pack_info['description']}

## 💰 Prix de vente suggéré
**{pack_info['price']}€**

## 📊 Contenu du pack
**{len(selected_workflows)} workflows sélectionnés**

### Liste des workflows inclus:

"""

        for i, workflow in enumerate(selected_workflows, 1):
            readme_content += f"{i:02d}. **{workflow['filename']}**\n"
            readme_content += f"    - Nom: {workflow['name']}\n"
            readme_content += f"    - Description: {workflow['description']}\n"
            readme_content += f"    - Nodes: {workflow['nodes']}\n\n"

        readme_content += f"""
## 🎯 Mots-clés de classification
- **Inclus**: {', '.join(pack_info['keywords'])}
"""

        if pack_info.get('exclude_keywords'):
            readme_content += f"- **Exclus**: {', '.join(pack_info['exclude_keywords'])}\n"

        readme_content += f"""
## 📈 Potentiel commercial
Ce pack cible les utilisateurs intéressés par **{pack_info['description'].lower()}** avec {len(selected_workflows)} workflows prêts à l'emploi.

## 🚀 Utilisation
1. Importer les workflows dans n8n
2. Configurer les credentials nécessaires
3. Adapter les paramètres selon vos besoins
4. Activer les workflows

---
*Généré automatiquement par analyse des {len(workflows)} workflows disponibles*
"""

        with open(pack_dir / "README.md", 'w', encoding='utf-8') as f:
            f.write(readme_content)

        print(f"   📄 README.md créé")
        print()

    # Créer un fichier de synthèse global
    summary_content = f"""# 📦 SYNTHÈSE DES PACKS THÉMATIQUES

**Date de création**: {os.popen('date').read().strip()}
**Total workflows analysés**: {len(workflows)}

## 📊 Packs créés

"""

    total_workflows_packed = 0
    total_value = 0

    for pack_name, pack_info in pack_definitions.items():
        if pack_info['workflows']:
            selected_count = min(len(pack_info['workflows']), 15)
            total_workflows_packed += selected_count
            total_value += pack_info['price']

            summary_content += f"### {pack_name.replace('_', ' ').title()}\n"
            summary_content += f"- **Prix**: {pack_info['price']}€\n"
            summary_content += f"- **Workflows**: {selected_count}\n"
            summary_content += f"- **Description**: {pack_info['description']}\n\n"

    summary_content += f"""
## 💰 Potentiel commercial total
- **{len([p for p in pack_definitions.values() if p['workflows']])} packs créés**
- **{total_workflows_packed} workflows packagés**
- **Valeur totale: {total_value}€**

## 🎯 Stratégie de vente
1. **Pack Premium Crypto** (47€) - Marché haute valeur
2. **Packs IA & Business** (35-37€) - Marché professionnel
3. **Packs Spécialisés** (25-33€) - Marché ciblé

## 📈 Analyse de marché
- **Crypto/Trading**: Marché en forte demande, prix premium justifié
- **IA/Automation**: Tendance forte, valeur élevée
- **Email/CRM**: Besoins récurrents, marché stable
- **Intégrations**: Besoins techniques, valeur ajoutée

---
*Analyse basée sur {len(workflows)} workflows réels*
"""

    with open(packs_dir / "SYNTHESE_PACKS.md", 'w', encoding='utf-8') as f:
        f.write(summary_content)

    print("🎉 CRÉATION DES PACKS TERMINÉE!")
    print(f"📦 {len([p for p in pack_definitions.values() if p['workflows']])} packs créés")
    print(f"📊 {total_workflows_packed} workflows packagés")
    print(f"💰 Valeur totale: {total_value}€")
    print(f"📁 Répertoire: {packs_dir}")

if __name__ == "__main__":
    create_thematic_packs()