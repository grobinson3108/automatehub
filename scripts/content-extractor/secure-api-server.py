#!/usr/bin/env python3
"""
API Server Sécurisé pour Content Extractor
Avec authentification, rate limiting et logging
"""

from http.server import HTTPServer, BaseHTTPRequestHandler
import json
import os
import subprocess
import hashlib
import time
from datetime import datetime
import sqlite3
from collections import defaultdict
import threading
import secrets

# Configuration
API_KEYS = os.environ.get('CONTENT_EXTRACTOR_API_KEYS', '').split(',')
if not API_KEYS[0]:
    # Générer une clé par défaut sécurisée
    API_KEYS = [secrets.token_urlsafe(32)]
    print(f"⚠️  Clé API générée: {API_KEYS[0]}")

PORT = int(os.environ.get('PORT', 5680))
DATABASE_PATH = '/var/www/automatehub/data/content-extractor.db'

# Rate limiting en mémoire
rate_limits = defaultdict(lambda: {'count': 0, 'reset_time': time.time() + 60})
rate_limit_lock = threading.Lock()

class SecureContentExtractorHandler(BaseHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        # Initialiser la base de données
        self.init_db()
        super().__init__(*args, **kwargs)
    
    def init_db(self):
        """Initialise la base de données pour le logging"""
        os.makedirs(os.path.dirname(DATABASE_PATH), exist_ok=True)
        
        conn = sqlite3.connect(DATABASE_PATH)
        cursor = conn.cursor()
        
        cursor.execute('''
        CREATE TABLE IF NOT EXISTS api_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            ip_address TEXT,
            endpoint TEXT,
            method TEXT,
            status_code INTEGER,
            api_key_hash TEXT,
            user_agent TEXT,
            response_time_ms INTEGER
        )
        ''')
        
        cursor.execute('''
        CREATE TABLE IF NOT EXISTS api_usage (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            date DATE,
            endpoint TEXT,
            count INTEGER DEFAULT 1,
            UNIQUE(date, endpoint)
        )
        ''')
        
        conn.commit()
        conn.close()
    
    def log_request(self, endpoint, status_code, response_time_ms, api_key_hash):
        """Enregistre la requête dans la base de données"""
        try:
            conn = sqlite3.connect(DATABASE_PATH)
            cursor = conn.cursor()
            
            cursor.execute('''
            INSERT INTO api_logs (ip_address, endpoint, method, status_code, 
                                 api_key_hash, user_agent, response_time_ms)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ''', (
                self.headers.get('X-Real-IP', self.client_address[0]),
                endpoint,
                self.command,
                status_code,
                api_key_hash,
                self.headers.get('User-Agent', ''),
                response_time_ms
            ))
            
            # Mettre à jour les statistiques d'usage
            cursor.execute('''
            INSERT OR REPLACE INTO api_usage (date, endpoint, count)
            VALUES (
                DATE('now'),
                ?,
                COALESCE((SELECT count + 1 FROM api_usage 
                         WHERE date = DATE('now') AND endpoint = ?), 1)
            )
            ''', (endpoint, endpoint))
            
            conn.commit()
            conn.close()
        except Exception as e:
            print(f"Erreur logging: {e}")
    
    def check_rate_limit(self, ip):
        """Vérifie le rate limit pour une IP"""
        with rate_limit_lock:
            current_time = time.time()
            
            # Réinitialiser si la période est écoulée
            if current_time > rate_limits[ip]['reset_time']:
                rate_limits[ip] = {'count': 1, 'reset_time': current_time + 60}
                return True
            
            # Vérifier la limite
            if rate_limits[ip]['count'] >= 100:  # 100 requêtes par minute
                return False
            
            rate_limits[ip]['count'] += 1
            return True
    
    def authenticate(self):
        """Vérifie l'authentification et le rate limit"""
        start_time = time.time()
        
        # Vérifier le header Authorization
        auth_header = self.headers.get('Authorization')
        if not auth_header or not auth_header.startswith('Bearer '):
            return None, 401, "Token d'authentification manquant"
        
        api_key = auth_header.replace('Bearer ', '')
        api_key_hash = hashlib.sha256(api_key.encode()).hexdigest()[:16]
        
        # Vérifier la clé API
        if api_key not in API_KEYS:
            self.log_request(self.path, 403, int((time.time() - start_time) * 1000), api_key_hash)
            return None, 403, "Clé API invalide"
        
        # Vérifier le rate limit
        ip = self.headers.get('X-Real-IP', self.client_address[0])
        if not self.check_rate_limit(ip):
            self.log_request(self.path, 429, int((time.time() - start_time) * 1000), api_key_hash)
            return None, 429, "Trop de requêtes. Limite: 100/minute"
        
        return api_key_hash, 200, None
    
    def do_POST(self):
        start_time = time.time()
        
        # Authentification
        api_key_hash, status, error = self.authenticate()
        if error:
            self.send_error(status, error)
            return
        
        # Lire le body
        try:
            content_length = int(self.headers['Content-Length'])
            if content_length > 10 * 1024 * 1024:  # 10MB max
                self.send_error(413, "Payload trop large")
                return
                
            post_data = self.rfile.read(content_length)
            data = json.loads(post_data.decode('utf-8'))
        except:
            self.send_error(400, "JSON invalide")
            return
        
        # Router vers la bonne fonction
        if self.path == '/api/v1/get-youtube-transcript':
            self.handle_youtube_transcript(data, api_key_hash, start_time)
        elif self.path == '/api/v1/scrape':
            self.handle_scrape(data, api_key_hash, start_time)
        else:
            self.send_error(404, "Endpoint non trouvé")
            self.log_request(self.path, 404, int((time.time() - start_time) * 1000), api_key_hash)
    
    def do_GET(self):
        start_time = time.time()
        
        if self.path == '/health':
            # Health check sans auth
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            response = {
                'status': 'healthy',
                'service': 'content-extractor',
                'version': '1.0.0',
                'secure': True
            }
            self.wfile.write(json.dumps(response).encode())
        elif self.path == '/api/v1/stats':
            # Stats avec auth
            api_key_hash, status, error = self.authenticate()
            if error:
                self.send_error(status, error)
                return
            self.handle_stats(api_key_hash, start_time)
        else:
            self.send_error(404, "Not found")
    
    def handle_youtube_transcript(self, data, api_key_hash, start_time):
        """Gère l'extraction YouTube de manière sécurisée"""
        video_url = data.get('videoUrl', '').strip()
        
        # Validation de l'URL
        if not video_url or not ('youtube.com' in video_url or 'youtu.be' in video_url):
            self.send_error(400, "URL YouTube invalide")
            return
        
        # Limiter les paramètres
        preferred_language = data.get('preferredLanguage', 'fr')[:5]
        include_timestamps = bool(data.get('includeTimestamps', True))
        timestamps_to_combine = min(int(data.get('timestampsToCombine', 5)), 10)
        
        # Appeler le script
        script_path = os.path.join(os.path.dirname(__file__), 'youtube-transcript.py')
        python_bin = os.path.join(os.path.dirname(__file__), 'env/bin/python3')
        if not os.path.exists(python_bin):
            python_bin = 'python3'
        
        cmd = [
            python_bin, script_path,
            video_url,
            preferred_language,
            str(include_timestamps),
            str(timestamps_to_combine)
        ]
        
        try:
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=30)
            response_time = int((time.time() - start_time) * 1000)
            
            if result.returncode == 0:
                response_data = json.loads(result.stdout)
                self.send_response(200)
                self.send_header('Content-Type', 'application/json')
                self.end_headers()
                self.wfile.write(json.dumps(response_data).encode())
                self.log_request('/api/v1/get-youtube-transcript', 200, response_time, api_key_hash)
            else:
                self.send_error(400, "Erreur lors de l'extraction")
                self.log_request('/api/v1/get-youtube-transcript', 400, response_time, api_key_hash)
                
        except subprocess.TimeoutExpired:
            self.send_error(504, "Timeout lors de l'extraction")
            self.log_request('/api/v1/get-youtube-transcript', 504, int((time.time() - start_time) * 1000), api_key_hash)
        except Exception as e:
            self.send_error(500, f"Erreur serveur: {str(e)}")
            self.log_request('/api/v1/get-youtube-transcript', 500, int((time.time() - start_time) * 1000), api_key_hash)
    
    def handle_scrape(self, data, api_key_hash, start_time):
        """Gère le scraping web de manière sécurisée"""
        url = data.get('url', '').strip()
        
        # Validation basique de l'URL
        if not url or not url.startswith(('http://', 'https://')):
            self.send_error(400, "URL invalide")
            return
        
        # Bloquer les IPs privées
        if any(blocked in url for blocked in ['localhost', '127.0.0.1', '192.168.', '10.', '172.']):
            self.send_error(403, "URL non autorisée")
            return
        
        # Paramètres
        format_type = data.get('format', 'markdown')
        if format_type not in ['markdown', 'html']:
            format_type = 'markdown'
        
        cleaned = bool(data.get('cleaned', True))
        
        # Appeler le script
        script_path = os.path.join(os.path.dirname(__file__), 'web-scraper.py')
        python_bin = os.path.join(os.path.dirname(__file__), 'env/bin/python3')
        if not os.path.exists(python_bin):
            python_bin = 'python3'
        
        cmd = [
            python_bin, script_path,
            url,
            format_type,
            str(cleaned),
            'false'  # Pas de JS
        ]
        
        try:
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=30)
            response_time = int((time.time() - start_time) * 1000)
            
            if result.returncode == 0:
                response_data = json.loads(result.stdout)
                self.send_response(200)
                self.send_header('Content-Type', 'application/json')
                self.end_headers()
                self.wfile.write(json.dumps(response_data).encode())
                self.log_request('/api/v1/scrape', 200, response_time, api_key_hash)
            else:
                self.send_error(400, "Erreur lors du scraping")
                self.log_request('/api/v1/scrape', 400, response_time, api_key_hash)
                
        except subprocess.TimeoutExpired:
            self.send_error(504, "Timeout lors du scraping")
            self.log_request('/api/v1/scrape', 504, int((time.time() - start_time) * 1000), api_key_hash)
        except Exception as e:
            self.send_error(500, f"Erreur serveur: {str(e)}")
            self.log_request('/api/v1/scrape', 500, int((time.time() - start_time) * 1000), api_key_hash)
    
    def handle_stats(self, api_key_hash, start_time):
        """Retourne les statistiques d'usage"""
        try:
            conn = sqlite3.connect(DATABASE_PATH)
            cursor = conn.cursor()
            
            # Stats du jour
            cursor.execute('''
            SELECT endpoint, count
            FROM api_usage
            WHERE date = DATE('now')
            ''')
            today_stats = dict(cursor.fetchall())
            
            # Stats totales
            cursor.execute('''
            SELECT endpoint, SUM(count)
            FROM api_usage
            GROUP BY endpoint
            ''')
            total_stats = dict(cursor.fetchall())
            
            conn.close()
            
            response = {
                'today': today_stats,
                'total': total_stats,
                'timestamp': datetime.now().isoformat()
            }
            
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            self.wfile.write(json.dumps(response).encode())
            
        except Exception as e:
            self.send_error(500, f"Erreur: {str(e)}")
        
        self.log_request('/api/v1/stats', 200, int((time.time() - start_time) * 1000), api_key_hash)

def run_server():
    print(f"🔐 Content Extractor API Sécurisé - Port {PORT}")
    print(f"🔑 {len(API_KEYS)} clé(s) API configurée(s)")
    print(f"📊 Logs: {DATABASE_PATH}")
    print(f"🚀 Démarrage...")
    
    server_address = ('', PORT)
    httpd = HTTPServer(server_address, SecureContentExtractorHandler)
    
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        print("\n🛑 Arrêt du serveur...")
        httpd.shutdown()

if __name__ == '__main__':
    run_server()