<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CookieConsentController extends Controller
{
    /**
     * Enregistrer le consentement de l'utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'consent' => 'required|array',
            'consent.essential' => 'required|boolean',
            'consent.analytics' => 'required|boolean',
            'consent.marketing' => 'required|boolean',
            'consent.preferences' => 'required|boolean',
            'consent.timestamp' => 'required|string',
            'user_agent' => 'nullable|string',
            'timestamp' => 'required|string'
        ]);

        try {
            // Enregistrer dans les logs pour compliance RGPD
            Log::info('Cookie consent recorded', [
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $validated['user_agent'] ?? $request->userAgent(),
                'consent' => $validated['consent'],
                'timestamp' => $validated['timestamp'],
                'session_id' => session()->getId()
            ]);

            // Optionnel : Enregistrer en base de données pour analytics
            if ($validated['consent']['analytics']) {
                DB::table('cookie_consents')->updateOrInsert(
                    [
                        'user_id' => auth()->id(),
                        'session_id' => session()->getId()
                    ],
                    [
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'essential_cookies' => $validated['consent']['essential'],
                        'analytics_cookies' => $validated['consent']['analytics'],
                        'marketing_cookies' => $validated['consent']['marketing'],
                        'preferences_cookies' => $validated['consent']['preferences'],
                        'consent_timestamp' => $validated['consent']['timestamp'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Consentement enregistré avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to record cookie consent', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du consentement'
            ], 500);
        }
    }

    /**
     * Récupérer les statistiques de consentement (admin)
     */
    public function statistics()
    {
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403, 'Accès non autorisé');
        }

        try {
            $stats = DB::table('cookie_consents')
                ->selectRaw('
                    COUNT(*) as total_consents,
                    SUM(CASE WHEN essential_cookies = 1 THEN 1 ELSE 0 END) as essential_accepted,
                    SUM(CASE WHEN analytics_cookies = 1 THEN 1 ELSE 0 END) as analytics_accepted,
                    SUM(CASE WHEN marketing_cookies = 1 THEN 1 ELSE 0 END) as marketing_accepted,
                    SUM(CASE WHEN preferences_cookies = 1 THEN 1 ELSE 0 END) as preferences_accepted,
                    DATE(created_at) as date
                ')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();

            $summary = DB::table('cookie_consents')
                ->selectRaw('
                    COUNT(*) as total,
                    ROUND(AVG(CASE WHEN analytics_cookies = 1 THEN 100 ELSE 0 END), 1) as analytics_rate,
                    ROUND(AVG(CASE WHEN marketing_cookies = 1 THEN 100 ELSE 0 END), 1) as marketing_rate,
                    ROUND(AVG(CASE WHEN preferences_cookies = 1 THEN 100 ELSE 0 END), 1) as preferences_rate
                ')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'daily_stats' => $stats,
                    'summary' => $summary
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques'
            ], 500);
        }
    }

    /**
     * Afficher la page des préférences cookies
     */
    public function preferences()
    {
        return view('legal.cookie-preferences');
    }
}