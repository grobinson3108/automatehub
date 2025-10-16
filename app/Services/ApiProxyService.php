<?php

namespace App\Services;

use App\Models\UserApiSubscription;
use App\Models\ApiUsageLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiProxyService
{
    /**
     * Proxy une requête vers l'API externe
     */
    public function proxyRequest(
        UserApiSubscription $subscription,
        string $endpoint,
        string $method = 'GET',
        array $data = [],
        int $creditsRequired = 1
    ) {
        // Vérifier les crédits
        if (!$subscription->canMakeRequest($creditsRequired)) {
            return [
                'error' => 'Crédits insuffisants',
                'remaining_credits' => $subscription->remaining_credits,
                'credits_required' => $creditsRequired
            ];
        }
        
        $startTime = microtime(true);
        $apiService = $subscription->apiService;
        
        try {
            // Construire l'URL complète
            $url = rtrim($apiService->endpoint_base, '/') . '/' . ltrim($endpoint, '/');
            
            // Préparer les headers
            $headers = [
                'Authorization' => 'Bearer ' . $this->getInternalApiKey($apiService->slug),
                'X-User-Email' => $subscription->user->email,
                'X-Subscription-Type' => $subscription->pricingPlan ? $subscription->pricingPlan->name : 'free'
            ];
            
            // Faire la requête
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->{$method}($url, $data);
            
            $responseTime = round((microtime(true) - $startTime) * 1000);
            
            // Logger l'utilisation
            $usageLog = ApiUsageLog::create([
                'subscription_id' => $subscription->id,
                'endpoint' => $endpoint,
                'method' => $method,
                'credits_used' => $creditsRequired,
                'response_code' => $response->status(),
                'response_time_ms' => $responseTime,
                'metadata' => [
                    'request_size' => strlen(json_encode($data)),
                    'response_size' => strlen($response->body())
                ]
            ]);
            
            // Déduire les crédits seulement si la requête est réussie
            if ($response->successful()) {
                $subscription->useCredits($creditsRequired);
            }
            
            // Retourner la réponse
            return [
                'success' => true,
                'data' => $response->json() ?? $response->body(),
                'status_code' => $response->status(),
                'credits_used' => $creditsRequired,
                'remaining_credits' => $subscription->fresh()->remaining_credits
            ];
            
        } catch (\Exception $e) {
            Log::error('API Proxy Error', [
                'api' => $apiService->slug,
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            
            // Logger l'échec
            ApiUsageLog::create([
                'subscription_id' => $subscription->id,
                'endpoint' => $endpoint,
                'method' => $method,
                'credits_used' => 0,
                'response_code' => 0,
                'response_time_ms' => round((microtime(true) - $startTime) * 1000),
                'metadata' => [
                    'error' => $e->getMessage()
                ]
            ]);
            
            return [
                'error' => 'Erreur lors de l\'appel à l\'API',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Récupérer la clé API interne pour un service
     */
    private function getInternalApiKey(string $apiSlug): string
    {
        // Les clés internes sont stockées dans .env
        $envKey = 'API_KEY_' . strtoupper(str_replace('-', '_', $apiSlug));
        return env($envKey, '');
    }
    
    /**
     * Vérifier la validité d'une clé API
     */
    public function validateApiKey(string $apiKey): ?UserApiSubscription
    {
        return UserApiSubscription::where('api_key', $apiKey)
            ->where('status', 'active')
            ->with(['user', 'apiService', 'pricingPlan'])
            ->first();
    }
    
    /**
     * Calculer les crédits nécessaires pour une requête
     */
    public function calculateCreditsRequired(string $apiSlug, string $endpoint, array $data = []): int
    {
        // Logique spécifique par API
        switch ($apiSlug) {
            case 'content-extractor':
                // 1 crédit par URL pour YouTube ou web
                return count($data['urls'] ?? [$data['url'] ?? null]);
                
            case 'image-generator':
                // Basé sur la résolution
                $resolution = $data['resolution'] ?? '512x512';
                if (str_contains($resolution, '1024')) {
                    return 3;
                } elseif (str_contains($resolution, '768')) {
                    return 2;
                }
                return 1;
                
            default:
                return 1;
        }
    }
}