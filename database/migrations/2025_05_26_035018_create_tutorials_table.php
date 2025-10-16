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
        Schema::create('tutorials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('content');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->enum('required_level', ['beginner', 'intermediate', 'expert'])->default('beginner');
            $table->enum('target_audience', ['all', 'professional', 'personal'])->default('all');
            $table->enum('subscription_required', ['free', 'premium', 'pro'])->default('free');
            $table->json('files')->nullable(); // Stockage des fichiers associés
            $table->json('tags')->nullable(); // Tags sous forme JSON
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_draft')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['published_at', 'is_draft']);
            $table->index('required_level');
            $table->index('target_audience');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutorials');
    }
};
