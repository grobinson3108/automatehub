// Node Code: Fix Email Encoding
// À placer AVANT l'agent LangChain

function fixEncoding(text) {
  if (!text) return '';

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

    // Caractères spéciaux
    'â‚¬': '€', 'â€"': '—', 'â€"': '–',
    'â€¦': '…', 'â„¢': '™', 'Â°': '°',
    'Â«': '«', 'Â»': '»',

    // Espaces multiples
    '  ': ' ', '   ': ' ', '    ': ' '
  };

  let result = text;

  // Première passe : remplacements simples
  for (const [bad, good] of Object.entries(replacements)) {
    result = result.split(bad).join(good);
  }

  // Deuxième passe : nettoyer les espaces multiples restants
  result = result.replace(/\s{2,}/g, ' ');

  // Troisième passe : patterns complexes
  result = result.replace(/Ã©rÃ©/g, 'éré');
  result = result.replace(/transfÃ©rÃ©/g, 'transféré');
  result = result.replace(/certifiÃ©/g, 'certifié');

  return result;
}

// Récupérer les données d'entrée
const items = $input.all();
const fixedItems = [];

for (const item of items) {
  const data = item.json;

  // Corriger tous les champs texte
  const fixed = {
    id: data.id,
    messageId: data.messageId,
    from: fixEncoding(data.from),
    cleanedFromName: fixEncoding(data.cleanedFromName),
    cleanedFromEmail: data.cleanedFromEmail, // Email ne devrait pas avoir d'accents
    subject: fixEncoding(data.subject),
    body: fixEncoding(data.body),
    date: data.date,
    hasHtml: data.hasHtml,
    hasPlain: data.hasPlain
  };

  fixedItems.push({ json: fixed });
}

return fixedItems;
