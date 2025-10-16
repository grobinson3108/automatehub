#!/usr/bin/env python3
"""
Système de traduction complète des workflows n8n en français
Traduit tous les éléments textuels tout en préservant la structure et les expressions n8n
"""

import json
import os
import re
from pathlib import Path
from typing import Dict, List, Any, Optional
import logging
from datetime import datetime

# Configuration des logs
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('/var/www/automatehub/translation.log'),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger(__name__)

class WorkflowTranslator:
    """Traducteur intelligent pour workflows n8n"""
    
    def __init__(self):
        # Mapping des traductions courantes pour les noms de nodes
        self.node_name_translations = {
            # Triggers
            'Trigger': 'Déclencheur',
            'Schedule Trigger': 'Déclencheur Programmé',
            'Webhook': 'Webhook',
            'Manual Trigger': 'Déclencheur Manuel',
            'Form Trigger': 'Déclencheur Formulaire',
            'Cron': 'Programmation Cron',
            
            # Actions communes
            'Set': 'Définir',
            'Edit Fields': 'Modifier Champs',
            'If': 'Si',
            'Switch': 'Aiguillage',
            'Merge': 'Fusionner',
            'Split': 'Diviser',
            'Sort': 'Trier',
            'Filter': 'Filtrer',
            'Loop': 'Boucle',
            'Code': 'Code',
            'Function': 'Fonction',
            'Execute Command': 'Exécuter Commande',
            'HTTP Request': 'Requête HTTP',
            'Wait': 'Attendre',
            'Stop and Error': 'Arrêt et Erreur',
            
            # Convertisseurs
            'Convert to File': 'Convertir en Fichier',
            'JSON': 'JSON',
            'CSV': 'CSV',
            'XML': 'XML',
            
            # Retours et sorties
            'Return': 'Retourner',
            'Return to form': 'Retourner au formulaire',
            'Respond to Webhook': 'Répondre au Webhook',
            'Send Email': 'Envoyer Email',
            'Send SMS': 'Envoyer SMS',
            
            # Notes et documentation
            'Sticky Note': 'Note Adhésive',
            'Comment': 'Commentaire',
            
            # Opérations courantes
            'Create': 'Créer',
            'Update': 'Mettre à jour',
            'Delete': 'Supprimer',
            'Read': 'Lire',
            'Search': 'Rechercher',
            'List': 'Lister',
            'Get': 'Obtenir',
            'Send': 'Envoyer',
            'Receive': 'Recevoir',
            'Process': 'Traiter',
            'Transform': 'Transformer',
            'Validate': 'Valider',
            'Parse': 'Parser',
            'Format': 'Formater'
        }
        
        # Traductions pour les termes techniques courants
        self.technical_translations = {
            'Prompt': 'Prompt',
            'Image size': 'Taille d\'image',
            'Result': 'Résultat',
            'Options': 'Options',
            'Parameters': 'Paramètres',
            'Settings': 'Paramètres',
            'Configuration': 'Configuration',
            'Input': 'Entrée',
            'Output': 'Sortie',
            'Data': 'Données',
            'Content': 'Contenu',
            'Message': 'Message',
            'Subject': 'Sujet',
            'Body': 'Corps',
            'Title': 'Titre',
            'Description': 'Description',
            'Name': 'Nom',
            'ID': 'ID',
            'URL': 'URL',
            'Path': 'Chemin',
            'File': 'Fichier',
            'Folder': 'Dossier',
            'Database': 'Base de données',
            'Table': 'Table',
            'Query': 'Requête',
            'Response': 'Réponse',
            'Request': 'Requête',
            'Method': 'Méthode',
            'Headers': 'En-têtes',
            'Authentication': 'Authentification',
            'Credentials': 'Identifiants',
            'Token': 'Jeton',
            'API Key': 'Clé API',
            'Success': 'Succès',
            'Error': 'Erreur',
            'Warning': 'Avertissement',
            'Information': 'Information',
            'Status': 'Statut',
            'Active': 'Actif',
            'Inactive': 'Inactif',
            'Enabled': 'Activé',
            'Disabled': 'Désactivé',
            'True': 'Vrai',
            'False': 'Faux',
            'Yes': 'Oui',
            'No': 'Non'
        }
        
        # Services à ne pas traduire (noms propres)
        self.preserve_names = {
            'Gmail', 'Outlook', 'Office 365', 'Google', 'Microsoft',
            'Slack', 'Discord', 'Teams', 'Telegram', 'WhatsApp',
            'Twitter', 'Facebook', 'LinkedIn', 'Instagram',
            'OpenAI', 'ChatGPT', 'GPT', 'Claude', 'Anthropic',
            'Stripe', 'PayPal', 'Square',
            'AWS', 'Azure', 'Google Cloud',
            'Notion', 'Airtable', 'Zapier', 'IFTTT',
            'Shopify', 'WooCommerce', 'Magento',
            'Salesforce', 'HubSpot', 'Pipedrive',
            'Typeform', 'Google Forms', 'JotForm',
            'Dropbox', 'OneDrive', 'Google Drive',
            'YouTube', 'Vimeo', 'Spotify',
            'GitHub', 'GitLab', 'Bitbucket',
            'Docker', 'Kubernetes', 'Jenkins',
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis',
            'API', 'REST', 'GraphQL', 'JSON', 'XML', 'CSV', 'PDF',
            'HTTP', 'HTTPS', 'FTP', 'SSH', 'SSL', 'TLS',
            'OAuth', 'JWT', 'SAML',
            'n8n', 'Node.js', 'Python', 'JavaScript'
        }
    
    def is_n8n_expression(self, text: str) -> bool:
        """Vérifie si le texte contient une expression n8n à ne pas traduire"""
        if not isinstance(text, str):
            return False
        
        # Expressions n8n courantes
        n8n_patterns = [
            r'\{\{.*?\}\}',  # {{ expressions }}
            r'\$json',       # $json
            r'\$node',       # $node
            r'\$input',      # $input
            r'\$parameter',  # $parameter
            r'\$workflow',   # $workflow
            r'\$vars',       # $vars
            r'\$now',        # $now
            r'\$today',      # $today
            r'\$binary',     # $binary
            r'=\s*\{\{',     # Expressions commençant par ={{
        ]
        
        return any(re.search(pattern, text) for pattern in n8n_patterns)
    
    def should_preserve_text(self, text: str) -> bool:
        """Détermine si un texte doit être préservé tel quel"""
        if not isinstance(text, str):
            return False
            
        # Préserver les expressions n8n
        if self.is_n8n_expression(text):
            return True
            
        # Préserver les noms de services
        for name in self.preserve_names:
            if name.lower() in text.lower():
                return True
                
        # Préserver les URLs, emails, etc.
        url_patterns = [
            r'https?://',
            r'www\.',
            r'@[\w.-]+\.[a-zA-Z]{2,}',
            r'[\w.-]+@[\w.-]+\.[a-zA-Z]{2,}'
        ]
        
        return any(re.search(pattern, text) for pattern in url_patterns)
    
    def translate_text(self, text: str, context: str = '') -> str:
        """Traduit un texte en tenant compte du contexte"""
        if not isinstance(text, str) or not text.strip():
            return text
            
        # Ne pas traduire si c'est à préserver
        if self.should_preserve_text(text):
            return text
            
        # Traduction contextuelle pour les noms de nodes
        if context == 'node_name':
            # Recherche exacte d'abord
            if text in self.node_name_translations:
                return self.node_name_translations[text]
            
            # Recherche partielle pour les noms composés
            translated_parts = []
            words = text.split()
            
            for word in words:
                if word in self.node_name_translations:
                    translated_parts.append(self.node_name_translations[word])
                elif word in self.preserve_names:
                    translated_parts.append(word)
                elif word in self.technical_translations:
                    translated_parts.append(self.technical_translations[word])
                else:
                    translated_parts.append(word)
            
            return ' '.join(translated_parts)
        
        # Traductions techniques courantes
        for en_term, fr_term in self.technical_translations.items():
            if en_term.lower() == text.lower():
                return fr_term
        
        # Traduction de phrases courantes
        common_phrases = {
            'Welcome to my': 'Bienvenue dans mon',
            'This workflow': 'Ce workflow',
            'The following sequence': 'La séquence suivante',
            'The following accesses are required': 'Les accès suivants sont requis',
            'You can contact me': 'Vous pouvez me contacter',
            'if you have any questions': 'si vous avez des questions',
            'Here is the created image': 'Voici l\'image créée',
            'Snow-covered mountain village in the Alps': 'Village de montagne enneigé dans les Alpes'
        }
        
        for en_phrase, fr_phrase in common_phrases.items():
            if en_phrase.lower() in text.lower():
                text = text.replace(en_phrase, fr_phrase)
        
        return text
    
    def translate_node(self, node: Dict[str, Any]) -> Dict[str, Any]:
        """Traduit un node de workflow"""
        translated_node = node.copy()
        
        # Traduire le nom du node
        if 'name' in node:
            original_name = node['name']
            translated_name = self.translate_text(original_name, 'node_name')
            translated_node['name'] = translated_name
            logger.debug(f"Node name: '{original_name}' -> '{translated_name}'")
        
        # Traduire les notes
        if 'notes' in node:
            original_notes = node['notes']
            translated_notes = self.translate_text(original_notes, 'notes')
            translated_node['notes'] = translated_notes
        
        # Traduire notesInFlow
        if 'notesInFlow' in node:
            translated_node['notesInFlow'] = self.translate_text(node['notesInFlow'], 'notes')
        
        # Traduire les paramètres
        if 'parameters' in node:
            translated_node['parameters'] = self.translate_parameters(node['parameters'])
        
        return translated_node
    
    def translate_parameters(self, params: Any) -> Any:
        """Traduit récursivement les paramètres"""
        if isinstance(params, dict):
            translated = {}
            for key, value in params.items():
                # Traduire certaines clés spécifiques
                if key in ['formTitle', 'completionTitle', 'completionMessage']:
                    translated[key] = self.translate_text(str(value), 'parameter')
                elif key == 'fieldLabel':
                    translated[key] = self.translate_text(str(value), 'field_label')
                elif key == 'placeholder':
                    translated[key] = self.translate_text(str(value), 'placeholder')
                elif key == 'content' and isinstance(value, str):
                    # Pour le contenu des sticky notes
                    translated[key] = self.translate_text(value, 'content')
                else:
                    # Traduction récursive
                    translated[key] = self.translate_parameters(value)
            return translated
        
        elif isinstance(params, list):
            return [self.translate_parameters(item) for item in params]
        
        elif isinstance(params, str):
            return self.translate_text(params, 'parameter')
        
        else:
            return params
    
    def translate_workflow(self, workflow: Dict[str, Any]) -> Dict[str, Any]:
        """Traduit un workflow complet"""
        translated_workflow = workflow.copy()
        
        # Traduire le nom du workflow
        if 'name' in workflow:
            original_name = workflow['name']
            translated_name = self.translate_text(original_name, 'workflow_name')
            translated_workflow['name'] = translated_name
            logger.info(f"Workflow name: '{original_name}' -> '{translated_name}'")
        
        # Traduire tous les nodes
        if 'nodes' in workflow:
            translated_nodes = []
            for node in workflow['nodes']:
                translated_node = self.translate_node(node)
                translated_nodes.append(translated_node)
            translated_workflow['nodes'] = translated_nodes
        
        # Traduire pinData si présent
        if 'pinData' in workflow and workflow['pinData']:
            translated_workflow['pinData'] = self.translate_parameters(workflow['pinData'])
        
        return translated_workflow
    
    def translate_file(self, input_path: Path, output_path: Path) -> bool:
        """Traduit un fichier workflow"""
        try:
            logger.info(f"Traduction de {input_path.name}")
            
            # Charger le workflow
            with open(input_path, 'r', encoding='utf-8') as f:
                workflow = json.load(f)
            
            # Traduire le workflow
            translated_workflow = self.translate_workflow(workflow)
            
            # Sauvegarder le résultat
            os.makedirs(output_path.parent, exist_ok=True)
            with open(output_path, 'w', encoding='utf-8') as f:
                json.dump(translated_workflow, f, indent=2, ensure_ascii=False)
            
            logger.info(f"✅ {input_path.name} traduit avec succès")
            return True
            
        except Exception as e:
            logger.error(f"❌ Erreur lors de la traduction de {input_path.name}: {e}")
            return False
    
    def translate_directory(self, input_dir: Path, output_dir: Path) -> Dict[str, int]:
        """Traduit tous les workflows d'un répertoire"""
        stats = {'success': 0, 'error': 0, 'total': 0}
        
        # Trouver tous les fichiers JSON
        json_files = list(input_dir.glob('*.json'))
        stats['total'] = len(json_files)
        
        logger.info(f"Début de la traduction de {stats['total']} workflows")
        logger.info(f"Source: {input_dir}")
        logger.info(f"Destination: {output_dir}")
        
        for i, json_file in enumerate(json_files, 1):
            logger.info(f"[{i}/{stats['total']}] Traitement de {json_file.name}")
            
            # Nom de fichier de sortie (garder le même nom)
            output_file = output_dir / json_file.name
            
            # Traduire le fichier
            if self.translate_file(json_file, output_file):
                stats['success'] += 1
            else:
                stats['error'] += 1
        
        return stats

def main():
    """Fonction principale"""
    print("🤖 Système de traduction des workflows n8n")
    print("=" * 50)
    
    # Chemins
    input_dir = Path('/var/www/automatehub/TOP_100_PRIORITAIRES')
    output_dir = Path('/var/www/automatehub/TOP_100_FR')
    
    # Vérifier que le répertoire source existe
    if not input_dir.exists():
        print(f"❌ Répertoire source introuvable: {input_dir}")
        return 1
    
    # Créer le répertoire de destination
    output_dir.mkdir(exist_ok=True)
    
    # Initialiser le traducteur
    translator = WorkflowTranslator()
    
    # Lancer la traduction
    start_time = datetime.now()
    stats = translator.translate_directory(input_dir, output_dir)
    end_time = datetime.now()
    
    # Rapport final
    duration = (end_time - start_time).total_seconds()
    
    print("\n" + "=" * 50)
    print("📊 RAPPORT DE TRADUCTION")
    print("=" * 50)
    print(f"Workflows traités: {stats['total']}")
    print(f"✅ Succès: {stats['success']}")
    print(f"❌ Erreurs: {stats['error']}")
    print(f"⏱️  Durée: {duration:.2f} secondes")
    print(f"📁 Résultats dans: {output_dir}")
    
    if stats['error'] > 0:
        print(f"⚠️  Consultez le log pour les détails des erreurs: /var/www/automatehub/translation.log")
    
    return 0 if stats['error'] == 0 else 1

if __name__ == '__main__':
    exit(main())