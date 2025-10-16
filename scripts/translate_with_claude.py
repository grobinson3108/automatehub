#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import json
import os
import sys
import subprocess

def translate_prompt_with_claude(text):
    """Utiliser Claude via un script pour traduire les prompts"""
    if not text or not isinstance(text, str) or len(text.strip()) < 10:
        return text
    
    # Créer un fichier temporaire avec le prompt
    temp_prompt_file = "/tmp/prompt_to_translate.txt"
    with open(temp_prompt_file, 'w', encoding='utf-8') as f:
        f.write(text)
    
    # Script pour appeler Claude
    translate_script = """
import json

prompt = open('/tmp/prompt_to_translate.txt', 'r', encoding='utf-8').read()

print(f'''Traduis ce prompt de workflow n8n de l'anglais vers le français.

Règles IMPORTANTES:
1. Traduire de manière naturelle et professionnelle
2. PRÉSERVER EXACTEMENT:
   - Les variables entre {{ }} et $()
   - Les URLs
   - Les noms de champs JSON (linkedin, instagram, twitter, facebook)
   - La structure et le formatage
3. Traduire les placeholders [key topic] → [sujet clé], etc.
4. Garder les noms de réseaux sociaux: LinkedIn, Instagram, Twitter, Facebook

Prompt à traduire:
{prompt}

Réponds UNIQUEMENT avec la traduction, sans explications.''')
"""
    
    # Sauvegarder le script
    script_file = "/tmp/translate_script.py"
    with open(script_file, 'w', encoding='utf-8') as f:
        f.write(translate_script)
    
    try:
        # Exécuter le script et capturer la sortie
        result = subprocess.run(
            ['python3', script_file],
            capture_output=True,
            text=True,
            encoding='utf-8'
        )
        
        if result.returncode == 0:
            translated = result.stdout.strip()
            # Nettoyer la sortie si nécessaire
            if translated:
                return translated
    except Exception as e:
        print(f"⚠️ Erreur lors de la traduction: {str(e)}")
    
    # Si échec, retourner l'original
    return text

# Je vais créer une version plus simple qui utilise un dictionnaire enrichi
def create_enhanced_translation_dict():
    """Créer un dictionnaire de traduction enrichi"""
    return {
        # Phrases complètes pour les prompts sociaux
        "Generate tailored social media posts for LinkedIn, Instagram, Twitter (X), and Facebook based on the given content": 
            "Générer des publications personnalisées pour les réseaux sociaux pour LinkedIn, Instagram, Twitter (X), et Facebook basées sur le contenu fourni",
        
        "Generate tailored social media posts for LinkedIn, Instagram, Twitter (X), and Facebook based on the following article summary":
            "Générer des publications personnalisées pour les réseaux sociaux pour LinkedIn, Instagram, Twitter (X), et Facebook basées sur le résumé d'article suivant",
            
        "which could be either an article summary or a YouTube transcription": 
            "qui pourrait être soit un résumé d'article ou une transcription YouTube",
            
        "Write a professional, insightful post summarizing the key takeaways with a formal tone":
            "Écrire une publication professionnelle et perspicace résumant les points clés avec un ton formel",
            
        "Write a professional, insightful post that summarizes the key takeaways in a formal tone":
            "Écrire une publication professionnelle et perspicace qui résume les points clés avec un ton formel",
            
        "positioning the content as a valuable resource":
            "positionnant le contenu comme une ressource précieuse",
            
        "positioning the article as a valuable resource":
            "positionnant l'article comme une ressource précieuse",
            
        "positioning it as an external source of information":
            "le positionnant comme une source externe d'information",
            
        "Craft a short, engaging caption with a compelling call to action and relevant hashtags to drive interaction":
            "Créer une légende courte et engageante avec un appel à l'action convaincant et des hashtags pertinents pour stimuler l'interaction",
            
        "Create a concise post under 280 characters that highlights key points and includes a few impactful hashtags":
            "Créer une publication concise de moins de 280 caractères qui met en évidence les points clés et inclut quelques hashtags percutants",
            
        "Develop a conversational post that provides additional context and includes a link to the article or video to encourage engagement":
            "Développer une publication conversationnelle qui fournit un contexte supplémentaire et inclut un lien vers l'article ou la vidéo pour encourager l'engagement",
            
        "Develop a conversational post that provides additional context and includes a link to the article to encourage engagement":
            "Développer une publication conversationnelle qui fournit un contexte supplémentaire et inclut un lien vers l'article pour encourager l'engagement",
            
        "This should be written from a third-person perspective":
            "Ceci devrait être écrit d'un point de vue à la troisième personne",
            
        "This should be written as if referring to the article from a third-person perspective":
            "Ceci devrait être écrit comme en se référant à l'article d'un point de vue à la troisième personne",
            
        "The response must be structured in valid JSON format as follows":
            "La réponse doit être structurée en format JSON valide comme suit",
            
        "The response should be formatted as valid JSON with the following structure":
            "La réponse devrait être formatée en JSON valide avec la structure suivante",
            
        "Make sure the output is always a properly formatted JSON object":
            "Assurez-vous que la sortie est toujours un objet JSON correctement formaté",
            
        # Templates de réponse
        "This content explores": "Ce contenu explore",
        "This article explores": "Cet article explore",
        "offering insights into": "offrant des perspectives sur",
        "providing insights into": "fournissant des perspectives sur",
        "Professionals looking to": "Les professionnels cherchant à",
        "will find valuable strategies here": "trouveront des stratégies précieuses ici",
        "Read more": "Lire plus",
        "Discover how": "Découvrez comment",
        "can transform your": "peut transformer votre",
        "Watch/read the latest insights now": "Voir/lire les dernières perspectives maintenant",
        "Read the latest insights now": "Lire les dernières perspectives maintenant",
        "is changing the game": "change la donne",
        "Discover the latest insights on": "Découvrir les dernières perspectives sur",
        "A must-read/watch on": "À lire/regarder absolument sur",
        "A must-read article on": "Un article à lire absolument sur",
        "It breaks down": "Il décompose",
        "offers valuable insights for those in": "offre des perspectives précieuses pour ceux dans",
        "Dive in": "Plongez-y",
        
        # Variables et placeholders
        "[key topic]": "[sujet clé]",
        "[Key topic]": "[Sujet clé]",
        "[main takeaways]": "[points principaux]",
        "[main takeaway]": "[point principal]",
        "[goal or impact]": "[objectif ou impact]",
        "[industry or field]": "[industrie ou domaine]",
        "[industry]": "[industrie]",
        
        # Autres
        "Or YouTube Transcription": "Ou Transcription YouTube",
        "Article Summary": "Résumé d'article",
        "Transcript not available": "Transcription non disponible",
        "Title not available": "Titre non disponible",
        "Description not available": "Description non disponible",  
        "Content not available": "Contenu non disponible",
        "URL not available": "URL non disponible",
    }

def translate_prompt_enhanced(text):
    """Traduction améliorée des prompts longs"""
    if not text or not isinstance(text, str):
        return text
    
    result = text
    translations = create_enhanced_translation_dict()
    
    # Appliquer les traductions du plus long au plus court
    for eng, fr in sorted(translations.items(), key=lambda x: len(x[0]), reverse=True):
        result = result.replace(eng, fr)
    
    return result

# Exporter pour utilisation dans d'autres scripts
if __name__ == "__main__":
    # Test
    test_prompt = "Generate tailored social media posts for LinkedIn, Instagram, Twitter (X), and Facebook based on the given content"
    print("Original:", test_prompt)
    print("Traduit:", translate_prompt_enhanced(test_prompt))