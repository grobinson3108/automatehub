<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Download;
use App\Models\Tutorial;
use App\Models\Analytics;
use App\Services\AnalyticsService;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class UserManagementController extends Controller
{
    protected AnalyticsService $analyticsService;
    protected BadgeService $badgeService;

    public function __construct(AnalyticsService $analyticsService, BadgeService $badgeService)
    {
        $this->analyticsService = $analyticsService;
        $this->badgeService = $badgeService;
    }

    /**
     * Display a paginated listing of users with filters.
     */
    public function index(Request $request)
    {
        try {
            $query = User::with(['badges', 'downloads', 'tutorialProgress']);

            // Filtres
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                });
            }

            if ($request->filled('subscription_type')) {
                $query->where('subscription_type', $request->get('subscription_type'));
            }

            if ($request->filled('level_n8n')) {
                $query->where('level_n8n', $request->get('level_n8n'));
            }

            if ($request->filled('is_professional')) {
                $query->where('is_professional', $request->get('is_professional'));
            }

            if ($request->filled('date_from')) {
                $query->where('created_at', '>=', $request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->where('created_at', '<=', $request->get('date_to'));
            }

            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $users = $query->paginate(20);

            // Statistiques pour les filtres
            $stats = [
                'total' => User::count(),
                'free' => User::where('subscription_type', 'free')->count(),
                'premium' => User::where('subscription_type', 'premium')->count(),
                'pro' => User::where('subscription_type', 'pro')->count(),
                'professional' => User::where('is_professional', true)->count(),
            ];

            return view('admin.users.index', compact('users', 'stats'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans UserManagementController::index', ['error' => $e->getMessage()]);
            
            // Valeurs par défaut en cas d'erreur
            $users = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), 0, 20, 1, ['path' => request()->url()]
            );
            $stats = ['total' => 0, 'free' => 0, 'premium' => 0, 'pro' => 0, 'professional' => 0];
            
            return view('admin.users.index', compact('users', 'stats'));
        }
    }

    /**
     * Display contact messages and inquiries.
     */
    public function contacts(Request $request)
    {
        try {
            // Pour l'instant, on affiche une page simple
            // Plus tard, on pourra ajouter une table contacts pour les messages de contact
            $contacts = collect(); // Placeholder pour les futurs messages de contact
            
            return view('admin.contacts.index', compact('contacts'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans UserManagementController::contacts', ['error' => $e->getMessage()]);
            
            $contacts = collect();
            return view('admin.contacts.index', compact('contacts'));
        }
    }

    /**
     * Display detailed information about a specific user.
     */
    public function show($id)
    {
        try {
            $user = User::with([
                'badges',
                'downloads.tutorial',
                'tutorialProgress.tutorial',
                'favorites.tutorial',
                'analytics'
            ])->findOrFail($id);

            // Statistiques utilisateur
            $userStats = [
                'total_downloads' => $user->downloads()->count(),
                'downloads_this_month' => $user->downloads()
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->count(),
                'tutorials_completed' => $user->tutorialProgress()
                    ->where('completed', true)
                    ->count(),
                'tutorials_in_progress' => $user->tutorialProgress()
                    ->where('completed', false)
                    ->count(),
                'favorites_count' => $user->favorites()->count(),
                'badges_count' => $user->badges()->count(),
                'last_activity' => $user->last_activity_at,
                'registration_date' => $user->created_at,
                'days_since_registration' => $user->created_at->diffInDays(now()),
            ];

            // Activité récente
            $recentActivity = [
                'downloads' => $user->downloads()->with('tutorial')->latest()->limit(10)->get(),
                'progress' => $user->tutorialProgress()->with('tutorial')->latest()->limit(10)->get(),
                'analytics' => $user->analytics()->latest()->limit(10)->get(),
            ];

            // Tutoriels recommandés - Correction : utiliser required_level au lieu de difficulty_level
            $recommendedTutorials = Tutorial::where('target_audience', $user->is_professional ? 'pro' : 'individual')
                ->where('required_level', $user->level_n8n ?? 'beginner')
                ->whereNotIn('id', $user->downloads()->pluck('tutorial_id'))
                ->limit(5)
                ->get();

            return view('admin.users.show', compact('user', 'userStats', 'recentActivity', 'recommendedTutorials'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans UserManagementController::show', ['error' => $e->getMessage()]);
            return redirect()->route('admin.users.index')->with('error', 'Utilisateur introuvable');
        }
    }

    /**
     * Show the form for editing a user.
     */
    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
            
            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans UserManagementController::edit', ['error' => $e->getMessage()]);
            return redirect()->route('admin.users.index')->with('error', 'Utilisateur introuvable');
        }
    }

    /**
     * Update user information.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            $rules = [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
                'subscription_type' => 'required|in:free,premium,pro',
                'level_n8n' => 'required|in:beginner,intermediate,expert',
                'is_professional' => 'boolean',
                'company_name' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:10',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'vat_number' => 'nullable|string|max:50',
                'password' => 'nullable|string|min:8|confirmed',
            ];

            // Si professionnel, company_name devient obligatoire
            if ($request->get('is_professional')) {
                $rules['company_name'] = 'required|string|max:255';
            }

            $validated = $request->validate($rules);

            // Supprimer le mot de passe s'il est vide
            if (empty($validated['password'])) {
                unset($validated['password']);
            } else {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            // Log de l'action admin
            $this->analyticsService->track($user->id, 'admin_user_updated', [
                'admin_id' => auth()->id(),
                'updated_fields' => array_keys($validated),
            ]);

            return redirect()->route('admin.users.show', $user->id)
                ->with('success', 'Utilisateur mis à jour avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur dans UserManagementController::update', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour de l\'utilisateur');
        }
    }

    /**
     * Toggle user subscription (upgrade/downgrade).
     */
    public function toggleSubscription(Request $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $newSubscription = $request->get('subscription_type');

            $validSubscriptions = ['free', 'premium', 'pro'];
            if (!in_array($newSubscription, $validSubscriptions)) {
                return response()->json(['error' => 'Type d\'abonnement invalide'], 400);
            }

            $oldSubscription = $user->subscription_type;
            $user->update(['subscription_type' => $newSubscription]);

            // Log de l'action
            $this->analyticsService->track($user->id, 'subscription_changed', [
                'old_subscription' => $oldSubscription,
                'new_subscription' => $newSubscription,
                'changed_by_admin' => true,
                'admin_id' => auth()->id(),
            ]);

            // Attribution de badges si upgrade
            if ($newSubscription === 'premium' && $oldSubscription === 'free') {
                $this->badgeService->awardBadge($user->id, 'premium_subscriber');
            } elseif ($newSubscription === 'pro' && in_array($oldSubscription, ['free', 'premium'])) {
                $this->badgeService->awardBadge($user->id, 'pro_subscriber');
            }

            return response()->json([
                'success' => true,
                'message' => "Abonnement changé de {$oldSubscription} vers {$newSubscription}",
                'new_subscription' => $newSubscription,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur dans UserManagementController::toggleSubscription', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors du changement d\'abonnement'], 500);
        }
    }

    /**
     * Soft delete a user.
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $user = User::findOrFail($id);

            // Vérifier que ce n'est pas un admin
            if ($user->is_admin) {
                return redirect()->back()->with('error', 'Impossible de supprimer un administrateur.');
            }

            // Log avant suppression
            $this->analyticsService->track($user->id, 'user_deleted_by_admin', [
                'admin_id' => auth()->id(),
                'user_data' => $user->toArray(),
            ]);

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'Utilisateur supprimé avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur dans UserManagementController::destroy', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de la suppression de l\'utilisateur');
        }
    }

    /**
     * Export users to CSV.
     */
    public function export(Request $request)
    {
        try {
            $query = User::query();

            // Appliquer les mêmes filtres que l'index
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                });
            }

            if ($request->filled('subscription_type')) {
                $query->where('subscription_type', $request->get('subscription_type'));
            }

            if ($request->filled('level_n8n')) {
                $query->where('level_n8n', $request->get('level_n8n'));
            }

            if ($request->filled('is_professional')) {
                $query->where('is_professional', $request->get('is_professional'));
            }

            $users = $query->get();

            $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($users) {
                $file = fopen('php://output', 'w');
                
                // En-têtes CSV
                fputcsv($file, [
                    'ID',
                    'Prénom',
                    'Nom',
                    'Email',
                    'Type d\'abonnement',
                    'Niveau n8n',
                    'Professionnel',
                    'Entreprise',
                    'Adresse',
                    'Code postal',
                    'Ville',
                    'Pays',
                    'N° TVA',
                    'Date d\'inscription',
                    'Dernière activité',
                    'Téléchargements',
                    'Badges',
                ]);

                // Données
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->first_name,
                        $user->last_name,
                        $user->email,
                        $user->subscription_type,
                        $user->level_n8n,
                        $user->is_professional ? 'Oui' : 'Non',
                        $user->company_name,
                        $user->address,
                        $user->postal_code,
                        $user->city,
                        $user->country,
                        $user->vat_number,
                        $user->created_at->format('Y-m-d H:i:s'),
                        $user->last_activity_at ? $user->last_activity_at->format('Y-m-d H:i:s') : '',
                        $user->downloads()->count(),
                        $user->badges()->count(),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Erreur dans UserManagementController::export', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Erreur lors de l\'export');
        }
    }

    /**
     * Display subscription overview page.
     */
    public function subscriptions()
    {
        try {
            $subscriptionStats = [
                'free' => User::where('subscription_type', 'free')->count(),
                'premium' => User::where('subscription_type', 'premium')->count(),
                'pro' => User::where('subscription_type', 'pro')->count(),
                'total' => User::count()
            ];
            
            // Récupérer les derniers changements d'abonnement
            $recentSubscriptionChanges = Analytics::where('event_type', 'subscription_changed')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            // Récupérer les statistiques de conversion
            $conversionRates = [
                'free_to_premium' => $this->calculateConversionRate('free', 'premium'),
                'premium_to_pro' => $this->calculateConversionRate('premium', 'pro'),
                'free_to_pro' => $this->calculateConversionRate('free', 'pro')
            ];
            
            // Récupérer les utilisateurs par type d'abonnement
            $freeUsers = User::where('subscription_type', 'free')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'free_page');
                
            $premiumUsers = User::where('subscription_type', 'premium')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'premium_page');
                
            $proUsers = User::where('subscription_type', 'pro')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'pro_page');
            
            return view('admin.users.subscriptions', compact(
                'subscriptionStats', 
                'recentSubscriptionChanges', 
                'conversionRates',
                'freeUsers',
                'premiumUsers',
                'proUsers'
            ));
        } catch (\Exception $e) {
            \Log::error('Erreur dans UserManagementController::subscriptions', ['error' => $e->getMessage()]);
            return redirect()->route('admin.dashboard')->with('error', 'Erreur lors du chargement des abonnements');
        }
    }
    
    /**
     * Calculate conversion rate between two subscription types.
     */
    private function calculateConversionRate($fromType, $toType)
    {
        $totalFromType = User::where('subscription_type', $fromType)->count();
        
        if ($totalFromType === 0) {
            return 0;
        }
        
        $conversions = Analytics::where('event_type', 'subscription_changed')
            ->where('data->old_subscription', $fromType)
            ->where('data->new_subscription', $toType)
            ->count();
        
        return ($conversions / $totalFromType) * 100;
    }
    
    /**
     * Display user activity logs.
     */
    public function activity()
    {
        try {
            $recentActivity = Analytics::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
                
            $activityStats = [
                'total' => Analytics::count(),
                'today' => Analytics::whereDate('created_at', Carbon::today())->count(),
                'week' => Analytics::where('created_at', '>=', Carbon::now()->subWeek())->count(),
                'month' => Analytics::where('created_at', '>=', Carbon::now()->subMonth())->count()
            ];
            
            $eventTypes = Analytics::select('event_type')
                ->distinct()
                ->pluck('event_type');
                
            return view('admin.users.activity', compact('recentActivity', 'activityStats', 'eventTypes'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans UserManagementController::activity', ['error' => $e->getMessage()]);
            return redirect()->route('admin.dashboard')->with('error', 'Erreur lors du chargement des activités');
        }
    }
    
    /**
     * Display n8n levels overview.
     */
    public function n8nLevels()
    {
        try {
            $levelStats = [
                'beginner' => User::where('level_n8n', 'beginner')->count(),
                'intermediate' => User::where('level_n8n', 'intermediate')->count(),
                'expert' => User::where('level_n8n', 'expert')->count(),
                'null' => User::whereNull('level_n8n')->count(),
                'total' => User::count()
            ];
            
            $beginnerUsers = User::where('level_n8n', 'beginner')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'beginner_page');
                
            $intermediateUsers = User::where('level_n8n', 'intermediate')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'intermediate_page');
                
            $expertUsers = User::where('level_n8n', 'expert')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'expert_page');
                
            $noLevelUsers = User::whereNull('level_n8n')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'no_level_page');
            
            return view('admin.users.levels', compact(
                'levelStats', 
                'beginnerUsers', 
                'intermediateUsers', 
                'expertUsers', 
                'noLevelUsers'
            ));
        } catch (\Exception $e) {
            \Log::error('Erreur dans UserManagementController::n8nLevels', ['error' => $e->getMessage()]);
            return redirect()->route('admin.dashboard')->with('error', 'Erreur lors du chargement des niveaux n8n');
        }
    }
    
    /**
     * Get user statistics for charts.
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '30');
            $startDate = Carbon::now()->subDays($period);

            // Évolution des inscriptions
            $registrations = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Répartition par abonnement
            $subscriptionStats = User::selectRaw('subscription_type, COUNT(*) as count')
                ->groupBy('subscription_type')
                ->get();

            // Répartition par niveau
            $levelStats = User::selectRaw('level_n8n, COUNT(*) as count')
                ->whereNotNull('level_n8n')
                ->groupBy('level_n8n')
                ->get();

            // Utilisateurs actifs
            $activeUsers = User::where('last_activity_at', '>=', Carbon::now()->subDays(30))->count();
            $totalUsers = User::count();
            $activityRate = $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0;

            return response()->json([
                'registrations' => $registrations,
                'subscription_stats' => $subscriptionStats,
                'level_stats' => $levelStats,
                'activity_rate' => round($activityRate, 2),
                'active_users' => $activeUsers,
                'total_users' => $totalUsers,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur dans UserManagementController::getStats', ['error' => $e->getMessage()]);
            return response()->json([
                'registrations' => [],
                'subscription_stats' => [],
                'level_stats' => [],
                'activity_rate' => 0,
                'active_users' => 0,
                'total_users' => 0,
            ]);
        }
    }

    /**
     * Bulk actions on users.
     */
    public function bulkAction(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'action' => 'required|in:delete,upgrade,downgrade,activate,deactivate',
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
                'subscription_type' => 'nullable|in:free,premium,pro',
            ]);

            $userIds = $request->get('user_ids');
            $action = $request->get('action');
            $affected = 0;

            switch ($action) {
                case 'delete':
                    $affected = User::whereIn('id', $userIds)
                        ->where('is_admin', false)
                        ->delete();
                    break;

                case 'upgrade':
                case 'downgrade':
                    $subscriptionType = $request->get('subscription_type');
                    if ($subscriptionType) {
                        $affected = User::whereIn('id', $userIds)->update([
                            'subscription_type' => $subscriptionType
                        ]);
                    }
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => "{$affected} utilisateur(s) traité(s)",
                'affected_count' => $affected,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur dans UserManagementController::bulkAction', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors de l\'action groupée'], 500);
        }
    }
}
