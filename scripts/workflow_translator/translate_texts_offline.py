#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Traduction offline des textes extraits (sans API)
"""
import json
import sys
import os

def get_translation_dict():
    """Dictionnaire complet de traduction"""
    return {
        # Noms de workflows et nodes
        "Generate social post ideas or summaries": "Générer des idées de publications sociales ou des résumés",
        "When clicking 'Test workflow'": "Lors du clic sur 'Tester le workflow'",
        "Text Classifier": "Classificateur de texte",
        "OpenAI Chat Model": "Modèle de Chat OpenAI",
        "Google Docs": "Google Docs",
        "Airtable": "Airtable",
        "OpenAI": "OpenAI",
        "OpenAI1": "OpenAI1",
        "Airtable1": "Airtable1",
        "Airtable2": "Airtable2",
        "Dumpling Youtube": "Dumpling Youtube",
        "Dumpling Blog": "Dumpling Blog",
        
        # Prompts longs OpenAI - Premier prompt
        """Generate tailored social media posts for LinkedIn, Instagram, Twitter (X), and Facebook based on the given content, which could be either an article summary or a YouTube transcription.
	•	LinkedIn: Write a professional, insightful post summarizing the key takeaways with a formal tone, positioning the content as a valuable resource.
	•	Instagram: Craft a short, engaging caption with a compelling call to action and relevant hashtags to drive interaction.
	•	Twitter (X): Create a concise post under 280 characters that highlights key points and includes a few impactful hashtags.
	•	Facebook: Develop a conversational post that provides additional context and includes a link to the article or video to encourage engagement.

This should be written from a third-person perspective, positioning the content as an external source of information.

**Or YouTube Transcription:**
{{ $json?.transcript ?? 'Transcript not available' }}

The response must be structured in valid JSON format as follows:

{
  "linkedin": "This content explores [key topic], offering insights into [main takeaways]. Professionals looking to [goal or impact] will find valuable strategies here. Read more: [URL]",
  "instagram": "Discover how [key topic] can transform your [industry or field]! 🚀 Watch/read the latest insights now. #Innovation #Success #YourHashtag [URL]",
  "twitter": "[Key topic] is changing the game! Discover the latest insights on [main takeaway]. #Trending #YourHashtag [URL]",
  "facebook": "A must-read/watch on [key topic]! It breaks down [main takeaway] and offers valuable insights for those in [industry]. Dive in: [URL]"
}

Make sure the output is always a properly formatted JSON object.""": 
        """Générer des publications personnalisées pour les réseaux sociaux pour LinkedIn, Instagram, Twitter (X) et Facebook basées sur le contenu fourni, qui pourrait être soit un résumé d'article ou une transcription YouTube.
	•	LinkedIn : Écrire une publication professionnelle et perspicace résumant les points clés avec un ton formel, positionnant le contenu comme une ressource précieuse.
	•	Instagram : Créer une légende courte et engageante avec un appel à l'action convaincant et des hashtags pertinents pour stimuler l'interaction.
	•	Twitter (X) : Créer une publication concise de moins de 280 caractères qui met en évidence les points clés et inclut quelques hashtags percutants.
	•	Facebook : Développer une publication conversationnelle qui fournit un contexte supplémentaire et inclut un lien vers l'article ou la vidéo pour encourager l'engagement.

Ceci devrait être écrit d'un point de vue à la troisième personne, positionnant le contenu comme une source externe d'information.

**Ou Transcription YouTube :**
{{ $json?.transcript ?? 'Transcription non disponible' }}

La réponse doit être structurée en format JSON valide comme suit :

{
  "linkedin": "Ce contenu explore [sujet clé], offrant des perspectives sur [points principaux]. Les professionnels cherchant à [objectif ou impact] trouveront des stratégies précieuses ici. Lire plus : [URL]",
  "instagram": "Découvrez comment [sujet clé] peut transformer votre [industrie ou domaine] ! 🚀 Voir/lire les dernières perspectives maintenant. #Innovation #Succès #VotreHashtag [URL]",
  "twitter": "[Sujet clé] change la donne ! Découvrir les dernières perspectives sur [point principal]. #Tendance #VotreHashtag [URL]",
  "facebook": "À lire/regarder absolument sur [sujet clé] ! Il décompose [point principal] et offre des perspectives précieuses pour ceux dans [industrie]. Plongez-y : [URL]"
}

Assurez-vous que la sortie est toujours un objet JSON correctement formaté.""",

        # Deuxième prompt OpenAI
        """Generate tailored social media posts for LinkedIn, Instagram, Twitter (X), and Facebook based on the following article summary:
	•	LinkedIn: Write a professional, insightful post that summarizes the key takeaways in a formal tone, positioning the article as a valuable resource.
	•	Instagram: Craft a short, engaging caption with a compelling call to action and relevant hashtags to drive interaction.
	•	Twitter (X): Create a concise post under 280 characters that highlights key points and includes a few impactful hashtags.
	•	Facebook: Develop a conversational post that provides additional context and includes a link to the article to encourage engagement.

This should be written as if referring to the article from a third-person perspective, positioning it as an external source of information.

**Article Summary:**
{{ $('Dumpling Blog')?.item?.json?.title ?? 'Title not available' }}
{{ $('Dumpling Blog')?.item?.json?.metadata?.description ?? 'Description not available' }}
{{ $('Dumpling Blog')?.item?.json?.content ?? 'Content not available' }}
{{ $('Text Classifier')?.item?.json?.URL ?? 'URL not available' }}

The response should be formatted as valid JSON with the following structure:

{
  "linkedin": "This article explores [key topic], providing insights into [main takeaways]. Professionals looking to [goal or impact] will find valuable strategies here. Read more: [URL]",
  "instagram": "Discover how [key topic] can transform your [industry or field]! 🚀 Read the latest insights now. #Innovation #Success #YourHashtag [URL]",
  "twitter": "[Key topic] is changing the game! Discover the latest insights on [main takeaway]. #Trending #YourHashtag [URL]",
  "facebook": "A must-read article on [key topic]! It breaks down [main takeaway] and offers valuable insights for those in [industry]. Dive in: [URL]"
}

""":
        """Générer des publications personnalisées pour les réseaux sociaux pour LinkedIn, Instagram, Twitter (X) et Facebook basées sur le résumé d'article suivant :
	•	LinkedIn : Écrire une publication professionnelle et perspicace qui résume les points clés avec un ton formel, positionnant l'article comme une ressource précieuse.
	•	Instagram : Créer une légende courte et engageante avec un appel à l'action convaincant et des hashtags pertinents pour stimuler l'interaction.
	•	Twitter (X) : Créer une publication concise de moins de 280 caractères qui met en évidence les points clés et inclut quelques hashtags percutants.
	•	Facebook : Développer une publication conversationnelle qui fournit un contexte supplémentaire et inclut un lien vers l'article pour encourager l'engagement.

Ceci devrait être écrit comme en se référant à l'article d'un point de vue à la troisième personne, le positionnant comme une source externe d'information.

**Résumé d'article :**
{{ $('Dumpling Blog')?.item?.json?.title ?? 'Titre non disponible' }}
{{ $('Dumpling Blog')?.item?.json?.metadata?.description ?? 'Description non disponible' }}
{{ $('Dumpling Blog')?.item?.json?.content ?? 'Contenu non disponible' }}
{{ $('Text Classifier')?.item?.json?.URL ?? 'URL non disponible' }}

La réponse devrait être formatée en JSON valide avec la structure suivante :

{
  "linkedin": "Cet article explore [sujet clé], fournissant des perspectives sur [points principaux]. Les professionnels cherchant à [objectif ou impact] trouveront des stratégies précieuses ici. Lire plus : [URL]",
  "instagram": "Découvrez comment [sujet clé] peut transformer votre [industrie ou domaine] ! 🚀 Lire les dernières perspectives maintenant. #Innovation #Succès #VotreHashtag [URL]",
  "twitter": "[Sujet clé] change la donne ! Découvrir les dernières perspectives sur [point principal]. #Tendance #VotreHashtag [URL]",
  "facebook": "Un article à lire absolument sur [sujet clé] ! Il décompose [point principal] et offre des perspectives précieuses pour ceux dans [industrie]. Plongez-y : [URL]"
}

"""
    }

def translate_texts_file(input_file):
    """Traduire tous les textes d'un fichier d'extraction"""
    # Charger les textes extraits
    with open(input_file, 'r', encoding='utf-8') as f:
        extraction_data = json.load(f)
    
    texts = extraction_data['texts']
    total_texts = len(texts)
    
    print(f"🔄 Traduction de {total_texts} textes...")
    
    # Obtenir le dictionnaire de traduction
    translation_dict = get_translation_dict()
    
    # Traduire chaque texte
    translated_count = 0
    for text_id, text_info in texts.items():
        original = text_info['original']
        
        # Chercher une traduction exacte d'abord
        if original in translation_dict:
            text_info['translated'] = translation_dict[original]
            translated_count += 1
        else:
            # Si pas de traduction exacte, essayer de traduire partiellement
            translated = original
            for eng, fr in translation_dict.items():
                if len(eng) > 10 and eng in translated:
                    translated = translated.replace(eng, fr)
            
            if translated != original:
                text_info['translated'] = translated
                translated_count += 1
    
    # Sauvegarder le résultat
    output_file = input_file.replace('_texts_to_translate.json', '_texts_translated.json')
    
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(extraction_data, f, ensure_ascii=False, indent=2)
    
    print(f"\n✅ Traduction terminée!")
    print(f"📊 {translated_count}/{total_texts} textes traduits")
    print(f"💾 Sauvegardé dans: {output_file}")
    
    return output_file

def main():
    if len(sys.argv) < 2:
        print("Usage: python translate_texts_offline.py <texts_to_translate.json>")
        sys.exit(1)
    
    input_file = sys.argv[1]
    translate_texts_file(input_file)

if __name__ == "__main__":
    main()