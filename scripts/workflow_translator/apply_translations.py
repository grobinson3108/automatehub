#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Étape 3: Appliquer les traductions au workflow original
"""
import json
import sys
import os

def get_nested_value(data, path):
    """Obtenir une valeur imbriquée dans un dictionnaire"""
    current = data
    for key in path:
        if isinstance(current, dict) and key in current:
            current = current[key]
        elif isinstance(current, list) and isinstance(key, int) and 0 <= key < len(current):
            current = current[key]
        else:
            return None
    return current

def set_nested_value(data, path, value):
    """Définir une valeur imbriquée dans un dictionnaire"""
    current = data
    for i, key in enumerate(path[:-1]):
        if isinstance(current, dict):
            if key not in current:
                # Créer la clé si elle n'existe pas
                if isinstance(path[i+1], int):
                    current[key] = []
                else:
                    current[key] = {}
            current = current[key]
        elif isinstance(current, list) and isinstance(key, int):
            while len(current) <= key:
                current.append({})
            current = current[key]
    
    # Définir la valeur finale
    if isinstance(current, dict):
        current[path[-1]] = value
    elif isinstance(current, list) and isinstance(path[-1], int):
        while len(current) <= path[-1]:
            current.append({})
        current[path[-1]] = value

def apply_translations(workflow_file, translations_file):
    """Appliquer les traductions au workflow"""
    # Charger le workflow original
    with open(workflow_file, 'r', encoding='utf-8') as f:
        workflow_data = json.load(f)
    
    # Charger les traductions
    with open(translations_file, 'r', encoding='utf-8') as f:
        translations_data = json.load(f)
    
    # Créer un mapping des anciens noms vers les nouveaux noms de nodes
    node_name_mapping = {}
    
    # Appliquer chaque traduction
    applied_count = 0
    for text_id, text_info in translations_data['texts'].items():
        if 'translated' in text_info and text_info['translated']:
            path = text_info['path']
            translated_text = text_info['translated']
            
            # Si c'est un message OpenAI avec le préfixe =, le remettre
            if text_info.get('has_equal_prefix', False):
                translated_text = '=' + translated_text
            
            # Si c'est un nom de node, sauvegarder le mapping
            if text_info['type'] == 'node_name' and len(path) == 3:
                old_name = get_nested_value(workflow_data, path)
                if old_name:
                    node_name_mapping[old_name] = translated_text
            
            # Appliquer la traduction
            set_nested_value(workflow_data, path, translated_text)
            applied_count += 1
    
    # Mettre à jour les connexions avec les nouveaux noms de nodes
    if 'connections' in workflow_data and node_name_mapping:
        new_connections = {}
        
        for source_node, connections in workflow_data['connections'].items():
            # Traduire le nom du node source
            new_source_name = node_name_mapping.get(source_node, source_node)
            new_connections[new_source_name] = {}
            
            for conn_type, conn_list in connections.items():
                new_connections[new_source_name][conn_type] = []
                
                for conn_group in conn_list:
                    new_group = []
                    for conn in conn_group:
                        # Traduire le nom du node de destination
                        if 'node' in conn:
                            old_dest_name = conn['node']
                            new_dest_name = node_name_mapping.get(old_dest_name, old_dest_name)
                            conn['node'] = new_dest_name
                        new_group.append(conn)
                    new_connections[new_source_name][conn_type].append(new_group)
        
        workflow_data['connections'] = new_connections
    
    # Ajouter le tag Audelalia
    if 'tags' not in workflow_data:
        workflow_data['tags'] = []
    
    has_audelalia = any(tag.get('name') == 'Audelalia' for tag in workflow_data['tags'])
    if not has_audelalia:
        workflow_data['tags'].append({
            'id': '1',
            'name': 'Audelalia'
        })
    
    # Sauvegarder le workflow traduit dans le même répertoire que le fichier source
    source_dir = os.path.dirname(os.path.abspath(workflow_file))
    basename = os.path.basename(workflow_file)
    output_file = os.path.join(source_dir, basename.replace('.json', '_FR.json'))
    
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(workflow_data, f, ensure_ascii=False, indent=2)
    
    print(f"✅ Application des traductions terminée!")
    print(f"📊 {applied_count} traductions appliquées")
    print(f"💾 Workflow traduit sauvegardé: {output_file}")
    
    return output_file

def main():
    if len(sys.argv) < 3:
        print("Usage: python apply_translations.py <workflow.json> <translations.json>")
        sys.exit(1)
    
    workflow_file = sys.argv[1]
    translations_file = sys.argv[2]
    
    # Si le fichier de traductions n'a pas de chemin, le chercher dans le répertoire du script
    if not os.path.exists(translations_file):
        script_dir = os.path.dirname(os.path.abspath(__file__))
        translations_file = os.path.join(script_dir, translations_file)
    
    apply_translations(workflow_file, translations_file)

if __name__ == "__main__":
    main()