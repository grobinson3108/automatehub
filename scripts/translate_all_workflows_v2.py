#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import json
import os
import sys
import re
import shutil
from datetime import datetime
from translate_workflow_complete import translate_workflow, translate_text_with_dict

# Dictionnaire pour traduire les noms de fichiers
FILENAME_TRANSLATIONS = {
    # Termes techniques courants
    "webhook": "declencheur_web",
    "trigger": "declencheur",
    "schedule": "planification",
    "scheduled": "planifie",
    "manual": "manuel",
    "email": "courriel",
    "gmail": "gmail",
    "outlook": "outlook",
    "slack": "slack",
    "telegram": "telegram",
    "discord": "discord",
    "api": "api",
    "integration": "integration",
    "automation": "automatisation",
    "workflow": "flux_travail",
    "process": "processus",
    "code": "code",
    "execute": "executer",
    "executeworkflow": "executer_flux",
    "database": "base_donnees",
    "postgres": "postgres",
    "mysql": "mysql",
    "mongodb": "mongodb",
    "file": "fichier",
    "ops": "operations",
    "management": "gestion",
    "communication": "communication",
    "data": "donnees",
    "analysis": "analyse",
    "reporting": "rapportage",
    "dashboard": "tableau_bord",
    "notification": "notification",
    "alert": "alerte",
    "sync": "synchronisation",
    "backup": "sauvegarde",
    "export": "exportation",
    "import": "importation",
    "transform": "transformation",
    "filter": "filtrage",
    "merge": "fusion",
    "split": "separation",
    "aggregate": "agregation",
    "calculate": "calcul",
    "validate": "validation",
    "clean": "nettoyage",
    "enrich": "enrichissement",
    "monitor": "surveillance",
    "track": "suivi",
    "log": "journalisation",
    "error": "erreur",
    "success": "succes",
    "failure": "echec",
    "retry": "reessayer",
    "wait": "attendre",
    "delay": "delai",
    "loop": "boucle",
    "condition": "condition",
    "switch": "aiguillage",
    "router": "routeur",
    "splitter": "diviseur",
    "merger": "fusionneur",
    "transformer": "transformateur",
    "formatter": "formateur",
    "parser": "analyseur",
    "encoder": "encodeur",
    "decoder": "decodeur",
    "encrypt": "chiffrer",
    "decrypt": "dechiffrer",
    "hash": "hacher",
    "sign": "signer",
    "verify": "verifier",
    "authenticate": "authentifier",
    "authorize": "autoriser",
    "simple": "simple",
    "complex": "complexe",
    "basic": "basique",
    "advanced": "avance",
    "custom": "personnalise",
    "template": "modele",
    "example": "exemple",
    "demo": "demo",
    "test": "test",
    "production": "production",
    "development": "developpement",
    "staging": "preparation",
    # Nombres en anglais
    "one": "un",
    "two": "deux", 
    "three": "trois",
    "four": "quatre",
    "five": "cinq",
    "six": "six",
    "seven": "sept",
    "eight": "huit",
    "nine": "neuf",
    "ten": "dix",
    # Termes spécifiques aux noms de fichiers trouvés
    "nodes": "noeuds",
    "node": "noeud",
    "googledrive": "google_drive",
    "dropbox": "dropbox",
    "onedrive": "onedrive",
    "sharepoint": "sharepoint",
    "ftp": "ftp",
    "sftp": "sftp",
    "http": "http",
    "rest": "rest",
    "soap": "soap",
    "graphql": "graphql",
    "websocket": "websocket",
    "mqtt": "mqtt",
    "redis": "redis",
    "kafka": "kafka",
    "rabbitmq": "rabbitmq",
    "sqs": "sqs",
    "sns": "sns",
    "s3": "s3",
    "lambda": "lambda",
    "function": "fonction",
    "cloud": "nuage",
    "azure": "azure",
    "gcp": "gcp",
    "aws": "aws"
}

def translate_filename(filename):
    """Traduire un nom de fichier en français"""
    # Séparer le nom et l'extension
    if filename.endswith('.json'):
        base_name = filename[:-5]
        extension = '.json'
    else:
        base_name = filename
        extension = ''
    
    # Convertir en minuscules pour la traduction
    translated = base_name.lower()
    
    # Remplacer les termes anglais par leurs équivalents français
    for eng, fr in sorted(FILENAME_TRANSLATIONS.items(), key=lambda x: len(x[0]), reverse=True):
        translated = translated.replace(eng, fr)
    
    # Nettoyer le nom (remplacer les caractères spéciaux)
    translated = re.sub(r'[^a-z0-9_-]', '_', translated)
    translated = re.sub(r'_+', '_', translated)  # Enlever les underscores multiples
    translated = translated.strip('_')  # Enlever les underscores au début et à la fin
    
    return translated + extension

def count_json_files(directory):
    """Compter le nombre de fichiers JSON dans un répertoire et ses sous-répertoires"""
    count = 0
    for root, _, files in os.walk(directory):
        for file in files:
            if file.endswith('.json') and not file.endswith('_FR.json'):
                count += 1
    return count

def add_audelalia_tag(workflow_data):
    """Ajouter le tag Audelalia à un workflow"""
    # S'assurer que la section tags existe
    if 'tags' not in workflow_data:
        workflow_data['tags'] = []
    
    # Vérifier si le tag Audelalia existe déjà
    has_audelalia = False
    for tag in workflow_data.get('tags', []):
        if isinstance(tag, dict) and tag.get('name') == 'Audelalia':
            has_audelalia = True
            break
    
    # Ajouter le tag s'il n'existe pas
    if not has_audelalia:
        # Trouver l'ID le plus élevé
        max_id = 0
        for tag in workflow_data.get('tags', []):
            if isinstance(tag, dict) and 'id' in tag:
                try:
                    tag_id = int(tag['id'])
                    max_id = max(max_id, tag_id)
                except:
                    pass
        
        # Ajouter le nouveau tag
        workflow_data['tags'].append({
            "id": str(max_id + 1),
            "name": "Audelalia"
        })
    
    return workflow_data

def translate_single_workflow(source_path, target_path):
    """Traduire un seul workflow"""
    try:
        # Lire le workflow
        with open(source_path, 'r', encoding='utf-8') as f:
            content = f.read()
            
        # Vérifier si c'est un JSON valide
        try:
            workflow_data = json.loads(content)
        except json.JSONDecodeError:
            return False, "JSON invalide"
        
        # Vérifier si c'est bien un workflow n8n
        if not isinstance(workflow_data, dict) or 'nodes' not in workflow_data:
            return False, "Pas un workflow n8n"
        
        # Traduire le workflow
        translated_workflow = translate_workflow(workflow_data)
        
        # Ajouter le tag Audelalia
        translated_workflow = add_audelalia_tag(translated_workflow)
        
        # Corriger les connexions
        if 'connections' in translated_workflow and 'nodes' in translated_workflow:
            # Créer un mapping ancien nom -> nouveau nom
            name_mapping = {}
            
            # D'abord récupérer le mapping depuis les nodes originaux et traduits
            original_nodes = workflow_data.get('nodes', [])
            translated_nodes = translated_workflow.get('nodes', [])
            
            for i, node in enumerate(original_nodes):
                if i < len(translated_nodes):
                    old_name = node.get('name', '')
                    new_name = translated_nodes[i].get('name', '')
                    if old_name and new_name and old_name != new_name:
                        name_mapping[old_name] = new_name
            
            # Appliquer le mapping aux connexions
            new_connections = {}
            for source_node, connections in translated_workflow['connections'].items():
                # Traduire le nom du node source
                new_source = name_mapping.get(source_node, source_node)
                new_connections[new_source] = connections
                
                # Traduire les noms des nodes de destination
                if isinstance(connections, dict):
                    for conn_type, conn_list in connections.items():
                        if isinstance(conn_list, list):
                            for i, conn_group in enumerate(conn_list):
                                if isinstance(conn_group, list):
                                    for j, conn in enumerate(conn_group):
                                        if isinstance(conn, dict) and 'node' in conn:
                                            old_target = conn['node']
                                            conn['node'] = name_mapping.get(old_target, old_target)
            
            translated_workflow['connections'] = new_connections
        
        # Créer le répertoire cible si nécessaire
        os.makedirs(os.path.dirname(target_path), exist_ok=True)
        
        # Sauvegarder le workflow traduit
        with open(target_path, 'w', encoding='utf-8') as f:
            json.dump(translated_workflow, f, ensure_ascii=False, indent=2)
        
        return True, "OK"
        
    except Exception as e:
        return False, str(e)

def translate_all_workflows(source_dir, target_base_dir):
    """Traduire tous les workflows d'un répertoire vers un autre"""
    
    # Créer le répertoire FR dans la destination
    target_dir = os.path.join(target_base_dir, "FR")
    os.makedirs(target_dir, exist_ok=True)
    
    # Statistiques
    total_files = count_json_files(source_dir)
    translated = 0
    errors = 0
    skipped = 0
    
    print(f"📊 Total de workflows à traduire: {total_files}")
    print(f"📁 Source: {source_dir}")
    print(f"📁 Destination: {target_dir}")
    print("=" * 60)
    
    # Parcourir tous les fichiers
    for root, dirs, files in os.walk(source_dir):
        for file in files:
            if file.endswith('.json') and not file.endswith('_FR.json'):
                source_path = os.path.join(root, file)
                
                # Calculer le chemin relatif
                rel_path = os.path.relpath(source_path, source_dir)
                rel_dir = os.path.dirname(rel_path)
                
                # Traduire le nom du fichier
                translated_filename = translate_filename(file)
                
                # Construire le chemin de destination
                if rel_dir and rel_dir != '.':
                    target_path = os.path.join(target_dir, rel_dir, translated_filename)
                else:
                    target_path = os.path.join(target_dir, translated_filename)
                
                # Traduire le workflow
                success, message = translate_single_workflow(source_path, target_path)
                
                if success:
                    translated += 1
                    # Afficher la progression
                    if translated % 10 == 0:
                        progress = (translated + errors + skipped) / total_files * 100
                        print(f"🔄 Progression: {progress:.1f}% ({translated} traduits, {errors} erreurs, {skipped} ignorés)")
                else:
                    if message in ["JSON invalide", "Pas un workflow n8n"]:
                        skipped += 1
                        print(f"⚠️  {message}: {rel_path}")
                    else:
                        errors += 1
                        print(f"❌ Erreur avec {rel_path}: {message}")
    
    # Rapport final
    print("\n" + "=" * 60)
    print("✅ TRADUCTION TERMINÉE!")
    print(f"📊 Statistiques finales:")
    print(f"   - Total de fichiers: {total_files}")
    print(f"   - Traduits avec succès: {translated}")
    print(f"   - Erreurs: {errors}")
    print(f"   - Ignorés: {skipped}")
    print(f"   - Répertoire de sortie: {target_dir}")
    
    return translated, errors, skipped

def main():
    # Répertoires par défaut
    source_dirs = [
        "/var/www/automatehub/200_automations_n8n",
        "/var/www/automatehub/github_workflows"
    ]
    
    # Demander confirmation
    print("🌐 TRADUCTION EN MASSE DES WORKFLOWS N8N")
    print("=" * 60)
    print("Ce script va:")
    print("  1. Traduire tous les workflows en français")
    print("  2. Les mettre dans un dossier 'FR'")
    print("  3. Traduire les noms de fichiers")
    print("  4. Ajouter le tag 'Audelalia' à tous les workflows")
    print("\nRépertoires sources:")
    
    for dir in source_dirs:
        if os.path.exists(dir):
            count = count_json_files(dir)
            print(f"  - {dir}: {count} workflows")
    
    print("\nLes workflows traduits seront sauvegardés dans:")
    print("  - /var/www/automatehub/workflows_traduits/FR/")
    
    response = input("\n⚠️  Voulez-vous continuer? (o/n): ")
    if response.lower() != 'o':
        print("Annulé.")
        return
    
    # Créer le répertoire de destination principal
    target_base = "/var/www/automatehub/workflows_traduits"
    os.makedirs(target_base, exist_ok=True)
    
    # Traiter chaque répertoire source
    total_translated = 0
    total_errors = 0
    total_skipped = 0
    
    start_time = datetime.now()
    
    for source_dir in source_dirs:
        if os.path.exists(source_dir):
            print(f"\n📂 Traitement de {source_dir}...")
            
            # Traduire les workflows
            translated, errors, skipped = translate_all_workflows(source_dir, target_base)
            
            total_translated += translated
            total_errors += errors
            total_skipped += skipped
    
    # Temps écoulé
    end_time = datetime.now()
    duration = end_time - start_time
    
    # Rapport global final
    print("\n" + "=" * 60)
    print("🎉 TRADUCTION GLOBALE TERMINÉE!")
    print(f"📊 Résumé global:")
    print(f"   - Workflows traduits: {total_translated}")
    print(f"   - Erreurs totales: {total_errors}")
    print(f"   - Fichiers ignorés: {total_skipped}")
    print(f"   - Temps écoulé: {duration}")
    print(f"   - Répertoire de sortie: {target_base}/FR/")
    print(f"\n✨ Tous les workflows ont le tag 'Audelalia' ajouté!")

if __name__ == "__main__":
    main()