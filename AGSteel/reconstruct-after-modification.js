// Node Code: Reconstruct After Modification
// À placer APRÈS l'agent si Iterations ≥ 1

const items = $input.all();
const result = [];

for (const item of items) {
  const data = item.json;

  // Si pas de parties sauvegardées, passer directement
  if (!data._partie1) {
    result.push(item);
    continue;
  }

  // Récupérer le brouillon modifié retourné par l'agent
  const brouillonAgent = data.brouillon || data.output?.brouillon || '';

  // Si l'agent a retourné un brouillon COMPLET (avec signature et historique)
  // → Il n'a pas respecté la consigne, on ignore sa réponse et on garde l'original
  if (brouillonAgent.includes('Salutations,') || brouillonAgent.includes('-----Message d\'origine-----')) {
    console.log('⚠️ AGENT A RETOURNÉ UN BROUILLON COMPLET - ON FORCE LA MODIFICATION CHIRURGICALE');

    // Extraire uniquement le corps de la réponse de l'agent
    const lignesAgent = brouillonAgent.split('\n');

    let corpsDebut = 0;
    for (let i = 0; i < lignesAgent.length; i++) {
      if (lignesAgent[i].trim() === '' && i > 0) {
        corpsDebut = i + 1;
        break;
      }
    }

    let corpsFin = lignesAgent.length;
    for (let i = 0; i < lignesAgent.length; i++) {
      if (lignesAgent[i].includes('Salutations,') || lignesAgent[i].includes('Bien cordialement,')) {
        corpsFin = i;
        break;
      }
    }

    const corpsModifie = lignesAgent.slice(corpsDebut, corpsFin).join('\n').trim();

    // Reconstruire avec le corps modifié de l'agent
    const brouillonFinal = [
      data._partie1,
      '',
      corpsModifie,
      '',
      data._partie3,
      data._partie4 ? '\n' + data._partie4 : ''
    ].filter(Boolean).join('\n').trim();

    result.push({
      json: {
        ...data,
        brouillon: brouillonFinal,
        // Supprimer les champs temporaires
        _partie1: undefined,
        _partie2: undefined,
        _partie3: undefined,
        _partie4: undefined,
        _bodyOnly: undefined,
        _fullBrouillon: undefined
      }
    });
  } else {
    // L'agent a retourné UNIQUEMENT le corps modifié (bien !)
    const brouillonFinal = [
      data._partie1,
      '',
      brouillonAgent.trim(),
      '',
      data._partie3,
      data._partie4 ? '\n' + data._partie4 : ''
    ].filter(Boolean).join('\n').trim();

    result.push({
      json: {
        ...data,
        brouillon: brouillonFinal,
        // Supprimer les champs temporaires
        _partie1: undefined,
        _partie2: undefined,
        _partie3: undefined,
        _partie4: undefined,
        _bodyOnly: undefined,
        _fullBrouillon: undefined
      }
    });
  }
}

return result;
