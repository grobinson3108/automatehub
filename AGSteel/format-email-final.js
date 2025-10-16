// Node Code: Format Email - Décodage Quoted-Printable
// À placer IMMÉDIATEMENT APRÈS l'Email Trigger (méthode 3 - raw)

function decodeQuotedPrintable(text) {
  if (!text) return '';

  // Décoder quoted-printable (=C3=A7 → ç, =21 → !)
  let decoded = text.replace(/=([0-9A-F]{2})/gi, (match, hex) => {
    return String.fromCharCode(parseInt(hex, 16));
  });

  // Supprimer les soft line breaks (=\r\n ou =\n)
  decoded = decoded.replace(/=\r?\n/g, '');

  // Nettoyer les retours chariot multiples
  decoded = decoded.replace(/\r\n/g, '\n');
  decoded = decoded.replace(/\n{3,}/g, '\n\n');

  return decoded;
}

function extractTextFromRaw(raw) {
  if (!raw) return { plain: '', html: '' };

  // Extraire le text/plain (entre Content-Type: text/plain et le boundary)
  const plainMatch = raw.match(/Content-Type: text\/plain[\s\S]*?\n\n([\s\S]*?)(?:\r?\n--[\w_-]+)/);
  const plain = plainMatch ? decodeQuotedPrintable(plainMatch[1]) : '';

  // Extraire le text/html (entre Content-Type: text/html et le boundary)
  const htmlMatch = raw.match(/Content-Type: text\/html[\s\S]*?\n\n([\s\S]*?)(?:\r?\n--[\w_-]+)/);
  const html = htmlMatch ? decodeQuotedPrintable(htmlMatch[1]) : '';

  return { plain, html };
}

function cleanHtml(html) {
  if (!html) return '';

  return html
    // Supprimer les balises HTML
    .replace(/<[^>]*>/g, '')

    // Décoder les entités HTML
    .replace(/&nbsp;/g, ' ')
    .replace(/&amp;/g, '&')
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&quot;/g, '"')
    .replace(/&#39;/g, "'")
    .replace(/&agrave;/g, 'à')
    .replace(/&aacute;/g, 'á')
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
    .replace(/&Aacute;/g, 'Á')
    .replace(/&Eacute;/g, 'É')
    .replace(/&Egrave;/g, 'È')
    .replace(/&Ecirc;/g, 'Ê')
    .replace(/&Icirc;/g, 'Î')
    .replace(/&Ocirc;/g, 'Ô')
    .replace(/&Ugrave;/g, 'Ù')
    .replace(/&Ccedil;/g, 'Ç')

    // Nettoyer les espaces multiples
    .replace(/\s+/g, ' ')
    .trim();
}

function extractHeaderValue(raw, headerName) {
  // Extraire un header du raw (ex: "From: ", "Subject: ")
  const regex = new RegExp(`^${headerName}: (.+)$`, 'm');
  const match = raw.match(regex);
  return match ? match[1].trim() : '';
}

// Traitement des items
const items = $input.all();
const processedItems = [];

for (const item of items) {
  const data = item.json;

  // Générer un ID unique pour chaque email
  const timestamp = Date.now().toString(36);
  const random = Math.random().toString(36).substring(2, 5);
  const shortId = `${timestamp}${random}`;

  // Extraire et décoder le contenu depuis raw
  const { plain, html } = extractTextFromRaw(data.raw);

  // Utiliser plain text si disponible, sinon HTML nettoyé
  const body = plain || cleanHtml(html);

  // Extraire les headers depuis raw
  const fromField = extractHeaderValue(data.raw, 'From');
  const subjectRaw = extractHeaderValue(data.raw, 'Subject');
  const dateRaw = extractHeaderValue(data.raw, 'Date');

  // Décoder le sujet (peut aussi être en quoted-printable)
  const subject = subjectRaw ? decodeQuotedPrintable(subjectRaw) : 'Sans objet';

  // Parser le champ From: "Name <email@domain.com>"
  let cleanedFromName = '';
  let cleanedFromEmail = '';

  if (fromField.includes('<') && fromField.includes('>')) {
    // Format: "Greg Robinson <greg@audelalia.fr>"
    cleanedFromName = fromField.replace(/<.*>/, '').replace(/"/g, '').trim();
    cleanedFromEmail = fromField.match(/<(.+)>/)?.[1] || '';
  } else {
    // Format: "greg@audelalia.fr"
    cleanedFromEmail = fromField.trim();
    cleanedFromName = fromField.trim();
  }

  // Formater la date
  let formattedDate = '';
  if (dateRaw) {
    try {
      formattedDate = new Date(dateRaw).toLocaleString('fr-FR');
    } catch (e) {
      formattedDate = dateRaw; // Fallback si parsing échoue
    }
  }

  // Construire l'objet final
  const processed = {
    id: shortId,
    messageId: data.messageId || '',
    from: fromField,
    cleanedFromName: cleanedFromName,
    cleanedFromEmail: cleanedFromEmail,
    subject: subject,
    body: body,
    date: formattedDate,
    hasHtml: !!html,
    hasPlain: !!plain,
  };

  processedItems.push({ json: processed });
}

return processedItems;
