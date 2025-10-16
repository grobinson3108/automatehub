<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Models\Tutorial;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TutorialController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display public tutorials page with conversion optimization.
     */
    public function publicIndex(Request $request)
    {
        // Récupérer les tutoriels en vedette
        $featuredTutorials = Tutorial::with(['category'])
            ->where('status', 'published')
            ->withCount('downloads')
            ->orderBy('downloads_count', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($tutorial) {
                return [
                    'id' => $tutorial->id,
                    'title' => $tutorial->title,
                    'description' => $tutorial->description,
                    'thumbnail' => $tutorial->thumbnail,
                    'category' => $tutorial->category->name,
                    'difficulty_level' => $tutorial->difficulty_level,
                    'subscription_type' => $tutorial->subscription_type,
                    'downloads_count' => $tutorial->downloads_count,
                    'views_count' => $tutorial->views_count ?? rand(100, 1000),
                    'duration_minutes' => $tutorial->estimated_duration ?? rand(15, 90),
                    'slug' => $tutorial->slug,
                ];
            });

        // Catégories populaires
        $categories = Category::withCount(['tutorials' => function($query) {
                $query->where('status', 'published');
            }])
            ->having('tutorials_count', '>', 0)
            ->orderBy('tutorials_count', 'desc')
            ->limit(8)
            ->get();

        // Statistiques pour social proof
        $stats = [
            'totalTutorials' => Tutorial::where('status', 'published')->count(),
            'totalUsers' => \App\Models\User::count(),
            'totalDownloads' => \App\Models\Download::count(),
            'avgRating' => 4.8,
        ];

        // Tracking
        $this->analyticsService->trackAnonymous('tutorials_page_viewed', [
            'source' => 'public_index'
        ]);

        return inertia('Tutorials/PublicIndex', [
            'featuredTutorials' => $featuredTutorials,
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }

    /**
     * Display tutorials grid with previews (incitation to register).
     */
    public function index(Request $request)
    {
        // Construire la requête des tutoriels
        $query = Tutorial::with(['category', 'tags'])
            ->published();

        // Filtres
        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        if ($request->filled('level')) {
            $query->where('difficulty_level', $request->get('level'));
        }

        if ($request->filled('type')) {
            $type = $request->get('type');
            if ($type !== 'all') {
                $query->where('subscription_required', $type);
            }
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('tags', function($tagQuery) use ($search) {
                      $tagQuery->where('name', 'like', "%{$search}%");
                  });
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

        // Transformer les tutoriels pour l'affichage public (aperçus seulement)
        $tutorials->getCollection()->transform(function ($tutorial) {
            return [
                'id' => $tutorial->id,
                'title' => $tutorial->title,
                'description' => $tutorial->description,
                'excerpt' => $this->createExcerpt($tutorial->description, 150),
                'thumbnail' => $tutorial->thumbnail,
                'category' => [
                    'id' => $tutorial->category->id,
                    'name' => $tutorial->category->name,
                    'slug' => $tutorial->category->slug,
                    'color' => $tutorial->category->color,
                ],
                'tags' => $tutorial->tags->map(function($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                    ];
                }),
                'difficulty_level' => $tutorial->difficulty_level,
                'subscription_type' => $tutorial->subscription_required,
                'estimated_duration' => $tutorial->estimated_duration,
                'downloads_count' => $tutorial->downloads()->count(),
                'slug' => $tutorial->slug,
                'is_premium' => $tutorial->subscription_required !== 'free',
                'preview_only' => true, // Toujours en aperçu pour les visiteurs
                'created_at' => $tutorial->created_at,
            ];
        });

        // Données pour les filtres
        $categories = Category::withCount(['tutorials' => function($query) {
                $query->published();
            }])
            ->having('tutorials_count', '>', 0)
            ->orderBy('name')
            ->get();

        $tags = Tag::withCount(['tutorials' => function($query) {
                $query->published();
            }])
            ->having('tutorials_count', '>', 0)
            ->orderBy('name')
            ->get();

        // Statistiques pour call-to-action
        $stats = [
            'total_tutorials' => Tutorial::published()->count(),
            'free_tutorials' => Tutorial::published()->where('subscription_required', 'free')->count(),
            'premium_tutorials' => Tutorial::published()->where('subscription_required', 'premium')->count(),
            'pro_tutorials' => Tutorial::published()->where('subscription_required', 'pro')->count(),
        ];

        // Call-to-action pour inscription
        $callToAction = [
            'title' => 'Accédez à tous nos tutoriels',
            'subtitle' => 'Inscrivez-vous gratuitement pour débloquer le contenu complet',
            'benefits' => [
                'Accès aux tutoriels gratuits',
                'Suivi de progression',
                'Système de badges',
                'Téléchargements illimités (premium)',
            ],
            'button' => [
                'text' => 'S\'inscrire gratuitement',
                'url' => route('register'),
            ],
        ];

        // Tracking
        $this->analyticsService->trackAnonymous('tutorials_page_viewed', [
            'filters' => $request->only(['category', 'level', 'type', 'search']),
            'sort' => $sortBy,
            'total_results' => $tutorials->total(),
        ]);

        return inertia('Tutorials/Index', compact(
            'tutorials',
            'categories',
            'tags',
            'stats',
            'callToAction'
        ));
    }

    /**
     * Display tutorial preview for non-logged visitors.
     */
    public function show($slug)
    {
        $tutorial = Tutorial::with(['category', 'tags'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Données du tutoriel pour aperçu
        $tutorialData = [
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
            'tags' => $tutorial->tags->map(function($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ];
            }),
            'difficulty_level' => $tutorial->difficulty_level,
            'subscription_type' => $tutorial->subscription_required,
            'estimated_duration' => $tutorial->estimated_duration,
            'target_audience' => $tutorial->target_audience,
            'downloads_count' => $tutorial->downloads()->count(),
            'slug' => $tutorial->slug,
            'is_premium' => $tutorial->subscription_required !== 'free',
            'created_at' => $tutorial->created_at,
            'updated_at' => $tutorial->updated_at,
            // Contenu limité pour inciter à l'inscription
            'content_preview' => $this->createContentPreview($tutorial->content),
            'files_count' => $tutorial->files ? count(json_decode($tutorial->files, true)) : 0,
        ];

        // Tutoriels similaires
        $similarTutorials = Tutorial::published()
            ->where('id', '!=', $tutorial->id)
            ->where(function($query) use ($tutorial) {
                $query->where('category_id', $tutorial->category_id)
                      ->orWhere('difficulty_level', $tutorial->difficulty_level);
            })
            ->with('category')
            ->limit(4)
            ->get()
            ->map(function($t) {
                return [
                    'id' => $t->id,
                    'title' => $t->title,
                    'thumbnail' => $t->thumbnail,
                    'category' => $t->category->name,
                    'difficulty_level' => $t->difficulty_level,
                    'subscription_type' => $t->subscription_required,
                    'slug' => $t->slug,
                ];
            });

        // Call-to-action spécifique au tutoriel
        $callToAction = [
            'title' => $tutorial->subscription_required === 'free' 
                ? 'Inscrivez-vous pour accéder à ce tutoriel gratuit'
                : 'Débloquez ce tutoriel ' . ucfirst($tutorial->subscription_required),
            'subtitle' => $tutorial->subscription_required === 'free'
                ? 'Créez votre compte gratuit pour accéder au contenu complet'
                : 'Upgrader votre compte pour accéder aux tutoriels ' . $tutorial->subscription_required,
            'benefits' => $this->getSubscriptionBenefits($tutorial->subscription_required),
            'button' => [
                'text' => $tutorial->subscription_required === 'free' 
                    ? 'S\'inscrire gratuitement'
                    : 'Découvrir ' . ucfirst($tutorial->subscription_required),
                'url' => $tutorial->subscription_required === 'free'
                    ? route('register')
                    : route('register') . '?plan=' . $tutorial->subscription_required,
            ],
        ];

        // Breadcrumbs pour SEO
        $breadcrumbs = [
            ['name' => 'Accueil', 'url' => route('frontend.home')],
            ['name' => 'Tutoriels', 'url' => route('frontend.tutorials.index')],
            ['name' => $tutorial->category->name, 'url' => route('frontend.tutorials.category', $tutorial->category->slug)],
            ['name' => $tutorial->title, 'url' => null],
        ];

        // Meta données pour SEO
        $metaData = [
            'title' => $tutorial->title . ' - Tutoriel n8n | Automatehub',
            'description' => $this->createExcerpt($tutorial->description, 160),
            'keywords' => $tutorial->tags->pluck('name')->join(', ') . ', n8n, automation, tutoriel',
            'image' => $tutorial->thumbnail,
            'url' => route('frontend.tutorials.show', $tutorial->slug),
        ];

        // Tracking
        $this->analyticsService->trackAnonymous('tutorial_preview_viewed', [
            'tutorial_id' => $tutorial->id,
            'tutorial_title' => $tutorial->title,
            'subscription_type' => $tutorial->subscription_required,
            'difficulty_level' => $tutorial->difficulty_level,
            'category' => $tutorial->category->name,
        ]);

        return inertia('Tutorials/Show', compact(
            'tutorialData',
            'similarTutorials',
            'callToAction',
            'breadcrumbs',
            'metaData'
        ));
    }

    /**
     * Search tutorials with filters.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'category' => 'nullable|exists:categories,id',
            'level' => 'nullable|in:beginner,intermediate,advanced,expert',
            'type' => 'nullable|in:free,premium,pro',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $query = Tutorial::with(['category', 'tags'])
            ->published();

        // Recherche textuelle
        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('tags', function($tagQuery) use ($search) {
                      $tagQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtres
        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        if ($request->filled('level')) {
            $query->where('difficulty_level', $request->get('level'));
        }

        if ($request->filled('type')) {
            $query->where('subscription_required', $request->get('type'));
        }

        $limit = $request->get('limit', 10);
        $tutorials = $query->limit($limit)->get();

        $results = $tutorials->map(function($tutorial) {
            return [
                'id' => $tutorial->id,
                'title' => $tutorial->title,
                'description' => $this->createExcerpt($tutorial->description, 100),
                'thumbnail' => $tutorial->thumbnail,
                'category' => $tutorial->category->name,
                'difficulty_level' => $tutorial->difficulty_level,
                'subscription_type' => $tutorial->subscription_required,
                'slug' => $tutorial->slug,
                'url' => route('frontend.tutorials.show', $tutorial->slug),
            ];
        });

        // Tracking
        $this->analyticsService->trackAnonymous('tutorial_search', [
            'query' => $request->get('q'),
            'filters' => $request->only(['category', 'level', 'type']),
            'results_count' => $results->count(),
        ]);

        return response()->json([
            'results' => $results,
            'total' => $results->count(),
            'query' => $request->get('q'),
        ]);
    }

    /**
     * Display tutorials by category.
     */
    public function category($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        // Construire la requête des tutoriels de cette catégorie
        $query = Tutorial::with(['category', 'tags'])
            ->published()
            ->where('category_id', $category->id);

        // Filtres additionnels
        if ($request->filled('level')) {
            $query->where('difficulty_level', $request->get('level'));
        }

        if ($request->filled('type')) {
            $type = $request->get('type');
            if ($type !== 'all') {
                $query->where('subscription_required', $type);
            }
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

        $tutorials = $query->paginate(12);

        // Transformer pour l'affichage public
        $tutorials->getCollection()->transform(function ($tutorial) {
            return [
                'id' => $tutorial->id,
                'title' => $tutorial->title,
                'description' => $this->createExcerpt($tutorial->description, 150),
                'thumbnail' => $tutorial->thumbnail,
                'difficulty_level' => $tutorial->difficulty_level,
                'subscription_type' => $tutorial->subscription_required,
                'estimated_duration' => $tutorial->estimated_duration,
                'slug' => $tutorial->slug,
                'is_premium' => $tutorial->subscription_required !== 'free',
            ];
        });

        // Statistiques de la catégorie
        $categoryStats = [
            'total_tutorials' => $category->tutorials()->published()->count(),
            'free_tutorials' => $category->tutorials()->published()->where('subscription_required', 'free')->count(),
            'premium_tutorials' => $category->tutorials()->published()->where('subscription_required', 'premium')->count(),
            'pro_tutorials' => $category->tutorials()->published()->where('subscription_required', 'pro')->count(),
        ];

        // Breadcrumbs
        $breadcrumbs = [
            ['name' => 'Accueil', 'url' => route('frontend.home')],
            ['name' => 'Tutoriels', 'url' => route('frontend.tutorials.index')],
            ['name' => $category->name, 'url' => null],
        ];

        // Meta données pour SEO
        $metaData = [
            'title' => $category->name . ' - Tutoriels n8n | Automatehub',
            'description' => $category->description ?: "Découvrez nos tutoriels n8n dans la catégorie {$category->name}. Apprenez l'automation étape par étape.",
            'keywords' => $category->name . ', n8n, automation, tutoriels',
            'url' => route('frontend.tutorials.category', $category->slug),
        ];

        // Tracking
        $this->analyticsService->trackAnonymous('category_viewed', [
            'category_id' => $category->id,
            'category_name' => $category->name,
            'tutorials_count' => $tutorials->total(),
        ]);

        return view('frontend.tutorials.category', compact(
            'category',
            'tutorials',
            'categoryStats',
            'breadcrumbs',
            'metaData'
        ));
    }

    /**
     * Get autocomplete suggestions for search.
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $query = $request->get('q');

        // Recherche dans les titres de tutoriels
        $tutorials = Tutorial::published()
            ->where('title', 'like', "%{$query}%")
            ->limit(5)
            ->get(['id', 'title', 'slug']);

        // Recherche dans les catégories
        $categories = Category::where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get(['id', 'name', 'slug']);

        // Recherche dans les tags
        $tags = Tag::where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get(['id', 'name', 'slug']);

        $suggestions = [
            'tutorials' => $tutorials->map(function($tutorial) {
                return [
                    'type' => 'tutorial',
                    'title' => $tutorial->title,
                    'url' => route('frontend.tutorials.show', $tutorial->slug),
                ];
            }),
            'categories' => $categories->map(function($category) {
                return [
                    'type' => 'category',
                    'title' => $category->name,
                    'url' => route('frontend.tutorials.category', $category->slug),
                ];
            }),
            'tags' => $tags->map(function($tag) {
                return [
                    'type' => 'tag',
                    'title' => $tag->name,
                    'url' => route('frontend.tutorials.index', ['tag' => $tag->slug]),
                ];
            }),
        ];

        return response()->json($suggestions);
    }

    /**
     * Create excerpt from text.
     */
    private function createExcerpt(string $text, int $length = 150): string
    {
        $text = strip_tags($text);
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . '...';
    }

    /**
     * Create content preview for non-logged users.
     */
    private function createContentPreview(?string $content): string
    {
        if (!$content) {
            return 'Contenu disponible après inscription...';
        }

        $preview = strip_tags($content);
        $words = explode(' ', $preview);
        
        // Montrer seulement les 50 premiers mots
        if (count($words) > 50) {
            $preview = implode(' ', array_slice($words, 0, 50)) . '...';
        }
        
        return $preview . "\n\n[Contenu complet disponible après inscription]";
    }

    /**
     * Get subscription benefits based on type.
     */
    private function getSubscriptionBenefits(string $subscriptionType): array
    {
        switch ($subscriptionType) {
            case 'free':
                return [
                    'Accès aux tutoriels gratuits',
                    'Suivi de progression',
                    'Système de badges',
                    'Communauté Discord',
                ];
            case 'premium':
                return [
                    'Tous les avantages gratuits',
                    'Accès aux tutoriels premium',
                    'Téléchargements illimités',
                    'Support prioritaire',
                ];
            case 'pro':
                return [
                    'Tous les avantages premium',
                    'Tutoriels sur demande',
                    'Support dédié',
                    'Fonctionnalités entreprise',
                ];
            default:
                return [];
        }
    }
}
