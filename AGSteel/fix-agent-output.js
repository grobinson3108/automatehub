// Node Code: Fix Agent Output
// À placer APRÈS l'agent LangChain "Envoyer Mails"

function fixEncoding(text) {
  if (!text) return '';

  // Supprimer les null bytes
  text = text.replace(/\u0000/g, '');
  text = text.replace(/\x00/g, '');

  const replacements = {
    // Apostrophes et guillemets
    'â€™': "'", 'â€˜': "'", 'â€œ': '"', 'â€': '"',

    // Accents minuscules - patterns complets
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

    // Patterns spécifiques trouvés
    'transfÃ©rÃ©': 'transféré',
    'TransfÃ©rÃ©': 'Transféré',
    'CertifiÃ©': 'Certifié',
    'certifiÃ©': 'certifié',
    'Ã§a': 'ça',
    'Ã§Ã ': 'ça',
  };

  let result = text;

  // Première passe : remplacements directs
  for (const [bad, good] of Object.entries(replacements)) {
    result = result.split(bad).join(good);
  }

  // Deuxième passe : patterns regex pour les accents résiduels
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

  // Troisième passe : majuscules
  result = result.replace(/Ã‰/g, 'É');
  result = result.replace(/Ã€/g, 'À');
  result = result.replace(/Ãˆ/g, 'È');
  result = result.replace(/Ã‡/g, 'Ç');

  // Quatrième passe : nettoyer espaces multiples incluant null bytes
  result = result.replace(/[\s\u0000]+/g, ' ');
  result = result.replace(/\s{2,}/g, ' ');

  return result.trim();
}

// Récupérer l'output de l'agent
const items = $input.all();

if (!items || items.length === 0) {
  return [];
}

const fixedItems = [];

for (const item of items) {
  const data = item.json;

  // Vérifier la structure de l'output
  let output = data.output || data;

  // Corriger tous les champs texte dans l'output
  const fixedOutput = {
    ID: output.ID,
    mail: fixEncoding(output.mail || ''),
    message: fixEncoding(output.message || '')
  };

  // Reconstruire l'objet complet
  const fixed = {
    ...data,
    output: fixedOutput
  };

  fixedItems.push({ json: fixed });
}

return fixedItems;
