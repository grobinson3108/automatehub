<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BlogApiController extends Controller
{
    /**
     * Store a new blog post from n8n workflow.
     *
     * Expected payload:
     * {
     *   "api_key": "n8n_api_key_...",
     *   "title": "Article Title",
     *   "content": "<html>Article content</html>",
     *   "image_url": "https://...",
     *   "article_data": {
     *     "meta_tags": [...],
     *     "schema_org": {...},
     *     "faq": [...],
     *     "cta": {...},
     *     "seo_title": "...",
     *     "seo_description": "..."
     *   }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate API key
            if (!$this->validateApiKey($request->input('api_key'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API key'
                ], 401);
            }

            // Validate required fields
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image_url' => 'nullable|url',
                'article_data' => 'nullable|array',
                'excerpt' => 'nullable|string',
                'created_by' => 'nullable|exists:users,id',
            ]);

            // Generate slug from title
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $counter = 1;

            // Ensure unique slug
            while (BlogPost::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Extract excerpt from content if not provided
            if (empty($validated['excerpt'])) {
                $validated['excerpt'] = $this->createExcerpt($validated['content'], 160);
            }

            // Get default creator (first admin or system user)
            if (empty($validated['created_by'])) {
                $validated['created_by'] = User::where('is_admin', true)->first()?->id ?? 1;
            }

            // Create the blog post
            $post = BlogPost::create([
                'title' => $validated['title'],
                'slug' => $slug,
                'content' => $validated['content'],
                'excerpt' => $validated['excerpt'],
                'featured_image' => $validated['image_url'] ?? null,
                'created_by' => $validated['created_by'],
                'article_data' => $validated['article_data'] ?? null,
                'is_published' => true,
                'published_at' => now(),
            ]);

            Log::info('Blog post created via n8n API', [
                'post_id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'has_article_data' => !empty($validated['article_data']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Blog post created successfully',
                'data' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'url' => route('blog.show', $post->slug),
                    'published_at' => $post->published_at->toIso8601String(),
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error creating blog post via API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating blog post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing blog post.
     */
    public function update(Request $request, $slug): JsonResponse
    {
        try {
            // Validate API key
            if (!$this->validateApiKey($request->input('api_key'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API key'
                ], 401);
            }

            $post = BlogPost::where('slug', $slug)->firstOrFail();

            // Validate fields
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'content' => 'sometimes|string',
                'image_url' => 'nullable|url',
                'article_data' => 'nullable|array',
                'excerpt' => 'nullable|string',
            ]);

            // Update slug if title changed
            if (isset($validated['title']) && $validated['title'] !== $post->title) {
                $newSlug = Str::slug($validated['title']);
                $originalSlug = $newSlug;
                $counter = 1;

                while (BlogPost::where('slug', $newSlug)->where('id', '!=', $post->id)->exists()) {
                    $newSlug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                $validated['slug'] = $newSlug;
            }

            // Update featured image
            if (isset($validated['image_url'])) {
                $validated['featured_image'] = $validated['image_url'];
                unset($validated['image_url']);
            }

            $post->update($validated);

            Log::info('Blog post updated via n8n API', [
                'post_id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Blog post updated successfully',
                'data' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'url' => route('blog.show', $post->slug),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating blog post via API', [
                'error' => $e->getMessage(),
                'slug' => $slug
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating blog post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate the API key from n8n.
     */
    private function validateApiKey(?string $apiKey): bool
    {
        if (empty($apiKey)) {
            return false;
        }

        // Check against environment variable or config
        $validKey = config('services.n8n.api_key', env('N8N_API_KEY'));

        return $apiKey === $validKey;
    }

    /**
     * Create excerpt from HTML content.
     */
    private function createExcerpt(string $content, int $length = 160): string
    {
        $text = strip_tags($content);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . '...';
    }
}
