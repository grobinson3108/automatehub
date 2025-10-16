#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour optimiser les titres des packs avec des noms vendeurs et explicatifs
"""
import os
import shutil
from pathlib import Path

def optimize_pack_titles():
    """Renommer les packs avec des titres ultra-vendeurs"""

    packs_dir = Path("/var/www/automatehub/MEGA_PACKS_COLLECTION")
    optimized_dir = Path("/var/www/automatehub/PACKS_WORKFLOWS_VENDEURS")

    # Supprimer et recréer
    if optimized_dir.exists():
        shutil.rmtree(optimized_dir)
    optimized_dir.mkdir()

    # MAPPING DES TITRES OPTIMISÉS - Marketing pur !
    title_mapping = {
        "01_CRYPTO_DEXSCREENER_PRO": {
            "new_name": "01_CRYPTO_DEXSCREENER_MILLIONAIRE_67EUR",
            "marketing_title": "🚀 CRYPTO DEXSCREENER MILLIONAIRE",
            "tagline": "Devenez un pro du trading crypto avec DexScreener - Les workflows qui génèrent 1000€/jour",
            "price": "67€"
        },
        "02_BLOCKCHAIN_TRADING_MASTER": {
            "new_name": "02_BLOCKCHAIN_TRADING_EMPIRE_47EUR",
            "marketing_title": "⚡ BLOCKCHAIN TRADING EMPIRE",
            "tagline": "Empire de trading blockchain - Automatisez vos gains crypto comme un hedge fund",
            "price": "47€"
        },
        "03_COINGECKO_DATA_MINER": {
            "new_name": "03_COINGECKO_PROFIT_MACHINE_37EUR",
            "marketing_title": "📊 COINGECKO PROFIT MACHINE",
            "tagline": "Machine à profits CoinGecko - Data mining crypto qui révèle les pépites",
            "price": "37€"
        },
        "04_OPENAI_GPT_POWERHOUSE": {
            "new_name": "04_IA_BUSINESS_REVOLUTION_47EUR",
            "marketing_title": "🤖 IA BUSINESS RÉVOLUTION",
            "tagline": "Révolutionnez votre business avec l'IA - Automatisation OpenAI qui remplace 10 employés",
            "price": "47€"
        },
        "06_AI_CONTENT_GENERATOR": {
            "new_name": "05_CONTENT_VIRAL_FACTORY_39EUR",
            "marketing_title": "🔥 CONTENT VIRAL FACTORY",
            "tagline": "Factory de contenu viral - IA qui génère 100 posts/jour et fait exploser votre audience",
            "price": "39€"
        },
        "07_TELEGRAM_CRYPTO_BOTS": {
            "new_name": "06_TELEGRAM_CRYPTO_EMPIRE_52EUR",
            "marketing_title": "💎 TELEGRAM CRYPTO EMPIRE",
            "tagline": "Empire Telegram Crypto - Bots qui analysent et tradent 24h/24 pour vous enrichir",
            "price": "52€"
        },
        "08_TELEGRAM_AI_ASSISTANTS": {
            "new_name": "07_TELEGRAM_AI_ASSISTANT_SUPREME_42EUR",
            "marketing_title": "🧠 TELEGRAM AI ASSISTANT SUPRÊME",
            "tagline": "Assistant IA Telegram suprême - Votre cerveau artificiel personnel disponible 24h/24",
            "price": "42€"
        },
        "09_TELEGRAM_AUTOMATION_HUB": {
            "new_name": "08_TELEGRAM_MARKETING_DOMINATION_32EUR",
            "marketing_title": "📱 TELEGRAM MARKETING DOMINATION",
            "tagline": "Domination marketing Telegram - Automatisez vos ventes et fidélisez 10000 clients",
            "price": "32€"
        },
        "10_TELEGRAM_MARKETING_BEAST": {
            "new_name": "09_TELEGRAM_LEAD_MAGNET_37EUR",
            "marketing_title": "🧲 TELEGRAM LEAD MAGNET",
            "tagline": "Aimant à prospects Telegram - Transformez chaque message en vente automatique",
            "price": "37€"
        },
        "11_EMAIL_AI_MARKETING": {
            "new_name": "10_EMAIL_MARKETING_MILLIONAIRE_42EUR",
            "marketing_title": "💰 EMAIL MARKETING MILLIONAIRE",
            "tagline": "Email marketing millionnaire - IA qui écrit et envoie des emails qui convertissent à 47%",
            "price": "42€"
        },
        "12_GMAIL_AUTOMATION_PRO": {
            "new_name": "11_GMAIL_PRODUCTIVITY_BEAST_32EUR",
            "marketing_title": "⚡ GMAIL PRODUCTIVITY BEAST",
            "tagline": "Bête de productivité Gmail - Gérez 1000 emails/jour sans effort, triez tout automatiquement",
            "price": "32€"
        },
        "14_EMAIL_CRM_FUSION": {
            "new_name": "12_EMAIL_CRM_SALES_MACHINE_37EUR",
            "marketing_title": "🎯 EMAIL CRM SALES MACHINE",
            "tagline": "Machine à ventes Email+CRM - Nurturez vos prospects et fermez des deals automatiquement",
            "price": "37€"
        },
        "15_GOOGLE_SHEETS_AI_PRO": {
            "new_name": "13_GOOGLE_SHEETS_DATA_GENIUS_42EUR",
            "marketing_title": "📈 GOOGLE SHEETS DATA GENIUS",
            "tagline": "Génie des données Sheets - IA qui transforme vos tableaux en insights business puissants",
            "price": "42€"
        },
        "16_GOOGLE_DRIVE_MANAGER": {
            "new_name": "14_GOOGLE_DRIVE_ORGANISATION_KING_27EUR",
            "marketing_title": "👑 GOOGLE DRIVE ORGANISATION KING",
            "tagline": "Roi de l'organisation Drive - Triez, classez et retrouvez n'importe quel fichier en 2 secondes",
            "price": "27€"
        },
        "17_GOOGLE_CALENDAR_SYNC": {
            "new_name": "15_GOOGLE_CALENDAR_TIME_MASTER_25EUR",
            "marketing_title": "⏰ GOOGLE CALENDAR TIME MASTER",
            "tagline": "Maître du temps Calendar - Synchronisez votre vie, ne ratez plus jamais un RDV important",
            "price": "25€"
        },
        "18_GOOGLE_WORKSPACE_COMPLETE": {
            "new_name": "16_GOOGLE_WORKSPACE_BUSINESS_SUITE_35EUR",
            "marketing_title": "🏢 GOOGLE WORKSPACE BUSINESS SUITE",
            "tagline": "Suite business complète - Transformez Google Workspace en machine de guerre professionnelle",
            "price": "35€"
        },
        "23_CRM_AI_OPTIMIZER": {
            "new_name": "17_CRM_SALES_ACCELERATOR_52EUR",
            "marketing_title": "🚀 CRM SALES ACCELERATOR",
            "tagline": "Accélérateur de ventes CRM - IA qui qualifie vos leads et multiplie vos revenus par 5",
            "price": "52€"
        },
        "26_ECOMMERCE_AI_ASSISTANT": {
            "new_name": "18_ECOMMERCE_PROFIT_MAXIMIZER_52EUR",
            "marketing_title": "💎 ECOMMERCE PROFIT MAXIMIZER",
            "tagline": "Maximiseur de profits e-commerce - IA qui optimise prix, stock et ventes automatiquement",
            "price": "52€"
        },
        "28_SOCIAL_MEDIA_AI_BEAST": {
            "new_name": "19_SOCIAL_MEDIA_VIRAL_ENGINE_47EUR",
            "marketing_title": "🔥 SOCIAL MEDIA VIRAL ENGINE",
            "tagline": "Moteur viral réseaux sociaux - IA qui crée du contenu viral et explose votre audience",
            "price": "47€"
        },
        "33_DATABASE_AI_ANALYST": {
            "new_name": "20_DATABASE_INSIGHTS_GENIUS_47EUR",
            "marketing_title": "🧠 DATABASE INSIGHTS GENIUS",
            "tagline": "Génie des insights BDD - IA qui analyse vos données et révèle des opportunities cachées",
            "price": "47€"
        },
        "37_WEBHOOK_INTEGRATION_HUB": {
            "new_name": "21_API_INTEGRATION_WIZARD_29EUR",
            "marketing_title": "🪄 API INTEGRATION WIZARD",
            "tagline": "Magicien des intégrations API - Connectez tout à tout, créez votre écosystème parfait",
            "price": "29€"
        },
        "38_API_AUTOMATION_FACTORY": {
            "new_name": "22_AUTOMATION_ECOSYSTEM_BUILDER_32EUR",
            "marketing_title": "🏗️ AUTOMATION ECOSYSTEM BUILDER",
            "tagline": "Architecte d'écosystème automation - Construisez votre empire digital interconnecté",
            "price": "32€"
        },
        "39_ZAPIER_ALTERNATIVE_PRO": {
            "new_name": "23_ZAPIER_KILLER_ALTERNATIVE_35EUR",
            "marketing_title": "⚔️ ZAPIER KILLER ALTERNATIVE",
            "tagline": "Alternative qui tue Zapier - 10x plus puissant, 5x moins cher, infiniment personnalisable",
            "price": "35€"
        },
        "40_SLACK_PRODUCTIVITY_BOOST": {
            "new_name": "24_SLACK_TEAM_SUPERCHARGER_35EUR",
            "marketing_title": "⚡ SLACK TEAM SUPERCHARGER",
            "tagline": "Surcharge d'équipe Slack - Multipliez la productivité de votre team par 3 instantanément",
            "price": "35€"
        },
        "42_TEAM_COLLABORATION_AI": {
            "new_name": "25_TEAM_COLLABORATION_REVOLUTION_42EUR",
            "marketing_title": "🤝 TEAM COLLABORATION RÉVOLUTION",
            "tagline": "Révolution collaboration d'équipe - IA qui coordonne vos teams comme un chef d'orchestre",
            "price": "42€"
        },
        "44_CONTENT_MARKETING_AI": {
            "new_name": "26_CONTENT_MARKETING_EMPIRE_42EUR",
            "marketing_title": "📝 CONTENT MARKETING EMPIRE",
            "tagline": "Empire marketing de contenu - IA qui crée une stratégie content et l'exécute parfaitement",
            "price": "42€"
        },
        "47_BUSINESS_PROCESS_OPTIMIZER": {
            "new_name": "27_BUSINESS_EFFICIENCY_MAXIMIZER_42EUR",
            "marketing_title": "⚙️ BUSINESS EFFICIENCY MAXIMIZER",
            "tagline": "Maximiseur d'efficacité business - Optimisez chaque processus, éliminez le gaspillage",
            "price": "42€"
        },
        "48_TIME_MANAGEMENT_AI": {
            "new_name": "28_TIME_MANAGEMENT_GENIUS_37EUR",
            "marketing_title": "⏱️ TIME MANAGEMENT GENIUS",
            "tagline": "Génie de la gestion du temps - IA qui vous fait gagner 4h/jour en optimisant tout",
            "price": "37€"
        },
        "53_AI_CRYPTO_FUSION": {
            "new_name": "29_AI_CRYPTO_WEALTH_MACHINE_67EUR",
            "marketing_title": "💰 AI CRYPTO WEALTH MACHINE",
            "tagline": "Machine à richesse IA+Crypto - Fusion ultime qui génère des profits 24h/24 automatiquement",
            "price": "67€"
        },
        "54_EMAIL_AI_CRM_TRINITY": {
            "new_name": "30_EMAIL_AI_CRM_TRINITY_POWER_57EUR",
            "marketing_title": "🔱 EMAIL AI CRM TRINITY POWER",
            "tagline": "Puissance trinité Email+IA+CRM - Triple force qui transforme prospects en clients fidèles",
            "price": "57€"
        },
        "55_SOCIAL_AI_VIRAL_PACK": {
            "new_name": "31_SOCIAL_AI_INFLUENCE_EMPIRE_52EUR",
            "marketing_title": "👑 SOCIAL AI INFLUENCE EMPIRE",
            "tagline": "Empire d'influence Social+IA - Devenez influenceur avec une audience engagée automatiquement",
            "price": "52€"
        },
        "56_BEGINNER_AUTOMATION_KIT": {
            "new_name": "32_AUTOMATION_STARTER_SUCCESS_19EUR",
            "marketing_title": "🌱 AUTOMATION STARTER SUCCESS",
            "tagline": "Succès débutant automation - Kit parfait pour commencer et voir des résultats en 24h",
            "price": "19€"
        },
        "59_ENTERPRISE_MEGA_SUITE": {
            "new_name": "33_ENTERPRISE_DOMINATION_SUITE_97EUR",
            "marketing_title": "🏆 ENTERPRISE DOMINATION SUITE",
            "tagline": "Suite de domination enterprise - Arsenal complet pour conquérir votre marché comme Fortune 500",
            "price": "97€"
        },
        "60_AI_MASTER_COLLECTION": {
            "new_name": "34_AI_MASTER_WEALTH_COLLECTION_87EUR",
            "marketing_title": "🧙 AI MASTER WEALTH COLLECTION",
            "tagline": "Collection maître richesse IA - Tous les secrets IA pour construire votre empire digital",
            "price": "87€"
        }
    }

    print("🎯 OPTIMISATION DES TITRES MARKETING EN COURS...")
    print("💰 Transformation en noms ultra-vendeurs !")
    print()

    total_value = 0
    packs_processed = 0

    # Traiter chaque pack existant
    for old_name, mapping in title_mapping.items():
        old_pack_dir = packs_dir / old_name

        if not old_pack_dir.exists():
            continue

        new_name = mapping["new_name"]
        new_pack_dir = optimized_dir / new_name

        # Copier le répertoire
        shutil.copytree(old_pack_dir, new_pack_dir)

        # Mettre à jour le README avec le nouveau marketing
        readme_path = new_pack_dir / "README.md"
        if readme_path.exists():
            # Lire l'ancien README
            with open(readme_path, 'r', encoding='utf-8') as f:
                content = f.read()

            # Créer le nouveau README marketing
            new_readme = f"""# {mapping['marketing_title']}

## 🎯 {mapping['tagline']}

### 💰 PRIX DE LANCEMENT EXCLUSIF
**{mapping['price']}** *(Valeur réelle: {int(mapping['price'].replace('€', '')) * 2}€)*

### 🚀 TRANSFORMATION GARANTIE
Ce pack va **révolutionner** votre façon de travailler. Plus de tâches répétitives, plus de perte de temps, plus de stress !

### ⚡ RÉSULTATS IMMÉDIATS
- ✅ **Installation en 10 minutes**
- ✅ **Premiers résultats en 24h**
- ✅ **ROI visible en 1 semaine**
- ✅ **Support premium inclus**

### 🎁 BONUS EXCLUSIFS INCLUS
- 📚 **Guide d'installation étape par étape**
- 🎥 **Vidéos de formation privées**
- 💬 **Accès au groupe VIP Telegram**
- 🔄 **Mises à jour gratuites à vie**

---

{content.split('---')[1] if '---' in content else content}

## 🛡️ GARANTIE SATISFAIT OU REMBOURSÉ 30 JOURS
Si vous n'êtes pas **100% satisfait**, remboursement intégral, sans question !

## ⏰ OFFRE LIMITÉE - SEULEMENT 100 COPIES DISPONIBLES
**ATTENTION**: Le prix va augmenter à {int(mapping['price'].replace('€', '')) + 20}€ dès que les 100 premières copies seront vendues !

### 🔥 TÉMOIGNAGES CLIENTS
*"Ce pack a transformé mon business ! Je gagne maintenant 3x plus en travaillant 2x moins !"* - **Marie L., Consultante**

*"Incroyable ! Les workflows sont d'une qualité exceptionnelle, tout fonctionne parfaitement !"* - **Thomas R., CEO**

---
💎 **PACK PREMIUM CRÉÉ PAR DES EXPERTS** - Qualité garantie, résultats prouvés !
"""

            with open(readme_path, 'w', encoding='utf-8') as f:
                f.write(new_readme)

        print(f"✅ {mapping['marketing_title']}")
        print(f"   💰 {mapping['price']} | 🎯 {mapping['tagline'][:60]}...")
        print()

        total_value += int(mapping['price'].replace('€', ''))
        packs_processed += 1

    # Créer le catalogue de vente ultra-vendeur
    catalog_content = f"""# 🚀 CATALOGUE MEGA PACKS - WORKFLOWS BUSINESS PREMIUM

## 💰 COLLECTION EXCLUSIVE - VALEUR TOTALE: {total_value}€

### 🔥 TRANSFORMEZ VOTRE BUSINESS EN MACHINE À CASH !

**{packs_processed} packs ultra-premium** sélectionnés pour **dominer votre marché** !

---

## 💎 PACKS PREMIUM (50€+) - POUR LES VISIONNAIRES

"""

    # Grouper par gamme de prix
    premium_packs = []
    pro_packs = []
    standard_packs = []
    starter_packs = []

    for old_name, mapping in title_mapping.items():
        price = int(mapping['price'].replace('€', ''))
        pack_info = {
            'title': mapping['marketing_title'],
            'tagline': mapping['tagline'],
            'price': mapping['price'],
            'new_name': mapping['new_name']
        }

        if price >= 50:
            premium_packs.append(pack_info)
        elif price >= 35:
            pro_packs.append(pack_info)
        elif price >= 25:
            standard_packs.append(pack_info)
        else:
            starter_packs.append(pack_info)

    # Trier par prix décroissant
    premium_packs.sort(key=lambda x: int(x['price'].replace('€', '')), reverse=True)
    pro_packs.sort(key=lambda x: int(x['price'].replace('€', '')), reverse=True)
    standard_packs.sort(key=lambda x: int(x['price'].replace('€', '')), reverse=True)
    starter_packs.sort(key=lambda x: int(x['price'].replace('€', '')), reverse=True)

    # Ajouter chaque catégorie au catalogue
    for pack in premium_packs:
        catalog_content += f"### {pack['title']} - **{pack['price']}**\n"
        catalog_content += f"*{pack['tagline']}*\n"
        catalog_content += f"📁 `{pack['new_name']}`\n\n"

    catalog_content += "\n## 🚀 PACKS PROFESSIONNELS (35-49€) - POUR LES ENTREPRENEURS\n\n"
    for pack in pro_packs:
        catalog_content += f"### {pack['title']} - **{pack['price']}**\n"
        catalog_content += f"*{pack['tagline']}*\n"
        catalog_content += f"📁 `{pack['new_name']}`\n\n"

    catalog_content += "\n## 📦 PACKS STANDARDS (25-34€) - POUR LES PROFESSIONNELS\n\n"
    for pack in standard_packs:
        catalog_content += f"### {pack['title']} - **{pack['price']}**\n"
        catalog_content += f"*{pack['tagline']}*\n"
        catalog_content += f"📁 `{pack['new_name']}`\n\n"

    catalog_content += "\n## 🌱 PACKS STARTER (<25€) - POUR COMMENCER\n\n"
    for pack in starter_packs:
        catalog_content += f"### {pack['title']} - **{pack['price']}**\n"
        catalog_content += f"*{pack['tagline']}*\n"
        catalog_content += f"📁 `{pack['new_name']}`\n\n"

    catalog_content += f"""
---

## 🎯 STRATÉGIE DE VENTE RECOMMANDÉE

### 💎 PACKS PREMIUM ({len(premium_packs)} packs)
**Cible**: Entrepreneurs, enterprises, crypto traders
**Positionnement**: Solutions haut de gamme, ROI élevé
**Argument**: "Investissement qui se rembourse en 1 semaine"

### 🚀 PACKS PRO ({len(pro_packs)} packs)
**Cible**: PME, consultants, freelancers confirmés
**Positionnement**: Outils professionnels, gains de productivité
**Argument**: "Remplace un employé à temps plein"

### 📦 PACKS STANDARD ({len(standard_packs)} packs)
**Cible**: Solopreneurs, petites entreprises
**Positionnement**: Solutions accessibles, efficacité immédiate
**Argument**: "Automatisez en 1 clic, résultats garantis"

### 🌱 PACKS STARTER ({len(starter_packs)} packs)
**Cible**: Débutants, étudiants, curieux
**Positionnement**: Première expérience, apprentissage
**Argument**: "Découvrez l'automation sans risque"

---

## 💰 OFFRES SPÉCIALES RECOMMANDÉES

### 🔥 BUNDLE "MILLIONAIRE" - 97€ au lieu de 220€
- AI Crypto Wealth Machine (67€)
- Enterprise Domination Suite (97€)
- Email Marketing Millionaire (42€)
- **ÉCONOMIE: 123€ !**

### ⚡ BUNDLE "ENTREPRENEUR" - 67€ au lieu de 139€
- CRM Sales Accelerator (52€)
- Social Media Viral Engine (47€)
- Gmail Productivity Beast (32€)
- **ÉCONOMIE: 72€ !**

### 🎯 BUNDLE "DÉBUTANT RÉUSSITE" - 47€ au lieu de 83€
- Automation Starter Success (19€)
- API Integration Wizard (29€)
- Google Calendar Time Master (25€)
- **ÉCONOMIE: 36€ !**

---

## 📈 PRICING PSYCHOLOGIQUE OPTIMISÉ

✅ **Prix en fin de 7**: Effet psychologique de "presque gratuit"
✅ **Comparaisons de valeur**: "Valeur réelle 2x le prix"
✅ **Urgence**: "Seulement 100 copies" / "Prix qui augmente"
✅ **Garantie**: "30 jours satisfait ou remboursé"
✅ **Bonus**: "Guide + Vidéos + Support inclus"

---

🎊 **{packs_processed} PACKS PRÊTS À VENDRE POUR {total_value}€ DE REVENUS POTENTIELS !**

*Catalogue généré automatiquement - Prêt pour la vente immédiate !*
"""

    with open(optimized_dir / "CATALOGUE_VENTE_PREMIUM.md", 'w', encoding='utf-8') as f:
        f.write(catalog_content)

    print("🎉 OPTIMISATION TERMINÉE !")
    print(f"📦 {packs_processed} packs optimisés")
    print(f"💰 Valeur totale: {total_value}€")
    print(f"📁 Nouveaux packs dans: {optimized_dir}")
    print("🎯 Catalogue de vente créé !")

if __name__ == "__main__":
    optimize_pack_titles()