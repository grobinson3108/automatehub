#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import json
import os
import re
import sys
from openai import OpenAI

# Configuration OpenAI
OPENAI_API_KEY = "YOUR_OPENAI_API_KEY"  # À remplacer par votre clé
MODEL = "gpt-4"  # ou "gpt-3.5-turbo" pour réduire les coûts

def translate_text(text, context=""):
    """Traduire un texte de l'anglais vers le français en préservant le formatage"""
    if not text or not isinstance(text, str):
        return text
    
    # Ne pas traduire certains éléments techniques
    tech_terms_to_preserve = [
        r'\{\{.*?\}\}',  # Expressions n8n
        r'\$\(.*?\)',    # Références de nodes
        r'https?://.*?(?=\s|$)',  # URLs
        r'[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}',  # Emails
        r'\b[A-Z][a-z]+[A-Z][a-zA-Z]*\b',  # CamelCase (noms de classes/méthodes)
    ]
    
    # Marquer les termes techniques pour les préserver
    preserved = []
    temp_text = text
    for pattern in tech_terms_to_preserve:
        matches = re.finditer(pattern, temp_text)
        for i, match in enumerate(matches):
            placeholder = f"__PRESERVE_{len(preserved)}__"
            preserved.append(match.group())
            temp_text = temp_text.replace(match.group(), placeholder, 1)
    
    # Appel à l'API OpenAI
    client = OpenAI(api_key=OPENAI_API_KEY)
    
    prompt = f"""Traduis le texte suivant de l'anglais vers le français.
Contexte: {context}
Règles importantes:
1. Préserve EXACTEMENT tout le formatage (markdown, sauts de ligne, espaces)
2. Ne traduis PAS les placeholders qui commencent par __PRESERVE_
3. Garde un ton professionnel et technique
4. Pour les termes techniques sans équivalent français, garde l'anglais

Texte à traduire:
{temp_text}"""

    try:
        response = client.chat.completions.create(
            model=MODEL,
            messages=[
                {"role": "system", "content": "Tu es un traducteur technique spécialisé en automatisation et workflows n8n."},
                {"role": "user", "content": prompt}
            ],
            temperature=0.3
        )
        
        translated = response.choices[0].message.content.strip()
        
        # Restaurer les termes préservés
        for i, preserved_text in enumerate(preserved):
            placeholder = f"__PRESERVE_{i}__"
            translated = translated.replace(placeholder, preserved_text)
        
        return translated
    
    except Exception as e:
        print(f"Erreur de traduction: {e}")
        return text

def translate_node_name(name):
    """Traduire le nom d'un node de manière concise"""
    # Mapping des noms courants
    common_mappings = {
        "Gmail Trigger": "Déclencheur Gmail",
        "Microsoft Outlook Trigger": "Déclencheur Microsoft Outlook",
        "Screenshot HTML": "Capture d'écran HTML",
        "Retrieve Screenshot": "Récupérer la capture d'écran",
        "Set Variables": "Définir les variables",
        "Format Headers": "Formater les en-têtes",
        "Manual Trigger": "Déclencheur manuel",
        "Webhook": "Webhook",
        "HTTP Request": "Requête HTTP",
        "Set": "Définir",
        "Code": "Code",
        "If": "Si",
        "Switch": "Aiguillage",
        "Merge": "Fusionner",
        "Split In Batches": "Diviser en lots",
        "Wait": "Attendre",
        "No Operation": "Aucune opération",
        "Sticky Note": "Note adhésive"
    }
    
    # Chercher une correspondance exacte d'abord
    for eng, fr in common_mappings.items():
        if eng in name:
            return name.replace(eng, fr)
    
    # Sinon, traduire avec l'API
    return translate_text(name, "Nom de node n8n - Doit être court et descriptif")

def translate_workflow(workflow_data):
    """Traduire tous les éléments textuels d'un workflow n8n"""
    translated = json.loads(json.dumps(workflow_data))  # Deep copy
    
    # Traduire le nom du workflow si présent
    if 'name' in translated:
        print(f"Traduction du nom du workflow: {translated['name']}")
        translated['name'] = translate_text(translated['name'], "Nom du workflow")
    
    # Traduire chaque node
    if 'nodes' in translated:
        total_nodes = len(translated['nodes'])
        for i, node in enumerate(translated['nodes']):
            print(f"Traduction du node {i+1}/{total_nodes}: {node.get('name', 'Sans nom')}")
            
            # Traduire le nom du node
            if 'name' in node:
                node['name'] = translate_node_name(node['name'])
            
            # Traduire les notes
            if 'notes' in node:
                print(f"  - Traduction des notes")
                node['notes'] = translate_text(node['notes'], "Notes du node")
            
            # Traduire les sticky notes
            if node.get('type') == 'n8n-nodes-base.stickyNote' and 'parameters' in node:
                if 'content' in node['parameters']:
                    print(f"  - Traduction de la sticky note")
                    node['parameters']['content'] = translate_text(
                        node['parameters']['content'], 
                        "Contenu d'une note adhésive dans un workflow"
                    )
            
            # Traduire les paramètres spécifiques
            if 'parameters' in node:
                # Pour les nodes OpenAI/ChatGPT
                if node.get('type') in ['@n8n/n8n-nodes-langchain.openAi', 'n8n-nodes-base.openAi']:
                    if 'text' in node['parameters']:
                        print(f"  - Traduction du prompt IA")
                        node['parameters']['text'] = translate_text(
                            node['parameters']['text'],
                            "Prompt pour ChatGPT/OpenAI"
                        )
                
                # Pour les nodes Set
                if node.get('type') == 'n8n-nodes-base.set' and 'assignments' in node['parameters']:
                    if 'assignments' in node['parameters'].get('assignments', {}):
                        for assignment in node['parameters']['assignments']['assignments']:
                            if 'name' in assignment:
                                print(f"  - Traduction de la variable: {assignment['name']}")
                                assignment['name'] = translate_text(
                                    assignment['name'],
                                    "Nom de variable"
                                )
                
                # Pour les nodes Email
                if 'subject' in node['parameters']:
                    print(f"  - Traduction du sujet email")
                    node['parameters']['subject'] = translate_text(
                        node['parameters']['subject'],
                        "Sujet d'email"
                    )
                
                # Pour les nodes avec description
                if 'description' in node['parameters']:
                    print(f"  - Traduction de la description")
                    node['parameters']['description'] = translate_text(
                        node['parameters']['description'],
                        "Description"
                    )
    
    return translated

def main():
    if len(sys.argv) < 2:
        print("Usage: python translate_workflow.py <workflow_file.json>")
        sys.exit(1)
    
    input_file = sys.argv[1]
    if not os.path.exists(input_file):
        print(f"Erreur: Le fichier {input_file} n'existe pas")
        sys.exit(1)
    
    # Lire le workflow
    print(f"Lecture du workflow: {input_file}")
    with open(input_file, 'r', encoding='utf-8') as f:
        workflow_data = json.load(f)
    
    # Traduire
    print("\nDébut de la traduction...")
    translated_workflow = translate_workflow(workflow_data)
    
    # Sauvegarder
    output_file = input_file.replace('.json', '_FR.json')
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(translated_workflow, f, ensure_ascii=False, indent=2)
    
    print(f"\n✅ Traduction terminée! Fichier sauvegardé: {output_file}")

if __name__ == "__main__":
    main()