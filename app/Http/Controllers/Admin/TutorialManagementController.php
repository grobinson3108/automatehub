<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Download;
use App\Models\BlogPost;
use App\Services\TutorialService;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TutorialManagementController extends Controller
{
    protected TutorialService $tutorialService;
    protected AnalyticsService $analyticsService;

    public function __construct(TutorialService $tutorialService, AnalyticsService $analyticsService)
    {
        $this->tutorialService = $tutorialService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display a paginated listing of tutorials with filters.
     */
    public function index(Request $request)
    {
        $query = Tutorial::with(['category', 'tags', 'downloads']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'published') {
                $query->published();
            } elseif ($status === 'draft') {
                $query->draft();
            }
        }

        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->get('difficulty_level'));
        }

        if ($request->filled('target_audience')) {
            $query->where('target_audience', $request->get('target_audience'));
        }

        if ($request->filled('subscription_type')) {
            $query->where('subscription_type', $request->get('subscription_type'));
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

        $tutorials = $query->paginate(20);

        // Données pour les filtres
        $categories = Category::all();
        $tags = Tag::all();

        // Statistiques
        $stats = [
            'total' => Tutorial::count(),
            'published' => Tutorial::published()->count(),
            'draft' => Tutorial::draft()->count(),
            'free' => Tutorial::where('subscription_required', 'free')->count(),
            'premium' => Tutorial::where('subscription_required', 'premium')->count(),
            'pro' => Tutorial::where('subscription_required', 'pro')->count(),
        ];

        return view('admin.tutorials.index', compact('tutorials', 'categories', 'tags', 'stats'));
    }

    /**
     * Show the form for creating a new tutorial.
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.tutorials.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created tutorial.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced,expert',
            'target_audience' => 'required|in:individual,pro,both',
            'subscription_type' => 'required|in:free,premium,pro',
            'estimated_duration' => 'nullable|integer|min:1',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'files.*' => 'nullable|file|mimes:pdf,json,zip|max:10240', // 10MB max
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Créer le tutoriel
            $tutorial = Tutorial::create([
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']),
                'description' => $validated['description'],
                'content' => $validated['content'],
                'category_id' => $validated['category_id'],
                'difficulty_level' => $validated['difficulty_level'],
                'target_audience' => $validated['target_audience'],
                'subscription_type' => $validated['subscription_type'],
                'estimated_duration' => $validated['estimated_duration'] ?? null,
                'status' => 'draft',
                'author_id' => auth()->id(),
            ]);

            // Associer les tags
            if (!empty($validated['tags'])) {
                $tutorial->tags()->attach($validated['tags']);
            }

            // Gérer l'upload de la thumbnail
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('tutorials/thumbnails', 'public');
                $tutorial->update(['thumbnail' => $thumbnailPath]);
            }

            // Gérer l'upload des fichiers
            if ($request->hasFile('files')) {
                $this->handleFileUploads($request->file('files'), $tutorial);
            }

            DB::commit();

            // Log de l'action
            $this->analyticsService->track(auth()->id(), 'tutorial_created', [
                'tutorial_id' => $tutorial->id,
                'title' => $tutorial->title,
            ]);

            return redirect()->route('admin.tutorials.show', $tutorial->id)
                ->with('success', 'Tutoriel créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du tutoriel : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified tutorial with statistics.
     */
    public function show($id)
    {
        $tutorial = Tutorial::with([
            'category',
            'tags',
            'downloads.user',
            'favorites.user',
            'tutorialProgress.user'
        ])->findOrFail($id);

        // Statistiques du tutoriel
        $stats = [
            'total_downloads' => $tutorial->downloads()->count(),
            'downloads_this_month' => $tutorial->downloads()
                ->whereMonth('created_at', Carbon::now()->month)
                ->count(),
            'favorites_count' => $tutorial->favorites()->count(),
            'completion_rate' => $this->calculateCompletionRate($tutorial),
            'average_rating' => $tutorial->reviews()->avg('rating') ?? 0,
            'views_count' => $tutorial->views_count ?? 0,
        ];

        // Activité récente
        $recentActivity = [
            'downloads' => $tutorial->downloads()->with('user')->latest()->limit(10)->get(),
            'favorites' => $tutorial->favorites()->with('user')->latest()->limit(10)->get(),
            'progress' => $tutorial->tutorialProgress()->with('user')->latest()->limit(10)->get(),
        ];

        return view('admin.tutorials.show', compact('tutorial', 'stats', 'recentActivity'));
    }

    /**
     * Show the form for editing the specified tutorial.
     */
    public function edit($id)
    {
        $tutorial = Tutorial::with(['tags'])->findOrFail($id);
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.tutorials.edit', compact('tutorial', 'categories', 'tags'));
    }

    /**
     * Update the specified tutorial.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $tutorial = Tutorial::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced,expert',
            'target_audience' => 'required|in:individual,pro,both',
            'subscription_type' => 'required|in:free,premium,pro',
            'estimated_duration' => 'nullable|integer|min:1',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'files.*' => 'nullable|file|mimes:pdf,json,zip|max:10240',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_files' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Mettre à jour le tutoriel
            $tutorial->update([
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']),
                'description' => $validated['description'],
                'content' => $validated['content'],
                'category_id' => $validated['category_id'],
                'difficulty_level' => $validated['difficulty_level'],
                'target_audience' => $validated['target_audience'],
                'subscription_type' => $validated['subscription_type'],
                'estimated_duration' => $validated['estimated_duration'] ?? null,
                'updated_at' => now(),
            ]);

            // Mettre à jour les tags
            if (isset($validated['tags'])) {
                $tutorial->tags()->sync($validated['tags']);
            }

            // Gérer la nouvelle thumbnail
            if ($request->hasFile('thumbnail')) {
                // Supprimer l'ancienne thumbnail
                if ($tutorial->thumbnail) {
                    Storage::disk('public')->delete($tutorial->thumbnail);
                }
                $thumbnailPath = $request->file('thumbnail')->store('tutorials/thumbnails', 'public');
                $tutorial->update(['thumbnail' => $thumbnailPath]);
            }

            // Supprimer les fichiers sélectionnés
            if (!empty($validated['remove_files'])) {
                $this->removeFiles($validated['remove_files'], $tutorial);
            }

            // Ajouter de nouveaux fichiers
            if ($request->hasFile('files')) {
                $this->handleFileUploads($request->file('files'), $tutorial);
            }

            DB::commit();

            // Log de l'action
            $this->analyticsService->track(auth()->id(), 'tutorial_updated', [
                'tutorial_id' => $tutorial->id,
                'title' => $tutorial->title,
            ]);

            return redirect()->route('admin.tutorials.show', $tutorial->id)
                ->with('success', 'Tutoriel mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified tutorial.
     */
    public function destroy($id): RedirectResponse
    {
        $tutorial = Tutorial::findOrFail($id);

        DB::beginTransaction();
        try {
            // Supprimer les fichiers associés
            $this->deleteAllTutorialFiles($tutorial);

            // Log avant suppression
            $this->analyticsService->track(auth()->id(), 'tutorial_deleted', [
                'tutorial_id' => $tutorial->id,
                'title' => $tutorial->title,
            ]);

            $tutorial->delete();

            DB::commit();

            return redirect()->route('admin.tutorials.index')
                ->with('success', 'Tutoriel supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Toggle tutorial publication status.
     */
    public function togglePublish($id): JsonResponse
    {
        $tutorial = Tutorial::findOrFail($id);
        
        $isCurrentlyPublished = !$tutorial->is_draft && $tutorial->published_at !== null;
        $newStatus = $isCurrentlyPublished ? 'draft' : 'published';
        
        $updateData = [];
        if ($newStatus === 'published') {
            $updateData = [
                'is_draft' => false,
                'published_at' => now(),
            ];
        } else {
            $updateData = [
                'is_draft' => true,
            ];
        }

        $tutorial->update($updateData);

        // Log de l'action
        $this->analyticsService->track(auth()->id(), 'tutorial_status_changed', [
            'tutorial_id' => $tutorial->id,
            'old_status' => $isCurrentlyPublished ? 'published' : 'draft',
            'new_status' => $newStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => $newStatus === 'published' ? 'Tutoriel publié' : 'Tutoriel mis en brouillon',
            'new_status' => $newStatus,
        ]);
    }

    /**
     * Handle multiple file uploads for a tutorial.
     */
    public function uploadFiles(Request $request, $id): JsonResponse
    {
        $tutorial = Tutorial::findOrFail($id);

        $request->validate([
            'files.*' => 'required|file|mimes:pdf,json,zip|max:10240',
        ]);

        try {
            $uploadedFiles = $this->handleFileUploads($request->file('files'), $tutorial);

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' fichier(s) uploadé(s) avec succès',
                'files' => $uploadedFiles,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tutorial statistics for charts.
     */
    public function getStats(Request $request): JsonResponse
    {
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays($period);

        // Évolution des téléchargements
        $downloadStats = Download::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top tutoriels
        $topTutorials = Tutorial::withCount('downloads')
            ->orderBy('downloads_count', 'desc')
            ->limit(10)
            ->get(['id', 'title', 'downloads_count']);

        // Répartition par catégorie
        $categoryStats = Tutorial::join('categories', 'tutorials.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, COUNT(*) as count')
            ->groupBy('categories.id', 'categories.name')
            ->get();

        // Répartition par niveau
        $difficultyStats = Tutorial::selectRaw('difficulty_level, COUNT(*) as count')
            ->groupBy('difficulty_level')
            ->get();

        return response()->json([
            'download_stats' => $downloadStats,
            'top_tutorials' => $topTutorials,
            'category_stats' => $categoryStats,
            'difficulty_stats' => $difficultyStats,
        ]);
    }

    /**
     * Handle file uploads for a tutorial.
     */
    private function handleFileUploads(array $files, Tutorial $tutorial): array
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;
            
            $path = $file->storeAs("tutorials/{$tutorial->id}", $filename, 'local');
            
            $uploadedFiles[] = [
                'original_name' => $originalName,
                'filename' => $filename,
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];
        }

        // Mettre à jour les fichiers du tutoriel
        $currentFiles = $tutorial->files ? json_decode($tutorial->files, true) : [];
        $allFiles = array_merge($currentFiles, $uploadedFiles);
        $tutorial->update(['files' => json_encode($allFiles)]);

        return $uploadedFiles;
    }

    /**
     * Remove specific files from a tutorial.
     */
    private function removeFiles(array $filesToRemove, Tutorial $tutorial): void
    {
        $currentFiles = $tutorial->files ? json_decode($tutorial->files, true) : [];
        
        foreach ($filesToRemove as $fileToRemove) {
            // Supprimer le fichier du stockage
            Storage::disk('local')->delete($fileToRemove);
            
            // Retirer de la liste
            $currentFiles = array_filter($currentFiles, function($file) use ($fileToRemove) {
                return $file['path'] !== $fileToRemove;
            });
        }

        $tutorial->update(['files' => json_encode(array_values($currentFiles))]);
    }

    /**
     * Delete all files associated with a tutorial.
     */
    private function deleteAllTutorialFiles(Tutorial $tutorial): void
    {
        // Supprimer la thumbnail
        if ($tutorial->thumbnail) {
            Storage::disk('public')->delete($tutorial->thumbnail);
        }

        // Supprimer tous les fichiers
        if ($tutorial->files) {
            $files = json_decode($tutorial->files, true);
            foreach ($files as $file) {
                Storage::disk('local')->delete($file['path']);
            }
        }

        // Supprimer le dossier du tutoriel
        Storage::disk('local')->deleteDirectory("tutorials/{$tutorial->id}");
    }

    /**
     * Calculate tutorial completion rate.
     */
    private function calculateCompletionRate(Tutorial $tutorial): float
    {
        $totalProgress = $tutorial->tutorialProgress()->count();
        if ($totalProgress === 0) {
            return 0;
        }

        $completedProgress = $tutorial->tutorialProgress()->where('completed', true)->count();
        return round(($completedProgress / $totalProgress) * 100, 2);
    }
    
    /**
     * Display a paginated listing of blog posts.
     */
    public function blogIndex(Request $request)
    {
        try {
            $query = BlogPost::with(['author', 'category', 'tags']);

            // Filtres
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->get('category_id'));
            }

            if ($request->filled('status')) {
                $status = $request->get('status');
                if ($status === 'published') {
                    $query->where('is_published', true);
                } else {
                    $query->where('is_published', false);
                }
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

            $posts = $query->paginate(20);

            // Données pour les filtres
            $categories = Category::all();
            $tags = Tag::all();

            // Statistiques
            $stats = [
                'total' => BlogPost::count(),
                'published' => BlogPost::where('is_published', true)->count(),
                'draft' => BlogPost::where('is_published', false)->count(),
            ];

            return view('admin.blog.index', compact('posts', 'categories', 'tags', 'stats'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans TutorialManagementController::blogIndex', ['error' => $e->getMessage()]);
            
            // Valeurs par défaut en cas d'erreur
            $posts = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), 0, 20, 1, ['path' => request()->url()]
            );
            $categories = Category::all();
            $tags = Tag::all();
            $stats = ['total' => 0, 'published' => 0, 'draft' => 0];
            
            return view('admin.blog.index', compact('posts', 'categories', 'tags', 'stats'))
                ->with('error', 'Une erreur est survenue lors du chargement des articles de blog.');
        }
    }
    
    /**
     * Show the form for creating a new blog post.
     */
    public function blogCreate()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.blog.create', compact('categories', 'tags'));
    }
    
    /**
     * Store a newly created blog post.
     */
    public function blogStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'required|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Créer l'article
            $post = BlogPost::create([
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']),
                'content' => $validated['content'],
                'excerpt' => $validated['excerpt'],
                'category_id' => $validated['category_id'],
                'author_id' => auth()->id(),
                'is_published' => $validated['is_published'] ?? false,
                'published_at' => $validated['is_published'] ? now() : null,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
            ]);

            // Associer les tags
            if (!empty($validated['tags'])) {
                $post->tags()->attach($validated['tags']);
            }

            // Gérer l'upload de l'image à la une
            if ($request->hasFile('featured_image')) {
                $imagePath = $request->file('featured_image')->store('blog/images', 'public');
                $post->update(['featured_image' => $imagePath]);
            }

            DB::commit();

            // Log de l'action
            $this->analyticsService->track(auth()->id(), 'blog_post_created', [
                'post_id' => $post->id,
                'title' => $post->title,
            ]);

            return redirect()->route('admin.blog.show', $post->id)
                ->with('success', 'Article créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'article : ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified blog post.
     */
    public function blogShow($id)
    {
        $post = BlogPost::with(['category', 'tags', 'author'])->findOrFail($id);

        return view('admin.blog.show', compact('post'));
    }
    
    /**
     * Show the form for editing the specified blog post.
     */
    public function blogEdit($id)
    {
        $post = BlogPost::with(['tags'])->findOrFail($id);
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.blog.edit', compact('post', 'categories', 'tags'));
    }
    
    /**
     * Update the specified blog post.
     */
    public function blogUpdate(Request $request, $id): RedirectResponse
    {
        $post = BlogPost::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'required|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Mettre à jour l'article
            $wasPublished = $post->is_published;
            $isNowPublished = $validated['is_published'] ?? false;
            
            $post->update([
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']),
                'content' => $validated['content'],
                'excerpt' => $validated['excerpt'],
                'category_id' => $validated['category_id'],
                'is_published' => $isNowPublished,
                'published_at' => (!$wasPublished && $isNowPublished) ? now() : $post->published_at,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
            ]);

            // Mettre à jour les tags
            if (isset($validated['tags'])) {
                $post->tags()->sync($validated['tags']);
            }

            // Gérer la nouvelle image à la une
            if ($request->hasFile('featured_image')) {
                // Supprimer l'ancienne image
                if ($post->featured_image) {
                    Storage::disk('public')->delete($post->featured_image);
                }
                $imagePath = $request->file('featured_image')->store('blog/images', 'public');
                $post->update(['featured_image' => $imagePath]);
            }

            DB::commit();

            // Log de l'action
            $this->analyticsService->track(auth()->id(), 'blog_post_updated', [
                'post_id' => $post->id,
                'title' => $post->title,
            ]);

            return redirect()->route('admin.blog.show', $post->id)
                ->with('success', 'Article mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified blog post.
     */
    public function blogDestroy($id): RedirectResponse
    {
        $post = BlogPost::findOrFail($id);

        DB::beginTransaction();
        try {
            // Supprimer l'image à la une
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }

            // Log avant suppression
            $this->analyticsService->track(auth()->id(), 'blog_post_deleted', [
                'post_id' => $post->id,
                'title' => $post->title,
            ]);

            $post->delete();

            DB::commit();

            return redirect()->route('admin.blog.index')
                ->with('success', 'Article supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }
    
    /**
     * Display a listing of categories with management options.
     */
    public function categories(Request $request)
    {
        try {
            $query = Category::withCount(['tutorials', 'blogPosts']);

            // Filtres
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $categories = $query->paginate(20);

            // Statistiques
            $stats = [
                'total' => Category::count(),
                'with_tutorials' => Category::has('tutorials')->count(),
                'with_blog_posts' => Category::has('blogPosts')->count(),
                'empty' => Category::doesntHave('tutorials')->doesntHave('blogPosts')->count(),
            ];

            return view('admin.tutorials.categories.index', compact('categories', 'stats'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans TutorialManagementController::categories', ['error' => $e->getMessage()]);
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'Une erreur est survenue lors du chargement des catégories : ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for creating a new category.
     */
    public function createCategory()
    {
        return view('admin.tutorials.categories.create');
    }
    
    /**
     * Store a newly created category.
     */
    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        try {
            $category = Category::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'icon' => $validated['icon'] ?? null,
                'color' => $validated['color'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'display_order' => $validated['display_order'] ?? 0,
            ]);

            // Log de l'action
            $this->analyticsService->track(auth()->id(), 'category_created', [
                'category_id' => $category->id,
                'name' => $category->name,
            ]);

            return redirect()->route('admin.tutorials.categories')
                ->with('success', 'Catégorie créée avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la catégorie : ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing the specified category.
     */
    public function editCategory($id)
    {
        $category = Category::findOrFail($id);

        return view('admin.tutorials.categories.edit', compact('category'));
    }
    
    /**
     * Update the specified category.
     */
    public function updateCategory(Request $request, $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        try {
            $category->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'icon' => $validated['icon'] ?? null,
                'color' => $validated['color'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'display_order' => $validated['display_order'] ?? 0,
            ]);

            // Log de l'action
            $this->analyticsService->track(auth()->id(), 'category_updated', [
                'category_id' => $category->id,
                'name' => $category->name,
            ]);

            return redirect()->route('admin.tutorials.categories')
                ->with('success', 'Catégorie mise à jour avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de la catégorie : ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified category.
     */
    public function destroyCategory($id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        // Vérifier si la catégorie est utilisée
        $tutorialsCount = $category->tutorials()->count();
        $blogPostsCount = $category->blogPosts()->count();

        if ($tutorialsCount > 0 || $blogPostsCount > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer cette catégorie car elle est utilisée par ' . 
                    $tutorialsCount . ' tutoriel(s) et ' . $blogPostsCount . ' article(s) de blog.');
        }

        try {
            // Log avant suppression
            $this->analyticsService->track(auth()->id(), 'category_deleted', [
                'category_id' => $category->id,
                'name' => $category->name,
            ]);

            $category->delete();

            return redirect()->route('admin.tutorials.categories')
                ->with('success', 'Catégorie supprimée avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de la catégorie : ' . $e->getMessage());
        }
    }
    
    /**
     * Bulk update category display order.
     */
    public function updateCategoryOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.display_order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['categories'] as $categoryData) {
                Category::where('id', $categoryData['id'])
                    ->update(['display_order' => $categoryData['display_order']]);
            }

            DB::commit();

            // Log de l'action
            $this->analyticsService->track(auth()->id(), 'category_order_updated', [
                'count' => count($validated['categories']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ordre des catégories mis à jour avec succès.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'ordre des catégories : ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Display a listing of tags with management options.
     */
    public function tags(Request $request)
    {
        try {
            $query = Tag::withCount(['tutorials', 'blogPosts']);

            // Filtres
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Tri
            $sortBy = $request->get('sort_by', 'name');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $tags = $query->paginate(20);

            // Statistiques
            $stats = [
                'total' => Tag::count(),
                'with_tutorials' => Tag::has('tutorials')->count(),
                'with_blog_posts' => Tag::has('blogPosts')->count(),
                'empty' => Tag::doesntHave('tutorials')->doesntHave('blogPosts')->count(),
            ];

            return view('admin.tutorials.tags.index', compact('tags', 'stats'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans TutorialManagementController::tags', ['error' => $e->getMessage()]);
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'Une erreur est survenue lors du chargement des tags : ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for creating a new tag.
     */
    public function createTag()
    {
        return view('admin.tutorials.tags.create');
    }
    
    /**
     * Store a newly created tag.
     */
    public function storeTag(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:20',
        ]);

        try {
            $tag = Tag::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'color' => $validated['color'] ?? null,
            ]);

            // Log de l'action
            $this->analyticsService->track(auth()->id(), 'tag_created', [
                'tag_id' => $tag->id,
                'name' => $tag->name,
            ]);

            return redirect()->route('admin.tutorials.tags')
                ->with('success', 'Tag créé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du tag : ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing the specified tag.
     */
    public function editTag($id)
    {
        $tag = Tag::findOrFail($id);

        return view('admin.tutorials.tags.edit', compact('tag'));
    }
    
    /**
     * Update the specified tag.
     */
    public function updateTag(Request $request, $id): RedirectResponse
    {
        $tag = Tag::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:20',
        ]);

        try {
            $tag->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'color' => $validated['color'] ?? null,
            ]);

            // Log de l'action
            $this->analyticsService->track(auth()->id(), 'tag_updated', [
                'tag_id' => $tag->id,
                'name' => $tag->name,
            ]);

            return redirect()->route('admin.tutorials.tags')
                ->with('success', 'Tag mis à jour avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour du tag : ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified tag.
     */
    public function destroyTag($id): RedirectResponse
    {
        $tag = Tag::findOrFail($id);

        // Vérifier si le tag est utilisé
        $tutorialsCount = $tag->tutorials()->count();
        $blogPostsCount = $tag->blogPosts()->count();

        if ($tutorialsCount > 0 || $blogPostsCount > 0) {
            return redirect()->back()
                ->with('error', 'Impossible de supprimer ce tag car il est utilisé par ' . 
                    $tutorialsCount . ' tutoriel(s) et ' . $blogPostsCount . ' article(s) de blog.');
        }

        try {
            // Log avant suppression
            $this->analyticsService->track(auth()->id(), 'tag_deleted', [
                'tag_id' => $tag->id,
                'name' => $tag->name,
            ]);

            $tag->delete();

            return redirect()->route('admin.tutorials.tags')
                ->with('success', 'Tag supprimé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression du tag : ' . $e->getMessage());
        }
    }
    
    /**
     * Bulk delete tags.
     */
    public function bulkDeleteTags(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'required|exists:tags,id',
        ]);

        DB::beginTransaction();
        try {
            $tagIds = $validated['tag_ids'];
            
            // Vérifier si les tags sont utilisés
            $usedTags = Tag::whereIn('id', $tagIds)
                ->where(function($query) {
                    $query->has('tutorials')->orHas('blogPosts');
                })
                ->get();
            
            if ($usedTags->count() > 0) {
                $usedTagNames = $usedTags->pluck('name')->implode(', ');
                
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer les tags suivants car ils sont utilisés : ' . $usedTagNames,
                ], 400);
            }
            
            // Supprimer les tags
            $deletedCount = Tag::whereIn('id', $tagIds)->delete();
            
            DB::commit();
            
            // Log de l'action
            $this->analyticsService->track(auth()->id(), 'tags_bulk_deleted', [
                'count' => $deletedCount,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $deletedCount . ' tag(s) supprimé(s) avec succès.',
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression des tags : ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Display a listing of files with management options.
     */
    public function files(Request $request)
    {
        try {
            // Récupérer tous les tutoriels avec leurs fichiers
            $query = Tutorial::select(['id', 'title', 'files', 'created_at', 'updated_at'])
                ->whereNotNull('files')
                ->where('files', '<>', '[]');
                
            // Filtres
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where('title', 'like', "%{$search}%");
            }
            
            // Tri
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);
            
            $tutorials = $query->paginate(20);
            
            // Préparer les données des fichiers
            $allFiles = [];
            $totalSize = 0;
            $fileTypes = [];
            
            foreach ($tutorials as $tutorial) {
                if ($tutorial->files) {
                    $files = json_decode($tutorial->files, true);
                    if (is_array($files)) {
                        foreach ($files as $file) {
                            $file['tutorial_id'] = $tutorial->id;
                            $file['tutorial_title'] = $tutorial->title;
                            $allFiles[] = $file;
                            
                            // Calculer la taille totale
                            $totalSize += $file['size'] ?? 0;
                            
                            // Compter les types de fichiers
                            $extension = pathinfo($file['filename'], PATHINFO_EXTENSION);
                            if (!isset($fileTypes[$extension])) {
                                $fileTypes[$extension] = 0;
                            }
                            $fileTypes[$extension]++;
                        }
                    }
                }
            }
            
            // Statistiques
            $stats = [
                'total_files' => count($allFiles),
                'total_size' => $this->formatBytes($totalSize),
                'file_types' => $fileTypes,
                'tutorials_with_files' => $tutorials->total(),
            ];
            
            return view('admin.tutorials.files.index', compact('tutorials', 'allFiles', 'stats'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans TutorialManagementController::files', ['error' => $e->getMessage()]);
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'Une erreur est survenue lors du chargement des fichiers : ' . $e->getMessage());
        }
    }
    
    /**
     * Display file details and download options.
     */
    public function showFile($tutorialId, $filename)
    {
        try {
            $tutorial = Tutorial::findOrFail($tutorialId);
            
            if (!$tutorial->files) {
                return redirect()->route('admin.tutorials.files')
                    ->with('error', 'Ce tutoriel ne contient pas de fichiers.');
            }
            
            $files = json_decode($tutorial->files, true);
            $fileData = null;
            
            foreach ($files as $file) {
                if ($file['filename'] === $filename) {
                    $fileData = $file;
                    break;
                }
            }
            
            if (!$fileData) {
                return redirect()->route('admin.tutorials.files')
                    ->with('error', 'Fichier non trouvé.');
            }
            
            // Statistiques de téléchargement
            $downloadStats = Download::where('tutorial_id', $tutorialId)
                ->where('file_name', $filename)
                ->selectRaw('COUNT(*) as total, DATE(downloaded_at) as date')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get();
            
            return view('admin.tutorials.files.show', compact('tutorial', 'fileData', 'downloadStats'));
        } catch (\Exception $e) {
            \Log::error('Erreur dans TutorialManagementController::showFile', ['error' => $e->getMessage()]);
            
            return redirect()->route('admin.tutorials.files')
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }
    
    /**
     * Download a file.
     */
    public function downloadFile($tutorialId, $filename)
    {
        try {
            $tutorial = Tutorial::findOrFail($tutorialId);
            
            if (!$tutorial->files) {
                return redirect()->route('admin.tutorials.files')
                    ->with('error', 'Ce tutoriel ne contient pas de fichiers.');
            }
            
            $files = json_decode($tutorial->files, true);
            $fileData = null;
            
            foreach ($files as $file) {
                if ($file['filename'] === $filename) {
                    $fileData = $file;
                    break;
                }
            }
            
            if (!$fileData || !isset($fileData['path'])) {
                return redirect()->route('admin.tutorials.files')
                    ->with('error', 'Fichier non trouvé.');
            }
            
            $filePath = storage_path('app/' . $fileData['path']);
            
            if (!file_exists($filePath)) {
                return redirect()->route('admin.tutorials.files')
                    ->with('error', 'Le fichier physique n\'existe pas.');
            }
            
            // Log de l'action
            $this->analyticsService->track(auth()->id(), 'admin_file_downloaded', [
                'tutorial_id' => $tutorial->id,
                'file_name' => $filename,
            ]);
            
            return response()->download($filePath, $fileData['original_name']);
        } catch (\Exception $e) {
            \Log::error('Erreur dans TutorialManagementController::downloadFile', ['error' => $e->getMessage()]);
            
            return redirect()->route('admin.tutorials.files')
                ->with('error', 'Une erreur est survenue lors du téléchargement : ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a file.
     */
    public function deleteFile(Request $request, $tutorialId, $filename): RedirectResponse
    {
        try {
            $tutorial = Tutorial::findOrFail($tutorialId);
            
            if (!$tutorial->files) {
                return redirect()->route('admin.tutorials.files')
                    ->with('error', 'Ce tutoriel ne contient pas de fichiers.');
            }
            
            $files = json_decode($tutorial->files, true);
            $fileIndex = null;
            $fileData = null;
            
            foreach ($files as $index => $file) {
                if ($file['filename'] === $filename) {
                    $fileIndex = $index;
                    $fileData = $file;
                    break;
                }
            }
            
            if ($fileIndex === null || !$fileData) {
                return redirect()->route('admin.tutorials.files')
                    ->with('error', 'Fichier non trouvé.');
            }
            
            // Supprimer le fichier physique
            if (isset($fileData['path'])) {
                Storage::delete($fileData['path']);
            }
            
            // Supprimer l'entrée du fichier dans le tutoriel
            array_splice($files, $fileIndex, 1);
            $tutorial->update(['files' => json_encode($files)]);
            
            // Log de l'action
            $this->analyticsService->track(auth()->id(), 'file_deleted', [
                'tutorial_id' => $tutorial->id,
                'file_name' => $filename,
            ]);
            
            return redirect()->route('admin.tutorials.files')
                ->with('success', 'Fichier supprimé avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur dans TutorialManagementController::deleteFile', ['error' => $e->getMessage()]);
            
            return redirect()->route('admin.tutorials.files')
                ->with('error', 'Une erreur est survenue lors de la suppression : ' . $e->getMessage());
        }
    }
    
    /**
     * Upload files to a tutorial.
     */
    public function uploadFilesToTutorial(Request $request, $id): RedirectResponse
    {
        $tutorial = Tutorial::findOrFail($id);
        
        $request->validate([
            'files.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,json,txt|max:10240',
        ]);
        
        if (!$request->hasFile('files')) {
            return redirect()->back()
                ->with('error', 'Aucun fichier sélectionné.');
        }
        
        try {
            $uploadedFiles = $this->handleFileUploads($request->file('files'), $tutorial);
            
            // Log de l'action
            $this->analyticsService->track(auth()->id(), 'files_uploaded', [
                'tutorial_id' => $tutorial->id,
                'count' => count($uploadedFiles),
            ]);
            
            return redirect()->route('admin.tutorials.files')
                ->with('success', count($uploadedFiles) . ' fichier(s) uploadé(s) avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur dans TutorialManagementController::uploadFilesToTutorial', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'upload : ' . $e->getMessage());
        }
    }
    
    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
