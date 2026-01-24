<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des mini-apps disponibles
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // postmaid, videoplan, referail, etc.
            $table->string('name'); // "PostMaid", "VideoPlan", etc.
            $table->text('description');
            $table->text('tagline')->nullable(); // Short tagline for cards
            $table->string('icon')->nullable(); // Icon/logo path
            $table->string('category'); // social-media, video, crm, etc.
            $table->json('features')->nullable(); // Liste des fonctionnalités
            $table->json('required_integrations')->nullable(); // ['openai', 'instagram', 'tiktok']
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('coming_soon'); // coming_soon, beta, active
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Table des plans tarifaires par app
        Schema::create('app_pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->onDelete('cascade');
            $table->string('name'); // Solo, Pro, Business
            $table->decimal('monthly_price', 10, 2);
            $table->decimal('yearly_price', 10, 2)->nullable(); // Prix annuel (avec discount)
            $table->json('features')->nullable(); // Features incluses dans ce plan
            $table->json('limits')->nullable(); // Limites (posts/mois, videos/mois, etc.)
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('stripe_price_id_monthly')->nullable();
            $table->string('stripe_price_id_yearly')->nullable();
            $table->timestamps();
        });

        // Souscriptions utilisateurs aux apps
        Schema::create('user_app_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('app_id')->constrained('apps')->onDelete('cascade');
            $table->foreignId('pricing_plan_id')->constrained('app_pricing_plans');
            $table->enum('billing_period', ['monthly', 'yearly'])->default('monthly');
            $table->enum('status', ['active', 'suspended', 'cancelled', 'trial'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'app_id']);
            $table->index(['user_id', 'status']);
        });

        // Credentials utilisateur pour intégrations tierces
        Schema::create('user_app_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('app_slug'); // postmaid, videoplan, etc.
            $table->string('service'); // openai, instagram, tiktok, linkedin, smtp, etc.
            $table->enum('type', ['api_key', 'oauth', 'smtp', 'webhook'])->default('api_key');
            $table->text('credentials'); // Encrypted JSON (api_key OU access_token/refresh_token/etc.)
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable(); // Pour OAuth tokens
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'app_slug', 'service']);
            $table->index(['user_id', 'is_active']);
        });

        // Logs d'utilisation par app
        Schema::create('app_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('app_id')->constrained('apps')->onDelete('cascade');
            $table->string('action'); // generate_post, create_video, send_email, etc.
            $table->integer('credits_used')->default(1);
            $table->json('metadata')->nullable(); // Détails de l'action
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'app_id', 'created_at']);
        });

        // Reviews/ratings des apps
        Schema::create('app_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('app_id')->constrained('apps')->onDelete('cascade');
            $table->integer('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'app_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_reviews');
        Schema::dropIfExists('app_usage_logs');
        Schema::dropIfExists('user_app_credentials');
        Schema::dropIfExists('user_app_subscriptions');
        Schema::dropIfExists('app_pricing_plans');
        Schema::dropIfExists('apps');
    }
};
