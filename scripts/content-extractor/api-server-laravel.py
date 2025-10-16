#!/usr/bin/env python3
"""
Content Extractor API Server - Version Laravel Integration
"""

import os
import json
import logging
from http.server import BaseHTTPRequestHandler, HTTPServer
from urllib.parse import urlparse, parse_qs
import subprocess
import sys
import mysql.connector
from datetime import datetime
import hashlib

# Import de la librairie content extractor
from content_extractor_lib import extract_youtube_transcript, scrape_web_page

# Configuration
PORT = int(os.environ.get('PORT', 5682))
DB_HOST = os.environ.get('DB_HOST', 'localhost')
DB_USER = os.environ.get('DB_USERNAME', 'automatehub')
DB_PASS = os.environ.get('DB_PASSWORD', '')
DB_NAME = os.environ.get('DB_DATABASE', 'automatehub')

# Logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

class ContentExtractorHandler(BaseHTTPRequestHandler):
    
    def get_db_connection(self):
        """Créer une connexion MySQL"""
        return mysql.connector.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASS,
            database=DB_NAME
        )
    
    def validate_api_key(self, api_key):
        """Valider la clé API et récupérer les infos de souscription"""
        conn = self.get_db_connection()
        cursor = conn.cursor(dictionary=True)
        
        try:
            # Récupérer la souscription
            query = """
            SELECT 
                s.id,
                s.user_id,
                s.monthly_quota,
                s.used_this_month,
                s.extra_credits,
                s.status,
                s.reset_date,
                u.email,
                a.slug as api_slug
            FROM user_api_subscriptions s
            JOIN users u ON s.user_id = u.id
            JOIN api_services a ON s.api_service_id = a.id
            WHERE s.api_key = %s AND s.status = 'active' AND a.slug = 'content-extractor'
            """
            
            cursor.execute(query, (api_key,))
            subscription = cursor.fetchone()
            
            if not subscription:
                return None
                
            # Vérifier si le quota doit être réinitialisé
            if subscription['reset_date'] <= datetime.now().date():
                update_query = """
                UPDATE user_api_subscriptions 
                SET used_this_month = 0, reset_date = DATE_ADD(NOW(), INTERVAL 1 MONTH)
                WHERE id = %s
                """
                cursor.execute(update_query, (subscription['id'],))
                conn.commit()
                subscription['used_this_month'] = 0
            
            # Calculer les crédits restants
            subscription['remaining_credits'] = (
                subscription['monthly_quota'] + 
                subscription['extra_credits'] - 
                subscription['used_this_month']
            )
            
            return subscription
            
        finally:
            cursor.close()
            conn.close()
    
    def use_credits(self, subscription_id, credits=1, endpoint='', method='GET'):
        """Utiliser des crédits et logger l'utilisation"""
        conn = self.get_db_connection()
        cursor = conn.cursor()
        
        try:
            # Incrémenter l'utilisation
            cursor.execute(
                "UPDATE user_api_subscriptions SET used_this_month = used_this_month + %s WHERE id = %s",
                (credits, subscription_id)
            )
            
            # Logger l'utilisation
            cursor.execute("""
                INSERT INTO api_usage_logs 
                (subscription_id, endpoint, method, credits_used, response_code, created_at)
                VALUES (%s, %s, %s, %s, %s, %s)
            """, (subscription_id, endpoint, method, credits, 200, datetime.now()))
            
            conn.commit()
            return True
            
        except Exception as e:
            logger.error(f"Erreur lors de l'utilisation des crédits: {e}")
            conn.rollback()
            return False
            
        finally:
            cursor.close()
            conn.close()
    
    def do_OPTIONS(self):
        """Gérer les requêtes OPTIONS pour CORS"""
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        self.end_headers()
    
    def do_GET(self):
        """Gérer les requêtes GET"""
        parsed_path = urlparse(self.path)
        
        if parsed_path.path == '/health':
            self.send_json_response({'status': 'healthy', 'service': 'content-extractor'})
            return
            
        self.send_error_response(404, "Endpoint non trouvé")
    
    def do_POST(self):
        """Gérer les requêtes POST"""
        # Vérifier l'authentification
        auth_header = self.headers.get('Authorization', '')
        if not auth_header.startswith('Bearer '):
            self.send_error_response(401, "Token d'authentification manquant")
            return
            
        api_key = auth_header[7:]
        subscription = self.validate_api_key(api_key)
        
        if not subscription:
            self.send_error_response(401, "Clé API invalide")
            return
            
        # Vérifier les crédits
        if subscription['remaining_credits'] <= 0:
            self.send_error_response(403, "Crédits insuffisants", {
                'remaining_credits': 0,
                'monthly_quota': subscription['monthly_quota']
            })
            return
        
        # Parser la requête
        parsed_path = urlparse(self.path)
        
        if parsed_path.path == '/youtube':
            self.handle_youtube_extraction(subscription)
        elif parsed_path.path == '/web':
            self.handle_web_extraction(subscription)
        else:
            self.send_error_response(404, "Endpoint non trouvé")
    
    def handle_youtube_extraction(self, subscription):
        """Gérer l'extraction YouTube"""
        try:
            content_length = int(self.headers.get('Content-Length', 0))
            post_data = self.rfile.read(content_length)
            data = json.loads(post_data.decode('utf-8'))
            
            url = data.get('url')
            if not url:
                self.send_error_response(400, "URL manquante")
                return
            
            # Extraire la transcription
            result = extract_youtube_transcript(url)
            
            if 'error' in result:
                self.send_error_response(400, result['error'])
                return
            
            # Utiliser les crédits
            if self.use_credits(subscription['id'], 1, '/youtube', 'POST'):
                # Ajouter les infos de crédits
                result['credits_used'] = 1
                result['remaining_credits'] = subscription['remaining_credits'] - 1
                
                self.send_json_response(result)
            else:
                self.send_error_response(500, "Erreur lors de l'utilisation des crédits")
                
        except Exception as e:
            logger.error(f"Erreur YouTube: {e}")
            self.send_error_response(500, str(e))
    
    def handle_web_extraction(self, subscription):
        """Gérer l'extraction web"""
        try:
            content_length = int(self.headers.get('Content-Length', 0))
            post_data = self.rfile.read(content_length)
            data = json.loads(post_data.decode('utf-8'))
            
            url = data.get('url')
            if not url:
                self.send_error_response(400, "URL manquante")
                return
            
            # Options
            format_type = data.get('format', 'markdown')
            include_images = data.get('include_images', True)
            
            # Scraper la page
            result = scrape_web_page(
                url, 
                format_type=format_type,
                include_images=include_images
            )
            
            if 'error' in result:
                self.send_error_response(400, result['error'])
                return
            
            # Utiliser les crédits
            if self.use_credits(subscription['id'], 1, '/web', 'POST'):
                # Ajouter les infos de crédits
                result['credits_used'] = 1
                result['remaining_credits'] = subscription['remaining_credits'] - 1
                
                self.send_json_response(result)
            else:
                self.send_error_response(500, "Erreur lors de l'utilisation des crédits")
                
        except Exception as e:
            logger.error(f"Erreur Web: {e}")
            self.send_error_response(500, str(e))
    
    def send_json_response(self, data, status=200):
        """Envoyer une réponse JSON"""
        self.send_response(status)
        self.send_header('Content-Type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.end_headers()
        self.wfile.write(json.dumps(data).encode('utf-8'))
    
    def send_error_response(self, status, error, extra_data=None):
        """Envoyer une réponse d'erreur"""
        data = {'error': error}
        if extra_data:
            data.update(extra_data)
        self.send_json_response(data, status)
    
    def log_message(self, format, *args):
        """Logger les requêtes"""
        logger.info(f"{self.client_address[0]} - {format % args}")

def main():
    """Lancer le serveur"""
    # Tester la connexion DB
    try:
        conn = mysql.connector.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASS,
            database=DB_NAME
        )
        conn.close()
        logger.info("Connexion à la base de données réussie")
    except Exception as e:
        logger.error(f"Impossible de se connecter à la base de données: {e}")
        sys.exit(1)
    
    # Lancer le serveur
    server = HTTPServer(('0.0.0.0', PORT), ContentExtractorHandler)
    logger.info(f"Content Extractor API (Laravel) démarré sur le port {PORT}")
    
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        logger.info("Arrêt du serveur...")
        server.shutdown()

if __name__ == '__main__':
    main()