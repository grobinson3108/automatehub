#!/bin/bash

# Script de sauvegarde pour AutomateHub
# Exécution quotidienne recommandée

# Configuration
DB_NAME="automatehub"
DB_USER="automatehub_user"
DB_PASS="Af7\$Br2@ZqP!9kL"
BACKUP_DIR="/var/www/automatehub/backups"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=7

# Créer le répertoire de backup s'il n'existe pas
mkdir -p $BACKUP_DIR

# Backup de la base de données
echo "$(date): Début du backup de la base de données AutomateHub"
mysqldump -u $DB_USER -p"$DB_PASS" $DB_NAME | gzip > "$BACKUP_DIR/automatehub_db_$DATE.sql.gz"

# Backup des fichiers importants (storage, .env, etc.)
echo "$(date): Backup des fichiers de configuration"
tar -czf "$BACKUP_DIR/automatehub_files_$DATE.tar.gz" \
    /var/www/automatehub/.env \
    /var/www/automatehub/storage/app \
    /var/www/automatehub/public/uploads 2>/dev/null || true

# Nettoyage des anciens backups
echo "$(date): Nettoyage des backups de plus de $RETENTION_DAYS jours"
find $BACKUP_DIR -name "automatehub_*.gz" -mtime +$RETENTION_DAYS -delete

# Vérification de l'espace disque
DISK_USAGE=$(df -h /var/www | awk 'NR==2 {print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    echo "$(date): ATTENTION - Espace disque critique: $DISK_USAGE% utilisé"
fi

echo "$(date): Backup terminé avec succès"

# Log dans Laravel
echo "$(date): Backup AutomateHub terminé" >> /var/www/automatehub/storage/logs/backup.log