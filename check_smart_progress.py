#!/usr/bin/env python3
"""
Vérifier la progression du script intelligent
"""

import os
import subprocess
import time

def main():
    print("🧠 PROGRESSION TRADUCTION INTELLIGENTE")
    print("=" * 50)
    
    mapping_dir = "/var/www/automatehub/translation_mappings_smart"
    workflows_dir = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"
    
    # Compter total workflows
    total_workflows = sum(1 for root, dirs, files in os.walk(workflows_dir) 
                         for file in files if file.endswith('.json'))
    
    # Compter mappings créés
    mappings_count = 0
    if os.path.exists(mapping_dir):
        mappings_count = len([f for f in os.listdir(mapping_dir) if f.endswith('.mapping.json')])
    
    print(f"📁 Workflows totaux: {total_workflows}")
    print(f"✅ Workflows traités: {mappings_count}")
    print(f"📈 Progression: {mappings_count/total_workflows*100:.1f}%")
    print(f"⏳ Restants: {total_workflows - mappings_count}")
    
    # Vérifier si le processus tourne
    try:
        result = subprocess.run(['pgrep', '-f', 'translate_workflow_smart'], 
                              capture_output=True, text=True)
        process_active = result.returncode == 0
        print(f"🔄 Processus: {'✅ Actif' if process_active else '❌ Terminé'}")
    except:
        print("🔄 Processus: ❓ Inconnu")
    
    # Analyser quelques mappings récents
    if os.path.exists(mapping_dir):
        mappings = [f for f in os.listdir(mapping_dir) if f.endswith('.mapping.json')]
        if mappings:
            print(f"\n📋 DERNIERS WORKFLOWS TRAITÉS:")
            for mapping in sorted(mappings, 
                                key=lambda x: os.path.getmtime(os.path.join(mapping_dir, x)), 
                                reverse=True)[:3]:
                workflow_name = mapping.replace('.mapping.json', '')
                
                # Compter les textes détectés
                mapping_path = os.path.join(mapping_dir, mapping)
                try:
                    with open(mapping_path, 'r', encoding='utf-8') as f:
                        content = f.read()
                        text_count = content.count('$text_')
                    print(f"  - {workflow_name}: {text_count} textes détectés")
                except:
                    print(f"  - {workflow_name}: Erreur lecture")

if __name__ == "__main__":
    main()