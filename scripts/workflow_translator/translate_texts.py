#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Étape 2: Traduire les textes extraits via OpenAI API
"""
import json
import sys
import os
import requests
from time import sleep

def get_openai_key():
    """Récupérer la clé OpenAI depuis les variables d'environnement ou demander à l'utilisateur"""
    key = os.environ.get('OPENAI_API_KEY')
    if not key:
        print("⚠️  Clé OpenAI non trouvée dans l'environnement.")
        print("Veuillez entrer votre clé OpenAI (ou définir OPENAI_API_KEY):")
        key = input().strip()
    return key

def translate_batch_with_openai(texts, api_key):
    """Traduire un batch de textes avec OpenAI"""
    # Préparer le prompt
    texts_to_translate = {}
    for text_id, text_info in texts.items():
        texts_to_translate[text_id] = text_info['original']
    
    prompt = f"""Tu es un expert en traduction de workflows n8n de l'anglais vers le français.

Traduis les textes suivants en respectant ces règles:
1. Traduction naturelle et professionnelle adaptée au contexte français
2. PRÉSERVER EXACTEMENT (ne pas traduire):
   - Variables entre {{{{ }}}} et $()
   - URLs complètes
   - Noms de champs JSON (linkedin, instagram, twitter, facebook)
   - Balises et code
3. Traduire les placeholders: [key topic] → [sujet clé], [main takeaway] → [point principal], etc.
4. Garder les noms de réseaux sociaux: LinkedIn, Instagram, Twitter, Facebook, YouTube

Voici les textes à traduire (format JSON):
{json.dumps(texts_to_translate, ensure_ascii=False, indent=2)}

Réponds UNIQUEMENT avec un objet JSON contenant les traductions, avec les mêmes clés."""
    
    headers = {
        'Authorization': f'Bearer {api_key}',
        'Content-Type': 'application/json'
    }
    
    data = {
        'model': 'gpt-3.5-turbo',
        'messages': [
            {'role': 'system', 'content': 'Tu es un traducteur expert spécialisé en workflows d\'automatisation.'},
            {'role': 'user', 'content': prompt}
        ],
        'temperature': 0.3,
        'max_tokens': 4000
    }
    
    try:
        response = requests.post(
            'https://api.openai.com/v1/chat/completions',
            headers=headers,
            json=data
        )
        
        if response.status_code == 200:
            result = response.json()
            content = result['choices'][0]['message']['content']
            
            # Essayer de parser le JSON
            try:
                # Nettoyer le contenu si nécessaire
                content = content.strip()
                if content.startswith('```json'):
                    content = content[7:]
                if content.endswith('```'):
                    content = content[:-3]
                
                translations = json.loads(content.strip())
                return translations
            except json.JSONDecodeError:
                print("⚠️  Erreur de parsing JSON de la réponse OpenAI")
                print("Réponse reçue:", content[:200])
                return {}
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
    
    # Traduire par batch (max 20 textes par requête pour éviter les limites)
    batch_size = 20
    all_translations = {}
    
    text_items = list(texts.items())
    for i in range(0, len(text_items), batch_size):
        batch = dict(text_items[i:i+batch_size])
        print(f"  📦 Traduction batch {i//batch_size + 1}/{(len(text_items)-1)//batch_size + 1}...")
        
        translations = translate_batch_with_openai(batch, api_key)
        
        if translations:
            all_translations.update(translations)
            print(f"    ✅ {len(translations)} textes traduits")
        else:
            print(f"    ⚠️  Échec de la traduction du batch")
        
        # Pause pour éviter de surcharger l'API
        if i + batch_size < len(text_items):
            sleep(1)
    
    # Mettre à jour les textes avec les traductions
    for text_id, translation in all_translations.items():
        if text_id in texts:
            texts[text_id]['translated'] = translation
    
    # Sauvegarder le résultat
    output_file = input_file.replace('_texts_to_translate.json', '_texts_translated.json')
    
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(extraction_data, f, ensure_ascii=False, indent=2)
    
    print(f"\n✅ Traduction terminée!")
    print(f"📊 {len(all_translations)}/{total_texts} textes traduits")
    print(f"💾 Sauvegardé dans: {output_file}")
    
    return output_file

def main():
    if len(sys.argv) < 2:
        print("Usage: python translate_texts.py <texts_to_translate.json>")
        sys.exit(1)
    
    input_file = sys.argv[1]
    
    # Obtenir la clé API
    api_key = get_openai_key()
    
    # Traduire
    translate_texts_file(input_file, api_key)

if __name__ == "__main__":
    main()