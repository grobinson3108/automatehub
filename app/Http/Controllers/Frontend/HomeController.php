<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Models\Tutorial;
use App\Models\User;
use App\Models\Download;
use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class HomeController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the home page with statistics and featured content.
     */
    public function index()
    {
        // Statistiques du site pour preuves sociales
        $siteStats = [
            'total_users' => User::count(),
            'total_tutorials' => Tutorial::where('status', 'published')->count(),
            'total_downloads' => Download::count(),
            'total_categories' => Category::count(),
        ];

        // Tutoriels en vedette (les plus populaires)
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
                    'estimated_duration' => $tutorial->estimated_duration,
                    'slug' => $tutorial->slug,
                ];
            });

        // Catégories populaires
        $popularCategories = Category::withCount(['tutorials' => function($query) {
                $query->where('status', 'published');
            }])
            ->having('tutorials_count', '>', 0)
            ->orderBy('tutorials_count', 'desc')
            ->limit(8)
            ->get();

        // Articles de blog récents
        $recentBlogPosts = BlogPost::where('status', 'published')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'thumbnail' => $post->thumbnail,
                    'published_at' => $post->published_at,
                    'slug' => $post->slug,
                ];
            });

        // Témoignages (simulés pour l'exemple - vous pourriez avoir une table dédiée)
        $testimonials = $this->getTestimonials();

        // Tutoriels gratuits pour inciter à l'inscription
        $freeTutorials = Tutorial::where('status', 'published')
            ->where('subscription_type', 'free')
            ->with('category')
            ->latest()
            ->limit(4)
            ->get();

        // Statistiques de croissance (pour preuves sociales)
        $growthStats = [
            'new_users_this_month' => User::whereMonth('created_at', Carbon::now()->month)->count(),
            'new_tutorials_this_month' => Tutorial::where('status', 'published')
                ->whereMonth('created_at', Carbon::now()->month)
                ->count(),
            'downloads_this_month' => Download::whereMonth('downloaded_at', Carbon::now()->month)->count(),
        ];

        // Tracking de la visite (anonyme)
        $this->analyticsService->trackAnonymous('homepage_viewed', [
            'user_agent' => request()->userAgent(),
            'referer' => request()->header('referer'),
        ]);

        return inertia('Welcome', [
            'featuredTutorials' => $featuredTutorials,
            'categories' => $popularCategories,
            'stats' => [
                'totalTutorials' => $siteStats['total_tutorials'],
                'totalUsers' => $siteStats['total_users'],
                'totalDownloads' => $siteStats['total_downloads'],
                'avgRating' => 4.8, // Moyenne des évaluations
            ],
            'testimonials' => $testimonials,
            'growthStats' => $growthStats,
        ]);
    }

    /**
     * Get site statistics for API calls.
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'new_this_month' => User::whereMonth('created_at', Carbon::now()->month)->count(),
                'active_this_week' => User::where('last_activity_at', '>=', Carbon::now()->subWeek())->count(),
            ],
            'tutorials' => [
                'total' => Tutorial::where('status', 'published')->count(),
                'free' => Tutorial::where('status', 'published')->where('subscription_type', 'free')->count(),
                'premium' => Tutorial::where('status', 'published')->where('subscription_type', 'premium')->count(),
                'pro' => Tutorial::where('status', 'published')->where('subscription_type', 'pro')->count(),
            ],
            'downloads' => [
                'total' => Download::count(),
                'this_month' => Download::whereMonth('downloaded_at', Carbon::now()->month)->count(),
                'this_week' => Download::where('downloaded_at', '>=', Carbon::now()->subWeek())->count(),
            ],
            'categories' => Category::withCount(['tutorials' => function($query) {
                $query->where('status', 'published');
            }])->get()->map(function($category) {
                return [
                    'name' => $category->name,
                    'tutorials_count' => $category->tutorials_count,
                ];
            }),
        ];

        return response()->json($stats);
    }

    /**
     * Get featured tutorials for homepage.
     */
    public function getFeaturedTutorials(): JsonResponse
    {
        $featured = Tutorial::with(['category'])
            ->where('status', 'published')
            ->where('featured', true) // Vous pourriez ajouter ce champ
            ->orWhere(function($query) {
                // Sinon, prendre les plus populaires
                $query->withCount('downloads')
                    ->orderBy('downloads_count', 'desc');
            })
            ->limit(8)
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
                    'estimated_duration' => $tutorial->estimated_duration,
                    'slug' => $tutorial->slug,
                    'is_premium' => $tutorial->subscription_type !== 'free',
                ];
            });

        return response()->json($featured);
    }

    /**
     * Get testimonials for homepage.
     */
    public function getTestimonials(): array
    {
        // Pour l'exemple, testimonials statiques
        // Vous pourriez créer une table testimonials
        return [
            [
                'id' => 1,
                'name' => 'Marie Dubois',
                'role' => 'Consultante Automation',
                'company' => 'TechFlow Solutions',
                'avatar' => '/images/testimonials/marie.jpg',
                'content' => 'Automatehub m\'a permis de maîtriser n8n en quelques semaines. Les tutoriels sont clairs et pratiques.',
                'rating' => 5,
                'featured' => true,
            ],
            [
                'id' => 2,
                'name' => 'Pierre Martin',
                'role' => 'Développeur',
                'company' => 'StartupTech',
                'avatar' => '/images/testimonials/pierre.jpg',
                'content' => 'Excellent contenu pour débuter avec n8n. J\'ai pu automatiser tous nos processus internes.',
                'rating' => 5,
                'featured' => true,
            ],
            [
                'id' => 3,
                'name' => 'Sophie Laurent',
                'role' => 'Chef de projet',
                'company' => 'Digital Agency',
                'avatar' => '/images/testimonials/sophie.jpg',
                'content' => 'La progression par niveaux est parfaite. Du débutant à l\'expert, tout y est !',
                'rating' => 5,
                'featured' => true,
            ],
            [
                'id' => 4,
                'name' => 'Thomas Rousseau',
                'role' => 'Freelance Automation',
                'company' => 'Indépendant',
                'avatar' => '/images/testimonials/thomas.jpg',
                'content' => 'Grâce à Automatehub, j\'ai pu lancer mon activité de consultant en automation.',
                'rating' => 5,
                'featured' => false,
            ],
            [
                'id' => 5,
                'name' => 'Amélie Moreau',
                'role' => 'Responsable IT',
                'company' => 'InnovCorp',
                'avatar' => '/images/testimonials/amelie.jpg',
                'content' => 'Formation complète et professionnelle. Nos équipes sont maintenant autonomes sur n8n.',
                'rating' => 5,
                'featured' => false,
            ],
        ];
    }

    /**
     * Get popular categories with tutorial counts.
     */
    public function getPopularCategories(): JsonResponse
    {
        $categories = Category::withCount(['tutorials' => function($query) {
                $query->where('status', 'published');
            }])
            ->having('tutorials_count', '>', 0)
            ->orderBy('tutorials_count', 'desc')
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'slug' => $category->slug,
                    'icon' => $category->icon,
                    'color' => $category->color,
                    'tutorials_count' => $category->tutorials_count,
                ];
            });

        return response()->json($categories);
    }

    /**
     * Get recent blog posts for homepage.
     */
    public function getRecentBlogPosts(): JsonResponse
    {
        $posts = BlogPost::where('status', 'published')
            ->latest('published_at')
            ->limit(6)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'thumbnail' => $post->thumbnail,
                    'published_at' => $post->published_at,
                    'slug' => $post->slug,
                    'reading_time' => $this->calculateReadingTime($post->content),
                ];
            });

        return response()->json($posts);
    }

    /**
     * Get growth statistics for social proof.
     */
    public function getGrowthStats(): JsonResponse
    {
        $now = Carbon::now();
        
        $stats = [
            'daily_growth' => [
                'users' => User::whereDate('created_at', $now->toDateString())->count(),
                'downloads' => Download::whereDate('downloaded_at', $now->toDateString())->count(),
            ],
            'weekly_growth' => [
                'users' => User::where('created_at', '>=', $now->subWeek())->count(),
                'downloads' => Download::where('downloaded_at', '>=', $now->subWeek())->count(),
            ],
            'monthly_growth' => [
                'users' => User::whereMonth('created_at', $now->month)->count(),
                'downloads' => Download::whereMonth('downloaded_at', $now->month)->count(),
                'tutorials' => Tutorial::where('status', 'published')
                    ->whereMonth('created_at', $now->month)
                    ->count(),
            ],
            'milestones' => [
                'next_user_milestone' => $this->getNextMilestone(User::count()),
                'next_download_milestone' => $this->getNextMilestone(Download::count()),
            ],
        ];

        return response()->json($stats);
    }

    /**
     * Get newsletter signup statistics.
     */
    public function getNewsletterStats(): JsonResponse
    {
        // Vous pourriez avoir une table newsletter_subscribers
        $stats = [
            'total_subscribers' => User::whereNotNull('email_verified_at')->count(),
            'growth_rate' => 15.2, // Pourcentage de croissance mensuelle
            'engagement_rate' => 68.5, // Taux d'engagement moyen
        ];

        return response()->json($stats);
    }

    /**
     * Calculate reading time for blog posts.
     */
    private function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $wordsPerMinute = 200; // Vitesse de lecture moyenne
        
        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Get next milestone for gamification.
     */
    private function getNextMilestone(int $current): int
    {
        $milestones = [100, 250, 500, 1000, 2500, 5000, 10000, 25000, 50000, 100000];
        
        foreach ($milestones as $milestone) {
            if ($current < $milestone) {
                return $milestone;
            }
        }
        
        // Si on dépasse tous les milestones, calculer le prochain multiple de 100k
        return ceil($current / 100000) * 100000 + 100000;
    }

    /**
     * Get call-to-action data for homepage sections.
     */
    public function getCallToActions(): JsonResponse
    {
        $ctas = [
            'hero' => [
                'title' => 'Maîtrisez n8n et automatisez tout !',
                'subtitle' => 'Rejoignez plus de ' . number_format(User::count()) . ' utilisateurs qui automatisent déjà leurs tâches',
                'primary_button' => [
                    'text' => 'Commencer gratuitement',
                    'url' => route('register'),
                    'style' => 'primary',
                ],
                'secondary_button' => [
                    'text' => 'Voir les tutoriels',
                    'url' => route('frontend.tutorials.index'),
                    'style' => 'outline',
                ],
            ],
            'tutorials_section' => [
                'title' => 'Accédez à tous nos tutoriels',
                'subtitle' => 'Plus de ' . Tutorial::where('status', 'published')->count() . ' tutoriels vous attendent',
                'button' => [
                    'text' => 'S\'inscrire maintenant',
                    'url' => route('register'),
                    'style' => 'success',
                ],
            ],
            'premium_section' => [
                'title' => 'Débloquez le contenu premium',
                'subtitle' => 'Tutoriels avancés, support prioritaire et bien plus',
                'button' => [
                    'text' => 'Découvrir Premium',
                    'url' => route('register') . '?plan=premium',
                    'style' => 'warning',
                ],
            ],
            'footer_cta' => [
                'title' => 'Prêt à automatiser ?',
                'subtitle' => 'Rejoignez la communauté Automatehub dès aujourd\'hui',
                'button' => [
                    'text' => 'Inscription gratuite',
                    'url' => route('register'),
                    'style' => 'primary',
                ],
            ],
        ];

        return response()->json($ctas);
    }

    /**
     * Track homepage interactions for analytics.
     */
    public function trackInteraction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|max:100',
            'element' => 'nullable|string|max:100',
            'value' => 'nullable|string|max:255',
        ]);

        $this->analyticsService->trackAnonymous('homepage_interaction', [
            'action' => $request->get('action'),
            'element' => $request->get('element'),
            'value' => $request->get('value'),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
        ]);

        return response()->json(['success' => true]);
    }
}
