#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script de traduction performant avec système de mapping
Approche : Extraction → Mapping → Traduction batch → Remplacement
"""

import json
import os
import re
import requests
import time
from pathlib import Path
import hashlib

class WorkflowTranslator:
    def __init__(self, api_key):
        self.api_key = api_key
        self.mapping_dir = "/var/www/automatehub/translation_mappings"
        os.makedirs(self.mapping_dir, exist_ok=True)
        
    def extract_texts_to_translate(self, workflow):
        """Extrait tous les textes à traduire et crée un mapping"""
        texts_map = {}
        text_counter = 1
        modified_workflow = json.loads(json.dumps(workflow))  # Deep copy
        
        # 1. Nom du workflow
        if 'name' in modified_workflow:
            name = modified_workflow['name']
            if name and len(name) > 2:
                placeholder = f"$text_{text_counter}"
                texts_map[placeholder] = {
                    'original': name,
                    'path': 'name',
                    'context': 'workflow_name'
                }
                modified_workflow['name'] = placeholder
                text_counter += 1
        
        # 2. Parcourir les nœuds
        for node_idx, node in enumerate(modified_workflow.get('nodes', [])):
            # Nom du nœud
            if 'name' in node:
                node_name = node['name']
                if node_name and len(node_name) > 1:
                    placeholder = f"$text_{text_counter}"
                    texts_map[placeholder] = {
                        'original': node_name,
                        'path': f'nodes[{node_idx}].name',
                        'context': 'node_name'
                    }
                    node['name'] = placeholder
                    text_counter += 1
            
            # Paramètres du nœud
            params = node.get('parameters', {})
            
            # Texte principal
            if 'text' in params and isinstance(params['text'], str):
                text = params['text']
                if len(text) > 10:
                    # Gérer le préfixe =
                    has_prefix = text.startswith('=')
                    actual_text = text[1:] if has_prefix else text
                    
                    placeholder = f"$text_{text_counter}"
                    texts_map[placeholder] = {
                        'original': actual_text,
                        'path': f'nodes[{node_idx}].parameters.text',
                        'context': 'prompt',
                        'has_prefix': has_prefix
                    }
                    params['text'] = ('=' if has_prefix else '') + placeholder
                    text_counter += 1
            
            # Messages (pour OpenAI nodes)
            if 'messages' in params and 'values' in params['messages']:
                for msg_idx, message in enumerate(params['messages']['values']):
                    if 'content' in message and isinstance(message['content'], str):
                        content = message['content']
                        if len(content) > 10:
                            has_prefix = content.startswith('=')
                            actual_content = content[1:] if has_prefix else content
                            
                            placeholder = f"$text_{text_counter}"
                            texts_map[placeholder] = {
                                'original': actual_content,
                                'path': f'nodes[{node_idx}].parameters.messages.values[{msg_idx}].content',
                                'context': 'message',
                                'has_prefix': has_prefix
                            }
                            message['content'] = ('=' if has_prefix else '') + placeholder
                            text_counter += 1
            
            # SystemMessage dans options
            if 'options' in params and 'systemMessage' in params['options']:
                sys_msg = params['options']['systemMessage']
                if isinstance(sys_msg, str) and len(sys_msg) > 10:
                    has_prefix = sys_msg.startswith('=')
                    actual_msg = sys_msg[1:] if has_prefix else sys_msg
                    
                    placeholder = f"$text_{text_counter}"
                    texts_map[placeholder] = {
                        'original': actual_msg,
                        'path': f'nodes[{node_idx}].parameters.options.systemMessage',
                        'context': 'system_message',
                        'has_prefix': has_prefix
                    }
                    params['options']['systemMessage'] = ('=' if has_prefix else '') + placeholder
                    text_counter += 1
        
        return modified_workflow, texts_map
    
    def create_translation_batch(self, texts_map, max_chars=3000):
        """Crée des batches de textes pour l'API OpenAI"""
        batches = []
        current_batch = {}
        current_chars = 0
        
        for placeholder, text_info in texts_map.items():
            text = text_info['original']
            text_chars = len(text)
            
            # Si un seul texte est trop long, le traiter seul
            if text_chars > max_chars:
                if current_batch:
                    batches.append(current_batch)
                    current_batch = {}
                    current_chars = 0
                batches.append({placeholder: text_info})
            else:
                # Si ajouter ce texte dépasse la limite, créer un nouveau batch
                if current_chars + text_chars > max_chars and current_batch:
                    batches.append(current_batch)
                    current_batch = {}
                    current_chars = 0
                
                current_batch[placeholder] = text_info
                current_chars += text_chars
        
        # Ajouter le dernier batch
        if current_batch:
            batches.append(current_batch)
        
        return batches
    
    def translate_batch(self, batch):
        """Traduit un batch de textes avec OpenAI"""
        # Créer le prompt
        prompt = """Tu es un traducteur expert pour workflows n8n.
Traduis les textes suivants de l'anglais vers le français.

RÈGLES IMPORTANTES :
1. Si le texte est DÉJÀ en français, retourne-le EXACTEMENT tel quel
2. Garde TOUTES les variables entre {{ }} exactement comme elles sont
3. Traduis uniquement les textes en anglais
4. Retourne UNIQUEMENT un objet JSON valide sans aucun autre texte

Format de réponse EXACT :
{
  "$text_X": "traduction en français ou texte original si déjà en français"
}

Textes à traduire :
"""
        
        # Ajouter les textes (limiter la longueur des très longs textes)
        for placeholder, text_info in batch.items():
            context = text_info['context']
            text = text_info['original']
            
            # Limiter les textes très longs pour l'affichage dans le prompt
            if len(text) > 500:
                display_text = text[:497] + "..."
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
                            'content': 'Tu es un traducteur expert spécialisé dans les workflows techniques et l\'automatisation.'
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
                print(f"  📋 Réponse OpenAI reçue ({len(result)} caractères)")
                
                # Extraire le JSON de la réponse
                try:
                    # Chercher le JSON dans la réponse (pattern plus flexible)
                    json_match = re.search(r'\{[\s\S]*\}', result, re.DOTALL)
                    if json_match:
                        translations = json.loads(json_match.group())
                        return translations
                except Exception as e:
                    print(f"  ⚠️  Erreur parsing regex: {e}")
                    
                # Essayer de parser directement
                try:
                    translations = json.loads(result)
                    return translations
                except Exception as e:
                    print(f"  ❌ Impossible de parser la réponse JSON: {e}")
                    print(f"  📄 Réponse brute: {result[:200]}...")
                    
                    # Essayer de construire manuellement si possible
                    try:
                        translations = {}
                        # Chercher des patterns comme "$text_1": "..."
                        pattern = r'"\$text_(\d+)"\s*:\s*"([^"]*)"'
                        matches = re.findall(pattern, result)
                        for num, trans in matches:
                            translations[f"$text_{num}"] = trans
                        if translations:
                            print(f"  ✅ {len(translations)} traductions extraites manuellement")
                            return translations
                    except:
                        pass
                    
                    return {}
            else:
                print(f"❌ Erreur API: {response.status_code}")
                if response.status_code == 400:
                    print(f"  Détails: {response.json()}")
                return {}
                
        except Exception as e:
            print(f"❌ Erreur de traduction: {e}")
            return {}
    
    def apply_translations(self, modified_workflow, texts_map, translations):
        """Applique les traductions au workflow"""
        final_workflow = json.loads(json.dumps(modified_workflow))  # Deep copy
        
        # Remplacer récursivement tous les placeholders
        def replace_placeholders(obj):
            if isinstance(obj, str):
                # Vérifier si c'est un placeholder avec préfixe
                if obj.startswith('=') and obj[1:] in translations:
                    placeholder = obj[1:]
                    return '=' + translations.get(placeholder, texts_map[placeholder]['original'])
                # Placeholder simple
                elif obj in translations:
                    return translations.get(obj, texts_map[obj]['original'])
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
        """Traduit un workflow complet"""
        print(f"\n📄 Traduction de: {os.path.basename(workflow_path)}")
        
        # 1. Charger le workflow
        with open(workflow_path, 'r', encoding='utf-8') as f:
            workflow = json.load(f)
        
        # 2. Extraire les textes et créer le workflow modifié
        print("  📝 Extraction des textes...")
        modified_workflow, texts_map = self.extract_texts_to_translate(workflow)
        
        if not texts_map:
            print("  ℹ️  Aucun texte à traduire")
            return False
        
        print(f"  📊 {len(texts_map)} textes extraits")
        
        # 3. Sauvegarder le mapping (pour debug)
        mapping_file = os.path.join(self.mapping_dir, f"{os.path.basename(workflow_path)}.mapping.json")
        with open(mapping_file, 'w', encoding='utf-8') as f:
            json.dump({
                'workflow_path': workflow_path,
                'texts_map': texts_map,
                'modified_workflow': modified_workflow
            }, f, indent=2, ensure_ascii=False)
        
        # 4. Créer les batches pour traduction
        batches = self.create_translation_batch(texts_map)
        print(f"  📦 {len(batches)} batch(es) créé(s)")
        
        # 5. Traduire chaque batch
        all_translations = {}
        for i, batch in enumerate(batches):
            print(f"  🌐 Traduction batch {i+1}/{len(batches)} ({len(batch)} textes)...")
            translations = self.translate_batch(batch)
            all_translations.update(translations)
            
            # Pause entre les batches pour éviter rate limit
            if i < len(batches) - 1:
                time.sleep(2)
        
        print(f"  ✅ {len(all_translations)} textes traduits")
        
        # 6. Appliquer les traductions
        final_workflow = self.apply_translations(modified_workflow, texts_map, all_translations)
        
        # 7. Sauvegarder le workflow traduit
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
                    return line.strip().split('=', 1)[1].strip('"\'')
    return None

def main():
    print("🚀 TRADUCTION PERFORMANTE AVEC MAPPING")
    print("=" * 60)
    
    # Obtenir la clé API
    api_key = get_openai_key()
    if not api_key:
        print("❌ Clé OpenAI non trouvée dans .env")
        return
    
    print("✅ Clé OpenAI trouvée")
    
    # Créer le traducteur
    translator = WorkflowTranslator(api_key)
    
    # Base directory
    base_dir = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe"
    
    # Collecter tous les workflows JSON
    workflows = []
    for root, dirs, files in os.walk(base_dir):
        for file in files:
            if file.endswith('.json'):
                workflows.append(os.path.join(root, file))
    
    print(f"\n📁 {len(workflows)} workflows trouvés dans AutomationTribe")
    print("=" * 60)
    
    # Traiter chaque workflow
    success_count = 0
    error_count = 0
    
    for i, workflow_path in enumerate(workflows):
        try:
            print(f"\n[{i+1}/{len(workflows)}] Traitement...")
            if translator.translate_workflow(workflow_path):
                success_count += 1
            else:
                print("  ⚠️  Pas de texte à traduire")
            
            # Pause tous les 10 workflows pour éviter rate limit
            if (i + 1) % 10 == 0:
                print(f"\n⏸️  Pause de 10 secondes après {i+1} workflows...")
                time.sleep(10)
                
        except Exception as e:
            print(f"  ❌ ERREUR: {e}")
            error_count += 1
            
    print("\n" + "=" * 60)
    print("📊 RÉSUMÉ FINAL:")
    print(f"  ✅ Workflows traduits avec succès: {success_count}")
    print(f"  ⚠️  Workflows sans texte à traduire: {len(workflows) - success_count - error_count}")
    print(f"  ❌ Erreurs: {error_count}")
    print(f"  📁 Mappings sauvegardés dans: {translator.mapping_dir}")
    print("=" * 60)

if __name__ == "__main__":
    main()