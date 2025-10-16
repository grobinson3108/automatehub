<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the user's profile index page.
     */
    public function index()
    {
        return $this->show();
    }

    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = auth()->user();

        // Statistiques du profil
        $profileStats = [
            'total_downloads' => $user->downloads()->count(),
            'tutorials_completed' => $user->progress()->completed()->count(),
            'badges_earned' => $user->badges()->count(),
            'favorites_count' => $user->favorites()->count(),
            'member_since' => $user->created_at,
            'last_activity' => $user->last_activity_at,
        ];

        // Badges récents
        $recentBadges = $user->badges()
            ->orderByPivot('created_at', 'desc')
            ->limit(6)
            ->get();

        // Activité récente
        $recentActivity = [
            'downloads' => $user->downloads()
                ->with('tutorial:id,title')
                ->latest('downloaded_at')
                ->limit(5)
                ->get(),
            'completions' => $user->progress()
                ->completed()
                ->with('tutorial:id,title')
                ->latest('completed_at')
                ->limit(5)
                ->get(),
        ];

        // Tracking
        $this->analyticsService->track($user->id, 'profile_viewed');

        return view('user.profile.index', compact('user', 'profileStats', 'recentBadges', 'recentActivity'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = auth()->user();

        return view('user.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'is_professional' => 'boolean',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'vat_number' => 'nullable|string|max:50',
        ];

        // Si l'utilisateur passe en professionnel, company_name devient obligatoire
        if ($request->get('is_professional')) {
            $rules['company_name'] = 'required|string|max:255';
        }

        $validated = $request->validate($rules);

        // Nettoyer les données
        $validated['is_professional'] = filter_var($validated['is_professional'] ?? false, FILTER_VALIDATE_BOOLEAN);
        
        // Si l'utilisateur n'est plus professionnel, vider les champs pro
        if (!$validated['is_professional']) {
            $validated['company_name'] = null;
            $validated['vat_number'] = null;
        }

        // Nettoyer l'email
        $validated['email'] = strtolower(trim($validated['email']));

        // Nettoyer les champs texte
        foreach (['name', 'phone', 'bio', 'company_name', 'address', 'city', 'country'] as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = trim($validated[$field]);
            }
        }

        // Nettoyer le code postal et le numéro de TVA
        if (isset($validated['postal_code'])) {
            $validated['postal_code'] = preg_replace('/[^0-9A-Za-z\-\s]/', '', $validated['postal_code']);
        }

        if (isset($validated['vat_number'])) {
            $validated['vat_number'] = strtoupper(preg_replace('/[^0-9A-Za-z]/', '', $validated['vat_number']));
        }

        $user->update($validated);

        // Tracking
        $this->analyticsService->track($user->id, 'profile_updated', [
            'fields_updated' => array_keys($validated),
            'is_professional' => $validated['is_professional'],
        ]);

        return redirect()->route('user.profile.index')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Vérifier le mot de passe actuel
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                ->withInput();
        }

        // Mettre à jour le mot de passe
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Tracking
        $this->analyticsService->track($user->id, 'password_updated');

        return redirect()->route('user.profile.index')
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }

    /**
     * Toggle between professional and individual account.
     */
    public function toggleProfessional(Request $request): JsonResponse
    {
        $user = auth()->user();
        $isProfessional = filter_var($request->get('is_professional'), FILTER_VALIDATE_BOOLEAN);

        if ($isProfessional) {
            // Validation des champs professionnels requis
            $request->validate([
                'company_name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:10',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'vat_number' => 'nullable|string|max:50',
            ]);

            $updateData = [
                'is_professional' => true,
                'company_name' => trim($request->get('company_name')),
                'address' => trim($request->get('address')),
                'postal_code' => preg_replace('/[^0-9A-Za-z\-\s]/', '', $request->get('postal_code')),
                'city' => trim($request->get('city')),
                'country' => trim($request->get('country')),
                'vat_number' => strtoupper(preg_replace('/[^0-9A-Za-z]/', '', $request->get('vat_number'))),
            ];
        } else {
            $updateData = [
                'is_professional' => false,
                'company_name' => null,
                'address' => null,
                'postal_code' => null,
                'city' => null,
                'country' => null,
                'vat_number' => null,
            ];
        }

        $user->update($updateData);

        // Tracking
        $this->analyticsService->track($user->id, 'account_type_changed', [
            'from' => $user->is_professional ? 'professional' : 'individual',
            'to' => $isProfessional ? 'professional' : 'individual',
        ]);

        return response()->json([
            'success' => true,
            'message' => $isProfessional 
                ? 'Compte converti en professionnel avec succès.' 
                : 'Compte converti en particulier avec succès.',
            'is_professional' => $isProfessional,
        ]);
    }

    /**
     * Upload user avatar.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $user = auth()->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        try {
            // Supprimer l'ancien avatar s'il existe
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Stocker le nouveau avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            // Mettre à jour l'utilisateur
            $user->update(['avatar' => $avatarPath]);

            // Tracking
            $this->analyticsService->track($user->id, 'avatar_uploaded');

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès.',
                'avatar_url' => Storage::disk('public')->url($avatarPath),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload de la photo : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove user avatar.
     */
    public function removeAvatar(): JsonResponse
    {
        $user = auth()->user();

        if ($user->avatar) {
            // Supprimer le fichier
            Storage::disk('public')->delete($user->avatar);

            // Mettre à jour l'utilisateur
            $user->update(['avatar' => null]);

            // Tracking
            $this->analyticsService->track($user->id, 'avatar_removed');

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil supprimée avec succès.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Aucune photo de profil à supprimer.',
        ], 400);
    }

    /**
     * Get user's profile statistics.
     */
    public function getStats(): JsonResponse
    {
        $user = auth()->user();

        $stats = [
            'downloads' => [
                'total' => $user->downloads()->count(),
                'this_month' => $user->downloads()
                    ->whereMonth('downloaded_at', now()->month)
                    ->count(),
                'this_week' => $user->downloads()
                    ->where('downloaded_at', '>=', now()->subWeek())
                    ->count(),
            ],
            'tutorials' => [
                'completed' => $user->progress()->completed()->count(),
                'in_progress' => $user->progress()->inProgress()->count(),
                'favorites' => $user->favorites()->count(),
            ],
            'badges' => [
                'total' => $user->badges()->count(),
                'recent' => $user->badges()
                    ->wherePivot('created_at', '>=', now()->subDays(30))
                    ->count(),
            ],
            'activity' => [
                'member_since_days' => $user->created_at->diffInDays(now()),
                'last_activity' => $user->last_activity_at,
                'login_streak' => $this->calculateLoginStreak($user),
            ],
        ];

        return response()->json($stats);
    }

    /**
     * Display the user's statistics page.
     */
    public function statistics()
    {
        $user = auth()->user();
        
        // Récupérer les statistiques de l'utilisateur
        $stats = [
            'downloads' => [
                'total' => $user->downloads()->count(),
                'this_month' => $user->downloads()
                    ->whereMonth('downloaded_at', now()->month)
                    ->count(),
                'this_week' => $user->downloads()
                    ->where('downloaded_at', '>=', now()->subWeek())
                    ->count(),
            ],
            'tutorials' => [
                'completed' => $user->progress()->completed()->count(),
                'in_progress' => $user->progress()->inProgress()->count(),
                'favorites' => $user->favorites()->count(),
            ],
            'badges' => [
                'total' => $user->badges()->count(),
                'recent' => $user->badges()
                    ->wherePivot('created_at', '>=', now()->subDays(30))
                    ->count(),
            ],
            'activity' => [
                'member_since_days' => $user->created_at->diffInDays(now()),
                'last_activity' => $user->last_activity_at,
                'login_streak' => $this->calculateLoginStreak($user),
            ],
        ];
        
        return view('user.profile.statistics', compact('user', 'stats'));
    }
    
    /**
     * Display the user's activity page.
     */
    public function activity()
    {
        $user = auth()->user();
        
        // Récupérer l'activité récente de l'utilisateur
        $activities = collect();

        // Téléchargements récents
        $downloads = $user->downloads()
            ->with('tutorial:id,title')
            ->latest('downloaded_at')
            ->limit(10)
            ->get()
            ->map(function ($download) {
                return [
                    'type' => 'download',
                    'title' => 'Téléchargement',
                    'description' => 'Téléchargement du tutoriel "' . $download->tutorial->title . '"',
                    'date' => $download->downloaded_at,
                    'icon' => 'download',
                    'color' => 'primary',
                ];
            });

        // Tutoriels complétés
        $completions = $user->progress()
            ->completed()
            ->with('tutorial:id,title')
            ->latest('completed_at')
            ->limit(10)
            ->get()
            ->map(function ($progress) {
                return [
                    'type' => 'completion',
                    'title' => 'Tutoriel terminé',
                    'description' => 'Completion du tutoriel "' . $progress->tutorial->title . '"',
                    'date' => $progress->completed_at,
                    'icon' => 'check-circle',
                    'color' => 'success',
                ];
            });

        // Badges obtenus
        $badges = $user->badges()
            ->wherePivot('created_at', '>=', now()->subDays(90))
            ->orderByPivot('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($badge) {
                return [
                    'type' => 'badge',
                    'title' => 'Nouveau badge',
                    'description' => 'Badge "' . $badge->name . '" obtenu',
                    'date' => $badge->pivot->created_at,
                    'icon' => 'award',
                    'color' => 'warning',
                ];
            });

        // Favoris ajoutés
        $favorites = $user->favorites()
            ->with('tutorial:id,title')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($favorite) {
                return [
                    'type' => 'favorite',
                    'title' => 'Favori ajouté',
                    'description' => 'Ajout aux favoris : "' . $favorite->tutorial->title . '"',
                    'date' => $favorite->created_at,
                    'icon' => 'heart',
                    'color' => 'danger',
                ];
            });

        // Fusionner et trier
        $timeline = $activities
            ->merge($downloads)
            ->merge($completions)
            ->merge($badges)
            ->merge($favorites)
            ->sortByDesc('date')
            ->take(20)
            ->values();
        
        return view('user.profile.activity', compact('user', 'timeline'));
    }
    
    /**
     * Display the user's preferences page.
     */
    public function preferences()
    {
        $user = auth()->user();
        
        return view('user.profile.preferences', compact('user'));
    }
    
    /**
     * Display the user's subscription page.
     */
    public function subscription()
    {
        $user = auth()->user();
        
        return view('user.profile.subscription', compact('user'));
    }
    
    /**
     * Display the user's notifications page.
     */
    public function notifications()
    {
        $user = auth()->user();
        
        return view('user.profile.notifications', compact('user'));
    }
    
    /**
     * Display the user's security page.
     */
    public function security()
    {
        $user = auth()->user();
        
        return view('user.profile.security', compact('user'));
    }
    
    /**
     * Display the user's support page.
     */
    public function support()
    {
        $user = auth()->user();
        
        return view('user.profile.support', compact('user'));
    }
    
    /**
     * Get user's activity timeline.
     */
    public function getActivityTimeline(): JsonResponse
    {
        $user = auth()->user();

        $activities = collect();

        // Téléchargements récents
        $downloads = $user->downloads()
            ->with('tutorial:id,title')
            ->latest('downloaded_at')
            ->limit(10)
            ->get()
            ->map(function ($download) {
                return [
                    'type' => 'download',
                    'title' => 'Téléchargement',
                    'description' => 'Téléchargement du tutoriel "' . $download->tutorial->title . '"',
                    'date' => $download->downloaded_at,
                    'icon' => 'download',
                    'color' => 'primary',
                ];
            });

        // Tutoriels complétés
        $completions = $user->progress()
            ->completed()
            ->with('tutorial:id,title')
            ->latest('completed_at')
            ->limit(10)
            ->get()
            ->map(function ($progress) {
                return [
                    'type' => 'completion',
                    'title' => 'Tutoriel terminé',
                    'description' => 'Completion du tutoriel "' . $progress->tutorial->title . '"',
                    'date' => $progress->completed_at,
                    'icon' => 'check-circle',
                    'color' => 'success',
                ];
            });

        // Badges obtenus
        $badges = $user->badges()
            ->wherePivot('created_at', '>=', now()->subDays(90))
            ->orderByPivot('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($badge) {
                return [
                    'type' => 'badge',
                    'title' => 'Nouveau badge',
                    'description' => 'Badge "' . $badge->name . '" obtenu',
                    'date' => $badge->pivot->created_at,
                    'icon' => 'award',
                    'color' => 'warning',
                ];
            });

        // Favoris ajoutés
        $favorites = $user->favorites()
            ->with('tutorial:id,title')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($favorite) {
                return [
                    'type' => 'favorite',
                    'title' => 'Favori ajouté',
                    'description' => 'Ajout aux favoris : "' . $favorite->tutorial->title . '"',
                    'date' => $favorite->created_at,
                    'icon' => 'heart',
                    'color' => 'danger',
                ];
            });

        // Fusionner et trier
        $timeline = $activities
            ->merge($downloads)
            ->merge($completions)
            ->merge($badges)
            ->merge($favorites)
            ->sortByDesc('date')
            ->take(20)
            ->values();

        return response()->json($timeline);
    }

    /**
     * Update user preferences.
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'newsletter_subscription' => 'boolean',
            'tutorial_recommendations' => 'boolean',
            'badge_notifications' => 'boolean',
            'language' => 'string|in:fr,en',
            'timezone' => 'string|max:50',
        ]);

        // Mettre à jour les préférences (vous pourriez avoir une table séparée pour cela)
        $preferences = $user->preferences ?? [];
        $preferences = array_merge($preferences, $validated);
        
        $user->update(['preferences' => $preferences]);

        // Tracking
        $this->analyticsService->track($user->id, 'preferences_updated', $validated);

        return response()->json([
            'success' => true,
            'message' => 'Préférences mises à jour avec succès.',
            'preferences' => $preferences,
        ]);
    }

    /**
     * Calculate user's login streak.
     */
    private function calculateLoginStreak(User $user): int
    {
        // Logique simplifiée - vous pourriez avoir une table de logs plus détaillée
        $lastActivity = $user->last_activity_at;
        
        if (!$lastActivity) {
            return 0;
        }

        $daysSinceLastActivity = $lastActivity->diffInDays(now());
        
        // Si l'utilisateur s'est connecté aujourd'hui ou hier, on considère qu'il a une streak
        if ($daysSinceLastActivity <= 1) {
            // Ici vous pourriez implémenter une logique plus complexe
            // en consultant une table de logs d'activité
            return max(1, 7 - $daysSinceLastActivity); // Exemple simplifié
        }

        return 0;
    }
}
