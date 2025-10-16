#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour analyser tous les workflows n8n et créer un fichier d'analyse complète
"""
import json
import os
import sys
from pathlib import Path

def analyze_workflow(workflow_path):
    """Analyser un workflow n8n et retourner une description courte"""
    try:
        with open(workflow_path, 'r', encoding='utf-8') as f:
            data = json.load(f)

        name = data.get('name', 'Sans nom')
        nodes = data.get('nodes', [])

        # Analyser les types de nodes principaux
        node_types = [node.get('type', '') for node in nodes]

        # Détection de patterns spécifiques
        description_parts = []

        # Crypto/Blockchain
        if any('dex' in str(node).lower() for node in nodes) or 'dexscreener' in str(data).lower():
            description_parts.append("Crypto/DexScreener")
        elif any('blockchain' in str(node).lower() for node in nodes):
            description_parts.append("Blockchain")
        elif any('coingecko' in t.lower() for t in node_types):
            description_parts.append("Crypto/CoinGecko")

        # Telegram
        if any('telegram' in t.lower() for t in node_types):
            if any('langchain' in t.lower() for t in node_types) or any('openai' in t.lower() for t in node_types):
                description_parts.append("Telegram Bot IA")
            else:
                description_parts.append("Telegram automation")

        # Email
        if any('email' in t.lower() or 'gmail' in t.lower() or 'mailchimp' in t.lower() for t in node_types):
            description_parts.append("Email automation")

        # Google services
        google_services = [t for t in node_types if 'google' in t.lower()]
        if google_services:
            if 'googlesheets' in str(google_services).lower():
                description_parts.append("Google Sheets")
            elif 'googledrive' in str(google_services).lower():
                description_parts.append("Google Drive")
            elif 'googlecalendar' in str(google_services).lower():
                description_parts.append("Google Calendar")
            else:
                description_parts.append("Google services")

        # Social Media
        if any(social in t.lower() for t in node_types for social in ['twitter', 'facebook', 'linkedin', 'discord']):
            description_parts.append("Social media")

        # CRM/Business
        if any(crm in t.lower() for t in node_types for crm in ['hubspot', 'pipedrive', 'airtable', 'notion']):
            description_parts.append("CRM/Business")

        # E-commerce
        if any(ecom in t.lower() for t in node_types for ecom in ['shopify', 'woocommerce', 'stripe']):
            description_parts.append("E-commerce")

        # AI/OpenAI
        if any('openai' in t.lower() or 'langchain' in t.lower() for t in node_types):
            description_parts.append("IA/OpenAI")

        # Webhook/HTTP
        if any('webhook' in t.lower() or 'http' in t.lower() for t in node_types):
            description_parts.append("Webhook/HTTP")

        # Database
        if any(db in t.lower() for t in node_types for db in ['mysql', 'postgres', 'mongodb']):
            description_parts.append("Database")

        # Si aucun pattern détecté, essayer d'analyser le nom
        if not description_parts:
            name_lower = name.lower()
            if 'crypto' in name_lower or 'token' in name_lower:
                description_parts.append("Crypto")
            elif 'email' in name_lower:
                description_parts.append("Email")
            elif 'telegram' in name_lower:
                description_parts.append("Telegram")
            elif 'automation' in name_lower:
                description_parts.append("Automation générale")
            else:
                # Utiliser les types de nodes les plus fréquents
                main_types = [t.split('.')[-1] for t in node_types if t]
                if main_types:
                    description_parts.append(f"Workflow {main_types[0]}")
                else:
                    description_parts.append("Workflow générique")

        description = " + ".join(description_parts) if description_parts else "Workflow non catégorisé"

        return {
            'name': name,
            'description': description,
            'node_count': len(nodes),
            'main_types': list(set([t.split('.')[-1] for t in node_types[:3]]))
        }

    except Exception as e:
        return {
            'name': 'Erreur lecture',
            'description': f"Erreur: {str(e)}",
            'node_count': 0,
            'main_types': []
        }

def main():
    workflows_dir = Path("/var/www/automatehub/github_workflows/n8n-workflows/workflows")
    output_file = Path("/var/www/automatehub/ANALYSE_COMPLETE_WORKFLOWS.md")

    print("🔍 Analyse de tous les workflows n8n...")
    print(f"📁 Répertoire: {workflows_dir}")

    # Collecter tous les fichiers JSON
    workflow_files = list(workflows_dir.rglob("*.json"))
    total_files = len(workflow_files)

    print(f"📊 {total_files} workflows trouvés")

    # Analyser chaque workflow
    analyses = []
    categories = {}

    for i, workflow_file in enumerate(workflow_files, 1):
        if i % 100 == 0:
            print(f"⏳ Progression: {i}/{total_files} ({i/total_files*100:.1f}%)")

        relative_path = workflow_file.relative_to(workflows_dir)
        analysis = analyze_workflow(workflow_file)

        analyses.append({
            'path': str(relative_path),
            'filename': workflow_file.name,
            'analysis': analysis
        })

        # Compter les catégories
        desc = analysis['description']
        categories[desc] = categories.get(desc, 0) + 1

    print("✅ Analyse terminée!")
    print("📝 Génération du fichier d'analyse...")

    # Générer le fichier markdown
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write("# 📊 ANALYSE COMPLÈTE DES WORKFLOWS N8N\n\n")
        f.write(f"**Total workflows analysés**: {total_files}\n\n")
        f.write(f"**Date d'analyse**: {os.popen('date').read().strip()}\n\n")

        # Statistiques par catégorie
        f.write("## 📈 Répartition par catégories\n\n")
        sorted_categories = sorted(categories.items(), key=lambda x: x[1], reverse=True)
        for category, count in sorted_categories:
            f.write(f"- **{category}**: {count} workflows\n")

        f.write("\n## 📋 Liste complète des workflows\n\n")
        f.write("| Fichier | Nom | Description | Nodes |\n")
        f.write("|---------|-----|-------------|-------|\n")

        # Trier par catégorie puis par nom
        analyses.sort(key=lambda x: (x['analysis']['description'], x['filename']))

        for item in analyses:
            path = item['path']
            filename = item['filename']
            analysis = item['analysis']

            f.write(f"| `{filename}` | {analysis['name']} | {analysis['description']} | {analysis['node_count']} |\n")

        # Section détaillée par catégorie
        f.write("\n## 🔍 Analyse détaillée par catégorie\n\n")

        for category, count in sorted_categories:
            f.write(f"### {category} ({count} workflows)\n\n")

            category_workflows = [item for item in analyses if item['analysis']['description'] == category]

            for item in category_workflows[:10]:  # Limiter à 10 par catégorie pour la lisibilité
                f.write(f"- **{item['filename']}**: {item['analysis']['name']}\n")

            if len(category_workflows) > 10:
                f.write(f"- ... et {len(category_workflows) - 10} autres\n")

            f.write("\n")

    print(f"✅ Fichier d'analyse créé: {output_file}")
    print(f"📊 {len(categories)} catégories identifiées")
    print("\n🔥 Top 5 des catégories:")
    for category, count in sorted_categories[:5]:
        print(f"   {category}: {count} workflows")

if __name__ == "__main__":
    main()