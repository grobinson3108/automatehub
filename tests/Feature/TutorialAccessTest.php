<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tutorial;
use App\Models\Category;
use App\Services\TutorialService;
use App\Services\RestrictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TutorialAccessTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $tutorialService;
    protected $restrictionService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tutorialService = app(TutorialService::class);
        $this->restrictionService = app(RestrictionService::class);

        // Créer une catégorie de test
        Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);
    }

    /** @test */
    public function free_user_can_access_free_tutorials()
    {
        $freeUser = User::factory()->create(['subscription_type' => 'free']);
        $freeTutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true
        ]);

        $canAccess = $this->tutorialService->canAccessTutorial($freeUser->id, $freeTutorial->id);
        $this->assertTrue($canAccess);

        // Test via HTTP
        $response = $this->actingAs($freeUser)
                         ->get("/user/tutorials/{$freeTutorial->id}");
        
        $response->assertStatus(200);
    }

    /** @test */
    public function free_user_cannot_access_premium_tutorials()
    {
        $freeUser = User::factory()->create(['subscription_type' => 'free']);
        $premiumTutorial = Tutorial::factory()->create([
            'subscription_required' => 'premium',
            'is_published' => true
        ]);

        $canAccess = $this->tutorialService->canAccessTutorial($freeUser->id, $premiumTutorial->id);
        $this->assertFalse($canAccess);

        // Test via HTTP - devrait rediriger vers upgrade
        $response = $this->actingAs($freeUser)
                         ->get("/user/tutorials/{$premiumTutorial->id}");
        
        $response->assertRedirect();
        $response->assertSessionHas('upgrade_prompt');
    }

    /** @test */
    public function premium_user_can_access_premium_tutorials()
    {
        $premiumUser = User::factory()->create(['subscription_type' => 'premium']);
        $premiumTutorial = Tutorial::factory()->create([
            'subscription_required' => 'premium',
            'is_published' => true
        ]);

        $canAccess = $this->tutorialService->canAccessTutorial($premiumUser->id, $premiumTutorial->id);
        $this->assertTrue($canAccess);

        $response = $this->actingAs($premiumUser)
                         ->get("/user/tutorials/{$premiumTutorial->id}");
        
        $response->assertStatus(200);
    }

    /** @test */
    public function premium_user_cannot_access_pro_tutorials()
    {
        $premiumUser = User::factory()->create(['subscription_type' => 'premium']);
        $proTutorial = Tutorial::factory()->create([
            'subscription_required' => 'pro',
            'is_published' => true
        ]);

        $canAccess = $this->tutorialService->canAccessTutorial($premiumUser->id, $proTutorial->id);
        $this->assertFalse($canAccess);

        $response = $this->actingAs($premiumUser)
                         ->get("/user/tutorials/{$proTutorial->id}");
        
        $response->assertRedirect();
    }

    /** @test */
    public function pro_user_can_access_all_tutorials()
    {
        $proUser = User::factory()->create(['subscription_type' => 'pro']);
        
        $freeTutorial = Tutorial::factory()->create(['subscription_required' => 'free', 'is_published' => true]);
        $premiumTutorial = Tutorial::factory()->create(['subscription_required' => 'premium', 'is_published' => true]);
        $proTutorial = Tutorial::factory()->create(['subscription_required' => 'pro', 'is_published' => true]);

        $this->assertTrue($this->tutorialService->canAccessTutorial($proUser->id, $freeTutorial->id));
        $this->assertTrue($this->tutorialService->canAccessTutorial($proUser->id, $premiumTutorial->id));
        $this->assertTrue($this->tutorialService->canAccessTutorial($proUser->id, $proTutorial->id));
    }

    /** @test */
    public function unpublished_tutorials_are_not_accessible()
    {
        $proUser = User::factory()->create(['subscription_type' => 'pro']);
        $unpublishedTutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => false
        ]);

        $canAccess = $this->tutorialService->canAccessTutorial($proUser->id, $unpublishedTutorial->id);
        $this->assertFalse($canAccess);

        $response = $this->actingAs($proUser)
                         ->get("/user/tutorials/{$unpublishedTutorial->id}");
        
        $response->assertStatus(404);
    }

    /** @test */
    public function tutorial_recommendations_respect_subscription_level()
    {
        $freeUser = User::factory()->create(['subscription_type' => 'free']);
        
        // Créer des tutoriels de différents niveaux
        Tutorial::factory()->create(['subscription_required' => 'free', 'is_published' => true]);
        Tutorial::factory()->create(['subscription_required' => 'premium', 'is_published' => true]);
        Tutorial::factory()->create(['subscription_required' => 'pro', 'is_published' => true]);

        $recommendations = $this->tutorialService->getRecommendations($freeUser->id, 10);

        // Vérifier que seuls les tutoriels gratuits sont recommandés
        foreach ($recommendations as $tutorial) {
            $this->assertEquals('free', $tutorial->subscription_required);
        }
    }

    /** @test */
    public function tutorial_listing_filters_by_subscription()
    {
        $freeUser = User::factory()->create(['subscription_type' => 'free']);
        
        Tutorial::factory()->count(3)->create(['subscription_required' => 'free', 'is_published' => true]);
        Tutorial::factory()->count(2)->create(['subscription_required' => 'premium', 'is_published' => true]);

        $response = $this->actingAs($freeUser)
                         ->get('/user/tutorials');

        $response->assertStatus(200);
        
        // Vérifier que seuls les tutoriels gratuits sont affichés
        $viewData = $response->viewData('tutorials');
        foreach ($viewData as $tutorial) {
            $this->assertEquals('free', $tutorial->subscription_required);
        }
    }

    /** @test */
    public function tutorial_search_respects_subscription_level()
    {
        $freeUser = User::factory()->create(['subscription_type' => 'free']);
        
        Tutorial::factory()->create([
            'title' => 'Free Tutorial Test',
            'subscription_required' => 'free',
            'is_published' => true
        ]);
        
        Tutorial::factory()->create([
            'title' => 'Premium Tutorial Test',
            'subscription_required' => 'premium',
            'is_published' => true
        ]);

        $response = $this->actingAs($freeUser)
                         ->get('/user/tutorials/search?q=Test');

        $response->assertStatus(200);
        
        // Vérifier que seul le tutoriel gratuit apparaît dans les résultats
        $results = $response->viewData('tutorials');
        $this->assertCount(1, $results);
        $this->assertEquals('Free Tutorial Test', $results->first()->title);
    }

    /** @test */
    public function tutorial_completion_is_tracked()
    {
        $user = User::factory()->create();
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true
        ]);

        $response = $this->actingAs($user)
                         ->post("/user/tutorials/{$tutorial->id}/complete");

        $response->assertStatus(200);

        // Vérifier que la progression est enregistrée
        $this->assertDatabaseHas('user_tutorial_progress', [
            'user_id' => $user->id,
            'tutorial_id' => $tutorial->id,
            'completed' => true
        ]);

        // Vérifier que l'événement analytics est créé
        $this->assertDatabaseHas('analytics', [
            'user_id' => $user->id,
            'event_type' => 'tutorial_completed'
        ]);
    }

    /** @test */
    public function tutorial_favorites_work_correctly()
    {
        $user = User::factory()->create();
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true
        ]);

        // Ajouter aux favoris
        $response = $this->actingAs($user)
                         ->post("/user/tutorials/{$tutorial->id}/favorite");

        $response->assertStatus(200);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'tutorial_id' => $tutorial->id
        ]);

        // Retirer des favoris
        $response = $this->actingAs($user)
                         ->delete("/user/tutorials/{$tutorial->id}/favorite");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'tutorial_id' => $tutorial->id
        ]);
    }

    /** @test */
    public function tutorial_view_triggers_analytics()
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
    }

    /** @test */
    public function tutorial_categories_filter_correctly()
    {
        $user = User::factory()->create(['subscription_type' => 'free']);
        $category = Category::first();
        
        $tutorial1 = Tutorial::factory()->create([
            'category_id' => $category->id,
            'subscription_required' => 'free',
            'is_published' => true
        ]);
        
        $tutorial2 = Tutorial::factory()->create([
            'category_id' => $category->id,
            'subscription_required' => 'premium',
            'is_published' => true
        ]);

        $response = $this->actingAs($user)
                         ->get("/user/tutorials/category/{$category->slug}");

        $response->assertStatus(200);
        
        // Vérifier que seul le tutoriel gratuit de cette catégorie est affiché
        $tutorials = $response->viewData('tutorials');
        $this->assertCount(1, $tutorials);
        $this->assertEquals($tutorial1->id, $tutorials->first()->id);
    }

    /** @test */
    public function guest_users_see_tutorial_previews_only()
    {
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true
        ]);

        $response = $this->get("/tutorials/{$tutorial->slug}");

        $response->assertStatus(200);
        $response->assertSee('Inscrivez-vous pour accéder');
        $response->assertSee('Aperçu gratuit');
    }

    /** @test */
    public function tutorial_difficulty_filtering_works()
    {
        $user = User::factory()->create(['subscription_type' => 'premium']);
        
        Tutorial::factory()->create([
            'difficulty' => 'beginner',
            'subscription_required' => 'free',
            'is_published' => true
        ]);
        
        Tutorial::factory()->create([
            'difficulty' => 'advanced',
            'subscription_required' => 'premium',
            'is_published' => true
        ]);

        $response = $this->actingAs($user)
                         ->get('/user/tutorials?difficulty=beginner');

        $response->assertStatus(200);
        
        $tutorials = $response->viewData('tutorials');
        foreach ($tutorials as $tutorial) {
            $this->assertEquals('beginner', $tutorial->difficulty);
        }
    }
}
