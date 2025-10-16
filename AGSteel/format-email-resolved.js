// Node Code n8n: Format Email depuis "Resolved"
// Configuraton Email Trigger IMAP : Download type = "Resolved"

const items = $input.all();
const emailData = [];

for (const item of items) {
  const data = item.json;

  // Générer ID unique
  const timestamp = Date.now().toString(36);
  const random = Math.random().toString(36).substring(2, 5);
  const shortId = `${timestamp}${random}`;

  // Récupérer les données depuis Resolved (déjà décodées !)
  const fromField = data.from?.text || data.from || '';
  const subject = data.subject || 'Sans objet';
  let body = data.text || '';  // Le text est déjà bien encodé !

  // 🐛 DEBUG : Afficher le body AVANT nettoyage
  console.log('=== BODY AVANT NETTOYAGE ===');
  console.log(body);
  console.log('=== LONGUEUR ===', body.length);

  // ⚠️ NETTOYAGE DU BODY

  // 1. Supprimer null bytes
  body = body.replace(/\u0000/g, '').replace(/\x00/g, '');

  // 2. Supprimer images (balises markdown, HTML, ou liens vers images)
  body = body.replace(/!\[.*?\]\(.*?\)/g, ''); // ![alt](url)
  body = body.replace(/<img[^>]*>/gi, ''); // <img src="...">
  body = body.replace(/\(https?:\/\/[^\s\)]+\.(jpg|jpeg|png|gif|webp|svg)[^\)]*\)/gi, '');

  // 3. Supprimer liens trackés Mailspring/GetMailspring
  body = body.replace(/\(https:\/\/link\.getmailspring\.com\/[^\)]+\)/g, '');

  // 4. Supprimer liens tel: (garder juste le texte avant)
  body = body.replace(/\s*\(tel:[^\)]+\)/g, '');

  // 5. Supprimer emojis et icônes (LISTE PRÉCISE pour éviter de manger des lettres)
  // Ne PAS utiliser de ranges Unicode larges !
  const emojiPattern = /[✉✅❌⚠️💡🔔📧📱📞☎☏📩📬📭📮📯📊📈📉💼🔗🌐🌍🌎🌏🔴🟢🟡⭐️🎯💪👍👎🙏💬📝✏️🖊️📌📍🗂️🗃️🏢🏠🏡🏢]/g;
  body = body.replace(emojiPattern, '');

  // 6. Supprimer lignes vides ou avec seulement des espaces
  body = body.replace(/\n\s*\n/g, '\n\n');

  // Supprimer lignes avec juste "f", "in", "Y" isolés (artefacts signatures)
  // Matcher aussi si suivis de sauts de ligne multiples
  body = body.replace(/\n(f|in|Y)\s*\n/g, '\n');

  // 7. Nettoyer retours chariot multiples (max 2 sauts de ligne)
  body = body.replace(/\n{3,}/g, '\n\n');

  // 8. Supprimer espaces en début/fin de lignes
  body = body.split('\n').map(line => line.trim()).join('\n');

  // 9. Supprimer retours chariot Windows (\r)
  body = body.replace(/\r/g, '');

  body = body.trim();

  // 🐛 DEBUG : Afficher le body APRÈS nettoyage
  console.log('=== BODY APRÈS NETTOYAGE ===');
  console.log(body);
  console.log('=== LONGUEUR ===', body.length);

  // Parser From: "Name <email>"
  let cleanedFromName = '';
  let cleanedFromEmail = '';

  if (fromField.includes('<') && fromField.includes('>')) {
    cleanedFromName = fromField.replace(/<.*>/, '').replace(/"/g, '').trim();
    cleanedFromEmail = fromField.match(/<(.+)>/)?.[1] || '';
  } else {
    cleanedFromEmail = fromField.trim();
    cleanedFromName = fromField.trim();
  }

  // Formater date
  let formattedDate = '';
  if (data.date) {
    try {
      const dateObj = typeof data.date === 'string' ? new Date(data.date) : data.date;
      formattedDate = dateObj.toLocaleString('fr-FR');
    } catch (e) {
      formattedDate = data.date.toString();
    }
  }

  // Construire objet final
  emailData.push({
    id: shortId,
    messageId: data.messageId || '',
    from: fromField,
    cleanedFromName: cleanedFromName,
    cleanedFromEmail: cleanedFromEmail,
    subject: subject,
    body: body,
    date: formattedDate,
    hasHtml: !!data.html,
    hasPlain: !!data.text
  });
}

return emailData;
