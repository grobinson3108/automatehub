#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour créer des descriptions détaillées de chaque workflow avec instructions d'installation
"""
import json
import os
from pathlib import Path

def analyze_workflow_details(workflow_path):
    """Analyser un workflow pour extraire APIs, credentials et instructions"""
    try:
        with open(workflow_path, 'r', encoding='utf-8') as f:
            data = json.load(f)

        name = data.get('name', 'Sans nom')
        nodes = data.get('nodes', [])

        # Extraire les types de nodes et credentials
        node_types = []
        credentials_needed = []
        apis_urls = []
        webhook_urls = []

        for node in nodes:
            node_type = node.get('type', '')
            node_types.append(node_type)

            # Extraire les credentials
            if 'credentials' in node:
                for cred_key, cred_info in node['credentials'].items():
                    if cred_key not in credentials_needed:
                        credentials_needed.append(cred_key)

            # Extraire les URLs et APIs
            parameters = node.get('parameters', {})

            # URLs dans les paramètres
            for param_key, param_value in parameters.items():
                if isinstance(param_value, str):
                    if param_value.startswith('http'):
                        if 'api.' in param_value or 'webhook' in param_value:
                            apis_urls.append(param_value)
                        elif 'webhook' in param_value.lower():
                            webhook_urls.append(param_value)

            # URLs spécifiques selon le type de node
            if 'telegram' in node_type.lower():
                apis_urls.append('https://api.telegram.org/bot{TOKEN}/')
            elif 'openai' in node_type.lower():
                apis_urls.append('https://api.openai.com/v1/')
            elif 'gmail' in node_type.lower():
                apis_urls.append('https://gmail.googleapis.com/gmail/v1/')
            elif 'google' in node_type.lower():
                if 'sheets' in node_type.lower():
                    apis_urls.append('https://sheets.googleapis.com/v4/')
                elif 'drive' in node_type.lower():
                    apis_urls.append('https://www.googleapis.com/drive/v3/')
                elif 'calendar' in node_type.lower():
                    apis_urls.append('https://www.googleapis.com/calendar/v3/')
            elif 'slack' in node_type.lower():
                apis_urls.append('https://slack.com/api/')
            elif 'hubspot' in node_type.lower():
                apis_urls.append('https://api.hubapi.com/')
            elif 'airtable' in node_type.lower():
                apis_urls.append('https://api.airtable.com/v0/')
            elif 'shopify' in node_type.lower():
                apis_urls.append('https://{shop}.myshopify.com/admin/api/')
            elif 'stripe' in node_type.lower():
                apis_urls.append('https://api.stripe.com/v1/')
            elif 'discord' in node_type.lower():
                apis_urls.append('https://discord.com/api/v10/')
            elif 'twitter' in node_type.lower():
                apis_urls.append('https://api.twitter.com/2/')
            elif 'facebook' in node_type.lower():
                apis_urls.append('https://graph.facebook.com/')
            elif 'dexscreener' in str(node).lower():
                apis_urls.append('https://api.dexscreener.com/')
            elif 'coingecko' in node_type.lower():
                apis_urls.append('https://api.coingecko.com/api/v3/')

        # Déterminer la complexité et les bénéfices
        complexity = "Simple" if len(nodes) <= 5 else "Intermédiaire" if len(nodes) <= 15 else "Avancé"

        # Générer des bénéfices basés sur les types de nodes
        benefits = []
        if any('openai' in t.lower() for t in node_types):
            benefits.append("🤖 Automatisation intelligente avec IA")
            benefits.append("📝 Génération de contenu automatique")

        if any('telegram' in t.lower() for t in node_types):
            benefits.append("📱 Communication automatisée")
            benefits.append("🔔 Notifications instantanées")

        if any('email' in t.lower() or 'gmail' in t.lower() for t in node_types):
            benefits.append("📧 Gestion email automatisée")
            benefits.append("📬 Campagnes marketing optimisées")

        if any('google' in t.lower() for t in node_types):
            benefits.append("🌐 Integration Google Workspace")
            benefits.append("📊 Synchronisation de données")

        if any('crypto' in str(node).lower() or 'dexscreener' in str(node).lower() for node in nodes):
            benefits.append("💎 Analyse crypto en temps réel")
            benefits.append("📈 Alertes de trading automatiques")

        if any('webhook' in t.lower() or 'http' in t.lower() for t in node_types):
            benefits.append("🔗 Intégrations API puissantes")
            benefits.append("⚡ Automatisation multi-plateformes")

        # Ajouter des bénéfices génériques si aucun spécifique
        if not benefits:
            benefits = [
                "⚡ Automatisation de tâches répétitives",
                "⏰ Gain de temps considérable",
                "🎯 Amélioration de la productivité"
            ]

        return {
            'name': name,
            'complexity': complexity,
            'node_count': len(nodes),
            'node_types': list(set([t.split('.')[-1] for t in node_types if t])),
            'credentials': list(set(credentials_needed)),
            'apis': list(set(apis_urls)),
            'webhooks': list(set(webhook_urls)),
            'benefits': benefits
        }

    except Exception as e:
        return {
            'name': 'Erreur lors de l\'analyse',
            'complexity': 'Inconnu',
            'node_count': 0,
            'node_types': [],
            'credentials': [],
            'apis': [],
            'webhooks': [],
            'benefits': ['⚠️ Erreur lors de l\'analyse']
        }

def generate_installation_instructions(workflow_details):
    """Générer les instructions d'installation basées sur l'analyse"""
    instructions = []

    # Instructions de base
    instructions.append("📥 **ÉTAPE 1: Import du workflow**")
    instructions.append("   - Ouvrez n8n dans votre navigateur")
    instructions.append("   - Cliquez sur 'Importer' ou 'Import'")
    instructions.append("   - Sélectionnez le fichier .json du workflow")
    instructions.append("   - Confirmez l'import")
    instructions.append("")

    # Instructions spécifiques selon les credentials
    if workflow_details['credentials']:
        instructions.append("🔐 **ÉTAPE 2: Configuration des credentials**")

        for cred in workflow_details['credentials']:
            if 'openai' in cred.lower():
                instructions.append("   - **OpenAI API**: Créez un compte sur https://platform.openai.com")
                instructions.append("     • Allez dans API Keys > Create new secret key")
                instructions.append("     • Copiez votre clé API dans n8n")
            elif 'telegram' in cred.lower():
                instructions.append("   - **Telegram Bot**: Créez un bot via @BotFather")
                instructions.append("     • Envoyez /newbot à @BotFather")
                instructions.append("     • Suivez les instructions et récupérez le token")
            elif 'gmail' in cred.lower() or 'google' in cred.lower():
                instructions.append("   - **Google**: Configurez OAuth2 sur Google Cloud Console")
                instructions.append("     • Créez un projet sur https://console.cloud.google.com")
                instructions.append("     • Activez les APIs nécessaires")
                instructions.append("     • Créez des credentials OAuth2")
            elif 'slack' in cred.lower():
                instructions.append("   - **Slack**: Créez une app Slack")
                instructions.append("     • Allez sur https://api.slack.com/apps")
                instructions.append("     • Créez une nouvelle app et configurez les permissions")
            elif 'hubspot' in cred.lower():
                instructions.append("   - **HubSpot**: Obtenez votre clé API")
                instructions.append("     • Connectez-vous à HubSpot > Settings > Integrations > API key")
            elif 'airtable' in cred.lower():
                instructions.append("   - **Airtable**: Générez un token personnel")
                instructions.append("     • Allez dans Account > Developer > Personal access tokens")
        instructions.append("")

    # Instructions d'activation
    instructions.append("⚡ **ÉTAPE 3: Activation**")
    instructions.append("   - Vérifiez que tous les nodes sont correctement configurés")
    instructions.append("   - Testez le workflow avec 'Test workflow'")
    instructions.append("   - Activez le workflow avec le bouton 'Active'")
    instructions.append("")

    # Instructions spécifiques selon la complexité
    if workflow_details['complexity'] == 'Avancé':
        instructions.append("⚙️ **ÉTAPE 4: Configuration avancée**")
        instructions.append("   - Personnalisez les paramètres selon vos besoins")
        instructions.append("   - Configurez les triggers et schedules")
        instructions.append("   - Testez avec des données réelles")
        instructions.append("")

    return "\\n".join(instructions)

def create_detailed_descriptions():
    """Créer des descriptions détaillées pour tous les packs"""

    packs_dir = Path("/var/www/automatehub/PACKS_WORKFLOWS_VENDEURS")

    if not packs_dir.exists():
        print("❌ Répertoire des packs non trouvé!")
        return

    print("🔍 CRÉATION DES DESCRIPTIONS DÉTAILLÉES")
    print("📋 Analyse de chaque workflow pour instructions d'installation")
    print()

    for pack_dir in packs_dir.iterdir():
        if not pack_dir.is_dir() or pack_dir.name.endswith('.md'):
            continue

        print(f"📦 Traitement du pack: {pack_dir.name}")

        # Analyser tous les workflows du pack
        workflows_analysis = []
        workflow_files = list(pack_dir.glob("*.json"))

        for workflow_file in workflow_files:
            print(f"   🔍 Analyse: {workflow_file.name}")
            analysis = analyze_workflow_details(workflow_file)
            analysis['filename'] = workflow_file.name
            workflows_analysis.append(analysis)

        # Créer le README détaillé pour acheteurs
        pack_name = pack_dir.name.replace('_', ' ').title()
        readme_content = f"""# {pack_name}

## 🎯 PACK PROFESSIONNEL COMPLET

Ce pack contient **{len(workflows_analysis)} workflows professionnels** prêts à l'emploi pour automatiser votre business.

## 📋 CONTENU DÉTAILLÉ DU PACK

"""

        # Détails de chaque workflow
        for i, workflow in enumerate(workflows_analysis, 1):
            readme_content += f"""### {i:02d}. {workflow['name'] if workflow['name'] != 'Sans nom' else workflow['filename'].replace('.json', '')}

**📄 Fichier**: `{workflow['filename']}`
**🎯 Description**: {', '.join(workflow['benefits'][:2]) if workflow['benefits'] else 'Workflow d\'automatisation avancé'}
**⚙️ Complexité**: {workflow['complexity']} ({workflow['node_count']} nodes)

#### 🔧 Installation et Configuration

{generate_installation_instructions(workflow)}

#### 🌐 APIs et Services Requis
"""

            if workflow['apis']:
                readme_content += "**APIs utilisées**:\\n"
                for api in workflow['apis']:
                    readme_content += f"- {api}\\n"

            if workflow['credentials']:
                readme_content += "\\n**Credentials nécessaires**:\\n"
                for cred in workflow['credentials']:
                    readme_content += f"- {cred}\\n"

            if not workflow['apis'] and not workflow['credentials']:
                readme_content += "- Aucune API externe requise (workflow autonome)\\n"

            readme_content += "\\n#### 💡 Avantages et Bénéfices\\n"
            for benefit in workflow['benefits']:
                readme_content += f"- {benefit}\\n"

            readme_content += "\\n---\\n\\n"

        # Section d'aide
        readme_content += f"""
## 🆘 SUPPORT ET AIDE

### 📚 Documentation
- Chaque workflow inclut des commentaires détaillés
- Les paramètres sont pré-configurés pour un démarrage rapide
- Instructions d'installation spécifiques pour chaque service

### 🔧 Résolution de problèmes
1. **Erreur de credentials**: Vérifiez que tous les tokens sont valides
2. **Workflow inactif**: Contrôlez que tous les services externes sont accessibles
3. **Données manquantes**: Testez d'abord avec des données d'exemple

### 💬 Support Premium
- Documentation complète incluse
- Exemples d'utilisation fournis
- Configuration step-by-step détaillée

## ⚡ DÉMARRAGE RAPIDE

1. **Importez** tous les workflows dans votre instance n8n
2. **Configurez** les credentials pour les services que vous utilisez
3. **Testez** chaque workflow individuellement
4. **Activez** les workflows selon vos besoins
5. **Personnalisez** les paramètres selon votre business

## 🎊 PROFITEZ DE VOS AUTOMATIONS !

Vous avez maintenant accès à **{len(workflows_analysis)} workflows professionnels** qui vont transformer votre façon de travailler !

---
*Pack créé par des experts en automation - Qualité professionnelle garantie*
"""

        # Sauvegarder le README détaillé
        with open(pack_dir / "README.md", 'w', encoding='utf-8') as f:
            f.write(readme_content)

        # Créer le fichier pour Claude Code (prompts de vente)
        claude_prompt_content = f"""# PROMPT CLAUDE CODE - {pack_name}

## 🎯 OBJECTIF
Créer une page de vente ultra-convaincante pour ce pack de workflows d'automation.

## 📦 INFORMATIONS DU PACK

**Nom du pack**: {pack_name}
**Nombre de workflows**: {len(workflows_analysis)}
**Complexité globale**: Mélange de workflows {', '.join(set([w['complexity'] for w in workflows_analysis]))}

## 🔍 ANALYSE DÉTAILLÉE DES WORKFLOWS

"""

        # Analyse pour Claude Code
        total_apis = set()
        total_benefits = set()
        complexity_distribution = {}

        for workflow in workflows_analysis:
            claude_prompt_content += f"""### {workflow['name'] if workflow['name'] != 'Sans nom' else workflow['filename'].replace('.json', '')}

**Valeur ajoutée**: {', '.join(workflow['benefits'][:3])}
**Technologies**: {', '.join(workflow['node_types'][:5])}
**APIs intégrées**: {len(workflow['apis'])} services connectés
**Facilité d'installation**: {workflow['complexity']}

**Pitch de vente suggéré**: "Ce workflow {workflow['benefits'][0].lower() if workflow['benefits'] else 'automatise vos tâches'} en {workflow['complexity'].lower()}, vous fait gagner X heures par jour et se connecte à {len(workflow['apis'])} services professionnels."

"""
            total_apis.update(workflow['apis'])
            total_benefits.update(workflow['benefits'])
            complexity_distribution[workflow['complexity']] = complexity_distribution.get(workflow['complexity'], 0) + 1

        claude_prompt_content += f"""
## 💰 ARGUMENTS DE VENTE PRINCIPAUX

### 🎯 Proposition de valeur unique
- **{len(workflows_analysis)} workflows professionnels** en un seul pack
- **{len(total_apis)} intégrations API** différentes incluses
- **Installation guidée** avec instructions détaillées pour chaque workflow
- **Complexité adaptée**: {', '.join([f'{count} {comp}' for comp, count in complexity_distribution.items()])}

### 🚀 Bénéfices principaux à mettre en avant
"""

        for benefit in list(total_benefits)[:8]:
            claude_prompt_content += f"- {benefit}\\n"

        claude_prompt_content += f"""

### 🔧 Preuves de qualité technique
- Workflows testés et fonctionnels
- Instructions d'installation complètes
- Support de {len(total_apis)} APIs professionnelles majeures
- Configuration pré-optimisée pour démarrage immédiat

### 📈 ROI et gains de temps
- Automatisation de tâches récurrentes
- Élimination des erreurs manuelles
- Intégration de services multiples
- Workflows évolutifs et personnalisables

## ✍️ CONSIGNES POUR LA PAGE DE VENTE

### Structure recommandée:
1. **Hook**: Problème que ces workflows résolvent
2. **Solution**: Comment ce pack transforme le business
3. **Preuves**: Détails techniques et bénéfices concrets
4. **Urgence**: Pourquoi agir maintenant
5. **CTA**: Appel à l'action clair

### Ton et style:
- **Professionnel** mais accessible
- **Orienté résultats** avec métriques quand possible
- **Technique** sans être intimidant
- **Persuasif** sans être agressif

### Éléments à inclure absolument:
- Liste détaillée des {len(workflows_analysis)} workflows
- APIs et services supportés ({len(total_apis)} intégrations)
- Niveau de difficulté et temps d'installation
- Bénéfices business concrets
- Instructions de démarrage rapide

## 🎨 SUGGESTIONS CRÉATIVES

### Métaphores possibles:
- "Armée de robots digitaux qui travaillent pour vous"
- "Écosystème d'automation professionnelle"
- "Boîte à outils du business moderne"

### Hooks d'accroche:
- "Imaginez si vous aviez {len(workflows_analysis)} assistants virtuels..."
- "Que feriez-vous si vous gagniez X heures par jour?"
- "Comment les pros automatisent leur business en 2025"

---

**MISSION**: Créer une page de vente qui convertit en expliquant clairement la valeur de chaque workflow tout en restant accessible aux non-techniques.
"""

        # Sauvegarder le prompt Claude Code
        with open(pack_dir / "CLAUDE_CODE_PROMPT.md", 'w', encoding='utf-8') as f:
            f.write(claude_prompt_content)

        print(f"   ✅ README détaillé créé")
        print(f"   ✅ Prompt Claude Code créé")
        print()

    print("🎉 DESCRIPTIONS DÉTAILLÉES TERMINÉES!")
    print("✅ README.md mis à jour pour chaque pack (guide acheteur)")
    print("✅ CLAUDE_CODE_PROMPT.md créé pour chaque pack (création pages de vente)")

if __name__ == "__main__":
    create_detailed_descriptions()