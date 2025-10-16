// Node Code: Decode Quoted-Printable Email
// À placer IMMÉDIATEMENT APRÈS l'Email Trigger (méthode 3 - raw)

function decodeQuotedPrintable(text) {
  if (!text) return '';

  // Décoder quoted-printable (=C3=A7 → ç)
  let decoded = text.replace(/=([0-9A-F]{2})/gi, (match, hex) => {
    return String.fromCharCode(parseInt(hex, 16));
  });

  // Supprimer les soft line breaks (=\r\n)
  decoded = decoded.replace(/=\r?\n/g, '');

  // Nettoyer les retours chariot multiples
  decoded = decoded.replace(/\r\n/g, '\n');
  decoded = decoded.replace(/\n{3,}/g, '\n\n');

  return decoded;
}

function extractTextFromRaw(raw) {
  if (!raw) return { plain: '', html: '' };

  // Extraire le text/plain
  const plainMatch = raw.match(/Content-Type: text\/plain[\s\S]*?\n\n([\s\S]*?)(?=\n--68e56)/);
  const plain = plainMatch ? decodeQuotedPrintable(plainMatch[1]) : '';

  // Extraire le text/html
  const htmlMatch = raw.match(/Content-Type: text\/html[\s\S]*?\n\n([\s\S]*?)(?=\n--68e56)/);
  const html = htmlMatch ? decodeQuotedPrintable(htmlMatch[1]) : '';

  return { plain, html };
}

function cleanHtml(html) {
  if (!html) return '';

  return html
    .replace(/<[^>]*>/g, '') // Supprimer balises HTML
    .replace(/&nbsp;/g, ' ')
    .replace(/&amp;/g, '&')
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&quot;/g, '"')
    .replace(/&#39;/g, "'")
    .replace(/&agrave;/g, 'à')
    .replace(/&eacute;/g, 'é')
    .replace(/&egrave;/g, 'è')
    .replace(/&ecirc;/g, 'ê')
    .replace(/&ccedil;/g, 'ç')
    .replace(/\s+/g, ' ')
    .trim();
}

const items = $input.all();
const processedItems = [];

for (const item of items) {
  const data = item.json;

  // Générer ID unique
  const timestamp = Date.now().toString(36);
  const random = Math.random().toString(36).substring(2, 5);
  const shortId = `${timestamp}${random}`;

  // Extraire et décoder le contenu
  const { plain, html } = extractTextFromRaw(data.raw);

  // Utiliser plain si disponible, sinon html nettoyé
  const body = plain || cleanHtml(html);

  // Extraire from/to depuis les headers
  const fromMatch = data.raw.match(/^From: (.+)$/m);
  const toMatch = data.raw.match(/^To: (.+)$/m);
  const subjectMatch = data.raw.match(/^Subject: (.+)$/m);
  const dateMatch = data.raw.match(/^Date: (.+)$/m);

  const fromField = fromMatch ? fromMatch[1].trim() : '';

  // Parser "Name <email@domain.com>"
  let cleanedFromName = '';
  let cleanedFromEmail = '';

  if (fromField.includes('<') && fromField.includes('>')) {
    cleanedFromName = fromField.replace(/<.*>/, '').replace(/"/g, '').trim();
    cleanedFromEmail = fromField.match(/<(.+)>/)?.[1] || '';
  } else {
    cleanedFromEmail = fromField.trim();
    cleanedFromName = fromField.trim();
  }

  const processed = {
    id: shortId,
    messageId: data.messageId || '',
    from: fromField,
    cleanedFromName: cleanedFromName,
    cleanedFromEmail: cleanedFromEmail,
    subject: subjectMatch ? decodeQuotedPrintable(subjectMatch[1]) : 'Sans objet',
    body: body,
    date: dateMatch ? new Date(dateMatch[1]).toLocaleString('fr-FR') : '',
    hasHtml: !!html,
    hasPlain: !!plain,
  };

  processedItems.push({ json: processed });
}

return processedItems;
