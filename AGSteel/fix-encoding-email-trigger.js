// Node Code: Fix Email Encoding at Source
// À placer IMMÉDIATEMENT APRÈS l'Email Trigger

function forceUTF8(text) {
  if (!text) return '';

  // Cette fonction réinterprète les bytes ISO-8859-1 comme UTF-8
  try {
    // Si le texte contient Ã©, ça veut dire qu'il a été lu en ISO-8859-1
    // On doit reconvertir les bytes

    // Méthode 1 : Utiliser TextEncoder/TextDecoder
    const utf8Bytes = new TextEncoder().encode(text);
    const decoder = new TextDecoder('utf-8', { fatal: false });
    let result = decoder.decode(utf8Bytes);

    // Si ça n'a pas marché, on applique les corrections manuelles
    const replacements = {
      // Patterns UTF-8 mal interprétés en ISO-8859-1
      'Ã§': 'ç',
      'Ã©': 'é',
      'Ã¨': 'è',
      'Ãª': 'ê',
      'Ã ': 'à',
      'Ã´': 'ô',
      'Ã¹': 'ù',
      'Ã»': 'û',
      'Ã¯': 'ï',
      'Ã®': 'î',
      'Ã¢': 'â',

      // Majuscules
      'Ã‰': 'É',
      'Ã€': 'À',
      'Ãˆ': 'È',
      'ÃŠ': 'Ê',
      'Ã‡': 'Ç',
      'Ã"': 'Ô',
      'Ã™': 'Ù',
      'Ã›': 'Û',
      'ÃŽ': 'Î',
      'Ã': 'Ï',
      'Ã‚': 'Â',

      // Patterns composés (double encodage)
      'Ã©Ã§Ã ': 'éça',
      'transfÃ©rÃ©': 'transféré',
      'CertifiÃ©': 'Certifié',
    };

    for (const [bad, good] of Object.entries(replacements)) {
      result = result.split(bad).join(good);
    }

    return result;

  } catch (e) {
    // Fallback : corrections manuelles
    const replacements = {
      'Ã§': 'ç', 'Ã©': 'é', 'Ã¨': 'è', 'Ãª': 'ê',
      'Ã ': 'à', 'Ã´': 'ô', 'Ã¹': 'ù', 'Ã»': 'û',
      'Ã¯': 'ï', 'Ã®': 'î', 'Ã¢': 'â',
      'Ã‰': 'É', 'Ã€': 'À', 'Ãˆ': 'È', 'ÃŠ': 'Ê',
      'Ã‡': 'Ç', 'Ã"': 'Ô', 'Ã™': 'Ù', 'Ã›': 'Û',
      'ÃŽ': 'Î', 'Ã': 'Ï', 'Ã‚': 'Â',
    };

    let result = text;
    for (const [bad, good] of Object.entries(replacements)) {
      result = result.split(bad).join(good);
    }
    return result;
  }
}

const items = $input.all();
const fixedItems = [];

for (const item of items) {
  const data = item.json;

  // Corriger TOUS les champs texte à la source
  const fixed = {
    ...data,
    textHtml: data.textHtml ? forceUTF8(data.textHtml) : data.textHtml,
    textPlain: data.textPlain ? forceUTF8(data.textPlain) : data.textPlain,
    from: data.from ? forceUTF8(data.from) : data.from,
    subject: data.subject ? forceUTF8(data.subject) : data.subject,
  };

  fixedItems.push({ json: fixed });
}

return fixedItems;
