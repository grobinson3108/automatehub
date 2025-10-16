#!/usr/bin/env python3
"""
Validation des traductions de workflows n8n
Vérifie la qualité et la cohérence des traductions
"""

import json
import re
from pathlib import Path
from typing import Dict, List, Tuple, Any
import logging

# Configuration des logs
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class TranslationValidator:
    """Validateur pour les traductions de workflows"""
    
    def __init__(self):
        self.issues = []
        self.stats = {
            'total_workflows': 0,
            'total_nodes': 0,
            'translated_names': 0,
            'preserved_expressions': 0,
            'untranslated_text': 0,
            'validation_errors': 0
        }
    
    def validate_n8n_expressions(self, text: str, context: str) -> List[str]:
        """Valide que les expressions n8n sont préservées"""
        issues = []
        
        if not isinstance(text, str):
            return issues
        
        # Patterns d'expressions n8n qui doivent être préservées
        n8n_patterns = [
            (r'\{\{.*?\}\}', 'Expression n8n'),
            (r'\$json', 'Variable $json'),
            (r'\$node', 'Variable $node'),
            (r'\$input', 'Variable $input'),
            (r'\$parameter', 'Variable $parameter'),
            (r'\$workflow', 'Variable $workflow'),
            (r'\$binary', 'Variable $binary')
        ]
        
        for pattern, description in n8n_patterns:
            matches = re.findall(pattern, text)
            if matches:
                self.stats['preserved_expressions'] += len(matches)
                # Vérifier que les expressions ne sont pas corrompues
                for match in matches:
                    if '{{' in match and '}}' not in match:
                        issues.append(f"Expression n8n incomplète dans {context}: {match}")
                    elif '$' in match and not re.match(r'\$\w+', match):
                        issues.append(f"Variable n8n malformée dans {context}: {match}")
        
        return issues
    
    def validate_json_structure(self, original: Dict[str, Any], translated: Dict[str, Any]) -> List[str]:
        """Valide que la structure JSON est préservée"""
        issues = []
        
        # Vérifier les clés principales
        required_keys = ['id', 'name', 'nodes', 'connections']
        for key in required_keys:
            if key in original and key not in translated:
                issues.append(f"Clé manquante après traduction: {key}")
            elif key in original and key in translated:
                if key == 'id' and original[key] != translated[key]:
                    issues.append(f"ID modifié: {original[key]} -> {translated[key]}")
        
        # Vérifier le nombre de nodes
        if 'nodes' in original and 'nodes' in translated:
            if len(original['nodes']) != len(translated['nodes']):
                issues.append(f"Nombre de nodes différent: {len(original['nodes'])} -> {len(translated['nodes'])}")
        
        # Vérifier les connections
        if 'connections' in original and 'connections' in translated:
            if original['connections'] != translated['connections']:
                issues.append("Connections modifiées lors de la traduction")
        
        return issues
    
    def validate_node_translations(self, original_nodes: List[Dict], translated_nodes: List[Dict]) -> List[str]:
        """Valide les traductions des nodes"""
        issues = []
        
        if len(original_nodes) != len(translated_nodes):
            issues.append(f"Nombre de nodes différent: {len(original_nodes)} vs {len(translated_nodes)}")
            return issues
        
        for orig_node, trans_node in zip(original_nodes, translated_nodes):
            # Vérifier que l'ID est préservé
            if orig_node.get('id') != trans_node.get('id'):
                issues.append(f"ID de node modifié: {orig_node.get('id')} -> {trans_node.get('id')}")
            
            # Vérifier que le type est préservé
            if orig_node.get('type') != trans_node.get('type'):
                issues.append(f"Type de node modifié: {orig_node.get('type')} -> {trans_node.get('type')}")
            
            # Vérifier les positions
            if orig_node.get('position') != trans_node.get('position'):
                issues.append(f"Position de node modifiée pour {orig_node.get('name', 'Unknown')}")
            
            # Valider les expressions n8n dans les paramètres
            if 'parameters' in trans_node:
                param_issues = self.validate_parameters(trans_node['parameters'], f"Node {trans_node.get('name', 'Unknown')}")
                issues.extend(param_issues)
        
        return issues
    
    def validate_parameters(self, params: Any, context: str) -> List[str]:
        """Valide récursivement les paramètres"""
        issues = []
        
        if isinstance(params, dict):
            for key, value in params.items():
                if isinstance(value, str):
                    expr_issues = self.validate_n8n_expressions(value, f"{context}.{key}")
                    issues.extend(expr_issues)
                else:
                    issues.extend(self.validate_parameters(value, f"{context}.{key}"))
        
        elif isinstance(params, list):
            for i, item in enumerate(params):
                issues.extend(self.validate_parameters(item, f"{context}[{i}]"))
        
        elif isinstance(params, str):
            expr_issues = self.validate_n8n_expressions(params, context)
            issues.extend(expr_issues)
        
        return issues
    
    def check_translation_quality(self, original: str, translated: str, context: str) -> List[str]:
        """Vérifie la qualité de la traduction"""
        issues = []
        
        if not isinstance(original, str) or not isinstance(translated, str):
            return issues
        
        # Vérifier si quelque chose a été traduit
        if original == translated and not self.should_be_preserved(original):
            issues.append(f"Texte non traduit dans {context}: '{original}'")
            self.stats['untranslated_text'] += 1
        elif original != translated:
            self.stats['translated_names'] += 1
        
        # Vérifier la longueur (traduction française généralement plus longue)
        if len(translated) < len(original) * 0.8 and not self.should_be_preserved(original):
            issues.append(f"Traduction potentiellement incomplète dans {context}: '{original}' -> '{translated}'")
        
        return issues
    
    def should_be_preserved(self, text: str) -> bool:
        """Détermine si un texte devrait être préservé"""
        if not isinstance(text, str):
            return True
        
        preserve_patterns = [
            r'\{\{.*?\}\}',  # Expressions n8n
            r'\$\w+',        # Variables n8n
            r'https?://',    # URLs
            r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}',  # Emails
            r'\d+x\d+',      # Tailles d'images
            r'[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}'  # UUIDs
        ]
        
        # Noms de services à préserver
        services = ['OpenAI', 'Gmail', 'Slack', 'API', 'JSON', 'HTTP', 'n8n']
        
        for pattern in preserve_patterns:
            if re.search(pattern, text):
                return True
        
        for service in services:
            if service.lower() in text.lower():
                return True
        
        return False
    
    def validate_workflow_file(self, original_path: Path, translated_path: Path) -> Dict[str, Any]:
        """Valide un fichier de workflow traduit"""
        validation_result = {
            'file': translated_path.name,
            'issues': [],
            'stats': {'nodes': 0, 'translations': 0, 'expressions': 0}
        }
        
        try:
            # Charger les fichiers
            with open(original_path, 'r', encoding='utf-8') as f:
                original = json.load(f)
            
            with open(translated_path, 'r', encoding='utf-8') as f:
                translated = json.load(f)
            
            # Valider la structure JSON
            structure_issues = self.validate_json_structure(original, translated)
            validation_result['issues'].extend(structure_issues)
            
            # Valider le nom du workflow
            if 'name' in original and 'name' in translated:
                quality_issues = self.check_translation_quality(
                    original['name'], 
                    translated['name'], 
                    'workflow name'
                )
                validation_result['issues'].extend(quality_issues)
            
            # Valider les nodes
            if 'nodes' in original and 'nodes' in translated:
                node_issues = self.validate_node_translations(original['nodes'], translated['nodes'])
                validation_result['issues'].extend(node_issues)
                validation_result['stats']['nodes'] = len(translated['nodes'])
            
            # Compter les statistiques
            self.stats['total_workflows'] += 1
            self.stats['total_nodes'] += validation_result['stats']['nodes']
            
            if validation_result['issues']:
                self.stats['validation_errors'] += len(validation_result['issues'])
            
        except Exception as e:
            validation_result['issues'].append(f"Erreur lors de la validation: {e}")
            logger.error(f"Erreur lors de la validation de {translated_path.name}: {e}")
        
        return validation_result
    
    def validate_directory(self, original_dir: Path, translated_dir: Path) -> Dict[str, Any]:
        """Valide tous les workflows traduits d'un répertoire"""
        logger.info(f"Validation des traductions: {original_dir} -> {translated_dir}")
        
        results = {
            'summary': {},
            'files': [],
            'global_issues': []
        }
        
        # Trouver tous les fichiers JSON traduits
        translated_files = list(translated_dir.glob('*.json'))
        
        if not translated_files:
            results['global_issues'].append("Aucun fichier traduit trouvé")
            return results
        
        for translated_file in translated_files:
            original_file = original_dir / translated_file.name
            
            if not original_file.exists():
                results['global_issues'].append(f"Fichier original manquant: {translated_file.name}")
                continue
            
            file_result = self.validate_workflow_file(original_file, translated_file)
            results['files'].append(file_result)
        
        # Générer le résumé
        results['summary'] = {
            'total_files': len(results['files']),
            'files_with_issues': len([f for f in results['files'] if f['issues']]),
            'total_issues': sum(len(f['issues']) for f in results['files']),
            'stats': self.stats
        }
        
        return results
    
    def generate_report(self, results: Dict[str, Any], output_path: Path):
        """Génère un rapport de validation"""
        report_lines = [
            "# Rapport de Validation des Traductions n8n",
            "=" * 50,
            "",
            "## Résumé Global",
            f"- Fichiers validés: {results['summary']['total_files']}",
            f"- Fichiers avec problèmes: {results['summary']['files_with_issues']}",
            f"- Total des problèmes: {results['summary']['total_issues']}",
            "",
            "## Statistiques",
            f"- Workflows traduits: {self.stats['total_workflows']}",
            f"- Nodes au total: {self.stats['total_nodes']}",
            f"- Éléments traduits: {self.stats['translated_names']}",
            f"- Expressions n8n préservées: {self.stats['preserved_expressions']}",
            f"- Textes non traduits: {self.stats['untranslated_text']}",
            "",
        ]
        
        if results['global_issues']:
            report_lines.extend([
                "## Problèmes Globaux",
                *[f"- {issue}" for issue in results['global_issues']],
                ""
            ])
        
        # Détails par fichier
        if results['files']:
            report_lines.append("## Détails par Fichier")
            report_lines.append("")
            
            for file_result in results['files']:
                if file_result['issues']:
                    report_lines.extend([
                        f"### {file_result['file']}",
                        *[f"- {issue}" for issue in file_result['issues']],
                        ""
                    ])
        
        # Sauvegarder le rapport
        with open(output_path, 'w', encoding='utf-8') as f:
            f.write('\n'.join(report_lines))
        
        return output_path

def main():
    """Fonction principale de validation"""
    print("🔍 Validation des traductions de workflows n8n")
    print("=" * 50)
    
    # Chemins
    original_dir = Path('/var/www/automatehub/TOP_100_PRIORITAIRES')
    translated_dir = Path('/var/www/automatehub/TOP_100_FR')
    report_path = Path('/var/www/automatehub/validation_report.md')
    
    # Vérifier que les répertoires existent
    if not original_dir.exists():
        print(f"❌ Répertoire original introuvable: {original_dir}")
        return 1
    
    if not translated_dir.exists():
        print(f"❌ Répertoire traduit introuvable: {translated_dir}")
        return 1
    
    # Lancer la validation
    validator = TranslationValidator()
    results = validator.validate_directory(original_dir, translated_dir)
    
    # Générer le rapport
    report_file = validator.generate_report(results, report_path)
    
    # Afficher le résumé
    summary = results['summary']
    print(f"✅ Validation terminée")
    print(f"📊 {summary['total_files']} fichiers validés")
    print(f"⚠️  {summary['files_with_issues']} fichiers avec problèmes")
    print(f"🔍 {summary['total_issues']} problèmes au total")
    print(f"📄 Rapport détaillé: {report_file}")
    
    return 0 if summary['total_issues'] == 0 else 1

if __name__ == '__main__':
    exit(main())