<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTutorialRequest extends FormRequest
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
        $tutorialId = $this->route('id') ?? $this->route('tutorial');

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tutorials', 'title')->ignore($tutorialId)
            ],
            'description' => 'required|string|max:1000',
            'content' => 'required|string|min:100',
            'category_id' => 'required|exists:categories,id',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced,expert',
            'target_audience' => 'required|in:individual,pro,both',
            'subscription_type' => 'required|in:free,premium,pro',
            'estimated_duration' => 'nullable|integer|min:1|max:1440', // Max 24 heures
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'exists:tags,id',
            'files' => 'nullable|array|max:5',
            'files.*' => 'file|mimes:pdf,json,zip|max:10240', // 10MB max par fichier
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            'remove_files' => 'nullable|array',
            'remove_files.*' => 'string', // Chemins des fichiers à supprimer
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'prerequisites' => 'nullable|string|max:500',
            'learning_objectives' => 'nullable|array|max:10',
            'learning_objectives.*' => 'string|max:255',
            'status' => 'nullable|in:draft,published',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre du tutoriel est obligatoire.',
            'title.unique' => 'Un autre tutoriel avec ce titre existe déjà.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            
            'description.required' => 'La description est obligatoire.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
            
            'content.required' => 'Le contenu du tutoriel est obligatoire.',
            'content.min' => 'Le contenu doit contenir au moins 100 caractères.',
            
            'category_id.required' => 'La catégorie est obligatoire.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            
            'difficulty_level.required' => 'Le niveau de difficulté est obligatoire.',
            'difficulty_level.in' => 'Le niveau de difficulté doit être : débutant, intermédiaire, avancé ou expert.',
            
            'target_audience.required' => 'L\'audience cible est obligatoire.',
            'target_audience.in' => 'L\'audience cible doit être : particulier, professionnel ou les deux.',
            
            'subscription_type.required' => 'Le type d\'abonnement requis est obligatoire.',
            'subscription_type.in' => 'Le type d\'abonnement doit être : gratuit, premium ou pro.',
            
            'estimated_duration.integer' => 'La durée estimée doit être un nombre entier.',
            'estimated_duration.min' => 'La durée estimée doit être d\'au moins 1 minute.',
            'estimated_duration.max' => 'La durée estimée ne peut pas dépasser 24 heures (1440 minutes).',
            
            'tags.array' => 'Les tags doivent être un tableau.',
            'tags.max' => 'Vous ne pouvez pas sélectionner plus de 10 tags.',
            'tags.*.exists' => 'Un des tags sélectionnés n\'existe pas.',
            
            'files.array' => 'Les fichiers doivent être un tableau.',
            'files.max' => 'Vous ne pouvez pas uploader plus de 5 fichiers.',
            'files.*.file' => 'Chaque élément doit être un fichier valide.',
            'files.*.mimes' => 'Les fichiers doivent être au format PDF, JSON ou ZIP.',
            'files.*.max' => 'Chaque fichier ne peut pas dépasser 10 MB.',
            
            'thumbnail.image' => 'La miniature doit être une image.',
            'thumbnail.mimes' => 'La miniature doit être au format JPEG, PNG, JPG ou GIF.',
            'thumbnail.max' => 'La miniature ne peut pas dépasser 2 MB.',
            
            'remove_files.array' => 'Les fichiers à supprimer doivent être un tableau.',
            'remove_files.*.string' => 'Chaque fichier à supprimer doit être une chaîne de caractères.',
            
            'meta_title.max' => 'Le titre SEO ne peut pas dépasser 60 caractères.',
            'meta_description.max' => 'La description SEO ne peut pas dépasser 160 caractères.',
            
            'prerequisites.max' => 'Les prérequis ne peuvent pas dépasser 500 caractères.',
            
            'learning_objectives.array' => 'Les objectifs d\'apprentissage doivent être un tableau.',
            'learning_objectives.max' => 'Vous ne pouvez pas avoir plus de 10 objectifs d\'apprentissage.',
            'learning_objectives.*.string' => 'Chaque objectif d\'apprentissage doit être une chaîne de caractères.',
            'learning_objectives.*.max' => 'Chaque objectif d\'apprentissage ne peut pas dépasser 255 caractères.',
            
            'status.in' => 'Le statut doit être : brouillon ou publié.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'titre',
            'description' => 'description',
            'content' => 'contenu',
            'category_id' => 'catégorie',
            'difficulty_level' => 'niveau de difficulté',
            'target_audience' => 'audience cible',
            'subscription_type' => 'type d\'abonnement',
            'estimated_duration' => 'durée estimée',
            'tags' => 'tags',
            'files' => 'fichiers',
            'thumbnail' => 'miniature',
            'remove_files' => 'fichiers à supprimer',
            'meta_title' => 'titre SEO',
            'meta_description' => 'description SEO',
            'prerequisites' => 'prérequis',
            'learning_objectives' => 'objectifs d\'apprentissage',
            'status' => 'statut',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer et formater les données avant validation
        if ($this->has('learning_objectives') && is_string($this->learning_objectives)) {
            // Si les objectifs sont envoyés comme string (textarea), les convertir en array
            $objectives = array_filter(
                array_map('trim', explode("\n", $this->learning_objectives)),
                function($objective) {
                    return !empty($objective);
                }
            );
            $this->merge(['learning_objectives' => $objectives]);
        }

        // Nettoyer le contenu HTML
        if ($this->has('content')) {
            $this->merge([
                'content' => trim($this->content)
            ]);
        }

        // S'assurer que estimated_duration est un entier
        if ($this->has('estimated_duration') && !empty($this->estimated_duration)) {
            $this->merge([
                'estimated_duration' => (int) $this->estimated_duration
            ]);
        }

        // Nettoyer la liste des fichiers à supprimer
        if ($this->has('remove_files') && is_array($this->remove_files)) {
            $cleanFiles = array_filter($this->remove_files, function($file) {
                return !empty(trim($file));
            });
            $this->merge(['remove_files' => array_values($cleanFiles)]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validation personnalisée : vérifier que le contenu contient des informations utiles
            if ($this->has('content')) {
                $contentLength = strlen(strip_tags($this->content));
                if ($contentLength < 100) {
                    $validator->errors()->add('content', 'Le contenu doit contenir au moins 100 caractères de texte utile.');
                }
            }

            // Validation personnalisée : vérifier la cohérence entre target_audience et subscription_type
            if ($this->target_audience === 'pro' && $this->subscription_type === 'free') {
                $validator->errors()->add('subscription_type', 'Un tutoriel destiné aux professionnels ne peut pas être gratuit.');
            }

            // Validation personnalisée : vérifier que les tutoriels experts ne sont pas gratuits
            if ($this->difficulty_level === 'expert' && $this->subscription_type === 'free') {
                $validator->errors()->add('subscription_type', 'Un tutoriel de niveau expert ne peut pas être gratuit.');
            }

            // Validation personnalisée : vérifier qu'on ne peut pas publier un tutoriel sans contenu suffisant
            if ($this->status === 'published') {
                if (empty($this->description) || strlen($this->description) < 50) {
                    $validator->errors()->add('description', 'La description doit contenir au moins 50 caractères pour publier le tutoriel.');
                }

                if (empty($this->learning_objectives) || count($this->learning_objectives) < 1) {
                    $validator->errors()->add('learning_objectives', 'Au moins un objectif d\'apprentissage est requis pour publier le tutoriel.');
                }
            }

            // Validation des fichiers à supprimer (vérifier qu'ils existent)
            if ($this->has('remove_files') && is_array($this->remove_files)) {
                foreach ($this->remove_files as $index => $filePath) {
                    if (!empty($filePath) && !file_exists(storage_path('app/' . $filePath))) {
                        $validator->errors()->add("remove_files.{$index}", "Le fichier à supprimer n'existe pas : {$filePath}");
                    }
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

        // Traitement supplémentaire pour les données validées
        if (isset($validated['learning_objectives']) && is_array($validated['learning_objectives'])) {
            // S'assurer que les objectifs sont bien formatés
            $validated['learning_objectives'] = array_values(array_filter(
                array_map('trim', $validated['learning_objectives']),
                function($objective) {
                    return !empty($objective);
                }
            ));
        }

        return $validated;
    }
}
