<?php

namespace App\Http\Controllers;

use App\Services\ApiProxyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiProxyController extends Controller
{
    private ApiProxyService $proxyService;
    
    public function __construct(ApiProxyService $proxyService)
    {
        $this->proxyService = $proxyService;
    }
    
    public function handle(Request $request, string $api, string $endpoint): JsonResponse
    {
        // Extraire la clé API du header Authorization
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'error' => 'Token d\'authentification manquant',
                'message' => 'Veuillez fournir un header Authorization: Bearer YOUR_API_KEY'
            ], 401);
        }
        
        $apiKey = substr($authHeader, 7);
        
        // Valider la clé API
        $subscription = $this->proxyService->validateApiKey($apiKey);
        if (!$subscription) {
            return response()->json([
                'error' => 'Clé API invalide',
                'message' => 'La clé API fournie n\'existe pas ou n\'est pas active'
            ], 401);
        }
        
        // Vérifier que l'API correspond
        if ($subscription->apiService->slug !== $api) {
            return response()->json([
                'error' => 'API non autorisée',
                'message' => 'Cette clé API n\'est pas valide pour l\'API ' . $api
            ], 403);
        }
        
        // Calculer les crédits requis
        $data = $request->method() === 'GET' ? $request->query() : $request->all();
        $creditsRequired = $this->proxyService->calculateCreditsRequired(
            $api,
            $endpoint,
            $data
        );
        
        // Proxifier la requête
        $result = $this->proxyService->proxyRequest(
            $subscription,
            $endpoint,
            $request->method(),
            $data,
            $creditsRequired
        );
        
        // Retourner la réponse
        if (isset($result['error'])) {
            $statusCode = $result['status_code'] ?? 400;
            return response()->json([
                'error' => $result['error'],
                'message' => $result['message'] ?? null,
                'remaining_credits' => $result['remaining_credits'] ?? null,
                'credits_required' => $result['credits_required'] ?? null
            ], $statusCode);
        }
        
        // Ajouter les informations de crédits dans les headers
        return response()->json($result['data'])
            ->header('X-Credits-Used', $result['credits_used'])
            ->header('X-Credits-Remaining', $result['remaining_credits'])
            ->setStatusCode($result['status_code']);
    }
}