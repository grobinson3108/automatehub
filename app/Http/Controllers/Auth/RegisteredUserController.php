<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_professional' => 'sometimes|boolean',
            'rgpd_accepted' => 'required|accepted',
        ]);

        // Créer le nom complet à partir du prénom et nom
        $fullName = $request->first_name . ' ' . $request->last_name;

        // Générer un username unique basé sur le prénom et nom
        $baseUsername = strtolower($request->first_name . '_' . $request->last_name);
        $username = $baseUsername;
        $counter = 1;
        
        // Vérifier l'unicité du username
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . '_' . $counter;
            $counter++;
        }

        // Utiliser tous les champs requis
        $userData = [
            'name' => $fullName,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_professional' => $request->boolean('is_professional', false),
            'rgpd_accepted' => true,
            'level_n8n' => 'beginner', // Niveau par défaut avant le quiz
        ];

        $user = User::create($userData);

        event(new Registered($user));

        Auth::login($user);

        // Rediriger vers le dashboard avec un paramètre pour afficher le quiz
        return redirect()->route('dashboard')->with('show_quiz', true);
    }
}
