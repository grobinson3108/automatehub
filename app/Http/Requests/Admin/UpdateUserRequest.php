<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('id') ?? $this->route('user');

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'nullable|string|min:8',
            'subscription_type' => 'required|in:free,premium,pro',
            'n8n_level' => 'required|in:beginner,intermediate,advanced,expert',
            'is_professional' => 'boolean',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'vat_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email_verified_at' => 'nullable|date',
            'role' => 'nullable|in:user,admin',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.max' => 'L\'adresse email ne peut pas dépasser 255 caractères.',
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre utilisateur.',
            
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            
            'password_confirmation.string' => 'La confirmation du mot de passe doit être une chaîne de caractères.',
            'password_confirmation.min' => 'La confirmation du mot de passe doit contenir au moins 8 caractères.',
            
            'subscription_type.required' => 'Le type d\'abonnement est obligatoire.',
            'subscription_type.in' => 'Le type d\'abonnement doit être : gratuit, premium ou pro.',
            
            'n8n_level.required' => 'Le niveau n8n est obligatoire.',
            'n8n_level.in' => 'Le niveau n8n doit être : débutant, intermédiaire, avancé ou expert.',
            
            'is_professional.boolean' => 'Le statut professionnel doit être vrai ou faux.',
            
            'company_name.string' => 'Le nom de l\'entreprise doit être une chaîne de caractères.',
            'company_name.max' => 'Le nom de l\'entreprise ne peut pas dépasser 255 caractères.',
            
            'address.string' => 'L\'adresse doit être une chaîne de caractères.',
            'address.max' => 'L\'adresse ne peut pas dépasser 255 caractères.',
            
            'postal_code.string' => 'Le code postal doit être une chaîne de caractères.',
            'postal_code.max' => 'Le code postal ne peut pas dépasser 10 caractères.',
            
            'city.string' => 'La ville doit être une chaîne de caractères.',
            'city.max' => 'La ville ne peut pas dépasser 255 caractères.',
            
            'country.string' => 'Le pays doit être une chaîne de caractères.',
            'country.max' => 'Le pays ne peut pas dépasser 255 caractères.',
            
            'vat_number.string' => 'Le numéro de TVA doit être une chaîne de caractères.',
            'vat_number.max' => 'Le numéro de TVA ne peut pas dépasser 50 caractères.',
            
            'phone.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            
            'website.url' => 'Le site web doit être une URL valide.',
            'website.max' => 'Le site web ne peut pas dépasser 255 caractères.',
            
            'bio.string' => 'La biographie doit être une chaîne de caractères.',
            'bio.max' => 'La biographie ne peut pas dépasser 1000 caractères.',
            
            'avatar.image' => 'L\'avatar doit être une image.',
            'avatar.mimes' => 'L\'avatar doit être au format JPEG, PNG, JPG ou GIF.',
            'avatar.max' => 'L\'avatar ne peut pas dépasser 2 MB.',
            
            'email_verified_at.date' => 'La date de vérification de l\'email doit être une date valide.',
            
            'role.in' => 'Le rôle doit être : utilisateur ou administrateur.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'email' => 'adresse email',
            'password' => 'mot de passe',
            'password_confirmation' => 'confirmation du mot de passe',
            'subscription_type' => 'type d\'abonnement',
            'n8n_level' => 'niveau n8n',
            'is_professional' => 'statut professionnel',
            'company_name' => 'nom de l\'entreprise',
            'address' => 'adresse',
            'postal_code' => 'code postal',
            'city' => 'ville',
            'country' => 'pays',
            'vat_number' => 'numéro de TVA',
            'phone' => 'numéro de téléphone',
            'website' => 'site web',
            'bio' => 'biographie',
            'avatar' => 'avatar',
            'email_verified_at' => 'date de vérification de l\'email',
            'role' => 'rôle',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convertir is_professional en boolean
        if ($this->has('is_professional')) {
            $this->merge([
                'is_professional' => filter_var($this->is_professional, FILTER_VALIDATE_BOOLEAN)
            ]);
        }

        // Nettoyer les champs texte
        $textFields = ['name', 'company_name', 'address', 'city', 'country', 'vat_number', 'phone', 'bio'];
        foreach ($textFields as $field) {
            if ($this->has($field) && !is_null($this->get($field))) {
                $this->merge([
                    $field => trim($this->get($field))
                ]);
            }
        }

        // Nettoyer l'email
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->email))
            ]);
        }

        // Nettoyer le code postal
        if ($this->has('postal_code')) {
            $this->merge([
                'postal_code' => preg_replace('/[^0-9A-Za-z\-\s]/', '', $this->postal_code)
            ]);
        }

        // Nettoyer le numéro de TVA
        if ($this->has('vat_number')) {
            $this->merge([
                'vat_number' => strtoupper(preg_replace('/[^0-9A-Za-z]/', '', $this->vat_number))
            ]);
        }

        // Nettoyer le numéro de téléphone
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^0-9\+\-\s\(\)]/', '', $this->phone)
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validation personnalisée : company_name obligatoire si is_professional = true
            if ($this->is_professional && empty($this->company_name)) {
                $validator->errors()->add('company_name', 'Le nom de l\'entreprise est obligatoire pour un compte professionnel.');
            }

            // Validation personnalisée : vérifier que l'utilisateur ne se supprime pas ses propres droits admin
            if ($this->has('role') && $this->role !== 'admin') {
                $userId = $this->route('id') ?? $this->route('user');
                if ($userId == auth()->id()) {
                    $validator->errors()->add('role', 'Vous ne pouvez pas retirer vos propres droits d\'administrateur.');
                }
            }

            // Validation personnalisée : vérifier la cohérence du niveau n8n avec le type d'abonnement
            if ($this->n8n_level === 'expert' && $this->subscription_type === 'free') {
                $validator->errors()->add('subscription_type', 'Un utilisateur de niveau expert devrait avoir un abonnement premium ou pro.');
            }

            // Validation personnalisée : vérifier le format du numéro de TVA pour les professionnels
            if ($this->is_professional && !empty($this->vat_number)) {
                // Validation basique du format de numéro de TVA européen
                if (!preg_match('/^[A-Z]{2}[0-9A-Z]{2,12}$/', $this->vat_number)) {
                    $validator->errors()->add('vat_number', 'Le format du numéro de TVA semble incorrect (ex: FR12345678901).');
                }
            }

            // Validation personnalisée : vérifier que l'email n'est pas déjà vérifié si on essaie de le déverifier
            if ($this->has('email_verified_at') && is_null($this->email_verified_at)) {
                $userId = $this->route('id') ?? $this->route('user');
                $user = \App\Models\User::find($userId);
                if ($user && $user->email_verified_at) {
                    $validator->errors()->add('email_verified_at', 'Attention : vous êtes sur le point de marquer cet email comme non vérifié.');
                }
            }

            // Validation personnalisée : vérifier la force du mot de passe si fourni
            if ($this->has('password') && !empty($this->password)) {
                $password = $this->password;
                
                // Au moins une majuscule
                if (!preg_match('/[A-Z]/', $password)) {
                    $validator->errors()->add('password', 'Le mot de passe doit contenir au moins une majuscule.');
                }
                
                // Au moins une minuscule
                if (!preg_match('/[a-z]/', $password)) {
                    $validator->errors()->add('password', 'Le mot de passe doit contenir au moins une minuscule.');
                }
                
                // Au moins un chiffre
                if (!preg_match('/[0-9]/', $password)) {
                    $validator->errors()->add('password', 'Le mot de passe doit contenir au moins un chiffre.');
                }
                
                // Au moins un caractère spécial
                if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                    $validator->errors()->add('password', 'Le mot de passe doit contenir au moins un caractère spécial.');
                }
            }
        });
    }

    /**
     * Get the validated data with additional processing.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Si on ne passe qu'une clé spécifique, retourner directement
        if ($key !== null) {
            return $validated;
        }

        // Supprimer le mot de passe s'il est vide
        if (isset($validated['password']) && empty($validated['password'])) {
            unset($validated['password']);
            unset($validated['password_confirmation']);
        }

        // Supprimer password_confirmation des données validées
        unset($validated['password_confirmation']);

        // Nettoyer les champs vides pour les professionnels
        if (!$validated['is_professional']) {
            $professionalFields = ['company_name', 'vat_number'];
            foreach ($professionalFields as $field) {
                if (isset($validated[$field])) {
                    $validated[$field] = null;
                }
            }
        }

        return $validated;
    }
}
