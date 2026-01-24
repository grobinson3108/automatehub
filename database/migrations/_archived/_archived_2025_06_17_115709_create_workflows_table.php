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
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->string('n8n_id')->unique(); // ID from n8n
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('nodes'); // Workflow nodes
            $table->json('connections'); // Node connections
            $table->json('tags')->nullable(); // Workflow tags
            $table->boolean('active')->default(false);
            $table->boolean('is_template')->default(false); // For future template system
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Creator
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null'); // Category
            $table->string('difficulty_level')->nullable(); // beginner, intermediate, advanced
            $table->integer('download_count')->default(0);
            $table->decimal('rating', 3, 2)->nullable(); // 0.00 to 5.00
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamp('last_synced_at')->nullable(); // Last sync with n8n
            $table->timestamps();
            
            $table->index(['active', 'is_template']);
            $table->index(['category_id', 'difficulty_level']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};
