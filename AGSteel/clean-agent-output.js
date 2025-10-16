// Node Code: Clean Agent Output
// À placer APRÈS l'agent pour nettoyer les null bytes et prendre le meilleur output

const items = $input.all();

// Fonction pour compter les null bytes
function countNullBytes(text) {
  if (!text) return 0;
  return (text.match(/\u0000/g) || []).length;
}

// Si plusieurs outputs, prendre le meilleur (celui sans null bytes)
let bestItem = items[0];
let minNullBytes = countNullBytes(bestItem.json?.output?.brouillon || bestItem.json?.brouillon || '');

for (const item of items) {
  const brouillon = item.json?.output?.brouillon || item.json?.brouillon || '';
  const nullCount = countNullBytes(brouillon);

  if (nullCount < minNullBytes) {
    minNullBytes = nullCount;
    bestItem = item;
  }
}

// Nettoyer le brouillon du meilleur item
let brouillon = bestItem.json?.output?.brouillon || bestItem.json?.brouillon || '';

// Supprimer tous les null bytes
brouillon = brouillon.replace(/\u0000/g, '').replace(/\x00/g, '');

// Nettoyer les espaces multiples causés par la suppression des null bytes
brouillon = brouillon.replace(/\s{3,}/g, ' ');

// Mettre à jour le brouillon nettoyé
if (bestItem.json?.output?.brouillon) {
  bestItem.json.output.brouillon = brouillon;
} else if (bestItem.json?.brouillon) {
  bestItem.json.brouillon = brouillon;
}

console.log(`✅ Pris le meilleur output (${minNullBytes} null bytes)`);
console.log(`🗑️ Ignoré ${items.length - 1} autres outputs`);

return [bestItem];
