#!/usr/bin/env python3
"""
API Server pour Content Extractor - Version simplifiée
Sans Selenium pour commencer
"""

from http.server import HTTPServer, BaseHTTPRequestHandler
import json
import urllib.parse
import subprocess
import os
import hashlib
import time
from datetime import datetime

# Configuration
API_KEY = os.environ.get('CONTENT_EXTRACTOR_API_KEY', 'default-api-key-change-me')
PORT = int(os.environ.get('PORT', 5678))

class ContentExtractorHandler(BaseHTTPRequestHandler):
    def do_POST(self):
        # Vérifier l'authentification
        auth_header = self.headers.get('Authorization')
        if not auth_header or not auth_header.startswith('Bearer '):
            self.send_error(401, "Token d'authentification manquant")
            return
            
        api_key = auth_header.replace('Bearer ', '')
        if api_key != API_KEY:
            self.send_error(403, "Clé API invalide")
            return
        
        # Lire le body
        content_length = int(self.headers['Content-Length'])
        post_data = self.rfile.read(content_length)
        
        try:
            data = json.loads(post_data.decode('utf-8'))
        except:
            self.send_error(400, "JSON invalide")
            return
        
        # Router vers la bonne fonction
        if self.path == '/api/v1/get-youtube-transcript':
            self.handle_youtube_transcript(data)
        elif self.path == '/api/v1/scrape':
            self.handle_scrape(data)
        elif self.path == '/api/v1/credits':
            self.handle_credits()
        else:
            self.send_error(404, "Endpoint non trouvé")
    
    def do_GET(self):
        if self.path == '/health':
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            response = {'status': 'healthy', 'service': 'content-extractor'}
            self.wfile.write(json.dumps(response).encode())
        elif self.path == '/api/v1/credits':
            # Vérifier auth
            auth_header = self.headers.get('Authorization')
            if not auth_header or auth_header.replace('Bearer ', '') != API_KEY:
                self.send_error(403, "Non autorisé")
                return
            self.handle_credits()
        else:
            self.send_error(404, "Not found")
    
    def handle_youtube_transcript(self, data):
        """Gère l'extraction YouTube"""
        video_url = data.get('videoUrl')
        if not video_url:
            self.send_error(400, "URL de vidéo manquante")
            return
        
        # Appeler le script Python
        script_path = os.path.join(os.path.dirname(__file__), 'youtube-transcript.py')
        
        # Utiliser l'environnement virtuel si disponible
        python_bin = os.path.join(os.path.dirname(__file__), 'env/bin/python3')
        if not os.path.exists(python_bin):
            python_bin = 'python3'
            
        cmd = [
            python_bin, script_path,
            video_url,
            data.get('preferredLanguage', 'fr'),
            str(data.get('includeTimestamps', True)),
            str(data.get('timestampsToCombine', 5))
        ]
        
        try:
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=30)
            
            if result.returncode == 0:
                response_data = json.loads(result.stdout)
                self.send_response(200)
            else:
                response_data = {
                    'success': False,
                    'error': result.stderr or "Erreur lors de l'extraction"
                }
                self.send_response(400)
            
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            self.wfile.write(json.dumps(response_data).encode())
            
        except subprocess.TimeoutExpired:
            self.send_error(504, "Timeout lors de l'extraction")
        except Exception as e:
            self.send_error(500, str(e))
    
    def handle_scrape(self, data):
        """Gère le scraping web"""
        url = data.get('url')
        if not url:
            self.send_error(400, "URL manquante")
            return
        
        # Appeler le script Python
        script_path = os.path.join(os.path.dirname(__file__), 'web-scraper.py')
        
        # Utiliser l'environnement virtuel si disponible
        python_bin = os.path.join(os.path.dirname(__file__), 'env/bin/python3')
        if not os.path.exists(python_bin):
            python_bin = 'python3'
            
        cmd = [
            python_bin, script_path,
            url,
            data.get('format', 'markdown'),
            str(data.get('cleaned', True)),
            'false'  # Pas de JS pour l'instant
        ]
        
        try:
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=30)
            
            if result.returncode == 0:
                response_data = json.loads(result.stdout)
                self.send_response(200)
            else:
                response_data = {
                    'success': False,
                    'error': result.stderr or "Erreur lors du scraping"
                }
                self.send_response(400)
            
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            self.wfile.write(json.dumps(response_data).encode())
            
        except subprocess.TimeoutExpired:
            self.send_error(504, "Timeout lors du scraping")
        except Exception as e:
            self.send_error(500, str(e))
    
    def handle_credits(self):
        """Retourne les crédits (simulé pour l'instant)"""
        response = {
            'credits_remaining': 1000.0,
            'email': 'admin@automatehub.fr'
        }
        
        self.send_response(200)
        self.send_header('Content-Type', 'application/json')
        self.end_headers()
        self.wfile.write(json.dumps(response).encode())
    
    def log_message(self, format, *args):
        """Override pour personnaliser les logs"""
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] {format % args}")

def run_server():
    print(f"🚀 Content Extractor API démarré sur le port {PORT}")
    print(f"🔑 Clé API configurée: {API_KEY[:8]}...")
    
    server_address = ('', PORT)
    httpd = HTTPServer(server_address, ContentExtractorHandler)
    
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print("\n🛑 Arrêt du serveur...")
        httpd.shutdown()

if __name__ == '__main__':
    run_server()