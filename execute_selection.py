#!/usr/bin/env python3

import subprocess
import sys
from pathlib import Path

def run_selection():
    """Execute la sélection des TOP 100 workflows"""
    try:
        print("🚀 Lancement de la sélection des TOP 100 workflows...")
        
        # Changer vers le bon répertoire
        os.chdir("/var/www/automatehub")
        
        # Exécuter le script de sélection
        result = subprocess.run([
            sys.executable, "select_top100_simple.py"
        ], capture_output=True, text=True, cwd="/var/www/automatehub")
        
        print("STDOUT:")
        print(result.stdout)
        
        if result.stderr:
            print("STDERR:")
            print(result.stderr)
        
        print(f"Code de retour: {result.returncode}")
        
    except Exception as e:
        print(f"Erreur lors de l'exécution: {e}")

if __name__ == "__main__":
    import os
    run_selection()