#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour tester la traduction sur 10 workflows variés
"""
import os
import subprocess
import json

# Liste des 10 workflows à tester
workflows_to_test = [
    "/var/www/automatehub/AutomationTribe/5 - Produits Prennent Vie/Life_Style_Product_Photo_Generator.json",
    "/var/www/automatehub/AutomationTribe/Generate_social_post_ideas_or_summaries.json",
    "/var/www/automatehub/Ventes_Workflows/Pack_Decouverte_Gratuit/001_automation.json",
    "/var/www/automatehub/Ventes_Workflows/Pack_Decouverte_Gratuit/003_automation.json",
    "/var/www/automatehub/Ventes_Workflows/Pack_Ultimate_Collection/027_communicate_emailreadimap.json",
    "/var/www/automatehub/Ventes_Workflows/Pack_Ultimate_Collection/118_webhook_automation_eventbrite.json",
    "/var/www/automatehub/storage/app/tutorials/workflow-email-automation.json",
    "/var/www/automatehub/storage/app/tutorials/telegram-autoresponder-workflow.json",
    "/var/www/automatehub/storage/app/tutorials/workflow-telegram-advanced-bot.json",
    "/var/www/automatehub/storage/app/tutorials/module-1-1-workflow.json"
]

# Répertoire de sortie
output_dir = "/var/www/automatehub/workflows_traduits/FR/test_10_workflows"
os.makedirs(output_dir, exist_ok=True)

# Résultats
results = []

print("🚀 Test de traduction sur 10 workflows")
print("=" * 60)

for i, workflow_path in enumerate(workflows_to_test, 1):
    if not os.path.exists(workflow_path):
        print(f"\n❌ Workflow {i}: {os.path.basename(workflow_path)} - N'existe pas")
        results.append({"file": workflow_path, "status": "not_found"})
        continue
    
    print(f"\n📋 Workflow {i}/{len(workflows_to_test)}: {os.path.basename(workflow_path)}")
    
    # Utiliser le script de traduction avec fallback offline
    result = subprocess.run(
        [
            "python3",
            "/var/www/automatehub/scripts/translate_workflow_ai.py",
            workflow_path,
            output_dir
        ],
        capture_output=True,
        text=True
    )
    
    if result.returncode == 0:
        print("✅ Succès")
        
        # Analyser le résultat
        workflow_info = {
            "file": workflow_path,
            "status": "success",
            "original_name": os.path.basename(workflow_path)
        }
        
        # Trouver le fichier traduit
        from translate_all_workflows_v3 import translate_filename
        translated_name = translate_filename(os.path.basename(workflow_path))
        translated_path = os.path.join(output_dir, translated_name)
        
        if os.path.exists(translated_path):
            with open(translated_path, 'r', encoding='utf-8') as f:
                data = json.load(f)
            
            workflow_info["translated_name"] = translated_name
            workflow_info["workflow_name"] = data.get('name', 'Sans nom')
            workflow_info["nodes_count"] = len(data.get('nodes', []))
            workflow_info["has_audelalia_tag"] = any(tag.get('name') == 'Audelalia' for tag in data.get('tags', []))
            
            # Vérifier si des prompts ont été traduits
            prompts_translated = 0
            for node in data.get('nodes', []):
                if node.get('type') in ['@n8n/n8n-nodes-langchain.openAi', 'n8n-nodes-base.openAi']:
                    if 'parameters' in node and 'messages' in node['parameters']:
                        if 'values' in node['parameters']['messages']:
                            for msg in node['parameters']['messages']['values']:
                                if 'content' in msg and isinstance(msg['content'], str):
                                    content = msg['content']
                                    if content.startswith('='):
                                        content = content[1:]
                                    # Vérifier si c'est en français
                                    if any(fr_word in content for fr_word in ['Générer', 'Écrire', 'Créer', 'français', 'pour', 'avec']):
                                        prompts_translated += 1
            
            workflow_info["prompts_translated"] = prompts_translated
        
        results.append(workflow_info)
    else:
        print("❌ Échec")
        print(f"   Erreur: {result.stderr[:200]}...")
        results.append({
            "file": workflow_path,
            "status": "error",
            "error": result.stderr[:200]
        })

# Rapport final
print("\n" + "=" * 60)
print("📊 RAPPORT FINAL")
print("=" * 60)

success_count = sum(1 for r in results if r.get('status') == 'success')
print(f"\n✅ Succès: {success_count}/{len(workflows_to_test)}")

print("\n📋 Détails:")
for r in results:
    if r['status'] == 'success':
        print(f"\n✓ {os.path.basename(r['file'])}")
        print(f"  → {r.get('translated_name', '???')}")
        print(f"  Nom du workflow: {r.get('workflow_name', '???')}")
        print(f"  Nodes: {r.get('nodes_count', 0)}")
        print(f"  Tag Audelalia: {'✅' if r.get('has_audelalia_tag') else '❌'}")
        if r.get('prompts_translated', 0) > 0:
            print(f"  Prompts traduits: {r.get('prompts_translated', 0)}")
    elif r['status'] == 'error':
        print(f"\n✗ {os.path.basename(r['file'])}")
        print(f"  Erreur: {r.get('error', 'Inconnue')}")
    else:
        print(f"\n✗ {os.path.basename(r['file'])} - Non trouvé")

print("\n✨ Test terminé!")
print(f"📁 Workflows traduits dans: {output_dir}")

if __name__ == "__main__":
    pass