<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
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
        // Check if user is authenticated and has admin role
        if (!$request->user() || !$request->user()->is_admin) {
            // If not admin, redirect to home with error message
            return redirect()->route('home')->with('error', 'Accès non autorisé. Vous devez être administrateur pour accéder à cette page.');
        }

        return $next($request);
    }
}
