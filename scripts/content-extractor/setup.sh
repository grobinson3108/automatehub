#!/bin/bash

# Script d'installation pour Content Extractor

echo "🚀 Installation de Content Extractor..."

# Créer l'environnement virtuel
python3 -m venv venv

# Activer l'environnement
source venv/bin/activate

# Installer les dépendances
pip install -r requirements.txt

# Installer Chrome et ChromeDriver pour Selenium
if ! command -v google-chrome &> /dev/null; then
    echo "📦 Installation de Google Chrome..."
    wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
    sudo sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list'
    sudo apt-get update
    sudo apt-get install -y google-chrome-stable
fi

# Installer ChromeDriver
echo "📦 Installation de ChromeDriver..."
CHROME_VERSION=$(google-chrome --version | awk '{print $3}' | cut -d'.' -f1)
wget -N https://chromedriver.storage.googleapis.com/LATEST_RELEASE_${CHROME_VERSION} -P /tmp/
CHROMEDRIVER_VERSION=$(cat /tmp/LATEST_RELEASE_${CHROME_VERSION})
wget -N https://chromedriver.storage.googleapis.com/${CHROMEDRIVER_VERSION}/chromedriver_linux64.zip -P /tmp/
unzip -o /tmp/chromedriver_linux64.zip -d /tmp/
sudo mv -f /tmp/chromedriver /usr/local/bin/chromedriver
sudo chmod +x /usr/local/bin/chromedriver

# Créer le service systemd
echo "⚙️ Création du service systemd..."
sudo tee /etc/systemd/system/content-extractor.service > /dev/null <<EOF
[Unit]
Description=Content Extractor API Service
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/automatehub/scripts/content-extractor
Environment="PATH=/var/www/automatehub/scripts/content-extractor/venv/bin:/usr/local/bin:/usr/bin:/bin"
Environment="CONTENT_EXTRACTOR_API_KEY=change-me-to-secure-key"
Environment="ADMIN_TOKEN=change-me-to-secure-admin-token"
Environment="PORT=5678"
ExecStart=/var/www/automatehub/scripts/content-extractor/venv/bin/python api-server.py
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF

# Recharger systemd
sudo systemctl daemon-reload

# Créer les répertoires nécessaires
mkdir -p /var/www/automatehub/data

# Permissions
sudo chown -R www-data:www-data /var/www/automatehub/scripts/content-extractor
sudo chown -R www-data:www-data /var/www/automatehub/data

echo "✅ Installation terminée!"
echo ""
echo "📋 Prochaines étapes:"
echo "1. Modifiez les variables d'environnement dans /etc/systemd/system/content-extractor.service"
echo "2. Démarrez le service: sudo systemctl start content-extractor"
echo "3. Activez au démarrage: sudo systemctl enable content-extractor"
echo "4. Vérifiez le statut: sudo systemctl status content-extractor"
echo ""
echo "🔗 L'API sera disponible sur: http://localhost:5678"