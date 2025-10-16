#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Module spécialisé pour la traduction intelligente des prompts IA
"""

import re

# Dictionnaire étendu pour les prompts IA
PROMPT_TRANSLATIONS = {
    # Verbes d'action courants dans les prompts
    "generate": "générer",
    "create": "créer",
    "make": "faire",
    "produce": "produire",
    "design": "concevoir",
    "draw": "dessiner",
    "render": "rendre",
    "transform": "transformer",
    "convert": "convertir",
    "analyze": "analyser",
    "describe": "décrire",
    "explain": "expliquer",
    "summarize": "résumer",
    "translate": "traduire",
    "write": "écrire",
    "compose": "composer",
    "build": "construire",
    "develop": "développer",
    "optimize": "optimiser",
    "enhance": "améliorer",
    "modify": "modifier",
    "adjust": "ajuster",
    "fix": "corriger",
    "improve": "améliorer",
    "refine": "affiner",
    
    # Objets et sujets
    "image": "image",
    "photo": "photo",
    "picture": "image",
    "illustration": "illustration",
    "drawing": "dessin",
    "artwork": "œuvre d'art",
    "graphic": "graphique",
    "visualization": "visualisation",
    "diagram": "diagramme",
    "chart": "graphique",
    "text": "texte",
    "document": "document",
    "article": "article",
    "report": "rapport",
    "email": "email",
    "message": "message",
    "content": "contenu",
    "data": "données",
    "information": "information",
    
    # Personnes et caractères
    "man": "homme",
    "woman": "femme",
    "person": "personne",
    "people": "personnes",
    "child": "enfant",
    "children": "enfants",
    "boy": "garçon",
    "girl": "fille",
    "adult": "adulte",
    "teenager": "adolescent",
    "elderly": "personne âgée",
    "customer": "client",
    "user": "utilisateur",
    "employee": "employé",
    "manager": "gestionnaire",
    "team": "équipe",
    
    # Descriptions et qualificatifs
    "professional": "professionnel",
    "modern": "moderne",
    "elegant": "élégant",
    "simple": "simple",
    "complex": "complexe",
    "beautiful": "beau",
    "clean": "propre",
    "minimalist": "minimaliste",
    "colorful": "coloré",
    "detailed": "détaillé",
    "realistic": "réaliste",
    "abstract": "abstrait",
    "vintage": "vintage",
    "futuristic": "futuriste",
    "natural": "naturel",
    "organic": "organique",
    "geometric": "géométrique",
    
    # Couleurs
    "red": "rouge",
    "blue": "bleu",
    "green": "vert",
    "yellow": "jaune",
    "orange": "orange",
    "purple": "violet",
    "pink": "rose",
    "black": "noir",
    "white": "blanc",
    "gray": "gris",
    "grey": "gris",
    "brown": "marron",
    "gold": "doré",
    "silver": "argenté",
    
    # Actions et états
    "holding": "tenant",
    "wearing": "portant",
    "using": "utilisant",
    "sitting": "assis",
    "standing": "debout",
    "walking": "marchant",
    "running": "courant",
    "working": "travaillant",
    "smiling": "souriant",
    "looking": "regardant",
    "pointing": "pointant",
    "showing": "montrant",
    "presenting": "présentant",
    
    # Objets courants
    "bottle": "bouteille",
    "glass": "verre",
    "cup": "tasse",
    "phone": "téléphone",
    "computer": "ordinateur",
    "laptop": "ordinateur portable",
    "tablet": "tablette",
    "book": "livre",
    "pen": "stylo",
    "paper": "papier",
    "desk": "bureau",
    "chair": "chaise",
    "table": "table",
    "car": "voiture",
    "building": "bâtiment",
    "house": "maison",
    "office": "bureau",
    
    # Vêtements
    "suit": "costume",
    "suite": "costume",  # Correction orthographe
    "dress": "robe",
    "shirt": "chemise",
    "pants": "pantalon",
    "shoes": "chaussures",
    "hat": "chapeau",
    "jacket": "veste",
    "coat": "manteau",
    "tie": "cravate",
    
    # Contexte et environnement
    "background": "arrière-plan",
    "foreground": "premier plan",
    "landscape": "paysage",
    "portrait": "portrait",
    "indoor": "intérieur",
    "outdoor": "extérieur",
    "nature": "nature",
    "city": "ville",
    "street": "rue",
    "room": "pièce",
    "studio": "studio",
    
    # Styles et formats
    "style": "style",
    "format": "format",
    "template": "modèle",
    "layout": "mise en page",
    "design": "design",
    "pattern": "motif",
    "theme": "thème",
    
    # Connecteurs et prépositions
    "with": "avec",
    "without": "sans",
    "for": "pour",
    "from": "de",
    "in": "dans",
    "on": "sur",
    "at": "à",
    "by": "par",
    "near": "près de",
    "between": "entre",
    "behind": "derrière",
    "front": "devant",
    "beside": "à côté de",
    "above": "au-dessus de",
    "below": "en-dessous de",
    "under": "sous",
    "over": "sur",
    
    # Quantités et nombres
    "one": "un",
    "two": "deux",
    "three": "trois",
    "four": "quatre",
    "five": "cinq",
    "many": "plusieurs",
    "few": "quelques",
    "some": "certains",
    "all": "tous",
    "none": "aucun",
    
    # Temps
    "morning": "matin",
    "afternoon": "après-midi",
    "evening": "soir",
    "night": "nuit",
    "day": "jour",
    "week": "semaine",
    "month": "mois",
    "year": "année",
    
    # Autres termes utiles
    "please": "s'il vous plaît",
    "thank you": "merci",
    "wine": "vin",
    "vine": "vin",  # Correction orthographe
    "coffee": "café",
    "tea": "thé",
    "water": "eau",
    "food": "nourriture",
    "meal": "repas",
    "product": "produit",
    "service": "service",
    "solution": "solution",
    "problem": "problème",
    "question": "question",
    "answer": "réponse",
    "help": "aide",
    "support": "support"
}

def translate_prompt(prompt, context="general"):
    """
    Traduit un prompt de manière intelligente
    
    Args:
        prompt: Le prompt à traduire
        context: Le contexte (image, text, code, etc.)
    
    Returns:
        Le prompt traduit
    """
    if not prompt or not isinstance(prompt, str):
        return prompt
    
    # Préserver certains éléments
    preserved_elements = []
    
    # 1. Préserver les URLs
    url_pattern = r'https?://[^\s]+'
    urls = re.findall(url_pattern, prompt)
    for i, url in enumerate(urls):
        placeholder = f"__URL_{i}__"
        prompt = prompt.replace(url, placeholder)
        preserved_elements.append((placeholder, url))
    
    # 2. Préserver les expressions entre guillemets
    quote_pattern = r'"([^"]*)"'
    quotes = re.findall(quote_pattern, prompt)
    for i, quote in enumerate(quotes):
        placeholder = f"__QUOTE_{i}__"
        prompt = prompt.replace(f'"{quote}"', placeholder)
        preserved_elements.append((placeholder, f'"{quote}"'))
    
    # 3. Préserver les nombres avec unités
    number_pattern = r'\b\d+(?:\.\d+)?(?:\s*(?:px|em|rem|%|cm|mm|in|pt|pc))\b'
    numbers = re.findall(number_pattern, prompt)
    for i, number in enumerate(numbers):
        placeholder = f"__NUM_{i}__"
        prompt = prompt.replace(number, placeholder)
        preserved_elements.append((placeholder, number))
    
    # 4. Préserver les codes hexadécimaux
    hex_pattern = r'#[0-9a-fA-F]{3,6}\b'
    hexcodes = re.findall(hex_pattern, prompt)
    for i, hexcode in enumerate(hexcodes):
        placeholder = f"__HEX_{i}__"
        prompt = prompt.replace(hexcode, placeholder)
        preserved_elements.append((placeholder, hexcode))
    
    # Traduire le prompt
    translated = prompt.lower()  # Convertir en minuscules pour la traduction
    
    # Appliquer les traductions (du plus long au plus court)
    for eng, fr in sorted(PROMPT_TRANSLATIONS.items(), key=lambda x: len(x[0]), reverse=True):
        # Utiliser des limites de mots pour éviter les traductions partielles
        pattern = r'\b' + re.escape(eng) + r'\b'
        translated = re.sub(pattern, fr, translated, flags=re.IGNORECASE)
    
    # Remettre la première lettre en majuscule si nécessaire
    if prompt[0].isupper():
        translated = translated[0].upper() + translated[1:]
    
    # Restaurer les éléments préservés
    for placeholder, original in preserved_elements:
        translated = translated.replace(placeholder, original)
    
    return translated

def translate_prompt_advanced(prompt):
    """
    Version avancée qui gère des structures de phrases plus complexes
    """
    # Patterns de phrases courantes dans les prompts
    sentence_patterns = [
        # Pattern: Create/Generate X with/containing Y
        (r"(create|generate|make|produce)\s+(?:an?\s+)?(.+?)\s+with\s+(.+)", 
         r"\1 \2 avec \3"),
        
        # Pattern: X holding/wearing Y
        (r"(.+?)\s+(holding|wearing|using)\s+(.+)",
         r"\1 \2 \3"),
        
        # Pattern: X in Y style
        (r"(.+?)\s+in\s+(.+?)\s+style",
         r"\1 dans le style \2"),
        
        # Pattern: X for Y purpose
        (r"(.+?)\s+for\s+(.+)",
         r"\1 pour \2")
    ]
    
    # D'abord appliquer la traduction de base
    translated = translate_prompt(prompt)
    
    # Puis appliquer les patterns de phrases si applicable
    # (Cette partie pourrait être étendue selon les besoins)
    
    return translated

# Test de la fonction
if __name__ == "__main__":
    test_prompts = [
        "generate an image with a man in a red suite holding a bottle of vine",
        "Create a professional photo of a woman wearing a blue dress",
        "Design a modern logo for a tech company",
        "Generate 3 variations of a minimalist poster",
        "Create an illustration in watercolor style",
        "Make a detailed diagram showing the process",
        "Generate a #FF5733 colored background with white text",
        "Create an image 1920x1080 pixels in size"
    ]
    
    print("🧪 Test de traduction de prompts:")
    print("=" * 60)
    for prompt in test_prompts:
        translated = translate_prompt(prompt)
        print(f"EN: {prompt}")
        print(f"FR: {translated}")
        print("-" * 60)