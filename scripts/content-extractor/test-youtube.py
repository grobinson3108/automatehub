#!/usr/bin/env python3
from youtube_transcript_api import YouTubeTranscriptApi

# Test simple
video_id = "z04PhM2bNyU"
api = YouTubeTranscriptApi()

# Lister les transcriptions disponibles
transcript_list = api.list(video_id)
print("Transcriptions disponibles:")
for key, transcript in transcript_list.manually_created_transcripts.items():
    print(f"- Manuel: {key} - {transcript.language_code}")
for key, transcript in transcript_list.generated_transcripts.items():
    print(f"- Auto: {key} - {transcript.language_code}")

# Récupérer la transcription en français
try:
    # Méthode 1: directe
    transcript_data = api.fetch(video_id, languages=['fr'])
    print(f"\nType de données: {type(transcript_data)}")
    print(f"Premier élément: {transcript_data[0]}")
except Exception as e:
    print(f"Erreur méthode 1: {e}")
    
    # Méthode 2: via transcript object
    try:
        transcript = transcript_list.find_transcript(['fr'])
        transcript_data = transcript.fetch()
        print(f"\nType de données: {type(transcript_data)}")
        print(f"Premier élément: {transcript_data[0]}")
    except Exception as e2:
        print(f"Erreur méthode 2: {e2}")