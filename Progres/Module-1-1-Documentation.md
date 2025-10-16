# Module 1.1 - Introduction à l'automatisation

**Durée :** 20 minutes  
**Niveau :** Débutant  
**Workflow ID :** 5EzMaKRFXnkgLPOU  
**URL :** https://n8n.automatehub.fr/workflow/5EzMaKRFXnkgLPOU

## 🎯 Objectifs

- Comprendre les concepts de base de n8n
- Créer un workflow simple avec trigger, data et action
- Apprendre la logique conditionnelle
- Maîtriser l'envoi d'emails automatisés

## 📚 Concepts abordés

- **Cron Trigger** - Déclenchement automatique basé sur une planification
- **Set Node** - Manipulation et création de données JSON
- **If Node** - Logique conditionnelle et branchement
- **Gmail Node** - Action d'envoi d'emails automatisés

## 🔧 Instructions étape par étape

### 1. Créer le workflow de base
1. Ouvrir n8n : https://n8n.automatehub.fr
2. Créer un nouveau workflow
3. Renommer : "Module 1.1 - Introduction à l'automatisation"

### 2. Ajouter le trigger Cron
1. Rechercher "Cron" dans les nodes
2. Faire glisser le node Cron sur le canvas
3. Configurer :
   - **Mode** : Every Day
   - **Hour** : 9
   - **Minute** : 0
4. Renommer : "Démarrage quotidien"

### 3. Ajouter le node Set
1. Rechercher "Set" dans les nodes
2. Connecter au trigger Cron
3. Configurer les valeurs :
   - **message_bienvenue** : "Bienvenue dans n8n ! Ceci est votre première automation."
   - **plateforme** : "n8n"  
   - **niveau** : "débutant"
   - **etape** : 1 (nombre)
4. Renommer : "Définir les données"

### 4. Ajouter la condition If
1. Rechercher "If" dans les nodes
2. Connecter au node Set
3. Configurer :
   - **Value 1** : `{{$json.plateforme}}`
   - **Operation** : Equal
   - **Value 2** : "n8n"
4. Renommer : "Vérifier plateforme"

### 5. Ajouter l'email de succès
1. Rechercher "Gmail" dans les nodes
2. Connecter à la sortie "true" du node If
3. Configurer :
   - **Resource** : Message
   - **Operation** : Send
   - **To** : student@example.com
   - **Subject** : "Module 1.1 - Introduction à n8n"
   - **Message** : 
   ```
   Félicitations ! Vous avez créé votre premier workflow n8n.

   Ce workflow démontre :
   - Trigger automatique (Cron)
   - Manipulation de données (Set)
   - Logique conditionnelle (If)
   - Action finale (Email)

   Message : {{$json.message_bienvenue}}
   Étape : {{$json.etape}}
   Niveau : {{$json.niveau}}
   ```
4. Renommer : "Envoyer confirmation"

### 6. Ajouter la gestion d'erreur
1. Ajouter un node Set
2. Connecter à la sortie "false" du node If
3. Configurer :
   - **erreur** : "Plateforme non reconnue"
4. Renommer : "Gérer erreur"

### 7. Test et activation
1. Cliquer sur "Execute Workflow" pour tester
2. Vérifier les données dans chaque node
3. Activer avec le bouton "Active"

## 💡 Conseils

- Utilisez le mode debug pour voir les données à chaque étape
- Testez chaque node individuellement avec "Execute Node"
- Vérifiez vos credentials Gmail dans Settings > Credentials
- Consultez l'historique d'exécution dans l'onglet "Executions"
- Les expressions `{{$json.nom_variable}}` permettent d'accéder aux données

## 🏋️ Exercices pratiques

1. **Modifier l'heure** : Changez le trigger pour 14h au lieu de 9h
2. **Personnaliser le message** : Modifiez le message de bienvenue
3. **Ajouter Slack** : Remplacez l'email par une notification Slack
4. **Double condition** : Ajoutez une condition sur le niveau "débutant"
5. **Formatage date** : Ajoutez la date actuelle dans le message

## 🔍 Points techniques importants

### Expressions n8n
- `{{$json.variable}}` : Accès aux données JSON
- `{{$now}}` : Timestamp actuel
- `{{$today}}` : Date d'aujourd'hui
- `{{$json.array[0]}}` : Premier élément d'un tableau

### Gestion d'erreurs
- Toujours prévoir un branchement "false" pour les conditions
- Utiliser le node "Error Trigger" pour capturer les erreurs
- Logger les erreurs avec des nodes Set pour debug

### Best practices
- Nommer clairement chaque node
- Documenter les workflows complexes
- Tester manuellement avant activation
- Utiliser des variables d'environnement pour les credentials

## 🎬 Script vidéo (guide pour l'enregistrement)

**Introduction (0-2 min)**
- Bonjour et bienvenue dans le Module 1.1
- Aujourd'hui nous créons notre premier workflow
- Objectifs de la leçon

**Démonstration (2-15 min)**
- Créer le workflow étape par étape
- Expliquer chaque node et sa fonction
- Montrer les expressions et les données
- Tester le workflow

**Exercices (15-18 min)**
- Proposer les exercices pratiques
- Montrer une modification simple

**Conclusion (18-20 min)**
- Récapituler les concepts appris
- Annoncer le prochain module
- Encourager à pratiquer

## 📊 Métriques d'apprentissage

- **Temps moyen de réalisation** : 15-25 minutes
- **Taux de réussite attendu** : 95%
- **Concepts maîtrisés** : 4/4
- **Prérequis** : Aucun

## 🔗 Liens utiles

- [Documentation n8n Cron](https://docs.n8n.io/integrations/builtin/core-nodes/n8n-nodes-base.cron/)
- [Documentation n8n Set](https://docs.n8n.io/integrations/builtin/core-nodes/n8n-nodes-base.set/)
- [Documentation n8n If](https://docs.n8n.io/integrations/builtin/core-nodes/n8n-nodes-base.if/)
- [Documentation n8n Gmail](https://docs.n8n.io/integrations/builtin/app-nodes/n8n-nodes-base.gmail/)

---

**Prochaine leçon :** Module 1.2 - Votre premier workflow  
**Badge à obtenir :** n8n Rookie 🌱 (après quiz du Module 1)