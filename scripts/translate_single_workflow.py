#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import sys
import os

# Ajouter le répertoire des scripts au path pour importer les modules
sys.path.insert(0, '/var/www/automatehub/scripts')

from translate_all_workflows_v3 import translate_single_workflow, translate_filename, add_audelalia_tag

def main():
    # Fichier source
    if len(sys.argv) > 1:
        source_file = sys.argv[1]
    else:
        source_file = "/var/www/automatehub/AutomationTribe/5 - Produits Prennent Vie/Life_Style_Product_Photo_Generator.json"
    
    # Nom traduit
    original_name = os.path.basename(source_file)
    translated_name = translate_filename(original_name)
    
    print(f"🔄 Traduction du workflow:")
    print(f"   Source: {original_name}")
    print(f"   → Nom traduit: {translated_name}")
    
    # Fichier de destination dans workflows_traduits
    dest_dir = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"
    os.makedirs(dest_dir, exist_ok=True)
    dest_file = os.path.join(dest_dir, translated_name)
    
    print(f"   Destination: {dest_file}")
    print()
    
    # Traduire le workflow
    success, message = translate_single_workflow(source_file, dest_file)
    
    if success:
        print("✅ Workflow traduit avec succès!")
        print(f"📁 Fichier créé: {dest_file}")
        print("✨ Tag 'Audelalia' ajouté")
        
        # Afficher un aperçu du contenu
        print("\n📋 Vérification du contenu traduit:")
        import json
        with open(dest_file, 'r', encoding='utf-8') as f:
            data = json.load(f)
            
        # Afficher le nom du workflow
        if 'name' in data:
            print(f"   Nom du workflow: {data['name']}")
            
        # Afficher quelques nodes traduits
        if 'nodes' in data and len(data['nodes']) > 0:
            print(f"\n   Nodes ({len(data['nodes'])} total):")
            for i, node in enumerate(data['nodes'][:5]):
                print(f"   - {node.get('name', 'Sans nom')}")
                
        # Vérifier le tag
        if 'tags' in data:
            print(f"\n   Tags: {[tag.get('name') for tag in data['tags']]}")
            
    else:
        print(f"❌ Erreur: {message}")

if __name__ == "__main__":
    main()