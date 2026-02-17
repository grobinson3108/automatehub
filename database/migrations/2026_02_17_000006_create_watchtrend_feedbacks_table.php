<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchtrend_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watch_id')->constrained('watchtrend_watches')->cascadeOnDelete();
            $table->foreignId('analysis_id')->constrained('watchtrend_analyses')->cascadeOnDelete();
            $table->tinyInteger('rating');
            $table->enum('source_channel', ['web', 'telegram', 'onboarding'])->default('web');
            $table->timestamps();

            $table->unique(['watch_id', 'analysis_id'], 'uk_watch_analysis');
            $table->index(['watch_id', 'rating'], 'idx_watch_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchtrend_feedbacks');
    }
};
