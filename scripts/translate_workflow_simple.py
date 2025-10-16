#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import json
import os
import re
import sys

# Dictionnaire de traduction pour les termes courants
TRANSLATIONS = {
    # Noms de nodes
    "Gmail Trigger": "Déclencheur Gmail",
    "Microsoft Outlook Trigger": "Déclencheur Microsoft Outlook",
    "Screenshot HTML": "Capture d'écran HTML",
    "Retrieve Screenshot": "Récupérer la capture d'écran",
    "Set Gmail Variables": "Définir les variables Gmail",
    "Set Outlook Variables": "Définir les variables Outlook",
    "Set Email Variables": "Définir les variables Email",
    "Format Headers": "Formater les en-têtes",
    "Retrieve Headers of Email": "Récupérer les en-têtes de l'email",
    "ChatGPT Analysis": "Analyse ChatGPT",
    "Create Jira Ticket": "Créer un ticket Jira",
    "Rename Screenshot": "Renommer la capture d'écran",
    "Upload Screenshot of Email to Jira": "Télécharger la capture d'écran de l'email vers Jira",
    "Sticky Note": "Note adhésive",
    
    # Variables communes
    "htmlBody": "corpsHtml",
    "headers": "enTetes",
    "subject": "sujet",
    "recipient": "destinataire",
    "textBody": "corpsTexte",
    "body": "corps",
    
    # Termes dans le contenu
    "Gmail Integration and Data Extraction": "Intégration Gmail et extraction de données",
    "Microsoft Outlook Integration and Email Header Processing": "Intégration Microsoft Outlook et traitement des en-têtes d'email",
    "HTML Screenshot Generation and Email Visualization": "Génération de capture d'écran HTML et visualisation d'email",
    "AI-Powered Email Analysis with ChatGPT": "Analyse d'email alimentée par IA avec ChatGPT",
    "Automated Jira Ticket Creation for Phishing Reports": "Création automatique de tickets Jira pour les rapports de phishing",
    
    # Phrases courantes
    "This section": "Cette section",
    "connects to": "se connecte à",
    "using the": "utilisant le",
    "node": "nœud",
    "capturing incoming emails": "capturant les emails entrants",
    "in real-time": "en temps réel",
    "with checks performed every minute": "avec des vérifications effectuées toutes les minutes",
    "Once an email is detected": "Une fois qu'un email est détecté",
    "such as": "tels que",
    "are extracted and assigned to variables": "sont extraits et assignés à des variables",
    "for subsequent analysis": "pour l'analyse ultérieure",
    "Phishing Email Reported": "Email de phishing signalé",
    "Here is ChatGPT's analysis of the email": "Voici l'analyse de l'email par ChatGPT",
}

def translate_text(text, preserve_technical=True):
    """Traduire un texte en utilisant le dictionnaire"""
    if not text or not isinstance(text, str):
        return text
    
    result = text
    
    # Préserver les éléments techniques
    preserved = []
    if preserve_technical:
        # Patterns à préserver
        patterns = [
            (r'\{\{.*?\}\}', 'EXPR'),  # Expressions n8n
            (r'\$\(.*?\)', 'REF'),      # Références de nodes
            (r'https?://[^\s]+', 'URL'), # URLs
            (r'!\[.*?\]\(.*?\)', 'IMG'), # Images markdown
        ]
        
        for pattern, prefix in patterns:
            matches = list(re.finditer(pattern, result))
            for i, match in enumerate(reversed(matches)):
                placeholder = f"__{prefix}_{len(preserved)}__"
                preserved.insert(0, match.group())
                result = result[:match.start()] + placeholder + result[match.end():]
    
    # Appliquer les traductions
    for eng, fr in sorted(TRANSLATIONS.items(), key=lambda x: len(x[0]), reverse=True):
        result = result.replace(eng, fr)
    
    # Restaurer les éléments préservés
    for i, preserved_text in enumerate(preserved):
        for prefix in ['EXPR', 'REF', 'URL', 'IMG']:
            placeholder = f"__{prefix}_{i}__"
            if placeholder in result:
                result = result.replace(placeholder, preserved_text)
                break
    
    return result

def translate_workflow(workflow_data):
    """Traduire tous les éléments textuels d'un workflow n8n"""
    translated = json.loads(json.dumps(workflow_data))  # Deep copy
    
    # Traduire le nom du workflow si présent
    if 'name' in translated:
        translated['name'] = translate_text(translated['name'])
    
    # Traduire chaque node
    if 'nodes' in translated:
        for node in translated['nodes']:
            # Traduire le nom du node
            if 'name' in node:
                node['name'] = translate_text(node['name'])
            
            # Traduire les notes
            if 'notes' in node:
                node['notes'] = translate_text(node['notes'])
            
            # Traduire les sticky notes
            if node.get('type') == 'n8n-nodes-base.stickyNote' and 'parameters' in node:
                if 'content' in node['parameters']:
                    node['parameters']['content'] = translate_text(node['parameters']['content'])
            
            # Traduire les paramètres spécifiques
            if 'parameters' in node:
                # Pour les nodes OpenAI/ChatGPT
                if node.get('type') in ['@n8n/n8n-nodes-langchain.openAi', 'n8n-nodes-base.openAi']:
                    if 'text' in node['parameters']:
                        # Traduction spéciale pour les prompts
                        prompt = node['parameters']['text']
                        prompt = prompt.replace("Describe this image", "Décris cette image")
                        prompt = prompt.replace("Determine if the email could be a phishing email", "Détermine si l'email pourrait être un email de phishing")
                        prompt = prompt.replace("The message headers are as follows", "Les en-têtes du message sont les suivants")
                        prompt = prompt.replace("Format the response for Jira", "Formate la réponse pour Jira")
                        prompt = prompt.replace("Do not include ``` around your response", "N'inclus pas ``` autour de ta réponse")
                        node['parameters']['text'] = prompt
                
                # Pour les nodes Set
                if node.get('type') == 'n8n-nodes-base.set' and 'assignments' in node['parameters']:
                    if 'assignments' in node['parameters'].get('assignments', {}):
                        for assignment in node['parameters']['assignments']['assignments']:
                            if 'name' in assignment:
                                assignment['name'] = translate_text(assignment['name'], preserve_technical=False)
                
                # Pour les descriptions Jira
                if 'summary' in node['parameters'] and isinstance(node['parameters']['summary'], str):
                    node['parameters']['summary'] = node['parameters']['summary'].replace(
                        "Phishing Email Reported", "Email de phishing signalé"
                    )
                
                if 'additionalFields' in node['parameters']:
                    if 'description' in node['parameters']['additionalFields']:
                        desc = node['parameters']['additionalFields']['description']
                        desc = desc.replace("A phishing email was reported by", "Un email de phishing a été signalé par")
                        desc = desc.replace("with the subject line", "avec la ligne d'objet")
                        desc = desc.replace("and body", "et le corps")
                        desc = desc.replace("Here is ChatGPT's analysis of the email", "Voici l'analyse de l'email par ChatGPT")
                        node['parameters']['additionalFields']['description'] = desc
    
    return translated

def main():
    # Utiliser le fichier du workflow d'exemple
    input_file = "/var/www/automatehub/workflows/workflow_to_translate.json"
    
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
    
    # Afficher un aperçu
    print("\n📋 Aperçu des traductions effectuées:")
    print("- Nodes traduits:", len(translated_workflow.get('nodes', [])))
    for node in translated_workflow.get('nodes', [])[:5]:
        print(f"  • {node.get('name', 'Sans nom')}")

if __name__ == "__main__":
    main()