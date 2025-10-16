# 🐛 Fix Memory Bug - Triple Message

## 🚨 Problème identifié

L'agent Spontané envoie **3 messages identiques** à cause de la **Simple Memory** qui stocke l'historique de conversation.

### Analyse des logs

Dans le log de la mémoire, on voit :
- **12 appels LLM** pour 1 seul message user
- **6 appels mémoire** (saveContext)
- **3 appels à chaque tool** (FindContacts, AllContacts, CreateBrouillons)

### Historique dans la mémoire (dernier état)

```json
{
  "chat_history": [
    {"content": "/start"},
    {"content": "question"},
    {"content": "/start"},
    {"content": "question"},
    {"content": "/start"},
    {"content": "question"},
    {"content": "Greg Robinson 11h"},
    {"content": "brouillon 1"},
    {"content": "Greg Robinson 11h"},
    {"content": "brouillon 2"},
    {"content": "Greg Robinson 11h"},
    {"content": "brouillon 3"},
    {"content": "Greg Robinson 20 min"},
    {"content": "brouillon 4"},
    {"content": "Greg Robinson 20 min"},
    {"content": "brouillon 5"},
    {"content": "Greg Robinson 20 min"},
    {"content": "brouillon 6"}
  ]
}
```

### Ce qui se passe :

1. **Message 1 : "/start"**
   - Agent génère une question
   - Mémoire sauvegarde : H: "/start" + AI: "question"

2. **Message 2 : "/start"** (répété par erreur quelque part)
   - Agent voit l'historique (H1, AI1)
   - Agent traite H1 à nouveau → génère "question" #2
   - Agent traite le nouveau message → génère "question" #3
   - Mémoire sauvegarde 2 nouvelles entrées

3. **Message 3 : "Greg Robinson 11h"**
   - Agent voit l'historique (3x /start, 3x questions)
   - Agent traite chaque entrée de l'historique comme nouvelle demande
   - Génère 3 brouillons identiques

4. **Message 4 : "Greg Robinson 20 min"**
   - Même problème amplifié
   - Historique contient maintenant 9 entrées
   - Génère encore 3 brouillons

---

## ✅ Solution 1 : Supprimer la mémoire (RECOMMANDÉ)

### Pourquoi la mémoire n'est PAS nécessaire pour l'agent Spontané

La **Simple Memory (BufferWindow)** est conçue pour des **conversations continues** où l'agent a besoin du contexte des messages précédents.

**Cas d'usage de la mémoire :**
- Chatbot conversationnel : "Comment ça va ?" → "Bien, et toi ?" → "Super !"
- Assistant avec suivi : "Crée un rapport" → "Ajoute une section" → "Modifie le titre"

**Agent Spontané :**
- Chaque demande est **indépendante**
- "Crée un mail pour Greg" → Brouillon créé → **FIN**
- Pas de suivi, pas de contexte nécessaire

### Actions à effectuer

1. **Déconnecter le node "Simple Memory"** de l'agent AI Agent1
2. **Ne pas supprimer le node** (au cas où tu veux le réutiliser plus tard)
3. **Tester à nouveau**

---

## ✅ Solution 2 : Vider la mémoire au début de chaque appel

Si tu veux absolument garder la mémoire pour une raison future, ajoute un **node Code avant l'agent** qui vide la mémoire :

```javascript
// Node Code: Clear Memory
const chatId = $('Telegram').item.json.message.chat.id;

// Appeler un endpoint pour vider la mémoire (si disponible)
// Ou simplement ne rien faire et laisser passer

return {
  json: $input.item.json
};
```

**Problème :** n8n ne fournit pas d'API pour vider la mémoire directement depuis un node Code. La seule solution propre est de **déconnecter la mémoire**.

---

## ✅ Solution 3 : Limiter la fenêtre de mémoire à 0

Si le node Memory a un paramètre **Window Size**, mets-le à **0** pour désactiver l'historique.

Configuration actuelle (probablement) :
```json
{
  "sessionIdType": "customKey",
  "sessionKey": "={{ $('Telegram').item.json.message.chat.id }}",
  "windowSize": 5  // <-- Problème ici
}
```

Configuration corrigée :
```json
{
  "sessionIdType": "customKey",
  "sessionKey": "={{ $('Telegram').item.json.message.chat.id }}",
  "windowSize": 0  // <-- Désactive l'historique
}
```

---

## 🎯 Recommandation finale

**Supprimer complètement la mémoire de l'agent Spontané.**

### Architecture correcte :

```
Telegram Trigger
  ↓
Code2 (parse message)
  ↓
Get row(s) in sheet3
  ↓
Get Contact
  ↓
Switch5 (Modif/Spontané)
  ↓
AI Agent1 (Spontané)
  ├─ OpenAI Chat Model1 ✅
  ├─ FindContacts ✅
  ├─ AllContacts ✅
  ├─ CreateBrouillons ✅
  ├─ Structured Output Parser1 ✅
  └─ Simple Memory ❌ DÉCONNECTER
```

### Pourquoi ça fonctionnait "avant" ?

Probablement que :
1. La mémoire était vide au début
2. Le premier test a fonctionné
3. Les tests suivants ont commencé à accumuler l'historique
4. Bug apparu progressivement

---

## 📋 Checklist de correction

### Étape 1 : Déconnecter la mémoire
- [ ] Ouvrir le workflow dans n8n
- [ ] Sélectionner le node "AI Agent1" (Spontané)
- [ ] Supprimer la connexion entre "Simple Memory" et "AI Agent1"
- [ ] Sauvegarder

### Étape 2 : Tester
- [ ] Envoyer un message test : "Crée un mail pour Greg Robinson"
- [ ] Vérifier que tu reçois **1 seul message** Telegram
- [ ] Vérifier dans les logs :
  - [ ] 1 seul appel LLM
  - [ ] 1 seul appel FindContacts
  - [ ] 1 seul appel CreateBrouillons
  - [ ] 0 appel mémoire

### Étape 3 : Vérifier le Sheet
- [ ] Ouvrir le Sheet AGSteel
- [ ] Vérifier qu'il y a **1 seule nouvelle ligne** avec le brouillon
- [ ] Pas de doublons

---

## 🔍 Pour débugger si le problème persiste

Si après avoir déconnecté la mémoire tu as encore des triples messages, vérifie :

1. **Le Telegram Trigger** : Est-il configuré pour écouter plusieurs types d'events ?
   ```json
   {
     "updates": ["message", "callback_query"]
   }
   ```
   Si oui, peut-être qu'il déclenche 3 fois pour un même message.

2. **Le Code2** : Est-ce qu'il retourne un tableau avec 3 éléments ?
   ```javascript
   return [
     { json: data1 },
     { json: data2 },
     { json: data3 }
   ];
   ```
   Si oui, chaque élément déclenchera l'agent.

3. **Les connexions** : Est-ce que plusieurs nodes envoient des données à l'agent ?
   ```
   Node A → AI Agent1
   Node B → AI Agent1
   Node C → AI Agent1
   ```

---

## 💡 Note sur l'agent Réponse (AI Agent)

L'**agent Réponse** (celui qui gère ChatGPT/Emrah) n'a **pas de mémoire** connectée, et c'est normal !

Il reçoit toutes les données nécessaires dans le prompt :
- Email reçu
- Mail préparé
- Instructions user
- Iterations

Il n'a **pas besoin** de mémoire pour fonctionner correctement.

---

**Conclusion : Déconnecte la mémoire, et le problème sera résolu ! 🚀**
