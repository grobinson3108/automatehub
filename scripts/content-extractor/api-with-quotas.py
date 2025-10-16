#!/usr/bin/env python3
"""
API Content Extractor avec système de quotas
Pour les abonnés Skool et autres utilisateurs
"""

import os
import json
import sqlite3
from datetime import datetime, date
from http.server import HTTPServer, BaseHTTPRequestHandler
import hashlib
import subprocess

# Configuration
PORT = int(os.environ.get('PORT', 5682))
DATABASE_PATH = '/var/www/automatehub/data/content-extractor-quotas.db'
SKOOL_API_KEY = os.environ.get('SKOOL_API_KEY', 'skool-secret-key')

class QuotaManager:
    def __init__(self):
        self.init_db()
    
    def init_db(self):
        """Initialise la base de données des quotas"""
        os.makedirs(os.path.dirname(DATABASE_PATH), exist_ok=True)
        
        conn = sqlite3.connect(DATABASE_PATH)
        cursor = conn.cursor()
        
        # Table des utilisateurs et quotas
        cursor.execute('''
        CREATE TABLE IF NOT EXISTS user_quotas (
            user_id VARCHAR(255) PRIMARY KEY,
            email VARCHAR(255),
            subscription_type VARCHAR(50) DEFAULT 'free',
            monthly_quota INTEGER DEFAULT 10,
            used_this_month INTEGER DEFAULT 0,
            extra_credits INTEGER DEFAULT 0,
            reset_date DATE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
        ''')
        
        # Table des clés API
        cursor.execute('''
        CREATE TABLE IF NOT EXISTS api_keys (
            api_key VARCHAR(255) PRIMARY KEY,
            user_id VARCHAR(255),
            is_active BOOLEAN DEFAULT TRUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(user_id) REFERENCES user_quotas(user_id)
        )
        ''')
        
        # Table des usages
        cursor.execute('''
        CREATE TABLE IF NOT EXISTS usage_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id VARCHAR(255),
            endpoint VARCHAR(255),
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            credits_used INTEGER DEFAULT 1
        )
        ''')
        
        conn.commit()
        conn.close()
    
    def check_quota(self, api_key):
        """Vérifie le quota d'un utilisateur"""
        conn = sqlite3.connect(DATABASE_PATH)
        cursor = conn.cursor()
        
        # Récupérer l'utilisateur depuis la clé API
        cursor.execute('''
        SELECT u.user_id, u.subscription_type, u.monthly_quota, 
               u.used_this_month, u.extra_credits, u.reset_date
        FROM api_keys k
        JOIN user_quotas u ON k.user_id = u.user_id
        WHERE k.api_key = ? AND k.is_active = TRUE
        ''', (api_key,))
        
        result = cursor.fetchone()
        if not result:
            conn.close()
            return False, "Clé API invalide"
        
        user_id, sub_type, monthly_quota, used, extra, reset_date = result
        
        # Vérifier si on doit réinitialiser le compteur mensuel
        today = date.today()
        if not reset_date or date.fromisoformat(reset_date) < date(today.year, today.month, 1):
            cursor.execute('''
            UPDATE user_quotas 
            SET used_this_month = 0, reset_date = ? 
            WHERE user_id = ?
            ''', (today.isoformat(), user_id))
            used = 0
            conn.commit()
        
        # Vérifier le quota
        if used < monthly_quota:
            # Utiliser le quota mensuel
            cursor.execute('''
            UPDATE user_quotas 
            SET used_this_month = used_this_month + 1 
            WHERE user_id = ?
            ''', (user_id,))
        elif extra > 0:
            # Utiliser les crédits supplémentaires
            cursor.execute('''
            UPDATE user_quotas 
            SET extra_credits = extra_credits - 1 
            WHERE user_id = ?
            ''', (user_id,))
        else:
            conn.close()
            return False, f"Quota dépassé ({used}/{monthly_quota})"
        
        # Logger l'usage
        cursor.execute('''
        INSERT INTO usage_logs (user_id, endpoint) 
        VALUES (?, ?)
        ''', (user_id, 'extraction'))
        
        conn.commit()
        conn.close()
        
        return True, {
            'user_id': user_id,
            'subscription': sub_type,
            'used': used + 1,
            'limit': monthly_quota,
            'extra': extra
        }
    
    def create_user(self, email, subscription_type='free'):
        """Crée un nouvel utilisateur"""
        conn = sqlite3.connect(DATABASE_PATH)
        cursor = conn.cursor()
        
        # Déterminer le quota selon le type
        quotas = {
            'free': 10,
            'skool': 100,
            'pro': 200,
            'unlimited': 999999
        }
        
        user_id = hashlib.sha256(email.encode()).hexdigest()[:16]
        api_key = hashlib.sha256(f"{email}{datetime.now()}".encode()).hexdigest()
        
        try:
            cursor.execute('''
            INSERT INTO user_quotas (user_id, email, subscription_type, monthly_quota, reset_date)
            VALUES (?, ?, ?, ?, ?)
            ''', (user_id, email, subscription_type, quotas.get(subscription_type, 10), date.today().isoformat()))
            
            cursor.execute('''
            INSERT INTO api_keys (api_key, user_id)
            VALUES (?, ?)
            ''', (api_key, user_id))
            
            conn.commit()
            conn.close()
            
            return True, api_key
        except Exception as e:
            conn.close()
            return False, str(e)

class ContentExtractorHandler(BaseHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        self.quota_manager = QuotaManager()
        super().__init__(*args, **kwargs)
    
    def do_POST(self):
        if self.path == '/api/v1/get-youtube-transcript':
            self.handle_youtube_transcript()
        elif self.path == '/api/v1/scrape':
            self.handle_scrape()
        elif self.path == '/api/v1/admin/create-user':
            self.handle_create_user()
        else:
            self.send_error(404, "Endpoint non trouvé")
    
    def do_GET(self):
        if self.path == '/health':
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            response = {'status': 'healthy', 'service': 'content-extractor-quotas'}
            self.wfile.write(json.dumps(response).encode())
        elif self.path == '/api/v1/quota':
            self.handle_quota_check()
        else:
            self.send_error(404, "Not found")
    
    def authenticate_and_check_quota(self):
        """Authentifie et vérifie le quota"""
        auth_header = self.headers.get('Authorization')
        if not auth_header or not auth_header.startswith('Bearer '):
            return None, "Token d'authentification manquant"
        
        api_key = auth_header.replace('Bearer ', '')
        
        # Vérifier le quota
        has_quota, result = self.quota_manager.check_quota(api_key)
        if not has_quota:
            return None, result
        
        return result, None
    
    def handle_youtube_transcript(self):
        """Gère l'extraction YouTube avec vérification de quota"""
        # Authentifier et vérifier le quota
        quota_info, error = self.authenticate_and_check_quota()
        if error:
            self.send_error(403, error)
            return
        
        # Lire le body
        try:
            content_length = int(self.headers['Content-Length'])
            post_data = self.rfile.read(content_length)
            data = json.loads(post_data.decode('utf-8'))
        except:
            self.send_error(400, "JSON invalide")
            return
        
        # Extraire la transcription
        video_url = data.get('videoUrl')
        if not video_url:
            self.send_error(400, "URL de vidéo manquante")
            return
        
        # Appeler le script d'extraction
        script_path = os.path.join(os.path.dirname(__file__), 'youtube-transcript.py')
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
                # Ajouter les infos de quota
                response_data['quota_info'] = quota_info
                
                self.send_response(200)
                self.send_header('Content-Type', 'application/json')
                self.end_headers()
                self.wfile.write(json.dumps(response_data).encode())
            else:
                self.send_error(400, "Erreur lors de l'extraction")
                
        except Exception as e:
            self.send_error(500, str(e))
    
    def handle_scrape(self):
        """Gère le scraping web avec vérification de quota"""
        # Même logique que YouTube
        quota_info, error = self.authenticate_and_check_quota()
        if error:
            self.send_error(403, error)
            return
        
        # ... (reste de l'implémentation similaire)
    
    def handle_create_user(self):
        """Endpoint admin pour créer un utilisateur"""
        # Vérifier le token admin
        admin_token = self.headers.get('X-Admin-Token')
        if admin_token != SKOOL_API_KEY:
            self.send_error(403, "Non autorisé")
            return
        
        try:
            content_length = int(self.headers['Content-Length'])
            post_data = self.rfile.read(content_length)
            data = json.loads(post_data.decode('utf-8'))
            
            success, api_key = self.quota_manager.create_user(
                data.get('email'),
                data.get('subscription_type', 'free')
            )
            
            if success:
                self.send_response(200)
                self.send_header('Content-Type', 'application/json')
                self.end_headers()
                response = {'api_key': api_key, 'message': 'Utilisateur créé'}
                self.wfile.write(json.dumps(response).encode())
            else:
                self.send_error(400, api_key)
                
        except Exception as e:
            self.send_error(500, str(e))
    
    def handle_quota_check(self):
        """Vérifie le quota restant"""
        auth_header = self.headers.get('Authorization')
        if not auth_header:
            self.send_error(401, "Token manquant")
            return
        
        api_key = auth_header.replace('Bearer ', '')
        
        conn = sqlite3.connect(DATABASE_PATH)
        cursor = conn.cursor()
        
        cursor.execute('''
        SELECT u.subscription_type, u.monthly_quota, u.used_this_month, u.extra_credits
        FROM api_keys k
        JOIN user_quotas u ON k.user_id = u.user_id
        WHERE k.api_key = ?
        ''', (api_key,))
        
        result = cursor.fetchone()
        conn.close()
        
        if result:
            sub_type, quota, used, extra = result
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.end_headers()
            response = {
                'subscription': sub_type,
                'monthly_limit': quota,
                'used': used,
                'remaining': quota - used,
                'extra_credits': extra
            }
            self.wfile.write(json.dumps(response).encode())
        else:
            self.send_error(404, "Utilisateur non trouvé")

def run_server():
    print(f"🚀 Content Extractor avec Quotas - Port {PORT}")
    server_address = ('', PORT)
    httpd = HTTPServer(server_address, ContentExtractorHandler)
    httpd.serve_forever()

if __name__ == '__main__':
    run_server()