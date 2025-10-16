#!/usr/bin/env python3
"""
Script de traduction intelligent qui utilise OpenAI et un dictionnaire de fallback.
"""

import sys
import json
import os
import requests
from pathlib import Path
from time import sleep

def get_openai_key():
    """Récupérer la clé OpenAI depuis les variables d'environnement ou le .env"""
    # D'abord essayer dans l'environnement
    key = os.environ.get('OPENAI_API_KEY')

    # Sinon chercher dans le .env
    if not key:
        env_file = '/var/www/automatehub/.env'
        if os.path.exists(env_file):
            with open(env_file, 'r') as f:
                for line in f:
                    if line.strip().startswith('OPENAI_API_KEY='):
                        key = line.strip().split('=', 1)[1].strip('"\'')
                        break

    return key

def get_translation_dictionary():
    """Dictionnaire de traduction basique"""
    return {
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
        "Claude": "Claude",

        # Textes courants
        "Welcome Message": "Message de Bienvenue",
        "Hello and welcome to our service!": "Bonjour et bienvenue dans notre service !",
        "Simple Email Workflow": "Workflow Email Simple",
        "Create": "Créer",
        "Update": "Mettre à jour",
        "Delete": "Supprimer",
        "Generate": "Générer",
        "Process": "Traiter",
        "Transform": "Transformer",
        "Filter": "Filtrer",
        "Sort": "Trier",
        "Format": "Formater",

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
        "value": "valeur",
        "input": "entrée",
        "output": "sortie",

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
        "service": "service",
        "request": "requête",
        "response": "réponse",
        "data": "données",
        "file": "fichier",
        "document": "document",
        "image": "image",
        "video": "vidéo",
        "audio": "audio"
    }

def translate_with_openai(texts_to_translate, api_key):
    """Traduire les textes avec OpenAI"""
    if not texts_to_translate:
        return {}

    # Préparer le contenu pour OpenAI
    content_lines = []
    for i, (key, value) in enumerate(texts_to_translate.items()):
        content_lines.append(f"${i}={key}:{value}")

    content_to_translate = '\n'.join(content_lines)

    prompt = f"""Tu es un expert en traduction de workflows n8n de l'anglais vers le français.

Traduis ces textes de manière naturelle et professionnelle :

Règles importantes:
1. PRÉSERVER EXACTEMENT les variables entre {{{{ }}}} et $()
2. PRÉSERVER les URLs, emails, et références techniques
3. Traduire de manière fluide et naturelle
4. Garder le même format de réponse

Textes à traduire:
{content_to_translate}

Réponds avec le même format mais traduit en français."""

    headers = {
        'Authorization': f'Bearer {api_key}',
        'Content-Type': 'application/json'
    }

    data = {
        'model': 'gpt-3.5-turbo',
        'messages': [
            {
                'role': 'system',
                'content': 'Tu es un traducteur expert. Tu réponds uniquement avec les traductions demandées.'
            },
            {
                'role': 'user',
                'content': prompt
            }
        ],
        'temperature': 0.3,
        'max_tokens': 2000
    }

    try:
        response = requests.post(
            'https://api.openai.com/v1/chat/completions',
            headers=headers,
            json=data,
            timeout=30
        )

        if response.status_code == 200:
            result = response.json()
            content = result['choices'][0]['message']['content'].strip()

            # Parser les traductions
            translations = {}
            for line in content.split('\n'):
                if '=' in line and ':' in line:
                    parts = line.split('=', 1)
                    if len(parts) == 2:
                        index = parts[0].strip('$')
                        key_value = parts[1].split(':', 1)
                        if len(key_value) == 2:
                            key = key_value[0]
                            translated = key_value[1]
                            translations[key] = translated

            return translations

        else:
            print(f"❌ Erreur API OpenAI: {response.status_code}")
            return {}

    except Exception as e:
        print(f"❌ Erreur OpenAI: {str(e)}")
        return {}

def translate_text_intelligent(text, translations_dict, openai_translations=None):
    """Traduire un texte avec intelligence (OpenAI + dictionnaire)"""
    if not text or not isinstance(text, str):
        return text

    # D'abord essayer les traductions OpenAI
    if openai_translations and text in openai_translations:
        return openai_translations[text]

    # Ensuite utiliser le dictionnaire
    translated = text
    for en, fr in translations_dict.items():
        if en in translated:
            translated = translated.replace(en, fr)
        elif en.lower() in translated.lower():
            # Remplacement insensible à la casse
            pos = translated.lower().find(en.lower())
            if pos != -1:
                before = translated[:pos]
                after = translated[pos + len(en):]
                # Adapter la casse
                if translated[pos:pos+len(en)].isupper():
                    replacement = fr.upper()
                elif translated[pos:pos+len(en)].istitle():
                    replacement = fr.title()
                else:
                    replacement = fr
                translated = before + replacement + after

    return translated

def translate_workflow_intelligent(input_file_path, use_openai=True):
    """Traduire un workflow avec traduction intelligente"""
    try:
        input_path = Path(input_file_path)

        if not input_path.exists():
            raise FileNotFoundError(f"Fichier non trouvé: {input_file_path}")

        print(f"🔄 Traduction intelligente du workflow: {input_path.name}")

        # Charger le workflow
        with open(input_path, 'r', encoding='utf-8') as f:
            workflow_data = json.load(f)

        print(f"✅ Workflow chargé: {len(workflow_data.get('nodes', []))} nœuds")

        # Obtenir le dictionnaire de traduction
        translation_dict = get_translation_dictionary()

        # Collecter tous les textes à traduire pour OpenAI
        texts_to_translate = {}
        node_name_mapping = {}

        # Collecter les textes
        if 'name' in workflow_data and workflow_data['name']:
            texts_to_translate['workflow_name'] = workflow_data['name']

        if 'nodes' in workflow_data:
            for i, node in enumerate(workflow_data['nodes']):
                if 'name' in node and node['name']:
                    key = f"node_{i}_name"
                    texts_to_translate[key] = node['name']

                # Paramètres textuels
                if 'parameters' in node:
                    for param_key, param_value in node['parameters'].items():
                        if isinstance(param_value, str) and param_value.strip():
                            if not (param_value.startswith('http') or param_value.startswith('{{') or param_value.startswith('$')):
                                key = f"node_{i}_{param_key}"
                                texts_to_translate[key] = param_value

        # Traduction avec OpenAI si disponible
        openai_translations = {}
        if use_openai:
            api_key = get_openai_key()
            if api_key:
                print("🤖 Traduction avec OpenAI...")
                openai_translations = translate_with_openai(texts_to_translate, api_key)
                print(f"✅ {len(openai_translations)} textes traduits par OpenAI")
            else:
                print("⚠️  Clé OpenAI non disponible, utilisation du dictionnaire seulement")

        print("📚 Application des traductions...")

        # Traduire le nom du workflow
        if 'name' in workflow_data and workflow_data['name']:
            original_name = workflow_data['name']
            translated_name = translate_text_intelligent(
                original_name,
                translation_dict,
                openai_translations
            )
            workflow_data['name'] = translated_name
            print(f"📝 Nom traduit: {original_name} → {translated_name}")

        # Traduire les nœuds
        if 'nodes' in workflow_data:
            for i, node in enumerate(workflow_data['nodes']):
                # Traduire le nom du nœud
                if 'name' in node and node['name']:
                    original_name = node['name']
                    translated_name = translate_text_intelligent(
                        original_name,
                        translation_dict,
                        openai_translations
                    )
                    node_name_mapping[original_name] = translated_name
                    node['name'] = translated_name
                    print(f"🔧 Nœud traduit: {original_name} → {translated_name}")

                # Traduire les paramètres
                if 'parameters' in node:
                    for param_key, param_value in node['parameters'].items():
                        if isinstance(param_value, str) and param_value.strip():
                            if not (param_value.startswith('http') or param_value.startswith('{{') or param_value.startswith('$')):
                                translated_value = translate_text_intelligent(
                                    param_value,
                                    translation_dict,
                                    openai_translations
                                )
                                node['parameters'][param_key] = translated_value

        # Mettre à jour les connexions
        if 'connections' in workflow_data and node_name_mapping:
            new_connections = {}
            for source_node, connection_data in workflow_data['connections'].items():
                new_source_name = node_name_mapping.get(source_node, source_node)
                new_connections[new_source_name] = {}

                for conn_type, conn_list in connection_data.items():
                    new_connections[new_source_name][conn_type] = []
                    for conn_group in conn_list:
                        new_group = []
                        for conn in conn_group:
                            if isinstance(conn, dict) and 'node' in conn:
                                old_dest_name = conn['node']
                                new_dest_name = node_name_mapping.get(old_dest_name, old_dest_name)
                                conn['node'] = new_dest_name
                            new_group.append(conn)
                        new_connections[new_source_name][conn_type].append(new_group)

            workflow_data['connections'] = new_connections

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
        print(f"🎯 Traduction intelligente terminée avec succès!")

        return True

    except Exception as e:
        print(f"❌ Erreur lors de la traduction: {str(e)}")
        return False

def main():
    """Point d'entrée principal"""
    if len(sys.argv) != 2:
        print("Usage: python3 intelligent_translate.py /path/to/workflow.json")
        sys.exit(1)

    input_file = sys.argv[1]

    print("🚀 Démarrage de la traduction intelligente...")
    print(f"📁 Fichier d'entrée: {input_file}")

    success = translate_workflow_intelligent(input_file, use_openai=True)

    if success:
        print("🎉 Traduction terminée avec succès!")
        sys.exit(0)
    else:
        print("💥 Échec de la traduction")
        sys.exit(1)

if __name__ == "__main__":
    main()