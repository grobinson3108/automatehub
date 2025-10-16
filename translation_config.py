#!/usr/bin/env python3
"""
Configuration avancée pour les traductions de workflows n8n
Permet de personnaliser et d'étendre les mappings de traduction
"""

from typing import Dict, List, Pattern
import re

class TranslationConfig:
    """Configuration des traductions personnalisées"""
    
    def __init__(self):
        self.load_custom_mappings()
    
    def load_custom_mappings(self):
        """Charge les mappings personnalisés basés sur l'analyse des workflows"""
        
        # Traductions spécifiques pour les workflows d'automatisation
        self.automation_translations = {
            # Workflows spécifiques identifiés
            'Simple OpenAI Image Generator': 'Générateur d\'Images OpenAI Simple',
            'Typeform to Airtable': 'Typeform vers Airtable',
            'Notion Time Tracking': 'Suivi du Temps Notion',
            'Binary File Writer': 'Écriture de Fichier Binaire',
            'API Request Data Transformation': 'Transformation de Données via API',
            
            # Actions et opérations courantes
            'Image Generator': 'Générateur d\'Images',
            'Form Trigger': 'Déclencheur de Formulaire',
            'Prompt and options': 'Prompt et options',
            'Image Generation': 'Génération d\'Images',
            'Convert to File': 'Convertir en Fichier',
            'Return to form': 'Retourner au formulaire',
            
            # Messages et interfaces utilisateur
            'OpenAI Image Generator': 'Générateur d\'Images OpenAI',
            'Here is the created image:': 'Voici l\'image créée :',
            'Snow-covered mountain village in the Alps': 'Village de montagne enneigé dans les Alpes',
            
            # Termes techniques spécifiques
            'Image size': 'Taille d\'image',
            'placeholder': 'texte d\'exemple',
            'required field': 'champ obligatoire',
            'dropdown': 'liste déroulante',
            'field options': 'options du champ',
            'body parameters': 'paramètres du corps',
            'send headers': 'envoyer les en-têtes',
            'send body': 'envoyer le corps',
            'predefined credential type': 'type d\'identifiant prédéfini',
            'node credential type': 'type d\'identifiant du nœud',
            'return binary': 'retourner binaire',
            'completion title': 'titre de finalisation',
            'completion message': 'message de finalisation',
            'respond with': 'répondre avec',
            
            # Documentation et notes
            'Welcome to my Simple OpenAI Image Generator Workflow!': 'Bienvenue dans mon Workflow de Génération d\'Images OpenAI Simple !',
            'This workflow creates an image': 'Ce workflow crée une image',
            'based on a form input': 'basé sur une saisie de formulaire',
            'This workflow has the following sequence:': 'Ce workflow suit la séquence suivante :',
            'Form trigger (image prompt and image size input)': 'Déclencheur de formulaire (prompt d\'image et saisie de taille)',
            'Generate the Image via OpenAI API': 'Générer l\'image via l\'API OpenAI',
            'Return the image to the input form for download': 'Retourner l\'image au formulaire de saisie pour téléchargement',
            'The following accesses are required for the workflow:': 'Les accès suivants sont requis pour le workflow :',
            'OpenAI API access': 'Accès API OpenAI',
            'Documentation': 'Documentation',
            'You can contact me via LinkedIn': 'Vous pouvez me contacter via LinkedIn',
            'if you have any questions': 'si vous avez des questions'
        }
        
        # Patterns pour identifier les types de contenu
        self.content_patterns = {
            'workflow_description': re.compile(r'This workflow.*?\.', re.IGNORECASE | re.DOTALL),
            'sequence_description': re.compile(r'following sequence:.*?(\n\n|\Z)', re.IGNORECASE | re.DOTALL),
            'requirements': re.compile(r'required.*?:', re.IGNORECASE),
            'contact_info': re.compile(r'contact.*?:', re.IGNORECASE),
            'welcome_message': re.compile(r'Welcome to.*?!', re.IGNORECASE),
        }
        
        # Traductions pour les types de nodes spécifiques
        self.node_type_translations = {
            'n8n-nodes-base.formTrigger': {
                'display_name': 'Déclencheur de Formulaire',
                'common_names': ['Form Trigger', 'Formulaire']
            },
            'n8n-nodes-base.httpRequest': {
                'display_name': 'Requête HTTP',
                'common_names': ['HTTP Request', 'API Call', 'Web Request']
            },
            'n8n-nodes-base.convertToFile': {
                'display_name': 'Convertir en Fichier',
                'common_names': ['Convert to File', 'File Converter']
            },
            'n8n-nodes-base.form': {
                'display_name': 'Formulaire',
                'common_names': ['Form', 'Web Form']
            },
            'n8n-nodes-base.stickyNote': {
                'display_name': 'Note Adhésive',
                'common_names': ['Sticky Note', 'Note', 'Comment']
            },
            'n8n-nodes-base.set': {
                'display_name': 'Définir',
                'common_names': ['Set', 'Set Variable', 'Define']
            },
            'n8n-nodes-base.if': {
                'display_name': 'Si',
                'common_names': ['If', 'Condition', 'Conditional']
            },
            'n8n-nodes-base.switch': {
                'display_name': 'Aiguillage',
                'common_names': ['Switch', 'Router', 'Conditional Router']
            }
        }
        
        # Traductions pour les paramètres courants
        self.parameter_translations = {
            'formTitle': 'Titre du formulaire',
            'formFields': 'Champs du formulaire',
            'fieldLabel': 'Libellé du champ',
            'fieldType': 'Type de champ',
            'fieldOptions': 'Options du champ',
            'placeholder': 'Texte d\'exemple',
            'requiredField': 'Champ obligatoire',
            'completionTitle': 'Titre de finalisation',
            'completionMessage': 'Message de finalisation',
            'respondWith': 'Répondre avec',
            'returnBinary': 'Retourner binaire',
            'bodyParameters': 'Paramètres du corps',
            'sendHeaders': 'Envoyer les en-têtes',
            'sendBody': 'Envoyer le corps',
            'authentication': 'Authentification',
            'nodeCredentialType': 'Type d\'identifiant du nœud',
            'predefinedCredentialType': 'Type d\'identifiant prédéfini'
        }
        
        # Expressions à ne jamais traduire
        self.never_translate = {
            # Expressions n8n
            r'\{\{.*?\}\}',
            r'\$json',
            r'\$node',
            r'\$input',
            r'\$parameter',
            r'\$workflow',
            r'\$vars',
            r'\$now',
            r'\$today',
            r'\$binary',
            
            # URLs et domaines
            r'https?://[^\s]+',
            r'www\.[^\s]+',
            r'[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}',
            
            # Emails
            r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}',
            
            # Tailles d'images
            r'\d+x\d+',
            
            # Modèles IA
            r'gpt-\w+',
            r'dall-e-\d+',
            
            # APIs et endpoints
            r'/v\d+/',
            r'/api/',
            
            # IDs et tokens
            r'[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}',
            r'[a-zA-Z0-9]{16,}'
        }
    
    def get_translation(self, text: str, context: str = '') -> str:
        """Récupère la traduction pour un texte donné"""
        if not text or not isinstance(text, str):
            return text
        
        # Vérifier si on ne doit jamais traduire ce texte
        for pattern in self.never_translate:
            if re.search(pattern, text):
                return text
        
        # Recherche exacte dans les traductions d'automatisation
        if text in self.automation_translations:
            return self.automation_translations[text]
        
        # Recherche par contexte
        if context == 'parameter' and text in self.parameter_translations:
            return self.parameter_translations[text]
        
        return text
    
    def get_node_translation(self, node_type: str, node_name: str) -> str:
        """Récupère la traduction pour un node basée sur son type"""
        if node_type in self.node_type_translations:
            node_info = self.node_type_translations[node_type]
            
            # Si le nom correspond à un nom commun, utiliser le nom d'affichage
            for common_name in node_info['common_names']:
                if common_name.lower() == node_name.lower():
                    return node_info['display_name']
        
        return node_name
    
    def is_markdown_content(self, text: str) -> bool:
        """Détermine si le texte contient du Markdown"""
        markdown_patterns = [
            r'^#+ ',  # Headers
            r'\*\*.*?\*\*',  # Bold
            r'\[.*?\]\(.*?\)',  # Links
            r'^- ',  # Lists
            r'^\d+\. ',  # Numbered lists
        ]
        
        return any(re.search(pattern, text, re.MULTILINE) for pattern in markdown_patterns)
    
    def translate_markdown(self, text: str) -> str:
        """Traduit le contenu Markdown en préservant le formatage"""
        if not self.is_markdown_content(text):
            return text
        
        # Traductions spécifiques pour le contenu Markdown
        markdown_translations = {
            'Welcome to my': 'Bienvenue dans mon',
            'This workflow': 'Ce workflow',
            'has the following sequence': 'suit la séquence suivante',
            'The following accesses are required': 'Les accès suivants sont requis',
            'for the workflow': 'pour le workflow',
            'You can contact me': 'Vous pouvez me contacter',
            'if you have any questions': 'si vous avez des questions'
        }
        
        translated_text = text
        for en_phrase, fr_phrase in markdown_translations.items():
            translated_text = re.sub(
                re.escape(en_phrase), 
                fr_phrase, 
                translated_text, 
                flags=re.IGNORECASE
            )
        
        return translated_text