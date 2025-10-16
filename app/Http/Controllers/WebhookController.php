<?php

namespace App\Http\Controllers;

use App\Models\ApiWebhookLog;
use App\Models\CreditPurchase;
use App\Models\ExternalMembership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleStripe(Request $request)
    {
        $payload = $request->all();
        
        // Logger le webhook
        $webhookLog = ApiWebhookLog::create([
            'source' => 'stripe',
            'event_type' => $payload['type'] ?? 'unknown',
            'payload' => $payload,
            'status' => 'pending'
        ]);
        
        try {
            // Vérifier la signature Stripe (TODO: implémenter)
            // $this->verifyStripeSignature($request);
            
            switch ($payload['type']) {
                case 'checkout.session.completed':
                    $this->handleStripeCheckoutCompleted($payload['data']['object']);
                    break;
                    
                case 'invoice.payment_succeeded':
                    $this->handleStripeInvoicePaid($payload['data']['object']);
                    break;
                    
                default:
                    Log::info('Unhandled Stripe webhook type: ' . $payload['type']);
            }
            
            $webhookLog->markAsProcessed();
            
            return response()->json(['received' => true]);
            
        } catch (\Exception $e) {
            $webhookLog->markAsFailed($e->getMessage());
            Log::error('Stripe webhook error', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            
            return response()->json(['error' => 'Webhook processing failed'], 400);
        }
    }
    
    private function handleStripeCheckoutCompleted($session)
    {
        // Récupérer l'achat de crédits
        $purchase = CreditPurchase::where('transaction_id', $session['id'])->first();
        
        if ($purchase && $purchase->status === 'pending') {
            $purchase->markAsCompleted();
            
            // Envoyer un email de confirmation
            // TODO: Mail::to($purchase->user)->send(new CreditsPurchased($purchase));
        }
    }
    
    private function handleStripeInvoicePaid($invoice)
    {
        // Gérer les abonnements mensuels
        // TODO: Implémenter la gestion des abonnements récurrents
    }
    
    public function handleSkool(Request $request)
    {
        $payload = $request->all();
        
        // Logger le webhook
        $webhookLog = ApiWebhookLog::create([
            'source' => 'skool',
            'event_type' => $payload['event'] ?? 'unknown',
            'payload' => $payload,
            'status' => 'pending'
        ]);
        
        try {
            $email = $payload['email'] ?? null;
            $event = $payload['event'] ?? null;
            
            if (!$email) {
                throw new \Exception('Email manquant dans le payload');
            }
            
            // Trouver ou créer l'utilisateur
            $user = User::where('email', $email)->first();
            if (!$user) {
                // Créer un compte basique
                $user = User::create([
                    'name' => $payload['name'] ?? explode('@', $email)[0],
                    'email' => $email,
                    'password' => bcrypt(\Str::random(32))
                ]);
            }
            
            // Gérer l'adhésion Skool
            $membership = ExternalMembership::updateOrCreate(
                [
                    'platform' => 'skool',
                    'email' => $email
                ],
                [
                    'user_id' => $user->id,
                    'external_id' => $payload['member_id'] ?? null,
                    'status' => $event === 'member.cancelled' ? 'cancelled' : 'active',
                    'benefits' => [
                        'api_credits' => [
                            'content-extractor' => 100  // 100 crédits/mois pour Content Extractor
                        ]
                    ]
                ]
            );
            
            // Appliquer les bénéfices
            $this->applyMembershipBenefits($membership);
            
            $webhookLog->markAsProcessed();
            
            return response()->json(['received' => true]);
            
        } catch (\Exception $e) {
            $webhookLog->markAsFailed($e->getMessage());
            Log::error('Skool webhook error', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            
            return response()->json(['error' => 'Webhook processing failed'], 400);
        }
    }
    
    private function applyMembershipBenefits(ExternalMembership $membership)
    {
        if (!$membership->isActive()) {
            return;
        }
        
        foreach ($membership->api_credits as $apiSlug => $monthlyCredits) {
            // Trouver l'API service
            $apiService = \App\Models\ApiService::where('slug', $apiSlug)->first();
            if (!$apiService) {
                continue;
            }
            
            // Mettre à jour ou créer la souscription
            $subscription = $membership->user->apiSubscriptions()
                ->where('api_service_id', $apiService->id)
                ->first();
                
            if ($subscription) {
                // Mettre à jour le quota bonus
                $subscription->update([
                    'monthly_quota' => max($subscription->monthly_quota, $monthlyCredits)
                ]);
            } else {
                // Créer une nouvelle souscription gratuite avec quota bonus
                $freePlan = $apiService->getFreePlan();
                if ($freePlan) {
                    $membership->user->apiSubscriptions()->create([
                        'api_service_id' => $apiService->id,
                        'pricing_plan_id' => $freePlan->id,
                        'monthly_quota' => $monthlyCredits,
                        'status' => 'active'
                    ]);
                }
            }
        }
    }
}