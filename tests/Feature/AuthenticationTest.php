<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Badge;
use App\Services\QuizService;
use App\Services\BadgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer les badges de base
        Badge::factory()->create([
            'name' => 'Bienvenue',
            'type' => 'registration',
            'criteria' => json_encode(['action' => 'register'])
        ]);

        Badge::factory()->create([
            'name' => 'Débutant n8n',
            'type' => 'n8n_level',
            'criteria' => json_encode(['n8n_level' => 'beginner'])
        ]);
    }

    /** @test */
    public function user_can_register_as_individual()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_professional' => false,
            'quiz_answers' => [
                'experience' => 'beginner',
                'usage' => 'personal',
                'automation_knowledge' => 'none'
            ]
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/user/dashboard');
        
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->is_professional);
        $this->assertEquals('beginner', $user->n8n_level);
        $this->assertEquals('free', $user->subscription_type);
        
        // Vérifier que les badges ont été attribués
        $this->assertTrue($user->badges()->where('name', 'Bienvenue')->exists());
        $this->assertTrue($user->badges()->where('name', 'Débutant n8n')->exists());
    }

    /** @test */
    public function user_can_register_as_professional()
    {
        $userData = [
            'name' => 'Jane Smith',
            'email' => 'jane@company.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_professional' => true,
            'company_name' => 'Tech Corp',
            'address' => '123 Business St',
            'postal_code' => '12345',
            'city' => 'Business City',
            'country' => 'France',
            'vat_number' => 'FR123456789',
            'quiz_answers' => [
                'experience' => 'intermediate',
                'usage' => 'business',
                'automation_knowledge' => 'some'
            ]
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/user/dashboard');
        
        $user = User::where('email', 'jane@company.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->is_professional);
        $this->assertEquals('Tech Corp', $user->company_name);
        $this->assertEquals('intermediate', $user->n8n_level);
    }

    /** @test */
    public function registration_requires_quiz_answers()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_professional' => false,
            // Pas de quiz_answers
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['quiz_answers']);
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    /** @test */
    public function professional_registration_requires_company_name()
    {
        $userData = [
            'name' => 'Pro User',
            'email' => 'pro@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_professional' => true,
            // Pas de company_name
            'quiz_answers' => [
                'experience' => 'beginner',
                'usage' => 'business',
                'automation_knowledge' => 'none'
            ]
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors(['company_name']);
    }

    /** @test */
    public function user_can_login_and_is_redirected_correctly()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user'
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/user/dashboard');
        $this->assertAuthenticatedAs($user);
        
        // Vérifier que last_activity_at est mis à jour
        $user->refresh();
        $this->assertNotNull($user->last_activity_at);
    }

    /** @test */
    public function admin_is_redirected_to_admin_dashboard()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    /** @test */
    public function invalid_credentials_are_rejected()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /** @test */
    public function quiz_service_determines_correct_level()
    {
        $quizService = app(QuizService::class);

        // Test niveau débutant
        $beginnerAnswers = [
            'experience' => 'beginner',
            'usage' => 'personal',
            'automation_knowledge' => 'none'
        ];
        $level = $quizService->calculateLevel($beginnerAnswers);
        $this->assertEquals('beginner', $level);

        // Test niveau intermédiaire
        $intermediateAnswers = [
            'experience' => 'intermediate',
            'usage' => 'business',
            'automation_knowledge' => 'some'
        ];
        $level = $quizService->calculateLevel($intermediateAnswers);
        $this->assertEquals('intermediate', $level);

        // Test niveau avancé
        $advancedAnswers = [
            'experience' => 'advanced',
            'usage' => 'enterprise',
            'automation_knowledge' => 'expert'
        ];
        $level = $quizService->calculateLevel($advancedAnswers);
        $this->assertEquals('advanced', $level);
    }

    /** @test */
    public function registration_triggers_welcome_email_job()
    {
        $this->expectsJobs(\App\Jobs\SendWelcomeEmailJob::class);

        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_professional' => false,
            'quiz_answers' => [
                'experience' => 'beginner',
                'usage' => 'personal',
                'automation_knowledge' => 'none'
            ]
        ];

        $this->post('/register', $userData);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user)
             ->post('/logout')
             ->assertRedirect('/');
             
        $this->assertGuest();
    }

    /** @test */
    public function registration_creates_analytics_entry()
    {
        $userData = [
            'name' => 'Analytics User',
            'email' => 'analytics@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_professional' => false,
            'quiz_answers' => [
                'experience' => 'beginner',
                'usage' => 'personal',
                'automation_knowledge' => 'none'
            ]
        ];

        $this->post('/register', $userData);

        $user = User::where('email', 'analytics@example.com')->first();
        
        // Vérifier que l'événement d'inscription a été enregistré
        $this->assertDatabaseHas('analytics', [
            'user_id' => $user->id,
            'event_type' => 'user_registered'
        ]);
    }

    /** @test */
    public function password_reset_works_correctly()
    {
        $user = User::factory()->create(['email' => 'reset@example.com']);

        // Demander un reset
        $response = $this->post('/forgot-password', [
            'email' => 'reset@example.com'
        ]);

        $response->assertSessionHas('status');
        
        // Vérifier qu'un token a été créé
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'reset@example.com'
        ]);
    }
}
