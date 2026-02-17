<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchtrend_watches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('icon', 10)->nullable();
            $table->enum('collection_frequency', ['daily', 'weekly', 'monthly', 'quarterly'])->default('daily');
            $table->enum('digest_frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'disabled'])->default('weekly');
            $table->tinyInteger('digest_hour')->default(8);
            $table->boolean('telegram_enabled')->default(false);
            $table->enum('ai_mode', ['byok', 'managed'])->default('managed');
            $table->enum('summary_language', ['fr', 'en'])->default('fr');
            $table->boolean('show_low_relevance')->default(true);
            $table->enum('status', ['active', 'paused', 'archived'])->default('active');
            $table->timestamp('calibration_completed_at')->nullable();
            $table->timestamp('last_collected_at')->nullable();
            $table->timestamp('last_digest_sent_at')->nullable();
            $table->integer('items_per_page')->default(20);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status'], 'idx_user_status');
            $table->index(['status', 'collection_frequency', 'last_collected_at'], 'idx_collection_due');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchtrend_watches');
    }
};
