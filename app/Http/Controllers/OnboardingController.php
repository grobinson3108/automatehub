<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show onboarding welcome page
     */
    public function welcome()
    {
        return view('onboarding.welcome-overlay');
    }

    /**
     * Update user's n8n level
     */
    public function updateLevel(Request $request)
    {
        $request->validate([
            'level_n8n' => 'required|in:beginner,intermediate,expert'
        ]);

        $user = Auth::user();
        $user->level_n8n = $request->level_n8n;
        $user->save();

        return redirect()->route('onboarding.preferences');
    }

    /**
     * Show preferences page
     */
    public function preferences()
    {
        return view('onboarding.preferences-overlay');
    }

    /**
     * Update user preferences
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'weekly_digest' => 'boolean',
        ]);

        $user = Auth::user();
        $user->email_notifications = $request->boolean('email_notifications', true);
        $user->weekly_digest = $request->boolean('weekly_digest', true);
        $user->onboarding_completed = true;
        $user->save();

        // Log onboarding completion
        activity()
            ->performedOn($user)
            ->log('Onboarding completed');

        return redirect()->route('user.dashboard')
            ->with('success', 'Bienvenue sur AutomateHub ! Votre profil est maintenant configuré.');
    }
}