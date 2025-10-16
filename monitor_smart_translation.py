#!/usr/bin/env python3
"""
Surveiller la progression de la traduction intelligente
"""

import os
import time
import subprocess

def main():
    print("🧠 SURVEILLANCE TRADUCTION INTELLIGENTE")
    print("=" * 60)
    
    log_file = "/var/www/automatehub/smart_translation_progress.log"
    mapping_dir = "/var/www/automatehub/translation_mappings_smart"
    
    while True:
        # Vérifier si le processus est actif
        try:
            result = subprocess.run(['pgrep', '-f', 'translate_workflow_smart'], 
                                  capture_output=True, text=True)
            process_active = result.returncode == 0
        except:
            process_active = False
        
        # Compter les mappings créés
        mapping_count = 0
        if os.path.exists(mapping_dir):
            mapping_count = len([f for f in os.listdir(mapping_dir) if f.endswith('.mapping.json')])
        
        # Lire les dernières lignes du log
        last_lines = ""
        if os.path.exists(log_file):
            try:
                with open(log_file, 'r', encoding='utf-8') as f:
                    lines = f.readlines()
                    last_lines = ''.join(lines[-3:]).strip()
            except:
                pass
        
        # Afficher le statut
        print(f"\r📊 Mappings créés: {mapping_count} | Processus: {'✅ Actif' if process_active else '❌ Arrêté'}", end="")
        
        if not process_active:
            print(f"\n\n✅ Traduction intelligente terminée!")
            
            # Afficher le résumé final
            if last_lines:
                print(f"\n📋 DERNIÈRES LIGNES DU LOG:")
                print(last_lines)
            
            print(f"\n📁 Mappings intelligents créés: {mapping_count}")
            break
        
        time.sleep(15)  # Vérifier toutes les 15 secondes
    
    print(f"\n🎉 Surveillance terminée. Vérifiez les résultats !")

if __name__ == "__main__":
    main()