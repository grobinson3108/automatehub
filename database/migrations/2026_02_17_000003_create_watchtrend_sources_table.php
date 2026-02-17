<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchtrend_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watch_id')->constrained('watchtrend_watches')->cascadeOnDelete();
            $table->enum('type', ['youtube', 'reddit', 'rss', 'hackernews', 'github', 'twitter']);
            $table->string('name', 255);
            $table->json('config')->nullable();
            $table->enum('status', ['active', 'paused', 'error'])->default('active');
            $table->text('error_message')->nullable();
            $table->integer('error_count')->default(0);
            $table->timestamp('last_collected_at')->nullable();
            $table->integer('items_collected_total')->default(0);
            $table->timestamps();

            $table->index(['watch_id', 'status'], 'idx_watch_status');
            $table->index(['type'], 'idx_type');
            $table->index(['last_collected_at'], 'idx_last_collected');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchtrend_sources');
    }
};
