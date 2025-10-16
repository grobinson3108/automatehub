// Node Code: Take First Output Only
// À placer IMMÉDIATEMENT APRÈS l'agent AI

const items = $input.all();

// Si l'agent retourne plusieurs outputs, prendre uniquement le premier
if (items.length > 1) {
  console.log(`⚠️ Agent a retourné ${items.length} outputs, on garde uniquement le premier`);
  return [items[0]];
}

return items;
