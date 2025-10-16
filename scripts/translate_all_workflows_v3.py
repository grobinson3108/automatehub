#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import json
import os
import sys
import re
import shutil
from datetime import datetime
from translate_workflow_complete import translate_workflow, translate_text_with_dict

def translate_filename(filename):
    """Traduire un nom de fichier selon les règles spécifiées"""
    # Séparer le nom et l'extension
    if filename.endswith('.json'):
        base_name = filename[:-5]
        extension = '.json'
    else:
        base_name = filename
        extension = ''
    
    # Convertir en minuscules
    name_parts = base_name.lower()
    
    # Enlever les parties inutiles
    # Retirer les mentions de nodes
    name_parts = re.sub(r'_\d+nodes?', '', name_parts)
    # Retirer complex/simple
    name_parts = re.sub(r'_(complex|simple)', '', name_parts)
    # Retirer les numéros à la fin (comme _1, _2, etc.)
    name_parts = re.sub(r'_\d+$', '', name_parts)
    
    # Dictionnaire de traduction pour les termes à traduire
    translations = {
        'automation': 'automatisation',
        'file': 'fichier',
        'files': 'fichiers',
        'management': 'gestion',
        'manager': 'gestionnaire',
        'customer': 'client',
        'customers': 'clients',
        'data': 'donnees',
        'cleanup': 'nettoyage',
        'clean': 'nettoyer',
        'transform': 'transformer',
        'transformation': 'transformation',
        'process': 'processus',
        'processing': 'traitement',
        'backup': 'sauvegarde',
        'export': 'export',
        'import': 'import',
        'generation': 'generation',
        'generate': 'generer',
        'invoice': 'facture',
        'invoices': 'factures',
        'report': 'rapport',
        'reports': 'rapports',
        'analysis': 'analyse',
        'analyze': 'analyser',
        'sync': 'sync',
        'synchronize': 'synchroniser',
        'synchronization': 'synchronisation',
        'upload': 'upload',
        'download': 'download',
        'send': 'envoyer',
        'receive': 'recevoir',
        'notification': 'notification',
        'alert': 'alerte',
        'monitor': 'surveiller',
        'monitoring': 'surveillance',
        'scheduled': 'planifie',
        'schedule': 'planification',
        'daily': 'quotidien',
        'weekly': 'hebdomadaire',
        'monthly': 'mensuel',
        'yearly': 'annuel',
        'lead': 'lead',
        'leads': 'leads',
        'campaign': 'campagne',
        'campaigns': 'campagnes',
        'marketing': 'marketing',
        'sales': 'ventes',
        'support': 'support',
        'ticket': 'ticket',
        'tickets': 'tickets',
        'order': 'commande',
        'orders': 'commandes',
        'product': 'produit',
        'products': 'produits',
        'inventory': 'inventaire',
        'stock': 'stock',
        'shipping': 'expedition',
        'delivery': 'livraison',
        'payment': 'paiement',
        'payments': 'paiements',
        'refund': 'remboursement',
        'discount': 'reduction',
        'coupon': 'coupon',
        'user': 'utilisateur',
        'users': 'utilisateurs',
        'account': 'compte',
        'accounts': 'comptes',
        'profile': 'profil',
        'profiles': 'profils',
        'settings': 'parametres',
        'configuration': 'configuration',
        'setup': 'configuration',
        'update': 'mise_a_jour',
        'create': 'creation',
        'delete': 'suppression',
        'remove': 'supprimer',
        'add': 'ajouter',
        'edit': 'modifier',
        'modify': 'modifier',
        'copy': 'copier',
        'duplicate': 'dupliquer',
        'merge': 'fusionner',
        'split': 'diviser',
        'filter': 'filtrer',
        'search': 'rechercher',
        'find': 'trouver',
        'replace': 'remplacer',
        'validate': 'valider',
        'validation': 'validation',
        'verify': 'verifier',
        'verification': 'verification',
        'authenticate': 'authentifier',
        'authentication': 'authentification',
        'authorize': 'autoriser',
        'authorization': 'autorisation',
        'encrypt': 'chiffrer',
        'encryption': 'chiffrement',
        'decrypt': 'dechiffrer',
        'decryption': 'dechiffrement',
        'compress': 'compresser',
        'compression': 'compression',
        'extract': 'extraire',
        'extraction': 'extraction',
        'convert': 'convertir',
        'conversion': 'conversion',
        'calculate': 'calculer',
        'calculation': 'calcul',
        'aggregate': 'agreger',
        'aggregation': 'agregation',
        'summarize': 'resumer',
        'summary': 'resume',
        'detail': 'detail',
        'details': 'details',
        'overview': 'apercu',
        'dashboard': 'tableau_bord',
        'chart': 'graphique',
        'charts': 'graphiques',
        'graph': 'graphique',
        'graphs': 'graphiques',
        'statistic': 'statistique',
        'statistics': 'statistiques',
        'metric': 'metrique',
        'metrics': 'metriques',
        'kpi': 'kpi',
        'performance': 'performance',
        'optimize': 'optimiser',
        'optimization': 'optimisation',
        'improve': 'ameliorer',
        'improvement': 'amelioration',
        'enhance': 'ameliorer',
        'enhancement': 'amelioration',
        'fix': 'corriger',
        'repair': 'reparer',
        'troubleshoot': 'depanner',
        'debug': 'debug',
        'test': 'test',
        'testing': 'test',
        'quality': 'qualite',
        'assurance': 'assurance',
        'control': 'controle',
        'review': 'revision',
        'approve': 'approuver',
        'approval': 'approbation',
        'reject': 'rejeter',
        'rejection': 'rejet',
        'pending': 'en_attente',
        'completed': 'termine',
        'failed': 'echoue',
        'success': 'reussi',
        'error': 'erreur',
        'warning': 'avertissement',
        'info': 'info',
        'log': 'journal',
        'logs': 'journaux',
        'audit': 'audit',
        'track': 'suivre',
        'tracking': 'suivi',
        'trace': 'trace',
        'history': 'historique',
        'archive': 'archiver',
        'archives': 'archives',
        'restore': 'restaurer',
        'recovery': 'recuperation',
        'disaster': 'catastrophe',
        'emergency': 'urgence',
        'critical': 'critique',
        'high': 'eleve',
        'medium': 'moyen',
        'low': 'faible',
        'priority': 'priorite',
        'urgent': 'urgent',
        'important': 'important',
        'task': 'tache',
        'tasks': 'taches',
        'job': 'job',
        'jobs': 'jobs',
        'queue': 'file_attente',
        'batch': 'lot',
        'bulk': 'masse',
        'single': 'unique',
        'multiple': 'multiple',
        'all': 'tous',
        'none': 'aucun',
        'some': 'certains',
        'other': 'autre',
        'others': 'autres',
        'new': 'nouveau',
        'old': 'ancien',
        'current': 'actuel',
        'previous': 'precedent',
        'next': 'suivant',
        'first': 'premier',
        'last': 'dernier',
        'start': 'debut',
        'end': 'fin',
        'begin': 'commencer',
        'finish': 'terminer',
        'complete': 'completer',
        'partial': 'partiel',
        'full': 'complet',
        'empty': 'vide',
        'null': 'nul',
        'default': 'defaut',
        'custom': 'personnalise',
        'template': 'modele',
        'example': 'exemple',
        'sample': 'echantillon',
        'demo': 'demo',
        'tutorial': 'tutoriel',
        'guide': 'guide',
        'manual': 'manuel',
        'documentation': 'documentation',
        'help': 'aide',
        'support': 'support',
        'faq': 'faq',
        'contact': 'contact',
        'message': 'message',
        'messages': 'messages',
        'chat': 'chat',
        'conversation': 'conversation',
        'thread': 'fil',
        'reply': 'repondre',
        'forward': 'transferer',
        'share': 'partager',
        'publish': 'publier',
        'unpublish': 'depublier',
        'draft': 'brouillon',
        'pending': 'en_attente',
        'approved': 'approuve',
        'rejected': 'rejete',
        'cancelled': 'annule',
        'suspended': 'suspendu',
        'active': 'actif',
        'inactive': 'inactif',
        'enabled': 'active',
        'disabled': 'desactive',
        'on': 'on',
        'off': 'off',
        'true': 'vrai',
        'false': 'faux',
        'yes': 'oui',
        'no': 'non',
        'open': 'ouvert',
        'closed': 'ferme',
        'public': 'public',
        'private': 'prive',
        'internal': 'interne',
        'external': 'externe',
        'local': 'local',
        'remote': 'distant',
        'online': 'en_ligne',
        'offline': 'hors_ligne',
        'connected': 'connecte',
        'disconnected': 'deconnecte',
        'available': 'disponible',
        'unavailable': 'indisponible',
        'busy': 'occupe',
        'free': 'libre',
        'locked': 'verrouille',
        'unlocked': 'deverrouille'
    }
    
    # Séparer les mots par underscore
    words = name_parts.split('_')
    translated_words = []
    
    # Traduire mot par mot
    for word in words:
        if word in translations:
            translated_words.append(translations[word])
        else:
            # Garder le mot original s'il n'est pas dans le dictionnaire
            translated_words.append(word)
    
    # Réorganiser les mots selon une logique plus française
    # Identifier les patterns courants et les réorganiser
    result_words = translated_words
    
    # Pattern: webhook/api/trigger + service + action
    # Exemple: webhook_gmail_trigger -> webhook_gmail_trigger (garde l'ordre)
    
    # Pattern: action + object + service
    # Exemple: backup_database_mysql -> backup_database_mysql (garde l'ordre)
    
    # Pattern: object + action + qualifier
    # Exemple: file_management_workflow -> workflow_gestion_fichiers
    if 'workflow' in result_words:
        # Mettre workflow au début
        result_words.remove('workflow')
        result_words.insert(0, 'workflow')
    
    if 'automatisation' in result_words:
        # Mettre automatisation au début
        result_words.remove('automatisation')
        result_words.insert(0, 'automatisation')
    
    # Joindre les mots avec des underscores
    result = '_'.join(result_words)
    
    # Nettoyer les underscores multiples
    result = re.sub(r'_+', '_', result)
    result = result.strip('_')
    
    return result + extension

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

def test_filename_translations():
    """Tester quelques traductions de noms de fichiers"""
    test_cases = [
        "webhook_gmail_trigger_complex_12nodes.json",
        "scheduled_email_automation.json",
        "file_management_workflow.json",
        "api_integration_complex_workflow.json",
        "database_sync_automation_20nodes.json",
        "google_drive_file_upload_workflow.json",
        "crm_lead_management_complex.json",
        "invoice_generation_pdf_export.json",
        "customer_onboarding_email_sequence.json",
        "data_transformation_cleanup_workflow.json"
    ]
    
    print("\n📋 Test de traduction des noms de fichiers:")
    print("=" * 80)
    for filename in test_cases:
        translated = translate_filename(filename)
        print(f"{filename}")
        print(f"  → {translated}")
        print()

def translate_all_workflows(source_dir, target_base_dir, limit=None):
    """Traduire tous les workflows d'un répertoire vers un autre"""
    
    # Créer le répertoire FR dans la destination
    target_dir = os.path.join(target_base_dir, "FR")
    os.makedirs(target_dir, exist_ok=True)
    
    # Statistiques
    total_files = count_json_files(source_dir)
    if limit:
        total_files = min(total_files, limit)
        
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
                if limit and (translated + errors + skipped) >= limit:
                    break
                    
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
                
                # Afficher le mapping
                if translated < 5:  # Afficher les 5 premiers pour vérification
                    print(f"\n📄 {file}")
                    print(f"   → {translated_filename}")
                
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
                        if skipped <= 5:
                            print(f"⚠️  {message}: {rel_path}")
                    else:
                        errors += 1
                        if errors <= 5:
                            print(f"❌ Erreur avec {rel_path}: {message}")
        
        if limit and (translated + errors + skipped) >= limit:
            break
    
    # Rapport final
    print("\n" + "=" * 60)
    print("✅ TRADUCTION TERMINÉE!")
    print(f"📊 Statistiques finales:")
    print(f"   - Total de fichiers traités: {translated + errors + skipped}")
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
    print("🌐 TRADUCTION EN MASSE DES WORKFLOWS N8N - VERSION 3")
    print("=" * 60)
    print("Ce script va:")
    print("  1. Traduire tous les workflows en français")
    print("  2. Les mettre dans un dossier 'FR'")
    print("  3. Traduire les noms de fichiers (version améliorée)")
    print("  4. Ajouter le tag 'Audelalia' à tous les workflows")
    
    # Tester quelques traductions de noms
    response = input("\n👀 Voulez-vous voir quelques exemples de traduction de noms? (o/n): ")
    if response.lower() == 'o':
        test_filename_translations()
    
    print("\nRépertoires sources:")
    
    for dir in source_dirs:
        if os.path.exists(dir):
            count = count_json_files(dir)
            print(f"  - {dir}: {count} workflows")
    
    print("\nLes workflows traduits seront sauvegardés dans:")
    print("  - /var/www/automatehub/workflows_traduits/FR/")
    
    # Demander si on fait un test ou tout
    print("\n🎯 Options:")
    print("  1. Tester sur 10 fichiers seulement")
    print("  2. Traduire TOUS les workflows")
    
    choice = input("\nVotre choix (1 ou 2): ")
    
    if choice == '1':
        limit = 10
        print(f"\n📋 Mode TEST: Traduction de {limit} fichiers seulement")
    elif choice == '2':
        limit = None
        print("\n⚠️  Mode COMPLET: Traduction de TOUS les workflows")
        confirm = input("Êtes-vous sûr? (o/n): ")
        if confirm.lower() != 'o':
            print("Annulé.")
            return
    else:
        print("Choix invalide. Annulé.")
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
            translated, errors, skipped = translate_all_workflows(source_dir, target_base, limit)
            
            total_translated += translated
            total_errors += errors
            total_skipped += skipped
            
            if limit and (total_translated + total_errors + total_skipped) >= limit:
                break
    
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
    
    if limit:
        print(f"\n📌 Mode TEST terminé. Pour traduire tous les workflows, relancez avec l'option 2.")

if __name__ == "__main__":
    main()