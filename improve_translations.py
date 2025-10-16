#!/usr/bin/env python3
"""
Amélioration post-traduction des workflows n8n
Corrige les traductions manquées et améliore la qualité
"""

import json
import re
from pathlib import Path
from typing import Dict, List, Any
import logging

# Configuration des logs
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class TranslationImprover:
    """Améliore les traductions existantes"""
    
    def __init__(self):
        # Traductions spécifiques pour le contenu Markdown et les notes
        self.markdown_translations = {
            'Welcome to my Simple OpenAI Image Generator Workflow!': 'Bienvenue dans mon Workflow de Génération d\'Images OpenAI Simple !',
            'This workflow creates an image with the new OpenAI image model': 'Ce workflow crée une image avec le nouveau modèle d\'image OpenAI',
            'based on a form input': 'basé sur une saisie de formulaire',
            'This workflow has the following sequence:': 'Ce workflow suit la séquence suivante :',
            'Form trigger (image prompt and image size input)': '1. Déclencheur de formulaire (prompt d\'image et saisie de taille d\'image)',
            'Generate the Image via OpenAI API': '2. Générer l\'image via l\'API OpenAI',
            'Return the image to the input form for download': '3. Retourner l\'image au formulaire de saisie pour téléchargement',
            'The following accesses are required for the workflow:': 'Les accès suivants sont requis pour le workflow :',
            'OpenAI API access': '- Accès API OpenAI',
            'You can contact me via LinkedIn, if you have any questions': 'Vous pouvez me contacter via LinkedIn si vous avez des questions',
            
            # Autres traductions courantes manquées
            'Track Working Time and Pauses': 'Suivi du Temps de Travail et des Pauses',
            'CFP Selection 1': 'Sélection CFP 1',
            'Simple OpenAI Image Generator': 'Générateur d\'Images OpenAI Simple',
            'Prompt and options': 'Prompt et options',
            'Return to form': 'Retourner au formulaire',
            
            # Noms de workflows courants
            'Email to Google Sheets Auto': 'Email vers Google Sheets Auto',
            'Webhook Google Sheets Gmail': 'Webhook Google Sheets Gmail',
            'Form to API': 'Formulaire vers API',
            'Email Monitoring Telegram Alerts': 'Surveillance Email Alertes Telegram',
            'Email to Nextcloud Deck': 'Email vers Nextcloud Deck',
            'File Operations HTTP': 'Opérations Fichiers HTTP',
            'Calculate Coordinates Center': 'Calculer Centre Coordonnées',
            'Drive Upload API Integration': 'Intégration API Upload Drive',
            'Advanced Sheets Gmail Operations': 'Opérations Sheets Gmail Avancées',
            'Notion ToDo to Slack': 'Notion ToDo vers Slack',
            'Facebook Leads Auto': 'Leads Facebook Auto',
            
            # Termes techniques
            'Image Generator': 'Générateur d\'Images',
            'Form Trigger': 'Déclencheur de Formulaire',
            'HTTP Request': 'Requête HTTP',
            'Sticky Note': 'Note Adhésive'
        }
        
        # Expressions regex pour identifier le contenu à traduire
        self.translation_patterns = [
            (r'Welcome to my ([^!]+)!', r'Bienvenue dans mon \1 !'),
            (r'This workflow creates', 'Ce workflow crée'),
            (r'This workflow has the following sequence:', 'Ce workflow suit la séquence suivante :'),
            (r'The following accesses are required', 'Les accès suivants sont requis'),
            (r'You can contact me', 'Vous pouvez me contacter'),
            (r'if you have any questions', 'si vous avez des questions'),
            (r'Here is the created image:', 'Voici l\'image créée :'),
        ]
    
    def improve_markdown_content(self, content: str) -> str:
        """Améliore la traduction du contenu Markdown"""
        if not isinstance(content, str) or not content.strip():
            return content
        
        improved_content = content
        
        # Traductions directes
        for en_text, fr_text in self.markdown_translations.items():
            if en_text in improved_content:
                improved_content = improved_content.replace(en_text, fr_text)
                logger.debug(f"Traduit: '{en_text}' -> '{fr_text}'")
        
        # Traductions par patterns regex
        for pattern, replacement in self.translation_patterns:
            improved_content = re.sub(pattern, replacement, improved_content, flags=re.IGNORECASE)
        
        return improved_content
    
    def improve_workflow_name(self, name: str) -> str:
        """Améliore la traduction du nom de workflow"""
        if not isinstance(name, str):
            return name
        
        if name in self.markdown_translations:
            return self.markdown_translations[name]
        
        # Traductions par mots-clés
        translations = {
            'Track Working Time': 'Suivi du Temps de Travail',
            'and Pauses': 'et des Pauses',
            'CFP Selection': 'Sélection CFP',
            'Simple': 'Simple',
            'Image Generator': 'Générateur d\'Images',
            'OpenAI': 'OpenAI',
            'Email to': 'Email vers',
            'Google Sheets': 'Google Sheets',
            'Auto': 'Auto',
            'Webhook': 'Webhook',
            'Gmail': 'Gmail',
            'Form to API': 'Formulaire vers API',
            'Monitoring': 'Surveillance',
            'Telegram': 'Telegram',
            'Alerts': 'Alertes',
            'Nextcloud': 'Nextcloud',
            'Deck': 'Deck',
            'File Operations': 'Opérations Fichiers',
            'HTTP': 'HTTP',
            'Calculate': 'Calculer',
            'Coordinates': 'Coordonnées',
            'Center': 'Centre',
            'Drive Upload': 'Upload Drive',
            'API Integration': 'Intégration API',
            'Advanced': 'Avancé',
            'Operations': 'Opérations',
            'Notion': 'Notion',
            'ToDo': 'ToDo',
            'to Slack': 'vers Slack',
            'Facebook Leads': 'Leads Facebook'
        }
        
        improved_name = name
        for en_term, fr_term in translations.items():
            improved_name = improved_name.replace(en_term, fr_term)
        
        return improved_name
    
    def improve_node_name(self, name: str) -> str:
        """Améliore la traduction du nom de node"""
        if not isinstance(name, str):
            return name
        
        if name in self.markdown_translations:
            return self.markdown_translations[name]
        
        # Traductions spécifiques pour les nodes
        node_translations = {
            'Prompt and options': 'Prompt et options',
            'Return to form': 'Retourner au formulaire',
            'Image Generation': 'Génération d\'Images',
            'OpenAI Image Generation': 'Génération d\'Images OpenAI',
            'Convert to File': 'Convertir en Fichier',
            'Sticky Note': 'Note Adhésive',
            'HTTP Request': 'Requête HTTP',
            'Form Trigger': 'Déclencheur de Formulaire',
            'Manual Trigger': 'Déclencheur Manuel',
            'Schedule Trigger': 'Déclencheur Programmé',
            'Webhook': 'Webhook',
            'Set': 'Définir',
            'If': 'Si',
            'Switch': 'Aiguillage',
            'Code': 'Code',
            'Function': 'Fonction',
            'Wait': 'Attendre',
            'Stop and Error': 'Arrêt et Erreur',
            'Merge': 'Fusionner',
            'Split': 'Diviser',
            'Filter': 'Filtrer',
            'Sort': 'Trier',
            'Loop': 'Boucle'
        }
        
        return node_translations.get(name, name)
    
    def improve_parameters(self, params: Any) -> Any:
        """Améliore les traductions dans les paramètres"""
        if isinstance(params, dict):
            improved = {}
            for key, value in params.items():
                if key == 'content' and isinstance(value, str):
                    # Contenu des sticky notes
                    improved[key] = self.improve_markdown_content(value)
                elif key in ['formTitle', 'completionTitle', 'completionMessage'] and isinstance(value, str):
                    # Titres et messages de formulaire
                    improved[key] = self.markdown_translations.get(value, value)
                elif key == 'fieldLabel' and isinstance(value, str):
                    # Libellés de champs
                    field_translations = {
                        'Prompt': 'Prompt',
                        'Image size': 'Taille d\'image',
                        'Email': 'Email',
                        'Subject': 'Sujet',
                        'Message': 'Message',
                        'Name': 'Nom',
                        'Date': 'Date',
                        'Time': 'Heure',
                        'Description': 'Description'
                    }
                    improved[key] = field_translations.get(value, value)
                elif key == 'placeholder' and isinstance(value, str):
                    # Textes d'exemple
                    placeholder_translations = {
                        'Snow-covered mountain village in the Alps': 'Village de montagne enneigé dans les Alpes',
                        'Enter your email': 'Entrez votre email',
                        'Enter your message': 'Entrez votre message',
                        'Enter description': 'Entrez une description'
                    }
                    improved[key] = placeholder_translations.get(value, value)
                else:
                    improved[key] = self.improve_parameters(value)
            return improved
        
        elif isinstance(params, list):
            return [self.improve_parameters(item) for item in params]
        
        elif isinstance(params, str):
            return self.improve_markdown_content(params)
        
        else:
            return params
    
    def improve_node(self, node: Dict[str, Any]) -> Dict[str, Any]:
        """Améliore les traductions d'un node"""
        improved_node = node.copy()
        
        # Améliorer le nom du node
        if 'name' in node:
            original_name = node['name']
            improved_name = self.improve_node_name(original_name)
            if improved_name != original_name:
                improved_node['name'] = improved_name
                logger.debug(f"Node name amélioré: '{original_name}' -> '{improved_name}'")
        
        # Améliorer les notes
        if 'notes' in node and isinstance(node['notes'], str):
            original_notes = node['notes']
            improved_notes = self.improve_markdown_content(original_notes)
            if improved_notes != original_notes:
                improved_node['notes'] = improved_notes
        
        # Améliorer notesInFlow
        if 'notesInFlow' in node and isinstance(node['notesInFlow'], str):
            improved_node['notesInFlow'] = self.improve_markdown_content(node['notesInFlow'])
        
        # Améliorer les paramètres
        if 'parameters' in node:
            improved_node['parameters'] = self.improve_parameters(node['parameters'])
        
        return improved_node
    
    def improve_workflow(self, workflow: Dict[str, Any]) -> Dict[str, Any]:
        """Améliore les traductions d'un workflow complet"""
        improved_workflow = workflow.copy()
        
        # Améliorer le nom du workflow
        if 'name' in workflow:
            original_name = workflow['name']
            improved_name = self.improve_workflow_name(original_name)
            if improved_name != original_name:
                improved_workflow['name'] = improved_name
                logger.info(f"Workflow name amélioré: '{original_name}' -> '{improved_name}'")
        
        # Améliorer tous les nodes
        if 'nodes' in workflow:
            improved_nodes = []
            for node in workflow['nodes']:
                improved_node = self.improve_node(node)
                improved_nodes.append(improved_node)
            improved_workflow['nodes'] = improved_nodes
        
        # Améliorer pinData si présent
        if 'pinData' in workflow and workflow['pinData']:
            improved_workflow['pinData'] = self.improve_parameters(workflow['pinData'])
        
        return improved_workflow
    
    def improve_file(self, file_path: Path) -> bool:
        """Améliore les traductions d'un fichier workflow"""
        try:
            logger.info(f"Amélioration de {file_path.name}")
            
            # Charger le workflow
            with open(file_path, 'r', encoding='utf-8') as f:
                workflow = json.load(f)
            
            # Améliorer les traductions
            improved_workflow = self.improve_workflow(workflow)
            
            # Sauvegarder les améliorations
            with open(file_path, 'w', encoding='utf-8') as f:
                json.dump(improved_workflow, f, indent=2, ensure_ascii=False)
            
            logger.info(f"✅ {file_path.name} amélioré")
            return True
            
        except Exception as e:
            logger.error(f"❌ Erreur lors de l'amélioration de {file_path.name}: {e}")
            return False
    
    def improve_directory(self, directory: Path) -> Dict[str, int]:
        """Améliore toutes les traductions d'un répertoire"""
        stats = {'success': 0, 'error': 0, 'total': 0}
        
        json_files = list(directory.glob('*.json'))
        stats['total'] = len(json_files)
        
        logger.info(f"Amélioration de {stats['total']} workflows traduits")
        
        for i, json_file in enumerate(json_files, 1):
            logger.info(f"[{i}/{stats['total']}] {json_file.name}")
            
            if self.improve_file(json_file):
                stats['success'] += 1
            else:
                stats['error'] += 1
        
        return stats

def main():
    """Fonction principale d'amélioration"""
    print("🔧 Amélioration des traductions de workflows n8n")
    print("=" * 50)
    
    # Chemin du répertoire traduit
    translated_dir = Path('/var/www/automatehub/TOP_100_FR')
    
    if not translated_dir.exists():
        print(f"❌ Répertoire traduit introuvable: {translated_dir}")
        return 1
    
    # Améliorer les traductions
    improver = TranslationImprover()
    stats = improver.improve_directory(translated_dir)
    
    # Rapport final
    print("\n" + "=" * 50)
    print("📊 RAPPORT D'AMÉLIORATION")
    print("=" * 50)
    print(f"Fichiers traités: {stats['total']}")
    print(f"✅ Améliorés avec succès: {stats['success']}")
    print(f"❌ Erreurs: {stats['error']}")
    print(f"📁 Workflows améliorés dans: {translated_dir}")
    
    return 0 if stats['error'] == 0 else 1

if __name__ == '__main__':
    exit(main())