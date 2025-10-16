#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour traduire un workflow n8n en utilisant l'approche avec placeholders OpenAI
"""
import os
import sys
import subprocess
import json
import shutil

# Ajouter le répertoire des scripts au path
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate_all_workflows_v3 import translate_filename

def main():
    if len(sys.argv) < 2:
        print("Usage: python translate_workflow_ai.py <workflow.json> [output_dir]")
        print("\nExemple:")
        print("  python translate_workflow_ai.py AutomationTribe/Generate_social_post_ideas_or_summaries.json")
        sys.exit(1)
    
    input_file = os.path.abspath(sys.argv[1])
    
    if not os.path.exists(input_file):
        print(f"❌ Erreur: Le fichier {input_file} n'existe pas")
        sys.exit(1)
    
    # Déterminer le répertoire de sortie
    if len(sys.argv) > 2:
        output_dir = sys.argv[2]
    else:
        output_dir = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"
    
    os.makedirs(output_dir, exist_ok=True)
    
    # Répertoire du script translator
    translator_dir = os.path.join(os.path.dirname(os.path.abspath(__file__)), "workflow_translator")
    
    print(f"🚀 Traduction AI du workflow: {os.path.basename(input_file)}")
    print("="*60)
    
    # Étape 1: Extraction des textes
    print("\n📋 Étape 1: Extraction des textes...")
    extract_script = os.path.join(translator_dir, "extract_texts.py")
    extract_result = subprocess.run(
        [sys.executable, extract_script, input_file],
        capture_output=True,
        text=True
    )
    
    if extract_result.returncode != 0:
        print(f"❌ Erreur lors de l'extraction: {extract_result.stderr}")
        sys.exit(1)
    
    print(extract_result.stdout)
    
    # Fichier d'extraction (dans le répertoire translator)
    basename = os.path.basename(input_file)
    texts_file = os.path.join(translator_dir, basename.replace('.json', '_texts_to_translate.json'))
    
    # Étape 2: Traduction via OpenAI
    print("\n🌐 Étape 2: Traduction via OpenAI...")
    translate_script = os.path.join(translator_dir, "translate_with_openai.py")
    
    translate_result = subprocess.run(
        [sys.executable, translate_script, texts_file],
        capture_output=True,
        text=True
    )
    
    if translate_result.returncode != 0:
        print(f"❌ Erreur lors de la traduction: {translate_result.stderr}")
        print("\n⚠️  Utilisation de la traduction offline...")
        
        # Fallback sur traduction offline
        translate_offline_script = os.path.join(translator_dir, "translate_texts_offline.py")
        translate_result = subprocess.run(
            [sys.executable, translate_offline_script, texts_file],
            capture_output=True,
            text=True
        )
        
        if translate_result.returncode != 0:
            print(f"❌ Erreur traduction offline: {translate_result.stderr}")
            sys.exit(1)
    
    print(translate_result.stdout)
    
    # Fichier de traductions
    translations_file = os.path.join(translator_dir, basename.replace('.json', '_texts_translated.json'))
    
    # Étape 3: Application des traductions
    print("\n🔧 Étape 3: Application des traductions...")
    apply_script = os.path.join(translator_dir, "apply_translations.py")
    
    apply_result = subprocess.run(
        [sys.executable, apply_script, input_file, translations_file],
        capture_output=True,
        text=True
    )
    
    if apply_result.returncode != 0:
        print(f"❌ Erreur lors de l'application: {apply_result.stderr}")
        sys.exit(1)
    
    print(apply_result.stdout)
    
    # Déplacer et renommer le fichier final
    temp_output = input_file.replace('.json', '_FR.json')
    final_name = translate_filename(os.path.basename(input_file))
    final_path = os.path.join(output_dir, final_name)
    
    if os.path.exists(temp_output):
        shutil.move(temp_output, final_path)
        print(f"\n✅ Workflow traduit sauvegardé: {final_path}")
        
        # Nettoyer les fichiers temporaires
        temp_files = [
            texts_file,
            translations_file
        ]
        
        for temp_file in temp_files:
            if os.path.exists(temp_file):
                os.remove(temp_file)
        
        print("🧹 Fichiers temporaires nettoyés")
        
        # Afficher un aperçu
        print("\n📋 Aperçu du workflow traduit:")
        with open(final_path, 'r', encoding='utf-8') as f:
            data = json.load(f)
            
        print(f"  Nom: {data.get('name', 'Sans nom')}")
        print(f"  Nodes: {len(data.get('nodes', []))}")
        print(f"  Tags: {[tag.get('name') for tag in data.get('tags', [])]}")
        
        # Vérifier si les prompts ont été traduits
        if 'nodes' in data:
            for node in data['nodes']:
                if node.get('type') in ['@n8n/n8n-nodes-langchain.openAi'] and 'parameters' in node:
                    if 'messages' in node['parameters'] and 'values' in node['parameters']['messages']:
                        for msg in node['parameters']['messages']['values']:
                            if 'content' in msg and isinstance(msg['content'], str):
                                content = msg['content']
                                if content.startswith('='):
                                    content = content[1:]
                                # Vérifier si c'est en français
                                if any(fr_word in content for fr_word in ['Générer', 'publications', 'réseaux sociaux', 'français']):
                                    print(f"  ✅ Prompt traduit dans le node '{node.get('name', 'Unknown')}'")
                                else:
                                    print(f"  ⚠️  Prompt non traduit dans le node '{node.get('name', 'Unknown')}'")
    else:
        print("❌ Fichier de sortie non trouvé")
    
    print("\n✨ Traduction terminée!")

if __name__ == "__main__":
    main()