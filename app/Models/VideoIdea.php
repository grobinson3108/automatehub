<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoIdea extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_content_plan_id',
        'platform',
        'video_index',
        'title',
        'description',
        'hook',
        'hashtags',
        'thumbnail_concept',
        'duration',
        'difficulty',
        'video_type',
        'call_to_action',
        'target_audience',
        'estimated_views',
        'viral_potential',
        'music',
        'transitions',
        'source_data',
        // Dates et heures de production
        'filming_date',
        'filming_start_time',
        'filming_end_time',
        'editing_date',
        'editing_start_time',
        'editing_end_time',
        // Publication
        'publication_date',
        'publication_time',
        'scheduled_datetime',
        // Statuts
        'filming_status',
        'editing_status',
        'publication_status',
        // Fichiers et notes
        'production_notes',
        'video_file_path',
        'thumbnail_file_path'
    ];

    protected $casts = [
        'hashtags' => 'array',
        'source_data' => 'array',
        'viral_potential' => 'integer',
        'estimated_views' => 'integer',
        'filming_date' => 'date',
        'editing_date' => 'date',
        'publication_date' => 'date',
        'scheduled_datetime' => 'datetime'
    ];

    // Relations
    public function videoContentPlan()
    {
        return $this->belongsTo(VideoContentPlan::class);
    }

    public function publications()
    {
        return $this->hasMany(VideoPublication::class);
    }

    // Scopes
    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeByWorkflow($query, $workflowId)
    {
        return $query->where('video_content_plan_id', $workflowId);
    }

    // Accessors
    public function getPlatformIconAttribute()
    {
        return match($this->platform) {
            'youtube' => 'fab fa-youtube',
            'tiktok' => 'fab fa-tiktok',
            'linkedin' => 'fab fa-linkedin',
            'instagram' => 'fab fa-instagram',
            'facebook' => 'fab fa-facebook',
            default => 'fa fa-video'
        };
    }

    public function getPlatformColorAttribute()
    {
        return match($this->platform) {
            'youtube' => '#FF0000',
            'tiktok' => '#000000',
            'linkedin' => '#0077B5',
            'instagram' => '#E4405F',
            'facebook' => '#1877F2',
            default => '#6c757d'
        };
    }

    public function getFormattedHashtagsAttribute()
    {
        if (!$this->hashtags) {
            return '';
        }

        return collect($this->hashtags)->map(function($tag) {
            return str_starts_with($tag, '#') ? $tag : '#' . $tag;
        })->join(' ');
    }
}