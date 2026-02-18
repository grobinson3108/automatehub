<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchtrend_watch_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watch_id')->constrained('watchtrend_watches')->cascadeOnDelete();
            $table->foreignId('shared_by_user_id')->constrained('users');
            $table->string('shared_with_email');
            $table->foreignId('shared_with_user_id')->nullable()->constrained('users');
            $table->string('permission')->default('view'); // view, edit
            $table->string('token')->unique();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['watch_id', 'shared_with_email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchtrend_watch_shares');
    }
};
