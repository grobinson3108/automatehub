// Node Code n8n: Format Email avec décodage Quoted-Printable
// À placer APRÈS l'Email Trigger (qui retourne le champ "raw")

function decodeQuotedPrintable(text) {
  if (!text) return '';

  // Décoder =C3=A7 → ç, =21 → !, etc.
  let decoded = text.replace(/=([0-9A-F]{2})/gi, (match, hex) => {
    return String.fromCharCode(parseInt(hex, 16));
  });

  // Supprimer soft line breaks (= en fin de ligne)
  decoded = decoded.replace(/=\r?\n/g, '');

  // Normaliser les retours à la ligne
  decoded = decoded.replace(/\r\n/g, '\n');
  decoded = decoded.replace(/\n{3,}/g, '\n\n');

  return decoded;
}

function extractTextFromRaw(raw) {
  if (!raw) return { plain: '', html: '' };

  // Extraire text/plain
  const plainMatch = raw.match(/Content-Type: text\/plain[\s\S]*?\n\n([\s\S]*?)(?:\r?\n--[\w_-]+)/);
  const plain = plainMatch ? decodeQuotedPrintable(plainMatch[1]) : '';

  // Extraire text/html
  const htmlMatch = raw.match(/Content-Type: text\/html[\s\S]*?\n\n([\s\S]*?)(?:\r?\n--[\w_-]+)/);
  const html = htmlMatch ? decodeQuotedPrintable(htmlMatch[1]) : '';

  return { plain, html };
}

function cleanHtml(html) {
  if (!html) return '';

  return html
    .replace(/<[^>]*>/g, '')
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
    .replace(/&euml;/g, 'ë')
    .replace(/&iuml;/g, 'ï')
    .replace(/&ocirc;/g, 'ô')
    .replace(/&ucirc;/g, 'û')
    .replace(/&ugrave;/g, 'ù')
    .replace(/&ccedil;/g, 'ç')
    .replace(/&Agrave;/g, 'À')
    .replace(/&Eacute;/g, 'É')
    .replace(/&Egrave;/g, 'È')
    .replace(/&Ecirc;/g, 'Ê')
    .replace(/&Icirc;/g, 'Î')
    .replace(/&Ocirc;/g, 'Ô')
    .replace(/&Ugrave;/g, 'Ù')
    .replace(/&Ccedil;/g, 'Ç')
    .replace(/\s+/g, ' ')
    .trim();
}

function extractHeader(raw, headerName) {
  const regex = new RegExp(`^${headerName}: (.+)$`, 'm');
  const match = raw.match(regex);
  return match ? match[1].trim() : '';
}

// === TRAITEMENT PRINCIPAL ===

const items = $input.all();
const emailData = [];

for (const item of items) {
  const data = item.json;

  // Générer ID unique
  const timestamp = Date.now().toString(36);
  const random = Math.random().toString(36).substring(2, 5);
  const shortId = `${timestamp}${random}`;

  // Extraire contenu depuis raw
  const { plain, html } = extractTextFromRaw(data.raw);
  const body = plain || cleanHtml(html);

  // Extraire headers depuis data.headers (pas data.raw)
  const fromField = data.headers?.from?.replace('From: ', '') ||
                    data.from?.text || '';
  const subjectRaw = data.headers?.subject?.replace('Subject: ', '') ||
                     data.subject || '';
  const dateRaw = data.headers?.date?.replace('Date: ', '') ||
                  data.date || '';

  // Décoder le sujet
  const subject = subjectRaw ? decodeQuotedPrintable(subjectRaw) : 'Sans objet';

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
  if (dateRaw) {
    try {
      formattedDate = new Date(dateRaw).toLocaleString('fr-FR');
    } catch (e) {
      formattedDate = dateRaw;
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
    hasHtml: !!html,
    hasPlain: !!plain
  });
}

return emailData;
