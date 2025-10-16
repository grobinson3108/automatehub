<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackController extends Controller
{
    /**
     * Display all packs
     */
    public function index(Request $request)
    {
        $query = Pack::active();

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Sort
        $sort = $request->get('sort', 'featured');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price_eur', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price_eur', 'desc');
                break;
            case 'popular':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'featured':
            default:
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('sales_count', 'desc');
                break;
        }

        $packs = $query->paginate(12);
        $categories = Pack::distinct('category')->pluck('category')->toArray();

        return \Inertia\Inertia::render('Packs/Index', [
            'packs' => $packs,
            'categories' => $categories,
            'currentCategory' => $request->get('category'),
            'currentSort' => $sort,
        ]);
    }

    /**
     * Display pack landing page
     */
    public function show($slug)
    {
        $pack = Pack::where('slug', $slug)->active()->firstOrFail();

        // Increment views
        $pack->incrementViews();

        // Detect currency based on user location (can be enhanced later)
        $currency = $this->detectCurrency();

        // Get workflows from pack folder
        $workflows = $this->getPackWorkflows($pack);

        // Get related packs
        $relatedPacks = Pack::where('category', $pack->category)
            ->where('id', '!=', $pack->id)
            ->active()
            ->limit(3)
            ->get();

        return \Inertia\Inertia::render('Packs/Show', [
            'pack' => $pack,
            'currency' => $currency,
            'workflows' => $workflows,
            'relatedPacks' => $relatedPacks,
        ]);
    }

    /**
     * Get workflows from pack folder
     */
    private function getPackWorkflows(Pack $pack)
    {
        $packPath = base_path($pack->folder_path);

        if (!is_dir($packPath)) {
            return [];
        }

        $workflows = [];
        $files = glob($packPath . '/*.json');

        foreach ($files as $file) {
            try {
                $content = json_decode(file_get_contents($file), true);
                if ($content) {
                    $workflows[] = [
                        'filename' => basename($file),
                        'name' => $content['name'] ?? 'Workflow',
                        'nodes' => count($content['nodes'] ?? []),
                        'connections' => count($content['connections'] ?? []),
                        'complexity' => $this->calculateComplexity(count($content['nodes'] ?? [])),
                    ];
                }
            } catch (\Exception $e) {
                // Skip invalid JSON files
                continue;
            }
        }

        return $workflows;
    }

    /**
     * Calculate workflow complexity based on node count
     */
    private function calculateComplexity($nodeCount)
    {
        if ($nodeCount <= 5) {
            return 'Simple';
        } elseif ($nodeCount <= 15) {
            return 'Intermédiaire';
        } else {
            return 'Avancé';
        }
    }

    /**
     * Detect user currency (basic implementation)
     */
    private function detectCurrency()
    {
        // TODO: Enhance with GeoIP detection
        // For now, default to EUR
        return 'EUR';
    }

    /**
     * Create Stripe Checkout Session
     */
    public function checkout(Request $request, $slug)
    {
        $pack = Pack::where('slug', $slug)->active()->firstOrFail();

        // TODO: Implement Stripe checkout
        // This will be done in next step

        return redirect()->route('packs.show', $slug)
            ->with('info', 'Stripe integration coming soon!');
    }
}
