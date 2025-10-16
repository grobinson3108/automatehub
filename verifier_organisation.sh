#!/bin/bash

echo "📊 VÉRIFICATION DE L'ORGANISATION DES WORKFLOWS"
echo "============================================="
echo ""

# Vérifier Freemium
echo "📁 FREEMIUM_WORKFLOWS :"
echo "---------------------"
total_freemium=0

for dir in /var/www/automatehub/Freemium_Workflows/*/; do
    if [ -d "$dir" ]; then
        dirname=$(basename "$dir")
        count=$(ls "$dir"*.json 2>/dev/null | wc -l)
        if [ $count -gt 0 ]; then
            printf "  %-25s : %4d workflows\n" "$dirname" "$count"
            total_freemium=$((total_freemium + count))
        fi
    fi
done

echo "  -------------------------"
echo "  TOTAL FREEMIUM          : $total_freemium workflows"

echo ""
echo "📁 PREMIUM_WORKFLOWS :"
echo "--------------------"
total_premium=0

for dir in /var/www/automatehub/Premium_Workflows/*/; do
    if [ -d "$dir" ]; then
        dirname=$(basename "$dir")
        count=$(ls "$dir"*.json 2>/dev/null | wc -l)
        if [ $count -gt 0 ]; then
            printf "  %-25s : %4d workflows\n" "$dirname" "$count"
            total_premium=$((total_premium + count))
        fi
    fi
done

echo "  -------------------------"
echo "  TOTAL PREMIUM           : $total_premium workflows"

echo ""
echo "📊 RÉSUMÉ GLOBAL :"
echo "=================="
echo "  Total Freemium : $total_freemium"
echo "  Total Premium  : $total_premium"
echo "  GRAND TOTAL    : $((total_freemium + total_premium))"
echo ""

# Vérifier s'il reste des fichiers à la racine
root_freemium=$(ls /var/www/automatehub/Freemium_Workflows/*.json 2>/dev/null | wc -l)
root_premium=$(ls /var/www/automatehub/Premium_Workflows/*.json 2>/dev/null | wc -l)

if [ $root_freemium -gt 0 ] || [ $root_premium -gt 0 ]; then
    echo "⚠️  ATTENTION : Il reste des fichiers à la racine !"
    echo "  - Freemium (racine) : $root_freemium fichiers"
    echo "  - Premium (racine)  : $root_premium fichiers"
else
    echo "✅ Tous les workflows sont correctement organisés dans leurs dossiers !"
fi