<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('video_publications', function (Blueprint $table) {
            $table->foreignId('video_idea_id')->nullable()->after('video_content_plan_id')->constrained()->onDelete('set null');
            $table->date('filming_date')->nullable()->after('scheduled_time');
            $table->date('editing_date')->nullable()->after('filming_date');
        });
    }

    public function down()
    {
        Schema::table('video_publications', function (Blueprint $table) {
            $table->dropForeign(['video_idea_id']);
            $table->dropColumn(['video_idea_id', 'filming_date', 'editing_date']);
        });
    }
};