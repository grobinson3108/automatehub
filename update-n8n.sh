#!/bin/bash

# Script to update n8n on AutomateHub server
# Usage: sudo bash update-n8n.sh

# Check if running as root
if [ "$EUID" -ne 0 ]; then
  echo "Please run as root (use sudo)"
  exit 1
fi

echo "=== n8n Update Script for AutomateHub ==="
echo "This script will update n8n to the latest version."
echo ""

# Backup current n8n data
echo "Creating backup of n8n data..."
BACKUP_FILE="/home/grobinson/n8n-backup-$(date +%Y%m%d%H%M%S).tar.gz"
tar -czvf "$BACKUP_FILE" /home/grobinson/.n8n
echo "Backup created at: $BACKUP_FILE"
echo ""

# Stop n8n service
echo "Stopping n8n service..."
systemctl stop n8n
echo ""

# Update n8n
echo "Updating n8n to the latest version..."
npm update -g n8n
echo ""

# Check version
echo "Checking installed version:"
n8n --version
echo ""

# Start n8n service
echo "Starting n8n service..."
systemctl start n8n
echo ""

# Fix permissions
echo "Fixing file permissions..."
chmod 600 /home/grobinson/.n8n/config
chmod 600 /home/grobinson/.n8n/.env
echo ""

# Check service status
echo "Checking n8n service status:"
systemctl status n8n --no-pager
echo ""

echo "=== Update Complete ==="
echo "If you encounter any issues, you can restore from the backup using:"
echo "tar -xzvf $BACKUP_FILE -C /"
echo ""
echo "You can access n8n at: https://n8n.automatehub.fr or http://localhost:5678"
