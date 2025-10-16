#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script principal pour traduire un workflow n8n en 3 étapes
"""
import os
import sys
import subprocess
import json

def translate_filename(filename):
    """Traduire le nom du fichier"""
    # Dictionnaire de traduction pour les noms de fichiers
    translations = {
        "generate": "generer",
        "social": "social",
        "post": "post",
        "ideas": "idees",
        "summaries": "resumes",
        "email": "email",
        "automation": "automatisation",
        "workflow": "workflow",
        "trigger": "declencheur",
        "webhook": "webhook",
        "gmail": "gmail",
        "scheduled": "planifie",
        "file": "fichier",
        "management": "gestion",
        "create": "creer",
        "update": "mettre_a_jour",
        "delete": "supprimer",
        "send": "envoyer",
        "receive": "recevoir",
        "process": "traiter",
        "analyze": "analyser",
        "report": "rapport",
        "dashboard": "tableau_de_bord",
        "notification": "notification",
        "alert": "alerte",
        "photo": "photo",
        "generator": "generateur",
        "product": "produit",
        "life_style": "life_style",
    }
    
    # Séparer le nom et l'extension
    name = filename.lower()
    if name.endswith('.json'):
        name = name[:-5]
    
    # Remplacer les underscores par des espaces temporairement
    parts = name.split('_')
    translated_parts = []
    
    for part in parts:
        # Traduire chaque partie si possible
        translated_parts.append(translations.get(part, part))
    
    # Reconstruire avec des underscores
    translated_name = '_'.join(translated_parts)
    
    return translated_name + '.json'

def main():
    if len(sys.argv) < 2:
        print("Usage: python translate_workflow.py <workflow.json> [output_dir]")
        print("\nExemple:")
        print("  python translate_workflow.py ../AutomationTribe/Generate_social_post_ideas_or_summaries.json")
        sys.exit(1)
    
    input_file = os.path.abspath(sys.argv[1])
    script_dir = os.path.dirname(os.path.abspath(__file__))
    
    if not os.path.exists(input_file):
        print(f"❌ Erreur: Le fichier {input_file} n'existe pas")
        sys.exit(1)
    
    # Déterminer le répertoire de sortie
    if len(sys.argv) > 2:
        output_dir = sys.argv[2]
    else:
        output_dir = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"
    
    os.makedirs(output_dir, exist_ok=True)
    
    print(f"🚀 Traduction du workflow: {os.path.basename(input_file)}")
    print("="*60)
    
    # Étape 1: Extraction des textes
    print("\n📋 Étape 1: Extraction des textes...")
    extract_result = subprocess.run(
        [sys.executable, os.path.join(script_dir, "extract_texts.py"), input_file],
        capture_output=True,
        text=True
    )
    
    if extract_result.returncode != 0:
        print(f"❌ Erreur lors de l'extraction: {extract_result.stderr}")
        sys.exit(1)
    
    print(extract_result.stdout)
    
    # Trouver le fichier d'extraction
    texts_file = input_file.replace('.json', '_texts_to_translate.json')
    
    # Vérifier si on a besoin de la clé OpenAI
    api_key = os.environ.get('OPENAI_API_KEY')
    if not api_key:
        print("\n⚠️  Clé OpenAI non trouvée dans l'environnement.")
        print("Entrez votre clé OpenAI (ou appuyez sur Entrée pour ignorer la traduction AI):")
        api_key = input().strip()
        
        if api_key:
            os.environ['OPENAI_API_KEY'] = api_key
    
    # Étape 2: Traduction (si clé disponible)
    if api_key:
        print("\n🌐 Étape 2: Traduction via OpenAI...")
        translate_result = subprocess.run(
            [sys.executable, os.path.join(script_dir, "translate_texts.py"), texts_file],
            capture_output=True,
            text=True
        )
        
        if translate_result.returncode != 0:
            print(f"⚠️  Erreur lors de la traduction: {translate_result.stderr}")
            print("Utilisation de la traduction par dictionnaire...")
            # Fallback: utiliser le script de traduction simple
            subprocess.run([
                sys.executable, 
                "/var/www/automatehub/scripts/translate_workflow_complete.py", 
                input_file
            ])
            return
        
        print(translate_result.stdout)
        
        # Fichier de traductions
        translations_file = texts_file.replace('_texts_to_translate.json', '_texts_translated.json')
        
        # Étape 3: Application des traductions
        print("\n🔧 Étape 3: Application des traductions...")
        apply_result = subprocess.run(
            [sys.executable, os.path.join(script_dir, "apply_translations.py"), input_file, translations_file],
            capture_output=True,
            text=True
        )
        
        if apply_result.returncode != 0:
            print(f"❌ Erreur lors de l'application: {apply_result.stderr}")
            sys.exit(1)
        
        print(apply_result.stdout)
        
        # Déplacer le fichier traduit vers le bon répertoire avec le bon nom
        temp_output = input_file.replace('.json', '_FR.json')
        final_name = translate_filename(os.path.basename(input_file))
        final_path = os.path.join(output_dir, final_name)
        
        if os.path.exists(temp_output):
            os.rename(temp_output, final_path)
            print(f"\n✅ Workflow traduit sauvegardé: {final_path}")
            
            # Nettoyer les fichiers temporaires
            for temp_file in [texts_file, translations_file]:
                if os.path.exists(temp_file):
                    os.remove(temp_file)
            
            print("\n🧹 Fichiers temporaires nettoyés")
    else:
        print("\n⚠️  Pas de clé OpenAI - Utilisation de la traduction par dictionnaire...")
        subprocess.run([
            sys.executable, 
            "/var/www/automatehub/scripts/translate_workflow_complete.py", 
            input_file
        ])
    
    print("\n✨ Traduction terminée!")

if __name__ == "__main__":
    main()