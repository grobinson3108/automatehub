<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchtrend_pain_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watch_id')->constrained('watchtrend_watches')->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->enum('status', ['active', 'resolved'])->default('active');
            $table->timestamps();

            $table->index(['watch_id', 'status'], 'idx_watch_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchtrend_pain_points');
    }
};
