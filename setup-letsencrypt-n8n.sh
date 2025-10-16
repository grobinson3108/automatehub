#!/bin/bash

# Script to set up Let's Encrypt SSL certificate for n8n
# Usage: sudo bash setup-letsencrypt-n8n.sh

# Check if running as root
if [ "$EUID" -ne 0 ]; then
  echo "Please run as root (use sudo)"
  exit 1
fi

echo "=== Let's Encrypt SSL Setup for n8n ==="
echo "This script will set up a Let's Encrypt SSL certificate for n8n.automatehub.fr"
echo ""

# Check if certificate already exists
if [ -d "/etc/letsencrypt/live/n8n.automatehub.fr" ]; then
  echo "Let's Encrypt certificate already exists!"
  echo ""
  echo "Certificate information:"
  certbot certificates | grep -A 6 "n8n.automatehub.fr"
  echo ""
  echo "To renew the certificate, run: sudo certbot renew"
  echo "To test the renewal process, run: sudo certbot renew --dry-run"
  echo ""
  echo "Would you like to force renewal of the certificate? (y/n)"
  read -r answer
  if [[ "$answer" =~ ^[Yy]$ ]]; then
    echo "Forcing certificate renewal..."
    certbot renew --force-renewal -d n8n.automatehub.fr
  else
    echo "Skipping renewal."
    exit 0
  fi
  exit 0
fi

# Install certbot if not already installed
if ! command -v certbot &> /dev/null; then
  echo "Installing certbot..."
  apt-get update
  apt-get install -y certbot
  echo ""
fi

# Stop nginx to free up port 80
echo "Stopping nginx to free up port 80..."
systemctl stop nginx

# Obtain certificate using standalone mode
echo "Obtaining Let's Encrypt certificate..."
certbot certonly --standalone -d n8n.automatehub.fr

# Check if certificate was obtained successfully
if [ $? -eq 0 ]; then
  echo ""
  echo "Certificate obtained successfully!"
  
  # Update nginx configuration
  echo "Updating nginx configuration..."
  sed -i 's|ssl_certificate .*|ssl_certificate /etc/letsencrypt/live/n8n.automatehub.fr/fullchain.pem;|' /etc/nginx/sites-available/n8n.conf
  sed -i 's|ssl_certificate_key .*|ssl_certificate_key /etc/letsencrypt/live/n8n.automatehub.fr/privkey.pem;|' /etc/nginx/sites-available/n8n.conf
  
  # Also update direct IP access configuration
  if [ -f "/etc/nginx/sites-available/n8n-direct-ip.conf" ]; then
    sed -i 's|ssl_certificate .*|ssl_certificate /etc/letsencrypt/live/n8n.automatehub.fr/fullchain.pem;|' /etc/nginx/sites-available/n8n-direct-ip.conf
    sed -i 's|ssl_certificate_key .*|ssl_certificate_key /etc/letsencrypt/live/n8n.automatehub.fr/privkey.pem;|' /etc/nginx/sites-available/n8n-direct-ip.conf
  fi
  
  # Start nginx
  echo "Starting nginx..."
  systemctl start nginx
  
  echo ""
  echo "Let's Encrypt certificates are valid for 90 days and will be automatically renewed."
  echo "You can test the renewal process with: certbot renew --dry-run"
  echo ""
  echo "Your n8n instance is now accessible at: https://n8n.automatehub.fr"
  echo "without any security warnings."
else
  echo ""
  echo "Failed to obtain certificate. Please check the error messages above."
  echo "Common issues:"
  echo "  - DNS not properly configured (n8n.automatehub.fr must point to this server)"
  echo "  - Port 80/443 not accessible from the internet"
  echo ""
  echo "Starting nginx again..."
  systemctl start nginx
  
  echo "You can continue using the self-signed certificate for now."
fi
