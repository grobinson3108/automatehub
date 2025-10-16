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
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // 'view', 'download', 'complete', 'favorite', etc.
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('tutorial_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('metadata')->nullable(); // Données supplémentaires de l'événement
            $table->timestamp('created_at');
            
            $table->index(['event_type', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['tutorial_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
