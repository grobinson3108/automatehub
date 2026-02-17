<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchtrend_user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('ai_mode', ['byok', 'managed'])->default('managed');
            $table->string('telegram_bot_token', 255)->nullable();
            $table->string('telegram_chat_id', 255)->nullable();
            $table->boolean('telegram_paused')->default(false);
            $table->boolean('onboarding_completed')->default(false);
            $table->enum('summary_language', ['fr', 'en'])->default('fr');
            $table->integer('items_per_page')->default(20);
            $table->timestamps();

            $table->unique(['user_id'], 'uk_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchtrend_user_settings');
    }
};
