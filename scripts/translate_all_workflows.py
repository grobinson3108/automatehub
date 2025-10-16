#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import json
import os
import sys
import shutil
from datetime import datetime
from translate_workflow_complete import translate_workflow

def count_json_files(directory):
    """Compter le nombre de fichiers JSON dans un répertoire et ses sous-répertoires"""
    count = 0
    for root, _, files in os.walk(directory):
        for file in files:
            if file.endswith('.json') and not file.endswith('_FR.json'):
                count += 1
    return count

def translate_all_workflows(source_dir, target_dir):
    """Traduire tous les workflows d'un répertoire vers un autre"""
    
    # Créer le répertoire de destination s'il n'existe pas
    os.makedirs(target_dir, exist_ok=True)
    
    # Statistiques
    total_files = count_json_files(source_dir)
    translated = 0
    errors = 0
    skipped = 0
    
    print(f"📊 Total de workflows à traduire: {total_files}")
    print(f"📁 Source: {source_dir}")
    print(f"📁 Destination: {target_dir}")
    print("=" * 60)
    
    # Parcourir tous les fichiers
    for root, dirs, files in os.walk(source_dir):
        for file in files:
            if file.endswith('.json') and not file.endswith('_FR.json'):
                source_path = os.path.join(root, file)
                
                # Calculer le chemin relatif
                rel_path = os.path.relpath(source_path, source_dir)
                target_path = os.path.join(target_dir, rel_path)
                target_path = target_path.replace('.json', '_FR.json')
                
                # Créer le répertoire cible si nécessaire
                os.makedirs(os.path.dirname(target_path), exist_ok=True)
                
                try:
                    # Lire le workflow
                    with open(source_path, 'r', encoding='utf-8') as f:
                        content = f.read()
                        
                        # Vérifier si c'est un JSON valide
                        try:
                            workflow_data = json.loads(content)
                        except json.JSONDecodeError:
                            print(f"⚠️  JSON invalide: {rel_path}")
                            skipped += 1
                            continue
                    
                    # Vérifier si c'est bien un workflow n8n
                    if not isinstance(workflow_data, dict) or 'nodes' not in workflow_data:
                        print(f"⚠️  Pas un workflow n8n: {rel_path}")
                        skipped += 1
                        continue
                    
                    # Traduire le workflow
                    translated_workflow = translate_workflow(workflow_data)
                    
                    # Corriger les connexions
                    if 'connections' in translated_workflow and 'nodes' in translated_workflow:
                        # Créer un mapping ancien nom -> nouveau nom
                        name_mapping = {}
                        
                        # D'abord récupérer le mapping depuis les nodes originaux et traduits
                        original_nodes = workflow_data.get('nodes', [])
                        translated_nodes = translated_workflow.get('nodes', [])
                        
                        for i, node in enumerate(original_nodes):
                            if i < len(translated_nodes):
                                old_name = node.get('name', '')
                                new_name = translated_nodes[i].get('name', '')
                                if old_name and new_name and old_name != new_name:
                                    name_mapping[old_name] = new_name
                        
                        # Appliquer le mapping aux connexions
                        new_connections = {}
                        for source_node, connections in translated_workflow['connections'].items():
                            # Traduire le nom du node source
                            new_source = name_mapping.get(source_node, source_node)
                            new_connections[new_source] = connections
                            
                            # Traduire les noms des nodes de destination
                            if isinstance(connections, dict):
                                for conn_type, conn_list in connections.items():
                                    if isinstance(conn_list, list):
                                        for i, conn_group in enumerate(conn_list):
                                            if isinstance(conn_group, list):
                                                for j, conn in enumerate(conn_group):
                                                    if isinstance(conn, dict) and 'node' in conn:
                                                        old_target = conn['node']
                                                        conn['node'] = name_mapping.get(old_target, old_target)
                        
                        translated_workflow['connections'] = new_connections
                    
                    # Sauvegarder le workflow traduit
                    with open(target_path, 'w', encoding='utf-8') as f:
                        json.dump(translated_workflow, f, ensure_ascii=False, indent=2)
                    
                    translated += 1
                    
                    # Afficher la progression
                    if translated % 10 == 0:
                        progress = (translated + errors + skipped) / total_files * 100
                        print(f"🔄 Progression: {progress:.1f}% ({translated} traduits, {errors} erreurs, {skipped} ignorés)")
                
                except Exception as e:
                    print(f"❌ Erreur avec {rel_path}: {str(e)}")
                    errors += 1
    
    # Rapport final
    print("\n" + "=" * 60)
    print("✅ TRADUCTION TERMINÉE!")
    print(f"📊 Statistiques finales:")
    print(f"   - Total de fichiers: {total_files}")
    print(f"   - Traduits avec succès: {translated}")
    print(f"   - Erreurs: {errors}")
    print(f"   - Ignorés: {skipped}")
    print(f"   - Temps écoulé: {datetime.now()}")
    
    return translated, errors, skipped

def main():
    # Répertoires par défaut
    source_dirs = [
        "/var/www/automatehub/200_automations_n8n",
        "/var/www/automatehub/github_workflows"
    ]
    
    # Demander confirmation
    print("🌐 TRADUCTION EN MASSE DES WORKFLOWS N8N")
    print("=" * 60)
    print("Ce script va traduire tous les workflows des répertoires suivants:")
    for dir in source_dirs:
        if os.path.exists(dir):
            count = count_json_files(dir)
            print(f"  - {dir}: {count} workflows")
    
    print("\nLes workflows traduits seront sauvegardés dans:")
    print("  - /var/www/automatehub/workflows_traduits/")
    
    response = input("\n⚠️  Voulez-vous continuer? (o/n): ")
    if response.lower() != 'o':
        print("Annulé.")
        return
    
    # Créer le répertoire de destination principal
    target_base = "/var/www/automatehub/workflows_traduits"
    os.makedirs(target_base, exist_ok=True)
    
    # Traiter chaque répertoire source
    total_translated = 0
    total_errors = 0
    total_skipped = 0
    
    for source_dir in source_dirs:
        if os.path.exists(source_dir):
            print(f"\n📂 Traitement de {source_dir}...")
            
            # Déterminer le nom du sous-répertoire cible
            dir_name = os.path.basename(source_dir)
            target_dir = os.path.join(target_base, dir_name)
            
            # Traduire les workflows
            translated, errors, skipped = translate_all_workflows(source_dir, target_dir)
            
            total_translated += translated
            total_errors += errors
            total_skipped += skipped
    
    # Rapport global final
    print("\n" + "=" * 60)
    print("🎉 TRADUCTION GLOBALE TERMINÉE!")
    print(f"📊 Résumé global:")
    print(f"   - Workflows traduits: {total_translated}")
    print(f"   - Erreurs totales: {total_errors}")
    print(f"   - Fichiers ignorés: {total_skipped}")
    print(f"   - Répertoire de sortie: {target_base}")

if __name__ == "__main__":
    main()