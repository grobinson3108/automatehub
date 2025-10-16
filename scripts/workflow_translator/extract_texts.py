#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Étape 1: Extraire tous les textes à traduire d'un workflow n8n
"""
import json
import sys
import os

def extract_texts_from_workflow(workflow_data):
    """Extraire tous les textes à traduire avec leurs chemins"""
    texts_to_translate = {}
    text_id = 0
    
    # 1. Nom du workflow
    if 'name' in workflow_data and workflow_data['name']:
        texts_to_translate[f"text_{text_id}"] = {
            "path": ["name"],
            "original": workflow_data['name'],
            "type": "workflow_name"
        }
        text_id += 1
    
    # 2. Parcourir tous les nodes
    if 'nodes' in workflow_data:
        for node_idx, node in enumerate(workflow_data['nodes']):
            # Nom du node
            if 'name' in node and node['name']:
                texts_to_translate[f"text_{text_id}"] = {
                    "path": ["nodes", node_idx, "name"],
                    "original": node['name'],
                    "type": "node_name"
                }
                text_id += 1
            
            # Notes du node
            if 'notes' in node and node['notes']:
                texts_to_translate[f"text_{text_id}"] = {
                    "path": ["nodes", node_idx, "notes"],
                    "original": node['notes'],
                    "type": "node_notes"
                }
                text_id += 1
            
            # Paramètres spécifiques
            if 'parameters' in node:
                # Sticky notes
                if node.get('type') == 'n8n-nodes-base.stickyNote' and 'content' in node['parameters']:
                    if node['parameters']['content']:
                        texts_to_translate[f"text_{text_id}"] = {
                            "path": ["nodes", node_idx, "parameters", "content"],
                            "original": node['parameters']['content'],
                            "type": "sticky_note"
                        }
                        text_id += 1
                
                # OpenAI et LangChain prompts (nouveau format avec messages)
                if node.get('type') in ['@n8n/n8n-nodes-langchain.openAi', 'n8n-nodes-base.openAi', '@n8n/n8n-nodes-langchain.agent', '@n8n/n8n-nodes-langchain.chainLlm']:
                    # Format ancien avec 'text'
                    if 'text' in node['parameters'] and node['parameters']['text']:
                        texts_to_translate[f"text_{text_id}"] = {
                            "path": ["nodes", node_idx, "parameters", "text"],
                            "original": node['parameters']['text'],
                            "type": "openai_prompt"
                        }
                        text_id += 1
                    
                    # Format nouveau avec 'messages' - structure différente pour LangChain
                    if 'messages' in node['parameters']:
                        # Structure OpenAI classique
                        if 'values' in node['parameters']['messages']:
                            for msg_idx, message in enumerate(node['parameters']['messages']['values']):
                                if 'content' in message and message['content']:
                                    content = message['content']
                                    # Retirer le = au début si présent
                                    has_equal = content.startswith('=')
                                    if has_equal:
                                        content = content[1:]

                                    texts_to_translate[f"text_{text_id}"] = {
                                        "path": ["nodes", node_idx, "parameters", "messages", "values", msg_idx, "content"],
                                        "original": content,
                                        "type": "openai_message",
                                        "has_equal_prefix": has_equal
                                    }
                                    text_id += 1

                        # Structure LangChain avec messageValues
                        elif 'messageValues' in node['parameters']['messages']:
                            for msg_idx, message in enumerate(node['parameters']['messages']['messageValues']):
                                if 'message' in message and message['message']:
                                    content = message['message']
                                    # Retirer le = au début si présent
                                    has_equal = content.startswith('=')
                                    if has_equal:
                                        content = content[1:]

                                    texts_to_translate[f"text_{text_id}"] = {
                                        "path": ["nodes", node_idx, "parameters", "messages", "messageValues", msg_idx, "message"],
                                        "original": content,
                                        "type": "langchain_message",
                                        "has_equal_prefix": has_equal
                                    }
                                    text_id += 1

                    # SystemMessage dans les options (pour les agents LangChain)
                    if 'options' in node['parameters'] and 'systemMessage' in node['parameters']['options']:
                        if node['parameters']['options']['systemMessage']:
                            content = node['parameters']['options']['systemMessage']
                            # Retirer le = au début si présent
                            has_equal = content.startswith('=')
                            if has_equal:
                                content = content[1:]

                            texts_to_translate[f"text_{text_id}"] = {
                                "path": ["nodes", node_idx, "parameters", "options", "systemMessage"],
                                "original": content,
                                "type": "system_message",
                                "has_equal_prefix": has_equal
                            }
                            text_id += 1
                
                # Variables dans les nodes Set
                if node.get('type') == 'n8n-nodes-base.set' and 'assignments' in node['parameters']:
                    if 'assignments' in node['parameters'].get('assignments', {}):
                        for assign_idx, assignment in enumerate(node['parameters']['assignments']['assignments']):
                            if 'name' in assignment and assignment['name']:
                                # Seulement si c'est un terme traduisible
                                if any(word in assignment['name'].lower() for word in ['body', 'subject', 'header', 'sender', 'recipient']):
                                    texts_to_translate[f"text_{text_id}"] = {
                                        "path": ["nodes", node_idx, "parameters", "assignments", "assignments", assign_idx, "name"],
                                        "original": assignment['name'],
                                        "type": "variable_name"
                                    }
                                    text_id += 1
                
                # Autres champs texte
                text_fields = ['summary', 'description', 'fileName', 'prompt', 'toolDescription', 'subject', 'message', 'text', 'body', 'title', 'content', 'value']
                for field in text_fields:
                    if field in node['parameters'] and node['parameters'][field]:
                        field_value = node['parameters'][field]
                        # Pour le champ 'value', on vérifie que c'est bien du texte traduisible
                        if field == 'value':
                            if isinstance(field_value, str) and len(field_value) > 10 and not field_value.startswith('http') and not field_value.startswith('{{'):
                                # Seulement si ça ressemble à du texte naturel
                                if any(word in field_value.lower() for word in ['the', 'this', 'that', 'and', 'or', 'with', 'for', 'to', 'a', 'an', 'le', 'la', 'les', 'et', 'ou', 'avec', 'pour', 'de', 'du', 'des']):
                                    texts_to_translate[f"text_{text_id}"] = {
                                        "path": ["nodes", node_idx, "parameters", field],
                                        "original": field_value,
                                        "type": f"parameter_{field}"
                                    }
                                    text_id += 1
                        else:
                            # Pour les autres champs, on traduit directement s'ils contiennent du texte
                            if isinstance(field_value, str) and len(field_value.strip()) > 0:
                                texts_to_translate[f"text_{text_id}"] = {
                                    "path": ["nodes", node_idx, "parameters", field],
                                    "original": field_value,
                                    "type": f"parameter_{field}"
                                }
                                text_id += 1
                
                # Body parameters pour les prompts
                if 'bodyParameters' in node['parameters'] and 'parameters' in node['parameters']['bodyParameters']:
                    for param_idx, param in enumerate(node['parameters']['bodyParameters']['parameters']):
                        if isinstance(param, dict) and param.get('name') == 'prompt' and 'value' in param:
                            if isinstance(param['value'], str) and len(param['value']) > 5:
                                texts_to_translate[f"text_{text_id}"] = {
                                    "path": ["nodes", node_idx, "parameters", "bodyParameters", "parameters", param_idx, "value"],
                                    "original": param['value'],
                                    "type": "body_prompt"
                                }
                                text_id += 1
    
    return texts_to_translate

def main():
    if len(sys.argv) < 2:
        print("Usage: python extract_texts.py <workflow.json>")
        sys.exit(1)
    
    input_file = sys.argv[1]
    
    # Lire le workflow
    with open(input_file, 'r', encoding='utf-8') as f:
        workflow_data = json.load(f)
    
    # Extraire les textes
    texts = extract_texts_from_workflow(workflow_data)
    
    # Créer le fichier d'extraction dans le même répertoire que le fichier source
    source_dir = os.path.dirname(os.path.abspath(input_file))
    basename = os.path.basename(input_file)
    output_file = os.path.join(source_dir, basename.replace('.json', '_texts_to_translate.json'))
    
    extraction_data = {
        "source_file": input_file,
        "total_texts": len(texts),
        "texts": texts
    }
    
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(extraction_data, f, ensure_ascii=False, indent=2)
    
    print(f"✅ Extraction terminée!")
    print(f"📊 {len(texts)} textes extraits")
    print(f"💾 Sauvegardé dans: {output_file}")
    
    # Aperçu
    print("\n📋 Aperçu des textes extraits:")
    for i, (text_id, text_info) in enumerate(list(texts.items())[:5]):
        print(f"{i+1}. Type: {text_info['type']}")
        print(f"   Texte: {text_info['original'][:100]}...")

if __name__ == "__main__":
    main()