<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Badge;
use App\Models\Tutorial;
use App\Models\Download;
use App\Models\UserTutorialProgress;
use App\Services\BadgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BadgeSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $badgeService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->badgeService = app(BadgeService::class);
        
        // Créer les badges de test
        $this->createTestBadges();
    }

    private function createTestBadges()
    {
        // Badge d'inscription
        Badge::factory()->create([
            'name' => 'Bienvenue',
            'description' => 'Premier pas sur AutomateHub',
            'type' => 'registration',
            'criteria' => json_encode(['action' => 'register']),
            'icon' => 'welcome.svg'
        ]);

        // Badge niveau n8n
        Badge::factory()->create([
            'name' => 'Débutant n8n',
            'description' => 'Niveau débutant en n8n',
            'type' => 'n8n_level',
            'criteria' => json_encode(['n8n_level' => 'beginner']),
            'icon' => 'beginner.svg'
        ]);

        Badge::factory()->create([
            'name' => 'Expert n8n',
            'description' => 'Niveau expert en n8n',
            'type' => 'n8n_level',
            'criteria' => json_encode(['n8n_level' => 'advanced']),
            'icon' => 'expert.svg'
        ]);

        // Badges de progression
        Badge::factory()->create([
            'name' => 'Premier Tutoriel',
            'description' => 'Premier tutoriel complété',
            'type' => 'tutorial_completion',
            'criteria' => json_encode(['tutorials_completed' => 1]),
            'icon' => 'first-tutorial.svg'
        ]);

        Badge::factory()->create([
            'name' => 'Apprenant Assidu',
            'description' => '5 tutoriels complétés',
            'type' => 'tutorial_completion',
            'criteria' => json_encode(['tutorials_completed' => 5]),
            'icon' => 'learner.svg'
        ]);

        Badge::factory()->create([
            'name' => 'Maître des Workflows',
            'description' => '20 tutoriels complétés',
            'type' => 'tutorial_completion',
            'criteria' => json_encode(['tutorials_completed' => 20]),
            'icon' => 'master.svg'
        ]);

        // Badges de téléchargement
        Badge::factory()->create([
            'name' => 'Premier Téléchargement',
            'description' => 'Premier fichier téléchargé',
            'type' => 'download',
            'criteria' => json_encode(['downloads_count' => 1]),
            'icon' => 'first-download.svg'
        ]);

        Badge::factory()->create([
            'name' => 'Collectionneur',
            'description' => '10 téléchargements effectués',
            'type' => 'download',
            'criteria' => json_encode(['downloads_count' => 10]),
            'icon' => 'collector.svg'
        ]);

        // Badge d'engagement
        Badge::factory()->create([
            'name' => 'Utilisateur Actif',
            'description' => 'Actif pendant 7 jours consécutifs',
            'type' => 'engagement',
            'criteria' => json_encode(['consecutive_days' => 7]),
            'icon' => 'active-user.svg'
        ]);

        // Badge spécial
        Badge::factory()->create([
            'name' => 'Pionnier',
            'description' => 'Parmi les 100 premiers utilisateurs',
            'type' => 'special',
            'criteria' => json_encode(['user_rank' => 100]),
            'icon' => 'pioneer.svg'
        ]);
    }

    /** @test */
    public function registration_badge_is_awarded_automatically()
    {
        $user = User::factory()->create();

        // Déclencher la vérification des badges
        $newBadges = $this->badgeService->checkAndAwardBadges($user->id);

        // Vérifier que le badge de bienvenue est attribué
        $this->assertCount(2, $newBadges); // Bienvenue + niveau n8n
        $this->assertTrue($user->badges()->where('name', 'Bienvenue')->exists());
    }

    /** @test */
    public function n8n_level_badge_is_awarded_based_on_user_level()
    {
        $beginnerUser = User::factory()->create(['n8n_level' => 'beginner']);
        $advancedUser = User::factory()->create(['n8n_level' => 'advanced']);

        $this->badgeService->checkAndAwardBadges($beginnerUser->id);
        $this->badgeService->checkAndAwardBadges($advancedUser->id);

        $this->assertTrue($beginnerUser->badges()->where('name', 'Débutant n8n')->exists());
        $this->assertTrue($advancedUser->badges()->where('name', 'Expert n8n')->exists());
        
        // L'utilisateur avancé ne devrait pas avoir le badge débutant
        $this->assertFalse($advancedUser->badges()->where('name', 'Débutant n8n')->exists());
    }

    /** @test */
    public function tutorial_completion_badges_are_awarded_progressively()
    {
        $user = User::factory()->create();
        $tutorials = Tutorial::factory()->count(25)->create([
            'subscription_required' => 'free',
            'is_published' => true
        ]);

        // Compléter 1 tutoriel
        UserTutorialProgress::factory()->create([
            'user_id' => $user->id,
            'tutorial_id' => $tutorials[0]->id,
            'completed' => true,
            'completed_at' => now()
        ]);

        $newBadges = $this->badgeService->checkAndAwardBadges($user->id);
        $this->assertTrue($user->badges()->where('name', 'Premier Tutoriel')->exists());

        // Compléter 5 tutoriels au total
        for ($i = 1; $i < 5; $i++) {
            UserTutorialProgress::factory()->create([
                'user_id' => $user->id,
                'tutorial_id' => $tutorials[$i]->id,
                'completed' => true,
                'completed_at' => now()
            ]);
        }

        $this->badgeService->checkAndAwardBadges($user->id);
        $this->assertTrue($user->badges()->where('name', 'Apprenant Assidu')->exists());

        // Compléter 20 tutoriels au total
        for ($i = 5; $i < 20; $i++) {
            UserTutorialProgress::factory()->create([
                'user_id' => $user->id,
                'tutorial_id' => $tutorials[$i]->id,
                'completed' => true,
                'completed_at' => now()
            ]);
        }

        $this->badgeService->checkAndAwardBadges($user->id);
        $this->assertTrue($user->badges()->where('name', 'Maître des Workflows')->exists());
    }

    /** @test */
    public function download_badges_are_awarded_based_on_download_count()
    {
        $user = User::factory()->create();

        // Premier téléchargement
        Download::factory()->create(['user_id' => $user->id]);
        
        $this->badgeService->checkAndAwardBadges($user->id);
        $this->assertTrue($user->badges()->where('name', 'Premier Téléchargement')->exists());

        // 10 téléchargements au total
        Download::factory()->count(9)->create(['user_id' => $user->id]);
        
        $this->badgeService->checkAndAwardBadges($user->id);
        $this->assertTrue($user->badges()->where('name', 'Collectionneur')->exists());
    }

    /** @test */
    public function badges_are_not_awarded_twice()
    {
        $user = User::factory()->create();

        // Première vérification
        $firstCheck = $this->badgeService->checkAndAwardBadges($user->id);
        $initialBadgeCount = $user->badges()->count();

        // Deuxième vérification - ne devrait pas attribuer de nouveaux badges
        $secondCheck = $this->badgeService->checkAndAwardBadges($user->id);
        
        $this->assertEmpty($secondCheck);
        $this->assertEquals($initialBadgeCount, $user->badges()->count());
    }

    /** @test */
    public function badge_service_returns_available_badges_correctly()
    {
        $user = User::factory()->create(['n8n_level' => 'beginner']);
        
        // Créer quelques progressions
        UserTutorialProgress::factory()->count(3)->create([
            'user_id' => $user->id,
            'completed' => true,
            'completed_at' => now()
        ]);

        $badgeStatus = $this->badgeService->getAvailableBadges($user->id);

        // Vérifier la structure de la réponse
        $this->assertArrayHasKey('earned', $badgeStatus);
        $this->assertArrayHasKey('available', $badgeStatus);
        $this->assertArrayHasKey('locked', $badgeStatus);

        // L'utilisateur devrait avoir des badges disponibles
        $this->assertNotEmpty($badgeStatus['available']);
    }

    /** @test */
    public function pioneer_badge_is_awarded_to_early_users()
    {
        // Créer 99 utilisateurs existants
        User::factory()->count(99)->create();
        
        // Le 100ème utilisateur devrait obtenir le badge pionnier
        $user = User::factory()->create();
        
        $this->badgeService->checkAndAwardBadges($user->id);
        $this->assertTrue($user->badges()->where('name', 'Pionnier')->exists());

        // Le 101ème utilisateur ne devrait pas l'obtenir
        $laterUser = User::factory()->create();
        $this->badgeService->checkAndAwardBadges($laterUser->id);
        $this->assertFalse($laterUser->badges()->where('name', 'Pionnier')->exists());
    }

    /** @test */
    public function badge_notification_is_sent_when_earned()
    {
        $this->expectsJobs(\App\Jobs\SendWelcomeEmailJob::class);

        $user = User::factory()->create();
        
        // Simuler l'attribution d'un badge via l'événement
        event(new \App\Events\UserRegistered($user));
    }

    /** @test */
    public function badge_progress_is_calculated_correctly()
    {
        $user = User::factory()->create();
        
        // Créer 3 tutoriels complétés (sur 5 requis pour le badge)
        UserTutorialProgress::factory()->count(3)->create([
            'user_id' => $user->id,
            'completed' => true,
            'completed_at' => now()
        ]);

        $progress = $this->badgeService->getBadgeProgress($user->id, 'Apprenant Assidu');
        
        $this->assertEquals(3, $progress['current']);
        $this->assertEquals(5, $progress['required']);
        $this->assertEquals(60, $progress['percentage']); // 3/5 * 100
    }

    /** @test */
    public function user_badge_statistics_are_accurate()
    {
        $user = User::factory()->create();
        
        // Attribuer quelques badges
        $this->badgeService->checkAndAwardBadges($user->id);
        
        UserTutorialProgress::factory()->create([
            'user_id' => $user->id,
            'completed' => true,
            'completed_at' => now()
        ]);
        
        $this->badgeService->checkAndAwardBadges($user->id);

        $stats = $this->badgeService->getUserBadgeStats($user->id);
        
        $this->assertArrayHasKey('total_earned', $stats);
        $this->assertArrayHasKey('total_available', $stats);
        $this->assertArrayHasKey('completion_percentage', $stats);
        $this->assertArrayHasKey('recent_badges', $stats);
        
        $this->assertGreaterThan(0, $stats['total_earned']);
    }

    /** @test */
    public function badge_leaderboard_works_correctly()
    {
        // Créer plusieurs utilisateurs avec différents nombres de badges
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // Attribuer des badges
        $this->badgeService->checkAndAwardBadges($user1->id);
        $this->badgeService->checkAndAwardBadges($user2->id);
        $this->badgeService->checkAndAwardBadges($user3->id);

        // Donner plus de badges à user1
        UserTutorialProgress::factory()->count(5)->create([
            'user_id' => $user1->id,
            'completed' => true,
            'completed_at' => now()
        ]);
        $this->badgeService->checkAndAwardBadges($user1->id);

        $leaderboard = $this->badgeService->getBadgeLeaderboard(10);
        
        $this->assertNotEmpty($leaderboard);
        $this->assertEquals($user1->id, $leaderboard[0]['user_id']);
    }

    /** @test */
    public function special_event_badges_can_be_awarded()
    {
        // Créer un badge d'événement spécial
        $eventBadge = Badge::factory()->create([
            'name' => 'Participant Beta',
            'description' => 'Participant au programme beta',
            'type' => 'special_event',
            'criteria' => json_encode(['event' => 'beta_program']),
            'icon' => 'beta.svg'
        ]);

        $user = User::factory()->create();

        // Attribuer manuellement le badge d'événement
        $result = $this->badgeService->awardSpecialBadge($user->id, $eventBadge->id);
        
        $this->assertTrue($result);
        $this->assertTrue($user->badges()->where('id', $eventBadge->id)->exists());
    }

    /** @test */
    public function badge_criteria_validation_works()
    {
        $user = User::factory()->create();

        // Tester différents critères
        $registrationBadge = Badge::where('name', 'Bienvenue')->first();
        $this->assertTrue($this->badgeService->checkBadgeCriteria($user, $registrationBadge));

        $tutorialBadge = Badge::where('name', 'Premier Tutoriel')->first();
        $this->assertFalse($this->badgeService->checkBadgeCriteria($user, $tutorialBadge));

        // Après avoir complété un tutoriel
        UserTutorialProgress::factory()->create([
            'user_id' => $user->id,
            'completed' => true,
            'completed_at' => now()
        ]);

        $this->assertTrue($this->badgeService->checkBadgeCriteria($user, $tutorialBadge));
    }

    /** @test */
    public function badge_analytics_are_tracked()
    {
        $user = User::factory()->create();
        
        $this->badgeService->checkAndAwardBadges($user->id);

        // Vérifier que l'attribution de badge est trackée dans les analytics
        $this->assertDatabaseHas('analytics', [
            'user_id' => $user->id,
            'event_type' => 'badge_earned'
        ]);
    }
}
