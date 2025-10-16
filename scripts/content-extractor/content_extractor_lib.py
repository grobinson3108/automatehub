#!/usr/bin/env python3
"""
Librairie Content Extractor pour import dans l'API Laravel
"""

import re
import requests
from bs4 import BeautifulSoup
import html2text
from urllib.parse import urlparse
from youtube_transcript_api import YouTubeTranscriptApi
import logging

# Configuration du logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

def extract_youtube_transcript(url, language='fr', include_timestamps=False, timestamps_to_combine=5):
    """Extraire la transcription d'une vidéo YouTube"""
    try:
        # Extraire l'ID de la vidéo
        video_id = None
        if 'youtube.com/watch' in url:
            match = re.search(r'v=([^&]+)', url)
            if match:
                video_id = match.group(1)
        elif 'youtu.be/' in url:
            match = re.search(r'youtu\.be/([^?]+)', url)
            if match:
                video_id = match.group(1)
        
        if not video_id:
            return {'error': 'URL YouTube invalide'}
        
        # Récupérer la transcription avec la nouvelle API
        try:
            api = YouTubeTranscriptApi()
            transcript = api.fetch(video_id, languages=[language, 'en'])
            # Convertir les snippets en format compatible
            transcript_list = [{
                'text': snippet.text,
                'start': snippet.start,
                'duration': snippet.duration
            } for snippet in transcript.snippets]
        except Exception:
            try:
                api = YouTubeTranscriptApi()
                transcript = api.fetch(video_id)
                transcript_list = [{
                    'text': snippet.text,
                    'start': snippet.start,
                    'duration': snippet.duration
                } for snippet in transcript.snippets]
            except Exception as e:
                return {'error': f'Impossible de récupérer la transcription: {str(e)}'}
        
        # Formater la transcription
        if include_timestamps:
            full_transcript = []
            for entry in transcript_list:
                timestamp = f"[{int(entry['start'])}s]"
                full_transcript.append(f"{timestamp} {entry['text']}")
            transcript_text = '\n'.join(full_transcript)
        else:
            # Combiner les entrées proches dans le temps
            combined_transcript = []
            current_group = []
            current_start = 0
            
            for i, entry in enumerate(transcript_list):
                if not current_group:
                    current_start = entry['start']
                
                current_group.append(entry['text'])
                
                # Vérifier si on doit créer un nouveau groupe
                if i == len(transcript_list) - 1 or (i < len(transcript_list) - 1 and 
                    transcript_list[i + 1]['start'] - current_start > timestamps_to_combine):
                    combined_transcript.append(' '.join(current_group))
                    current_group = []
            
            transcript_text = '\n\n'.join(combined_transcript)
        
        return {
            'video_id': video_id,
            'transcript': transcript_text,
            'language': transcript_list[0].get('language', language) if transcript_list else language,
            'word_count': len(transcript_text.split()),
            'url': url
        }
        
    except Exception as e:
        logger.error(f"Erreur extraction YouTube: {e}")
        return {'error': str(e)}

def scrape_web_page(url, format_type='markdown', include_images=True):
    """Scraper une page web et extraire le contenu"""
    try:
        # Headers pour simuler un navigateur
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        }
        
        # Récupérer la page
        response = requests.get(url, headers=headers, timeout=10)
        response.raise_for_status()
        
        # Parser le HTML
        soup = BeautifulSoup(response.text, 'html.parser')
        
        # Extraire les métadonnées
        title = soup.find('title').text if soup.find('title') else ''
        meta_desc = ''
        meta_tag = soup.find('meta', attrs={'name': 'description'})
        if meta_tag:
            meta_desc = meta_tag.get('content', '')
        
        # Nettoyer le contenu
        for script in soup(['script', 'style']):
            script.decompose()
        
        # Extraire le contenu principal
        main_content = soup.find('main') or soup.find('article') or soup.find('body')
        
        if format_type == 'markdown':
            # Convertir en Markdown
            h = html2text.HTML2Text()
            h.ignore_links = False
            h.ignore_images = not include_images
            content = h.handle(str(main_content))
        else:
            # Garder en HTML mais nettoyer
            content = str(main_content)
        
        return {
            'url': url,
            'title': title,
            'description': meta_desc,
            'content': content,
            'format': format_type,
            'word_count': len(content.split())
        }
        
    except Exception as e:
        logger.error(f"Erreur scraping web: {e}")
        return {'error': str(e)}