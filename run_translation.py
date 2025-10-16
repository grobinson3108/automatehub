#!/usr/bin/env python3
"""
Script principal pour orchestrer la traduction complète des workflows n8n
"""

import sys
import os
from pathlib import Path
import subprocess
import time
from datetime import datetime

def run_command(command, description):
    """Exécute une commande et affiche le résultat"""
    print(f"\n🔄 {description}")
    print(f"Commande: {command}")
    print("-" * 50)
    
    start_time = time.time()
    result = subprocess.run(command, shell=True, capture_output=True, text=True)
    duration = time.time() - start_time
    
    if result.returncode == 0:
        print(f"✅ Terminé en {duration:.2f}s")
        if result.stdout:
            print("Sortie:")
            print(result.stdout)
    else:
        print(f"❌ Échec après {duration:.2f}s")
        if result.stderr:
            print("Erreur:")
            print(result.stderr)
        if result.stdout:
            print("Sortie:")
            print(result.stdout)
    
    return result.returncode == 0

def main():
    """Fonction principale"""
    print("🤖 SYSTÈME COMPLET DE TRADUCTION DES WORKFLOWS N8N")
    print("=" * 60)
    print(f"Début de l'opération: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Vérifier les prérequis
    base_dir = Path('/var/www/automatehub')
    if not base_dir.exists():
        print(f"❌ Répertoire de base introuvable: {base_dir}")
        return 1
    
    source_dir = base_dir / 'TOP_100_PRIORITAIRES'
    if not source_dir.exists():
        print(f"❌ Répertoire source introuvable: {source_dir}")
        return 1
    
    # Changer vers le répertoire de travail
    os.chdir(base_dir)
    
    # Étapes du processus
    steps = [
        {
            'command': 'python3 translate_workflows.py',
            'description': 'Traduction des workflows',
            'required': True
        },
        {
            'command': 'python3 validate_translations.py',
            'description': 'Validation des traductions',
            'required': False
        }
    ]
    
    success_count = 0
    total_steps = len(steps)
    
    for i, step in enumerate(steps, 1):
        print(f"\n{'='*60}")
        print(f"ÉTAPE {i}/{total_steps}: {step['description'].upper()}")
        print(f"{'='*60}")
        
        success = run_command(step['command'], step['description'])
        
        if success:
            success_count += 1
        elif step['required']:
            print(f"\n❌ Étape critique échouée: {step['description']}")
            print("Arrêt du processus.")
            return 1
        else:
            print(f"\n⚠️  Étape optionnelle échouée: {step['description']}")
            print("Continuation du processus.")
    
    # Résumé final
    print(f"\n{'='*60}")
    print("RÉSUMÉ FINAL")
    print(f"{'='*60}")
    print(f"✅ Étapes réussies: {success_count}/{total_steps}")
    print(f"🏁 Processus terminé: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # Vérifier les résultats
    output_dir = base_dir / 'TOP_100_FR'
    if output_dir.exists():
        json_files = list(output_dir.glob('*.json'))
        print(f"📁 Workflows traduits: {len(json_files)} fichiers dans {output_dir}")
        
        if len(json_files) > 0:
            print(f"🎉 TRADUCTION RÉUSSIE!")
            
            # Afficher quelques exemples
            print(f"\n📋 Exemples de fichiers traduits:")
            for i, file_path in enumerate(json_files[:5], 1):
                print(f"   {i}. {file_path.name}")
            
            if len(json_files) > 5:
                print(f"   ... et {len(json_files) - 5} autres")
            
            # Informations sur les logs et rapports
            log_file = base_dir / 'translation.log'
            if log_file.exists():
                print(f"📄 Log détaillé: {log_file}")
            
            report_file = base_dir / 'validation_report.md'
            if report_file.exists():
                print(f"📊 Rapport de validation: {report_file}")
            
            print(f"\n🚀 Les workflows traduits sont prêts à être utilisés!")
            print(f"   Source: {source_dir}")
            print(f"   Destination: {output_dir}")
            
            return 0
    
    print(f"❌ Aucun fichier traduit trouvé dans {output_dir}")
    return 1

if __name__ == '__main__':
    try:
        exit_code = main()
        sys.exit(exit_code)
    except KeyboardInterrupt:
        print(f"\n\n⚠️  Processus interrompu par l'utilisateur")
        sys.exit(1)
    except Exception as e:
        print(f"\n\n❌ Erreur critique: {e}")
        sys.exit(1)