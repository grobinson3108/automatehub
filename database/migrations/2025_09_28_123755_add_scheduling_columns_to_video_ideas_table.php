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
        Schema::table('video_ideas', function (Blueprint $table) {
            // Dates et heures de production
            $table->date('filming_date')->nullable()->after('source_data');
            $table->time('filming_start_time')->nullable()->after('filming_date');
            $table->time('filming_end_time')->nullable()->after('filming_start_time');

            $table->date('editing_date')->nullable()->after('filming_end_time');
            $table->time('editing_start_time')->nullable()->after('editing_date');
            $table->time('editing_end_time')->nullable()->after('editing_start_time');

            // Date et heure de publication
            $table->date('publication_date')->nullable()->after('editing_end_time');
            $table->time('publication_time')->nullable()->after('publication_date');
            $table->datetime('scheduled_datetime')->nullable()->after('publication_time');

            // Statuts de production
            $table->enum('filming_status', ['pending', 'in_progress', 'completed'])->default('pending')->after('scheduled_datetime');
            $table->enum('editing_status', ['pending', 'in_progress', 'completed'])->default('pending')->after('filming_status');
            $table->enum('publication_status', ['pending', 'scheduled', 'published', 'failed'])->default('pending')->after('editing_status');

            // Notes et liens
            $table->text('production_notes')->nullable()->after('publication_status');
            $table->string('video_file_path')->nullable()->after('production_notes');
            $table->string('thumbnail_file_path')->nullable()->after('video_file_path');

            // Index pour les requêtes de calendrier
            $table->index(['filming_date', 'filming_status']);
            $table->index(['editing_date', 'editing_status']);
            $table->index(['publication_date', 'publication_status']);
            $table->index('scheduled_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_ideas', function (Blueprint $table) {
            $table->dropIndex(['filming_date', 'filming_status']);
            $table->dropIndex(['editing_date', 'editing_status']);
            $table->dropIndex(['publication_date', 'publication_status']);
            $table->dropIndex(['scheduled_datetime']);

            $table->dropColumn([
                'filming_date', 'filming_start_time', 'filming_end_time',
                'editing_date', 'editing_start_time', 'editing_end_time',
                'publication_date', 'publication_time', 'scheduled_datetime',
                'filming_status', 'editing_status', 'publication_status',
                'production_notes', 'video_file_path', 'thumbnail_file_path'
            ]);
        });
    }
};
