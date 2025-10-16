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
        Schema::create('video_content_plans', function (Blueprint $table) {
            $table->id();
            $table->string('workflow_name');
            $table->string('workflow_file_path')->nullable();
            $table->text('workflow_description');
            $table->json('platforms'); // ['youtube', 'tiktok', 'linkedin', 'instagram', 'facebook']
            $table->integer('priority')->default(50); // 1-100, plus bas = plus prioritaire
            $table->integer('viral_potential')->default(3); // 1-5 étoiles
            $table->enum('status', ['todo', 'in_progress', 'done'])->default('todo');
            $table->json('video_details'); // Détails des vidéos par plateforme
            $table->integer('estimated_videos')->default(1);
            $table->date('planned_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_content_plans');
    }
};
