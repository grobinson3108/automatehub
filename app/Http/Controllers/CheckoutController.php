<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use App\Models\Order;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function __construct(
        protected StripeService $stripeService
    ) {}

    /**
     * Create a Stripe Checkout Session.
     */
    public function createSession(Request $request, Pack $pack)
    {
        $validated = $request->validate([
            'currency' => 'required|in:EUR,USD',
            'email' => 'nullable|email',
        ]);

        $currency = $validated['currency'];
        $email = $validated['email'] ?? auth()->user()?->email ?? null;

        try {
            $session = $this->stripeService->createCheckoutSession($pack, $currency, $email);

            return response()->json([
                'sessionId' => $session->id,
                'url' => $session->url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue lors de la création de la session de paiement.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle successful payment.
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('packs.index')->with('error', 'Session invalide');
        }

        $order = $this->stripeService->getOrderBySessionId($sessionId);

        if (!$order) {
            return redirect()->route('packs.index')->with('error', 'Commande introuvable');
        }

        return Inertia::render('Checkout/Success', [
            'order' => $order->load('pack'),
        ]);
    }

    /**
     * Handle cancelled payment.
     */
    public function cancel()
    {
        return Inertia::render('Checkout/Cancel');
    }

    /**
     * Handle Stripe webhooks.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $this->stripeService->handleWebhook($payload, $signature);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Download pack workflows (with security checks).
     */
    public function download(Request $request, string $sessionId)
    {
        $order = $this->stripeService->getOrderBySessionId($sessionId);

        if (!$order) {
            abort(404, 'Commande introuvable');
        }

        if (!$order->canDownload()) {
            $message = 'Téléchargement non disponible. ';

            if ($order->download_count >= $order->max_downloads) {
                $message .= 'Limite de téléchargements atteinte (3 maximum).';
            } elseif ($order->expires_at && $order->expires_at->isPast()) {
                $message .= 'Accès expiré (30 jours maximum après l\'achat).';
            } elseif (!$order->isCompleted()) {
                $message .= 'Paiement non finalisé.';
            }

            abort(403, $message);
        }

        // Increment download counter
        $order->incrementDownload();

        // TODO: Generate watermarked ZIP with workflows
        // TODO: Add customer email to workflow metadata
        // TODO: Return the ZIP file

        return response()->json([
            'message' => 'Téléchargement en cours...',
            'remaining_downloads' => $order->remaining_downloads,
            'expires_at' => $order->expires_at?->format('d/m/Y'),
        ]);
    }

    /**
     * Show customer orders by email.
     */
    public function myOrders(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $orders = Order::byEmail($validated['email'])
            ->completed()
            ->with('pack')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Checkout/MyOrders', [
            'orders' => $orders,
            'email' => $validated['email'],
        ]);
    }
}
