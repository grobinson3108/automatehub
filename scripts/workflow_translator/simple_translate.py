#!/usr/bin/env python3
"""
Script de traduction simplifié pour l'interface web.
"""

import sys
import json
import os
from pathlib import Path

def translate_workflow_simple(input_file_path):
    """Traduit un workflow avec un dictionnaire simple."""

    # Dictionnaire de traduction basique
    translation_dict = {
        # Noms de nœuds
        "HTTP Request": "Requête HTTP",
        "Set": "Définir",
        "Code": "Code",
        "Edit Fields": "Modifier Champs",
        "IF": "SI",
        "Switch": "Commutateur",
        "Merge": "Fusionner",
        "Split": "Diviser",
        "Wait": "Attendre",
        "Schedule Trigger": "Déclencheur Programmé",
        "Webhook": "Webhook",
        "Email": "Email",
        "Send Email": "Envoyer Email",
        "Gmail": "Gmail",
        "Google Sheets": "Google Sheets",
        "Telegram": "Telegram",
        "Discord": "Discord",
        "Slack": "Slack",
        "Twitter": "Twitter",
        "Facebook": "Facebook",
        "LinkedIn": "LinkedIn",
        "Instagram": "Instagram",
        "OpenAI": "OpenAI",
        "ChatGPT": "ChatGPT",

        # Textes courants
        "Welcome Message": "Message de Bienvenue",
        "Hello and welcome to our service!": "Bonjour et bienvenue dans notre service !",
        "Simple Email Workflow": "Workflow Email Simple",

        # Paramètres
        "subject": "objet",
        "body": "corps",
        "text": "texte",
        "message": "message",
        "content": "contenu",
        "description": "description",
        "summary": "résumé",
        "title": "titre",
        "name": "nom",

        # Actions
        "send": "envoyer",
        "receive": "recevoir",
        "create": "créer",
        "update": "mettre à jour",
        "delete": "supprimer",
        "fetch": "récupérer",
        "process": "traiter",
        "welcome": "bienvenue",
        "hello": "bonjour",
        "service": "service"
    }

    try:
        input_path = Path(input_file_path)

        if not input_path.exists():
            raise FileNotFoundError(f"Fichier non trouvé: {input_file_path}")

        print(f"🔄 Traduction du workflow: {input_path.name}")

        # Charger le workflow
        with open(input_path, 'r', encoding='utf-8') as f:
            workflow_data = json.load(f)

        print(f"✅ Workflow chargé: {len(workflow_data.get('nodes', []))} nœuds")

        # Créer un mapping des anciens noms vers les nouveaux noms
        node_name_mapping = {}

        # Traduire le nom du workflow
        if 'name' in workflow_data and workflow_data['name']:
            original_name = workflow_data['name']
            translated_name = translate_text(original_name, translation_dict)
            workflow_data['name'] = translated_name
            print(f"📝 Nom traduit: {original_name} → {translated_name}")

        # Traduire les nœuds
        if 'nodes' in workflow_data:
            for node in workflow_data['nodes']:
                # Traduire le nom du nœud
                if 'name' in node and node['name']:
                    original_name = node['name']
                    translated_name = translate_text(original_name, translation_dict)
                    node_name_mapping[original_name] = translated_name
                    node['name'] = translated_name
                    print(f"🔧 Nœud traduit: {original_name} → {translated_name}")

                # Traduire les paramètres
                if 'parameters' in node:
                    translate_parameters(node['parameters'], translation_dict)

        # Mettre à jour les connexions
        if 'connections' in workflow_data and node_name_mapping:
            update_connections(workflow_data['connections'], node_name_mapping)

        # Ajouter le tag Audelalia
        if 'tags' not in workflow_data:
            workflow_data['tags'] = []

        has_audelalia = any(
            (isinstance(tag, dict) and tag.get('name') == 'Audelalia') or
            (isinstance(tag, str) and tag == 'Audelalia')
            for tag in workflow_data['tags']
        )

        if not has_audelalia:
            workflow_data['tags'].append({
                'id': 'audelalia',
                'name': 'Audelalia'
            })

        # Sauvegarder le workflow traduit
        output_path = input_path.parent / (input_path.stem + "_FR.json")

        with open(output_path, 'w', encoding='utf-8') as f:
            json.dump(workflow_data, f, indent=2, ensure_ascii=False)

        print(f"✅ Workflow traduit sauvegardé: {output_path}")
        print(f"🎯 Traduction terminée avec succès!")

        return True

    except Exception as e:
        print(f"❌ Erreur lors de la traduction: {str(e)}")
        return False

def translate_text(text, translation_dict):
    """Traduit un texte en utilisant le dictionnaire."""
    if not text or not isinstance(text, str):
        return text

    translated = text

    # Appliquer les traductions (insensible à la casse pour la recherche)
    for en, fr in translation_dict.items():
        # Remplacement exact (sensible à la casse pour le résultat)
        if en in translated:
            translated = translated.replace(en, fr)
        # Remplacement insensible à la casse
        elif en.lower() in translated.lower():
            # Trouver la position et conserver la casse
            pos = translated.lower().find(en.lower())
            if pos != -1:
                # Préserver la casse du texte original autant que possible
                before = translated[:pos]
                after = translated[pos + len(en):]

                # Adapter la casse de la traduction
                if translated[pos:pos+len(en)].isupper():
                    replacement = fr.upper()
                elif translated[pos:pos+len(en)].istitle():
                    replacement = fr.title()
                else:
                    replacement = fr

                translated = before + replacement + after

    return translated

def translate_parameters(params, translation_dict):
    """Traduit récursivement les paramètres d'un nœud."""
    if isinstance(params, dict):
        for key, value in params.items():
            if isinstance(value, str) and value.strip():
                # Éviter de traduire les URLs, expressions, etc.
                if not (value.startswith('http') or value.startswith('{{') or value.startswith('$')):
                    params[key] = translate_text(value, translation_dict)
            elif isinstance(value, dict):
                translate_parameters(value, translation_dict)
            elif isinstance(value, list):
                for item in value:
                    if isinstance(item, dict):
                        translate_parameters(item, translation_dict)
                    elif isinstance(item, str) and item.strip():
                        # Pour les listes, on doit modifier l'item directement
                        pass  # À implémenter si nécessaire

def update_connections(connections, node_name_mapping):
    """Met à jour les connexions avec les nouveaux noms de nœuds."""
    # Créer un nouveau dictionnaire de connexions avec les noms traduits
    new_connections = {}

    for source_node, connection_data in connections.items():
        # Traduire le nom du nœud source
        new_source_name = node_name_mapping.get(source_node, source_node)
        new_connections[new_source_name] = {}

        for conn_type, conn_list in connection_data.items():
            new_connections[new_source_name][conn_type] = []

            for conn_group in conn_list:
                new_group = []
                for conn in conn_group:
                    if isinstance(conn, dict) and 'node' in conn:
                        # Traduire le nom du nœud de destination
                        old_dest_name = conn['node']
                        new_dest_name = node_name_mapping.get(old_dest_name, old_dest_name)
                        conn['node'] = new_dest_name
                    new_group.append(conn)
                new_connections[new_source_name][conn_type].append(new_group)

    # Remplacer les connexions originales
    connections.clear()
    connections.update(new_connections)

def main():
    """Point d'entrée principal."""
    if len(sys.argv) != 2:
        print("Usage: python3 simple_translate.py /path/to/workflow.json")
        sys.exit(1)

    input_file = sys.argv[1]

    print("🚀 Démarrage de la traduction de workflow...")
    print(f"📁 Fichier d'entrée: {input_file}")

    success = translate_workflow_simple(input_file)

    if success:
        print("🎉 Traduction terminée avec succès!")
        sys.exit(0)
    else:
        print("💥 Échec de la traduction")
        sys.exit(1)

if __name__ == "__main__":
    main()