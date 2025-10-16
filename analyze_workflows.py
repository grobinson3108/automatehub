#!/usr/bin/env python3
"""
Analyseur de workflows pour créer un dictionnaire de traductions personnalisées
Examine tous les workflows pour identifier les termes à traduire
"""

import json
from pathlib import Path
from collections import Counter, defaultdict
from typing import Dict, Set, List, Any
import re

class WorkflowAnalyzer:
    """Analyse les workflows pour extraire les termes à traduire"""
    
    def __init__(self):
        self.node_names = Counter()
        self.workflow_names = []
        self.form_labels = Counter()
        self.notes_content = []
        self.parameter_values = Counter()
        self.unique_terms = set()
        self.n8n_expressions = set()
        
    def extract_text_from_value(self, value: Any, context: str = ''):
        """Extrait le texte traduisible d'une valeur"""
        if isinstance(value, str) and value.strip():
            # Identifier les expressions n8n
            if re.search(r'\{\{.*?\}\}|\$\w+|^=', value):
                self.n8n_expressions.add(value)
            else:
                self.unique_terms.add(value.strip())
                if context:
                    self.parameter_values[f"{context}: {value.strip()}"] += 1
        elif isinstance(value, (bool, int, float)):
            # Ignorer les valeurs booléennes et numériques
            pass
        
        elif isinstance(value, dict):
            for k, v in value.items():
                self.extract_text_from_value(v, k)
        
        elif isinstance(value, list):
            for i, item in enumerate(value):
                self.extract_text_from_value(item, context)
    
    def analyze_node(self, node: Dict[str, Any]):
        """Analyse un node de workflow"""
        # Nom du node
        if 'name' in node and isinstance(node['name'], str):
            name = node['name'].strip()
            if name:
                self.node_names[name] += 1
                self.unique_terms.add(name)
        
        # Notes du node
        if 'notes' in node and node['notes'] and isinstance(node['notes'], str):
            notes = node['notes'].strip()
            if notes:
                self.notes_content.append(notes)
                self.unique_terms.add(notes)
        
        # notesInFlow
        if 'notesInFlow' in node and node['notesInFlow'] and isinstance(node['notesInFlow'], str):
            notes_flow = node['notesInFlow'].strip()
            if notes_flow:
                self.unique_terms.add(notes_flow)
        
        # Paramètres
        if 'parameters' in node:
            self.extract_text_from_value(node['parameters'], 'parameters')
    
    def analyze_workflow(self, workflow: Dict[str, Any]):
        """Analyse un workflow complet"""
        # Nom du workflow
        if 'name' in workflow:
            name = workflow['name'].strip()
            self.workflow_names.append(name)
            self.unique_terms.add(name)
        
        # Nodes
        if 'nodes' in workflow:
            for node in workflow['nodes']:
                self.analyze_node(node)
        
        # pinData
        if 'pinData' in workflow and workflow['pinData']:
            self.extract_text_from_value(workflow['pinData'], 'pinData')
    
    def analyze_file(self, file_path: Path):
        """Analyse un fichier workflow"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                workflow = json.load(f)
            
            self.analyze_workflow(workflow)
            return True
        
        except Exception as e:
            print(f"Erreur lors de l'analyse de {file_path}: {e}")
            return False
    
    def analyze_directory(self, directory: Path) -> Dict[str, Any]:
        """Analyse tous les workflows d'un répertoire"""
        json_files = list(directory.glob('*.json'))
        
        print(f"Analyse de {len(json_files)} workflows...")
        
        success_count = 0
        for json_file in json_files:
            if self.analyze_file(json_file):
                success_count += 1
        
        return {
            'total_files': len(json_files),
            'analyzed_files': success_count,
            'unique_terms_count': len(self.unique_terms),
            'node_names_count': len(self.node_names),
            'workflow_names_count': len(self.workflow_names)
        }
    
    def generate_translation_dictionary(self) -> Dict[str, str]:
        """Génère un dictionnaire de traductions basé sur l'analyse"""
        translation_dict = {}
        
        # Traductions courantes basées sur l'analyse
        common_translations = {
            # Noms de nodes les plus fréquents
            'HTTP Request': 'Requête HTTP',
            'Set': 'Définir',
            'If': 'Si',
            'Switch': 'Aiguillage',
            'Code': 'Code',
            'Function': 'Fonction',
            'JSON': 'JSON',
            'Convert to File': 'Convertir en Fichier',
            'Webhook': 'Webhook',
            'Manual Trigger': 'Déclencheur Manuel',
            'Schedule Trigger': 'Déclencheur Programmé',
            'Form Trigger': 'Déclencheur de Formulaire',
            'Sticky Note': 'Note Adhésive',
            'Wait': 'Attendre',
            'Merge': 'Fusionner',
            'Split In Batches': 'Diviser en Lots',
            'Filter': 'Filtrer',
            'Sort': 'Trier',
            'Limit': 'Limiter',
            'Remove Duplicates': 'Supprimer les Doublons',
            'Item Lists': 'Listes d\'Éléments',
            'Execute Command': 'Exécuter Commande',
            'Read Binary File': 'Lire Fichier Binaire',
            'Write Binary File': 'Écrire Fichier Binaire',
            'Read/Write Files from Disk': 'Lire/Écrire Fichiers sur Disque',
            
            # Termes d'interface utilisateur
            'Form Title': 'Titre du Formulaire',
            'Field Label': 'Libellé du Champ',
            'Placeholder': 'Texte d\'Exemple',
            'Required Field': 'Champ Obligatoire',
            'Field Type': 'Type de Champ',
            'Field Options': 'Options du Champ',
            'Dropdown': 'Liste Déroulante',
            'Text Input': 'Saisie de Texte',
            'Number Input': 'Saisie de Nombre',
            'Date Input': 'Saisie de Date',
            'File Upload': 'Téléchargement de Fichier',
            'Checkbox': 'Case à Cocher',
            'Radio Button': 'Bouton Radio',
            
            # Messages et réponses
            'Success Message': 'Message de Succès',
            'Error Message': 'Message d\'Erreur',
            'Completion Title': 'Titre de Finalisation',
            'Completion Message': 'Message de Finalisation',
            'Response Body': 'Corps de la Réponse',
            'Response Headers': 'En-têtes de la Réponse',
            
            # Paramètres techniques
            'Authentication': 'Authentification',
            'Credentials': 'Identifiants',
            'API Key': 'Clé API',
            'Bearer Token': 'Jeton Bearer',
            'Basic Auth': 'Authentification de Base',
            'OAuth': 'OAuth',
            'Headers': 'En-têtes',
            'Query Parameters': 'Paramètres de Requête',
            'Body Parameters': 'Paramètres du Corps',
            'Request Method': 'Méthode de Requête',
            'Content Type': 'Type de Contenu',
            'Status Code': 'Code de Statut',
            
            # Actions communes
            'Create': 'Créer',
            'Read': 'Lire',
            'Update': 'Mettre à Jour',
            'Delete': 'Supprimer',
            'List': 'Lister',
            'Search': 'Rechercher',
            'Find': 'Trouver',
            'Get': 'Obtenir',
            'Post': 'Poster',
            'Put': 'Mettre',
            'Patch': 'Modifier',
            'Send': 'Envoyer',
            'Receive': 'Recevoir',
            'Process': 'Traiter',
            'Transform': 'Transformer',
            'Convert': 'Convertir',
            'Parse': 'Parser',
            'Format': 'Formater',
            'Validate': 'Valider',
            'Execute': 'Exécuter',
            'Run': 'Exécuter',
            'Start': 'Démarrer',
            'Stop': 'Arrêter',
            'Pause': 'Mettre en Pause',
            'Resume': 'Reprendre',
            
            # États et statuts
            'Active': 'Actif',
            'Inactive': 'Inactif',
            'Enabled': 'Activé',
            'Disabled': 'Désactivé',
            'Running': 'En Cours',
            'Stopped': 'Arrêté',
            'Pending': 'En Attente',
            'Completed': 'Terminé',
            'Failed': 'Échoué',
            'Success': 'Succès',
            'Error': 'Erreur',
            'Warning': 'Avertissement',
            'Info': 'Information',
            
            # Types de données
            'String': 'Chaîne',
            'Number': 'Nombre',
            'Boolean': 'Booléen',
            'Array': 'Tableau',
            'Object': 'Objet',
            'Date': 'Date',
            'Time': 'Heure',
            'DateTime': 'Date/Heure',
            'File': 'Fichier',
            'Image': 'Image',
            'Video': 'Vidéo',
            'Audio': 'Audio',
            'Document': 'Document',
            'Binary': 'Binaire',
            'Text': 'Texte',
            'URL': 'URL',
            'Email': 'Email',
            'Phone': 'Téléphone',
            'Address': 'Adresse'
        }
        
        return common_translations
    
    def generate_report(self, output_path: Path):
        """Génère un rapport d'analyse"""
        report_lines = [
            "# Analyse des Workflows n8n",
            "=" * 50,
            "",
            "## Statistiques Générales",
            f"- Termes uniques identifiés: {len(self.unique_terms)}",
            f"- Noms de nodes différents: {len(self.node_names)}",
            f"- Workflows analysés: {len(self.workflow_names)}",
            f"- Expressions n8n trouvées: {len(self.n8n_expressions)}",
            "",
            "## Top 20 des Noms de Nodes",
        ]
        
        for name, count in self.node_names.most_common(20):
            report_lines.append(f"- {name}: {count} fois")
        
        report_lines.extend([
            "",
            "## Exemples d'Expressions n8n (à préserver)",
        ])
        
        for expr in sorted(list(self.n8n_expressions))[:10]:
            report_lines.append(f"- {expr}")
        
        report_lines.extend([
            "",
            "## Noms de Workflows",
        ])
        
        for name in sorted(set(self.workflow_names)):
            report_lines.append(f"- {name}")
        
        # Sauvegarder le rapport
        with open(output_path, 'w', encoding='utf-8') as f:
            f.write('\n'.join(report_lines))
        
        return output_path

def main():
    """Fonction principale d'analyse"""
    print("🔍 Analyse des workflows pour la traduction")
    print("=" * 50)
    
    # Chemins
    input_dir = Path('/var/www/automatehub/TOP_100_PRIORITAIRES')
    report_path = Path('/var/www/automatehub/workflow_analysis.md')
    dict_path = Path('/var/www/automatehub/translation_dictionary.json')
    
    if not input_dir.exists():
        print(f"❌ Répertoire introuvable: {input_dir}")
        return 1
    
    # Analyser les workflows
    analyzer = WorkflowAnalyzer()
    stats = analyzer.analyze_directory(input_dir)
    
    # Générer le dictionnaire de traductions
    translation_dict = analyzer.generate_translation_dictionary()
    
    # Sauvegarder le dictionnaire
    with open(dict_path, 'w', encoding='utf-8') as f:
        json.dump(translation_dict, f, indent=2, ensure_ascii=False)
    
    # Générer le rapport
    analyzer.generate_report(report_path)
    
    # Afficher les résultats
    print(f"✅ Analyse terminée")
    print(f"📊 {stats['analyzed_files']}/{stats['total_files']} fichiers analysés")
    print(f"🔤 {stats['unique_terms_count']} termes uniques identifiés")
    print(f"📝 {len(translation_dict)} traductions dans le dictionnaire")
    print(f"📄 Rapport: {report_path}")
    print(f"📚 Dictionnaire: {dict_path}")
    
    return 0

if __name__ == '__main__':
    exit(main())