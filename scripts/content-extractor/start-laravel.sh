#!/bin/bash

# Script de démarrage pour Content Extractor avec Laravel

cd /var/www/automatehub/scripts/content-extractor

# Activer l'environnement virtuel
source env/bin/activate

# Variables d'environnement
export PORT=5682
export DB_HOST=127.0.0.1
export DB_USERNAME=automatehub_user
export DB_PASSWORD='Af7$Br2@ZqP!9kL'
export DB_DATABASE=automatehub

# Tuer l'ancien processus s'il existe
pkill -f "api-server-simple.py" || true
pkill -f "api-server-laravel.py" || true

# Démarrer le nouveau serveur
echo "Démarrage de Content Extractor (Laravel) sur le port $PORT..."
python api-server-laravel.py