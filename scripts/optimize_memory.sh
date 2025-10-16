#!/bin/bash

# Script d'optimisation mémoire pour AutomateHub

echo "$(date): Démarrage optimisation mémoire"

# 1. Clear page cache
sync && echo 1 > /proc/sys/vm/drop_caches 2>/dev/null || echo "Nécessite privilèges root pour clear cache"

# 2. Restart PHP-FPM pour libérer la mémoire
echo "$(date): Redémarrage PHP-FPM"
systemctl restart php8.3-fpm 2>/dev/null || service php8.3-fpm restart 2>/dev/null || echo "Nécessite privilèges pour restart PHP-FPM"

# 3. Clear Laravel caches
cd /var/www/automatehub
echo "$(date): Nettoyage caches Laravel"
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 4. Optimiser Laravel pour production
echo "$(date): Optimisation Laravel"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Afficher l'état de la mémoire
echo "$(date): État de la mémoire après optimisation:"
free -h

# 6. Log dans Laravel
echo "$(date): Optimisation mémoire terminée" >> /var/www/automatehub/storage/logs/optimization.log