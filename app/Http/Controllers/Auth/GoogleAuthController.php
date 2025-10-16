<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists
            $user = User::where('email', $googleUser->email)->first();
            
            if ($user) {
                // Update Google ID if not set
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            } else {
                // Create new user
                // Split name into first and last name
                $nameParts = explode(' ', $googleUser->name, 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? '';
                
                $user = User::create([
                    'name' => $googleUser->name,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $this->generateUniqueUsername($googleUser->email),
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(24)), // Random password since they use Google
                    'subscription_type' => 'freemium', // Default to freemium
                    'rgpd_accepted' => true, // Assumed accepted when using Google OAuth
                    'is_admin' => false,
                    'level_n8n' => 'beginner', // Default level
                ]);
            }
            
            // Login the user
            Auth::login($user, true);
            
            // Toujours rediriger vers le dashboard, l'onboarding s'affichera en modal si nécessaire
            return redirect()->route('user.dashboard');
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Une erreur est survenue lors de la connexion avec Google. Veuillez réessayer.');
        }
    }
    
    /**
     * Generate a unique username from email
     */
    private function generateUniqueUsername($email)
    {
        // Get the part before @
        $baseUsername = explode('@', $email)[0];
        // Remove any non-alphanumeric characters
        $baseUsername = preg_replace('/[^a-zA-Z0-9]/', '', $baseUsername);
        
        // Check if username exists and add number if needed
        $username = $baseUsername;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
}