#!/bin/bash
# Script pour installer les dépendances sans ChromeDriver

cd /var/www/automatehub/scripts/content-extractor

# Créer l'environnement virtuel si nécessaire
if [ ! -d "venv" ]; then
    python3 -m venv venv
fi

# Activer et installer
source venv/bin/activate
pip install --upgrade pip
pip install -r requirements.txt

# Créer le répertoire data
mkdir -p /var/www/automatehub/data

echo "✅ Dépendances installées!"