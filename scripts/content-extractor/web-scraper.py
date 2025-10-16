#!/usr/bin/env python3
"""
Web Scraper Simple - Sans Selenium
"""

import sys
import json
import requests
from bs4 import BeautifulSoup
import html2text
from urllib.parse import urljoin, urlparse

class WebScraper:
    def __init__(self):
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        })
    
    def scrape(self, url, format='markdown', cleaned=True, render_js=False):
        """
        Fonction principale de scraping
        
        Args:
            url: URL à scraper
            format: Format de sortie ('markdown' ou 'html')
            cleaned: Nettoyer le contenu
            render_js: Ignoré dans cette version simple
        """
        try:
            # Télécharger la page
            response = self.session.get(url, timeout=30)
            response.raise_for_status()
            html = response.text
            
            # Parser avec BeautifulSoup
            soup = BeautifulSoup(html, 'html.parser')
            
            # Extraire les métadonnées
            metadata = {
                'title': '',
                'description': '',
                'author': '',
                'published_date': ''
            }
            
            # Titre
            title_tag = soup.find('title')
            if title_tag:
                metadata['title'] = title_tag.get_text().strip()
            
            # Meta description
            meta_desc = soup.find('meta', attrs={'name': 'description'}) or \
                       soup.find('meta', attrs={'property': 'og:description'})
            if meta_desc:
                metadata['description'] = meta_desc.get('content', '')
            
            # Auteur
            meta_author = soup.find('meta', attrs={'name': 'author'}) or \
                         soup.find('meta', attrs={'property': 'article:author'})
            if meta_author:
                metadata['author'] = meta_author.get('content', '')
            
            # Nettoyer le HTML
            if cleaned:
                # Supprimer les scripts et styles
                for script in soup(['script', 'style', 'noscript']):
                    script.decompose()
                
                # Supprimer les éléments de navigation
                for elem in soup(['nav', 'header', 'footer', 'aside']):
                    elem.decompose()
            
            # Chercher le contenu principal
            main_content = soup.find('main') or soup.find('article') or \
                          soup.find('div', class_=['content', 'post-content', 'entry-content']) or \
                          soup.find('body') or soup
            
            # Convertir en format demandé
            if format == 'markdown':
                h = html2text.HTML2Text()
                h.ignore_links = False
                h.ignore_images = False
                h.body_width = 0
                content = h.handle(str(main_content))
            else:
                content = str(main_content)
            
            return {
                'success': True,
                'url': url,
                'title': metadata['title'],
                'metadata': metadata,
                'content': content,
                'format': format,
                'cleaned': cleaned,
                'rendered_js': False
            }
            
        except Exception as e:
            return {
                'success': False,
                'url': url,
                'error': str(e)
            }

def main():
    """Fonction principale pour utilisation en ligne de commande"""
    if len(sys.argv) < 2:
        print("Usage: python web-scraper-simple.py <url> [format] [cleaned] [render_js]")
        sys.exit(1)
    
    url = sys.argv[1]
    format = sys.argv[2] if len(sys.argv) > 2 else 'markdown'
    cleaned = sys.argv[3].lower() == 'true' if len(sys.argv) > 3 else True
    render_js = False  # Toujours false dans cette version
    
    scraper = WebScraper()
    result = scraper.scrape(url, format, cleaned, render_js)
    
    print(json.dumps(result, ensure_ascii=False, indent=2))

if __name__ == "__main__":
    main()