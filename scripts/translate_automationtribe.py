#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour traduire les workflows du dossier AutomationTribe
"""
import os
import sys

# Ajouter le répertoire des scripts au path
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from translate_all_workflows_v3 import translate_single_workflow, translate_filename

def main():
    source_dir = "/var/www/automatehub/AutomationTribe"
    dest_dir = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"
    
    print("🌐 TRADUCTION DES WORKFLOWS AUTOMATIONTRIBE")
    print("="*60)
    
    # Créer le répertoire de destination
    os.makedirs(dest_dir, exist_ok=True)
    
    # Statistiques
    total = 0
    success = 0
    errors = 0
    
    # Parcourir tous les fichiers
    for root, dirs, files in os.walk(source_dir):
        for filename in files:
            if filename.endswith('.json'):
                total += 1
                source_path = os.path.join(root, filename)
                
                # Créer le chemin de destination avec la même structure
                rel_path = os.path.relpath(root, source_dir)
                dest_subdir = os.path.join(dest_dir, rel_path)
                os.makedirs(dest_subdir, exist_ok=True)
                
                # Traduire le nom du fichier
                translated_filename = translate_filename(filename)
                dest_path = os.path.join(dest_subdir, translated_filename)
                
                print(f"\n📋 Workflow {total}: {filename}")
                print(f"   → {translated_filename}")
                
                # Traduire le workflow
                try:
                    result, message = translate_single_workflow(source_path, dest_path)
                    if result:
                        success += 1
                        print(f"   ✅ Traduit avec succès!")
                        
                        # Vérifier quelques détails
                        import json
                        with open(dest_path, 'r', encoding='utf-8') as f:
                            data = json.load(f)
                        
                        # Vérifier le tag Audelalia
                        has_tag = any(tag.get('name') == 'Audelalia' for tag in data.get('tags', []))
                        print(f"   🏷️  Tag Audelalia: {'✅' if has_tag else '❌'}")
                        
                        # Afficher le nom traduit
                        if 'name' in data:
                            print(f"   📝 Nom du workflow: {data['name']}")
                        
                        # Vérifier si des prompts ont été traduits
                        prompts_count = 0
                        for node in data.get('nodes', []):
                            if node.get('type') in ['@n8n/n8n-nodes-langchain.openAi', 'n8n-nodes-base.openAi']:
                                if 'parameters' in node and 'messages' in node['parameters']:
                                    if 'values' in node['parameters']['messages']:
                                        for msg in node['parameters']['messages']['values']:
                                            if 'content' in msg and isinstance(msg['content'], str):
                                                content = msg['content']
                                                if content.startswith('='):
                                                    content = content[1:]
                                                # Vérifier si c'est en français
                                                if any(word in content for word in ['Générer', 'Créer', 'français', 'pour']):
                                                    prompts_count += 1
                        
                        if prompts_count > 0:
                            print(f"   🤖 Prompts OpenAI traduits: {prompts_count}")
                            
                    else:
                        errors += 1
                        print(f"   ❌ Erreur: {message}")
                        
                except Exception as e:
                    errors += 1
                    print(f"   ❌ Erreur inattendue: {str(e)}")
    
    # Rapport final
    print("\n" + "="*60)
    print("📊 RAPPORT FINAL")
    print(f"\n✅ Succès: {success}/{total}")
    print(f"❌ Erreurs: {errors}")
    print(f"\n📁 Workflows traduits dans: {dest_dir}")
    
    # Vérifier quelques exemples
    if success > 0:
        print("\n📋 Exemples de fichiers traduits:")
        examples = 0
        for root, dirs, files in os.walk(dest_dir):
            for filename in files[:5]:
                if filename.endswith('.json') and examples < 5:
                    rel_path = os.path.relpath(os.path.join(root, filename), dest_dir)
                    print(f"  - {rel_path}")
                    examples += 1
    
    print("\n✨ Vérification terminée!")

if __name__ == "__main__":
    main()