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
        Schema::create('cookie_consents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id', 100)->index();
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            
            // Types de cookies acceptés
            $table->boolean('essential_cookies')->default(true);
            $table->boolean('analytics_cookies')->default(false);
            $table->boolean('marketing_cookies')->default(false);
            $table->boolean('preferences_cookies')->default(false);
            
            // Métadonnées de consentement
            $table->timestamp('consent_timestamp');
            $table->timestamps();
            
            // Index composé pour optimiser les requêtes
            $table->index(['user_id', 'session_id']);
            $table->index(['created_at']);
            
            // Contrainte de clé étrangère
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cookie_consents');
    }
};