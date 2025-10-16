@extends('emails.layout')

@section('title', 'Bienvenue sur AutomateHub !')

@section('subtitle', 'Votre parcours n8n commence maintenant')

@section('content')
    <p class="email-greeting">Bonjour {{ $user->name }} ! 👋</p>

    <p class="email-text">
        Bienvenue sur <strong>AutomateHub</strong>, la plateforme française dédiée à l'apprentissage de n8n !
    </p>

    <p class="email-text">
        Nous sommes ravis de vous accompagner dans votre parcours d'automatisation. Voici ce qui vous attend :
    </p>

    <div class="email-stats">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">{{ $stats['tutorials'] ?? 0 }}</span>
                <div class="stat-label">Tutoriels disponibles</div>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $stats['workflows'] ?? 0 }}</span>
                <div class="stat-label">Workflows téléchargeables</div>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $stats['badges'] ?? 0 }}</span>
                <div class="stat-label">Badges à débloquer</div>
            </div>
        </div>
    </div>

    <p class="email-text">
        <strong>Vos premières étapes :</strong>
    </p>

    <ul style="margin: 0 0 20px 20px; padding: 0;">
        <li style="margin-bottom: 10px;">✅ Complétez votre profil</li>
        <li style="margin-bottom: 10px;">📚 Découvrez nos tutoriels pour débutants</li>
        <li style="margin-bottom: 10px;">🏆 Gagnez vos premiers badges</li>
        <li style="margin-bottom: 10px;">🔄 Téléchargez vos premiers workflows</li>
    </ul>

    @if($user->n8n_level)
        <p class="email-text">
            Nous avons évalué votre niveau n8n comme <strong>{{ ucfirst($user->n8n_level) }}</strong> 
            et nous avons sélectionné les meilleurs tutoriels pour vous aider à progresser.
        </p>
    @endif

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="email-button">
            🚀 Accéder à mon dashboard
        </a>
    </div>

    @if($recommendedTutorials ?? false)
        <p class="email-text">
            <strong>Tutoriels recommandés pour vous :</strong>
        </p>
        
        @foreach($recommendedTutorials as $tutorial)
            <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin: 10px 0; background-color: #fafafa;">
                <h4 style="margin: 0 0 5px 0; color: #111827;">{{ $tutorial->title }}</h4>
                <p style="margin: 0 0 10px 0; font-size: 14px; color: #6b7280;">
                    {{ Str::limit($tutorial->description, 100) }}
                </p>
                <span style="background-color: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 4px; font-size: 12px;">
                    {{ ucfirst($tutorial->difficulty_level) }}
                </span>
                <span style="background-color: #f3e8ff; color: #7c3aed; padding: 2px 8px; border-radius: 4px; font-size: 12px; margin-left: 5px;">
                    {{ $tutorial->category->name }}
                </span>
            </div>
        @endforeach

        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ url('/tutorials') }}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">
                Voir tous les tutoriels →
            </a>
        </div>
    @endif

    <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 0 4px 4px 0;">
        <p style="margin: 0; font-size: 14px;">
            <strong>💡 Astuce :</strong> Vous pouvez télécharger {{ $user->subscription_type === 'free' ? '5 workflows par mois' : 'un nombre illimité de workflows' }} 
            pour suivre nos tutoriels pratiques.
        </p>
    </div>

    @if($user->subscription_type === 'free')
        <p class="email-text">
            Vous démarrez avec notre plan <strong>Gratuit</strong>. Si vous souhaitez accéder à plus de contenu premium, 
            n'hésitez pas à découvrir nos plans payants.
        </p>

        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ url('/pricing') }}" style="display: inline-block; padding: 10px 20px; background-color: #f59e0b; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                🌟 Découvrir Premium
            </a>
        </div>
    @endif

    <p class="email-text">
        Si vous avez des questions, notre équipe est là pour vous aider ! N'hésitez pas à nous contacter via 
        le chat en ligne ou par email.
    </p>

    <p class="email-text">
        Bonne automatisation ! 🤖<br>
        <strong>L'équipe AutomateHub</strong>
    </p>
@endsection