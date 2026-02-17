<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchtrend_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watch_id')->constrained('watchtrend_watches')->cascadeOnDelete();
            $table->string('name', 255);
            $table->json('keywords');
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->text('context_description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['watch_id', 'is_active'], 'idx_watch_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchtrend_interests');
    }
};
