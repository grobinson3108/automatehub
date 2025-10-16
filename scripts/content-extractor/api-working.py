#!/usr/bin/env python3
"""
Content Extractor API - Version Fonctionnelle avec Support YouTube Shorts
"""

from http.server import HTTPServer, BaseHTTPRequestHandler
import json
import os
import subprocess
import hashlib

# Configuration
API_KEY = os.environ.get('CONTENT_EXTRACTOR_API_KEY', '1ab54f24f0e313c8159aebf9cc99ebd0481e5a6275a11110600a0261f6605724')
PORT = int(os.environ.get('PORT', 5682))

class ContentExtractorHandler(BaseHTTPRequestHandler):
    def do_OPTIONS(self):
        """Gère les requêtes OPTIONS pour CORS"""
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', 'https://app.n8n.cloud')
        self.send_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Authorization, Content-Type')
        self.end_headers()
    
    def do_POST(self):
        """Gère les requêtes POST"""
        # Vérifier l'authentification
        auth_header = self.headers.get('Authorization')
        if not auth_header or not auth_header.startswith('Bearer '):
            self.send_error(401, "Token d'authentification manquant")
            return
        
        api_key = auth_header.replace('Bearer ', '')
        if api_key != API_KEY:
            self.send_error(403, "Clé API invalide")
            return
        
        # Lire les données
        try:
            content_length = int(self.headers['Content-Length'])
            post_data = self.rfile.read(content_length)
            data = json.loads(post_data.decode('utf-8'))
        except:
            self.send_error(400, "JSON invalide")
            return
        
        # Router
        if self.path == '/api/v1/get-youtube-transcript':
            self.handle_youtube_transcript(data)
        elif self.path == '/api/v1/scrape':
            self.handle_scrape(data)
        else:
            self.send_error(404, "Endpoint non trouvé")
    
    def do_GET(self):
        """Gère les requêtes GET"""
        if self.path == '/health':
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            response = {
                'status': 'healthy',
                'service': 'content-extractor',
                'version': '2.0.0',
                'supports_shorts': True
            }
            self.wfile.write(json.dumps(response).encode())
        else:
            self.send_error(404, "Not found")
    
    def handle_youtube_transcript(self, data):
        """Gère l'extraction YouTube avec support Shorts"""
        video_url = data.get('videoUrl', '').strip()
        
        if not video_url:
            self.send_error(400, "videoUrl manquant")
            return
        
        # Paramètres
        preferred_language = data.get('preferredLanguage', 'fr')
        include_timestamps = data.get('includeTimestamps', True)
        timestamps_to_combine = data.get('timestampsToCombine', 5)
        
        # Appeler le script mis à jour
        script_path = os.path.join(os.path.dirname(__file__), 'youtube-transcript.py')
        python_bin = os.path.join(os.path.dirname(__file__), 'env/bin/python3')
        
        cmd = [
            python_bin, script_path,
            video_url,
            preferred_language,
            str(include_timestamps).lower(),
            str(timestamps_to_combine)
        ]
        
        try:
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=30)
            
            if result.returncode == 0:
                response_data = json.loads(result.stdout)
                self.send_response(200)
                self.send_header('Content-Type', 'application/json')
                self.send_header('Access-Control-Allow-Origin', 'https://app.n8n.cloud')
                self.end_headers()
                self.wfile.write(json.dumps(response_data).encode())
            else:
                error_msg = result.stderr or "Erreur lors de l'extraction"
                self.send_error(400, error_msg)
                
        except subprocess.TimeoutExpired:
            self.send_error(504, "Timeout lors de l'extraction")
        except Exception as e:
            self.send_error(500, f"Erreur serveur: {str(e)}")
    
    def handle_scrape(self, data):
        """Gère le scraping web"""
        url = data.get('url', '').strip()
        
        if not url:
            self.send_error(400, "URL manquante")
            return
        
        # Paramètres
        format_type = data.get('format', 'markdown')
        cleaned = data.get('cleaned', True)
        
        # Appeler le script
        script_path = os.path.join(os.path.dirname(__file__), 'web-scraper.py')
        python_bin = os.path.join(os.path.dirname(__file__), 'env/bin/python3')
        
        cmd = [
            python_bin, script_path,
            url,
            format_type,
            str(cleaned).lower(),
            'false'
        ]
        
        try:
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=30)
            
            if result.returncode == 0:
                response_data = json.loads(result.stdout)
                self.send_response(200)
                self.send_header('Content-Type', 'application/json')
                self.send_header('Access-Control-Allow-Origin', 'https://app.n8n.cloud')
                self.end_headers()
                self.wfile.write(json.dumps(response_data).encode())
            else:
                error_msg = result.stderr or "Erreur lors du scraping"
                self.send_error(400, error_msg)
                
        except subprocess.TimeoutExpired:
            self.send_error(504, "Timeout lors du scraping")
        except Exception as e:
            self.send_error(500, f"Erreur serveur: {str(e)}")

def run_server():
    print(f"🚀 Content Extractor API v2.0 - Port {PORT}")
    print(f"🎯 Support YouTube Shorts: OUI")
    print(f"🔑 Clé API: {API_KEY[:10]}...")
    print(f"▶️  Démarrage...")
    
    server_address = ('', PORT)
    httpd = HTTPServer(server_address, ContentExtractorHandler)
    
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print("\n🛑 Arrêt du serveur...")
        httpd.shutdown()

if __name__ == '__main__':
    run_server()