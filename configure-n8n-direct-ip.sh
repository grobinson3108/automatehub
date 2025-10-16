#!/bin/bash

# Script to configure n8n to be accessible via server's IP address
# Usage: sudo bash configure-n8n-direct-ip.sh

# Check if running as root
if [ "$EUID" -ne 0 ]; then
  echo "Please run as root (use sudo)"
  exit 1
fi

# Get server's IP address
SERVER_IP=$(hostname -I | awk '{print $1}')

echo "=== Configure n8n for Direct IP Access ==="
echo "This script will configure nginx to make n8n accessible via your server's IP address."
echo "Server IP detected as: $SERVER_IP"
echo ""

# Create nginx configuration file
echo "Creating nginx configuration file..."
cat > /etc/nginx/sites-available/n8n-direct-ip.conf << EOF
server {
    listen 80;
    server_name $SERVER_IP;
    
    # Redirect all HTTP requests to HTTPS
    return 301 https://\$host\$request_uri;
}

server {
    listen 443 ssl;
    server_name $SERVER_IP;
    
    # SSL configuration
    ssl_certificate /etc/ssl/certs/n8n-cert.pem;
    ssl_certificate_key /etc/ssl/private/n8n-key.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_session_timeout 1d;
    ssl_session_cache shared:SSL:10m;
    # Disabled for self-signed certificate
    # ssl_stapling on;
    # ssl_stapling_verify on;
    
    # HSTS (optional, but recommended)
    add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload";

    location / {
        proxy_pass http://localhost:5678;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        
        # WebSocket support
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection "upgrade";
        
        # Timeout settings
        proxy_read_timeout 90s;
        proxy_send_timeout 90s;
    }

    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Frame-Options SAMEORIGIN;
    add_header Referrer-Policy strict-origin-when-cross-origin;
}
EOF

# Enable the configuration
echo "Enabling nginx configuration..."
ln -sf /etc/nginx/sites-available/n8n-direct-ip.conf /etc/nginx/sites-enabled/

# Test and reload nginx
echo "Testing nginx configuration..."
nginx -t

if [ $? -eq 0 ]; then
  echo "Reloading nginx..."
  systemctl reload nginx
  echo ""
  echo "Configuration complete!"
  echo "You can now access n8n via:"
  echo "https://$SERVER_IP"
  echo ""
  echo "Note: You will still see certificate warnings because the certificate"
  echo "was issued for n8n.automatehub.fr, not your server's IP address."
  echo "This is normal and you can safely proceed."
else
  echo ""
  echo "Nginx configuration test failed. Please check the error messages above."
fi
