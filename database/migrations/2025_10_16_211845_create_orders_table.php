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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pack_id')->constrained()->cascadeOnDelete();

            // Stripe fields
            $table->string('stripe_session_id')->unique();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_customer_id')->nullable();

            // Order details
            $table->integer('amount'); // in cents
            $table->string('currency', 3)->default('EUR'); // EUR or USD
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');

            // Customer info (for non-registered users)
            $table->string('customer_email');
            $table->string('customer_name')->nullable();

            // Delivery & Security
            $table->integer('download_count')->default(0);
            $table->integer('max_downloads')->default(3);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // 30 days access

            // Metadata
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('stripe_session_id');
            $table->index('stripe_payment_intent_id');
            $table->index('customer_email');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
