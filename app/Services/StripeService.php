<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Pack;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe Checkout Session for a pack purchase.
     */
    public function createCheckoutSession(Pack $pack, string $currency = 'EUR', ?string $customerEmail = null): Session
    {
        $amount = $currency === 'USD'
            ? (int) ($pack->price_usd * 100)
            : (int) ($pack->price_eur * 100);

        $sessionData = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($currency),
                    'product_data' => [
                        'name' => $pack->name,
                        'description' => $pack->tagline,
                        'metadata' => [
                            'pack_id' => $pack->id,
                            'pack_slug' => $pack->slug,
                        ],
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'metadata' => [
                'pack_id' => $pack->id,
                'currency' => $currency,
            ],
        ];

        // Add customer email if provided
        if ($customerEmail) {
            $sessionData['customer_email'] = $customerEmail;
        }

        // Create the session
        $session = Session::create($sessionData);

        // Create pending order in database
        Order::create([
            'pack_id' => $pack->id,
            'stripe_session_id' => $session->id,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
            'customer_email' => $customerEmail ?? '',
        ]);

        return $session;
    }

    /**
     * Handle Stripe webhook events.
     */
    public function handleWebhook(string $payload, string $signature): void
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );
        } catch (SignatureVerificationException $e) {
            throw new \Exception('Invalid webhook signature');
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            case 'charge.refunded':
                $this->handleChargeRefunded($event->data->object);
                break;
        }
    }

    /**
     * Handle successful checkout session.
     */
    protected function handleCheckoutSessionCompleted($session): void
    {
        $order = Order::where('stripe_session_id', $session->id)->first();

        if (!$order) {
            return;
        }

        // Update order with payment intent and customer info
        $order->update([
            'stripe_payment_intent_id' => $session->payment_intent ?? null,
            'stripe_customer_id' => $session->customer ?? null,
            'customer_email' => $session->customer_email ?? $order->customer_email,
            'customer_name' => $session->customer_details->name ?? null,
        ]);

        // Mark as completed if payment was successful
        if ($session->payment_status === 'paid') {
            $order->markAsCompleted();

            // TODO: Send confirmation email
            // TODO: Trigger workflow delivery
        }
    }

    /**
     * Handle successful payment intent.
     */
    protected function handlePaymentIntentSucceeded($paymentIntent): void
    {
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if (!$order) {
            return;
        }

        $order->markAsCompleted();
    }

    /**
     * Handle failed payment intent.
     */
    protected function handlePaymentIntentFailed($paymentIntent): void
    {
        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if (!$order) {
            return;
        }

        $order->markAsFailed();
    }

    /**
     * Handle refunded charge.
     */
    protected function handleChargeRefunded($charge): void
    {
        $order = Order::where('stripe_payment_intent_id', $charge->payment_intent)->first();

        if (!$order) {
            return;
        }

        $order->markAsRefunded();
    }

    /**
     * Get order by session ID.
     */
    public function getOrderBySessionId(string $sessionId): ?Order
    {
        return Order::where('stripe_session_id', $sessionId)->first();
    }

    /**
     * Convert EUR to USD.
     */
    public function convertEurToUsd(float $amountEur): float
    {
        $rate = config('services.stripe.usd_rate', 1.10);
        return round($amountEur * $rate, 2);
    }

    /**
     * Convert USD to EUR.
     */
    public function convertUsdToEur(float $amountUsd): float
    {
        $rate = config('services.stripe.usd_rate', 1.10);
        return round($amountUsd / $rate, 2);
    }
}
