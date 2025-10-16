<?php

namespace App\Services;

use App\Models\VideoIdea;
use App\Models\VideoPublication;
use Carbon\Carbon;

class ContentSchedulerService
{
    /**
     * Créneaux optimaux par plateforme (heure française)
     */
    private $optimalSlots = [
        'youtube' => [
            'lundi' => ['20:00'],
            'mardi' => ['18:00'],
            'mercredi' => ['19:00'],
            'jeudi' => ['18:30'],
            'vendredi' => ['19:30'],
            'samedi' => ['14:00', '20:00'],
            'dimanche' => ['15:00', '19:00']
        ],
        'youtube_shorts' => [
            'lundi' => ['12:00', '17:00'],
            'mardi' => ['11:00', '16:00'],
            'mercredi' => ['12:30', '17:30'],
            'jeudi' => ['11:30', '16:30'],
            'vendredi' => ['13:00', '18:00'],
            'samedi' => ['10:00', '15:00'],
            'dimanche' => ['11:00', '16:00']
        ],
        'tiktok' => [
            'lundi' => ['08:00', '12:00', '19:00'],
            'mardi' => ['09:00', '13:00', '20:00'],
            'mercredi' => ['07:30', '12:30', '18:30'],
            'jeudi' => ['08:30', '13:30', '19:30'],
            'vendredi' => ['09:30', '14:00', '20:30'],
            'samedi' => ['10:00', '15:00', '21:00'],
            'dimanche' => ['11:00', '16:00', '20:00']
        ],
        'instagram' => [
            'lundi' => ['09:00', '17:00'],
            'mardi' => ['10:00', '18:00'],
            'mercredi' => ['08:30', '17:30'],
            'jeudi' => ['09:30', '18:30'],
            'vendredi' => ['10:30', '19:00'],
            'samedi' => ['11:00', '16:00'],
            'dimanche' => ['12:00', '17:00']
        ],
        'linkedin' => [
            'lundi' => ['08:00', '17:00'],
            'mardi' => ['07:30', '16:30'],
            'mercredi' => ['08:30', '17:30'],
            'jeudi' => ['07:00', '16:00'],
            'vendredi' => ['08:00', '15:30'],
            'samedi' => [],
            'dimanche' => []
        ],
        'facebook' => [
            'lundi' => ['09:00', '15:00'],
            'mardi' => ['10:00', '16:00'],
            'mercredi' => ['09:30', '15:30'],
            'jeudi' => ['10:30', '16:30'],
            'vendredi' => ['11:00', '17:00'],
            'samedi' => ['12:00', '18:00'],
            'dimanche' => ['13:00', '19:00']
        ]
    ];

    /**
     * Fréquences de publication par plateforme (par semaine)
     */
    private $publicationFrequencies = [
        'youtube' => 3,      // 3 vidéos par semaine
        'youtube_shorts' => 2, // 2 shorts par semaine
        'tiktok' => 4,       // 4 vidéos par semaine
        'instagram' => 3,    // 3 posts par semaine
        'facebook' => 2,     // 2 posts par semaine
        'linkedin' => 4      // 4 posts par semaine
    ];

    /**
     * Délais recommandés entre publications (en jours)
     */
    private $publicationDelays = [
        'youtube' => 2,      // 3 fois par semaine = tous les 2 jours
        'youtube_shorts' => 3, // 2 fois par semaine = tous les 3 jours
        'tiktok' => 2,       // 4 fois par semaine = tous les 2 jours
        'instagram' => 2,    // 3 fois par semaine = tous les 2 jours
        'facebook' => 3,     // 2 fois par semaine = tous les 3 jours
        'linkedin' => 2      // 4 fois par semaine = tous les 2 jours
    ];

    /**
     * Génère un planning optimisé pour un workflow
     */
    public function generateOptimalSchedule($videoContentPlan, $startDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : today();

        // Supprimer les anciennes publications de ce workflow
        VideoPublication::where('video_content_plan_id', $videoContentPlan->id)->delete();

        $publications = [];
        $currentWeek = $startDate->copy()->startOfWeek();

        // Récupérer toutes les idées vidéos pour ce workflow
        $videoIdeas = VideoIdea::where('video_content_plan_id', $videoContentPlan->id)
            ->orderBy('platform')
            ->orderBy('video_index')
            ->get();

        if ($videoIdeas->isEmpty()) {
            return [];
        }

        // Grouper les idées par plateforme
        $ideasByPlatform = $videoIdeas->groupBy('platform');

        // Planning par workflow :
        // Lundi : Tournage de toutes les vidéos du workflow
        // Mardi-dimanche : Publication selon les créneaux optimaux

        $tournageDate = $currentWeek->copy(); // Lundi
        $publicationStartDate = $currentWeek->copy()->addDay(); // Mardi

        // Ordre de priorité des plateformes pour étaler les publications
        $platformOrder = ['youtube', 'youtube_shorts', 'linkedin', 'facebook', 'instagram', 'tiktok'];
        $nextPublicationDate = $publicationStartDate->copy();

        foreach ($platformOrder as $platform) {
            if (!isset($ideasByPlatform[$platform])) {
                continue;
            }

            $ideas = $ideasByPlatform[$platform];
            $delay = $this->publicationDelays[$platform] ?? 7;
            $slots = $this->optimalSlots[$platform] ?? [];

            foreach ($ideas as $index => $idea) {
                $publicationDate = $this->findNextOptimalSlot(
                    $nextPublicationDate,
                    $platform,
                    $slots
                );

                $publications[] = [
                    'video_content_plan_id' => $videoContentPlan->id,
                    'video_idea_id' => $idea->id,
                    'platform' => $platform,
                    'video_index' => $index,
                    'title' => $idea->title,
                    'description' => $idea->description,
                    'hashtags' => $idea->hashtags,
                    'target_audience' => $idea->target_audience,
                    'call_to_action' => $idea->call_to_action,
                    'thumbnail_concept' => $idea->thumbnail_concept,
                    'hooks' => $idea->hook,
                    'filming_date' => $tournageDate,
                    'editing_date' => $tournageDate->copy()->addDay(),
                    'scheduled_date' => $publicationDate['date'],
                    'scheduled_time' => $publicationDate['time'],
                    'status' => 'planned',
                    'estimated_views' => $this->estimateViews($platform, $idea->viral_potential),
                    'engagement_rate_target' => $this->getTargetEngagement($platform),
                    'notes' => "Tournage prévu le {$tournageDate->format('l j F Y')}"
                ];

                // Préparer la prochaine date pour cette plateforme
                $nextPublicationDate = $publicationDate['date']->copy()->addDays($delay);
            }
        }

        // Créer les publications en base
        foreach ($publications as $pubData) {
            VideoPublication::create($pubData);
        }

        return $publications;
    }

    /**
     * Trouve le prochain créneau optimal pour une plateforme
     */
    private function findNextOptimalSlot($startDate, $platform, $slots)
    {
        $currentDate = $startDate->copy();
        $availableDays = array_keys(array_filter($slots, fn($daySlots) => !empty($daySlots)));

        if (empty($availableDays)) {
            // Pas de créneaux définis, utiliser des valeurs par défaut
            $targetTime = match($platform) {
                'youtube' => '20:00',
                'tiktok' => '19:00',
                'instagram' => '17:00',
                'linkedin' => '08:00',
                'facebook' => '15:00',
                default => '18:00'
            };

            return [
                'date' => $currentDate,
                'time' => $targetTime
            ];
        }

        // Trouver le premier jour optimal à partir de la date donnée
        for ($i = 0; $i < 7; $i++) {
            $dayName = $this->englishDayToFrench($currentDate->format('l'));

            if (in_array($dayName, $availableDays)) {
                $daySlots = $slots[$dayName];
                $targetTime = $daySlots[0]; // Prendre le premier créneau de la journée

                return [
                    'date' => $currentDate->copy(),
                    'time' => $targetTime
                ];
            }

            $currentDate->addDay();
        }

        // Fallback si aucun jour optimal trouvé
        return [
            'date' => $startDate->copy(),
            'time' => '18:00'
        ];
    }

    private function estimateViews($platform, $viralPotential)
    {
        $baseViews = [
            'youtube' => 500,
            'tiktok' => 2000,
            'instagram' => 800,
            'linkedin' => 300,
            'facebook' => 600
        ];

        return ($baseViews[$platform] ?? 500) * $viralPotential;
    }

    private function getTargetEngagement($platform)
    {
        return match($platform) {
            'youtube' => 3.5,
            'tiktok' => 8.0,
            'instagram' => 4.2,
            'linkedin' => 2.1,
            'facebook' => 2.8,
            default => 3.0
        };
    }

    private function frenchDayToEnglish($frenchDay)
    {
        return match($frenchDay) {
            'lundi' => 'monday',
            'mardi' => 'tuesday',
            'mercredi' => 'wednesday',
            'jeudi' => 'thursday',
            'vendredi' => 'friday',
            'samedi' => 'saturday',
            'dimanche' => 'sunday',
            default => 'wednesday'
        };
    }

    private function englishDayToFrench($englishDay)
    {
        return match(strtolower($englishDay)) {
            'monday' => 'lundi',
            'tuesday' => 'mardi',
            'wednesday' => 'mercredi',
            'thursday' => 'jeudi',
            'friday' => 'vendredi',
            'saturday' => 'samedi',
            'sunday' => 'dimanche',
            default => 'mercredi'
        };
    }

    private function getDayOfWeekNumber($englishDay)
    {
        return match($englishDay) {
            'monday' => 0,
            'tuesday' => 1,
            'wednesday' => 2,
            'thursday' => 3,
            'friday' => 4,
            'saturday' => 5,
            'sunday' => 6,
            default => 2
        };
    }

    /**
     * Obtient le planning quotidien pour une date donnée
     */
    public function getDailyTasks($date = null)
    {
        $date = $date ? Carbon::parse($date) : today();

        $tasks = [
            'filming' => VideoIdea::whereDate('filming_date', $date)
                ->where('filming_status', 'pending')
                ->with('videoContentPlan')
                ->orderBy('filming_start_time')
                ->get()
                ->map(function($video) {
                    return [
                        'id' => $video->id,
                        'title' => $video->title,
                        'platform' => $video->platform,
                        'start_time' => $video->filming_start_time,
                        'end_time' => $video->filming_end_time,
                        'workflow' => $video->videoContentPlan->workflow_name ?? 'Workflow inconnu',
                        'workflow_id' => $video->video_content_plan_id,
                        'type' => 'filming',
                        'status' => $video->filming_status,
                        'description' => "Tournage: {$video->title}",
                        'duration' => $video->duration ?? '30 min',
                        'notes' => $video->production_notes
                    ];
                }),

            'editing' => VideoIdea::whereDate('editing_date', $date)
                ->where('editing_status', 'pending')
                ->with('videoContentPlan')
                ->orderBy('editing_start_time')
                ->get()
                ->map(function($video) {
                    return [
                        'id' => $video->id,
                        'title' => $video->title,
                        'platform' => $video->platform,
                        'start_time' => $video->editing_start_time,
                        'end_time' => $video->editing_end_time,
                        'workflow' => $video->videoContentPlan->workflow_name ?? 'Workflow inconnu',
                        'workflow_id' => $video->video_content_plan_id,
                        'type' => 'editing',
                        'status' => $video->editing_status,
                        'description' => "Montage: {$video->title}",
                        'duration' => $video->duration ?? '2h',
                        'notes' => $video->production_notes
                    ];
                }),

            'publishing' => VideoIdea::whereDate('publication_date', $date)
                ->where('publication_status', 'pending')
                ->with('videoContentPlan')
                ->orderBy('publication_time')
                ->get()
                ->map(function($video) {
                    return [
                        'id' => $video->id,
                        'title' => $video->title,
                        'platform' => $video->platform,
                        'start_time' => $video->publication_time,
                        'end_time' => null,
                        'workflow' => $video->videoContentPlan->workflow_name ?? 'Workflow inconnu',
                        'workflow_id' => $video->video_content_plan_id,
                        'type' => 'publishing',
                        'status' => $video->publication_status,
                        'description' => "Publication: {$video->title} ({$video->platform})",
                        'hashtags' => $video->formatted_hashtags,
                        'thumbnail_concept' => $video->thumbnail_concept,
                        'notes' => $video->production_notes
                    ];
                })
        ];

        return $tasks;
    }
}