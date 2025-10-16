<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsPremium
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has premium or pro subscription
        if (!$request->user() || 
            !in_array($request->user()->subscription_type, ['premium', 'pro'])) {
            // If not premium, redirect to subscription page with error message
            return redirect()->route('user.subscription.index')->with('error', 'Cette fonctionnalité nécessite un abonnement Premium ou Pro.');
        }

        return $next($request);
    }
}
