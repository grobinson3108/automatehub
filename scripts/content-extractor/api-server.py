#!/usr/bin/env python3
"""
API Server pour Content Extractor
Remplace les endpoints Dumpling AI
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import os
import sys
import json
import hashlib
import time
from datetime import datetime
import sqlite3

# Importer nos modules
sys.path.append(os.path.dirname(os.path.abspath(__file__)))
from youtube_transcript import get_transcript
from web_scraper import WebScraper

app = Flask(__name__)
CORS(app)

# Configuration
API_KEY = os.environ.get('CONTENT_EXTRACTOR_API_KEY', 'default-api-key-change-me')
DATABASE_PATH = '/var/www/automatehub/data/usage.db'

# Initialiser la base de données pour le tracking
def init_db():
    """Initialise la base de données pour le tracking d'usage"""
    os.makedirs(os.path.dirname(DATABASE_PATH), exist_ok=True)
    
    conn = sqlite3.connect(DATABASE_PATH)
    cursor = conn.cursor()
    
    cursor.execute('''
    CREATE TABLE IF NOT EXISTS usage_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id TEXT NOT NULL,
        api_key TEXT NOT NULL,
        endpoint TEXT NOT NULL,
        request_data TEXT,
        response_status INTEGER,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        credits_used REAL DEFAULT 0.1
    )
    ''')
    
    cursor.execute('''
    CREATE TABLE IF NOT EXISTS api_keys (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        api_key TEXT UNIQUE NOT NULL,
        user_email TEXT,
        user_name TEXT,
        credits_remaining REAL DEFAULT 100.0,
        subscription_type TEXT DEFAULT 'pay-as-you-go',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        is_active BOOLEAN DEFAULT TRUE
    )
    ''')
    
    # Ajouter la clé par défaut si elle n'existe pas
    cursor.execute('''
    INSERT OR IGNORE INTO api_keys (api_key, user_email, user_name, credits_remaining)
    VALUES (?, ?, ?, ?)
    ''', (API_KEY, 'admin@automatehub.fr', 'Admin', 1000.0))
    
    conn.commit()
    conn.close()

def check_api_key(api_key):
    """Vérifie la validité de la clé API et les crédits"""
    conn = sqlite3.connect(DATABASE_PATH)
    cursor = conn.cursor()
    
    cursor.execute('''
    SELECT credits_remaining, is_active, user_email
    FROM api_keys 
    WHERE api_key = ?
    ''', (api_key,))
    
    result = cursor.fetchone()
    conn.close()
    
    if not result:
        return False, "Clé API invalide"
    
    credits, is_active, email = result
    
    if not is_active:
        return False, "Clé API désactivée"
    
    if credits <= 0:
        return False, "Crédits épuisés"
    
    return True, {"credits": credits, "email": email}

def log_usage(api_key, endpoint, request_data, response_status, credits_used=0.1):
    """Enregistre l'usage et déduit les crédits"""
    conn = sqlite3.connect(DATABASE_PATH)
    cursor = conn.cursor()
    
    # Enregistrer l'usage
    cursor.execute('''
    INSERT INTO usage_logs (user_id, api_key, endpoint, request_data, response_status, credits_used)
    VALUES (
        (SELECT user_email FROM api_keys WHERE api_key = ?),
        ?, ?, ?, ?, ?
    )
    ''', (api_key, api_key, endpoint, json.dumps(request_data), response_status, credits_used))
    
    # Déduire les crédits
    cursor.execute('''
    UPDATE api_keys 
    SET credits_remaining = credits_remaining - ?
    WHERE api_key = ?
    ''', (credits_used, api_key))
    
    conn.commit()
    conn.close()

def authenticate():
    """Middleware d'authentification"""
    auth_header = request.headers.get('Authorization')
    
    if not auth_header or not auth_header.startswith('Bearer '):
        return None, jsonify({'error': 'Token d\'authentification manquant'}), 401
    
    api_key = auth_header.replace('Bearer ', '')
    
    valid, result = check_api_key(api_key)
    if not valid:
        return None, jsonify({'error': result}), 403
    
    return api_key, None, result

@app.route('/api/v1/get-youtube-transcript', methods=['POST'])
def get_youtube_transcript_api():
    """Endpoint pour récupérer les transcriptions YouTube"""
    api_key, error_response, user_info = authenticate()
    if error_response:
        return error_response
    
    try:
        data = request.get_json()
        
        # Paramètres
        video_url = data.get('videoUrl')
        if not video_url:
            return jsonify({'error': 'URL de vidéo manquante'}), 400
        
        include_timestamps = data.get('includeTimestamps', True)
        timestamps_to_combine = data.get('timestampsToCombine', 5)
        preferred_language = data.get('preferredLanguage', 'fr')
        
        # Obtenir la transcription
        result = get_transcript(
            video_url,
            preferred_language,
            include_timestamps,
            timestamps_to_combine
        )
        
        # Log usage
        log_usage(api_key, '/api/v1/get-youtube-transcript', data, 200 if result.get('success') else 400)
        
        if result.get('success'):
            return jsonify(result)
        else:
            return jsonify(result), 400
            
    except Exception as e:
        log_usage(api_key, '/api/v1/get-youtube-transcript', {}, 500)
        return jsonify({'error': str(e)}), 500

@app.route('/api/v1/scrape', methods=['POST'])
def scrape_api():
    """Endpoint pour scraper des pages web"""
    api_key, error_response, user_info = authenticate()
    if error_response:
        return error_response
    
    try:
        data = request.get_json()
        
        # Paramètres
        url = data.get('url')
        if not url:
            return jsonify({'error': 'URL manquante'}), 400
        
        format = data.get('format', 'markdown')
        cleaned = data.get('cleaned', True)
        render_js = data.get('renderJs', False)
        
        # Scraper
        scraper = WebScraper()
        result = scraper.scrape(url, format, cleaned, render_js)
        
        # Log usage
        log_usage(api_key, '/api/v1/scrape', data, 200 if result.get('success') else 400)
        
        if result.get('success'):
            return jsonify(result)
        else:
            return jsonify(result), 400
            
    except Exception as e:
        log_usage(api_key, '/api/v1/scrape', {}, 500)
        return jsonify({'error': str(e)}), 500

@app.route('/api/v1/credits', methods=['GET'])
def get_credits():
    """Endpoint pour vérifier les crédits restants"""
    api_key, error_response, user_info = authenticate()
    if error_response:
        return error_response
    
    return jsonify({
        'credits_remaining': user_info['credits'],
        'email': user_info['email']
    })

@app.route('/api/v1/usage', methods=['GET'])
def get_usage():
    """Endpoint pour obtenir l'historique d'usage"""
    api_key, error_response, user_info = authenticate()
    if error_response:
        return error_response
    
    conn = sqlite3.connect(DATABASE_PATH)
    cursor = conn.cursor()
    
    cursor.execute('''
    SELECT endpoint, COUNT(*) as count, SUM(credits_used) as total_credits,
           DATE(timestamp) as date
    FROM usage_logs
    WHERE api_key = ?
    GROUP BY endpoint, DATE(timestamp)
    ORDER BY date DESC
    LIMIT 30
    ''', (api_key,))
    
    usage = []
    for row in cursor.fetchall():
        usage.append({
            'endpoint': row[0],
            'count': row[1],
            'credits_used': row[2],
            'date': row[3]
        })
    
    conn.close()
    
    return jsonify({
        'usage': usage,
        'credits_remaining': user_info['credits']
    })

@app.route('/api/v1/admin/create-api-key', methods=['POST'])
def create_api_key():
    """Endpoint admin pour créer de nouvelles clés API"""
    # Vérifier le token admin
    admin_token = request.headers.get('X-Admin-Token')
    if admin_token != os.environ.get('ADMIN_TOKEN', 'admin-secret-token'):
        return jsonify({'error': 'Non autorisé'}), 403
    
    data = request.get_json()
    
    # Générer une nouvelle clé API
    new_key = hashlib.sha256(f"{data.get('email', '')}{time.time()}".encode()).hexdigest()[:32]
    
    conn = sqlite3.connect(DATABASE_PATH)
    cursor = conn.cursor()
    
    try:
        cursor.execute('''
        INSERT INTO api_keys (api_key, user_email, user_name, credits_remaining, subscription_type)
        VALUES (?, ?, ?, ?, ?)
        ''', (
            new_key,
            data.get('email'),
            data.get('name'),
            data.get('initial_credits', 100.0),
            data.get('subscription_type', 'pay-as-you-go')
        ))
        conn.commit()
        
        return jsonify({
            'api_key': new_key,
            'message': 'Clé API créée avec succès'
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 400
    finally:
        conn.close()

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({'status': 'healthy', 'service': 'content-extractor'})

if __name__ == '__main__':
    init_db()
    port = int(os.environ.get('PORT', 5000))
    app.run(host='0.0.0.0', port=port, debug=False)