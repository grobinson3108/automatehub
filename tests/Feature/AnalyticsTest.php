<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tutorial;
use App\Models\Analytics;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $analyticsService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->analyticsService = app(AnalyticsService::class);
    }

    /** @test */
    public function user_registration_is_tracked()
    {
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

        $user = User::where('email', 'test@example.com')->first();
        
        // Vérifier que l'événement d'inscription est tracké
        $this->assertDatabaseHas('analytics', [
            'user_id' => $user->id,
            'event_type' => 'user_registered'
        ]);

        $analytics = Analytics::where('user_id', $user->id)
                             ->where('event_type', 'user_registered')
                             ->first();

        $eventData = json_decode($analytics->event_data, true);
        $this->assertEquals('beginner', $eventData['n8n_level']);
        $this->assertEquals(false, $eventData['is_professional']);
    }

    /** @test */
    public function tutorial_views_are_tracked()
    {
        $user = User::factory()->create();
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true
        ]);

        $this->actingAs($user)
             ->get("/user/tutorials/{$tutorial->id}");

        // Vérifier que la vue est trackée
        $this->assertDatabaseHas('analytics', [
            'user_id' => $user->id,
            'event_type' => 'tutorial_viewed'
        ]);

        $analytics = Analytics::where('user_id', $user->id)
                             ->where('event_type', 'tutorial_viewed')
                             ->first();

        $eventData = json_decode($analytics->event_data, true);
        $this->assertEquals($tutorial->id, $eventData['tutorial_id']);
        $this->assertEquals($tutorial->title, $eventData['tutorial_title']);
    }

    /** @test */
    public function tutorial_completion_is_tracked()
    {
        $user = User::factory()->create();
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true
        ]);

        $this->actingAs($user)
             ->post("/user/tutorials/{$tutorial->id}/complete");

        // Vérifier que la complétion est trackée
        $this->assertDatabaseHas('analytics', [
            'user_id' => $user->id,
            'event_type' => 'tutorial_completed'
        ]);

        $analytics = Analytics::where('user_id', $user->id)
                             ->where('event_type', 'tutorial_completed')
                             ->first();

        $eventData = json_decode($analytics->event_data, true);
        $this->assertEquals($tutorial->id, $eventData['tutorial_id']);
        $this->assertArrayHasKey('completion_time', $eventData);
    }

    /** @test */
    public function download_events_are_tracked()
    {
        $user = User::factory()->create();
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true,
            'files' => json_encode(['test-file.json'])
        ]);

        // Simuler un téléchargement
        $this->analyticsService->track($user->id, 'download_completed', [
            'tutorial_id' => $tutorial->id,
            'file_name' => 'test-file.json',
            'file_size' => 1024
        ]);

        $this->assertDatabaseHas('analytics', [
            'user_id' => $user->id,
            'event_type' => 'download_completed'
        ]);

        $analytics = Analytics::where('user_id', $user->id)
                             ->where('event_type', 'download_completed')
                             ->first();

        $eventData = json_decode($analytics->event_data, true);
        $this->assertEquals('test-file.json', $eventData['file_name']);
        $this->assertEquals(1024, $eventData['file_size']);
    }

    /** @test */
    public function badge_earned_events_are_tracked()
    {
        $user = User::factory()->create();

        $this->analyticsService->track($user->id, 'badge_earned', [
            'badge_name' => 'Bienvenue',
            'badge_type' => 'registration'
        ]);

        $this->assertDatabaseHas('analytics', [
            'user_id' => $user->id,
            'event_type' => 'badge_earned'
        ]);

        $analytics = Analytics::where('user_id', $user->id)
                             ->where('event_type', 'badge_earned')
                             ->first();

        $eventData = json_decode($analytics->event_data, true);
        $this->assertEquals('Bienvenue', $eventData['badge_name']);
        $this->assertEquals('registration', $eventData['badge_type']);
    }

    /** @test */
    public function upgrade_prompts_are_tracked()
    {
        $user = User::factory()->create(['subscription_type' => 'free']);

        $this->analyticsService->track($user->id, 'upgrade_prompt_shown', [
            'context' => 'download_limit_reached',
            'current_subscription' => 'free',
            'target_subscription' => 'premium'
        ]);

        $this->assertDatabaseHas('analytics', [
            'user_id' => $user->id,
            'event_type' => 'upgrade_prompt_shown'
        ]);

        $analytics = Analytics::where('user_id', $user->id)
                             ->where('event_type', 'upgrade_prompt_shown')
                             ->first();

        $eventData = json_decode($analytics->event_data, true);
        $this->assertEquals('download_limit_reached', $eventData['context']);
        $this->assertEquals('free', $eventData['current_subscription']);
        $this->assertEquals('premium', $eventData['target_subscription']);
    }

    /** @test */
    public function analytics_service_calculates_user_stats_correctly()
    {
        $user = User::factory()->create();
        
        // Créer des événements de test
        $this->analyticsService->track($user->id, 'tutorial_viewed', ['tutorial_id' => 1]);
        $this->analyticsService->track($user->id, 'tutorial_viewed', ['tutorial_id' => 2]);
        $this->analyticsService->track($user->id, 'tutorial_completed', ['tutorial_id' => 1]);
        $this->analyticsService->track($user->id, 'download_completed', ['file_name' => 'test.json']);

        $stats = $this->analyticsService->getUserStats($user->id);

        $this->assertArrayHasKey('total_events', $stats);
        $this->assertArrayHasKey('tutorials_viewed', $stats);
        $this->assertArrayHasKey('tutorials_completed', $stats);
        $this->assertArrayHasKey('downloads_count', $stats);
        
        $this->assertEquals(4, $stats['total_events']);
        $this->assertEquals(2, $stats['tutorials_viewed']);
        $this->assertEquals(1, $stats['tutorials_completed']);
        $this->assertEquals(1, $stats['downloads_count']);
    }

    /** @test */
    public function analytics_service_gets_popular_content()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $tutorial1 = Tutorial::factory()->create(['title' => 'Popular Tutorial']);
        $tutorial2 = Tutorial::factory()->create(['title' => 'Less Popular Tutorial']);

        // Tutorial 1 plus populaire
        $this->analyticsService->track($user1->id, 'tutorial_viewed', ['tutorial_id' => $tutorial1->id]);
        $this->analyticsService->track($user2->id, 'tutorial_viewed', ['tutorial_id' => $tutorial1->id]);
        $this->analyticsService->track($user1->id, 'tutorial_viewed', ['tutorial_id' => $tutorial2->id]);

        $popularContent = $this->analyticsService->getPopularContent(now()->subDay(), now(), 10);

        $this->assertNotEmpty($popularContent);
        $this->assertEquals($tutorial1->id, $popularContent[0]['tutorial_id']);
        $this->assertEquals(2, $popularContent[0]['views']);
    }

    /** @test */
    public function analytics_service_calculates_conversion_rates()
    {
        $freeUser = User::factory()->create(['subscription_type' => 'free']);
        $premiumUser = User::factory()->create(['subscription_type' => 'premium']);

        // Simuler des prompts d'upgrade
        $this->analyticsService->track($freeUser->id, 'upgrade_prompt_shown', ['context' => 'download_limit']);
        $this->analyticsService->track($premiumUser->id, 'upgrade_prompt_shown', ['context' => 'download_limit']);
        
        // Simuler une conversion
        $this->analyticsService->track($premiumUser->id, 'subscription_upgraded', [
            'from' => 'free',
            'to' => 'premium'
        ]);

        $conversionRate = $this->analyticsService->getConversionRate(now()->subDay(), now());

        $this->assertArrayHasKey('prompts_shown', $conversionRate);
        $this->assertArrayHasKey('conversions', $conversionRate);
        $this->assertArrayHasKey('rate', $conversionRate);
        
        $this->assertEquals(2, $conversionRate['prompts_shown']);
        $this->assertEquals(1, $conversionRate['conversions']);
        $this->assertEquals(50, $conversionRate['rate']); // 1/2 * 100
    }

    /** @test */
    public function analytics_service_tracks_user_engagement()
    {
        $user = User::factory()->create();

        // Simuler de l'activité sur plusieurs jours
        $this->analyticsService->track($user->id, 'login', [], now()->subDays(2));
        $this->analyticsService->track($user->id, 'tutorial_viewed', ['tutorial_id' => 1], now()->subDays(2));
        $this->analyticsService->track($user->id, 'login', [], now()->subDay());
        $this->analyticsService->track($user->id, 'tutorial_viewed', ['tutorial_id' => 2], now()->subDay());
        $this->analyticsService->track($user->id, 'login', []);
        $this->analyticsService->track($user->id, 'download_completed', ['file_name' => 'test.json']);

        $engagement = $this->analyticsService->getUserEngagement($user->id, now()->subDays(7), now());

        $this->assertArrayHasKey('active_days', $engagement);
        $this->assertArrayHasKey('total_sessions', $engagement);
        $this->assertArrayHasKey('avg_session_duration', $engagement);
        
        $this->assertEquals(3, $engagement['active_days']);
    }

    /** @test */
    public function analytics_service_generates_daily_summary()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Créer des événements pour aujourd'hui
        $this->analyticsService->track($user1->id, 'tutorial_viewed', ['tutorial_id' => 1]);
        $this->analyticsService->track($user2->id, 'tutorial_viewed', ['tutorial_id' => 1]);
        $this->analyticsService->track($user1->id, 'download_completed', ['file_name' => 'test.json']);

        $summary = $this->analyticsService->getDailySummary(now()->startOfDay(), now()->endOfDay());

        $this->assertArrayHasKey('total_events', $summary);
        $this->assertArrayHasKey('unique_users', $summary);
        $this->assertArrayHasKey('tutorials_viewed', $summary);
        $this->assertArrayHasKey('downloads_completed', $summary);
        
        $this->assertEquals(3, $summary['total_events']);
        $this->assertEquals(2, $summary['unique_users']);
        $this->assertEquals(2, $summary['tutorials_viewed']);
        $this->assertEquals(1, $summary['downloads_completed']);
    }

    /** @test */
    public function analytics_service_tracks_search_queries()
    {
        $user = User::factory()->create();

        $this->analyticsService->track($user->id, 'search_performed', [
            'query' => 'n8n automation',
            'results_count' => 5,
            'category' => 'tutorials'
        ]);

        $this->assertDatabaseHas('analytics', [
            'user_id' => $user->id,
            'event_type' => 'search_performed'
        ]);

        $searchAnalytics = $this->analyticsService->getSearchAnalytics(now()->subDay(), now());
        
        $this->assertNotEmpty($searchAnalytics);
        $this->assertEquals('n8n automation', $searchAnalytics[0]['query']);
        $this->assertEquals(5, $searchAnalytics[0]['results_count']);
    }

    /** @test */
    public function analytics_service_tracks_email_interactions()
    {
        $user = User::factory()->create();

        // Email envoyé
        $this->analyticsService->track($user->id, 'email_sent', [
            'email_type' => 'welcome',
            'template' => 'welcome-template'
        ]);

        // Email ouvert
        $this->analyticsService->track($user->id, 'email_opened', [
            'email_type' => 'welcome',
            'open_time' => now()
        ]);

        // Lien cliqué
        $this->analyticsService->track($user->id, 'email_clicked', [
            'email_type' => 'welcome',
            'link_url' => 'https://automatehub.fr/tutorials'
        ]);

        $emailStats = $this->analyticsService->getEmailStats(now()->subDay(), now());

        $this->assertArrayHasKey('sent', $emailStats);
        $this->assertArrayHasKey('opened', $emailStats);
        $this->assertArrayHasKey('clicked', $emailStats);
        $this->assertArrayHasKey('open_rate', $emailStats);
        $this->assertArrayHasKey('click_rate', $emailStats);
        
        $this->assertEquals(1, $emailStats['sent']);
        $this->assertEquals(1, $emailStats['opened']);
        $this->assertEquals(1, $emailStats['clicked']);
        $this->assertEquals(100, $emailStats['open_rate']);
        $this->assertEquals(100, $emailStats['click_rate']);
    }

    /** @test */
    public function analytics_service_handles_bulk_tracking()
    {
        $user = User::factory()->create();

        $events = [
            ['event_type' => 'tutorial_viewed', 'event_data' => ['tutorial_id' => 1]],
            ['event_type' => 'tutorial_viewed', 'event_data' => ['tutorial_id' => 2]],
            ['event_type' => 'download_completed', 'event_data' => ['file_name' => 'test.json']]
        ];

        $this->analyticsService->trackBulk($user->id, $events);

        $this->assertEquals(3, Analytics::where('user_id', $user->id)->count());
    }

    /** @test */
    public function analytics_service_respects_privacy_settings()
    {
        $user = User::factory()->create([
            'analytics_enabled' => false
        ]);

        $result = $this->analyticsService->track($user->id, 'tutorial_viewed', ['tutorial_id' => 1]);

        // Ne devrait pas tracker si l'utilisateur a désactivé les analytics
        $this->assertFalse($result);
        $this->assertEquals(0, Analytics::where('user_id', $user->id)->count());
    }

    /** @test */
    public function analytics_data_can_be_exported()
    {
        $user = User::factory()->create();

        $this->analyticsService->track($user->id, 'tutorial_viewed', ['tutorial_id' => 1]);
        $this->analyticsService->track($user->id, 'download_completed', ['file_name' => 'test.json']);

        $exportData = $this->analyticsService->exportUserData($user->id);

        $this->assertArrayHasKey('user_id', $exportData);
        $this->assertArrayHasKey('events', $exportData);
        $this->assertArrayHasKey('summary', $exportData);
        
        $this->assertCount(2, $exportData['events']);
    }

    /** @test */
    public function analytics_service_can_delete_user_data()
    {
        $user = User::factory()->create();

        $this->analyticsService->track($user->id, 'tutorial_viewed', ['tutorial_id' => 1]);
        $this->analyticsService->track($user->id, 'download_completed', ['file_name' => 'test.json']);

        $this->assertEquals(2, Analytics::where('user_id', $user->id)->count());

        $this->analyticsService->deleteUserData($user->id);

        $this->assertEquals(0, Analytics::where('user_id', $user->id)->count());
    }
}
