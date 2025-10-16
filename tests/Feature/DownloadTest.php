<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tutorial;
use App\Models\Download;
use App\Services\RestrictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $restrictionService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->restrictionService = app(RestrictionService::class);
        
        // Créer le dossier de stockage pour les tests
        Storage::fake('local');
        Storage::disk('local')->makeDirectory('tutorials');
    }

    /** @test */
    public function free_user_can_download_within_monthly_limit()
    {
        $freeUser = User::factory()->create(['subscription_type' => 'free']);
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true,
            'files' => json_encode(['workflow.json', 'guide.pdf'])
        ]);

        // Créer des fichiers de test
        Storage::disk('local')->put('tutorials/workflow.json', 'test content');
        Storage::disk('local')->put('tutorials/guide.pdf', 'test pdf content');

        // Premier téléchargement - devrait réussir
        $response = $this->actingAs($freeUser)
                         ->post("/user/downloads/{$tutorial->id}", [
                             'file' => 'workflow.json'
                         ]);

        $response->assertStatus(200);
        
        // Vérifier que le téléchargement est enregistré
        $this->assertDatabaseHas('downloads', [
            'user_id' => $freeUser->id,
            'tutorial_id' => $tutorial->id,
            'file_name' => 'workflow.json'
        ]);

        // Vérifier que l'analytics est créé
        $this->assertDatabaseHas('analytics', [
            'user_id' => $freeUser->id,
            'event_type' => 'download_completed'
        ]);
    }

    /** @test */
    public function free_user_cannot_exceed_monthly_download_limit()
    {
        $freeUser = User::factory()->create(['subscription_type' => 'free']);
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true,
            'files' => json_encode(['file1.json', 'file2.json', 'file3.json', 'file4.json'])
        ]);

        // Créer 3 téléchargements (limite pour free)
        Download::factory()->count(3)->create([
            'user_id' => $freeUser->id,
            'created_at' => now()
        ]);

        // Tenter un 4ème téléchargement - devrait échouer
        $response = $this->actingAs($freeUser)
                         ->post("/user/downloads/{$tutorial->id}", [
                             'file' => 'file4.json'
                         ]);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Limite mensuelle atteinte']);
        
        // Vérifier qu'aucun nouveau téléchargement n'est créé
        $this->assertEquals(3, Download::where('user_id', $freeUser->id)->count());
    }

    /** @test */
    public function premium_user_has_unlimited_downloads()
    {
        $premiumUser = User::factory()->create(['subscription_type' => 'premium']);
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'premium',
            'is_published' => true,
            'files' => json_encode(['file.json'])
        ]);

        // Créer beaucoup de téléchargements existants
        Download::factory()->count(50)->create([
            'user_id' => $premiumUser->id,
            'created_at' => now()
        ]);

        Storage::disk('local')->put('tutorials/file.json', 'test content');

        // Devrait toujours pouvoir télécharger
        $response = $this->actingAs($premiumUser)
                         ->post("/user/downloads/{$tutorial->id}", [
                             'file' => 'file.json'
                         ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function pro_user_has_unlimited_downloads()
    {
        $proUser = User::factory()->create(['subscription_type' => 'pro']);
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'pro',
            'is_published' => true,
            'files' => json_encode(['file.json'])
        ]);

        // Créer beaucoup de téléchargements existants
        Download::factory()->count(100)->create([
            'user_id' => $proUser->id,
            'created_at' => now()
        ]);

        Storage::disk('local')->put('tutorials/file.json', 'test content');

        $response = $this->actingAs($proUser)
                         ->post("/user/downloads/{$tutorial->id}", [
                             'file' => 'file.json'
                         ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function user_cannot_download_from_inaccessible_tutorial()
    {
        $freeUser = User::factory()->create(['subscription_type' => 'free']);
        $premiumTutorial = Tutorial::factory()->create([
            'subscription_required' => 'premium',
            'is_published' => true,
            'files' => json_encode(['file.json'])
        ]);

        $response = $this->actingAs($freeUser)
                         ->post("/user/downloads/{$premiumTutorial->id}", [
                             'file' => 'file.json'
                         ]);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Accès non autorisé à ce tutoriel']);
    }

    /** @test */
    public function user_cannot_download_nonexistent_file()
    {
        $user = User::factory()->create();
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true,
            'files' => json_encode(['existing.json'])
        ]);

        $response = $this->actingAs($user)
                         ->post("/user/downloads/{$tutorial->id}", [
                             'file' => 'nonexistent.json'
                         ]);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Fichier non trouvé']);
    }

    /** @test */
    public function download_restrictions_are_checked_correctly()
    {
        $freeUser = User::factory()->create(['subscription_type' => 'free']);

        // Tester canDownload avec utilisateur free sans téléchargements
        $result = $this->restrictionService->canDownload($freeUser->id);
        $this->assertTrue($result['can_download']);

        // Créer 3 téléchargements (limite)
        Download::factory()->count(3)->create([
            'user_id' => $freeUser->id,
            'created_at' => now()
        ]);

        // Maintenant devrait être bloqué
        $result = $this->restrictionService->canDownload($freeUser->id);
        $this->assertFalse($result['can_download']);
        $this->assertEquals('monthly', $result['limit_type']);
    }

    /** @test */
    public function remaining_downloads_are_calculated_correctly()
    {
        $freeUser = User::factory()->create(['subscription_type' => 'free']);

        // Aucun téléchargement
        $remaining = $this->restrictionService->getRemainingDownloads($freeUser->id);
        $this->assertEquals(3, $remaining['monthly']['remaining']);

        // Après 1 téléchargement
        Download::factory()->create([
            'user_id' => $freeUser->id,
            'created_at' => now()
        ]);

        $remaining = $this->restrictionService->getRemainingDownloads($freeUser->id);
        $this->assertEquals(2, $remaining['monthly']['remaining']);
        $this->assertEquals(1, $remaining['monthly']['used']);
    }

    /** @test */
    public function download_triggers_badge_check()
    {
        $this->expectsJobs(\App\Jobs\CheckBadgesJob::class);

        $user = User::factory()->create();
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true,
            'files' => json_encode(['file.json'])
        ]);

        Storage::disk('local')->put('tutorials/file.json', 'test content');

        $this->actingAs($user)
             ->post("/user/downloads/{$tutorial->id}", [
                 'file' => 'file.json'
             ]);
    }

    /** @test */
    public function download_history_is_accessible()
    {
        $user = User::factory()->create();
        
        // Créer quelques téléchargements
        $downloads = Download::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
                         ->get('/user/downloads');

        $response->assertStatus(200);
        
        // Vérifier que les téléchargements sont affichés
        foreach ($downloads as $download) {
            $response->assertSee($download->file_name);
        }
    }

    /** @test */
    public function download_statistics_are_tracked()
    {
        $user = User::factory()->create();
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true,
            'files' => json_encode(['file.json'])
        ]);

        Storage::disk('local')->put('tutorials/file.json', 'test content');

        $this->actingAs($user)
             ->post("/user/downloads/{$tutorial->id}", [
                 'file' => 'file.json'
             ]);

        // Vérifier que les statistiques sont mises à jour
        $tutorial->refresh();
        $this->assertEquals(1, $tutorial->downloads_count);
    }

    /** @test */
    public function bulk_download_respects_limits()
    {
        $freeUser = User::factory()->create(['subscription_type' => 'free']);
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true,
            'files' => json_encode(['file1.json', 'file2.json', 'file3.json', 'file4.json'])
        ]);

        // Créer 2 téléchargements existants
        Download::factory()->count(2)->create([
            'user_id' => $freeUser->id,
            'created_at' => now()
        ]);

        // Tenter de télécharger 2 fichiers (dépasserait la limite)
        $response = $this->actingAs($freeUser)
                         ->post("/user/downloads/{$tutorial->id}/bulk", [
                             'files' => ['file3.json', 'file4.json']
                         ]);

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Limite de téléchargement dépassée']);
    }

    /** @test */
    public function download_links_are_secure()
    {
        $user = User::factory()->create();
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true,
            'files' => json_encode(['secure-file.json'])
        ]);

        Storage::disk('local')->put('tutorials/secure-file.json', 'sensitive content');

        // Générer un lien de téléchargement sécurisé
        $response = $this->actingAs($user)
                         ->post("/user/downloads/{$tutorial->id}/generate-link", [
                             'file' => 'secure-file.json'
                         ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['download_url', 'expires_at']);

        $downloadUrl = $response->json('download_url');
        
        // Vérifier que le lien contient un token
        $this->assertStringContainsString('token=', $downloadUrl);
    }

    /** @test */
    public function expired_download_links_are_rejected()
    {
        $user = User::factory()->create();
        
        // Simuler un lien expiré
        $expiredToken = encrypt([
            'user_id' => $user->id,
            'file' => 'test.json',
            'expires_at' => now()->subHour()
        ]);

        $response = $this->get("/downloads/secure?token={$expiredToken}");

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Lien de téléchargement expiré']);
    }

    /** @test */
    public function download_limits_reset_monthly()
    {
        $freeUser = User::factory()->create(['subscription_type' => 'free']);

        // Créer des téléchargements du mois précédent
        Download::factory()->count(3)->create([
            'user_id' => $freeUser->id,
            'created_at' => now()->subMonth()
        ]);

        // Vérifier que l'utilisateur peut télécharger ce mois
        $result = $this->restrictionService->canDownload($freeUser->id);
        $this->assertTrue($result['can_download']);

        $remaining = $this->restrictionService->getRemainingDownloads($freeUser->id);
        $this->assertEquals(3, $remaining['monthly']['remaining']);
        $this->assertEquals(0, $remaining['monthly']['used']);
    }

    /** @test */
    public function download_analytics_include_file_details()
    {
        $user = User::factory()->create();
        $tutorial = Tutorial::factory()->create([
            'subscription_required' => 'free',
            'is_published' => true,
            'files' => json_encode(['analytics-test.json'])
        ]);

        Storage::disk('local')->put('tutorials/analytics-test.json', 'test content');

        $this->actingAs($user)
             ->post("/user/downloads/{$tutorial->id}", [
                 'file' => 'analytics-test.json'
             ]);

        // Vérifier que l'analytics contient les détails du fichier
        $this->assertDatabaseHas('analytics', [
            'user_id' => $user->id,
            'event_type' => 'download_completed'
        ]);

        $analytics = \App\Models\Analytics::where('user_id', $user->id)
                                         ->where('event_type', 'download_completed')
                                         ->first();

        $eventData = json_decode($analytics->event_data, true);
        $this->assertEquals('analytics-test.json', $eventData['file_name']);
        $this->assertEquals($tutorial->id, $eventData['tutorial_id']);
    }
}
