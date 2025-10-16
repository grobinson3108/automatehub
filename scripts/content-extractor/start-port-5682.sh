#!/bin/bash
# Démarrage sur le port 5682 pour éviter les conflits

cd /var/www/automatehub/scripts/content-extractor
source env/bin/activate

export CONTENT_EXTRACTOR_API_KEY="1ab54f24f0e313c8159aebf9cc99ebd0481e5a6275a11110600a0261f6605724"
export PORT=5682

echo "🚀 Démarrage sur le port 5682..."
python3 api-server-simple.py