// Node Code: Extract Body for Modification
// À placer AVANT l'agent si Iterations ≥ 1

const items = $input.all();
const result = [];

for (const item of items) {
  const data = item.json;

  // Si Iterations = 0 ou vide, passer directement (pas de modification)
  if (!data.Iterations || data.Iterations === 0 || data.Iterations === '0') {
    result.push(item);
    continue;
  }

  // Extraire le brouillon existant
  const brouillon = data['Email Préparé'] || data['Mail préparé'] || '';

  if (!brouillon) {
    result.push(item);
    continue;
  }

  // Découper le brouillon en 4 parties
  const lines = brouillon.split('\n');

  // Partie 1 : Formule d'ouverture (jusqu'à première ligne vide)
  let partie1End = 0;
  for (let i = 0; i < lines.length; i++) {
    if (lines[i].trim() === '') {
      partie1End = i + 1;
      break;
    }
  }

  // Partie 3 : Signature (commence par "Salutations," ou "Bien cordialement,")
  let partie3Start = -1;
  for (let i = 0; i < lines.length; i++) {
    if (lines[i].includes('Salutations,') || lines[i].includes('Bien cordialement,')) {
      partie3Start = i;
      break;
    }
  }

  // Partie 4 : Historique (commence par "-----Message d'origine-----")
  let partie4Start = -1;
  for (let i = 0; i < lines.length; i++) {
    if (lines[i].includes('-----Message d\'origine-----')) {
      partie4Start = i;
      break;
    }
  }

  // Extraire les parties
  const partie1 = lines.slice(0, partie1End).join('\n');

  let partie2End = partie3Start !== -1 ? partie3Start : lines.length;
  const partie2 = lines.slice(partie1End, partie2End).join('\n').trim();

  let partie3End = partie4Start !== -1 ? partie4Start : lines.length;
  const partie3 = partie3Start !== -1 ? lines.slice(partie3Start, partie3End).join('\n') : '';

  const partie4 = partie4Start !== -1 ? lines.slice(partie4Start).join('\n') : '';

  // Sauvegarder les parties pour reconstruction ultérieure
  result.push({
    json: {
      ...data,
      _partie1: partie1,
      _partie2: partie2,
      _partie3: partie3,
      _partie4: partie4,
      _bodyOnly: partie2, // Le corps seul à envoyer à l'agent
      _fullBrouillon: brouillon // Backup du brouillon complet
    }
  });
}

return result;
