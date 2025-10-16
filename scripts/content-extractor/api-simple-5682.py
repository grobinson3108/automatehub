#!/usr/bin/env python3
"""
API Simple pour Content Extractor - Port 5682
Version fonctionnelle pour n8n
"""

from http.server import HTTPServer, BaseHTTPRequestHandler
import json
import os
import subprocess
import sys

# Configuration
API_KEY = "1ab54f24f0e313c8159aebf9cc99ebd0481e5a6275a11110600a0261f6605724"
PORT = 5682

class SimpleHandler(BaseHTTPRequestHandler):
    def log_message(self, format, *args):
        """Désactive les logs automatiques"""
        pass
    
    def do_OPTIONS(self):
        """CORS preflight"""
        self.send_response(200)
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Authorization, Content-Type')
        self.end_headers()
    
    def do_GET(self):
        """Health check"""
        if self.path == '/health':
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Access-Control-Allow-Origin', '*')
            self.end_headers()
            response = {"status": "OK", "port": PORT, "supports_shorts": True}
            self.wfile.write(json.dumps(response).encode())
        else:
            self.send_error(404)
    
    def do_POST(self):
        """Handle POST requests"""
        # Vérifier auth
        auth = self.headers.get('Authorization', '')
        if not auth.startswith('Bearer ') or auth.replace('Bearer ', '') != API_KEY:
            self.send_error(401, "Invalid API Key")
            return
        
        # Lire données
        try:
            length = int(self.headers.get('content-length', 0))
            body = self.rfile.read(length).decode('utf-8')
            data = json.loads(body)
        except:
            self.send_error(400, "Invalid JSON")
            return
        
        if self.path == '/api/v1/get-youtube-transcript':
            self.handle_youtube(data)
        else:
            self.send_error(404, "Not found")
    
    def handle_youtube(self, data):
        """Process YouTube transcript"""
        url = data.get('videoUrl', '').strip()
        if not url:
            self.send_error(400, "Missing videoUrl")
            return
        
        # Appeler le script
        script_dir = os.path.dirname(os.path.abspath(__file__))
        python_path = os.path.join(script_dir, 'env', 'bin', 'python3')
        script_path = os.path.join(script_dir, 'youtube-transcript.py')
        
        cmd = [python_path, script_path, url, 'fr', 'true', '5']
        
        try:
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=30)
            
            if result.returncode == 0:
                # Succès
                response_data = json.loads(result.stdout)
                self.send_response(200)
                self.send_header('Content-Type', 'application/json')
                self.send_header('Access-Control-Allow-Origin', '*')
                self.end_headers()
                self.wfile.write(json.dumps(response_data).encode())
                print(f"✅ SUCCESS: {url}")
            else:
                # Erreur
                error = result.stderr or "Extraction failed"
                self.send_error(400, error)
                print(f"❌ ERROR: {url} - {error}")
                
        except subprocess.TimeoutExpired:
            self.send_error(504, "Timeout")
            print(f"⏰ TIMEOUT: {url}")
        except Exception as e:
            self.send_error(500, str(e))
            print(f"💥 EXCEPTION: {url} - {e}")

def main():
    print(f"🚀 Starting Content Extractor API")
    print(f"📍 Port: {PORT}")
    print(f"🔑 API Key: {API_KEY[:10]}...")
    print(f"🎯 YouTube Shorts: SUPPORTED")
    
    try:
        server = HTTPServer(('', PORT), SimpleHandler)
        print(f"✅ Server ready at http://localhost:{PORT}")
        print(f"🔗 Health: http://localhost:{PORT}/health")
        print(f"📡 Endpoint: /api/v1/get-youtube-transcript")
        print(f"⚡ Starting...")
        server.serve_forever()
    except KeyboardInterrupt:
        print("\n🛑 Shutting down...")
        server.shutdown()
    except Exception as e:
        print(f"💥 Error: {e}")
        sys.exit(1)

if __name__ == '__main__':
    main()