#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import json
import os
import re
import sys

# Dictionnaire de traduction étendu
TRANSLATIONS = {
    # Noms de nodes
    "Gmail Trigger": "Déclencheur Gmail",
    "When clicking 'Test workflow'": "Lors du clic sur 'Tester le workflow'",
    "When clicking 'Test workflow'": "Lors du clic sur 'Tester le workflow'",
    "Generate social post ideas or summaries": "Générer des idées de publications sociales ou des résumés",
    "Text Classifier": "Classificateur de texte",
    "OpenAI Chat Model": "Modèle de Chat OpenAI",
    "Google Docs": "Google Docs",
    "Wait": "Attendre",
    "Get image": "Récupérer l'image",
    "Send request": "Envoyer la requête",
    "Get image link": "Récupérer le lien de l'image",
    "Life Style Product Photo Generator": "Générateur de Photos Lifestyle de Produits",
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
    "Manual Trigger": "Déclencheur manuel",
    "HTTP Request": "Requête HTTP",
    "Set": "Définir",
    "Code": "Code",
    "Email Send": "Envoyer Email",
    "Gmail": "Gmail",
    "Slack": "Slack",
    "Telegram": "Telegram",
    "Webhook": "Webhook",
    
    # Variables communes
    "htmlBody": "corpsHtml",
    "headers": "enTetes",
    "subject": "sujet",
    "recipient": "destinataire",
    "textBody": "corpsTexte",
    "body": "corps",
    "sender": "expediteur",
    "from": "de",
    # "to": "vers",  # Désactivé car cause des problèmes de traduction partielle
    "email": "email",
    "message": "message",
    
    # Titres et sections
    "Gmail Integration and Data Extraction": "Intégration Gmail et extraction de données",
    "Microsoft Outlook Integration and Email Header Processing": "Intégration Microsoft Outlook et traitement des en-têtes d'email",
    "HTML Screenshot Generation and Email Visualization": "Génération de capture d'écran HTML et visualisation d'email",
    "AI-Powered Email Analysis with ChatGPT": "Analyse d'email alimentée par IA avec ChatGPT",
    "Automated Jira Ticket Creation for Phishing Reports": "Création automatique de tickets Jira pour les rapports de phishing",
    
    # Phrases complètes
    "This section of the workflow": "Cette section du workflow",
    "connects to a Gmail account": "se connecte à un compte Gmail",
    "using the": "utilisant le",
    "node": "nœud",
    "capturing incoming emails in real-time": "capturant les emails entrants en temps réel",
    "with checks performed every minute": "avec des vérifications effectuées toutes les minutes",
    "Once an email is detected": "Une fois qu'un email est détecté",
    "its key components": "ses composants clés",
    "such as": "tels que",
    "are extracted and assigned to variables": "sont extraits et assignés à des variables",
    "These variables are structured": "Ces variables sont structurées",
    "for subsequent analysis and processing": "pour l'analyse et le traitement ultérieurs",
    "in later steps": "dans les étapes suivantes",
    
    # Phrases pour Outlook
    "This section connects to a Microsoft Outlook account": "Cette section se connecte à un compte Microsoft Outlook",
    "to monitor incoming emails": "pour surveiller les emails entrants",
    "which checks for new messages every minute": "qui vérifie les nouveaux messages toutes les minutes",
    "Emails are then processed": "Les emails sont ensuite traités",
    "to retrieve detailed headers and body content": "pour récupérer les en-têtes détaillés et le contenu du corps",
    "The headers are structured into a user-friendly format": "Les en-têtes sont structurés dans un format convivial",
    "ensuring clarity for further analysis": "assurant la clarté pour une analyse ultérieure",
    "Key details": "Détails clés",
    "including the email's": "incluant l'email",
    "are assigned to variables": "sont assignés à des variables",
    "for streamlined integration": "pour une intégration simplifiée",
    "into subsequent workflow steps": "dans les étapes suivantes du workflow",
    
    # Phrases pour Screenshot
    "processes an email's HTML content": "traite le contenu HTML d'un email",
    "to create a visual representation": "pour créer une représentation visuelle",
    "useful for documentation or phishing detection workflows": "utile pour la documentation ou les workflows de détection de phishing",
    "organizes the email's HTML body": "organise le corps HTML de l'email",
    "into a format ready for processing": "dans un format prêt pour le traitement",
    "sends this HTML content": "envoie ce contenu HTML",
    "which generates a screenshot": "qui génère une capture d'écran",
    "of the email's layout": "de la mise en page de l'email",
    "then fetches the image URL": "récupère ensuite l'URL de l'image",
    "for further use in the workflow": "pour une utilisation ultérieure dans le workflow",
    "This setup ensures": "Cette configuration assure",
    "that the email's appearance is preserved": "que l'apparence de l'email est préservée",
    "in a visually accessible format": "dans un format visuellement accessible",
    "simplifying review and reporting": "simplifiant l'examen et le rapport",
    "Keep in mind however": "Gardez à l'esprit cependant",
    "that this exposes the email content to a third party": "que cela expose le contenu de l'email à un tiers",
    "If you self host n8n": "Si vous hébergez n8n vous-même",
    "you can deploy a cli tool": "vous pouvez déployer un outil cli",
    "to rasterize locally instead": "pour pixelliser localement à la place",
    
    # Phrases pour AI Analysis
    "leverages AI to analyze email content": "exploite l'IA pour analyser le contenu des emails",
    "and headers for phishing indicators": "et les en-têtes pour les indicateurs de phishing",
    "utilizes the ChatGPT-4 model": "utilise le modèle ChatGPT-4",
    "to review the email screenshot": "pour examiner la capture d'écran de l'email",
    "and associated metadata": "et les métadonnées associées",
    "including message headers": "incluant les en-têtes de message",
    "It generates a detailed report": "Il génère un rapport détaillé",
    "indicating whether the email might be": "indiquant si l'email pourrait être",
    "a phishing attempt": "une tentative de phishing",
    "The output is formatted specifically": "La sortie est formatée spécifiquement",
    "for Jira's wiki-style renderer": "pour le rendu wiki de Jira",
    "making it ready for seamless integration": "la rendant prête pour une intégration transparente",
    "into ticketing workflows": "dans les workflows de ticketing",
    "This ensures thorough": "Cela assure une complète",
    "and automated email threat assessments": "et automatisée évaluation des menaces par email",
    
    # Phrases pour Jira
    "streamlines the process": "rationalise le processus",
    "of reporting phishing emails": "de signalement des emails de phishing",
    "by automatically creating detailed Jira tickets": "en créant automatiquement des tickets Jira détaillés",
    "compiles email information": "compile les informations de l'email",
    "including": "incluant",
    "and ChatGPT's phishing analysis": "et l'analyse de phishing de ChatGPT",
    "into a structured ticket": "dans un ticket structuré",
    "ensures that the email screenshot file": "assure que le fichier de capture d'écran de l'email",
    "is appropriately labeled for attachment": "est correctement étiqueté pour l'attachement",
    "Finally": "Finalement",
    "attaches the email's visual representation": "attache la représentation visuelle de l'email",
    "to the ticket": "au ticket",
    "providing additional context": "fournissant un contexte supplémentaire",
    "for the security team": "pour l'équipe de sécurité",
    "This integration ensures": "Cette intégration assure",
    "that phishing reports are logged": "que les rapports de phishing sont enregistrés",
    "with all necessary details": "avec tous les détails nécessaires",
    "enabling efficient tracking and resolution": "permettant un suivi et une résolution efficaces",
    
    # Mots et expressions individuels
    "Describe this image": "Décris cette image",
    "generate an image": "générer une image",
    "with a man": "avec un homme",
    "in a red suite": "dans un costume rouge",
    "holding a bottle of vine": "tenant une bouteille de vin",
    "generate": "générer",
    "create": "créer",
    "image": "image",
    "photo": "photo",
    "picture": "image",
    "man": "homme",
    "woman": "femme",
    "person": "personne",
    "holding": "tenant",
    "wearing": "portant",
    "with": "avec",
    "red": "rouge",
    "blue": "bleu",
    "green": "vert",
    "yellow": "jaune",
    "black": "noir",
    "white": "blanc",
    "suit": "costume",
    "suite": "costume",  # Correction de la faute de frappe
    "dress": "robe",
    "shirt": "chemise",
    "bottle": "bouteille",
    "wine": "vin",
    "vine": "vin",  # Correction de la faute de frappe
    "Determine if the email could be a phishing email": "Détermine si l'email pourrait être un email de phishing",
    "The message headers are as follows": "Les en-têtes du message sont les suivants",
    "Format the response for Jira": "Formate la réponse pour Jira",
    "who uses a wiki-style renderer": "qui utilise un rendu de style wiki",
    "Do not include": "N'inclus pas",
    "around your response": "autour de ta réponse",
    "Phishing Email Reported": "Email de phishing signalé",
    "Here is ChatGPT's analysis of the email": "Voici l'analyse de l'email par ChatGPT",
    "A phishing email was reported by": "Un email de phishing a été signalé par",
    "with the subject line": "avec la ligne d'objet",
    "and body": "et le corps",
    "emailScreenshot.png": "captureEmail.png"
}

def translate_text_with_dict(text, preserve_technical=True):
    """Traduire un texte en utilisant le dictionnaire de traduction"""
    if not text or not isinstance(text, str):
        return text
    
    result = text
    
    # Préserver les éléments techniques
    preserved = []
    if preserve_technical:
        patterns = [
            (r'\{\{.*?\}\}', 'EXPR'),      # Expressions n8n
            (r'\$\(.*?\)', 'REF'),          # Références de nodes  
            (r'https?://[^\s]+', 'URL'),    # URLs
            (r'!\[.*?\]\(.*?\)', 'IMG'),    # Images markdown
            (r'```[^`]*```', 'CODE'),       # Blocs de code
            (r'`[^`]+`', 'INLINE')          # Code inline
        ]
        
        for pattern, prefix in patterns:
            matches = list(re.finditer(pattern, result, re.DOTALL))
            for i, match in enumerate(reversed(matches)):
                placeholder = f"__{prefix}_{len(preserved)}__"
                preserved.insert(0, match.group())
                result = result[:match.start()] + placeholder + result[match.end():]
    
    # Appliquer les traductions (du plus long au plus court pour éviter les conflits)
    for eng, fr in sorted(TRANSLATIONS.items(), key=lambda x: len(x[0]), reverse=True):
        # Utiliser des limites de mots pour éviter les traductions partielles
        # Mais permettre la traduction dans les phrases
        result = result.replace(eng, fr)
    
    # Traductions spécifiques de mots isolés (désactivées pour éviter les bugs)
    # NOTE: Ces traductions causent des problèmes comme "Photo" -> "Phovers"
    # Elles sont désactivées pour l'instant
    # word_translations = {
    #     " the ": " le ",
    #     " The ": " Le ",
    #     " and ": " et ",
    #     " And ": " Et ",
    #     " or ": " ou ",
    #     " Or ": " Ou ",
    #     " for ": " pour ",
    #     " For ": " Pour ",
    #     " with ": " avec ",
    #     " With ": " Avec ",
    #     " in ": " dans ",
    #     " In ": " Dans ",
    #     " to ": " à ",
    #     " To ": " À ",
    #     " of ": " de ",
    #     " Of ": " De ",
    # }
    
    # for eng, fr in word_translations.items():
    #     result = result.replace(eng, fr)
    
    # Restaurer les éléments préservés
    for i, preserved_text in enumerate(preserved):
        for prefix in ['EXPR', 'REF', 'URL', 'IMG', 'CODE', 'INLINE']:
            placeholder = f"__{prefix}_{i}__"
            if placeholder in result:
                result = result.replace(placeholder, preserved_text)
                break
    
    return result

def translate_long_prompt(text):
    """Traduire les prompts longs et complexes"""
    if not text or not isinstance(text, str):
        return text
    
    # Dictionnaire spécifique pour les prompts longs
    long_prompt_translations = {
        # Phrases complètes courantes dans les prompts
        "Generate tailored social media posts": "Générer des publications personnalisées pour les réseaux sociaux",
        "based on the given content": "basé sur le contenu fourni",
        "which could be either": "qui pourrait être soit",
        "an article summary": "un résumé d'article",
        "YouTube transcription": "transcription YouTube",
        "Write a professional": "Écrire une publication professionnelle",
        "insightful post": "publication perspicace",
        "summarizing the key takeaways": "résumant les points clés",
        "that summarizes the key takeaways": "qui résume les points clés",
        "with a formal tone": "avec un ton formel",
        "in a formal tone": "avec un ton formel",
        "positioning the content": "positionnant le contenu",
        "positioning the article": "positionnant l'article",
        "positioning it": "le positionnant",
        "as a valuable resource": "comme une ressource précieuse",
        "Craft a short": "Créer une courte",
        "engaging caption": "légende engageante",
        "compelling call to action": "appel à l'action convaincant",
        "relevant hashtags": "hashtags pertinents",
        "to drive interaction": "pour stimuler l'interaction",
        "Create a concise post": "Créer une publication concise",
        "under 280 characters": "moins de 280 caractères",
        "that highlights key points": "qui met en évidence les points clés",
        "includes a few impactful hashtags": "inclut quelques hashtags percutants",
        "and includes a few impactful hashtags": "et inclut quelques hashtags percutants",
        "Develop a conversational post": "Développer une publication conversationnelle",
        "that provides additional context": "qui fournit un contexte supplémentaire",
        "includes a link": "inclut un lien",
        "includes a link to": "inclut un lien vers",
        "to encourage engagement": "pour encourager l'engagement",
        "This should be written": "Ceci devrait être écrit",
        "from a third-person perspective": "d'un point de vue à la troisième personne",
        "as an external source of information": "comme une source externe d'information",
        "The response must be structured": "La réponse doit être structurée",
        "The response should be formatted": "La réponse doit être formatée",
        "in valid JSON format": "en format JSON valide",
        "as valid JSON": "en JSON valide",
        "as follows": "comme suit",
        "with the following structure": "avec la structure suivante",
        "Make sure the output": "Assurez-vous que la sortie",
        "is always a properly formatted JSON object": "est toujours un objet JSON correctement formaté",
        "This article explores": "Cet article explore",
        "This content explores": "Ce contenu explore",
        "offering insights into": "offrant des perspectives sur",
        "providing insights into": "fournissant des perspectives sur",
        "Professionals looking to": "Les professionnels cherchant à",
        "will find valuable strategies": "trouveront des stratégies précieuses",
        "will find valuable strategies here": "trouveront des stratégies précieuses ici",
        "Read more": "Lire plus",
        "Read the latest insights": "Lire les dernières perspectives",
        "Watch/read the latest insights": "Voir/lire les dernières perspectives",
        "Read the latest insights now": "Lire les dernières perspectives maintenant",
        "Discover how": "Découvrez comment",
        "Discover the latest insights": "Découvrir les dernières perspectives",
        "Discover the latest insights on": "Découvrir les dernières perspectives sur",
        "can transform your": "peut transformer votre",
        "latest insights": "dernières perspectives",
        "is changing the game": "change la donne",
        "A must-read": "À lire absolument",
        "A must-read/watch": "À lire/regarder absolument",
        "A must-read/watch on": "À lire/regarder absolument sur",
        "A must-read article": "Un article à lire absolument",
        "A must-read article on": "Un article à lire absolument sur",
        "It breaks down": "Il décompose",
        "offers valuable insights": "offre des perspectives précieuses",
        "for those in": "pour ceux dans",
        "Dive in": "Plongez-y",
        "key topic": "sujet clé",
        "main takeaways": "points principaux", 
        "main takeaway": "point principal",
        "industry or field": "industrie ou domaine",
        "goal or impact": "objectif ou impact",
        "as if referring to": "comme en se référant à",
        "as if referring to the article": "comme en se référant à l'article",
        "from a third-person perspective": "d'un point de vue à la troisième personne",
        "to the article": "à l'article",
        "to the article or video": "à l'article ou vidéo",
        "based on the following article summary": "basé sur le résumé d'article suivant",
        
        # Termes spécifiques aux réseaux sociaux
        "LinkedIn": "LinkedIn",
        "Instagram": "Instagram", 
        "Twitter": "Twitter",
        "Facebook": "Facebook",
        "Youtube": "YouTube",
        "Blog post": "Article de blog",
        "any other link": "tout autre lien",
        "if it is not from": "s'il ne provient pas de",
        
        # Autres termes
        "following article summary": "résumé d'article suivant",
        "Article Summary": "Résumé de l'article",
        "Or YouTube Transcription": "Ou Transcription YouTube",
        "Transcript not available": "Transcription non disponible",
        "Title not available": "Titre non disponible",
        "Description not available": "Description non disponible",
        "Content not available": "Contenu non disponible",
        "URL not available": "URL non disponible"
    }
    
    result = text
    
    # Appliquer les traductions longues d'abord
    for eng, fr in sorted(long_prompt_translations.items(), key=lambda x: len(x[0]), reverse=True):
        result = result.replace(eng, fr)
    
    # Ensuite appliquer les traductions de mots simples
    simple_words = {
        " for ": " pour ",
        " and ": " et ",
        " or ": " ou ",
        " the ": " le ",
        " a ": " un ",
        " an ": " un ",
        " to ": " pour ",
        " in ": " dans ",
        " on ": " sur ",
        " with ": " avec ",
        " from ": " de ",
        " based ": " basé ",
        " here ": " ici ",
        " that ": " qui ",
        " this ": " ce ",
        " these ": " ces ",
        " now": " maintenant",
        " here.": " ici.",
        "[Key topic]": "[Sujet clé]",
        "[key topic]": "[sujet clé]",
        "[main takeaway]": "[point principal]",
        "[main takeaways]": "[points principaux]",
        "[industry]": "[industrie]",
        "[URL]": "[URL]",
        "[industry or field]": "[industrie ou domaine]",
        "[goal or impact]": "[objectif ou impact]"
    }
    
    for eng, fr in simple_words.items():
        result = result.replace(eng, fr)
    
    return result

def translate_prompt_simple(text):
    """Traduction simple mais efficace des prompts"""
    if not text or not isinstance(text, str):
        return text
    
    # Dictionnaire de traduction simple pour les prompts
    prompt_words = {
        # Articles et déterminants
        " a ": " un ",
        " an ": " un ",
        " the ": " le ",
        # Prépositions courantes
        " with ": " avec ",
        " in ": " dans ",
        " on ": " sur ",
        " for ": " pour ",
        " of ": " de ",
        " at ": " à ",
        # Verbes courants
        "generate": "générer",
        "create": "créer",
        "make": "faire",
        "design": "concevoir",
        "build": "construire",
        "generate": "générer",
        "Generate": "Générer",
        # Noms courants
        "image": "image",
        "photo": "photo",
        "picture": "image",
        "man": "homme",
        "woman": "femme",
        "person": "personne",
        # Adjectifs
        "red": "rouge",
        "blue": "bleu",
        "green": "vert",
        "black": "noir",
        "white": "blanc",
        "professional": "professionnel",
        "modern": "moderne",
        # Actions
        "holding": "tenant",
        "wearing": "portant",
        "showing": "montrant",
        # Objets
        "bottle": "bouteille",
        "dress": "robe",
        "suit ": "costume ",
        "suite ": "costume ",
        "wine": "vin",
        "vine": "vin",
        # Social media
        "social media": "médias sociaux",
        "post": "publication",
        "posts": "publications",
        "ideas": "idées",
        "summaries": "résumés",
        "tailored": "personnalisé",
        "content": "contenu",
        "article": "article",
        "summary": "résumé",
        "transcription": "transcription",
        "Youtube link": "Lien Youtube",
        "Blog post": "Article de blog"
    }
    
    result = text
    # Appliquer les traductions
    for eng, fr in prompt_words.items():
        result = result.replace(eng, fr)
    
    return result

def translate_workflow(workflow_data):
    """Traduire tous les éléments textuels d'un workflow n8n"""
    translated = json.loads(json.dumps(workflow_data))  # Deep copy
    
    # Traduire le nom du workflow si présent
    if 'name' in translated:
        translated['name'] = translate_text_with_dict(translated['name'])
    
    # Traduire chaque node
    if 'nodes' in translated:
        for node in translated['nodes']:
            # Traduire le nom du node
            if 'name' in node:
                node['name'] = translate_text_with_dict(node['name'])
            
            # Traduire les notes
            if 'notes' in node:
                node['notes'] = translate_text_with_dict(node['notes'])
            
            # Traduire les sticky notes
            if node.get('type') == 'n8n-nodes-base.stickyNote' and 'parameters' in node:
                if 'content' in node['parameters']:
                    node['parameters']['content'] = translate_text_with_dict(node['parameters']['content'])
            
            # Traduire les paramètres spécifiques
            if 'parameters' in node:
                # Pour les nodes OpenAI/ChatGPT
                if node.get('type') in ['@n8n/n8n-nodes-langchain.openAi', 'n8n-nodes-base.openAi']:
                    if 'text' in node['parameters']:
                        node['parameters']['text'] = translate_text_with_dict(node['parameters']['text'])
                    
                    # Pour les messages dans les nouveaux nodes OpenAI
                    if 'messages' in node['parameters']:
                        if 'values' in node['parameters']['messages']:
                            for message in node['parameters']['messages']['values']:
                                if 'content' in message and isinstance(message['content'], str):
                                    # Vérifier si c'est un prompt qui commence par =
                                    if message['content'].startswith('='):
                                        # Retirer le = au début, traduire, puis remettre
                                        content_without_equal = message['content'][1:]
                                        translated_content = translate_long_prompt(content_without_equal)
                                        message['content'] = '=' + translated_content
                                    else:
                                        # Traduire normalement
                                        message['content'] = translate_long_prompt(message['content'])
                
                # Pour les nodes Set
                if node.get('type') == 'n8n-nodes-base.set' and 'assignments' in node['parameters']:
                    if 'assignments' in node['parameters'].get('assignments', {}):
                        for assignment in node['parameters']['assignments']['assignments']:
                            if 'name' in assignment:
                                # Traduire seulement si c'est dans notre dictionnaire
                                translated_name = translate_text_with_dict(assignment['name'], preserve_technical=False)
                                # Ne changer que si une traduction existe
                                if translated_name != assignment['name']:
                                    assignment['name'] = translated_name
                
                # Pour les autres champs texte
                text_fields = ['summary', 'description', 'fileName', 'prompt', 'value']
                for field in text_fields:
                    if field in node['parameters']:
                        node['parameters'][field] = translate_text_with_dict(node['parameters'][field])
                    
                    # Champs imbriqués
                    if 'additionalFields' in node['parameters']:
                        if field in node['parameters']['additionalFields']:
                            node['parameters']['additionalFields'][field] = translate_text_with_dict(
                                node['parameters']['additionalFields'][field]
                            )
                    
                    # Pour les bodyParameters et autres paramètres imbriqués
                    if 'bodyParameters' in node['parameters']:
                        if 'parameters' in node['parameters']['bodyParameters']:
                            for param in node['parameters']['bodyParameters']['parameters']:
                                if isinstance(param, dict) and field in param:
                                    # Traduire seulement si c'est un texte (pas une URL ou un nombre)
                                    if field == 'value' and isinstance(param['value'], str):
                                        # Ne pas traduire les URLs, nombres, ou valeurs techniques
                                        if not param['value'].startswith('http') and not param['value'].replace('.', '').isdigit():
                                            # Pour les prompts, utiliser la traduction spécialisée
                                            if param.get('name') == 'prompt':
                                                param['value'] = translate_prompt_simple(param['value'])
                                            else:
                                                param['value'] = translate_text_with_dict(param['value'])
                
                # Pour les nodes Code (traduire les commentaires mais pas le code)
                if node.get('type') == 'n8n-nodes-base.code' and 'jsCode' in node['parameters']:
                    code = node['parameters']['jsCode']
                    # Traduire uniquement les commentaires
                    code = re.sub(
                        r'//\s*(.+)$', 
                        lambda m: '// ' + translate_text_with_dict(m.group(1)), 
                        code, 
                        flags=re.MULTILINE
                    )
                    node['parameters']['jsCode'] = code
    
    return translated

def main():
    # Utiliser le fichier spécifié ou celui par défaut
    if len(sys.argv) > 1:
        input_file = sys.argv[1]
    else:
        input_file = "/var/www/automatehub/workflows/workflow_to_translate.json"
    
    if not os.path.exists(input_file):
        print(f"Erreur: Le fichier {input_file} n'existe pas")
        sys.exit(1)
    
    # Lire le workflow
    print(f"📖 Lecture du workflow: {input_file}")
    with open(input_file, 'r', encoding='utf-8') as f:
        workflow_data = json.load(f)
    
    # Traduire
    print("\n🔄 Début de la traduction...")
    translated_workflow = translate_workflow(workflow_data)
    
    # Sauvegarder
    output_file = input_file.replace('.json', '_FR.json')
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(translated_workflow, f, ensure_ascii=False, indent=2)
    
    print(f"\n✅ Traduction terminée! Fichier sauvegardé: {output_file}")
    
    # Statistiques
    print("\n📊 Statistiques de traduction:")
    print(f"- Nodes traduits: {len(translated_workflow.get('nodes', []))}")
    sticky_count = sum(1 for n in translated_workflow.get('nodes', []) if n.get('type') == 'n8n-nodes-base.stickyNote')
    print(f"- Sticky notes traduites: {sticky_count}")
    
    # Aperçu
    print("\n📋 Aperçu des traductions:")
    for i, node in enumerate(translated_workflow.get('nodes', [])[:5]):
        print(f"{i+1}. {node.get('name', 'Sans nom')}")
        if node.get('notes'):
            print(f"   Notes: {node['notes'][:50]}...")

if __name__ == "__main__":
    main()