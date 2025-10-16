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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('level_n8n', ['beginner', 'intermediate', 'expert'])->default('beginner')->after('email_verified_at');
            $table->enum('subscription_type', ['free', 'premium', 'pro'])->default('free')->after('level_n8n');
            $table->boolean('is_admin')->default(false)->after('subscription_type');
            $table->string('phone')->nullable()->after('company_vat');
            $table->timestamp('last_activity_at')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'level_n8n',
                'subscription_type',
                'is_admin',
                'phone',
                'last_activity_at'
            ]);
        });
    }
};
