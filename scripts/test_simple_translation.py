#!/usr/bin/env python3
"""
Test de traduction sur un workflow simple
"""

import sys
sys.path.append('/var/www/automatehub/scripts')
from translate_workflow_smart import SmartWorkflowTranslator, get_openai_key

def main():
    print("🧪 TEST TRADUCTION SIMPLE")
    print("=" * 40)
    
    api_key = get_openai_key()
    if not api_key:
        print("❌ Clé OpenAI manquante")
        return
    
    translator = SmartWorkflowTranslator(api_key)
    
    # Test sur le workflow simple
    test_file = "/var/www/automatehub/test_simple_workflow.json"
    result = translator.translate_workflow(test_file)
    
    if result:
        print("\n✅ Traduction réussie")
        
        # Vérifier le résultat
        import json
        with open(test_file, 'r', encoding='utf-8') as f:
            workflow = json.load(f)
        
        print("\n📄 RÉSULTAT:")
        print(f"  Nom: {workflow['name']}")
        print(f"  Node name: {workflow['nodes'][0]['name']}")
        print(f"  Text: {workflow['nodes'][0]['parameters']['text']}")
        print(f"  Value: {workflow['nodes'][0]['parameters']['value']}")
        
        # Vérifier si API KEY est traduit
        api_value = workflow['nodes'][0]['parameters']['value']
        if "ENTER" not in api_value or "ENTREZ" in api_value:
            print("  ✅ Texte API traduit!")
        else:
            print("  ❌ Texte API non traduit")
    else:
        print("❌ Échec de traduction")

if __name__ == "__main__":
    main()