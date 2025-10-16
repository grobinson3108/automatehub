@extends('emails.layout')

@section('title', 'Votre résumé hebdomadaire')

@section('subtitle', 'Votre progression cette semaine sur AutomateHub')

@section('content')
    <p class="email-greeting">Bonjour {{ $user->name }} ! 📊</p>

    <p class="email-text">
        Voici un résumé de votre activité sur AutomateHub cette semaine du {{ $weekStart }} au {{ $weekEnd }}.
    </p>

    <div class="email-stats">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">{{ $weeklyStats['completedTutorials'] ?? 0 }}</span>
                <div class="stat-label">Tutoriels terminés</div>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $weeklyStats['badgesEarned'] ?? 0 }}</span>
                <div class="stat-label">Nouveaux badges</div>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $weeklyStats['watchTime'] ?? 0 }}min</span>
                <div class="stat-label">Temps d'apprentissage</div>
            </div>
        </div>
    </div>

    @if(($weeklyStats['completedTutorials'] ?? 0) > 0)
        <p class="email-text">
            <strong>🎯 Excellent travail cette semaine !</strong> 
            Vous avez terminé {{ $weeklyStats['completedTutorials'] }} tutoriel{{ $weeklyStats['completedTutorials'] > 1 ? 's' : '' }}.
            {{ $weeklyStats['completedTutorials'] >= 3 ? 'Votre assiduité est remarquable !' : 'Continuez sur cette lancée !' }}
        </p>

        @if($completedTutorials ?? false)
            <p class="email-text"><strong>Tutoriels terminés cette semaine :</strong></p>
            @foreach($completedTutorials as $tutorial)
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin: 10px 0; background-color: #f9fafb;">
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <span style="color: #10b981; margin-right: 8px; font-size: 16px;">✅</span>
                        <h4 style="margin: 0; color: #111827; font-size: 14px;">{{ $tutorial->title }}</h4>
                    </div>
                    <p style="margin: 0 0 8px 24px; font-size: 12px; color: #6b7280;">{{ $tutorial->category->name }}</p>
                    <div style="margin-left: 24px;">
                        <span style="background-color: #dbeafe; color: #1e40af; padding: 2px 6px; border-radius: 3px; font-size: 11px;">
                            {{ ucfirst($tutorial->difficulty_level) }}
                        </span>
                    </div>
                </div>
            @endforeach
        @endif
    @else
        <div style="background-color: #fef3c7; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h4 style="margin: 0 0 10px 0; color: #92400e;">📚 Aucun tutoriel terminé cette semaine</h4>
            <p style="margin: 0; font-size: 14px; color: #92400e;">
                Pas de souci ! Il n'est jamais trop tard pour reprendre l'apprentissage. 
                Nous avons sélectionné quelques tutoriels courts pour vous aider à redémarrer.
            </p>
        </div>
    @endif

    @if(($weeklyStats['badgesEarned'] ?? 0) > 0)
        <p class="email-text"><strong>🏆 Nouveaux badges cette semaine :</strong></p>
        @foreach($newBadges as $badge)
            <div style="display: flex; align-items: center; margin: 10px 0; padding: 10px; background-color: #fef3c7; border-radius: 6px;">
                <div style="width: 30px; height: 30px; border-radius: 50%; background-color: {{ $badge->color }}; display: flex; align-items: center; justify-content: center; margin-right: 10px; font-size: 14px;">
                    {{ $badge->icon }}
                </div>
                <span style="font-weight: 600; color: #92400e;">{{ $badge->name }}</span>
            </div>
        @endforeach
    @endif

    @if($currentStreak = $weeklyStats['currentStreak'] ?? false)
        <div style="background-color: #ecfdf5; border-left: 4px solid #10b981; padding: 15px; margin: 20px 0; border-radius: 0 4px 4px 0;">
            <p style="margin: 0; font-size: 14px;">
                <strong>🔥 Série actuelle : {{ $currentStreak }} jour{{ $currentStreak > 1 ? 's' : '' }}</strong><br>
                {{ $currentStreak >= 7 ? 'Impressionnant ! Vous maintenez une excellente régularité.' : 'Continuez à apprendre chaque jour pour maintenir votre série !' }}
            </p>
        </div>
    @endif

    <!-- Recommendations -->
    @if($recommendedTutorials ?? false)
        <p class="email-text">
            <strong>📖 Tutoriels recommandés pour la semaine prochaine :</strong>
        </p>

        @foreach($recommendedTutorials as $tutorial)
            <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin: 10px 0; background-color: #fafafa;">
                <h4 style="margin: 0 0 5px 0; color: #111827; font-size: 14px;">{{ $tutorial->title }}</h4>
                <p style="margin: 0 0 10px 0; font-size: 12px; color: #6b7280;">
                    {{ Str::limit($tutorial->description, 100) }}
                </p>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="background-color: #dbeafe; color: #1e40af; padding: 2px 6px; border-radius: 3px; font-size: 11px;">
                        {{ ucfirst($tutorial->difficulty_level) }}
                    </span>
                    <span style="background-color: #f3e8ff; color: #7c3aed; padding: 2px 6px; border-radius: 3px; font-size: 11px;">
                        {{ $tutorial->category->name }}
                    </span>
                    <span style="color: #6b7280; font-size: 11px;">⏱️ {{ $tutorial->duration_minutes }}min</span>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Community Highlights -->
    @if($communityStats ?? false)
        <div style="background-color: #f0f9ff; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h4 style="margin: 0 0 10px 0; color: #0c4a6e;">👥 Points forts de la communauté</h4>
            <ul style="margin: 0; padding-left: 16px; color: #0369a1; font-size: 14px;">
                <li>{{ $communityStats['newUsers'] ?? 0 }} nouveaux membres cette semaine</li>
                <li>{{ $communityStats['totalCompletions'] ?? 0 }} tutoriels terminés par la communauté</li>
                <li>{{ $communityStats['topCategory'] ?? 'Débutant' }} est la catégorie la plus populaire</li>
            </ul>
        </div>
    @endif

    <!-- Progress towards next level -->
    @if($nextLevelProgress ?? false)
        <div style="background-color: #fef3c7; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h4 style="margin: 0 0 10px 0; color: #92400e;">⭐ Progression vers le niveau suivant</h4>
            <p style="margin: 0 0 10px 0; font-size: 14px; color: #92400e;">
                Plus que {{ $nextLevelProgress['remaining'] }} tutoriel{{ $nextLevelProgress['remaining'] > 1 ? 's' : '' }} 
                pour atteindre le niveau <strong>{{ $nextLevelProgress['nextLevel'] }}</strong> !
            </p>
            <div style="background-color: #fbbf24; height: 8px; border-radius: 4px; overflow: hidden;">
                <div style="background-color: #f59e0b; height: 100%; width: {{ $nextLevelProgress['percentage'] }}%; transition: width 0.3s ease;"></div>
            </div>
        </div>
    @endif

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="email-button">
            📊 Voir mon dashboard complet
        </a>
    </div>

    <!-- Upgrade prompt for free users -->
    @if($user->subscription_type === 'free')
        <div style="background-color: #f0f9ff; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <h4 style="margin: 0 0 10px 0; color: #0c4a6e;">🌟 Accélérez votre apprentissage</h4>
            <p style="margin: 0 0 15px 0; font-size: 14px; color: #0369a1;">
                Avec Premium, accédez à tous nos tutoriels avancés et débloquez des fonctionnalités exclusives 
                pour progresser plus rapidement.
            </p>
            <a href="{{ url('/pricing') }}" style="display: inline-block; padding: 8px 16px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 600;">
                Découvrir Premium
            </a>
        </div>
    @endif

    <!-- Weekly tip -->
    <div style="background-color: #ecfdf5; border-left: 4px solid #10b981; padding: 15px; margin: 20px 0; border-radius: 0 4px 4px 0;">
        <p style="margin: 0; font-size: 14px;">
            <strong>💡 Astuce de la semaine :</strong> {{ $weeklyTip ?? 'Essayez de consacrer 15 minutes par jour à l\'apprentissage pour maintenir une progression constante.' }}
        </p>
    </div>

    <p class="email-text">
        Merci de faire partie de la communauté AutomateHub ! Continuez à explorer et à apprendre.
    </p>

    <p class="email-text">
        Excellente semaine d'apprentissage ! 🚀<br>
        <strong>L'équipe AutomateHub</strong>
    </p>

    <div style="text-align: center; margin: 20px 0; font-size: 12px; color: #6b7280;">
        <p>Vous recevez ce résumé hebdomadaire car vous êtes actif sur AutomateHub.</p>
        <a href="{{ url('/settings/notifications') }}" style="color: #3b82f6; text-decoration: none;">
            Gérer mes notifications
        </a>
    </div>
@endsection