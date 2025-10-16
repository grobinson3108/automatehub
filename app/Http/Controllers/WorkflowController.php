<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\Models\Download;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class WorkflowController extends Controller
{
    /**
     * Display listing of workflows
     */
    public function index(Request $request)
    {
        $query = Workflow::query();

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by access level
        if ($request->has('access')) {
            if ($request->access === 'freemium') {
                $query->where('is_premium', false);
            } else {
                $query->where('is_premium', true);
            }
        }

        // Search
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $workflows = $query->with('category')->get();
        $categories = Workflow::distinct('category')->pluck('category')->toArray();

        return \Inertia\Inertia::render('Workflows', [
            'workflows' => $workflows,
            'categories' => $categories,
        ]);
    }
    
    /**
     * Show single workflow
     */
    public function show(Workflow $workflow)
    {
        // Check if user has access
        if ($workflow->is_premium && !$this->userHasAccess($workflow)) {
            return view('workflows.premium-required', compact('workflow'));
        }
        
        return view('workflows.show', compact('workflow'));
    }
    
    /**
     * Download workflow JSON
     */
    public function download(Workflow $workflow)
    {
        // Check access
        if ($workflow->is_premium && !$this->userHasAccess($workflow)) {
            return redirect()->route('pricing')
                ->with('error', 'Ce workflow nécessite un abonnement premium.');
        }
        
        // Log download
        Download::create([
            'user_id' => Auth::id(),
            'workflow_id' => $workflow->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        
        // Increment download counter
        $workflow->increment('download_count');
        
        // Generate filename
        $filename = Str::slug($workflow->name) . '-' . date('Y-m-d') . '.json';
        
        // Return JSON file
        return response()->json($workflow->json_data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    /**
     * Get n8n import URL
     */
    public function getImportUrl(Workflow $workflow)
    {
        // Check access
        if ($workflow->is_premium && !$this->userHasAccess($workflow)) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }
        
        // Generate temporary signed URL (valid for 1 hour)
        $temporaryUrl = URL::temporarySignedRoute(
            'workflows.download', 
            now()->addHour(), 
            ['workflow' => $workflow->id]
        );
        
        return response()->json([
            'import_url' => $temporaryUrl,
            'n8n_url' => env('N8N_URL') . '/workflow/new?importUrl=' . urlencode($temporaryUrl)
        ]);
    }
    
    /**
     * Check if user has access to premium workflow
     */
    private function userHasAccess(Workflow $workflow)
    {
        if (!$workflow->is_premium) {
            return true;
        }
        
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Check subscription
        if (in_array($user->subscription_type, ['premium', 'pro'])) {
            if ($user->subscription_expires_at && $user->subscription_expires_at->isFuture()) {
                return true;
            }
        }
        
        // Check one-time purchases
        return $user->purchases()
            ->where('workflow_id', $workflow->id)
            ->where('status', 'completed')
            ->exists();
    }
    
    /**
     * Preview workflow (for logged-in users)
     */
    public function preview(Workflow $workflow)
    {
        // Basic info visible to all logged-in users
        $previewData = [
            'id' => $workflow->id,
            'name' => $workflow->name,
            'description' => $workflow->description,
            'category' => $workflow->category,
            'is_premium' => $workflow->is_premium,
            'node_count' => count($workflow->json_data['nodes'] ?? []),
            'tags' => $workflow->tags,
            'download_count' => $workflow->download_count,
            'created_at' => $workflow->created_at,
        ];
        
        // Add node types for preview
        if (isset($workflow->json_data['nodes'])) {
            $nodeTypes = collect($workflow->json_data['nodes'])
                ->pluck('type')
                ->unique()
                ->values();
            $previewData['node_types'] = $nodeTypes;
        }
        
        return response()->json($previewData);
    }
}