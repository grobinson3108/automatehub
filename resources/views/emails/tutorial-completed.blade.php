@extends('emails.layout')

@section('title', 'Tutoriel terminé !')

@section('subtitle', 'Bravo pour avoir terminé ce tutoriel')

@section('content')
    <p class="email-greeting">Excellent travail {{ $user->name }} ! 🎯</p>

    <p class="email-text">
        Vous venez de terminer le tutoriel <strong>{{ $tutorial->title }}</strong>. 
        C'est un pas de plus vers la maîtrise de n8n !
    </p>

    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0; background-color: #fafafa;">
        <h3 style="margin: 0 0 10px 0; color: #111827;">{{ $tutorial->title }}</h3>
        <p style="margin: 0 0 15px 0; color: #6b7280; font-size: 14px;">{{ $tutorial->description }}</p>
        
        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <span style="background-color: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                {{ ucfirst($tutorial->difficulty_level) }}
            </span>
            <span style="background-color: #f3e8ff; color: #7c3aed; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                {{ $tutorial->category->name }}
            </span>
            <span style="color: #6b7280; font-size: 12px;">
                ⏱️ {{ $tutorial->duration_minutes }} minutes
            </span>
            @if($tutorial->downloads_count > 0)
                <span style="color: #6b7280; font-size: 12px;">
                    📥 {{ $tutorial->downloads_count }} téléchargements
                </span>
            @endif
        </div>
    </div>

    @if($earnedBadges ?? false)
        <div style="background-color: #fef3c7; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h4 style="margin: 0 0 15px 0; color: #92400e;">🏆 Nouveaux badges débloqués !</h4>
            @foreach($earnedBadges as $earnedBadge)
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div style="width: 30px; height: 30px; border-radius: 50%; background-color: {{ $earnedBadge->color }}; display: flex; align-items: center; justify-content: center; margin-right: 10px; font-size: 14px;">
                        {{ $earnedBadge->icon }}
                    </div>
                    <span style="font-weight: 600; color: #92400e;">{{ $earnedBadge->name }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <div class="email-stats">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">{{ $userStats['completedTutorials'] ?? 0 }}</span>
                <div class="stat-label">Tutoriels terminés</div>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $userStats['totalBadges'] ?? 0 }}</span>
                <div class="stat-label">Badges obtenus</div>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $userStats['streakDays'] ?? 0 }}</span>
                <div class="stat-label">Jours de série</div>
            </div>
        </div>
    </div>

    @if($completionRate = $userStats['completionRate'] ?? false)
        <p class="email-text">
            Votre taux de complétion des tutoriels est maintenant de <strong>{{ $completionRate }}%</strong> ! 
            {{ $completionRate >= 80 ? 'Excellent engagement !' : 'Continuez comme ça !' }}
        </p>
    @endif

    @if($nextRecommendations ?? false)
        <p class="email-text">
            <strong>Tutoriels recommandés pour continuer votre apprentissage :</strong>
        </p>

        @foreach($nextRecommendations as $recommendation)
            <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin: 10px 0; background-color: #fafafa;">
                <h4 style="margin: 0 0 5px 0; color: #111827; font-size: 14px;">{{ $recommendation->title }}</h4>
                <p style="margin: 0 0 10px 0; font-size: 12px; color: #6b7280;">
                    {{ Str::limit($recommendation->description, 80) }}
                </p>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="background-color: #dbeafe; color: #1e40af; padding: 2px 6px; border-radius: 3px; font-size: 11px;">
                        {{ ucfirst($recommendation->difficulty_level) }}
                    </span>
                    <span style="color: #6b7280; font-size: 11px;">⏱️ {{ $recommendation->duration_minutes }}min</span>
                </div>
            </div>
        @endforeach

        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ url('/tutorials') }}" style="color: #3b82f6; text-decoration: none; font-weight: 600;">
                Voir tous les tutoriels →
            </a>
        </div>
    @endif

    @if($downloadAvailable ?? false)
        <div style="background-color: #ecfdf5; border-left: 4px solid #10b981; padding: 15px; margin: 20px 0; border-radius: 0 4px 4px 0;">
            <p style="margin: 0; font-size: 14px;">
                <strong>📥 Workflow disponible :</strong> N'oubliez pas de télécharger le workflow de ce tutoriel 
                pour l'utiliser dans vos propres projets !
            </p>
        </div>
    @endif

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="email-button">
            📊 Voir mon dashboard
        </a>
    </div>

    @if($milestoneReached ?? false)
        <div style="background-color: #fef3c7; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;">
            <h4 style="margin: 0 0 10px 0; color: #92400e;">🎉 Étape importante franchie !</h4>
            <p style="margin: 0; font-size: 14px; color: #92400e;">
                {{ $milestoneReached }}
            </p>
        </div>
    @endif

    @if($user->subscription_type === 'free' && ($tutorial->is_premium || $tutorial->is_pro))
        <div style="background-color: #f0f9ff; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h4 style="margin: 0 0 10px 0; color: #0c4a6e;">Débloquez plus de contenu !</h4>
            <p style="margin: 0 0 15px 0; font-size: 14px; color: #0369a1;">
                Vous avez terminé un tutoriel {{ $tutorial->is_pro ? 'Pro' : 'Premium' }} ! 
                Découvrez notre catalogue complet de tutoriels avancés.
            </p>
            <a href="{{ url('/pricing') }}" style="display: inline-block; padding: 8px 16px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 600;">
                Voir les plans
            </a>
        </div>
    @endif

    <p class="email-text">
        Continuez à explorer nos tutoriels pour développer vos compétences en automatisation. 
        Chaque tutoriel terminé vous rapproche de la maîtrise de n8n !
    </p>

    <p class="email-text">
        Félicitations encore et bonne continuation ! 🚀<br>
        <strong>L'équipe AutomateHub</strong>
    </p>
@endsection