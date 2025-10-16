<?php

namespace App\Services;

use App\Models\Tutorial;
use App\Models\User;
use App\Models\UserTutorialProgress;
use App\Models\Favorite;
use App\Models\Analytics;
use App\Models\Download;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class TutorialService
{
    /**
     * Récupère les tutoriels filtrés selon le niveau et l'abonnement de l'utilisateur
     */
    public function getFilteredTutorials($userId, $filters = [])
    {
        $user = User::findOrFail($userId);
        
        $query = Tutorial::query()
            ->where('is_published', true)
            ->with(['category', 'tags']);

        // Filtrer selon l'abonnement de l'utilisateur
        $query = $this->applySubscriptionFilter($query, $user->subscription_type);

        // Appliquer les filtres demandés
        if (isset($filters['level']) && !empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (isset($filters['category_id']) && !empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['tags']) && !empty($filters['tags'])) {
            $query->whereHas('tags', function (Builder $q) use ($filters) {
                $q->whereIn('tags.id', $filters['tags']);
            });
        }

        if (isset($filters['difficulty']) && !empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (isset($filters['duration_min']) && !empty($filters['duration_min'])) {
            $query->where('duration_minutes', '>=', $filters['duration_min']);
        }

        if (isset($filters['duration_max']) && !empty($filters['duration_max'])) {
            $query->where('duration_minutes', '<=', $filters['duration_max']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('content', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Tri
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        
        switch ($sortBy) {
            case 'popularity':
                $query->withCount('views')
                      ->orderBy('views_count', $sortOrder);
                break;
            case 'difficulty':
                $query->orderByRaw("FIELD(difficulty, 'beginner', 'intermediate', 'advanced') " . $sortOrder);
                break;
            case 'duration':
                $query->orderBy('duration_minutes', $sortOrder);
                break;
            case 'title':
                $query->orderBy('title', $sortOrder);
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        // Ajouter les informations de progression pour l'utilisateur
        $tutorials = $query->get()->map(function ($tutorial) use ($userId) {
            $tutorial->user_progress = $this->getUserTutorialProgress($userId, $tutorial->id);
            $tutorial->is_favorite = $this->isFavorite($userId, $tutorial->id);
            $tutorial->can_access = $this->canAccessTutorial($userId, $tutorial->id);
            return $tutorial;
        });

        return $tutorials;
    }

    /**
     * Vérifie si un utilisateur peut accéder à un tutoriel
     */
    public function canAccessTutorial($userId, $tutorialId)
    {
        $user = User::findOrFail($userId);
        $tutorial = Tutorial::findOrFail($tutorialId);

        // Vérifier si le tutoriel est publié
        if (!$tutorial->is_published) {
            return false;
        }

        // Vérifier les restrictions d'abonnement
        switch ($tutorial->subscription_required) {
            case 'free':
                return true; // Accessible à tous
            case 'premium':
                return in_array($user->subscription_type, ['premium', 'pro']);
            case 'pro':
                return $user->subscription_type === 'pro';
            default:
                return false;
        }
    }

    /**
     * Récupère les recommandations personnalisées pour un utilisateur
     */
    public function getRecommendations($userId, $limit = 10)
    {
        $user = User::findOrFail($userId);
        $recommendations = [];

        // 1. Tutoriels basés sur le niveau n8n de l'utilisateur
        $levelBasedTutorials = $this->getLevelBasedRecommendations($user, 4);
        $recommendations = array_merge($recommendations, $levelBasedTutorials);

        // 2. Tutoriels similaires aux favoris
        $favoriteBasedTutorials = $this->getFavoriteBasedRecommendations($userId, 3);
        $recommendations = array_merge($recommendations, $favoriteBasedTutorials);

        // 3. Tutoriels populaires non vus
        $popularTutorials = $this->getPopularUnseenTutorials($userId, 3);
        $recommendations = array_merge($recommendations, $popularTutorials);

        // 4. Tutoriels récents accessibles
        $recentTutorials = $this->getRecentAccessibleTutorials($user, 2);
        $recommendations = array_merge($recommendations, $recentTutorials);

        // Supprimer les doublons et limiter
        $uniqueRecommendations = collect($recommendations)
            ->unique('id')
            ->take($limit)
            ->values();

        // Ajouter les informations utilisateur
        return $uniqueRecommendations->map(function ($tutorial) use ($userId) {
            $tutorial->user_progress = $this->getUserTutorialProgress($userId, $tutorial->id);
            $tutorial->is_favorite = $this->isFavorite($userId, $tutorial->id);
            $tutorial->can_access = $this->canAccessTutorial($userId, $tutorial->id);
            $tutorial->recommendation_reason = $tutorial->recommendation_reason ?? 'Recommandé pour vous';
            return $tutorial;
        });
    }

    /**
     * Marque un tutoriel comme complété pour un utilisateur
     */
    public function markAsCompleted($userId, $tutorialId)
    {
        $user = User::findOrFail($userId);
        $tutorial = Tutorial::findOrFail($tutorialId);

        // Vérifier l'accès
        if (!$this->canAccessTutorial($userId, $tutorialId)) {
            throw new \Exception('Accès non autorisé à ce tutoriel');
        }

        // Mettre à jour ou créer la progression
        $progress = UserTutorialProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'tutorial_id' => $tutorialId
            ],
            [
                'completed' => true,
                'completed_at' => now(),
                'progress_percentage' => 100
            ]
        );

        // Enregistrer l'analytics
        app(AnalyticsService::class)->trackTutorialView($userId, $tutorialId);

        // Vérifier et attribuer les badges
        app(BadgeService::class)->checkAndAwardBadges($userId);

        return $progress;
    }

    /**
     * Met à jour la progression d'un tutoriel
     */
    public function updateProgress($userId, $tutorialId, $progressPercentage)
    {
        // Vérifier l'accès
        if (!$this->canAccessTutorial($userId, $tutorialId)) {
            throw new \Exception('Accès non autorisé à ce tutoriel');
        }

        $progress = UserTutorialProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'tutorial_id' => $tutorialId
            ],
            [
                'progress_percentage' => min(100, max(0, $progressPercentage)),
                'last_accessed_at' => now(),
                'completed' => $progressPercentage >= 100,
                'completed_at' => $progressPercentage >= 100 ? now() : null
            ]
        );

        // Si complété, déclencher les actions associées
        if ($progressPercentage >= 100) {
            app(BadgeService::class)->checkAndAwardBadges($userId);
        }

        return $progress;
    }

    /**
     * Ajoute ou retire un tutoriel des favoris
     */
    public function toggleFavorite($userId, $tutorialId)
    {
        $favorite = Favorite::where('user_id', $userId)
            ->where('tutorial_id', $tutorialId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return false; // Retiré des favoris
        } else {
            Favorite::create([
                'user_id' => $userId,
                'tutorial_id' => $tutorialId
            ]);
            return true; // Ajouté aux favoris
        }
    }

    /**
     * Récupère les tutoriels favoris d'un utilisateur
     */
    public function getFavorites($userId)
    {
        $favorites = Tutorial::whereHas('favorites', function (Builder $query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with(['category', 'tags'])
        ->get();

        return $favorites->map(function ($tutorial) use ($userId) {
            $tutorial->user_progress = $this->getUserTutorialProgress($userId, $tutorial->id);
            $tutorial->is_favorite = true;
            $tutorial->can_access = $this->canAccessTutorial($userId, $tutorial->id);
            return $tutorial;
        });
    }

    /**
     * Récupère l'historique des tutoriels consultés
     */
    public function getHistory($userId, $limit = 20)
    {
        $tutorialIds = Analytics::where('user_id', $userId)
            ->where('event_type', 'tutorial_view')
            ->orderBy('created_at', 'desc')
            ->take($limit * 2) // Prendre plus pour compenser les doublons
            ->get()
            ->map(function ($analytic) {
                $data = json_decode($analytic->event_data, true);
                return $data['tutorial_id'] ?? null;
            })
            ->filter()
            ->unique()
            ->take($limit);

        if ($tutorialIds->isEmpty()) {
            return collect();
        }

        $tutorials = Tutorial::whereIn('id', $tutorialIds)
            ->with(['category', 'tags'])
            ->get()
            ->sortBy(function ($tutorial) use ($tutorialIds) {
                return $tutorialIds->search($tutorial->id);
            });

        return $tutorials->map(function ($tutorial) use ($userId) {
            $tutorial->user_progress = $this->getUserTutorialProgress($userId, $tutorial->id);
            $tutorial->is_favorite = $this->isFavorite($userId, $tutorial->id);
            $tutorial->can_access = $this->canAccessTutorial($userId, $tutorial->id);
            return $tutorial;
        });
    }

    /**
     * Applique le filtre d'abonnement à la requête
     */
    private function applySubscriptionFilter(Builder $query, $subscriptionType)
    {
        switch ($subscriptionType) {
            case 'free':
                return $query->where('subscription_required', 'free');
            case 'premium':
                return $query->whereIn('subscription_required', ['free', 'premium']);
            case 'pro':
                return $query->whereIn('subscription_required', ['free', 'premium', 'pro']);
            default:
                return $query->where('subscription_required', 'free');
        }
    }

    /**
     * Récupère la progression d'un utilisateur pour un tutoriel
     */
    private function getUserTutorialProgress($userId, $tutorialId)
    {
        return UserTutorialProgress::where('user_id', $userId)
            ->where('tutorial_id', $tutorialId)
            ->first();
    }

    /**
     * Vérifie si un tutoriel est en favori
     */
    private function isFavorite($userId, $tutorialId)
    {
        return Favorite::where('user_id', $userId)
            ->where('tutorial_id', $tutorialId)
            ->exists();
    }

    /**
     * Recommandations basées sur le niveau n8n
     */
    private function getLevelBasedRecommendations(User $user, $limit)
    {
        $query = Tutorial::where('is_published', true)
            ->where('level', $user->n8n_level);

        $query = $this->applySubscriptionFilter($query, $user->subscription_type);

        $tutorials = $query->with(['category', 'tags'])
            ->inRandomOrder()
            ->take($limit)
            ->get();

        $tutorials->each(function ($tutorial) {
            $tutorial->recommendation_reason = 'Adapté à votre niveau ' . $tutorial->level;
        });

        return $tutorials->toArray();
    }

    /**
     * Recommandations basées sur les favoris
     */
    private function getFavoriteBasedRecommendations($userId, $limit)
    {
        // Récupérer les catégories des tutoriels favoris
        $favoriteCategories = Favorite::where('user_id', $userId)
            ->join('tutorials', 'favorites.tutorial_id', '=', 'tutorials.id')
            ->pluck('tutorials.category_id')
            ->unique();

        if ($favoriteCategories->isEmpty()) {
            return [];
        }

        $user = User::findOrFail($userId);
        $query = Tutorial::where('is_published', true)
            ->whereIn('category_id', $favoriteCategories)
            ->whereNotIn('id', function ($subQuery) use ($userId) {
                $subQuery->select('tutorial_id')
                    ->from('favorites')
                    ->where('user_id', $userId);
            });

        $query = $this->applySubscriptionFilter($query, $user->subscription_type);

        $tutorials = $query->with(['category', 'tags'])
            ->inRandomOrder()
            ->take($limit)
            ->get();

        $tutorials->each(function ($tutorial) {
            $tutorial->recommendation_reason = 'Similaire à vos favoris';
        });

        return $tutorials->toArray();
    }

    /**
     * Tutoriels populaires non vus
     */
    private function getPopularUnseenTutorials($userId, $limit)
    {
        $user = User::findOrFail($userId);
        
        // Récupérer les tutoriels les plus vus
        $popularTutorialIds = Analytics::where('event_type', 'tutorial_view')
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->groupBy(function ($item) {
                $data = json_decode($item->event_data, true);
                return $data['tutorial_id'] ?? null;
            })
            ->map(function ($group) {
                return $group->count();
            })
            ->sortDesc()
            ->take($limit * 2)
            ->keys();

        // Exclure ceux déjà vus par l'utilisateur
        $seenTutorialIds = Analytics::where('user_id', $userId)
            ->where('event_type', 'tutorial_view')
            ->get()
            ->map(function ($analytic) {
                $data = json_decode($analytic->event_data, true);
                return $data['tutorial_id'] ?? null;
            })
            ->filter()
            ->unique();

        $unseenPopularIds = $popularTutorialIds->diff($seenTutorialIds)->take($limit);

        if ($unseenPopularIds->isEmpty()) {
            return [];
        }

        $query = Tutorial::whereIn('id', $unseenPopularIds)
            ->where('is_published', true);

        $query = $this->applySubscriptionFilter($query, $user->subscription_type);

        $tutorials = $query->with(['category', 'tags'])->get();

        $tutorials->each(function ($tutorial) {
            $tutorial->recommendation_reason = 'Populaire cette semaine';
        });

        return $tutorials->toArray();
    }

    /**
     * Tutoriels récents accessibles
     */
    private function getRecentAccessibleTutorials(User $user, $limit)
    {
        $query = Tutorial::where('is_published', true)
            ->where('created_at', '>=', now()->subDays(14));

        $query = $this->applySubscriptionFilter($query, $user->subscription_type);

        $tutorials = $query->with(['category', 'tags'])
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        $tutorials->each(function ($tutorial) {
            $tutorial->recommendation_reason = 'Nouveau contenu';
        });

        return $tutorials->toArray();
    }
}
