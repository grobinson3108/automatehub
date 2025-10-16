<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_ideas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_content_plan_id')->constrained()->onDelete('cascade');
            $table->string('platform');
            $table->integer('video_index')->default(0);
            $table->string('title');
            $table->text('description');
            $table->string('hook')->nullable();
            $table->json('hashtags')->nullable();
            $table->text('thumbnail_concept')->nullable();
            $table->string('duration')->nullable();
            $table->string('difficulty')->nullable();
            $table->string('video_type')->nullable();
            $table->text('call_to_action')->nullable();
            $table->string('target_audience')->nullable();
            $table->integer('estimated_views')->nullable();
            $table->integer('viral_potential')->default(3);
            $table->string('music')->nullable();
            $table->string('transitions')->nullable();
            $table->json('source_data')->nullable();
            $table->timestamps();

            $table->index(['video_content_plan_id', 'platform']);
            $table->index(['platform', 'viral_potential']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('video_ideas');
    }
};