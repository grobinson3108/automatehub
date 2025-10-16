<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Tutorial;
use App\Models\BlogPost;
use App\Models\Category;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Page d'accueil
        $sitemap .= $this->createUrl(route('home'), now(), 'daily', '1.0');
        
        // Pages statiques importantes
        $sitemap .= $this->createUrl(route('pricing'), now()->subDays(7), 'weekly', '0.9');
        $sitemap .= $this->createUrl(route('about'), now()->subDays(7), 'monthly', '0.8');
        $sitemap .= $this->createUrl(route('tutorials'), now()->subDays(1), 'daily', '0.9');
        $sitemap .= $this->createUrl(route('downloads'), now()->subDays(7), 'weekly', '0.7');
        $sitemap .= $this->createUrl(route('contact'), now()->subDays(30), 'monthly', '0.6');
        
        // Pages légales
        $sitemap .= $this->createUrl(route('privacy-policy'), now()->subDays(30), 'monthly', '0.5');
        $sitemap .= $this->createUrl(route('legal'), now()->subDays(30), 'monthly', '0.5');

        // Tutoriels (si publiés)
        $tutorials = Tutorial::where('is_published', true)->get();
        foreach ($tutorials as $tutorial) {
            $sitemap .= $this->createUrl(
                route('tutorials.show', $tutorial->slug),
                $tutorial->updated_at,
                'weekly',
                '0.8'
            );
        }

        // Catégories
        $categories = Category::whereHas('tutorials', function($query) {
            $query->where('is_published', true);
        })->get();
        foreach ($categories as $category) {
            $sitemap .= $this->createUrl(
                route('categories.show', $category->slug),
                $category->updated_at,
                'weekly',
                '0.7'
            );
        }

        // Articles de blog (si publiés)
        $posts = BlogPost::where('is_published', true)->get();
        foreach ($posts as $post) {
            $sitemap .= $this->createUrl(
                route('blog.show', $post->slug),
                $post->updated_at,
                'weekly',
                '0.7'
            );
        }

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    private function createUrl($loc, $lastmod, $changefreq = 'weekly', $priority = '0.5')
    {
        return sprintf(
            '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%s</priority></url>',
            htmlspecialchars($loc),
            $lastmod->format('Y-m-d'),
            $changefreq,
            $priority
        );
    }

    public function robots()
    {
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /user/\n";
        $robots .= "Disallow: /api/\n";
        $robots .= "Disallow: /storage/\n";
        $robots .= "Disallow: /*.pdf$\n\n";
        
        $robots .= "Sitemap: " . url('/sitemap.xml') . "\n\n";
        
        // Crawl-delay pour éviter surcharge
        $robots .= "Crawl-delay: 1\n";

        return response($robots, 200)
            ->header('Content-Type', 'text/plain');
    }
}