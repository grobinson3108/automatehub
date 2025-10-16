<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('video_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_content_plan_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // youtube, tiktok, linkedin, instagram, facebook
            $table->integer('video_index')->default(0); // Index de la vidéo dans le plan (0, 1, 2...)

            // Contenu optimisé par plateforme
            $table->string('title');
            $table->text('description');
            $table->json('hashtags')->nullable(); // Tags/hashtags spécifiques
            $table->text('caption')->nullable(); // Caption pour réseaux sociaux
            $table->string('thumbnail_concept')->nullable();
            $table->text('hooks')->nullable(); // Plusieurs hooks possibles

            // Planification
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->enum('status', ['planned', 'filmed', 'edited', 'published', 'cancelled'])->default('planned');
            $table->string('frequency_type')->nullable(); // daily, weekly, monthly
            $table->integer('frequency_interval')->default(1); // Tous les X jours/semaines

            // Métadonnées
            $table->string('target_audience')->nullable();
            $table->string('call_to_action')->nullable();
            $table->integer('estimated_views')->nullable();
            $table->decimal('engagement_rate_target', 5, 2)->nullable();

            // Résultats (à remplir après publication)
            $table->string('published_url')->nullable();
            $table->date('published_date')->nullable();
            $table->integer('actual_views')->nullable();
            $table->integer('likes')->nullable();
            $table->integer('comments')->nullable();
            $table->integer('shares')->nullable();
            $table->decimal('actual_engagement_rate', 5, 2)->nullable();

            // Notes et optimisations
            $table->text('notes')->nullable();
            $table->json('optimization_tips')->nullable(); // Conseils d'optimisation

            $table->timestamps();

            // Index pour les requêtes fréquentes
            $table->index(['platform', 'scheduled_date']);
            $table->index(['status', 'scheduled_date']);
            $table->index(['video_content_plan_id', 'platform']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_publications');
    }
};