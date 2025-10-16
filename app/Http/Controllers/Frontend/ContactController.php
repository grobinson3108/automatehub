<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    protected AnalyticsService $analyticsService;
    protected NotificationService $notificationService;

    public function __construct(
        AnalyticsService $analyticsService,
        NotificationService $notificationService
    ) {
        $this->analyticsService = $analyticsService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display the contact form.
     */
    public function show()
    {
        // Informations de contact
        $contactInfo = [
            'email' => 'contact@automatehub.fr',
            'support_email' => 'support@automatehub.fr',
            'business_email' => 'business@automatehub.fr',
            'phone' => '+33 1 23 45 67 89',
            'address' => [
                'street' => '123 Rue de l\'Innovation',
                'city' => 'Paris',
                'postal_code' => '75001',
                'country' => 'France',
            ],
            'business_hours' => [
                'monday_friday' => '9h00 - 18h00',
                'saturday' => '10h00 - 16h00',
                'sunday' => 'Fermé',
            ],
            'response_time' => '24-48 heures',
        ];

        // Types de demandes
        $inquiryTypes = [
            'general' => 'Question générale',
            'support' => 'Support technique',
            'business' => 'Partenariat / Business',
            'billing' => 'Facturation',
            'feature' => 'Demande de fonctionnalité',
            'bug' => 'Signaler un bug',
            'other' => 'Autre',
        ];

        // FAQ rapide
        $quickFaq = [
            [
                'question' => 'Comment puis-je upgrader mon compte ?',
                'answer' => 'Connectez-vous à votre compte et rendez-vous dans les paramètres d\'abonnement.',
                'link' => route('login'),
            ],
            [
                'question' => 'Où puis-je trouver mes téléchargements ?',
                'answer' => 'Tous vos téléchargements sont disponibles dans votre espace personnel.',
                'link' => route('login'),
            ],
            [
                'question' => 'Comment fonctionne le système de badges ?',
                'answer' => 'Les badges sont attribués automatiquement selon votre progression et activité.',
                'link' => route('frontend.tutorials.index'),
            ],
        ];

        // Call-to-action
        $callToAction = [
            'title' => 'Besoin d\'aide immédiate ?',
            'subtitle' => 'Consultez notre documentation ou rejoignez notre communauté',
            'buttons' => [
                [
                    'text' => 'Documentation',
                    'url' => route('frontend.tutorials.index'),
                    'style' => 'primary',
                ],
                [
                    'text' => 'Communauté Discord',
                    'url' => '#', // Lien vers Discord
                    'style' => 'outline',
                ],
            ],
        ];

        // Meta données pour SEO
        $metaData = [
            'title' => 'Contact - Automatehub',
            'description' => 'Contactez l\'équipe Automatehub pour toute question sur n8n, l\'automation ou nos services. Support technique et commercial disponible.',
            'keywords' => 'contact, support, automatehub, n8n, aide, assistance',
            'url' => route('frontend.contact.show'),
        ];

        // Tracking
        $this->analyticsService->trackAnonymous('contact_page_viewed');

        return view('frontend.contact.show', compact(
            'contactInfo',
            'inquiryTypes',
            'quickFaq',
            'callToAction',
            'metaData'
        ));
    }

    /**
     * Handle the contact form submission.
     */
    public function store(Request $request): RedirectResponse
    {
        // Rate limiting pour éviter le spam
        $key = 'contact-form:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Trop de tentatives. Réessayez dans {$seconds} secondes.",
            ]);
        }

        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'inquiry_type' => 'required|in:general,support,business,billing,feature,bug,other',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
            'newsletter' => 'boolean',
            'terms' => 'accepted',
            // Protection anti-spam
            'honeypot' => 'nullable|max:0', // Champ caché pour les bots
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'inquiry_type.required' => 'Veuillez sélectionner le type de demande.',
            'subject.required' => 'Le sujet est obligatoire.',
            'message.required' => 'Le message est obligatoire.',
            'message.min' => 'Le message doit contenir au moins 10 caractères.',
            'message.max' => 'Le message ne peut pas dépasser 2000 caractères.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
        ]);

        // Vérification anti-spam
        if (!empty($validated['honeypot'])) {
            // Bot détecté, on fait semblant que ça marche
            return redirect()->route('frontend.contact.show')
                ->with('success', 'Votre message a été envoyé avec succès.');
        }

        RateLimiter::hit($key, 300); // 5 minutes de blocage

        try {
            // Envoyer l'email de contact
            $this->sendContactEmail($validated);

            // Envoyer l'email de confirmation à l'utilisateur
            $this->sendConfirmationEmail($validated);

            // Sauvegarder le message (optionnel - vous pourriez créer une table contact_messages)
            $this->saveContactMessage($validated, $request);

            // S'inscrire à la newsletter si demandé
            if ($validated['newsletter'] ?? false) {
                $this->subscribeToNewsletter($validated['email'], $validated['name']);
            }

            // Tracking
            $this->analyticsService->trackAnonymous('contact_form_submitted', [
                'inquiry_type' => $validated['inquiry_type'],
                'has_phone' => !empty($validated['phone']),
                'has_company' => !empty($validated['company']),
                'newsletter_signup' => $validated['newsletter'] ?? false,
                'message_length' => strlen($validated['message']),
            ]);

            return redirect()->route('frontend.contact.show')
                ->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');

        } catch (\Exception $e) {
            // Log l'erreur
            \Log::error('Contact form error: ' . $e->getMessage(), [
                'email' => $validated['email'],
                'subject' => $validated['subject'],
            ]);

            return redirect()->route('frontend.contact.show')
                ->with('error', 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer.')
                ->withInput();
        }
    }

    /**
     * Handle AJAX contact form submission.
     */
    public function submit(Request $request): JsonResponse
    {
        // Rate limiting
        $key = 'contact-ajax:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'success' => false,
                'message' => 'Trop de tentatives. Veuillez patienter avant de réessayer.',
            ], 429);
        }

        // Validation
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'inquiry_type' => 'required|in:general,support,business,billing,feature,bug,other',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|min:10|max:2000',
                'honeypot' => 'nullable|max:0',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides.',
                'errors' => $e->errors(),
            ], 422);
        }

        // Anti-spam
        if (!empty($validated['honeypot'])) {
            return response()->json([
                'success' => true,
                'message' => 'Message envoyé avec succès.',
            ]);
        }

        RateLimiter::hit($key, 180); // 3 minutes

        try {
            // Envoyer les emails
            $this->sendContactEmail($validated);
            $this->sendConfirmationEmail($validated);
            $this->saveContactMessage($validated, $request);

            // Tracking
            $this->analyticsService->trackAnonymous('contact_ajax_submitted', [
                'inquiry_type' => $validated['inquiry_type'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Votre message a été envoyé avec succès.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Contact AJAX error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.',
            ], 500);
        }
    }

    /**
     * Send contact email to admin.
     */
    private function sendContactEmail(array $data): void
    {
        $adminEmail = $this->getAdminEmailByType($data['inquiry_type']);

        Mail::send('emails.contact.admin', ['data' => $data], function ($message) use ($data, $adminEmail) {
            $message->to($adminEmail)
                    ->subject('[Automatehub] ' . $data['subject'])
                    ->replyTo($data['email'], $data['name']);
        });
    }

    /**
     * Send confirmation email to user.
     */
    private function sendConfirmationEmail(array $data): void
    {
        Mail::send('emails.contact.confirmation', ['data' => $data], function ($message) use ($data) {
            $message->to($data['email'], $data['name'])
                    ->subject('Confirmation de réception - Automatehub');
        });
    }

    /**
     * Save contact message to database.
     */
    private function saveContactMessage(array $data, Request $request): void
    {
        // Vous pourriez créer une table contact_messages
        // Pour l'instant, on sauvegarde dans les logs
        \Log::info('Contact message received', [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'inquiry_type' => $data['inquiry_type'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Subscribe user to newsletter.
     */
    private function subscribeToNewsletter(string $email, string $name): void
    {
        // Intégration avec votre service de newsletter
        // Pour l'instant, on log l'inscription
        \Log::info('Newsletter subscription from contact form', [
            'email' => $email,
            'name' => $name,
            'source' => 'contact_form',
        ]);
    }

    /**
     * Get admin email based on inquiry type.
     */
    private function getAdminEmailByType(string $type): string
    {
        return match($type) {
            'support', 'bug' => 'support@automatehub.fr',
            'business' => 'business@automatehub.fr',
            'billing' => 'billing@automatehub.fr',
            default => 'contact@automatehub.fr',
        };
    }

    /**
     * Get contact statistics for admin.
     */
    public function getStats(): JsonResponse
    {
        // Cette méthode pourrait être utilisée par l'admin
        // Pour l'instant, on retourne des stats simulées
        $stats = [
            'total_messages' => 150,
            'this_month' => 23,
            'response_rate' => 98.5,
            'avg_response_time' => '4.2 heures',
            'by_type' => [
                'support' => 45,
                'general' => 30,
                'business' => 20,
                'billing' => 15,
                'feature' => 10,
                'bug' => 8,
                'other' => 22,
            ],
        ];

        return response()->json($stats);
    }

    /**
     * Get contact form configuration.
     */
    public function getConfig(): JsonResponse
    {
        $config = [
            'inquiry_types' => [
                'general' => 'Question générale',
                'support' => 'Support technique',
                'business' => 'Partenariat / Business',
                'billing' => 'Facturation',
                'feature' => 'Demande de fonctionnalité',
                'bug' => 'Signaler un bug',
                'other' => 'Autre',
            ],
            'max_message_length' => 2000,
            'required_fields' => ['name', 'email', 'inquiry_type', 'subject', 'message'],
            'optional_fields' => ['phone', 'company'],
            'response_time' => '24-48 heures',
            'support_hours' => 'Lun-Ven 9h-18h, Sam 10h-16h',
        ];

        return response()->json($config);
    }
}
