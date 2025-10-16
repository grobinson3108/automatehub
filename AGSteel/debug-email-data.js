// Debug: Voir la structure exacte des données

const items = $input.all();

console.log('=== STRUCTURE DES DONNÉES ===');
console.log('Keys disponibles:', Object.keys(items[0].json));
console.log('\n=== HEADERS ===');
console.log(JSON.stringify(items[0].json.headers, null, 2));
console.log('\n=== FROM ===');
console.log(JSON.stringify(items[0].json.from, null, 2));
console.log('\n=== TO ===');
console.log(JSON.stringify(items[0].json.to, null, 2));
console.log('\n=== SUBJECT ===');
console.log(items[0].json.subject);
console.log('\n=== DATE ===');
console.log(items[0].json.date);
console.log('\n=== MESSAGE ID ===');
console.log(items[0].json.messageId);
console.log('\n=== RAW (100 premiers chars) ===');
console.log(items[0].json.raw.substring(0, 100));

return items;
