#!/usr/bin/env python3
"""
Script de maintenance pour les traductions de workflows n8n
Permet d'améliorer et maintenir les traductions au fil du temps
"""

import json
import argparse
from pathlib import Path
from typing import Dict, List, Any
import logging

# Configuration des logs
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class TranslationMaintainer:
    """Maintient et améliore les traductions existantes"""
    
    def __init__(self):
        # Dictionnaire de traductions supplémentaires trouvées
        self.additional_translations = {
            # Corrections pour les noms partiellement traduits
            'Créer new page': 'Créer nouvelle page',
            'Mettre à jour page with end date': 'Mettre à jour page avec date de fin',
            'Si pause_in_minuten is empty': 'Si pause_en_minutes est vide',
            'Si page responded': 'Si page a répondu',
            'Si page exist': 'Si page existe',
            'Si page exist1': 'Si page existe 1',
            'Get notion page by date': 'Obtenir page notion par date',
            'Définir Message - Break time tracked': 'Définir Message - Temps de pause suivi',
            'Définir Message - Break time updated': 'Définir Message - Temps de pause mis à jour',
            'Get notion page with todays date': 'Obtenir page notion avec date d\'aujourd\'hui',
            'Définir break duration for current day': 'Définir durée pause jour actuel',
            'Mettre à jour break duration for current day': 'Mettre à jour durée pause jour actuel',
            
            # Autres traductions communes
            'Calculate the Centroid of a Set of Vectors': 'Calculer le Centroïde d\'un Ensemble de Vecteurs',
            'Track Working Time and Pauses': 'Suivi du Temps de Travail et des Pauses',
            'Email to Google Sheets Auto': 'Email vers Google Sheets Auto',
            'Form to API': 'Formulaire vers API',
            'Simple PDF Reader': 'Lecteur PDF Simple',
            'Binary File Writer': 'Écrivain de Fichier Binaire',
            
            # Termes techniques courants
            'new page': 'nouvelle page',
            'with end date': 'avec date de fin',
            'is empty': 'est vide',
            'page responded': 'page a répondu',
            'page exist': 'page existe',
            'by date': 'par date',
            'Break time tracked': 'Temps de pause suivi',
            'Break time updated': 'Temps de pause mis à jour',
            'with todays date': 'avec la date d\'aujourd\'hui',
            'for current day': 'pour le jour actuel',
            'break duration': 'durée de pause',
            'current day': 'jour actuel'
        }
    
    def find_untranslated_terms(self, directory: Path) -> Dict[str, int]:
        """Trouve les termes non traduits dans les workflows"""
        untranslated = {}
        
        english_patterns = [
            'create', 'update', 'delete', 'get', 'set', 'send', 'receive',
            'new', 'old', 'current', 'previous', 'next', 'first', 'last',
            'with', 'without', 'from', 'to', 'in', 'on', 'at', 'by',
            'time', 'date', 'page', 'file', 'data', 'user', 'item',
            'is empty', 'is not', 'exists', 'does not exist'
        ]
        
        json_files = list(directory.glob('*.json'))
        
        for json_file in json_files:
            try:
                with open(json_file, 'r', encoding='utf-8') as f:
                    workflow = json.load(f)
                
                content = json.dumps(workflow, indent=2)
                
                for pattern in english_patterns:
                    if pattern in content.lower():
                        untranslated[pattern] = untranslated.get(pattern, 0) + 1
                        
            except Exception as e:
                logger.error(f"Erreur lors de l'analyse de {json_file}: {e}")
        
        return dict(sorted(untranslated.items(), key=lambda x: x[1], reverse=True))
    
    def apply_additional_translations(self, workflow: Dict[str, Any]) -> Dict[str, Any]:
        """Applique des traductions supplémentaires"""
        improved_workflow = workflow.copy()
        
        # Améliorer le nom du workflow
        if 'name' in workflow and isinstance(workflow['name'], str):
            original_name = workflow['name']
            improved_name = self.additional_translations.get(original_name, original_name)
            
            # Traductions par remplacement de parties
            for en_term, fr_term in self.additional_translations.items():
                if en_term in improved_name and en_term != improved_name:
                    improved_name = improved_name.replace(en_term, fr_term)
            
            if improved_name != original_name:
                improved_workflow['name'] = improved_name
                logger.info(f"Nom de workflow amélioré : '{original_name}' -> '{improved_name}'")
        
        # Améliorer les nodes
        if 'nodes' in workflow:
            improved_nodes = []
            for node in workflow['nodes']:
                improved_node = node.copy()
                
                # Améliorer le nom du node
                if 'name' in node and isinstance(node['name'], str):
                    original_name = node['name']
                    improved_name = self.additional_translations.get(original_name, original_name)
                    
                    # Traductions par remplacement de parties
                    for en_term, fr_term in self.additional_translations.items():
                        if en_term in improved_name and len(en_term) > 3:  # Éviter les remplacements trop courts
                            improved_name = improved_name.replace(en_term, fr_term)
                    
                    if improved_name != original_name:
                        improved_node['name'] = improved_name
                        logger.debug(f"Nom de node amélioré : '{original_name}' -> '{improved_name}'")
                
                improved_nodes.append(improved_node)
            
            improved_workflow['nodes'] = improved_nodes
        
        return improved_workflow
    
    def improve_file(self, file_path: Path) -> bool:
        """Améliore un fichier de workflow"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                workflow = json.load(f)
            
            improved_workflow = self.apply_additional_translations(workflow)
            
            # Sauvegarder seulement si des améliorations ont été apportées
            if improved_workflow != workflow:
                with open(file_path, 'w', encoding='utf-8') as f:
                    json.dump(improved_workflow, f, indent=2, ensure_ascii=False)
                
                logger.info(f"✅ {file_path.name} amélioré")
                return True
            else:
                logger.debug(f"Aucune amélioration nécessaire pour {file_path.name}")
                return False
                
        except Exception as e:
            logger.error(f"❌ Erreur lors de l'amélioration de {file_path.name}: {e}")
            return False
    
    def maintain_directory(self, directory: Path, dry_run: bool = False) -> Dict[str, int]:
        """Maintient toutes les traductions d'un répertoire"""
        stats = {'improved': 0, 'unchanged': 0, 'errors': 0, 'total': 0}
        
        json_files = list(directory.glob('*.json'))
        stats['total'] = len(json_files)
        
        logger.info(f"Maintenance de {stats['total']} workflows")
        if dry_run:
            logger.info("MODE TEST - Aucune modification ne sera sauvegardée")
        
        for json_file in json_files:
            if dry_run:
                # En mode test, juste analyser
                try:
                    with open(json_file, 'r', encoding='utf-8') as f:
                        workflow = json.load(f)
                    
                    improved_workflow = self.apply_additional_translations(workflow)
                    
                    if improved_workflow != workflow:
                        logger.info(f"💡 {json_file.name} pourrait être amélioré")
                        stats['improved'] += 1
                    else:
                        stats['unchanged'] += 1
                        
                except Exception as e:
                    logger.error(f"❌ Erreur d'analyse de {json_file.name}: {e}")
                    stats['errors'] += 1
            else:
                # Mode normal, appliquer les améliorations
                if self.improve_file(json_file):
                    stats['improved'] += 1
                else:
                    stats['unchanged'] += 1
        
        return stats
    
    def generate_maintenance_report(self, directory: Path, output_path: Path):
        """Génère un rapport de maintenance"""
        untranslated = self.find_untranslated_terms(directory)
        
        report_lines = [
            "# Rapport de Maintenance des Traductions",
            "=" * 50,
            "",
            "## Termes Anglais Détectés",
            "",
            "Les termes suivants pourraient nécessiter une traduction :",
            ""
        ]
        
        for term, count in list(untranslated.items())[:20]:
            report_lines.append(f"- `{term}` : {count} occurrences")
        
        report_lines.extend([
            "",
            "## Actions Recommandées",
            "",
            "1. **Réviser les termes fréquents** : Ajouter des traductions pour les termes les plus courants",
            "2. **Mettre à jour le dictionnaire** : Enrichir `additional_translations` avec de nouveaux termes",
            "3. **Exécuter la maintenance** : Utiliser `maintenance_translations.py --apply`",
            "",
            "## Commandes Utiles",
            "",
            "```bash",
            "# Tester les améliorations (sans modification)",
            "python3 maintenance_translations.py --dry-run",
            "",
            "# Appliquer les améliorations",
            "python3 maintenance_translations.py --apply",
            "",
            "# Générer un rapport de maintenance",
            "python3 maintenance_translations.py --report",
            "```"
        ])
        
        with open(output_path, 'w', encoding='utf-8') as f:
            f.write('\\n'.join(report_lines))
        
        return output_path

def main():
    """Fonction principale de maintenance"""
    parser = argparse.ArgumentParser(description='Maintenance des traductions de workflows n8n')
    parser.add_argument('--dry-run', action='store_true', help='Mode test sans modification')
    parser.add_argument('--apply', action='store_true', help='Appliquer les améliorations')
    parser.add_argument('--report', action='store_true', help='Générer un rapport de maintenance')
    parser.add_argument('--directory', default='/var/www/automatehub/TOP_100_FR', 
                       help='Répertoire des workflows traduits')
    
    args = parser.parse_args()
    
    if not any([args.dry_run, args.apply, args.report]):
        args.report = True  # Action par défaut
    
    directory = Path(args.directory)
    maintainer = TranslationMaintainer()
    
    if not directory.exists():
        print(f"❌ Répertoire introuvable: {directory}")
        return 1
    
    print("🔧 MAINTENANCE DES TRADUCTIONS")
    print("=" * 40)
    
    if args.report:
        print("📊 Génération du rapport de maintenance...")
        report_path = Path('/var/www/automatehub/maintenance_report.md')
        maintainer.generate_maintenance_report(directory, report_path)
        print(f"📄 Rapport généré : {report_path}")
    
    if args.dry_run or args.apply:
        stats = maintainer.maintain_directory(directory, dry_run=args.dry_run)
        
        print(f"")
        print(f"📊 RÉSULTATS DE MAINTENANCE")
        print(f"=" * 30)
        print(f"Fichiers traités : {stats['total']}")
        print(f"✨ Améliorations : {stats['improved']}")
        print(f"✅ Inchangés : {stats['unchanged']}")
        print(f"❌ Erreurs : {stats['errors']}")
        
        if args.dry_run and stats['improved'] > 0:
            print(f"")
            print(f"💡 Utilisez --apply pour appliquer les améliorations")
    
    return 0

if __name__ == '__main__':
    exit(main())