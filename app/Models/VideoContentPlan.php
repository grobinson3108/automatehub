<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoContentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_name',
        'workflow_file_path',
        'workflow_description',
        'platforms',
        'priority',
        'viral_potential',
        'status',
        'video_details',
        'estimated_videos',
        'planned_date',
        'completed_date',
        'notes'
    ];

    protected $casts = [
        'platforms' => 'array',
        'video_details' => 'array',
        'planned_date' => 'date',
        'completed_date' => 'date',
        'priority' => 'integer',
        'viral_potential' => 'integer',
        'estimated_videos' => 'integer'
    ];

    /**
     * Scope pour trier par priorité (plus bas = plus prioritaire)
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    /**
     * Scope pour les workflows à faire
     */
    public function scopeTodo($query)
    {
        return $query->where('status', 'todo');
    }

    /**
     * Scope pour les workflows en cours
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope pour les workflows terminés
     */
    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }

    /**
     * Obtenir le potentiel viral sous forme d'étoiles
     */
    public function getViralStarsAttribute()
    {
        return str_repeat('⭐', $this->viral_potential);
    }

    /**
     * Obtenir le nombre total de vidéos estimées
     */
    public function getTotalVideosAttribute()
    {
        return count($this->platforms) * $this->estimated_videos;
    }

    /**
     * Marquer comme terminé
     */
    public function markAsDone()
    {
        $this->update([
            'status' => 'done',
            'completed_date' => now()
        ]);
    }

    /**
     * Marquer comme en cours
     */
    public function markAsInProgress()
    {
        $this->update([
            'status' => 'in_progress'
        ]);
    }

    /**
     * Obtenir la couleur du statut pour l'interface
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'todo' => 'text-gray-600 bg-gray-100',
            'in_progress' => 'text-blue-600 bg-blue-100',
            'done' => 'text-green-600 bg-green-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    /**
     * Obtenir la couleur de priorité
     */
    public function getPriorityColorAttribute()
    {
        if ($this->priority <= 10) {
            return 'text-red-600 bg-red-100'; // Urgent
        } elseif ($this->priority <= 30) {
            return 'text-orange-600 bg-orange-100'; // Haute
        } elseif ($this->priority <= 60) {
            return 'text-yellow-600 bg-yellow-100'; // Moyenne
        } else {
            return 'text-gray-600 bg-gray-100'; // Basse
        }
    }

    /**
     * Relation avec les publications vidéo
     */
    public function publications()
    {
        return $this->hasMany(VideoPublication::class);
    }

    /**
     * Relation avec les idées vidéos
     */
    public function videoIdeas()
    {
        return $this->hasMany(VideoIdea::class);
    }

    /**
     * Générer automatiquement les publications pour ce plan
     */
    public function generatePublications($startDate = null)
    {
        // Supprimer les publications existantes en status 'planned'
        $this->publications()->where('status', 'planned')->delete();

        // Générer nouvelles publications
        $publicationsData = VideoPublication::generateOptimalSchedule($this, $startDate);

        foreach ($publicationsData as $data) {
            $this->publications()->create(array_merge($data, [
                'title' => $this->generatePlatformTitle($data['platform'], $data['video_index']),
                'description' => $this->generatePlatformDescription($data['platform'], $data['video_index']),
                'hashtags' => $this->generatePlatformHashtags($data['platform']),
                'caption' => $this->generatePlatformCaption($data['platform'], $data['video_index']),
                'target_audience' => $this->getVideoDetail($data['platform'], $data['video_index'], 'target_audience'),
                'call_to_action' => $this->getVideoDetail($data['platform'], $data['video_index'], 'call_to_action'),
                'thumbnail_concept' => $this->getVideoDetail($data['platform'], $data['video_index'], 'thumbnail_ideas'),
                'estimated_views' => $this->estimateViews($data['platform']),
                'engagement_rate_target' => $this->getTargetEngagement($data['platform']),
            ]));
        }
    }

    private function generatePlatformTitle($platform, $videoIndex)
    {
        $video = $this->getVideoDetail($platform, $videoIndex);
        return $video['title'] ?? $this->workflow_name;
    }

    private function generatePlatformDescription($platform, $videoIndex)
    {
        $video = $this->getVideoDetail($platform, $videoIndex);
        $baseDescription = $video['description'] ?? $this->workflow_description;

        // Ajouter des éléments spécifiques par plateforme
        switch ($platform) {
            case 'youtube':
                return $baseDescription . "\n\n🔗 Téléchargez le workflow complet : [LIEN]\n\n📚 Formation n8n complète : https://automatehub.fr\n\n⏰ Chapitres:\n0:00 Introduction\n2:00 Configuration\n5:00 Test en direct\n8:00 Optimisations\n\n#n8n #automation #workflow";

            case 'linkedin':
                return "🚀 " . $baseDescription . "\n\n💡 Cette automation peut transformer votre productivité en quelques minutes.\n\n👉 Que pensez-vous de ce type d'automation ?\n\n#automation #productivity #business #n8n";

            case 'tiktok':
            case 'instagram':
                return $baseDescription . "\n\n🔥 Follow pour plus d'automations\n💾 Workflow gratuit en bio";

            case 'facebook':
                return $baseDescription . "\n\n💭 Avez-vous déjà essayé ce type d'automation ? Partagez votre expérience en commentaire !\n\n🔗 Plus d'infos : https://automatehub.fr";

            default:
                return $baseDescription;
        }
    }

    private function generatePlatformHashtags($platform)
    {
        $baseHashtags = ['automation', 'n8n', 'workflow', 'productivity'];

        $platformSpecific = match($platform) {
            'youtube' => ['tutorial', 'howto', 'tech'],
            'tiktok' => ['TechTok', 'AutomationHack', 'ProductivityTips'],
            'linkedin' => ['business', 'entrepreneurship', 'innovation'],
            'instagram' => ['techlife', 'automation', 'productive'],
            'facebook' => ['technology', 'business', 'tips'],
            default => []
        };

        return array_merge($baseHashtags, $platformSpecific);
    }

    private function generatePlatformCaption($platform, $videoIndex)
    {
        $video = $this->getVideoDetail($platform, $videoIndex);
        $hook = $video['hook'] ?? 'Découvrez cette automation';

        switch ($platform) {
            case 'tiktok':
            case 'instagram':
                return "🔥 " . $hook . " #automation #productivity";
            case 'linkedin':
                return "💡 " . $hook . " | Transformez votre façon de travailler";
            default:
                return $hook;
        }
    }

    private function getVideoDetail($platform, $videoIndex, $key = null)
    {
        $videos = $this->video_details[$platform]['videos'] ?? [];
        $video = $videos[$videoIndex] ?? [];

        return $key ? ($video[$key] ?? null) : $video;
    }

    private function estimateViews($platform)
    {
        return match($platform) {
            'youtube' => rand(500, 2000),
            'tiktok' => rand(1000, 10000),
            'linkedin' => rand(200, 1000),
            'instagram' => rand(300, 1500),
            'facebook' => rand(100, 800),
            default => 500
        };
    }

    private function getTargetEngagement($platform)
    {
        return match($platform) {
            'youtube' => 4.5,
            'tiktok' => 8.0,
            'linkedin' => 3.5,
            'instagram' => 6.0,
            'facebook' => 2.5,
            default => 4.0
        };
    }
}
