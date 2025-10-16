# Configuration de automatehub.fr

Ce dépôt contient tous les fichiers nécessaires pour configurer le domaine automatehub.fr avec nginx et SSL pour une application Laravel.

## Contenu du dépôt

- **guide-automatehub-configuration.md** : Guide détaillé expliquant toutes les étapes de configuration
- **automatehub.fr.conf** : Fichier de configuration nginx pour le virtual host
- **security-headers.conf** : En-têtes de sécurité à ajouter après l'installation SSL
- **setup-automatehub.sh** : Script automatisé pour effectuer toute la configuration
- **README.md** : Ce fichier

## Comment utiliser ces fichiers

### Option 1 : Utiliser le script automatisé

Le script `setup-automatehub.sh` automatise l'ensemble du processus de configuration. Pour l'utiliser :

1. Modifiez la variable `SERVER_IP` dans le script pour y mettre l'adresse IP de votre serveur
2. Rendez le script exécutable (déjà fait) : `chmod +x setup-automatehub.sh`
3. Exécutez le script en tant que root : `sudo ./setup-automatehub.sh`
4. Suivez les instructions à l'écran

Le script effectuera toutes les étapes nécessaires, y compris :
- Configuration de nginx
- Installation du certificat SSL avec Let's Encrypt
- Configuration des en-têtes de sécurité
- Configuration des permissions
- Configuration du pare-feu

### Option 2 : Suivre le guide étape par étape

Si vous préférez effectuer la configuration manuellement ou si vous souhaitez comprendre chaque étape :

1. Ouvrez le fichier `guide-automatehub-configuration.md`
2. Suivez les instructions étape par étape

Le guide contient des explications détaillées pour :
- Configurer les enregistrements DNS chez IONOS
- Configurer nginx avec le virtual host
- Installer SSL avec Let's Encrypt
- Configurer la redirection HTTP vers HTTPS
- Optimiser la configuration pour Laravel
- Configurer les permissions
- Ajouter des en-têtes de sécurité

### Fichiers de configuration individuels

- `automatehub.fr.conf` : Vous pouvez copier ce fichier directement dans `/etc/nginx/sites-available/` si vous souhaitez configurer nginx manuellement
- `security-headers.conf` : Contient les en-têtes de sécurité à ajouter dans la configuration nginx après l'installation SSL

## Prérequis

- Serveur Ubuntu 22.04
- nginx installé
- PHP 8.3 avec php-fpm installé
- Application Laravel installée dans /var/www/automatehub
- Accès root au serveur
- Accès au compte IONOS pour configurer les DNS

## Après la configuration

Une fois la configuration terminée, votre site sera accessible à l'adresse https://automatehub.fr et sera :
- Sécurisé avec SSL
- Optimisé pour Laravel
- Configuré avec les meilleures pratiques de sécurité
- Configuré pour le renouvellement automatique du certificat SSL

En cas de problème, consultez les logs nginx :
```
sudo tail -f /var/log/nginx/automatehub.fr.error.log
