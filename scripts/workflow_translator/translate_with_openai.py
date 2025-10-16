#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Traduction des textes extraits via OpenAI API avec placeholders
"""
import json
import sys
import os
import requests
from time import sleep

def get_openai_key():
    """Récupérer la clé OpenAI depuis les variables d'environnement"""
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
    
    if not key:
        print("⚠️  Clé OpenAI non trouvée.")
        print("Veuillez entrer votre clé OpenAI:")
        key = input().strip()
    
    return key

def split_large_text(text, max_chars=500):
    """Découper un texte long en chunks intelligents"""
    if len(text) <= max_chars:
        return [text]

    chunks = []
    # Essayer de découper par paragraphes d'abord
    paragraphs = text.split('\n\n')
    current_chunk = ""

    for para in paragraphs:
        if len(current_chunk + para) <= max_chars:
            current_chunk += para + '\n\n'
        else:
            if current_chunk:
                chunks.append(current_chunk.strip())
                current_chunk = para + '\n\n'
            else:
                # Si même un paragraphe est trop long, découper par phrases
                sentences = para.split('. ')
                for sentence in sentences:
                    if len(current_chunk + sentence) <= max_chars:
                        current_chunk += sentence + '. '
                    else:
                        if current_chunk:
                            chunks.append(current_chunk.strip())
                        current_chunk = sentence + '. '

    if current_chunk:
        chunks.append(current_chunk.strip())

    return chunks

def translate_large_text(text_id, text_info, api_key):
    """Traduire un texte très long en le découpant"""
    original_text = text_info['original']

    # Si le texte fait moins de 2000 caractères, traduction directe (plus efficace)
    if len(original_text) <= 2000:
        single_batch = {text_id: text_info}
        return translate_batch_with_openai(single_batch, api_key)

    print(f"    🔪 Découpage du texte en chunks plus petits...")
    chunks = split_large_text(original_text, max_chars=500)  # Chunks plus petits
    print(f"    📦 {len(chunks)} chunks créés")

    translated_chunks = []

    for i, chunk in enumerate(chunks):
        chunk_text_info = {
            'original': chunk,
            'type': text_info['type']
        }

        chunk_id = f"{text_id}_chunk_{i}"
        chunk_batch = {chunk_id: chunk_text_info}

        print(f"    📝 Traduction chunk {i+1}/{len(chunks)} ({len(chunk)} chars)...")
        chunk_translations = translate_batch_with_openai(chunk_batch, api_key)

        if chunk_id in chunk_translations:
            translated_chunks.append(chunk_translations[chunk_id])
        else:
            print(f"    ⚠️  Échec chunk {i+1}, utilisation de l'original")
            translated_chunks.append(chunk)

        sleep(0.5)  # Pause entre chunks

    # Reconstituer le texte traduit
    final_translation = '\n\n'.join(translated_chunks)
    return {text_id: final_translation}

def translate_batch_with_openai(texts_batch, api_key):
    """Traduire un batch de textes avec OpenAI en utilisant des placeholders"""
    
    # Créer le contenu avec des placeholders
    content_lines = []
    for text_id, text_info in texts_batch.items():
        original = text_info['original']
        # Échapper les quotes dans le texte
        original_escaped = original.replace('"', '\\"')
        content_lines.append(f'${text_id} = "{original_escaped}"')
    
    content_to_translate = '\n'.join(content_lines)
    
    prompt = f"""Tu es un expert en traduction de workflows n8n de l'anglais vers le français.

Je vais te donner des textes à traduire sous forme de variables. Tu dois me retourner EXACTEMENT le même format avec les textes traduits COMPLETS.

Règles importantes:
1. CONSERVER EXACTEMENT le format $variable = "texte"
2. Traduire COMPLÈTEMENT et intégralement chaque texte
3. PRÉSERVER EXACTEMENT (ne pas traduire):
   - Variables entre {{{{ }}}} et $()
   - URLs complètes
   - Noms de champs JSON entre guillemets (ex: "linkedin", "instagram", "twitter", "facebook")
4. Traduire les placeholders: [key topic] → [sujet clé], [main takeaway] → [point principal], etc.
5. Garder les noms de réseaux sociaux: LinkedIn, Instagram, Twitter, Facebook, YouTube
6. IMPORTANT: Traduire TOUT le contenu, ne pas raccourcir ni résumer

Voici les textes à traduire:

{content_to_translate}

Réponds UNIQUEMENT avec les mêmes lignes mais traduites COMPLÈTEMENT en français. Ne rajoute AUCUNE explication."""

    headers = {
        'Authorization': f'Bearer {api_key}',
        'Content-Type': 'application/json'
    }
    
    # Déterminer le modèle et tokens basé sur la taille du contenu
    estimated_input_tokens = len(content_to_translate) // 4  # Approximation

    # Utiliser GPT-4.1-mini pour tous les textes (plus performant)
    model = 'gpt-4.1-mini'

    # Calculer les tokens nécessaires - Plus généreux pour assurer traduction complète
    max_tokens = min(8000, max(2000, estimated_input_tokens * 4))  # Au moins 2000 tokens, jusqu'à 4x l'input

    data = {
        'model': model,
        'messages': [
            {
                'role': 'system',
                'content': 'Tu es un traducteur expert. Tu réponds UNIQUEMENT avec les traductions demandées, sans aucune explication.'
            },
            {
                'role': 'user',
                'content': prompt
            }
        ],
        'temperature': 0.3,
        'max_tokens': max_tokens
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
            
            # Parser la réponse - Amélioration pour gérer les textes multilignes
            translations = {}

            # Pour gérer les textes multilignes, chercher la pattern complète
            import re

            # Pattern pour matcher $variable = "contenu multiline"
            pattern = r'\$(\w+)\s*=\s*"(.*?)"(?=\s*$|\s*\$\w+\s*=)'

            # Rechercher avec DOTALL pour inclure les nouvelles lignes
            matches = re.findall(pattern, content, re.DOTALL)

            for text_id, translated_text in matches:
                # Remplacer les échappements
                translated_text = translated_text.replace('\\"', '"')
                translations[text_id] = translated_text

            # Fallback vers l'ancienne méthode si rien trouvé
            if not translations:
                for line in content.split('\n'):
                    line = line.strip()
                    if line.startswith('$') and ' = ' in line:
                        # Extraire l'ID et la traduction
                        parts = line.split(' = ', 1)
                        text_id = parts[0].strip('$')

                        # Extraire le texte entre guillemets
                        if len(parts) > 1:
                            translated_text = parts[1].strip()
                            # Retirer les guillemets au début et à la fin seulement s'ils sont en paire
                            if translated_text.startswith('"') and translated_text.endswith('"'):
                                translated_text = translated_text[1:-1]
                            # Si on a juste un guillemet au début, le retirer aussi
                            elif translated_text.startswith('"'):
                                translated_text = translated_text[1:]
                            # Remplacer les échappements
                            translated_text = translated_text.replace('\\"', '"')

                            translations[text_id] = translated_text
            
            return translations
            
        else:
            print(f"❌ Erreur API OpenAI: {response.status_code}")
            print(response.text)
            return {}
            
    except Exception as e:
        print(f"❌ Erreur lors de la traduction: {str(e)}")
        return {}

def translate_texts_file(input_file, api_key):
    """Traduire tous les textes d'un fichier d'extraction"""
    # Charger les textes extraits
    with open(input_file, 'r', encoding='utf-8') as f:
        extraction_data = json.load(f)

    texts = extraction_data['texts']
    total_texts = len(texts)

    print(f"🔄 Traduction de {total_texts} textes...")

    # Séparer les textes longs des textes courts
    long_texts = {}
    short_texts = {}

    for text_id, text_info in texts.items():
        text_length = len(text_info['original'])
        if text_length > 1000:  # Seuil pour les prompts longs
            long_texts[text_id] = text_info
        else:
            short_texts[text_id] = text_info

    all_translations = {}

    # Traiter d'abord les textes longs individuellement
    if long_texts:
        print(f"  🔥 Traitement de {len(long_texts)} textes longs individuellement...")
        for text_id, text_info in long_texts.items():
            print(f"    📝 Traduction de {text_id} ({len(text_info['original'])} caractères)...")

            # Utiliser la nouvelle fonction pour les très gros textes
            translations = translate_large_text(text_id, text_info, api_key)

            if translations:
                all_translations.update(translations)
                print(f"    ✅ Texte long traduit")
            else:
                print(f"    ⚠️  Échec de la traduction du texte long")

            sleep(1)  # Pause plus longue pour les textes longs

    # Ensuite traiter les textes courts par batch (max 15 textes par requête)
    if short_texts:
        batch_size = 15
        text_items = list(short_texts.items())

        for i in range(0, len(text_items), batch_size):
            batch = dict(text_items[i:i+batch_size])
            batch_num = i//batch_size + 1
            total_batches = (len(text_items)-1)//batch_size + 1

            print(f"  📦 Traduction batch {batch_num}/{total_batches} (textes courts)...")

            translations = translate_batch_with_openai(batch, api_key)

            if translations:
                all_translations.update(translations)
                print(f"    ✅ {len(translations)} textes traduits")
            else:
                print(f"    ⚠️  Échec de la traduction du batch")

            # Pause pour éviter de surcharger l'API
            if i + batch_size < len(text_items):
                sleep(0.5)
    
    # Mettre à jour les textes avec les traductions
    for text_id, translation in all_translations.items():
        if text_id in texts:
            texts[text_id]['translated'] = translation
    
    # Sauvegarder le résultat dans le même répertoire que le fichier source
    source_dir = os.path.dirname(os.path.abspath(input_file))
    basename = os.path.basename(input_file)
    output_file = os.path.join(source_dir, basename.replace('_texts_to_translate.json', '_texts_translated.json'))
    
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(extraction_data, f, ensure_ascii=False, indent=2)
    
    print(f"\n✅ Traduction terminée!")
    print(f"📊 {len(all_translations)}/{total_texts} textes traduits")
    print(f"💾 Sauvegardé dans: {output_file}")
    
    return output_file

def main():
    if len(sys.argv) < 2:
        print("Usage: python translate_with_openai.py <texts_to_translate.json>")
        sys.exit(1)
    
    input_file = sys.argv[1]
    
    # Obtenir la clé API
    api_key = get_openai_key()
    
    if not api_key:
        print("❌ Impossible de continuer sans clé API")
        sys.exit(1)
    
    # Traduire
    translate_texts_file(input_file, api_key)

if __name__ == "__main__":
    main()