<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchtrend_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collected_item_id')->constrained('watchtrend_collected_items')->cascadeOnDelete();
            $table->foreignId('watch_id')->constrained('watchtrend_watches')->cascadeOnDelete();
            $table->decimal('relevance_score', 3, 2)->nullable();
            $table->enum('category', ['critical_update', 'trend', 'worth_watching', 'low_relevance'])->nullable();
            $table->text('summary_fr')->nullable();
            $table->text('actionable_insight')->nullable();
            $table->json('matching_interests')->nullable();
            $table->json('key_takeaways')->nullable();
            $table->string('ai_model', 50)->nullable();
            $table->enum('ai_mode', ['byok', 'managed']);
            $table->integer('tokens_used')->default(0);
            $table->decimal('credits_used', 8, 4)->default(0);
            $table->timestamp('created_at');

            $table->unique(['collected_item_id'], 'uk_item');
            $table->index(['watch_id', 'relevance_score'], 'idx_watch_score');
            $table->index(['watch_id', 'category'], 'idx_watch_category');
            $table->index(['created_at'], 'idx_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchtrend_analyses');
    }
};
