<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Tutorial;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TutorialModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que les attributs castés fonctionnent correctement
     */
    public function test_tutorial_casts_work(): void
    {
        $tutorial = new Tutorial([
            'title' => 'Test Tutorial',
            'files' => ['file1.json', 'file2.json'],
            'tags' => ['n8n', 'automation'],
            'is_draft' => true,
            'published_at' => now()
        ]);

        $this->assertIsArray($tutorial->files);
        $this->assertIsArray($tutorial->tags);
        $this->assertIsBool($tutorial->is_draft);
        $this->assertInstanceOf(\Carbon\Carbon::class, $tutorial->published_at);
    }

    /**
     * Test des scopes du modèle Tutorial
     */
    public function test_tutorial_scopes(): void
    {
        // Créer des tutorials pour les tests
        $publishedTutorial = Tutorial::factory()->create([
            'is_draft' => false,
            'published_at' => now()->subDay()
        ]);

        $draftTutorial = Tutorial::factory()->create([
            'is_draft' => true,
            'published_at' => null
        ]);

        $futureTutorial = Tutorial::factory()->create([
            'is_draft' => false,
            'published_at' => now()->addDay()
        ]);

        // Test du scope published
        $publishedTutorials = Tutorial::published()->get();
        $this->assertCount(1, $publishedTutorials);
        $this->assertEquals($publishedTutorial->id, $publishedTutorials->first()->id);

        // Test du scope draft
        $draftTutorials = Tutorial::draft()->get();
        $this->assertCount(1, $draftTutorials);
        $this->assertEquals($draftTutorial->id, $draftTutorials->first()->id);
    }

    /**
     * Test des méthodes d'état du Tutorial
     */
    public function test_tutorial_state_methods(): void
    {
        // Tutorial publié
        $publishedTutorial = new Tutorial([
            'is_draft' => false,
            'published_at' => now()->subDay()
        ]);
        $this->assertTrue($publishedTutorial->isPublished());

        // Tutorial draft
        $draftTutorial = new Tutorial([
            'is_draft' => true,
            'published_at' => null
        ]);
        $this->assertFalse($draftTutorial->isPublished());

        // Tutorial futur
        $futureTutorial = new Tutorial([
            'is_draft' => false,
            'published_at' => now()->addDay()
        ]);
        $this->assertFalse($futureTutorial->isPublished());
    }

    /**
     * Test des méthodes de subscription
     */
    public function test_tutorial_subscription_methods(): void
    {
        $freeTutorial = new Tutorial(['subscription_required' => 'free']);
        $this->assertTrue($freeTutorial->isFree());
        $this->assertFalse($freeTutorial->requiresPremium());
        $this->assertFalse($freeTutorial->requiresPro());

        $premiumTutorial = new Tutorial(['subscription_required' => 'premium']);
        $this->assertFalse($premiumTutorial->isFree());
        $this->assertTrue($premiumTutorial->requiresPremium());
        $this->assertFalse($premiumTutorial->requiresPro());

        $proTutorial = new Tutorial(['subscription_required' => 'pro']);
        $this->assertFalse($proTutorial->isFree());
        $this->assertFalse($proTutorial->requiresPremium());
        $this->assertTrue($proTutorial->requiresPro());
    }

    /**
     * Test des relations du modèle Tutorial
     */
    public function test_tutorial_relationships(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        
        $tutorial = Tutorial::factory()->create([
            'created_by' => $user->id,
            'category_id' => $category->id
        ]);

        // Test relation creator
        $this->assertInstanceOf(User::class, $tutorial->creator);
        $this->assertEquals($user->id, $tutorial->creator->id);

        // Test relation category
        $this->assertInstanceOf(Category::class, $tutorial->category);
        $this->assertEquals($category->id, $tutorial->category->id);
    }

    /**
     * Test de validation des attributs requis
     */
    public function test_tutorial_required_attributes(): void
    {
        $tutorial = new Tutorial();
        
        // Les attributs fillable sont définis
        $fillable = [
            'title', 'slug', 'description', 'content', 'category_id',
            'required_level', 'target_audience', 'subscription_required',
            'files', 'tags', 'published_at', 'is_draft', 'created_by'
        ];

        $this->assertEquals($fillable, $tutorial->getFillable());
    }
}
