<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('watchtrend_user_settings', function (Blueprint $table) {
            $table->string('slack_webhook_url', 500)->nullable()->after('telegram_paused');
            $table->string('discord_webhook_url', 500)->nullable()->after('slack_webhook_url');
        });
    }

    public function down(): void
    {
        Schema::table('watchtrend_user_settings', function (Blueprint $table) {
            $table->dropColumn(['slack_webhook_url', 'discord_webhook_url']);
        });
    }
};
