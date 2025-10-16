<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class CheckOnboarding
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if ($user && (!$user->onboarding_completed || !$user->level_n8n)) {
            View::share('showOnboarding', true);
            View::share('onboardingStep', !$user->level_n8n ? 'level' : 'preferences');
        }
        
        return $next($request);
    }
}