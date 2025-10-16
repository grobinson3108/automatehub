@extends('emails.layout')

@section('title', 'Nouveau badge débloqué !')

@section('subtitle', 'Félicitations pour votre progression')

@section('content')
    <p class="email-greeting">Félicitations {{ $user->name }} ! 🎉</p>

    <p class="email-text">
        Vous venez de débloquer un nouveau badge : <strong>{{ $badge->name }}</strong> !
    </p>

    <div style="text-align: center; margin: 30px 0;">
        <div style="display: inline-block; width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, {{ $badge->color }} 0%, {{ $badge->color }}CC 100%); position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 48px;">
                {{ $badge->icon }}
            </div>
            <div style="position: absolute; top: -10px; right: -10px; width: 30px; height: 30px; background-color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <span style="color: white; font-size: 16px;">✓</span>
            </div>
        </div>
    </div>

    <div style="background-color: #f0f9ff; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;">
        <h3 style="margin: 0 0 10px 0; color: #0c4a6e; font-size: 18px;">{{ $badge->name }}</h3>
        <p style="margin: 0; color: #0369a1; font-size: 14px;">{{ $badge->description }}</p>
    </div>

    <p class="email-text">
        <strong>Comment vous l'avez obtenu :</strong><br>
        {{ $badge->criteria }}
    </p>

    @if($badgeType = $badge->type)
        <div style="margin: 20px 0;">
            @if($badgeType === 'completion')
                <p class="email-text">
                    Ce badge récompense votre assiduité dans l'apprentissage. Continuez à terminer des tutoriels 
                    pour débloquer d'autres badges de progression !
                </p>
            @elseif($badgeType === 'streak')
                <p class="email-text">
                    Impressionnant ! Vous maintenez une belle série d'apprentissage. La régularité est la clé 
                    pour maîtriser n8n.
                </p>
            @elseif($badgeType === 'milestone')
                <p class="email-text">
                    Vous avez franchi une étape importante dans votre parcours n8n. C'est un excellent indicateur 
                    de votre progression !
                </p>
            @elseif($badgeType === 'special')
                <p class="email-text">
                    Ce badge spécial reconnaît quelque chose d'unique dans votre parcours. Bravo pour cette réalisation !
                </p>
            @endif
        </div>
    @endif

    <div class="email-stats">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">{{ $userStats['totalBadges'] ?? 0 }}</span>
                <div class="stat-label">Badges obtenus</div>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $userStats['completedTutorials'] ?? 0 }}</span>
                <div class="stat-label">Tutoriels terminés</div>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $userStats['streakDays'] ?? 0 }}</span>
                <div class="stat-label">Jours de série</div>
            </div>
        </div>
    </div>

    @if($nextBadges ?? false)
        <p class="email-text">
            <strong>Prochains badges à débloquer :</strong>
        </p>

        @foreach($nextBadges as $nextBadge)
            <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin: 10px 0; background-color: #fafafa;">
                <div style="display: flex; align-items: center;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-size: 18px;">
                        {{ $nextBadge->icon }}
                    </div>
                    <div>
                        <h4 style="margin: 0 0 5px 0; color: #111827; font-size: 14px;">{{ $nextBadge->name }}</h4>
                        <p style="margin: 0; font-size: 12px; color: #6b7280;">{{ $nextBadge->description }}</p>
                        @if($nextBadge->progress ?? false)
                            <div style="margin-top: 8px;">
                                <div style="background-color: #e5e7eb; height: 4px; border-radius: 2px; overflow: hidden;">
                                    <div style="background-color: #3b82f6; height: 100%; width: {{ $nextBadge->progress }}%; transition: width 0.3s ease;"></div>
                                </div>
                                <span style="font-size: 10px; color: #6b7280;">{{ $nextBadge->progress }}% complété</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard/badges') }}" class="email-button">
            🏆 Voir tous mes badges
        </a>
    </div>

    <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 0 4px 4px 0;">
        <p style="margin: 0; font-size: 14px;">
            <strong>💡 Le saviez-vous ?</strong> Partager vos réussites peut inspirer d'autres apprenants ! 
            N'hésitez pas à partager ce badge sur les réseaux sociaux.
        </p>
    </div>

    @if($user->subscription_type === 'free')
        <div style="background-color: #f0f9ff; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h4 style="margin: 0 0 10px 0; color: #0c4a6e;">Débloquez plus de badges !</h4>
            <p style="margin: 0 0 15px 0; font-size: 14px; color: #0369a1;">
                Avec un plan Premium ou Pro, accédez à des badges exclusifs et des défis spéciaux.
            </p>
            <a href="{{ url('/pricing') }}" style="display: inline-block; padding: 8px 16px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 600;">
                Découvrir Premium
            </a>
        </div>
    @endif

    <p class="email-text">
        Continuez sur cette lancée ! Votre progression sur AutomateHub est remarquable.
    </p>

    <p class="email-text">
        Bonne continuation dans votre apprentissage n8n ! 🚀<br>
        <strong>L'équipe AutomateHub</strong>
    </p>
@endsection