#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script de traduction en masse simplifiée
"""
import os
import json
import sys

# Ajouter le répertoire des scripts au path
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from translate_all_workflows_v3 import translate_single_workflow, translate_filename

def process_directory(source_dir, dest_base_dir):
    """Traiter un répertoire de workflows"""
    success = 0
    errors = 0
    total = 0
    
    print(f"\n📁 Traitement de {source_dir}...")
    
    for root, dirs, files in os.walk(source_dir):
        for filename in files:
            if filename.endswith('.json'):
                total += 1
                source_path = os.path.join(root, filename)
                
                try:
                    # Créer la structure de destination
                    rel_path = os.path.relpath(root, source_dir)
                    dest_dir = os.path.join(dest_base_dir, os.path.basename(source_dir), rel_path)
                    os.makedirs(dest_dir, exist_ok=True)
                    
                    # Traduire le nom de fichier
                    translated_filename = translate_filename(filename)
                    dest_path = os.path.join(dest_dir, translated_filename)
                    
                    # Traduire le workflow complet
                    result, message = translate_single_workflow(source_path, dest_path)
                    
                    if result:
                        success += 1
                    else:
                        raise Exception(message)
                    
                    # Afficher la progression
                    if success % 100 == 0:
                        print(f"  ✅ {success} workflows traduits...")
                    
                except Exception as e:
                    errors += 1
                    if errors <= 10:
                        print(f"  ❌ Erreur: {filename} - {str(e)}")
    
    return total, success, errors

def main():
    print("🌐 TRADUCTION EN MASSE DES WORKFLOWS N8N")
    print("="*60)
    
    # Répertoires à traiter
    source_dirs = [
        "/var/www/automatehub/200_automations_n8n",
        "/var/www/automatehub/github_workflows"
    ]
    
    # Destination
    dest_base = "/var/www/automatehub/workflows_traduits/FR"
    
    total_all = 0
    success_all = 0
    errors_all = 0
    
    # Traiter chaque répertoire
    for source_dir in source_dirs:
        if os.path.exists(source_dir):
            total, success, errors = process_directory(source_dir, dest_base)
            total_all += total
            success_all += success
            errors_all += errors
            print(f"  📊 Résultat: {success}/{total} traduits, {errors} erreurs")
        else:
            print(f"  ⚠️  Répertoire non trouvé: {source_dir}")
    
    # Rapport final
    print("\n" + "="*60)
    print("✨ TRADUCTION TERMINÉE!")
    print(f"\n📊 Statistiques globales:")
    print(f"  - Total fichiers: {total_all}")
    print(f"  - ✅ Traduits avec succès: {success_all}")
    print(f"  - ❌ Erreurs: {errors_all}")
    print(f"  - 📁 Destination: {dest_base}")
    
    if success_all > 0:
        print(f"\n🎉 {success_all} workflows ont été traduits en français avec le tag 'Audelalia' !")

if __name__ == "__main__":
    main()