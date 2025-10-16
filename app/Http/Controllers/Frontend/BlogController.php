<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class BlogController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display a listing of blog posts.
     */
    public function index(Request $request)
    {
        // Construire la requête des articles
        $query = BlogPost::where('status', 'published')
            ->where('published_at', '<=', now());

        // Filtres
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->get('tag'));
        }

        // Tri
        $sortBy = $request->get('sort', 'published_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $posts = $query->paginate(9);

        // Transformer les articles pour l'affichage
        $posts->getCollection()->transform(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'thumbnail' => $post->thumbnail,
                'category' => $post->category,
                'tags' => $post->tags ? json_decode($post->tags, true) : [],
                'published_at' => $post->published_at,
                'slug' => $post->slug,
                'reading_time' => $this->calculateReadingTime($post->content),
                'author' => [
                    'name' => $post->author_name ?: 'Équipe Automatehub',
                    'avatar' => $post->author_avatar,
                ],
            ];
        });

        // Articles en vedette (les plus récents)
        $featuredPosts = BlogPost::where('status', 'published')
            ->where('published_at', '<=', now())
            ->where('featured', true)
            ->latest('published_at')
            ->limit(3)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'thumbnail' => $post->thumbnail,
                    'category' => $post->category,
                    'published_at' => $post->published_at,
                    'slug' => $post->slug,
                    'reading_time' => $this->calculateReadingTime($post->content),
                ];
            });

        // Catégories disponibles
        $categories = BlogPost::where('status', 'published')
            ->selectRaw('category, COUNT(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->category,
                    'count' => $item->count,
                    'slug' => \Str::slug($item->category),
                ];
            });

        // Tags populaires
        $popularTags = $this->getPopularTags();

        // Articles récents pour sidebar
        $recentPosts = BlogPost::where('status', 'published')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->limit(5)
            ->get(['id', 'title', 'slug', 'published_at', 'thumbnail']);

        // Call-to-action pour inscription
        $callToAction = [
            'title' => 'Restez informé des dernières actualités n8n',
            'subtitle' => 'Inscrivez-vous pour recevoir nos articles et tutoriels',
            'button' => [
                'text' => 'S\'inscrire gratuitement',
                'url' => route('register'),
            ],
        ];

        // Meta données pour SEO
        $metaData = [
            'title' => 'Blog - Actualités et conseils n8n | Automatehub',
            'description' => 'Découvrez nos articles sur l\'automation, n8n, et les dernières tendances. Conseils, tutoriels et actualités pour maîtriser l\'automation.',
            'keywords' => 'blog, n8n, automation, conseils, actualités, tutoriels',
            'url' => route('frontend.blog.index'),
        ];

        // Tracking
        $this->analyticsService->trackAnonymous('blog_index_viewed', [
            'filters' => $request->only(['category', 'search', 'tag']),
            'total_posts' => $posts->total(),
        ]);

        return view('frontend.blog.index', compact(
            'posts',
            'featuredPosts',
            'categories',
            'popularTags',
            'recentPosts',
            'callToAction',
            'metaData'
        ));
    }

    /**
     * Display the specified blog post.
     */
    public function show($slug)
    {
        $post = BlogPost::with(['category', 'author', 'tags'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->firstOrFail();

        // Increment view count
        $post->incrementViewCount();

        // Extract SEO data from article_data (generated by n8n) or use fallback
        $seo = $this->extractSeoData($post);

        // Get related posts
        $relatedPosts = $post->getRelatedPosts(3);

        // Tracking
        $this->analyticsService->trackAnonymous('blog_post_viewed', [
            'post_id' => $post->id,
            'post_title' => $post->title,
            'category' => $post->category?->name,
            'reading_time' => $post->reading_time,
        ]);

        return view('blog-post', compact('post', 'seo', 'relatedPosts'));
    }

    /**
     * Extract and build SEO data from article_data or use fallbacks.
     */
    private function extractSeoData(BlogPost $post): array
    {
        $seo = [
            'title' => $post->title . ' | Blog Automatehub',
            'meta_description' => $post->excerpt ?: $this->createExcerpt($post->content, 160),
        ];

        // If article_data exists (from n8n workflow), extract rich SEO data
        if ($post->article_data && is_array($post->article_data)) {
            $articleData = $post->article_data;

            // Extract meta tags
            if (isset($articleData['meta_tags']) && is_array($articleData['meta_tags'])) {
                $seo['meta_tags'] = $articleData['meta_tags'];
            }

            // Extract Schema.org structured data
            if (isset($articleData['schema_org'])) {
                $seo['schema_org'] = $articleData['schema_org'];
            }

            // Extract FAQ if available
            if (isset($articleData['faq']) && is_array($articleData['faq'])) {
                $seo['faq'] = $articleData['faq'];
            }

            // Extract CTA if available
            if (isset($articleData['cta'])) {
                $seo['cta'] = $articleData['cta'];
            }

            // Extract internal links suggestions
            if (isset($articleData['internal_links']) && is_array($articleData['internal_links'])) {
                $seo['internal_links'] = $articleData['internal_links'];
            }

            // Use enhanced title and description if available
            if (isset($articleData['seo_title'])) {
                $seo['title'] = $articleData['seo_title'];
            }

            if (isset($articleData['seo_description'])) {
                $seo['meta_description'] = $articleData['seo_description'];
            }
        }

        return $seo;
    }

    /**
     * Display blog posts by category.
     */
    public function category($slug, Request $request)
    {
        // Convertir le slug en nom de catégorie
        $categoryName = ucfirst(str_replace('-', ' ', $slug));

        // Vérifier que la catégorie existe
        $categoryExists = BlogPost::where('status', 'published')
            ->where('category', $categoryName)
            ->exists();

        if (!$categoryExists) {
            abort(404);
        }

        // Construire la requête
        $query = BlogPost::where('status', 'published')
            ->where('published_at', '<=', now())
            ->where('category', $categoryName);

        // Filtres additionnels
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortBy = $request->get('sort', 'published_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $posts = $query->paginate(9);

        // Transformer les articles
        $posts->getCollection()->transform(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'thumbnail' => $post->thumbnail,
                'tags' => $post->tags ? json_decode($post->tags, true) : [],
                'published_at' => $post->published_at,
                'slug' => $post->slug,
                'reading_time' => $this->calculateReadingTime($post->content),
            ];
        });

        // Statistiques de la catégorie
        $categoryStats = [
            'total_posts' => BlogPost::where('status', 'published')
                ->where('category', $categoryName)
                ->count(),
            'latest_post' => BlogPost::where('status', 'published')
                ->where('category', $categoryName)
                ->latest('published_at')
                ->first(['published_at']),
        ];

        // Breadcrumbs
        $breadcrumbs = [
            ['name' => 'Accueil', 'url' => route('frontend.home')],
            ['name' => 'Blog', 'url' => route('frontend.blog.index')],
            ['name' => $categoryName, 'url' => null],
        ];

        // Meta données pour SEO
        $metaData = [
            'title' => $categoryName . ' - Blog | Automatehub',
            'description' => "Découvrez tous nos articles sur {$categoryName}. Conseils, actualités et guides pour maîtriser l'automation avec n8n.",
            'keywords' => $categoryName . ', n8n, automation, blog, conseils',
            'url' => route('frontend.blog.category', $slug),
        ];

        // Tracking
        $this->analyticsService->trackAnonymous('blog_category_viewed', [
            'category' => $categoryName,
            'posts_count' => $posts->total(),
        ]);

        return view('frontend.blog.category', compact(
            'posts',
            'categoryName',
            'categoryStats',
            'breadcrumbs',
            'metaData'
        ));
    }

    /**
     * Search blog posts.
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
            'category' => 'nullable|string|max:100',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        $query = BlogPost::where('status', 'published')
            ->where('published_at', '<=', now());

        // Recherche textuelle
        $search = $request->get('q');
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('excerpt', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
        });

        // Filtre par catégorie
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        $limit = $request->get('limit', 10);
        $posts = $query->latest('published_at')->limit($limit)->get();

        $results = $posts->map(function($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'excerpt' => $post->excerpt,
                'thumbnail' => $post->thumbnail,
                'category' => $post->category,
                'published_at' => $post->published_at,
                'slug' => $post->slug,
                'url' => route('frontend.blog.show', $post->slug),
                'reading_time' => $this->calculateReadingTime($post->content),
            ];
        });

        // Tracking
        $this->analyticsService->trackAnonymous('blog_search', [
            'query' => $search,
            'category' => $request->get('category'),
            'results_count' => $results->count(),
        ]);

        return response()->json([
            'results' => $results,
            'total' => $results->count(),
            'query' => $search,
        ]);
    }

    /**
     * Get blog post suggestions for autocomplete.
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $query = $request->get('q');

        $posts = BlogPost::where('status', 'published')
            ->where('published_at', '<=', now())
            ->where('title', 'like', "%{$query}%")
            ->limit(5)
            ->get(['id', 'title', 'slug']);

        $suggestions = $posts->map(function($post) {
            return [
                'title' => $post->title,
                'url' => route('frontend.blog.show', $post->slug),
            ];
        });

        return response()->json($suggestions);
    }

    /**
     * Get recent blog posts for widgets.
     */
    public function getRecent(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);

        $posts = BlogPost::where('status', 'published')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->limit($limit)
            ->get()
            ->map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => $this->createExcerpt($post->excerpt ?: $post->content, 100),
                    'thumbnail' => $post->thumbnail,
                    'category' => $post->category,
                    'published_at' => $post->published_at,
                    'slug' => $post->slug,
                    'url' => route('frontend.blog.show', $post->slug),
                ];
            });

        return response()->json($posts);
    }

    /**
     * Calculate reading time for a blog post.
     */
    private function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $wordsPerMinute = 200; // Vitesse de lecture moyenne en français
        
        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Create excerpt from content.
     */
    private function createExcerpt(string $content, int $length = 160): string
    {
        $content = strip_tags($content);
        if (strlen($content) <= $length) {
            return $content;
        }
        
        return substr($content, 0, $length) . '...';
    }

    /**
     * Get popular tags from blog posts.
     */
    private function getPopularTags(): array
    {
        $posts = BlogPost::where('status', 'published')
            ->whereNotNull('tags')
            ->get(['tags']);

        $tagCounts = [];
        
        foreach ($posts as $post) {
            $tags = json_decode($post->tags, true);
            if (is_array($tags)) {
                foreach ($tags as $tag) {
                    $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
                }
            }
        }

        // Trier par popularité et prendre les 10 premiers
        arsort($tagCounts);
        $popularTags = array_slice($tagCounts, 0, 10, true);

        return array_map(function($tag, $count) {
            return [
                'name' => $tag,
                'count' => $count,
                'slug' => \Str::slug($tag),
            ];
        }, array_keys($popularTags), $popularTags);
    }
}
