#!/bin/bash

# Script d'installation et de configuration pour automatehub.fr
# Ce script automatise les étapes décrites dans le guide de configuration

# Vérifier si l'utilisateur est root
if [ "$EUID" -ne 0 ]; then
  echo "Ce script doit être exécuté en tant que root (utilisez sudo)"
  exit 1
fi

# Variables à configurer
SERVER_IP="217.154.18.86"
DOMAIN="automatehub.fr"
WWW_DOMAIN="www.automatehub.fr"
APP_PATH="/var/www/automatehub"
USER="grobinson"
WEB_GROUP="www-data"
PHP_VERSION="8.3"

echo "=== Configuration de automatehub.fr ==="
echo "IP du serveur: $SERVER_IP"
echo "Domaine: $DOMAIN"
echo "Chemin de l'application: $APP_PATH"
echo ""

# 1. Vérifier les prérequis
echo "=== Vérification des prérequis ==="
if ! command -v nginx &> /dev/null; then
    echo "nginx n'est pas installé. Installation..."
    apt update
    apt install -y nginx
else
    echo "nginx est déjà installé."
fi

if ! command -v certbot &> /dev/null; then
    echo "certbot n'est pas installé. Installation..."
    apt update
    apt install -y certbot python3-certbot-nginx
else
    echo "certbot est déjà installé."
fi

# 2. Configurer nginx
echo "=== Configuration de nginx ==="
echo "Création du fichier de configuration nginx..."

cat > /etc/nginx/sites-available/$DOMAIN.conf << EOF
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN $WWW_DOMAIN;
    
    # Redirection vers HTTPS sera configurée après l'installation du certificat SSL
    
    root $APP_PATH/public;
    index index.php;
    
    # Logs
    access_log /var/log/nginx/automatehub.fr.access.log;
    error_log /var/log/nginx/automatehub.fr.error.log;
    
    # Règles de réécriture pour Laravel
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    # Traitement des fichiers PHP
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php$PHP_VERSION-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
    }
    
    # Cache des ressources statiques
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
    
    # Désactiver l'accès aux fichiers cachés
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
    
    # Désactiver l'accès aux fichiers sensibles
    location ~ (\.env|composer\.json|composer\.lock|package\.json|package-lock\.json|phpunit\.xml|webpack\.mix\.js|yarn\.lock)$ {
        deny all;
        access_log off;
        log_not_found off;
    }
}
EOF

echo "Activation de la configuration..."
ln -sf /etc/nginx/sites-available/$DOMAIN.conf /etc/nginx/sites-enabled/

echo "Vérification de la configuration nginx..."
nginx -t

if [ $? -ne 0 ]; then
    echo "Erreur dans la configuration nginx. Veuillez vérifier le fichier de configuration."
    exit 1
fi

echo "Redémarrage de nginx..."
systemctl restart nginx

# 3. Configurer Let's Encrypt
echo "=== Configuration de Let's Encrypt ==="
echo "IMPORTANT: Assurez-vous que les enregistrements DNS pour $DOMAIN et $WWW_DOMAIN pointent vers $SERVER_IP"
echo "et que la propagation DNS est terminée avant de continuer."
echo ""
read -p "Appuyez sur Entrée pour continuer avec l'installation du certificat SSL ou Ctrl+C pour annuler..."

echo "Obtention du certificat SSL avec Certbot..."
certbot --nginx -d $DOMAIN -d $WWW_DOMAIN

if [ $? -ne 0 ]; then
    echo "Erreur lors de l'obtention du certificat SSL. Vérifiez les messages d'erreur ci-dessus."
    exit 1
fi

# 4. Ajouter des en-têtes de sécurité supplémentaires
echo "=== Configuration des en-têtes de sécurité ==="
echo "Masquage de la version de nginx..."

if ! grep -q "server_tokens off;" /etc/nginx/nginx.conf; then
    sed -i '/http {/a \    server_tokens off;' /etc/nginx/nginx.conf
    echo "Version de nginx masquée."
else
    echo "La version de nginx est déjà masquée."
fi

echo "Vérification de la configuration nginx..."
nginx -t

if [ $? -ne 0 ]; then
    echo "Erreur dans la configuration nginx. Veuillez vérifier le fichier de configuration."
    exit 1
fi

echo "Redémarrage de nginx..."
systemctl restart nginx

# 5. Configurer les permissions
echo "=== Configuration des permissions ==="
echo "Mise à jour des permissions pour $APP_PATH..."

chown -R $USER:$WEB_GROUP $APP_PATH
find $APP_PATH -type f -exec chmod 664 {} \;
find $APP_PATH -type d -exec chmod 775 {} \;
chmod -R ug+rwx $APP_PATH/storage $APP_PATH/bootstrap/cache

echo "Permissions mises à jour."

# 6. Configurer le pare-feu
echo "=== Configuration du pare-feu ==="

if ! command -v ufw &> /dev/null; then
    echo "UFW n'est pas installé. Installation..."
    apt update
    apt install -y ufw
fi

echo "Configuration du pare-feu avec UFW..."
ufw status | grep -q "Status: active"
if [ $? -ne 0 ]; then
    echo "Activation du pare-feu..."
    ufw default deny incoming
    ufw default allow outgoing
    ufw allow ssh
    ufw allow http
    ufw allow https
    echo "y" | ufw enable
else
    echo "Le pare-feu est déjà actif. Vérification des règles..."
    ufw status | grep -q "22/tcp"
    if [ $? -ne 0 ]; then
        ufw allow ssh
    fi
    
    ufw status | grep -q "80/tcp"
    if [ $? -ne 0 ]; then
        ufw allow http
    fi
    
    ufw status | grep -q "443/tcp"
    if [ $? -ne 0 ]; then
        ufw allow https
    fi
fi

echo "Statut du pare-feu:"
ufw status

# 7. Vérification du renouvellement automatique de Let's Encrypt
echo "=== Configuration du renouvellement automatique de Let's Encrypt ==="
echo "Vérification du service certbot.timer..."

systemctl is-active --quiet certbot.timer
if [ $? -ne 0 ]; then
    echo "Activation du service certbot.timer..."
    systemctl enable certbot.timer
    systemctl start certbot.timer
else
    echo "Le service certbot.timer est déjà actif."
fi

echo "=== Installation terminée ==="
echo ""
echo "Votre domaine $DOMAIN est maintenant configuré avec:"
echo "- Serveur web nginx optimisé pour Laravel"
echo "- Certificat SSL Let's Encrypt avec renouvellement automatique"
echo "- Redirection HTTP vers HTTPS"
echo "- En-têtes de sécurité"
echo "- Permissions correctes pour les fichiers et répertoires"
echo "- Pare-feu configuré"
echo ""
echo "Vous pouvez maintenant accéder à votre site à l'adresse https://$DOMAIN"
echo ""
echo "En cas de problème, vérifiez les logs:"
echo "sudo tail -f /var/log/nginx/$DOMAIN.error.log"
