#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script de traduction intelligent qui détecte automatiquement les textes utilisateur
"""

import json
import os
import re
import requests
import time
from pathlib import Path

class SmartWorkflowTranslator:
    def __init__(self, api_key):
        self.api_key = api_key
        self.mapping_dir = "/var/www/automatehub/translation_mappings_smart"
        os.makedirs(self.mapping_dir, exist_ok=True)
        
        # Mots indicateurs de texte utilisateur en anglais
        self.english_indicators = [
            'enter', 'click', 'add', 'create', 'generate', 'select', 'choose',
            'upload', 'download', 'send', 'receive', 'start', 'stop', 'pause',
            'your', 'here', 'this', 'that', 'please', 'when', 'how', 'what',
            'the', 'and', 'for', 'with', 'from', 'into', 'about'
        ]
        
        # Champs à ignorer (technique)
        self.technical_fields = {
            'id', 'type', 'typeVersion', 'position', 'credentials', 'mode',
            'cachedResultName', 'cachedResultUrl', '__rl', 'webhook', 'timeout'
        }
    
    def is_user_text(self, text):
        """Détermine si un texte semble être du contenu utilisateur à traduire"""
        if not isinstance(text, str) or len(text.strip()) < 3:
            return False
        
        text_lower = text.lower().strip()
        
        # Ignorer les valeurs techniques courantes
        if text_lower in ['true', 'false', 'null', 'undefined', 'yes', 'no']:
            return False
        
        # Ignorer les URLs, emails, IDs techniques
        if re.match(r'^https?://', text) or '@' in text or re.match(r'^[a-zA-Z0-9_-]{20,}$', text):
            return False
        
        # Ignorer les variables n8n pures
        if re.match(r'^={{.*}}$', text.strip()):
            return False
        
        # Critère 1: Plus de 3 mots
        words = text.split()
        if len(words) >= 3:
            return True
        
        # Critère 2: Commence par un article ou mot courant anglais
        first_word = words[0].lower() if words else ""
        if first_word in ['the', 'a', 'an', 'enter', 'click', 'add', 'create', 'your']:
            return True
        
        # Critère 3: Contient des mots indicateurs anglais
        for word in words:
            if word.lower() in self.english_indicators:
                return True
        
        # Critère 4: Texte en majuscules avec espaces (comme "ENTER YOUR API")
        if text.isupper() and ' ' in text and len(words) >= 2:
            return True
        
        # Critère 5: Phrases avec ponctuation
        if re.search(r'[.!?]', text) and len(words) >= 2:
            return True
        
        return False
    
    def extract_texts_recursive(self, obj, path="", texts_map=None, text_counter=None):
        """Extraction récursive de tous les textes utilisateur"""
        if texts_map is None:
            texts_map = {}
        if text_counter is None:
            text_counter = [1]  # Liste pour référence mutable
        
        if isinstance(obj, dict):
            new_obj = {}
            for key, value in obj.items():
                new_path = f"{path}.{key}" if path else key
                
                # Ignorer les champs techniques
                if key in self.technical_fields:
                    new_obj[key] = value
                    continue
                
                # Si c'est une string et que c'est du texte utilisateur
                if isinstance(value, str) and self.is_user_text(value):
                    # Gérer le préfixe =
                    has_prefix = value.startswith('=')
                    actual_text = value[1:] if has_prefix else value
                    
                    if self.is_user_text(actual_text):  # Vérifier encore après avoir enlevé =
                        placeholder = f"$text_{text_counter[0]}"
                        texts_map[placeholder] = {
                            'original': actual_text,
                            'path': new_path,
                            'context': self.get_context_from_path(new_path),
                            'has_prefix': has_prefix
                        }
                        new_obj[key] = ('=' if has_prefix else '') + placeholder
                        text_counter[0] += 1
                    else:
                        new_obj[key] = value
                else:
                    # Récursion pour les objets et listes
                    new_obj[key] = self.extract_texts_recursive(value, new_path, texts_map, text_counter)
        
        elif isinstance(obj, list):
            new_obj = []
            for i, item in enumerate(obj):
                new_path = f"{path}[{i}]"
                new_obj.append(self.extract_texts_recursive(item, new_path, texts_map, text_counter))
        
        else:
            new_obj = obj
        
        return new_obj
    
    def get_context_from_path(self, path):
        """Détermine le contexte basé sur le chemin"""
        if path == 'name':
            return 'workflow_name'
        elif 'nodes[' in path and '.name' in path:
            return 'node_name'
        elif 'systemMessage' in path:
            return 'system_message'
        elif 'content' in path:
            return 'message'
        elif 'text' in path:
            return 'prompt'
        elif 'value' in path:
            return 'parameter_value'
        elif 'description' in path:
            return 'description'
        else:
            return 'user_text'
    
    def create_translation_batch(self, texts_map, max_chars=3000):
        """Crée des batches de textes pour l'API OpenAI"""
        batches = []
        current_batch = {}
        current_chars = 0
        
        for placeholder, text_info in texts_map.items():
            text = text_info['original']
            text_chars = len(text)
            
            if text_chars > max_chars:
                if current_batch:
                    batches.append(current_batch)
                    current_batch = {}
                    current_chars = 0
                batches.append({placeholder: text_info})
            else:
                if current_chars + text_chars > max_chars and current_batch:
                    batches.append(current_batch)
                    current_batch = {}
                    current_chars = 0
                
                current_batch[placeholder] = text_info
                current_chars += text_chars
        
        if current_batch:
            batches.append(current_batch)
        
        return batches
    
    def translate_batch(self, batch):
        """Traduit un batch de textes avec OpenAI"""
        prompt = """Tu es un traducteur expert pour workflows n8n.
Traduis les textes suivants de l'anglais vers le français.

RÈGLES IMPORTANTES :
1. Si le texte est DÉJÀ en français, retourne-le EXACTEMENT tel quel
2. Garde TOUTES les variables entre {{ }} exactement comme elles sont
3. Traduis uniquement les textes en anglais
4. Pour les instructions utilisateur, utilise un ton professionnel mais accessible
5. Retourne UNIQUEMENT un objet JSON valide sans aucun autre texte

Format de réponse EXACT :
{
  "$text_X": "traduction en français ou texte original si déjà en français"
}

Textes à traduire :
"""
        
        for placeholder, text_info in batch.items():
            context = text_info['context']
            text = text_info['original']
            
            if len(text) > 200:
                display_text = text[:197] + "..."
            else:
                display_text = text
                
            prompt += f"\n{placeholder} ({context}): {display_text}"
        
        prompt += "\n\nRéponse JSON :"
        
        try:
            response = requests.post(
                'https://api.openai.com/v1/chat/completions',
                headers={
                    'Authorization': f'Bearer {self.api_key}',
                    'Content-Type': 'application/json'
                },
                json={
                    'model': 'gpt-4o-mini',
                    'messages': [
                        {
                            'role': 'system',
                            'content': 'Tu es un traducteur expert spécialisé dans les interfaces utilisateur et l\'automatisation.'
                        },
                        {
                            'role': 'user',
                            'content': prompt
                        }
                    ],
                    'temperature': 0.3,
                    'max_tokens': 4000
                },
                timeout=60
            )
            
            if response.status_code == 200:
                result = response.json()['choices'][0]['message']['content']
                
                # Parser le JSON
                try:
                    json_match = re.search(r'\{[\s\S]*\}', result, re.DOTALL)
                    if json_match:
                        translations = json.loads(json_match.group())
                        return translations
                except:
                    pass
                
                try:
                    translations = json.loads(result)
                    return translations
                except:
                    pass
                
                # Extraction manuelle
                try:
                    translations = {}
                    pattern = r'"\$text_(\d+)"\s*:\s*"([^"]*)"'
                    matches = re.findall(pattern, result)
                    for num, trans in matches:
                        translations[f"$text_{num}"] = trans
                    return translations
                except:
                    pass
                    
                return {}
            else:
                print(f"❌ Erreur API: {response.status_code}")
                return {}
                
        except Exception as e:
            print(f"❌ Erreur de traduction: {e}")
            return {}
    
    def apply_translations(self, modified_workflow, texts_map, translations):
        """Applique les traductions au workflow"""
        final_workflow = json.loads(json.dumps(modified_workflow))
        
        def replace_placeholders(obj):
            if isinstance(obj, str):
                if obj.startswith('=') and obj[1:] in translations:
                    placeholder = obj[1:]
                    translated = translations.get(placeholder, texts_map.get(placeholder, {}).get('original', placeholder))
                    if isinstance(translated, str):
                        return '=' + translated
                    return obj
                elif obj in translations:
                    translated = translations.get(obj, texts_map.get(obj, {}).get('original', obj))
                    if isinstance(translated, str):
                        return translated
                    return obj
                return obj
            elif isinstance(obj, dict):
                return {k: replace_placeholders(v) for k, v in obj.items()}
            elif isinstance(obj, list):
                return [replace_placeholders(item) for item in obj]
            else:
                return obj
        
        final_workflow = replace_placeholders(final_workflow)
        
        # Ajouter le tag Audelalia
        tags = final_workflow.get('tags', [])
        if not any(tag.get('name') == 'Audelalia' for tag in tags):
            tags.append({"id": "1", "name": "Audelalia"})
            final_workflow['tags'] = tags
        
        return final_workflow
    
    def translate_workflow(self, workflow_path):
        """Traduit un workflow avec détection intelligente"""
        print(f"\n📄 Traduction intelligente: {os.path.basename(workflow_path)}")
        
        with open(workflow_path, 'r', encoding='utf-8') as f:
            workflow = json.load(f)
        
        # Extraction intelligente
        print("  🧠 Détection intelligente des textes...")
        texts_map = {}
        text_counter = [1]
        modified_workflow = self.extract_texts_recursive(workflow, "", texts_map, text_counter)
        
        if not texts_map:
            print("  ℹ️  Aucun texte utilisateur détecté")
            return False
        
        print(f"  📊 {len(texts_map)} textes utilisateur détectés")
        
        # Sauvegarder le mapping
        mapping_file = os.path.join(self.mapping_dir, f"{os.path.basename(workflow_path)}.mapping.json")
        with open(mapping_file, 'w', encoding='utf-8') as f:
            json.dump({
                'workflow_path': workflow_path,
                'texts_map': texts_map,
                'modified_workflow': modified_workflow
            }, f, indent=2, ensure_ascii=False)
        
        # Traduction
        batches = self.create_translation_batch(texts_map)
        print(f"  📦 {len(batches)} batch(es) créé(s)")
        
        all_translations = {}
        for i, batch in enumerate(batches):
            print(f"  🌐 Traduction batch {i+1}/{len(batches)} ({len(batch)} textes)...")
            translations = self.translate_batch(batch)
            all_translations.update(translations)
            
            if i < len(batches) - 1:
                time.sleep(2)
        
        print(f"  ✅ {len(all_translations)} textes traduits")
        
        # Application
        final_workflow = self.apply_translations(modified_workflow, texts_map, all_translations)
        
        with open(workflow_path, 'w', encoding='utf-8') as f:
            json.dump(final_workflow, f, indent=2, ensure_ascii=False)
        
        print("  💾 Workflow sauvegardé")
        return True

def get_openai_key():
    """Récupérer la clé OpenAI depuis le .env"""
    env_file = '/var/www/automatehub/.env'
    if os.path.exists(env_file):
        with open(env_file, 'r') as f:
            for line in f:
                if line.strip().startswith('OPENAI_API_KEY='):
                    key = line.strip().split('=', 1)[1]
                    return key.strip().strip('"').strip("'")
    return None

def main():
    print("🧠 TRADUCTION INTELLIGENTE - DÉTECTION AUTOMATIQUE")
    print("=" * 70)
    
    api_key = get_openai_key()
    if not api_key:
        print("❌ Clé OpenAI non trouvée")
        return
    
    print("✅ Clé OpenAI trouvée")
    
    translator = SmartWorkflowTranslator(api_key)
    
    # Traiter tous les workflows AutomationTribe
    base_dir = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"
    
    # Collecter tous les workflows JSON
    workflows = []
    for root, dirs, files in os.walk(base_dir):
        for file in files:
            if file.endswith('.json'):
                workflows.append(os.path.join(root, file))
    
    print(f"\n📁 {len(workflows)} workflows trouvés dans AutomationTribe")
    print("=" * 70)
    
    # Traiter chaque workflow
    success_count = 0
    enhanced_count = 0
    error_count = 0
    
    for i, workflow_path in enumerate(workflows):
        try:
            print(f"\n[{i+1}/{len(workflows)}] Traitement intelligent...")
            
            # Compter les textes avant
            with open(workflow_path, 'r', encoding='utf-8') as f:
                original_content = f.read()
            
            result = translator.translate_workflow(workflow_path)
            
            if result:
                # Compter les textes après
                with open(workflow_path, 'r', encoding='utf-8') as f:
                    new_content = f.read()
                
                if new_content != original_content:
                    enhanced_count += 1
                    print("  ✨ Workflow amélioré avec nouveaux textes")
                else:
                    print("  ✅ Workflow déjà optimal")
                
                success_count += 1
            else:
                print("  ⚠️  Pas de texte utilisateur détecté")
            
            # Pause tous les 5 workflows pour éviter rate limit
            if (i + 1) % 5 == 0:
                print(f"\n⏸️  Pause de 10 secondes après {i+1} workflows...")
                time.sleep(10)
                
        except Exception as e:
            print(f"  ❌ ERREUR: {e}")
            error_count += 1
            
    print("\n" + "=" * 70)
    print("📊 RÉSUMÉ FINAL INTELLIGENT:")
    print(f"  ✅ Workflows traités avec succès: {success_count}")
    print(f"  ✨ Workflows améliorés avec nouveaux textes: {enhanced_count}")
    print(f"  ⚠️  Workflows sans nouveaux textes: {success_count - enhanced_count}")
    print(f"  ❌ Erreurs: {error_count}")
    print(f"  📁 Mappings intelligents sauvegardés dans: {translator.mapping_dir}")
    print("=" * 70)

if __name__ == "__main__":
    main()