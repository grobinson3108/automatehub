#!/bin/bash
# Wrapper MySQL pour Claude Terminal

# Vérifier les arguments
if [ $# -eq 0 ]; then
    echo "Usage: $0 \"SELECT * FROM table\""
    exit 1
fi

# Configuration MySQL depuis .env
DB_HOST="127.0.0.1"
DB_USER="automatehub_user"
DB_PASSWORD="Af7\$Br2@ZqP!9kL"
DB_NAME="automatehub"

# Exécuter la requête (suppression du warning avec 2>/dev/null)
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" -e "$1" 2>&1 | grep -v "Warning"