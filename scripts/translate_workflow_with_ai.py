#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import json
import os
import sys
import openai
from time import sleep

# Configuration OpenAI
openai.api_key = os.getenv("OPENAI_API_KEY", "YOUR_API_KEY_HERE")

# Dictionnaire de traduction pour les éléments simples
SIMPLE_TRANSLATIONS = {
    # Noms de nodes courants
    "Gmail Trigger": "Déclencheur Gmail",
    "When clicking 'Test workflow'": "Lors du clic sur 'Tester le workflow'",
    "Text Classifier": "Classificateur de texte",
    "OpenAI Chat Model": "Modèle de Chat OpenAI",
    "Google Docs": "Google Docs",
    "Wait": "Attendre",
    "Set": "Définir",
    "Code": "Code",
    "HTTP Request": "Requête HTTP",
    "Manual Trigger": "Déclencheur manuel",
    "Email Send": "Envoyer Email",
    "Gmail": "Gmail",
    "Slack": "Slack",
    "Telegram": "Telegram",
    "Webhook": "Webhook",
    "Airtable": "Airtable",
    "Get image": "Récupérer l'image",
    "Send request": "Envoyer la requête",
    "Get image link": "Récupérer le lien de l'image",
    
    # Variables communes
    "htmlBody": "corpsHtml",
    "headers": "enTetes",
    "subject": "sujet",
    "recipient": "destinataire",
    "textBody": "corpsTexte",
    "body": "corps",
    "sender": "expediteur",
    "from": "de",
    "email": "email",
    "message": "message",
}

def translate_with_ai(text, context=""):
    """Traduire du texte en utilisant l'API OpenAI"""
    if not text or not isinstance(text, str) or len(text.strip()) < 10:
        return text
    
    try:
        response = openai.ChatCompletion.create(
            model="gpt-3.5-turbo",
            messages=[
                {
                    "role": "system",
                    "content": """Tu es un traducteur expert anglais-français spécialisé dans les workflows d'automatisation et n8n.
                    Règles importantes:
                    1. Traduire de l'anglais vers le français de manière naturelle
                    2. Préserver EXACTEMENT les variables entre {{ }}, $(), les URLs, et le code
                    3. Garder les noms de réseaux sociaux en anglais (LinkedIn, Instagram, Twitter, Facebook)
                    4. Traduire les placeholders entre crochets [key topic] → [sujet clé], etc.
                    5. Conserver la structure JSON si présente
                    6. Ne pas traduire les noms de champs JSON (linkedin, instagram, twitter, facebook)
                    7. Garder un ton professionnel adapté aux entreprises françaises"""
                },
                {
                    "role": "user",
                    "content": f"Traduis ce texte de workflow n8n en français:\n\n{text}"
                }
            ],
            temperature=0.3,
            max_tokens=2000
        )
        
        translated = response.choices[0].message.content
        
        # S'assurer que les variables sont préservées
        if "{{" in text:
            # Extraire toutes les variables du texte original
            import re
            variables = re.findall(r'\{\{[^}]+\}\}', text)
            for var in variables:
                if var not in translated:
                    # Si la variable a été modifiée, la restaurer
                    translated = translated.replace(var.replace("{{", "").replace("}}", ""), var)
        
        return translated
        
    except Exception as e:
        print(f"⚠️  Erreur API OpenAI: {str(e)}")
        # Fallback sur traduction basique
        return translate_simple(text)

def translate_simple(text):
    """Traduction simple par dictionnaire"""
    if not text or not isinstance(text, str):
        return text
    
    result = text
    for eng, fr in sorted(SIMPLE_TRANSLATIONS.items(), key=lambda x: len(x[0]), reverse=True):
        result = result.replace(eng, fr)
    
    return result

def translate_workflow(workflow_data):
    """Traduire tous les éléments textuels d'un workflow n8n"""
    translated = json.loads(json.dumps(workflow_data))  # Deep copy
    
    # Traduire le nom du workflow
    if 'name' in translated:
        translated['name'] = translate_simple(translated['name'])
    
    # Créer un mapping des anciens noms vers les nouveaux noms
    node_name_mapping = {}
    
    # Traduire chaque node
    if 'nodes' in translated:
        for node in translated['nodes']:
            # Sauvegarder l'ancien nom
            old_name = node.get('name', '')
            
            # Traduire le nom du node
            if 'name' in node:
                node['name'] = translate_simple(node['name'])
                node_name_mapping[old_name] = node['name']
            
            # Traduire les paramètres spécifiques
            if 'parameters' in node:
                # Pour les nodes OpenAI avec messages
                if node.get('type') in ['@n8n/n8n-nodes-langchain.openAi', 'n8n-nodes-base.openAi']:
                    if 'messages' in node['parameters'] and 'values' in node['parameters']['messages']:
                        for message in node['parameters']['messages']['values']:
                            if 'content' in message and isinstance(message['content'], str):
                                content = message['content']
                                # Retirer le = au début si présent
                                if content.startswith('='):
                                    content = content[1:]
                                    # Traduire avec l'IA
                                    print(f"🤖 Traduction AI du prompt dans '{node['name']}'...")
                                    translated_content = translate_with_ai(content)
                                    message['content'] = '=' + translated_content
                                    sleep(0.5)  # Éviter de surcharger l'API
                                else:
                                    message['content'] = translate_with_ai(content)
                
                # Pour les sticky notes
                if node.get('type') == 'n8n-nodes-base.stickyNote' and 'content' in node['parameters']:
                    node['parameters']['content'] = translate_simple(node['parameters']['content'])
                
                # Pour les nodes Set
                if node.get('type') == 'n8n-nodes-base.set' and 'assignments' in node['parameters']:
                    if 'assignments' in node['parameters'].get('assignments', {}):
                        for assignment in node['parameters']['assignments']['assignments']:
                            if 'name' in assignment and assignment['name'] in SIMPLE_TRANSLATIONS:
                                assignment['name'] = SIMPLE_TRANSLATIONS[assignment['name']]
    
    # Mettre à jour les connexions avec les nouveaux noms
    if 'connections' in translated and node_name_mapping:
        new_connections = {}
        
        for source_node, connections in translated['connections'].items():
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
        
        translated['connections'] = new_connections
    
    return translated

def add_audelalia_tag(workflow_data):
    """Ajouter le tag Audelalia au workflow"""
    if 'tags' not in workflow_data:
        workflow_data['tags'] = []
    
    # Vérifier si le tag existe déjà
    has_audelalia = any(tag.get('name') == 'Audelalia' for tag in workflow_data['tags'])
    
    if not has_audelalia:
        workflow_data['tags'].append({
            'id': '1',
            'name': 'Audelalia'
        })
    
    return workflow_data

def main():
    if len(sys.argv) < 2:
        print("Usage: python translate_workflow_with_ai.py <workflow_file.json>")
        sys.exit(1)
    
    input_file = sys.argv[1]
    
    if not os.path.exists(input_file):
        print(f"Erreur: Le fichier {input_file} n'existe pas")
        sys.exit(1)
    
    # Lire le workflow
    print(f"📖 Lecture du workflow: {input_file}")
    with open(input_file, 'r', encoding='utf-8') as f:
        workflow_data = json.load(f)
    
    # Traduire
    print("🔄 Début de la traduction avec IA...")
    translated_workflow = translate_workflow(workflow_data)
    
    # Ajouter le tag Audelalia
    translated_workflow = add_audelalia_tag(translated_workflow)
    
    # Sauvegarder
    output_file = input_file.replace('.json', '_FR_AI.json')
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(translated_workflow, f, ensure_ascii=False, indent=2)
    
    print(f"\n✅ Traduction terminée! Fichier sauvegardé: {output_file}")
    
    # Aperçu
    print("\n📋 Aperçu de la traduction:")
    print(f"- Nom: {translated_workflow.get('name', 'Sans nom')}")
    print(f"- Nodes traduits: {len(translated_workflow.get('nodes', []))}")
    print(f"- Tags: {[tag.get('name') for tag in translated_workflow.get('tags', [])]}")

if __name__ == "__main__":
    main()