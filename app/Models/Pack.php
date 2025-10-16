<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pack extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'marketing_title',
        'tagline',
        'price_eur',
        'price_usd',
        'original_price_eur',
        'original_price_usd',
        'description',
        'workflows_count',
        'complexity',
        'category',
        'features',
        'benefits',
        'requirements',
        'folder_path',
        'is_active',
        'is_featured',
        'sales_count',
        'views_count',
    ];

    protected $casts = [
        'features' => 'array',
        'benefits' => 'array',
        'requirements' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price_eur' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'original_price_eur' => 'decimal:2',
        'original_price_usd' => 'decimal:2',
    ];

    /**
     * Get all workflows in this pack
     */
    public function getWorkflowsAttribute()
    {
        $packPath = storage_path('app/packs/' . $this->folder_path);

        if (!is_dir($packPath)) {
            return [];
        }

        $workflows = [];
        $files = glob($packPath . '/*.json');

        foreach ($files as $file) {
            $content = json_decode(file_get_contents($file), true);
            $workflows[] = [
                'filename' => basename($file),
                'name' => $content['name'] ?? 'Workflow',
                'nodes' => count($content['nodes'] ?? []),
                'connections' => count($content['connections'] ?? []),
            ];
        }

        return $workflows;
    }

    /**
     * Get pricing based on currency
     */
    public function getPrice($currency = 'EUR')
    {
        return $currency === 'USD' ? $this->price_usd : $this->price_eur;
    }

    /**
     * Get original pricing based on currency
     */
    public function getOriginalPrice($currency = 'EUR')
    {
        return $currency === 'USD' ? $this->original_price_usd : $this->original_price_eur;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentage()
    {
        if (!$this->original_price_eur || $this->original_price_eur <= $this->price_eur) {
            return 0;
        }

        return round((($this->original_price_eur - $this->price_eur) / $this->original_price_eur) * 100);
    }

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Increment sales count
     */
    public function incrementSales()
    {
        $this->increment('sales_count');
    }

    /**
     * Scope for active packs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured packs
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
