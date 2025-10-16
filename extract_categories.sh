#!/bin/bash

echo "=== CATEGORIES DE WORKFLOWS N8N ==="

# Extraire toutes les catégories uniques
find /var/www/automatehub/200_automations_n8n -type f -name "*.json" | \
sed 's|/var/www/automatehub/200_automations_n8n/||' | \
cut -d'/' -f1 | \
sort | uniq -c | sort -nr

echo ""
echo "=== STRUCTURE DETAILLEE ==="

# Compter les fichiers par catégorie
for dir in $(find /var/www/automatehub/200_automations_n8n -type d -mindepth 1 -maxdepth 1 | sort); do
    category=$(basename "$dir")
    count=$(find "$dir" -name "*.json" | wc -l)
    echo "$category: $count workflows"
done