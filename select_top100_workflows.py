#!/usr/bin/env python3
"""
Script pour sélectionner et traduire les 100 workflows les plus prioritaires pour YouTube
Critères de sélection : potentiel viral, simplicité, apps populaires, ROI, cas d'usage universels
"""

import json
import os
import re
import shutil
from pathlib import Path
from collections import defaultdict
from typing import Dict, List, Tuple, Optional

class WorkflowAnalyzer:
    def __init__(self):
        self.source_dir = Path("/var/www/automatehub/Freemium_Workflows")
        self.target_dir = Path("/var/www/automatehub/TOP_100_PRIORITAIRES")
        self.target_dir.mkdir(exist_ok=True)
        
        # Apps populaires et leur score de popularité
        self.popular_apps = {
            'gmail': 100, 'telegram': 95, 'googlesheets': 90, 'slack': 85,
            'openai': 95, 'googledrive': 80, 'webhook': 75, 'email': 90,
            'whatsapp': 85, 'discord': 70, 'notion': 75, 'airtable': 70,
            'hubspot': 65, 'mailchimp': 60, 'youtube': 80, 'facebook': 75,
            'instagram': 75, 'twitter': 70, 'zapier': 65, 'shopify': 60,
            'wordpress': 65, 'mysql': 50, 'postgresql': 45, 'typeform': 65
        }
        
        # Mots-clés pour le potentiel viral (problèmes courants)
        self.viral_keywords = {
            'automation': 50, 'automatic': 50, 'auto': 45, 'schedule': 40,
            'notification': 45, 'alert': 45, 'monitor': 40, 'track': 35,
            'sync': 40, 'backup': 35, 'report': 35, 'summary': 40,
            'ai': 60, 'chatbot': 55, 'assistant': 50, 'response': 40,
            'classify': 45, 'analyze': 40, 'extract': 35, 'convert': 35,
            'daily': 35, 'weekly': 30, 'reminder': 40, 'task': 45,
            'lead': 40, 'customer': 35, 'invoice': 35, 'expense': 35
        }
    
    def count_nodes(self, workflow_data: Dict) -> int:
        """Compte le nombre de nodes dans un workflow"""
        return len(workflow_data.get('nodes', []))
    
    def extract_apps_used(self, workflow_data: Dict) -> List[str]:
        """Extrait les apps utilisées dans le workflow"""
        apps = set()
        for node in workflow_data.get('nodes', []):
            node_type = node.get('type', '').lower()
            # Extraction des noms d'apps à partir du type de node
            if 'gmail' in node_type:
                apps.add('gmail')
            elif 'telegram' in node_type:
                apps.add('telegram')
            elif 'googlesheets' in node_type or 'google-sheets' in node_type:
                apps.add('googlesheets')
            elif 'slack' in node_type:
                apps.add('slack')
            elif 'openai' in node_type:
                apps.add('openai')
            elif 'googledrive' in node_type or 'google-drive' in node_type:
                apps.add('googledrive')
            elif 'webhook' in node_type:
                apps.add('webhook')
            elif 'email' in node_type or 'imap' in node_type:
                apps.add('email')
            elif 'discord' in node_type:
                apps.add('discord')
            elif 'hubspot' in node_type:
                apps.add('hubspot')
            elif 'youtube' in node_type:
                apps.add('youtube')
        return list(apps)
    
    def calculate_popularity_score(self, apps: List[str]) -> int:
        """Calcule le score de popularité basé sur les apps utilisées"""
        score = 0
        for app in apps:
            score += self.popular_apps.get(app, 0)
        return min(score, 200)  # Cap à 200
    
    def calculate_viral_potential(self, filename: str, workflow_name: str) -> int:
        """Calcule le potentiel viral basé sur les mots-clés"""
        text = (filename + " " + workflow_name).lower()
        score = 0
        for keyword, points in self.viral_keywords.items():
            if keyword in text:
                score += points
        return min(score, 150)  # Cap à 150
    
    def calculate_simplicity_score(self, node_count: int) -> int:
        """Calcule le score de simplicité (plus de points = plus simple)"""
        if node_count <= 3:
            return 100
        elif node_count <= 5:
            return 80
        elif node_count <= 8:
            return 60
        elif node_count <= 10:
            return 40
        else:
            return 0  # Trop complexe
    
    def calculate_universality_score(self, filename: str, workflow_name: str) -> int:
        """Calcule le score d'universalité (cas d'usage courants)"""
        text = (filename + " " + workflow_name).lower()
        universal_cases = {
            'log': 30, 'backup': 25, 'sync': 35, 'notification': 40,
            'alert': 35, 'reminder': 35, 'report': 30, 'summary': 30,
            'forward': 25, 'monitor': 30, 'track': 25, 'convert': 20,
            'create': 20, 'update': 20, 'send': 25, 'receive': 20
        }
        score = 0
        for case, points in universal_cases.items():
            if case in text:
                score += points
        return min(score, 100)  # Cap à 100
    
    def translate_to_french(self, filename: str) -> str:
        """Traduit le nom de fichier en français selon les règles définies"""
        # Enlever l'extension
        name = filename.replace('.json', '')
        
        # Dictionnaire de traductions spécifiques
        translations = {
            'Log_New_Gmail_Messages_to_Google_Sheets_Automatically': 'Gmail_vers_Google_Sheets_Automatique',
            'Send_Kindle_Books_via_Telegram_Bot_Commands': 'Bot_Telegram_Envoyer_Livres_Kindle',
            'Monitor_Emails_and_Send_Telegram_Alerts': 'Surveillance_Emails_Alertes_Telegram',
            'Forward_Filtered_Gmail_Messages_to_Telegram': 'Gmail_Filtre_vers_Telegram',
            'Create_Google_Task_from_New_Gmail_Message': 'Gmail_vers_Google_Tasks',
            'Email_Attachment_Upload_to_Google_Drive': 'Pieces_Jointes_vers_Google_Drive',
            'YouTube_Video_Updates_to_Telegram_Channel': 'YouTube_Notifications_Telegram',
            'Classify_Incoming_Emails_Using_OpenAI_Language_Model': 'Classification_Emails_OpenAI',
            'AI-Powered_Email_Response_with_Approval_System': 'Reponses_Email_IA_Approbation',
            'Extract_Text_from_PDF_Files': 'Extraction_Texte_PDF',
            'Typeform_to_Sheets_with_Slack_and_Email_Notifications': 'Typeform_vers_Sheets_Notifications',
            'Weather_Updates_via_Webhook_Trigger': 'Notifications_Meteo_Webhook',
            'Daily_Calendar_Summary_and_Event_Reminders': 'Resume_Calendrier_Quotidien',
            'Auto-Post_Tweets_to_Rocket.Chat_Channel': 'Tweets_Auto_vers_RocketChat',
            'Analyze_YouTube_Comments_and_Log_Sentiment_in_Sheets': 'Analyse_Commentaires_YouTube',
            'Convert_Google_Drive_Files_to_WordPress_Posts': 'Google_Drive_vers_WordPress',
            'AI_Email_Assistant_for_Gmail_Responses': 'Assistant_IA_Reponses_Gmail',
            'Telegram-Triggered_AI_Chat_with_Gmail_Integration': 'Chat_IA_Telegram_Gmail',
            'Email-Triggered_Slack_Notifications_for_Delivery_Accounting': 'Email_vers_Slack_Livraisons'
        }
        
        if name in translations:
            return translations[name] + '.json'
        
        # Traduction automatique pour les autres
        # Remplacer des termes courants
        auto_translations = {
            'gmail': 'Gmail', 'telegram': 'Telegram', 'slack': 'Slack',
            'google_sheets': 'Google_Sheets', 'googledrive': 'Google_Drive',
            'email': 'Email', 'webhook': 'Webhook', 'openai': 'OpenAI',
            'ai': 'IA', 'automatic': 'Auto', 'auto': 'Auto',
            'notification': 'Notification', 'alert': 'Alerte',
            'monitor': 'Surveillance', 'track': 'Suivi', 'log': 'Journal',
            'sync': 'Sync', 'backup': 'Sauvegarde', 'report': 'Rapport',
            'summary': 'Resume', 'convert': 'Convertir', 'extract': 'Extraire',
            'create': 'Creer', 'send': 'Envoyer', 'forward': 'Transferer',
            'analyze': 'Analyser', 'classify': 'Classifier'
        }
        
        translated = name.lower()
        for en_term, fr_term in auto_translations.items():
            translated = translated.replace(en_term, fr_term)
        
        # Nettoyer et formater
        translated = re.sub(r'[^a-zA-Z0-9_]', '_', translated)
        translated = re.sub(r'_+', '_', translated)
        translated = translated.strip('_')
        
        # Capitaliser les mots
        parts = translated.split('_')
        capitalized = [part.capitalize() for part in parts if part]
        
        return '_'.join(capitalized[:8]) + '.json'  # Limiter à 8 mots max
    
    def analyze_workflow(self, filepath: Path) -> Optional[Dict]:
        """Analyse un workflow et retourne ses metrics"""
        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                data = json.load(f)
            
            filename = filepath.name
            workflow_name = data.get('name', '')
            node_count = self.count_nodes(data)
            
            # Exclure les workflows trop complexes
            if node_count > 10:
                return None
            
            apps = self.extract_apps_used(data)
            
            # Calculer les scores
            popularity = self.calculate_popularity_score(apps)
            viral_potential = self.calculate_viral_potential(filename, workflow_name)
            simplicity = self.calculate_simplicity_score(node_count)
            universality = self.calculate_universality_score(filename, workflow_name)
            
            # Score total pondéré
            total_score = (
                popularity * 0.3 +        # 30% popularité apps
                viral_potential * 0.25 +  # 25% potentiel viral
                simplicity * 0.25 +       # 25% simplicité
                universality * 0.2        # 20% universalité
            )
            
            return {
                'filepath': filepath,
                'filename': filename,
                'workflow_name': workflow_name,
                'node_count': node_count,
                'apps': apps,
                'popularity_score': popularity,
                'viral_score': viral_potential,
                'simplicity_score': simplicity,
                'universality_score': universality,
                'total_score': total_score,
                'french_name': self.translate_to_french(filename)
            }
            
        except Exception as e:
            print(f"Erreur lors de l'analyse de {filepath}: {e}")
            return None
    
    def select_top_100(self) -> List[Dict]:
        """Sélectionne les 100 meilleurs workflows"""
        print("📊 Analyse des workflows en cours...")
        
        all_workflows = []
        json_files = list(self.source_dir.glob("*.json"))
        
        for i, filepath in enumerate(json_files, 1):
            if i % 50 == 0:
                print(f"Progression: {i}/{len(json_files)} workflows analysés")
            
            analysis = self.analyze_workflow(filepath)
            if analysis:
                all_workflows.append(analysis)
        
        # Trier par score décroissant
        all_workflows.sort(key=lambda x: x['total_score'], reverse=True)
        
        print(f"✅ {len(all_workflows)} workflows analysés")
        print(f"🎯 Sélection des TOP 100...")
        
        return all_workflows[:100]
    
    def copy_selected_workflows(self, selected: List[Dict]) -> None:
        """Copie les workflows sélectionnés vers le dossier TOP_100_PRIORITAIRES"""
        print("📂 Copie des workflows sélectionnés...")
        
        for workflow in selected:
            source_path = workflow['filepath']
            target_filename = workflow['french_name']
            target_path = self.target_dir / target_filename
            
            try:
                shutil.copy2(source_path, target_path)
                print(f"✅ {workflow['filename']} → {target_filename}")
            except Exception as e:
                print(f"❌ Erreur copie {workflow['filename']}: {e}")
    
    def generate_report(self, selected: List[Dict]) -> str:
        """Génère un rapport des workflows sélectionnés"""
        report = []
        report.append("# 🎯 TOP 100 WORKFLOWS PRIORITAIRES POUR YOUTUBE\n")
        report.append(f"**Date de sélection**: {Path().cwd()}\n")
        report.append("## 📊 Critères de sélection")
        report.append("1. **Popularité apps** (30%) : Gmail, Telegram, Sheets, OpenAI...")
        report.append("2. **Potentiel viral** (25%) : Automation, AI, notifications...")
        report.append("3. **Simplicité** (25%) : 2-10 nodes maximum")
        report.append("4. **Universalité** (20%) : Cas d'usage courants\n")
        
        report.append("## 🏆 TOP 100 SÉLECTIONNÉS\n")
        report.append("| Rang | Score | Nodes | Apps | Nom Original | Nom Français |")
        report.append("|------|-------|-------|------|--------------|--------------|")
        
        for i, workflow in enumerate(selected, 1):
            apps_str = ", ".join(workflow['apps'][:3])  # Afficher max 3 apps
            report.append(
                f"| {i:2d} | {workflow['total_score']:.1f} | "
                f"{workflow['node_count']:2d} | {apps_str} | "
                f"{workflow['filename'][:40]}... | {workflow['french_name']} |"
            )
        
        report.append("\n## 📈 Statistiques")
        
        # Stats par nombre de nodes
        node_counts = defaultdict(int)
        for w in selected:
            node_counts[w['node_count']] += 1
        
        report.append("\n### Répartition par nombre de nodes:")
        for nodes, count in sorted(node_counts.items()):
            report.append(f"- {nodes} nodes: {count} workflows")
        
        # Stats par apps
        app_counts = defaultdict(int)
        for w in selected:
            for app in w['apps']:
                app_counts[app] += 1
        
        report.append("\n### Apps les plus utilisées:")
        for app, count in sorted(app_counts.items(), key=lambda x: x[1], reverse=True)[:10]:
            report.append(f"- {app}: {count} workflows")
        
        return "\n".join(report)

def main():
    analyzer = WorkflowAnalyzer()
    
    print("🚀 Démarrage de la sélection des TOP 100 workflows")
    print("=" * 60)
    
    # Sélectionner les TOP 100
    selected_workflows = analyzer.select_top_100()
    
    if len(selected_workflows) < 100:
        print(f"⚠️  Seulement {len(selected_workflows)} workflows éligibles trouvés")
    
    # Copier les fichiers
    analyzer.copy_selected_workflows(selected_workflows)
    
    # Générer le rapport
    report = analyzer.generate_report(selected_workflows)
    
    # Sauvegarder le rapport
    report_path = Path("/var/www/automatehub/TOP_100_SELECTION_REPORT.md")
    with open(report_path, 'w', encoding='utf-8') as f:
        f.write(report)
    
    print(f"\n📋 Rapport généré: {report_path}")
    print(f"📁 Workflows copiés dans: {analyzer.target_dir}")
    print("\n🎉 Sélection terminée avec succès!")
    
    # Afficher le top 10
    print("\n🏆 TOP 10:")
    for i, workflow in enumerate(selected_workflows[:10], 1):
        print(f"{i:2d}. {workflow['french_name']} (Score: {workflow['total_score']:.1f})")

if __name__ == "__main__":
    main()