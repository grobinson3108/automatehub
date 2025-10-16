# 📧 Intégration Email-Telegram avec n8n

## 🎯 Vue d'ensemble
Ce système permet de recevoir des emails sur Telegram avec un bouton "Répondre" fonctionnel qui :
1. Cache l'ID du mail dans le message Telegram
2. Force l'ouverture du clavier quand on clique sur "Répondre"
3. Traite la réponse avec l'IA pour créer un brouillon professionnel
4. Sauvegarde le brouillon directement dans le compte email via IMAP

## 🔧 Configuration des nodes n8n

### 1. Email Trigger (IMAP)
Configure ton node Email Trigger avec tes identifiants IMAP.

### 2. Telegram - Envoi du message initial
```javascript
// Dans le champ "Text" du node Telegram
📧 <b>Nouveau mail</b>

<b>De:</b> {{ $json["from"]["text"] }}
<b>Sujet:</b> {{ $json["subject"] }}

<b>Message:</b>
{{ $json["text"] }}

<!-- {{ $json["uid"] }} -->
```

**Important:** L'ID du mail est caché dans un commentaire HTML !

### 3. Telegram - Inline Keyboard
```json
[
  [
    {
      "text": "📝 Répondre",
      "callback_data": "reply_{{ $json.uid }}"
    }
  ]
]
```

### 4. Telegram Trigger - Callback
Configure pour recevoir les callbacks Telegram.

### 5. Code - Extraire l'ID du mail
```javascript
// Extraire l'ID du callback_data
const callbackData = $json.callback_query.data || '';
const mailId = callbackData.replace('reply_', '');

// Extraire l'ID caché du message original
const messageText = $json.callback_query.message.text || '';
const hiddenIdMatch = messageText.match(/<!-- (\d+) -->/);
const hiddenMailId = hiddenIdMatch ? hiddenIdMatch[1] : null;

return [{
  json: {
    mailId: mailId || hiddenMailId,
    chatId: $json.callback_query.message.chat.id,
    messageId: $json.callback_query.message.message_id,
    originalMessage: messageText
  }
}];
```

### 6. Telegram - Answer Callback
Pour confirmer la réception du clic.

### 7. Telegram - Force Reply
```javascript
// Text
Tapez votre réponse pour l'email #{{ $json.mailId }}:

// Reply Markup
{
  "force_reply": true,
  "input_field_placeholder": "Votre réponse..."
}
```

### 8. Google Sheets - Stockage
Stocke l'ID du mail et le chat ID pour la récupération ultérieure.

### 9. Telegram Trigger - Réception des réponses
Configure pour recevoir les messages normaux.

### 10. Code - Vérifier si c'est une réponse
```javascript
const replyTo = $json.reply_to_message;
if (!replyTo || !replyTo.text.includes('Tapez votre réponse')) {
  return [];
}

const mailIdMatch = replyTo.text.match(/#(\d+)/);
const mailId = mailIdMatch ? mailIdMatch[1] : null;

return [{
  json: {
    mailId,
    chatId: $json.chat.id,
    userResponse: $json.text
  }
}];
```

### 11. Google Sheets - Lookup
Récupère les données de l'email original basé sur l'ID.

### 12. Agent IA - Création du brouillon
```
Tu es un assistant professionnel qui aide à rédiger des réponses aux emails.

Email original:
De: {{ $node["Lookup for Reply"].json["Nom"] }}
Sujet: {{ $node["Lookup for Reply"].json["Email Reçu"].split('\n')[0] }}
Message: {{ $node["Lookup for Reply"].json["Email Reçu"] }}

Réponse de l'utilisateur: {{ $json.userResponse }}

Crée une réponse professionnelle en français basée sur la réponse de l'utilisateur.
- Si la réponse est très courte (OK, oui, non, etc.), développe de manière appropriée
- Garde un ton professionnel mais chaleureux
- Structure bien la réponse avec une formule de politesse appropriée
- N'invente pas d'informations non fournies par l'utilisateur

Retourne UNIQUEMENT le corps de l'email (sans l'objet).
```

### 13. HTTP Request - Sauvegarder le brouillon
```json
{
  "method": "POST",
  "url": "https://automatehub.fr/api/save-draft-alt.php",
  "headers": {
    "Content-Type": "application/json"
  },
  "body": {
    "environment": "test",
    "to": "{{ $node[\"Lookup for Reply\"].json.Nom }}",
    "originalSubject": "{{ $node[\"Lookup for Reply\"].json[\"Email Reçu\"].split('\\n')[0] }}",
    "body": "{{ $json.brouillon }}"
  }
}
```

### 14. Telegram - Confirmation
Envoie un message confirmant que le brouillon a été sauvegardé.

## 🔐 Configuration de l'API

### Fichier: `/var/www/automatehub/public/api/save-draft-alt.php`

Configurations disponibles :
- **test** : IONOS (greg@audelalia.fr)
- **production** : OVH/AG Steel Trading (à configurer avec le mot de passe)

Pour passer en production, modifie le mot de passe dans le fichier PHP :
```php
'password' => 'votre_mot_de_passe_production', // À remplacer
```

## 📝 Notes importantes

1. **ID caché** : L'ID du mail est caché dans un commentaire HTML `<!-- 123 -->` qui n'est pas visible dans Telegram mais reste dans les données du message.

2. **Force Reply** : Quand l'utilisateur clique sur "Répondre", Telegram force l'ouverture du clavier avec un placeholder.

3. **IMAP** : Les brouillons sont sauvegardés directement dans le dossier "Brouillons" (IONOS) ou "Drafts" (OVH).

4. **Environnement** : Change `"environment": "test"` en `"environment": "production"` dans le node HTTP Request pour utiliser AG Steel.

## 🚀 Prochaines étapes

1. Ajouter un bouton "Envoyer" en plus de "Sauvegarder comme brouillon"
2. Permettre la modification des brouillons
3. Ajouter des pièces jointes
4. Gérer les emails HTML avec conversion en texte brut