<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'n8n_id',
        'name',
        'description',
        'nodes',
        'connections',
        'tags',
        'active',
        'is_template',
        'user_id',
        'category_id',
        'difficulty_level',
        'download_count',
        'rating',
        'metadata',
        'last_synced_at'
    ];

    protected $casts = [
        'nodes' => 'array',
        'connections' => 'array',
        'tags' => 'array',
        'active' => 'boolean',
        'is_template' => 'boolean',
        'download_count' => 'integer',
        'rating' => 'decimal:2',
        'metadata' => 'array',
        'last_synced_at' => 'datetime'
    ];

    /**
     * Get the user who created this workflow
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of this workflow
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get downloads for this workflow
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    /**
     * Scope for active workflows
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope for template workflows
     */
    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    /**
     * Scope for user workflows
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope by difficulty level
     */
    public function scopeByDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Get workflows by tags
     */
    public function scopeByTags($query, array $tags)
    {
        return $query->whereJsonContains('tags', $tags);
    }

    /**
     * Search workflows by name or description
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', '%' . $term . '%')
              ->orWhere('description', 'like', '%' . $term . '%');
        });
    }

    /**
     * Get popular workflows (by download count)
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('download_count', 'desc')->limit($limit);
    }

    /**
     * Get top rated workflows
     */
    public function scopeTopRated($query, $limit = 10)
    {
        return $query->whereNotNull('rating')
                    ->orderBy('rating', 'desc')
                    ->limit($limit);
    }

    /**
     * Get recently updated workflows
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('updated_at', 'desc')->limit($limit);
    }

    /**
     * Increment download count
     */
    public function incrementDownloads()
    {
        $this->increment('download_count');
    }

    /**
     * Update rating
     */
    public function updateRating($newRating)
    {
        // Simple average for now - could be enhanced with weighted ratings
        $this->rating = $newRating;
        $this->save();
    }

    /**
     * Check if workflow needs sync with n8n
     */
    public function needsSync(): bool
    {
        if (!$this->last_synced_at) {
            return true;
        }
        
        // Consider workflow needs sync if not synced in last hour
        return $this->last_synced_at->addHour()->isPast();
    }

    /**
     * Mark as synced
     */
    public function markAsSynced()
    {
        $this->update(['last_synced_at' => now()]);
    }

    /**
     * Get workflow export filename
     */
    public function getExportFilename(): string
    {
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->name);
        return "workflow_{$this->n8n_id}_{$safeName}_" . now()->format('Y-m-d_H-i-s') . '.json';
    }

    /**
     * Get workflow summary for API responses
     */
    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'n8n_id' => $this->n8n_id,
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
            'is_template' => $this->is_template,
            'difficulty_level' => $this->difficulty_level,
            'download_count' => $this->download_count,
            'rating' => $this->rating,
            'tags' => $this->tags,
            'category' => $this->category?->name,
            'user' => $this->user?->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    /**
     * Get full workflow data for export
     */
    public function getFullData(): array
    {
        return [
            'id' => $this->n8n_id,
            'name' => $this->name,
            'nodes' => $this->nodes,
            'connections' => $this->connections,
            'active' => $this->active,
            'tags' => $this->tags,
            'createdAt' => $this->created_at->toISOString(),
            'updatedAt' => $this->updated_at->toISOString()
        ];
    }
}