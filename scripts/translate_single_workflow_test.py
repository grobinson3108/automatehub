#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import sys
import json
import os

# Test sans utiliser le script complet de traduction pour isoler le problème
def main():
    source_file = "/var/www/automatehub/AutomationTribe/5 - Produits Prennent Vie/Life_Style_Product_Photo_Generator.json"
    
    # Lire le workflow
    with open(source_file, 'r', encoding='utf-8') as f:
        workflow_data = json.load(f)
    
    print("🔍 Analyse du workflow original:")
    print(f"   Nom: {workflow_data.get('name')}")
    print(f"   Nombre de nodes: {len(workflow_data.get('nodes', []))}")
    
    # Afficher les noms des nodes
    print("\n📋 Nodes dans le workflow:")
    for i, node in enumerate(workflow_data.get('nodes', [])):
        print(f"   {i+1}. {node.get('name')} (type: {node.get('type')})")
    
    # Vérifier les tags
    print(f"\n🏷️  Tags existants: {workflow_data.get('tags', [])}")
    
    # Créer une version simple traduite manuellement
    translated = json.loads(json.dumps(workflow_data))  # Deep copy
    
    # Traduire le nom du workflow manuellement pour l'instant
    translated['name'] = "Générateur de Photos Lifestyle de Produits"
    
    # Ajouter le tag Audelalia
    if 'tags' not in translated:
        translated['tags'] = []
    translated['tags'].append({"id": "1", "name": "Audelalia"})
    
    # Traduire quelques nodes manuellement pour test
    node_translations = {
        "When clicking 'Test workflow'": "Lors du clic sur 'Tester le workflow'",
        "Wait": "Attendre",
        "Get image": "Récupérer l'image",
        "Send request": "Envoyer la requête",
        "Get image link": "Récupérer le lien de l'image"
    }
    
    for node in translated.get('nodes', []):
        if node.get('name') in node_translations:
            node['name'] = node_translations[node['name']]
    
    # Sauvegarder
    dest_file = "/var/www/automatehub/workflows_traduits/FR/AutomationTribe/life_style_produit_photo_generator_TEST.json"
    os.makedirs(os.path.dirname(dest_file), exist_ok=True)
    
    with open(dest_file, 'w', encoding='utf-8') as f:
        json.dump(translated, f, ensure_ascii=False, indent=2)
    
    print(f"\n✅ Workflow traduit manuellement sauvegardé dans:")
    print(f"   {dest_file}")

if __name__ == "__main__":
    main()