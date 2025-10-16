<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VideoPublication extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_content_plan_id',
        'video_idea_id',
        'platform',
        'video_index',
        'title',
        'description',
        'hashtags',
        'caption',
        'thumbnail_concept',
        'hooks',
        'scheduled_date',
        'scheduled_time',
        'filming_date',
        'editing_date',
        'status',
        'frequency_type',
        'frequency_interval',
        'target_audience',
        'call_to_action',
        'estimated_views',
        'engagement_rate_target',
        'published_url',
        'published_date',
        'actual_views',
        'likes',
        'comments',
        'shares',
        'actual_engagement_rate',
        'notes',
        'optimization_tips'
    ];

    protected $casts = [
        'hashtags' => 'array',
        'optimization_tips' => 'array',
        'scheduled_date' => 'date',
        'published_date' => 'date',
        'filming_date' => 'date',
        'editing_date' => 'date',
        'scheduled_time' => 'datetime:H:i',
        'engagement_rate_target' => 'decimal:2',
        'actual_engagement_rate' => 'decimal:2',
    ];

    // Relations
    public function videoContentPlan()
    {
        return $this->belongsTo(VideoContentPlan::class);
    }

    public function videoIdea()
    {
        return $this->belongsTo(VideoIdea::class);
    }

    // Scopes
    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeScheduledFor($query, $date)
    {
        return $query->whereDate('scheduled_date', $date);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', today())
                    ->where('status', 'planned')
                    ->orderBy('scheduled_date');
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('scheduled_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'planned' => 'bg-secondary',
            'filmed' => 'bg-info',
            'edited' => 'bg-warning',
            'published' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'planned' => 'Planifié',
            'filmed' => 'Filmé',
            'edited' => 'Monté',
            'published' => 'Publié',
            'cancelled' => 'Annulé',
            default => 'Planifié'
        };
    }

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
            'youtube' => 'text-danger',
            'tiktok' => 'text-dark',
            'linkedin' => 'text-info',
            'instagram' => 'text-pink',
            'facebook' => 'text-primary',
            default => 'text-muted'
        };
    }

    public function getEngagementRateAttribute()
    {
        if (!$this->actual_views || $this->actual_views == 0) {
            return 0;
        }

        $engagements = ($this->likes ?? 0) + ($this->comments ?? 0) + ($this->shares ?? 0);
        return round(($engagements / $this->actual_views) * 100, 2);
    }

    public function getIsOverdueAttribute()
    {
        return $this->scheduled_date < today() && $this->status === 'planned';
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

    // Mutators
    public function setHashtagsAttribute($value)
    {
        if (is_string($value)) {
            // Convertir une chaîne de hashtags en array
            $tags = explode(' ', $value);
            $tags = array_map('trim', $tags);
            $tags = array_filter($tags);
            $this->attributes['hashtags'] = json_encode(array_values($tags));
        } else {
            $this->attributes['hashtags'] = json_encode($value);
        }
    }

    // Méthodes utiles
    public function markAsFilmed()
    {
        $this->update(['status' => 'filmed']);
    }

    public function markAsEdited()
    {
        $this->update(['status' => 'edited']);
    }

    public function markAsPublished($url = null, $publishedDate = null)
    {
        $this->update([
            'status' => 'published',
            'published_url' => $url,
            'published_date' => $publishedDate ?? today()
        ]);
    }

    public function updateMetrics($views, $likes = 0, $comments = 0, $shares = 0)
    {
        $this->update([
            'actual_views' => $views,
            'likes' => $likes,
            'comments' => $comments,
            'shares' => $shares,
            'actual_engagement_rate' => $this->calculateEngagementRate($views, $likes, $comments, $shares)
        ]);
    }

    private function calculateEngagementRate($views, $likes, $comments, $shares)
    {
        if ($views == 0) return 0;
        return round((($likes + $comments + $shares) / $views) * 100, 2);
    }

    public static function generateOptimalSchedule($videoContentPlan, $startDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : today();
        $publications = [];

        // Fréquences recommandées par plateforme (TikTok/Instagram prioritaires)
        $frequencies = [
            'tiktok' => ['type' => 'daily', 'interval' => 1, 'priority' => 1], // Quotidien - PRIORITÉ MAX
            'instagram' => ['type' => 'daily', 'interval' => 1, 'priority' => 2], // Quotidien - PRIORITÉ HAUTE
            'facebook' => ['type' => 'weekly', 'interval' => 1], // 1 fois par semaine
            'linkedin' => ['type' => 'weekly', 'interval' => 2], // 1 fois toutes les 2 semaines
            'youtube' => ['type' => 'monthly', 'interval' => 1], // 1 fois par mois seulement
        ];

        foreach ($videoContentPlan->platforms as $platform) {
            $videos = $videoContentPlan->video_details[$platform]['videos'] ?? [];
            $frequency = $frequencies[$platform];

            foreach ($videos as $index => $video) {
                $scheduledDate = self::calculateNextDate($startDate, $platform, $index, $frequency);

                $publications[] = [
                    'video_content_plan_id' => $videoContentPlan->id,
                    'platform' => $platform,
                    'video_index' => $index,
                    'scheduled_date' => $scheduledDate,
                    'scheduled_time' => self::getOptimalTime($platform),
                    'frequency_type' => $frequency['type'],
                    'frequency_interval' => $frequency['interval'],
                    'status' => 'planned'
                ];
            }
        }

        return $publications;
    }

    private static function calculateNextDate($startDate, $platform, $videoIndex, $frequency)
    {
        $date = $startDate->copy();

        // Décalage par plateforme pour éviter la concurrence (TikTok/Instagram prioritaires)
        $platformOffsets = [
            'tiktok' => 0,      // Lundi (démarrage immédiat)
            'instagram' => 1,    // Mardi (+1 jour)
            'facebook' => 2,     // Mercredi (+2 jours)
            'linkedin' => 3,     // Jeudi (+3 jours)
            'youtube' => 4      // Vendredi (+4 jours)
        ];

        $date->addDays($platformOffsets[$platform]);

        // Ajouter l'intervalle selon la fréquence
        if ($frequency['type'] === 'daily') {
            $date->addDays($videoIndex * $frequency['interval']);
        } elseif ($frequency['type'] === 'weekly') {
            $date->addWeeks($videoIndex * $frequency['interval']);
        } elseif ($frequency['type'] === 'biweekly') {
            $date->addWeeks($videoIndex * $frequency['interval'] * 2);
        } elseif ($frequency['type'] === 'monthly') {
            $date->addMonths($videoIndex * $frequency['interval']);
        }

        return $date;
    }

    private static function getOptimalTime($platform)
    {
        // Heures optimales par plateforme (heure française)
        $optimalTimes = [
            'youtube' => '18:00',
            'tiktok' => '19:00',
            'linkedin' => '08:00',
            'instagram' => '17:00',
            'facebook' => '15:00'
        ];

        return $optimalTimes[$platform] ?? '12:00';
    }
}