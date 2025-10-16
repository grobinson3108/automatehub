#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Version automatique du script de traduction sans demande de confirmation
"""
import sys
import os

# Ajouter le chemin du script principal
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

# Importer le module principal
from translate_all_workflows_v3 import (
    find_all_workflows, 
    translate_workflow, 
    add_audelalia_tag,
    update_connections,
    translate_filename
)
import json

def main():
    source_dir = "/var/www/automatehub/workflows"
    dest_base_dir = "/var/www/automatehub/workflows_traduits"
    
    print("🌐 TRADUCTION AUTOMATIQUE EN MASSE DES WORKFLOWS N8N")
    print("="*60)
    
    # Trouver tous les workflows
    print("\n🔍 Recherche des workflows...")
    all_workflows = find_all_workflows(source_dir)
    total = len(all_workflows)
    print(f"✅ {total} workflows trouvés")
    
    # Créer le répertoire FR
    fr_dir = os.path.join(dest_base_dir, "FR")
    os.makedirs(fr_dir, exist_ok=True)
    
    # Traiter tous les workflows
    print(f"\n🚀 Début de la traduction de {total} workflows...")
    print("(Cela peut prendre un certain temps)")
    
    success_count = 0
    error_count = 0
    
    for i, workflow_path in enumerate(all_workflows, 1):
        try:
            # Progression
            if i % 100 == 0:
                print(f"\n📊 Progression: {i}/{total} ({(i/total)*100:.1f}%)")
            
            # Lire le workflow
            with open(workflow_path, 'r', encoding='utf-8') as f:
                workflow_data = json.load(f)
            
            # Traduire
            translated_data = translate_workflow(workflow_data)
            
            # Ajouter le tag
            translated_data = add_audelalia_tag(translated_data)
            
            # Mettre à jour les connexions
            translated_data = update_connections(translated_data)
            
            # Créer la structure de répertoires
            relative_path = os.path.relpath(workflow_path, source_dir)
            dir_path = os.path.dirname(relative_path)
            
            dest_dir = os.path.join(fr_dir, dir_path)
            os.makedirs(dest_dir, exist_ok=True)
            
            # Traduire le nom du fichier
            original_filename = os.path.basename(workflow_path)
            translated_filename = translate_filename(original_filename)
            
            # Sauvegarder
            dest_path = os.path.join(dest_dir, translated_filename)
            with open(dest_path, 'w', encoding='utf-8') as f:
                json.dump(translated_data, f, ensure_ascii=False, indent=2)
            
            success_count += 1
            
            # Afficher la progression tous les 50 workflows
            if i % 50 == 0:
                print(f"  ✅ {success_count} workflows traduits avec succès")
            
        except Exception as e:
            error_count += 1
            if error_count <= 10:  # Afficher seulement les 10 premières erreurs
                print(f"\n❌ Erreur workflow {i}: {workflow_path}")
                print(f"   {str(e)}")
    
    # Rapport final
    print("\n" + "="*60)
    print("📊 TRADUCTION TERMINÉE!")
    print(f"\n✅ Succès: {success_count}/{total} workflows")
    print(f"❌ Erreurs: {error_count} workflows")
    print(f"\n📁 Workflows traduits dans: {fr_dir}")
    print("\n✨ Traduction complète terminée!")

if __name__ == "__main__":
    main()