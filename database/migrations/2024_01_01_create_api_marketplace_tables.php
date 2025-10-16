<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des APIs disponibles
        Schema::create('api_services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // content-extractor, image-generator, etc.
            $table->string('name');
            $table->text('description');
            $table->string('icon')->nullable();
            $table->string('category'); // extraction, ai, automation, etc.
            $table->json('features'); // Liste des fonctionnalités
            $table->boolean('is_active')->default(true);
            $table->string('endpoint_base'); // URL de base de l'API
            $table->string('node_package')->nullable(); // n8n-nodes-content-extractor
            $table->integer('default_quota')->default(10);
            $table->timestamps();
        });
        
        // Table des plans tarifaires
        Schema::create('api_pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_service_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Free, Starter, Pro, etc.
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->integer('monthly_quota');
            $table->decimal('extra_credit_price', 10, 4); // Prix par crédit supplémentaire
            $table->json('features')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Souscriptions des utilisateurs
        Schema::create('user_api_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('api_service_id')->constrained()->onDelete('cascade');
            $table->foreignId('pricing_plan_id')->nullable()->constrained('api_pricing_plans');
            $table->string('api_key')->unique();
            $table->integer('monthly_quota');
            $table->integer('used_this_month')->default(0);
            $table->integer('extra_credits')->default(0);
            $table->date('reset_date');
            $table->enum('status', ['active', 'suspended', 'cancelled'])->default('active');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'api_service_id']);
            $table->index(['api_key', 'status']);
        });
        
        // Logs d'utilisation
        Schema::create('api_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('user_api_subscriptions');
            $table->string('endpoint');
            $table->string('method');
            $table->integer('credits_used')->default(1);
            $table->integer('response_code')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['subscription_id', 'created_at']);
        });
        
        // Packs de crédits
        Schema::create('credit_packs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_service_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Pack 100, Pack 500, etc.
            $table->integer('credits');
            $table->decimal('price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->string('stripe_price_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Achats de crédits
        Schema::create('credit_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->constrained('user_api_subscriptions');
            $table->foreignId('credit_pack_id')->constrained();
            $table->integer('credits');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // stripe, paypal, etc.
            $table->string('transaction_id')->unique();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded']);
            $table->json('payment_data')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
        
        // Webhooks entrants (pour tracking)
        Schema::create('api_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source'); // stripe, paypal, skool, etc.
            $table->string('event_type');
            $table->json('payload');
            $table->enum('status', ['pending', 'processed', 'failed']);
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
        
        // Table pour les intégrations (Skool, etc.)
        Schema::create('external_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // skool, discord, etc.
            $table->string('external_id')->nullable();
            $table->string('email');
            $table->enum('status', ['active', 'cancelled']);
            $table->json('benefits')->nullable(); // APIs et quotas bonus
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->unique(['platform', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_memberships');
        Schema::dropIfExists('api_webhook_logs');
        Schema::dropIfExists('credit_purchases');
        Schema::dropIfExists('credit_packs');
        Schema::dropIfExists('api_usage_logs');
        Schema::dropIfExists('user_api_subscriptions');
        Schema::dropIfExists('api_pricing_plans');
        Schema::dropIfExists('api_services');
    }
};