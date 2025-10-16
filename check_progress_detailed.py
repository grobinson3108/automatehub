#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Vérifier la progression détaillée avec estimation du temps restant
"""

import os
import time
import json

# Dossiers
mapping_dir = "/var/www/automatehub/translation_mappings"
workflows_dir = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"

# Compter les mappings et leur temps de création
mappings = []
for f in os.listdir(mapping_dir):
    if f.endswith('.mapping.json'):
        path = os.path.join(mapping_dir, f)
        mtime = os.path.getmtime(path)
        mappings.append((f, mtime))

# Trier par temps de création
mappings.sort(key=lambda x: x[1])

# Compter les workflows totaux
total_workflows = sum(1 for root, dirs, files in os.walk(workflows_dir) 
                     for file in files if file.endswith('.json'))

print(f"📊 PROGRESSION DÉTAILLÉE DE LA TRADUCTION")
print(f"=" * 60)
print(f"📁 Workflows totaux : {total_workflows}")
print(f"✅ Workflows traduits : {len(mappings)}")
print(f"📈 Progression : {len(mappings)/total_workflows*100:.1f}%")
print(f"⏳ Restants : {total_workflows - len(mappings)}")

# Calculer la vitesse moyenne si on a plus de 2 workflows
if len(mappings) >= 2:
    # Temps entre le premier et le dernier
    temps_total = mappings[-1][1] - mappings[0][1]
    workflows_traites = len(mappings) - 1
    
    if workflows_traites > 0:
        temps_par_workflow = temps_total / workflows_traites
        restants = total_workflows - len(mappings)
        temps_restant = restants * temps_par_workflow
        
        print(f"\n⏱️  ESTIMATION DU TEMPS")
        print(f"  Vitesse moyenne : {temps_par_workflow:.1f} secondes/workflow")
        print(f"  Temps restant estimé : {temps_restant/60:.1f} minutes")
        
        # Heure de fin estimée
        heure_fin = time.time() + temps_restant
        print(f"  Heure de fin estimée : {time.strftime('%H:%M', time.localtime(heure_fin))}")

# Vérifier la qualité d'un workflow récent
print(f"\n🔍 EXEMPLES DE TRADUCTIONS RÉCENTES")
print(f"-" * 60)

# Prendre les 3 derniers mappings
for mapping_file, _ in mappings[-3:]:
    try:
        with open(os.path.join(mapping_dir, mapping_file), 'r', encoding='utf-8') as f:
            data = json.load(f)
        
        workflow_name = mapping_file.replace('.mapping.json', '')
        texts_map = data.get('texts_map', {})
        
        print(f"\n📄 {workflow_name}")
        print(f"   Textes traduits : {len(texts_map)}")
        
        # Afficher quelques exemples
        for i, (placeholder, info) in enumerate(list(texts_map.items())[:2]):
            original = info['original'][:60] + "..." if len(info['original']) > 60 else info['original']
            context = info['context']
            print(f"   - [{context}] {original}")
            
    except Exception as e:
        print(f"   Erreur lecture mapping: {e}")

# Vérifier si le processus est toujours actif
import subprocess
try:
    result = subprocess.run(['pgrep', '-f', 'translate_workflow_mapping'], 
                          capture_output=True, text=True)
    if result.returncode == 0:
        print(f"\n✅ Processus de traduction EN COURS (PID: {result.stdout.strip()})")
    else:
        print(f"\n⚠️  Processus de traduction TERMINÉ")
except:
    pass