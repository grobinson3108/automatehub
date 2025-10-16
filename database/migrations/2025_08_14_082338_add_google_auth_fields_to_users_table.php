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
            // Only add fields that don't exist
            if (!Schema::hasColumn('users', 'subscription_expires_at')) {
                $table->timestamp('subscription_expires_at')->nullable()->after('subscription_type');
            }
            if (!Schema::hasColumn('users', 'onboarding_completed')) {
                $table->boolean('onboarding_completed')->default(false)->after('subscription_expires_at');
            }
            if (!Schema::hasColumn('users', 'last_email_sent_at')) {
                $table->timestamp('last_email_sent_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'email_notifications')) {
                $table->boolean('email_notifications')->default(true);
            }
            if (!Schema::hasColumn('users', 'weekly_digest')) {
                $table->boolean('weekly_digest')->default(true);
            }
            
            // Update subscription_type enum to match existing
            \DB::statement("ALTER TABLE users MODIFY subscription_type ENUM('free', 'freemium', 'premium', 'pro') DEFAULT 'free'");
            
            // Add index if column exists
            if (Schema::hasColumn('users', 'google_id') && !collect(\DB::select("SHOW INDEXES FROM users"))->pluck('Key_name')->contains('users_google_id_index')) {
                $table->index('google_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_expires_at',
                'onboarding_completed',
                'last_email_sent_at',
                'email_notifications',
                'weekly_digest'
            ]);
        });
    }
};
