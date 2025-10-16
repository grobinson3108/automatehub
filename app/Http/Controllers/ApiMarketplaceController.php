<?php

namespace App\Http\Controllers;

use App\Models\ApiService;
use App\Models\UserApiSubscription;
use App\Models\CreditPack;
use App\Models\ApiUsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiMarketplaceController extends Controller
{
    public function index()
    {
        $apiServices = ApiService::with('pricingPlans')
            ->where('is_active', true)
            ->get();
            
        $userSubscriptions = Auth::user()->apiSubscriptions()
            ->with(['apiService', 'pricingPlan'])
            ->where('status', 'active')
            ->get()
            ->keyBy('api_service_id');
            
        return view('api-marketplace.index', compact('apiServices', 'userSubscriptions'));
    }
    
    public function show(string $slug)
    {
        $apiService = ApiService::where('slug', $slug)
            ->with(['pricingPlans' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->firstOrFail();
            
        $subscription = Auth::user()->apiSubscriptions()
            ->where('api_service_id', $apiService->id)
            ->with('pricingPlan')
            ->first();
            
        $creditPacks = $apiService->creditPacks()
            ->where('is_active', true)
            ->orderBy('credits')
            ->get();
            
        // Statistiques d'utilisation
        $usageStats = null;
        if ($subscription) {
            $usageStats = [
                'today' => $subscription->usageLogs()->today()->sum('credits_used'),
                'this_week' => $subscription->usageLogs()
                    ->whereBetween('created_at', [now()->startOfWeek(), now()])
                    ->sum('credits_used'),
                'this_month' => $subscription->used_this_month,
                'success_rate' => $this->calculateSuccessRate($subscription)
            ];
        }
            
        return view('api-marketplace.show', compact(
            'apiService', 
            'subscription', 
            'creditPacks',
            'usageStats'
        ));
    }
    
    public function subscribe(Request $request, string $slug)
    {
        $request->validate([
            'pricing_plan_id' => 'required|exists:api_pricing_plans,id'
        ]);
        
        $apiService = ApiService::where('slug', $slug)->firstOrFail();
        $pricingPlan = $apiService->pricingPlans()->findOrFail($request->pricing_plan_id);
        
        // Vérifier si l'utilisateur a déjà une souscription
        $existingSubscription = Auth::user()->apiSubscriptions()
            ->where('api_service_id', $apiService->id)
            ->first();
            
        if ($existingSubscription) {
            // Mettre à jour le plan
            $existingSubscription->update([
                'pricing_plan_id' => $pricingPlan->id,
                'monthly_quota' => $pricingPlan->monthly_quota,
                'status' => 'active'
            ]);
            
            $subscription = $existingSubscription;
        } else {
            // Créer une nouvelle souscription
            $subscription = Auth::user()->apiSubscriptions()->create([
                'api_service_id' => $apiService->id,
                'pricing_plan_id' => $pricingPlan->id,
                'monthly_quota' => $pricingPlan->monthly_quota,
                'status' => 'active',
                'trial_ends_at' => $pricingPlan->isFree() ? null : now()->addDays(7)
            ]);
        }
        
        // Si c'est un plan payant, rediriger vers Stripe
        if (!$pricingPlan->isFree()) {
            // TODO: Implémenter la redirection Stripe
            return redirect()->route('api-marketplace.payment', [
                'slug' => $slug,
                'subscription' => $subscription->id
            ]);
        }
        
        return redirect()->route('api-marketplace.show', $slug)
            ->with('success', 'Souscription activée avec succès !');
    }
    
    public function dashboard()
    {
        $subscriptions = Auth::user()->apiSubscriptions()
            ->with(['apiService', 'pricingPlan'])
            ->where('status', 'active')
            ->get();
            
        // Statistiques globales
        $totalCreditsUsed = $subscriptions->sum('used_this_month');
        $totalCreditsAvailable = $subscriptions->sum(function ($sub) {
            return $sub->monthly_quota + $sub->extra_credits - $sub->used_this_month;
        });
        
        // Activité récente
        $recentActivity = ApiUsageLog::whereIn(
                'subscription_id', 
                $subscriptions->pluck('id')
            )
            ->with('subscription.apiService')
            ->latest('created_at')
            ->limit(10)
            ->get();
            
        return view('api-marketplace.dashboard', compact(
            'subscriptions',
            'totalCreditsUsed',
            'totalCreditsAvailable',
            'recentActivity'
        ));
    }
    
    private function calculateSuccessRate(UserApiSubscription $subscription): float
    {
        $total = $subscription->usageLogs()->count();
        if ($total === 0) {
            return 100.0;
        }
        
        $successful = $subscription->usageLogs()->successful()->count();
        return round(($successful / $total) * 100, 1);
    }
}