// Node Code: Fix Email Encoding v2
// À placer AVANT l'agent LangChain

function fixEncoding(text) {
  if (!text) return '';

  // Supprimer les null bytes
  text = text.replace(/\u0000/g, '');

  const replacements = {
    // Apostrophes et guillemets
    'â€™': "'", 'â€˜': "'", 'â€œ': '"', 'â€': '"',

    // Accents minuscules
    'Ã ': 'à', 'Ã©': 'é', 'Ã¨': 'è', 'Ãª': 'ê',
    'Ã§': 'ç', 'Ã´': 'ô', 'Ã¹': 'ù', 'Ã»': 'û',
    'Ã¯': 'ï', 'Ã®': 'î', 'Ã¢': 'â',

    // Accents majuscules
    'Ã€': 'À', 'Ã‰': 'É', 'Ãˆ': 'È', 'ÃŠ': 'Ê',
    'Ã‡': 'Ç', 'Ã"': 'Ô', 'Ã™': 'Ù', 'Ã›': 'Û',
    'ÃŽ': 'Î', 'Ã': 'Ï', 'Ã‚': 'Â',

    // Caractères spéciaux courants
    'â‚¬': '€', 'â€"': '—', 'â€"': '–',
    'â€¦': '…', 'â„¢': '™', 'Â°': '°',
    'Â«': '«', 'Â»': '»',

    // Patterns spécifiques trouvés dans ton email
    'transfÃ©rÃ©': 'transféré',
    'CertifiÃ©': 'Certifié',
    'Ã ': 'à',
    'Ã§a': 'ça',

    // Corrections pour les combinaisons mal encodées
    '\u00e9': 'é',  // é mal encodé
    '\u00e8': 'è',  // è mal encodé
    '\u00e0': 'à',  // à mal encodé
    '\u00e7': 'ç',  // ç mal encodé
  };

  let result = text;

  // Première passe : remplacements spécifiques
  for (const [bad, good] of Object.entries(replacements)) {
    result = result.split(bad).join(good);
  }

  // Deuxième passe : nettoyer caractères nulls restants
  result = result.replace(/\x00/g, '');

  // Troisième passe : nettoyer espaces multiples
  result = result.replace(/\s{2,}/g, ' ');

  // Quatrième passe : patterns regex pour les accents mal encodés
  result = result.replace(/Ã©/g, 'é');
  result = result.replace(/Ã¨/g, 'è');
  result = result.replace(/Ã /g, 'à');
  result = result.replace(/Ã§/g, 'ç');
  result = result.replace(/Ã´/g, 'ô');
  result = result.replace(/Ã¹/g, 'ù');
  result = result.replace(/Ã»/g, 'û');
  result = result.replace(/Ã¯/g, 'ï');
  result = result.replace(/Ã®/g, 'î');
  result = result.replace(/Ã¢/g, 'â');

  // Cinquième passe : accents majuscules
  result = result.replace(/Ã‰/g, 'É');
  result = result.replace(/Ã€/g, 'À');
  result = result.replace(/Ãˆ/g, 'È');
  result = result.replace(/Ã‡/g, 'Ç');

  return result;
}

// Récupérer TOUTES les données d'entrée
const items = $input.all();

// Si aucune donnée, retourner vide
if (!items || items.length === 0) {
  return [];
}

const fixedItems = [];

for (const item of items) {
  const data = item.json;

  // Créer un nouvel objet avec tous les champs corrigés
  const fixed = {
    id: data.id,
    messageId: data.messageId,
    from: fixEncoding(data.from || ''),
    cleanedFromName: fixEncoding(data.cleanedFromName || ''),
    cleanedFromEmail: data.cleanedFromEmail, // Email ne devrait pas avoir d'accents
    subject: fixEncoding(data.subject || ''),
    body: fixEncoding(data.body || ''),
    date: data.date,
    hasHtml: data.hasHtml,
    hasPlain: data.hasPlain
  };

  // Ajouter aux résultats
  fixedItems.push({ json: fixed });
}

// Retourner les données corrigées
return fixedItems;
