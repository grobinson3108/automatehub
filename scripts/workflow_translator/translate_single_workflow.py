#!/usr/bin/env python3
"""
Script de traduction d'un workflow n8n unique pour l'interface web admin.
Usage: python3 translate_single_workflow.py /path/to/workflow.json
"""

import sys
import json
import os
import traceback
from pathlib import Path

# Ajouter le répertoire parent au PYTHONPATH pour les imports
sys.path.append(str(Path(__file__).parent))

try:
    from extract_texts import extract_texts_from_workflow
    from apply_translations import apply_translations_to_workflow
except ImportError as e:
    print(f"Erreur d'import: {e}")
    print("Vérifiez que tous les modules de traduction sont présents")
    sys.exit(1)

def load_translation_dictionary():
    """Charge le dictionnaire de traduction depuis le fichier principal."""
    dict_path = Path(__file__).parent.parent / "translate_workflow_complete.py"

    # Dictionnaire de base si le fichier principal n'est pas trouvé
    translation_dict = {
        # Noms de nœuds basiques
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
        "Anthropic": "Anthropic",

        # Variables et paramètres
        "variable": "variable",
        "parameter": "paramètre",
        "value": "valeur",
        "name": "nom",
        "description": "description",
        "type": "type",
        "default": "par défaut",
        "required": "requis",
        "optional": "optionnel",

        # Actions
        "send": "envoyer",
        "receive": "recevoir",
        "create": "créer",
        "update": "mettre à jour",
        "delete": "supprimer",
        "fetch": "récupérer",
        "process": "traiter",
        "transform": "transformer",
        "validate": "valider",
        "format": "formater",

        # Connecteurs et intégrations
        "API": "API",
        "database": "base de données",
        "spreadsheet": "feuille de calcul",
        "file": "fichier",
        "folder": "dossier",
        "document": "document",
        "image": "image",
        "video": "vidéo",
        "audio": "audio",

        # Interface et formulaires
        "form": "formulaire",
        "field": "champ",
        "input": "entrée",
        "output": "sortie",
        "button": "bouton",
        "link": "lien",
        "menu": "menu",
        "dropdown": "liste déroulante",

        # Dates et temps
        "date": "date",
        "time": "heure",
        "timestamp": "horodatage",
        "schedule": "planification",
        "interval": "intervalle",
        "delay": "délai",

        # États et conditions
        "active": "actif",
        "inactive": "inactif",
        "enabled": "activé",
        "disabled": "désactivé",
        "success": "succès",
        "error": "erreur",
        "warning": "avertissement",
        "info": "information",

        # Workflow et automation
        "workflow": "workflow",
        "automation": "automatisation",
        "trigger": "déclencheur",
        "action": "action",
        "condition": "condition",
        "loop": "boucle",
        "iteration": "itération",
        "execution": "exécution"
    }

    try:
        if dict_path.exists():
            with open(dict_path, 'r', encoding='utf-8') as f:
                content = f.read()
                # Extraire le dictionnaire de traduction du fichier principal
                if 'translation_dict = {' in content:
                    start = content.find('translation_dict = {')
                    # Trouver la fin du dictionnaire (recherche de la fermeture des accolades)
                    bracket_count = 0
                    i = start + len('translation_dict = ')
                    while i < len(content):
                        if content[i] == '{':
                            bracket_count += 1
                        elif content[i] == '}':
                            bracket_count -= 1
                            if bracket_count == 0:
                                break
                        i += 1

                    dict_str = content[start:i+1].replace('translation_dict = ', '')
                    try:
                        extracted_dict = eval(dict_str)
                        translation_dict.update(extracted_dict)
                        print("Dictionnaire de traduction chargé depuis le fichier principal")
                    except:
                        print("Utilisation du dictionnaire de base")
    except Exception as e:
        print(f"Erreur lors du chargement du dictionnaire: {e}")
        print("Utilisation du dictionnaire de base")

    return translation_dict

def translate_workflow_single(input_file_path):
    """Traduit un workflow unique et sauvegarde le résultat."""
    try:
        input_path = Path(input_file_path)

        if not input_path.exists():
            raise FileNotFoundError(f"Fichier non trouvé: {input_file_path}")

        print(f"🔄 Traduction du workflow: {input_path.name}")

        # Charger le workflow
        with open(input_path, 'r', encoding='utf-8') as f:
            workflow_data = json.load(f)

        print(f"✅ Workflow chargé: {len(workflow_data.get('nodes', []))} nœuds")

        # Étape 1: Extraction des textes
        print("🔍 Extraction des textes à traduire...")
        texts_to_translate = extract_texts_from_workflow(workflow_data)
        print(f"📝 {len(texts_to_translate)} textes extraits")

        # Étape 2: Traduction
        print("🌐 Traduction des textes...")

        # Charger le dictionnaire de traduction
        translation_dict = load_translation_dictionary()

        # Traduction basique avec dictionnaire
        translated_texts = {}
        for text_id, text_info in texts_to_translate.items():
            original_text = text_info['original']
            translated_text = original_text

            # Appliquer les traductions du dictionnaire
            for en, fr in translation_dict.items():
                if en.lower() in translated_text.lower():
                    translated_text = translated_text.replace(en, fr)

            translated_texts[text_id] = translated_text

        print(f"✅ {len(translated_texts)} textes traduits")

        # Étape 3: Application des traductions
        print("🔧 Application des traductions au workflow...")

        # Créer le mapping des traductions avec les informations de chemin
        translations_mapping = {}
        for text_id, translated_text in translated_texts.items():
            if text_id in texts_to_translate:
                text_info = texts_to_translate[text_id]
                translations_mapping[text_id] = {
                    'path': text_info['path'],
                    'original': text_info['original'],
                    'translated': translated_text,
                    'type': text_info['type']
                }

        translated_workflow = apply_translations_to_workflow(
            workflow_data,
            translations_mapping,
            add_tag="Audelalia"
        )

        # Définir le chemin de sortie
        output_path = input_path.parent / (input_path.stem + "_FR.json")

        # Sauvegarder le workflow traduit
        with open(output_path, 'w', encoding='utf-8') as f:
            json.dump(translated_workflow, f, indent=2, ensure_ascii=False)

        print(f"✅ Workflow traduit sauvegardé: {output_path}")
        print(f"🎯 Traduction terminée avec succès!")

        return True

    except Exception as e:
        print(f"❌ Erreur lors de la traduction: {str(e)}")
        print(f"📍 Détails: {traceback.format_exc()}")
        return False

def main():
    """Point d'entrée principal du script."""
    if len(sys.argv) != 2:
        print("Usage: python3 translate_single_workflow.py /path/to/workflow.json")
        sys.exit(1)

    input_file = sys.argv[1]

    print("🚀 Démarrage de la traduction de workflow...")
    print(f"📁 Fichier d'entrée: {input_file}")

    success = translate_workflow_single(input_file)

    if success:
        print("🎉 Traduction terminée avec succès!")
        sys.exit(0)
    else:
        print("💥 Échec de la traduction")
        sys.exit(1)

if __name__ == "__main__":
    main()