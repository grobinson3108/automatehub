<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\RestrictionService;
use App\Services\AnalyticsService;
use App\Models\Download;
use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class DownloadController extends Controller
{
    protected RestrictionService $restrictionService;
    protected AnalyticsService $analyticsService;

    public function __construct(
        RestrictionService $restrictionService,
        AnalyticsService $analyticsService
    ) {
        $this->restrictionService = $restrictionService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display user's download history.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Construire la requête des téléchargements
        $query = $user->downloads()->with(['tutorial' => function($query) {
            $query->select('id', 'title', 'description', 'thumbnail', 'difficulty_level', 'subscription_type');
        }]);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('tutorial', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('downloaded_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('downloaded_at', '<=', $request->get('date_to'));
        }

        if ($request->filled('tutorial_type')) {
            $type = $request->get('tutorial_type');
            $query->whereHas('tutorial', function($q) use ($type) {
                $q->where('subscription_type', $type);
            });
        }

        // Tri
        $sortBy = $request->get('sort', 'downloaded_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $downloads = $query->paginate(15);

        // Statistiques des téléchargements
        $downloadStats = [
            'total_downloads' => $user->downloads()->count(),
            'downloads_this_month' => $user->downloads()
                ->whereMonth('downloaded_at', Carbon::now()->month)
                ->count(),
            'downloads_this_week' => $user->downloads()
                ->where('downloaded_at', '>=', Carbon::now()->subWeek())
                ->count(),
            'unique_tutorials' => $user->downloads()
                ->distinct('tutorial_id')
                ->count(),
        ];

        // Restrictions et limites
        $restrictions = [
            'download_limit' => $this->restrictionService->getDownloadLimit($user),
            'remaining_downloads' => $this->restrictionService->getRemainingDownloads($user->id),
            'can_download' => $this->restrictionService->canDownload($user),
            'subscription_type' => $user->subscription_type,
        ];

        // Tutoriels les plus téléchargés
        $topDownloads = $user->downloads()
            ->select('tutorial_id', \DB::raw('COUNT(*) as download_count'))
            ->with(['tutorial' => function($query) {
                $query->select('id', 'title', 'thumbnail');
            }])
            ->groupBy('tutorial_id')
            ->orderBy('download_count', 'desc')
            ->limit(5)
            ->get();

        // Tracking
        $this->analyticsService->track($user->id, 'downloads_page_viewed', [
            'total_downloads' => $downloadStats['total_downloads'],
            'subscription_type' => $user->subscription_type,
        ]);

        return view('user.downloads.index', compact(
            'downloads',
            'downloadStats',
            'restrictions',
            'topDownloads'
        ));
    }

    /**
     * Download a specific file with all verifications.
     */
    public function download($id)
    {
        $user = auth()->user();
        $download = Download::with('tutorial')->findOrFail($id);

        // Vérifier que le téléchargement appartient à l'utilisateur
        if ($download->user_id !== $user->id) {
            return redirect()->back()
                ->with('error', 'Vous n\'avez pas accès à ce téléchargement.');
        }

        // Vérifier que l'utilisateur peut encore télécharger
        if (!$this->restrictionService->canDownload($user)) {
            return redirect()->back()
                ->with('error', 'Vous avez atteint votre limite de téléchargements pour ce mois.');
        }

        // Vérifier que le tutoriel est toujours accessible
        if (!$this->restrictionService->canAccessTutorial($user, $download->tutorial)) {
            return redirect()->back()
                ->with('error', 'Vous n\'avez plus accès à ce tutoriel.');
        }

        $filePath = storage_path('app/' . $download->file_path);

        // Vérifier que le fichier existe
        if (!file_exists($filePath)) {
            return redirect()->back()
                ->with('error', 'Le fichier n\'existe plus sur le serveur.');
        }

        // Enregistrer un nouveau téléchargement (re-téléchargement)
        Download::create([
            'user_id' => $user->id,
            'tutorial_id' => $download->tutorial_id,
            'file_name' => $download->file_name,
            'file_path' => $download->file_path,
            'file_size' => $download->file_size,
        ]);

        // Tracking
        $this->analyticsService->track($user->id, 'file_re_downloaded', [
            'tutorial_id' => $download->tutorial_id,
            'tutorial_title' => $download->tutorial->title,
            'file_name' => $download->file_name,
            'original_download_date' => $download->downloaded_at,
        ]);

        // Retourner le fichier
        return Response::download($filePath, $download->file_name);
    }

    /**
     * Get remaining downloads for the current user.
     */
    public function getRemainingDownloads(): JsonResponse
    {
        $user = auth()->user();

        $remaining = $this->restrictionService->getRemainingDownloads($user->id);
        $limit = $this->restrictionService->getDownloadLimit($user);
        $used = $limit - $remaining;

        $resetDate = Carbon::now()->endOfMonth();

        return response()->json([
            'remaining' => $remaining,
            'limit' => $limit,
            'used' => $used,
            'percentage_used' => $limit > 0 ? round(($used / $limit) * 100, 1) : 0,
            'reset_date' => $resetDate,
            'can_download' => $this->restrictionService->canDownload($user),
            'subscription_type' => $user->subscription_type,
        ]);
    }

    /**
     * Get download statistics.
     */
    public function getStats(): JsonResponse
    {
        $user = auth()->user();

        // Statistiques générales
        $totalDownloads = $user->downloads()->count();
        $uniqueTutorials = $user->downloads()->distinct('tutorial_id')->count();

        // Statistiques par période
        $thisMonth = $user->downloads()
            ->whereMonth('downloaded_at', Carbon::now()->month)
            ->count();

        $lastMonth = $user->downloads()
            ->whereMonth('downloaded_at', Carbon::now()->subMonth()->month)
            ->count();

        $thisWeek = $user->downloads()
            ->where('downloaded_at', '>=', Carbon::now()->subWeek())
            ->count();

        // Évolution des téléchargements (30 derniers jours)
        $downloadTrend = $user->downloads()
            ->selectRaw('DATE(downloaded_at) as date, COUNT(*) as count')
            ->where('downloaded_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Répartition par type de tutoriel
        $typeDistribution = $user->downloads()
            ->join('tutorials', 'downloads.tutorial_id', '=', 'tutorials.id')
            ->selectRaw('tutorials.subscription_type, COUNT(*) as count')
            ->groupBy('tutorials.subscription_type')
            ->get();

        // Répartition par niveau de difficulté
        $difficultyDistribution = $user->downloads()
            ->join('tutorials', 'downloads.tutorial_id', '=', 'tutorials.id')
            ->selectRaw('tutorials.difficulty_level, COUNT(*) as count')
            ->groupBy('tutorials.difficulty_level')
            ->get();

        // Taille totale téléchargée
        $totalSize = $user->downloads()->sum('file_size');

        return response()->json([
            'total_downloads' => $totalDownloads,
            'unique_tutorials' => $uniqueTutorials,
            'this_month' => $thisMonth,
            'last_month' => $lastMonth,
            'this_week' => $thisWeek,
            'growth_rate' => $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 0,
            'download_trend' => $downloadTrend,
            'type_distribution' => $typeDistribution,
            'difficulty_distribution' => $difficultyDistribution,
            'total_size_mb' => round($totalSize / (1024 * 1024), 2),
        ]);
    }

    /**
     * Get download history with pagination for AJAX.
     */
    public function getHistory(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = $user->downloads()
            ->with(['tutorial' => function($query) {
                $query->select('id', 'title', 'thumbnail', 'difficulty_level', 'subscription_type');
            }]);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('tutorial', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $type = $request->get('type');
            $query->whereHas('tutorial', function($q) use ($type) {
                $q->where('subscription_type', $type);
            });
        }

        $downloads = $query->latest()
            ->paginate($request->get('per_page', 10));

        return response()->json($downloads);
    }

    /**
     * Bulk download multiple files (if allowed).
     */
    public function bulkDownload(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'download_ids' => 'required|array|max:10',
            'download_ids.*' => 'exists:downloads,id',
        ]);

        $downloadIds = $request->get('download_ids');

        // Vérifier que tous les téléchargements appartiennent à l'utilisateur
        $downloads = Download::with('tutorial')
            ->whereIn('id', $downloadIds)
            ->where('user_id', $user->id)
            ->get();

        if ($downloads->count() !== count($downloadIds)) {
            return redirect()->back()
                ->with('error', 'Certains téléchargements ne vous appartiennent pas.');
        }

        // Vérifier les limites de téléchargement
        if (!$this->restrictionService->canDownload($user)) {
            return redirect()->back()
                ->with('error', 'Vous avez atteint votre limite de téléchargements.');
        }

        // Créer un fichier ZIP temporaire
        $zipFileName = 'downloads_' . time() . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Créer le dossier temp s'il n'existe pas
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            return redirect()->back()
                ->with('error', 'Impossible de créer l\'archive ZIP.');
        }

        $addedFiles = 0;
        foreach ($downloads as $download) {
            $filePath = storage_path('app/' . $download->file_path);
            
            if (file_exists($filePath)) {
                // Ajouter le fichier au ZIP avec un nom unique
                $fileName = $download->tutorial->title . '_' . $download->file_name;
                $zip->addFile($filePath, $fileName);
                $addedFiles++;

                // Enregistrer un nouveau téléchargement
                Download::create([
                    'user_id' => $user->id,
                    'tutorial_id' => $download->tutorial_id,
                    'file_name' => $download->file_name,
                    'file_path' => $download->file_path,
                    'file_size' => $download->file_size,
                ]);
            }
        }

        $zip->close();

        if ($addedFiles === 0) {
            unlink($zipPath);
            return redirect()->back()
                ->with('error', 'Aucun fichier disponible pour le téléchargement.');
        }

        // Tracking
        $this->analyticsService->track($user->id, 'bulk_download', [
            'files_count' => $addedFiles,
            'download_ids' => $downloadIds,
        ]);

        // Programmer la suppression du fichier ZIP après téléchargement
        register_shutdown_function(function() use ($zipPath) {
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
        });

        return Response::download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Delete a download from history.
     */
    public function destroy($id): JsonResponse
    {
        $user = auth()->user();
        $download = Download::findOrFail($id);

        // Vérifier que le téléchargement appartient à l'utilisateur
        if ($download->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez pas accès à ce téléchargement.'
            ], 403);
        }

        $download->delete();

        // Tracking
        $this->analyticsService->track($user->id, 'download_history_deleted', [
            'tutorial_id' => $download->tutorial_id,
            'file_name' => $download->file_name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Téléchargement supprimé de l\'historique.',
        ]);
    }

    /**
     * Clear all download history.
     */
    public function clearHistory(): JsonResponse
    {
        $user = auth()->user();
        $deletedCount = $user->downloads()->delete();

        // Tracking
        $this->analyticsService->track($user->id, 'download_history_cleared', [
            'deleted_count' => $deletedCount,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Historique vidé ({$deletedCount} téléchargements supprimés).",
        ]);
    }

    /**
     * Get download limits information.
     */
    public function getLimits(): JsonResponse
    {
        $user = auth()->user();

        $limits = [
            'subscription_type' => $user->subscription_type,
            'monthly_limit' => $this->restrictionService->getDownloadLimit($user),
            'remaining_this_month' => $this->restrictionService->getRemainingDownloads($user->id),
            'used_this_month' => $user->downloads()
                ->whereMonth('downloaded_at', Carbon::now()->month)
                ->count(),
            'reset_date' => Carbon::now()->endOfMonth(),
            'can_download' => $this->restrictionService->canDownload($user),
            'upgrade_benefits' => $this->getUpgradeBenefits($user),
        ];

        return response()->json($limits);
    }

    /**
     * Get upgrade benefits for current user.
     */
    private function getUpgradeBenefits($user): array
    {
        $benefits = [];

        if ($user->subscription_type === 'free') {
            $benefits['premium'] = [
                'download_limit' => 'Illimité',
                'access' => 'Contenu premium',
                'support' => 'Support prioritaire',
            ];
            $benefits['pro'] = [
                'download_limit' => 'Illimité',
                'access' => 'Tout le contenu + tutoriels sur demande',
                'support' => 'Support dédié',
                'features' => 'Fonctionnalités entreprise',
            ];
        } elseif ($user->subscription_type === 'premium') {
            $benefits['pro'] = [
                'access' => 'Tutoriels sur demande',
                'support' => 'Support dédié',
                'features' => 'Fonctionnalités entreprise',
                'priority' => 'Accès prioritaire aux nouveautés',
            ];
        }

        return $benefits;
    }
}
