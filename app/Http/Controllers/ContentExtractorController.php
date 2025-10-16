<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ContentExtractorController extends Controller
{
    /**
     * Dashboard des crédits Content Extractor
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur a un compte Content Extractor
        $extractorAccount = DB::connection('sqlite')
            ->table('user_quotas')
            ->where('email', $user->email)
            ->first();
        
        if (!$extractorAccount) {
            // Créer automatiquement un compte
            $this->createExtractorAccount($user);
            $extractorAccount = DB::connection('sqlite')
                ->table('user_quotas')
                ->where('email', $user->email)
                ->first();
        }
        
        // Récupérer la clé API
        $apiKey = DB::connection('sqlite')
            ->table('api_keys')
            ->where('user_id', $extractorAccount->user_id)
            ->where('is_active', true)
            ->first();
        
        return view('content-extractor.dashboard', [
            'account' => $extractorAccount,
            'apiKey' => $apiKey->api_key ?? null,
            'stripeLinks' => $this->getStripePaymentLinks()
        ]);
    }
    
    /**
     * Créer un compte Content Extractor pour un utilisateur Laravel
     */
    private function createExtractorAccount($user)
    {
        $userId = substr(hash('sha256', $user->email), 0, 16);
        $apiKey = Str::random(32);
        
        // Déterminer le type d'abonnement
        $subscriptionType = 'free';
        $monthlyQuota = 10;
        
        // Vérifier si c'est un abonné Skool
        if ($this->isSkoolMember($user->email)) {
            $subscriptionType = 'skool';
            $monthlyQuota = 100;
        }
        
        // Créer l'utilisateur dans SQLite
        DB::connection('sqlite')->table('user_quotas')->insert([
            'user_id' => $userId,
            'email' => $user->email,
            'subscription_type' => $subscriptionType,
            'monthly_quota' => $monthlyQuota,
            'reset_date' => now()->format('Y-m-d'),
            'created_at' => now()
        ]);
        
        // Créer la clé API
        DB::connection('sqlite')->table('api_keys')->insert([
            'api_key' => $apiKey,
            'user_id' => $userId,
            'is_active' => true,
            'created_at' => now()
        ]);
    }
    
    /**
     * Vérifier si l'email est membre Skool
     */
    private function isSkoolMember($email)
    {
        // TODO: Implémenter la vérification via l'API Skool
        // Pour l'instant, on peut utiliser une table de synchronisation
        return DB::table('skool_members')
            ->where('email', $email)
            ->where('status', 'active')
            ->exists();
    }
    
    /**
     * Obtenir les liens de paiement Stripe
     */
    private function getStripePaymentLinks()
    {
        return [
            'pack_100' => [
                'name' => '100 crédits',
                'price' => '5€',
                'link' => 'https://buy.stripe.com/xxx' // Remplacer par votre lien
            ],
            'pack_500' => [
                'name' => '500 crédits',
                'price' => '20€',
                'link' => 'https://buy.stripe.com/yyy'
            ],
            'pack_1000' => [
                'name' => '1000 crédits',
                'price' => '35€',
                'link' => 'https://buy.stripe.com/zzz'
            ]
        ];
    }
    
    /**
     * Webhook Stripe pour ajouter des crédits après paiement
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->all();
        $sig = $request->header('Stripe-Signature');
        
        // Vérifier la signature Stripe
        try {
            $event = \Stripe\Webhook::constructEvent(
                $request->getContent(),
                $sig,
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            return response('Invalid signature', 400);
        }
        
        // Traiter l'événement
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $email = $session->customer_details->email;
            $productId = $session->metadata->product_id;
            
            // Ajouter les crédits
            $credits = match($productId) {
                'pack_100' => 100,
                'pack_500' => 500,
                'pack_1000' => 1000,
                default => 0
            };
            
            if ($credits > 0) {
                DB::connection('sqlite')
                    ->table('user_quotas')
                    ->where('email', $email)
                    ->increment('extra_credits', $credits);
            }
        }
        
        return response('OK', 200);
    }
}