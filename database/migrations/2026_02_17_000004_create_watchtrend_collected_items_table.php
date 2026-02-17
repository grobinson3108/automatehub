<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchtrend_collected_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('watchtrend_sources')->cascadeOnDelete();
            $table->foreignId('watch_id')->constrained('watchtrend_watches')->cascadeOnDelete();
            $table->string('external_id', 255)->nullable();
            $table->text('url')->nullable();
            $table->string('title', 500);
            $table->text('content')->nullable();
            $table->string('author', 255)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->json('metadata')->nullable();
            $table->string('content_hash', 64);
            $table->boolean('is_analyzed')->default(false);
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->unique(['content_hash', 'watch_id'], 'uk_content_hash_watch');
            $table->index(['watch_id', 'is_analyzed'], 'idx_watch_analyzed');
            $table->index(['watch_id', 'is_read'], 'idx_watch_read');
            $table->index(['source_id'], 'idx_source');
            $table->index(['published_at'], 'idx_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchtrend_collected_items');
    }
};
