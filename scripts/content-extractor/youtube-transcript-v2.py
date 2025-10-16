#!/usr/bin/env python3
"""
YouTube Transcript Extractor V2
Compatible avec la nouvelle version de l'API
"""

import sys
import json
from youtube_transcript_api import YouTubeTranscriptApi
from urllib.parse import urlparse, parse_qs
import re

def extract_video_id(url):
    """Extrait l'ID de la vidéo depuis différents formats d'URL YouTube"""
    parsed_url = urlparse(url)
    
    # Format standard youtube.com/watch?v=VIDEO_ID
    if parsed_url.hostname in ('www.youtube.com', 'youtube.com'):
        if parsed_url.path == '/watch':
            return parse_qs(parsed_url.query)['v'][0]
        elif parsed_url.path.startswith('/embed/'):
            return parsed_url.path.split('/')[2]
        elif parsed_url.path.startswith('/v/'):
            return parsed_url.path.split('/')[2]
    
    # Format court youtu.be/VIDEO_ID
    elif parsed_url.hostname == 'youtu.be':
        return parsed_url.path[1:]
    
    # Regex en dernier recours
    regex = r'(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/)([^&\n]+)'
    match = re.search(regex, url)
    if match:
        return match.group(1)
    
    return None

def get_transcript(video_url, preferred_language='fr', include_timestamps=True, timestamps_to_combine=5):
    """
    Récupère la transcription d'une vidéo YouTube
    
    Args:
        video_url: URL de la vidéo YouTube
        preferred_language: Langue préférée (fr par défaut)
        include_timestamps: Inclure les timestamps
        timestamps_to_combine: Nombre de segments à combiner
    """
    try:
        video_id = extract_video_id(video_url)
        if not video_id:
            return {"error": "Impossible d'extraire l'ID de la vidéo", "success": False}
        
        # Créer l'instance API et récupérer les transcriptions
        api = YouTubeTranscriptApi()
        transcript_list = api.list(video_id)
        
        # Essayer de trouver la transcription dans la langue préférée
        transcript = None
        try:
            transcript = transcript_list.find_transcript([preferred_language])
        except:
            # Si pas trouvé, prendre la première disponible
            if transcript_list.manually_created_transcripts:
                transcript = list(transcript_list.manually_created_transcripts.values())[0]
            elif transcript_list.generated_transcripts:
                transcript = list(transcript_list.generated_transcripts.values())[0]
            elif transcript_list.translation_transcripts:
                # Essayer de trouver une traduction dans la langue préférée
                for trans in transcript_list.translation_transcripts.values():
                    if trans.language_code == preferred_language:
                        transcript = trans
                        break
                if not transcript:
                    transcript = list(transcript_list.translation_transcripts.values())[0]
        
        if not transcript:
            return {
                "success": False, 
                "error": "Aucune transcription disponible",
                "video_url": video_url
            }
        
        # Récupérer les données de transcription
        transcript_data = transcript.fetch()
        
        # Combiner les segments si demandé
        if timestamps_to_combine > 1:
            combined_transcript = []
            buffer = []
            
            for i, entry in enumerate(transcript_data):
                buffer.append(entry)
                
                if len(buffer) >= timestamps_to_combine or i == len(transcript_data) - 1:
                    # Combiner les textes
                    combined_text = ' '.join([e['text'] for e in buffer])
                    start_time = buffer[0]['start']
                    end_time = buffer[-1]['start'] + buffer[-1]['duration']
                    
                    combined_entry = {
                        'text': combined_text,
                        'start': start_time,
                        'duration': end_time - start_time
                    }
                    
                    if include_timestamps:
                        # Formater le timestamp
                        minutes = int(start_time // 60)
                        seconds = int(start_time % 60)
                        combined_entry['timestamp'] = f"[{minutes:02d}:{seconds:02d}]"
                    
                    combined_transcript.append(combined_entry)
                    buffer = []
            
            transcript_data = combined_transcript
        
        # Formater la sortie
        if include_timestamps:
            formatted_transcript = '\n\n'.join([
                f"{entry.get('timestamp', '')} {entry['text']}" 
                for entry in transcript_data
            ])
        else:
            formatted_transcript = ' '.join([entry['text'] for entry in transcript_data])
        
        return {
            "success": True,
            "video_id": video_id,
            "video_url": video_url,
            "language": transcript.language_code,
            "transcript": formatted_transcript,
            "segments": transcript_data if include_timestamps else None
        }
        
    except Exception as e:
        return {
            "success": False,
            "error": str(e),
            "video_url": video_url
        }

if __name__ == "__main__":
    # Utilisation en ligne de commande
    if len(sys.argv) < 2:
        print("Usage: python youtube-transcript.py <video_url> [language] [include_timestamps] [timestamps_to_combine]")
        sys.exit(1)
    
    video_url = sys.argv[1]
    language = sys.argv[2] if len(sys.argv) > 2 else 'fr'
    include_timestamps = sys.argv[3].lower() == 'true' if len(sys.argv) > 3 else True
    timestamps_to_combine = int(sys.argv[4]) if len(sys.argv) > 4 else 5
    
    result = get_transcript(video_url, language, include_timestamps, timestamps_to_combine)
    print(json.dumps(result, ensure_ascii=False, indent=2))