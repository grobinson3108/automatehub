<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\TutorialService;
use App\Services\RestrictionService;
use App\Services\AnalyticsService;
use App\Services\BadgeService;
use App\Models\Tutorial;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\UserTutorialProgress;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class TutorialController extends Controller
{
    protected TutorialService $tutorialService;
    protected RestrictionService $restrictionService;
    protected AnalyticsService $analyticsService;
    protected BadgeService $badgeService;

    public function __construct(
        TutorialService $tutorialService,
        RestrictionService $restrictionService,
        AnalyticsService $analyticsService,
        BadgeService $badgeService
    ) {
        $this->tutorialService = $tutorialService;
        $this->restrictionService = $restrictionService;
        $this->analyticsService = $analyticsService;
        $this->badgeService = $badgeService;
    }

    /**
     * Display tutorials accessible according to user subscription and level.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Construire la requête avec les restrictions d'accès
        $query = Tutorial::with(['category', 'tags'])
            ->published();

        // Appliquer les restrictions selon l'abonnement
        if (!$this->restrictionService->canAccessPremium($user)) {
            $query->where('subscription_required', 'free');
        } elseif (!$this->restrictionService->canAccessPro($user)) {
            $query->whereIn('subscription_required', ['free', 'premium']);
        }

        // Filtres
        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        if ($request->filled('level')) {
            $query->where('difficulty_level', $request->get('level'));
        }

        if ($request->filled('audience')) {
            $audience = $request->get('audience');
            if ($audience !== 'both') {
                $query->where(function($q) use ($audience) {
                    $q->where('target_audience', $audience)
                      ->orWhere('target_audience', 'both');
                });
            }
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        switch ($sortBy) {
            case 'popularity':
                $query->withCount('downloads')->orderBy('downloads_count', 'desc');
                break;
            case 'difficulty':
                $query->orderByRaw("FIELD(difficulty_level, 'beginner', 'intermediate', 'advanced', 'expert')");
                break;
            case 'duration':
                $query->orderBy('estimated_duration', $sortOrder);
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        $tutorials = $query->paginate(12);

        // Ajouter les informations de progression et favoris
        $tutorials->getCollection()->transform(function ($tutorial) use ($user) {
            $tutorial->is_favorite = $user->favorites()
                ->where('tutorial_id', $tutorial->id)
                ->exists();
            
            $progress = $user->tutorialProgress()
                ->where('tutorial_id', $tutorial->id)
                ->first();
            
            $tutorial->progress = $progress ? [
                'completed' => $progress->completed,
                'progress_percentage' => $progress->progress_percentage,
                'last_accessed' => $progress->updated_at,
            ] : null;

            $tutorial->can_access = $this->restrictionService->canAccessTutorial($user, $tutorial);
            
            return $tutorial;
        });

        // Données pour les filtres
        $categories = Category::all();
        $userStats = [
            'subscription_type' => $user->subscription_type,
            'n8n_level' => $user->n8n_level,
            'is_professional' => $user->is_professional,
        ];

        // Tracking
        $this->analyticsService->track($user->id, 'tutorials_browsed', [
            'filters' => $request->only(['category', 'level', 'audience', 'search']),
            'sort' => $sortBy,
        ]);

        return view('user.tutorials.index', compact('tutorials', 'categories', 'userStats'));
    }

    /**
     * Display the specified tutorial with access verification.
     */
    public function show($id)
    {
        $user = auth()->user();
        $tutorial = Tutorial::with(['category', 'tags', 'downloads', 'favorites'])
            ->findOrFail($id);

        // Vérifier l'accès au tutoriel
        if (!$this->restrictionService->canAccessTutorial($user, $tutorial)) {
            return redirect()->route('user.tutorials.index')
                ->with('error', 'Vous n\'avez pas accès à ce tutoriel. Veuillez upgrader votre abonnement.');
        }

        // Informations de progression
        $progress = $user->tutorialProgress()
            ->where('tutorial_id', $tutorial->id)
            ->first();

        if (!$progress) {
            // Créer une entrée de progression si elle n'existe pas
            $progress = UserTutorialProgress::create([
                'user_id' => $user->id,
                'tutorial_id' => $tutorial->id,
                'progress_percentage' => 0,
                'completed' => false,
            ]);
        }

        // Marquer comme consulté
        $progress->update(['updated_at' => now()]);

        // Vérifier si c'est un favori
        $isFavorite = $user->favorites()
            ->where('tutorial_id', $tutorial->id)
            ->exists();

        // Vérifier si déjà téléchargé
        $hasDownloaded = $user->downloads()
            ->where('tutorial_id', $tutorial->id)
            ->exists();

        // Tutoriels similaires
        $similarTutorials = $this->tutorialService->getSimilarTutorials($tutorial->id, $user)
            ->take(4);

        // Fichiers disponibles
        $files = $tutorial->files ? json_decode($tutorial->files, true) : [];

        // Restrictions de téléchargement
        $downloadRestrictions = [
            'can_download' => $this->restrictionService->canDownload($user),
            'remaining_downloads' => $this->restrictionService->getRemainingDownloads($user->id),
            'download_limit' => $this->restrictionService->getDownloadLimit($user),
        ];

        // Tracking
        $this->analyticsService->track($user->id, 'tutorial_viewed', [
            'tutorial_id' => $tutorial->id,
            'tutorial_title' => $tutorial->title,
            'subscription_type' => $tutorial->subscription_required,
            'difficulty_level' => $tutorial->difficulty_level,
        ]);

        return view('user.tutorials.show', compact(
            'tutorial',
            'progress',
            'isFavorite',
            'hasDownloaded',
            'similarTutorials',
            'files',
            'downloadRestrictions'
        ));
    }

    /**
     * Add or remove tutorial from favorites.
     */
    public function favorite($id): JsonResponse
    {
        $user = auth()->user();
        $tutorial = Tutorial::findOrFail($id);

        // Vérifier l'accès au tutoriel
        if (!$this->restrictionService->canAccessTutorial($user, $tutorial)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à ce tutoriel.'
            ], 403);
        }

        $favorite = $user->favorites()->where('tutorial_id', $id)->first();

        if ($favorite) {
            // Retirer des favoris
            $favorite->delete();
            $isFavorite = false;
            $message = 'Tutoriel retiré des favoris';
        } else {
            // Ajouter aux favoris
            Favorite::create([
                'user_id' => $user->id,
                'tutorial_id' => $id,
            ]);
            $isFavorite = true;
            $message = 'Tutoriel ajouté aux favoris';
        }

        // Tracking
        $this->analyticsService->track($user->id, $isFavorite ? 'tutorial_favorited' : 'tutorial_unfavorited', [
            'tutorial_id' => $id,
            'tutorial_title' => $tutorial->title,
        ]);

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
            'message' => $message,
        ]);
    }

    /**
     * Mark tutorial as completed.
     */
    public function markCompleted($id): JsonResponse
    {
        $user = auth()->user();
        $tutorial = Tutorial::findOrFail($id);

        // Vérifier l'accès au tutoriel
        if (!$this->restrictionService->canAccessTutorial($user, $tutorial)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à ce tutoriel.'
            ], 403);
        }

        $progress = $user->tutorialProgress()
            ->where('tutorial_id', $id)
            ->first();

        if (!$progress) {
            $progress = UserTutorialProgress::create([
                'user_id' => $user->id,
                'tutorial_id' => $id,
                'progress_percentage' => 100,
                'completed' => true,
                'completed_at' => now(),
            ]);
        } else {
            $progress->update([
                'progress_percentage' => 100,
                'completed' => true,
                'completed_at' => now(),
            ]);
        }

        // Déclencher l'événement de completion
        event(new \App\Events\TutorialCompleted($user, $tutorial));

        // Vérifier les badges à attribuer
        $this->badgeService->checkAndAwardBadges($user->id);

        // Tracking
        $this->analyticsService->track($user->id, 'tutorial_completed', [
            'tutorial_id' => $id,
            'tutorial_title' => $tutorial->title,
            'difficulty_level' => $tutorial->difficulty_level,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Félicitations ! Tutoriel marqué comme terminé.',
            'progress' => [
                'completed' => true,
                'progress_percentage' => 100,
                'completed_at' => $progress->completed_at,
            ],
        ]);
    }

    /**
     * Secure file download with access verification.
     */
    public function download($tutorialId, $fileId)
    {
        $user = auth()->user();
        $tutorial = Tutorial::findOrFail($tutorialId);

        // Vérifier l'accès au tutoriel
        if (!$this->restrictionService->canAccessTutorial($user, $tutorial)) {
            return redirect()->back()
                ->with('error', 'Vous n\'avez pas accès à ce tutoriel.');
        }

        // Vérifier les limites de téléchargement
        if (!$this->restrictionService->canDownload($user)) {
            return redirect()->back()
                ->with('error', 'Vous avez atteint votre limite de téléchargements pour ce mois.');
        }

        // Récupérer les fichiers du tutoriel
        $files = $tutorial->files ? json_decode($tutorial->files, true) : [];
        
        if (!isset($files[$fileId])) {
            return redirect()->back()
                ->with('error', 'Fichier non trouvé.');
        }

        $file = $files[$fileId];
        $filePath = storage_path('app/' . $file['path']);

        if (!file_exists($filePath)) {
            return redirect()->back()
                ->with('error', 'Le fichier n\'existe plus sur le serveur.');
        }

        // Enregistrer le téléchargement
        $download = Download::create([
            'user_id' => $user->id,
            'tutorial_id' => $tutorialId,
            'file_name' => $file['original_name'],
            'file_path' => $file['path'],
            'file_size' => $file['size'],
            'downloaded_at' => now(),
        ]);

        // Déclencher l'événement de téléchargement
        event(new \App\Events\DownloadCompleted($user, $tutorial, $file['original_name']));

        // Tracking
        $this->analyticsService->track($user->id, 'file_downloaded', [
            'tutorial_id' => $tutorialId,
            'tutorial_title' => $tutorial->title,
            'file_name' => $file['original_name'],
            'file_size' => $file['size'],
        ]);

        // Retourner le fichier
        return Response::download($filePath, $file['original_name']);
    }

    /**
     * Get user's favorite tutorials.
     */
    public function getFavorites(): JsonResponse
    {
        $user = auth()->user();

        $favorites = $user->favorites()
            ->with(['tutorial' => function($query) {
                $query->select('id', 'title', 'description', 'thumbnail', 'difficulty_level', 'estimated_duration')
                      ->published();
            }])
            ->latest()
            ->get()
            ->map(function ($favorite) use ($user) {
                $tutorial = $favorite->tutorial;
                if (!$tutorial) return null;

                $progress = $user->tutorialProgress()
                    ->where('tutorial_id', $tutorial->id)
                    ->first();

                return [
                    'id' => $tutorial->id,
                    'title' => $tutorial->title,
                    'description' => $tutorial->description,
                    'thumbnail' => $tutorial->thumbnail,
                    'difficulty_level' => $tutorial->difficulty_level,
                    'estimated_duration' => $tutorial->estimated_duration,
                    'favorited_at' => $favorite->created_at,
                    'progress' => $progress ? [
                        'completed' => $progress->completed,
                        'progress_percentage' => $progress->progress_percentage,
                    ] : null,
                    'can_access' => $this->restrictionService->canAccessTutorial($user, $tutorial),
                ];
            })
            ->filter()
            ->values();

        return response()->json($favorites);
    }

    /**
     * Get user's tutorial consultation history.
     */
    public function getHistory(): JsonResponse
    {
        $user = auth()->user();

        $history = $user->tutorialProgress()
            ->with(['tutorial' => function($query) {
                $query->select('id', 'title', 'description', 'thumbnail', 'difficulty_level', 'estimated_duration')
                      ->published();
            }])
            ->latest('updated_at')
            ->get()
            ->map(function ($progress) use ($user) {
                $tutorial = $progress->tutorial;
                if (!$tutorial) return null;

                return [
                    'id' => $tutorial->id,
                    'title' => $tutorial->title,
                    'description' => $tutorial->description,
                    'thumbnail' => $tutorial->thumbnail,
                    'difficulty_level' => $tutorial->difficulty_level,
                    'estimated_duration' => $tutorial->estimated_duration,
                    'progress' => [
                        'completed' => $progress->completed,
                        'progress_percentage' => $progress->progress_percentage,
                        'last_accessed' => $progress->updated_at,
                        'completed_at' => $progress->completed_at,
                    ],
                    'can_access' => $this->restrictionService->canAccessTutorial($user, $tutorial),
                ];
            })
            ->filter()
            ->values();

        return response()->json($history);
    }

    /**
     * Update tutorial progress.
     */
    public function updateProgress(Request $request, $id): JsonResponse
    {
        $user = auth()->user();
        $tutorial = Tutorial::findOrFail($id);

        // Vérifier l'accès au tutoriel
        if (!$this->restrictionService->canAccessTutorial($user, $tutorial)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à ce tutoriel.'
            ], 403);
        }

        $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
        ]);

        $progressPercentage = $request->get('progress_percentage');
        $isCompleted = $progressPercentage >= 100;

        $progress = $user->tutorialProgress()
            ->where('tutorial_id', $id)
            ->first();

        if (!$progress) {
            $progress = UserTutorialProgress::create([
                'user_id' => $user->id,
                'tutorial_id' => $id,
                'progress_percentage' => $progressPercentage,
                'completed' => $isCompleted,
                'completed_at' => $isCompleted ? now() : null,
            ]);
        } else {
            $wasCompleted = $progress->completed;
            $progress->update([
                'progress_percentage' => $progressPercentage,
                'completed' => $isCompleted,
                'completed_at' => $isCompleted && !$wasCompleted ? now() : $progress->completed_at,
            ]);

            // Si le tutoriel vient d'être complété
            if ($isCompleted && !$wasCompleted) {
                event(new \App\Events\TutorialCompleted($user, $tutorial));
                $this->badgeService->checkAndAwardBadges($user->id);
            }
        }

        // Tracking
        $this->analyticsService->track($user->id, 'tutorial_progress_updated', [
            'tutorial_id' => $id,
            'progress_percentage' => $progressPercentage,
            'completed' => $isCompleted,
        ]);

        return response()->json([
            'success' => true,
            'progress' => [
                'completed' => $progress->completed,
                'progress_percentage' => $progress->progress_percentage,
                'completed_at' => $progress->completed_at,
            ],
        ]);
    }

    /**
     * Get tutorials filtered by subscription type.
     */
    public function getBySubscription($type): JsonResponse
    {
        $user = auth()->user();

        // Vérifier que l'utilisateur peut accéder à ce type de contenu
        if ($type === 'premium' && !$this->restrictionService->canAccessPremium($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Accès premium requis.'
            ], 403);
        }

        if ($type === 'pro' && !$this->restrictionService->canAccessPro($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Accès pro requis.'
            ], 403);
        }

        $tutorials = Tutorial::where('subscription_required', $type)
            ->published()
            ->with(['category', 'tags'])
            ->latest()
            ->paginate(12);

        return response()->json($tutorials);
    }
}
