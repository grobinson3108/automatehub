<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Models\Tutorial;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class DownloadController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display free resources for visitors.
     */
    public function index(Request $request)
    {
        // Ressources gratuites disponibles pour tous
        $query = Tutorial::with(['category'])
            ->where('status', 'published')
            ->where('subscription_type', 'free')
            ->whereNotNull('files');

        // Filtres
        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        if ($request->filled('level')) {
            $query->where('difficulty_level', $request->get('level'));
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
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        $resources = $query->paginate(12);

        // Transformer les ressources pour l'affichage public
        $resources->getCollection()->transform(function ($tutorial) {
            $files = $tutorial->files ? json_decode($tutorial->files, true) : [];
            
            return [
                'id' => $tutorial->id,
                'title' => $tutorial->title,
                'description' => $tutorial->description,
                'thumbnail' => $tutorial->thumbnail,
                'category' => [
                    'id' => $tutorial->category->id,
                    'name' => $tutorial->category->name,
                    'slug' => $tutorial->category->slug,
                    'color' => $tutorial->category->color,
                ],
                'difficulty_level' => $tutorial->difficulty_level,
                'estimated_duration' => $tutorial->estimated_duration,
                'downloads_count' => $tutorial->downloads()->count(),
                'slug' => $tutorial->slug,
                'files' => array_map(function($file, $index) use ($tutorial) {
                    return [
                        'id' => $index,
                        'name' => $file['original_name'],
                        'size' => $this->formatFileSize($file['size']),
                        'type' => $this->getFileType($file['original_name']),
                        'download_url' => route('frontend.downloads.free', [
                            'tutorial' => $tutorial->id,
                            'file' => $index
                        ]),
                    ];
                }, $files, array_keys($files)),
                'created_at' => $tutorial->created_at,
            ];
        });

        // Catégories avec ressources gratuites
        $categories = Category::whereHas('tutorials', function($query) {
                $query->where('status', 'published')
                      ->where('subscription_type', 'free')
                      ->whereNotNull('files');
            })
            ->withCount(['tutorials' => function($query) {
                $query->where('status', 'published')
                      ->where('subscription_type', 'free')
                      ->whereNotNull('files');
            }])
            ->orderBy('name')
            ->get();

        // Statistiques des ressources
        $stats = [
            'total_resources' => Tutorial::where('status', 'published')
                ->where('subscription_type', 'free')
                ->whereNotNull('files')
                ->count(),
            'total_downloads' => \DB::table('downloads')
                ->join('tutorials', 'downloads.tutorial_id', '=', 'tutorials.id')
                ->where('tutorials.subscription_type', 'free')
                ->count(),
            'categories_count' => $categories->count(),
        ];

        // Ressources populaires
        $popularResources = Tutorial::where('status', 'published')
            ->where('subscription_type', 'free')
            ->whereNotNull('files')
            ->withCount('downloads')
            ->orderBy('downloads_count', 'desc')
            ->limit(6)
            ->get()
            ->map(function($tutorial) {
                return [
                    'id' => $tutorial->id,
                    'title' => $tutorial->title,
                    'thumbnail' => $tutorial->thumbnail,
                    'downloads_count' => $tutorial->downloads_count,
                    'slug' => $tutorial->slug,
                ];
            });

        // Call-to-action pour inscription
        $callToAction = [
            'title' => 'Accédez à encore plus de ressources',
            'subtitle' => 'Inscrivez-vous gratuitement pour débloquer tous nos tutoriels et ressources',
            'benefits' => [
                'Accès à tous les tutoriels gratuits',
                'Suivi de progression personnalisé',
                'Système de badges et récompenses',
                'Communauté d\'entraide',
            ],
            'button' => [
                'text' => 'S\'inscrire gratuitement',
                'url' => route('register'),
            ],
        ];

        // Meta données pour SEO
        $metaData = [
            'title' => 'Ressources gratuites n8n - Téléchargements | Automatehub',
            'description' => 'Téléchargez gratuitement nos ressources n8n : workflows, templates, guides et outils pour automatiser vos tâches.',
            'keywords' => 'ressources gratuites, n8n, workflows, templates, téléchargements, automation',
            'url' => route('frontend.downloads.index'),
        ];

        // Tracking
        $this->analyticsService->trackAnonymous('free_downloads_page_viewed', [
            'filters' => $request->only(['category', 'level', 'search']),
            'total_resources' => $resources->total(),
        ]);

        return view('frontend.downloads.index', compact(
            'resources',
            'categories',
            'stats',
            'popularResources',
            'callToAction',
            'metaData'
        ));
    }

    /**
     * Download free resource file.
     */
    public function downloadFree($tutorialId, $fileId)
    {
        $tutorial = Tutorial::where('id', $tutorialId)
            ->where('status', 'published')
            ->where('subscription_type', 'free')
            ->whereNotNull('files')
            ->firstOrFail();

        // Récupérer les fichiers
        $files = json_decode($tutorial->files, true);
        
        if (!isset($files[$fileId])) {
            abort(404, 'Fichier non trouvé.');
        }

        $file = $files[$fileId];
        $filePath = storage_path('app/' . $file['path']);

        if (!file_exists($filePath)) {
            abort(404, 'Le fichier n\'existe plus sur le serveur.');
        }

        // Enregistrer le téléchargement anonyme
        $this->trackAnonymousDownload($tutorial, $file);

        // Tracking analytics
        $this->analyticsService->trackAnonymous('free_file_downloaded', [
            'tutorial_id' => $tutorial->id,
            'tutorial_title' => $tutorial->title,
            'file_name' => $file['original_name'],
            'file_size' => $file['size'],
            'category' => $tutorial->category->name ?? 'Unknown',
        ]);

        // Retourner le fichier
        return Response::download($filePath, $file['original_name']);
    }

    /**
     * Get free resources by category.
     */
    public function getByCategory($categorySlug): JsonResponse
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();

        $resources = Tutorial::where('status', 'published')
            ->where('subscription_type', 'free')
            ->where('category_id', $category->id)
            ->whereNotNull('files')
            ->with('category')
            ->latest()
            ->get()
            ->map(function($tutorial) {
                $files = $tutorial->files ? json_decode($tutorial->files, true) : [];
                
                return [
                    'id' => $tutorial->id,
                    'title' => $tutorial->title,
                    'description' => $tutorial->description,
                    'thumbnail' => $tutorial->thumbnail,
                    'difficulty_level' => $tutorial->difficulty_level,
                    'downloads_count' => $tutorial->downloads()->count(),
                    'files_count' => count($files),
                    'slug' => $tutorial->slug,
                ];
            });

        return response()->json([
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'resources' => $resources,
            'total' => $resources->count(),
        ]);
    }

    /**
     * Search free resources.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
            'category' => 'nullable|exists:categories,id',
            'level' => 'nullable|in:beginner,intermediate,advanced,expert',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        $query = Tutorial::where('status', 'published')
            ->where('subscription_type', 'free')
            ->whereNotNull('files')
            ->with('category');

        // Recherche textuelle
        $search = $request->get('q');
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });

        // Filtres
        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        if ($request->filled('level')) {
            $query->where('difficulty_level', $request->get('level'));
        }

        $limit = $request->get('limit', 10);
        $resources = $query->limit($limit)->get();

        $results = $resources->map(function($tutorial) {
            $files = $tutorial->files ? json_decode($tutorial->files, true) : [];
            
            return [
                'id' => $tutorial->id,
                'title' => $tutorial->title,
                'description' => substr(strip_tags($tutorial->description), 0, 100) . '...',
                'thumbnail' => $tutorial->thumbnail,
                'category' => $tutorial->category->name,
                'difficulty_level' => $tutorial->difficulty_level,
                'files_count' => count($files),
                'slug' => $tutorial->slug,
                'url' => route('frontend.tutorials.show', $tutorial->slug),
            ];
        });

        // Tracking
        $this->analyticsService->trackAnonymous('free_resources_search', [
            'query' => $search,
            'filters' => $request->only(['category', 'level']),
            'results_count' => $results->count(),
        ]);

        return response()->json([
            'results' => $results,
            'total' => $results->count(),
            'query' => $search,
        ]);
    }

    /**
     * Get download statistics for public display.
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_free_resources' => Tutorial::where('status', 'published')
                ->where('subscription_type', 'free')
                ->whereNotNull('files')
                ->count(),
            'total_downloads' => \DB::table('downloads')
                ->join('tutorials', 'downloads.tutorial_id', '=', 'tutorials.id')
                ->where('tutorials.subscription_type', 'free')
                ->count(),
            'downloads_this_month' => \DB::table('downloads')
                ->join('tutorials', 'downloads.tutorial_id', '=', 'tutorials.id')
                ->where('tutorials.subscription_type', 'free')
                ->whereMonth('downloads.downloaded_at', now()->month)
                ->count(),
            'most_downloaded' => Tutorial::where('status', 'published')
                ->where('subscription_type', 'free')
                ->whereNotNull('files')
                ->withCount('downloads')
                ->orderBy('downloads_count', 'desc')
                ->first(['id', 'title', 'downloads_count']),
        ];

        return response()->json($stats);
    }

    /**
     * Get popular free resources.
     */
    public function getPopular(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 6);

        $popular = Tutorial::where('status', 'published')
            ->where('subscription_type', 'free')
            ->whereNotNull('files')
            ->withCount('downloads')
            ->orderBy('downloads_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($tutorial) {
                $files = $tutorial->files ? json_decode($tutorial->files, true) : [];
                
                return [
                    'id' => $tutorial->id,
                    'title' => $tutorial->title,
                    'thumbnail' => $tutorial->thumbnail,
                    'downloads_count' => $tutorial->downloads_count,
                    'files_count' => count($files),
                    'slug' => $tutorial->slug,
                    'url' => route('frontend.tutorials.show', $tutorial->slug),
                ];
            });

        return response()->json($popular);
    }

    /**
     * Get recent free resources.
     */
    public function getRecent(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 6);

        $recent = Tutorial::where('status', 'published')
            ->where('subscription_type', 'free')
            ->whereNotNull('files')
            ->with('category')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function($tutorial) {
                $files = $tutorial->files ? json_decode($tutorial->files, true) : [];
                
                return [
                    'id' => $tutorial->id,
                    'title' => $tutorial->title,
                    'thumbnail' => $tutorial->thumbnail,
                    'category' => $tutorial->category->name,
                    'files_count' => count($files),
                    'created_at' => $tutorial->created_at,
                    'slug' => $tutorial->slug,
                    'url' => route('frontend.tutorials.show', $tutorial->slug),
                ];
            });

        return response()->json($recent);
    }

    /**
     * Track anonymous download for analytics.
     */
    private function trackAnonymousDownload($tutorial, $file): void
    {
        // Enregistrer dans une table de tracking anonyme ou logs
        \Log::info('Anonymous download tracked', [
            'tutorial_id' => $tutorial->id,
            'tutorial_title' => $tutorial->title,
            'file_name' => $file['original_name'],
            'file_size' => $file['size'],
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
        ]);

        // Vous pourriez aussi incrémenter un compteur dans la base de données
        // ou utiliser Redis pour des statistiques en temps réel
    }

    /**
     * Format file size for display.
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get file type from filename.
     */
    private function getFileType(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return match($extension) {
            'pdf' => 'PDF',
            'json' => 'Workflow n8n',
            'zip' => 'Archive',
            'txt' => 'Texte',
            'md' => 'Markdown',
            'xlsx', 'xls' => 'Excel',
            'docx', 'doc' => 'Word',
            'png', 'jpg', 'jpeg', 'gif' => 'Image',
            default => 'Fichier',
        };
    }

    /**
     * Get file icon class for display.
     */
    private function getFileIcon(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return match($extension) {
            'pdf' => 'fa-file-pdf text-danger',
            'json' => 'fa-code text-primary',
            'zip' => 'fa-file-archive text-warning',
            'txt', 'md' => 'fa-file-text text-info',
            'xlsx', 'xls' => 'fa-file-excel text-success',
            'docx', 'doc' => 'fa-file-word text-primary',
            'png', 'jpg', 'jpeg', 'gif' => 'fa-file-image text-info',
            default => 'fa-file text-muted',
        };
    }
}
