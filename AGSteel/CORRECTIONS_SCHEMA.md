# Corrections à apporter au workflow n8n

## Problème identifié

L'agent LangChain retourne cette structure :
```json
{
  "output": {
    "output": {
      "brouillon": "Le texte du mail..."
    }
  },
  "mailId": "mggto1et1tv"
}
```

Mais le Structured Output Parser attend :
```json
{
  "brouillon": "...",
  "mailId": "..."
}
```

## Solution : Modifier les 2 Structured Output Parsers

### 1. Structured Output Parser2 (Agent Pro - ligne 6)
### 2. Structured Output Parser (Agent Emrah - ligne 596)

**Remplacer le schéma actuel par :**

```json
{
  "type": "object",
  "properties": {
    "output": {
      "type": "object",
      "properties": {
        "output": {
          "type": "object",
          "properties": {
            "brouillon": {
              "type": "string",
              "description": "Le texte complet du brouillon email"
            }
          },
          "required": ["brouillon"]
        }
      },
      "required": ["output"]
    },
    "mailId": {
      "type": "string",
      "description": "L'ID du mail"
    }
  },
  "required": ["output", "mailId"]
}
```

## Étapes dans n8n

1. **Ouvrir le workflow dans n8n**
2. **Cliquer sur "Structured Output Parser2"** (pour l'agent Pro)
3. **Remplacer le contenu du champ "Input Schema" par le schéma ci-dessus**
4. **Cliquer sur "Structured Output Parser"** (pour l'agent Emrah)
5. **Remplacer le contenu du champ "Input Schema" par le même schéma**
6. **Modifier les nodes "Send Draft"** :
   - Au lieu de `$json.output.brouillon` → mettre `$json.output.output.brouillon`
   - Garder `$json.mailId` (déjà correct)
7. **Sauvegarder et tester**

## Alternative plus propre (si ça ne marche toujours pas)

Si le problème persiste, il faut **retirer complètement le Structured Output Parser** et utiliser un node "Code" pour extraire manuellement :

```javascript
// Dans un node "Code" après l'agent
const output = $input.first().json;

// Extraire les données selon la structure retournée
let brouillon, mailId;

if (output.output && output.output.output && output.output.output.brouillon) {
  brouillon = output.output.output.brouillon;
  mailId = output.mailId;
} else if (output.output && output.output.brouillon) {
  brouillon = output.output.brouillon;
  mailId = output.output.mailId || output.mailId;
} else if (output.brouillon) {
  brouillon = output.brouillon;
  mailId = output.mailId;
}

return {
  json: {
    output: {
      brouillon: brouillon,
      mailId: mailId
    }
  }
};
```

Cette approche est plus robuste car elle gère tous les formats possibles.
