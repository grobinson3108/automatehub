<?php

namespace App\Console\Commands;

use App\Models\VideoIdea;
use Illuminate\Console\Command;
use Carbon\Carbon;

class PopulateVideoSchedulingCommand extends Command
{
    protected $signature = 'video:populate-scheduling {--force}';
    protected $description = 'Génère les dates et heures de tournage, montage et publication pour toutes les idées vidéo';

    // Créneaux de tournage (lundi prioritaire)
    private $filmingSlots = [
        'monday' => [
            ['start' => '09:00', 'end' => '12:00'],
            ['start' => '14:00', 'end' => '17:00'],
            ['start' => '19:00', 'end' => '21:00']
        ],
        'tuesday' => [
            ['start' => '10:00', 'end' => '12:00'],
            ['start' => '15:00', 'end' => '17:00']
        ],
        'wednesday' => [
            ['start' => '09:00', 'end' => '11:00'],
            ['start' => '16:00', 'end' => '18:00']
        ]
    ];

    // Créneaux de montage
    private $editingSlots = [
        'tuesday' => [
            ['start' => '09:00', 'end' => '12:00'],
            ['start' => '14:00', 'end' => '18:00']
        ],
        'wednesday' => [
            ['start' => '09:00', 'end' => '12:00'],
            ['start' => '14:00', 'end' => '17:00']
        ],
        'thursday' => [
            ['start' => '09:00', 'end' => '12:00'],
            ['start' => '14:00', 'end' => '16:00']
        ],
        'friday' => [
            ['start' => '09:00', 'end' => '11:00'],
            ['start' => '14:00', 'end' => '16:00']
        ]
    ];

    // Heures optimales de publication par plateforme
    private $publicationTimes = [
        'youtube' => ['14:00', '17:00', '19:00'],
        'youtube_shorts' => ['12:00', '18:00', '21:00'],
        'tiktok' => ['09:00', '12:00', '19:00', '21:00'],
        'instagram' => ['11:00', '14:00', '17:00'],
        'facebook' => ['13:00', '15:00', '18:00'],
        'linkedin' => ['08:00', '12:00', '17:00']
    ];

    private $currentWeek;
    private $usedFilmingSlots = [];
    private $usedEditingSlots = [];

    public function handle()
    {
        $this->info('🎬 Génération du planning complet de production vidéo...');

        $force = $this->option('force');

        // Initialiser la semaine de départ
        $now = Carbon::now();

        // Toujours commencer par le prochain lundi (ou le lundi de cette semaine si nous sommes lundi)
        if ($now->isSunday()) {
            $this->currentWeek = $now->copy()->addDay(); // Demain = lundi 30/09
        } elseif ($now->isMonday()) {
            $this->currentWeek = $now->copy(); // Aujourd'hui = lundi
        } else {
            $this->currentWeek = $now->copy()->next('monday'); // Prochain lundi
        }

        $this->info("📅 Date de départ: " . $this->currentWeek->format('l j F Y'));

        // Récupérer toutes les idées vidéo
        $query = VideoIdea::with('videoContentPlan');

        if (!$force) {
            $query->whereNull('filming_date');
        }

        $videoIdeas = $query->orderBy('video_content_plan_id')
                           ->orderBy('platform')
                           ->orderBy('video_index')
                           ->get();

        if ($videoIdeas->isEmpty()) {
            $this->warn('Aucune idée vidéo à planifier.');
            return;
        }

        $this->info("📊 {$videoIdeas->count()} idées vidéo à planifier");

        // Grouper par workflow pour une production cohérente
        $videosByWorkflow = $videoIdeas->groupBy('video_content_plan_id');
        $this->info("🎬 {$videosByWorkflow->count()} workflows à planifier");

        $plannedCount = 0;
        $workflowCount = 0;

        foreach ($videosByWorkflow as $workflowId => $workflowVideos) {
            $workflowCount++;
            $workflowName = $workflowVideos->first()->videoContentPlan->workflow_name ?? "Workflow {$workflowId}";

            $this->info("📽️ Planning du workflow: {$workflowName} ({$workflowVideos->count()} vidéos)");

            // Planifier tout le workflow d'une traite
            $plannedCount += $this->planWorkflow($workflowVideos);

            if ($plannedCount % 20 == 0) {
                $this->info("✅ {$plannedCount} vidéos planifiées...");
            }
        }

        $this->info('✅ Planification terminée !');
        $this->showStatistics();
    }

    /**
     * Planifier tout un workflow d'une traite (tournages → montages → publications)
     */
    private function planWorkflow($workflowVideos)
    {
        $workflowName = $workflowVideos->first()->videoContentPlan->workflow_name ?? 'Workflow';
        $videoCount = $workflowVideos->count();

        // 1. PHASE TOURNAGE : Tout le workflow sur 1-2 jours consécutifs
        $filmingDate = $this->currentWeek->copy();
        $filmingSchedules = [];

        // Grouper par plateformes pour optimiser l'organisation
        $byPlatform = $workflowVideos->groupBy('platform');
        $videoIndex = 0;

        foreach ($byPlatform as $platform => $videos) {
            foreach ($videos as $video) {
                $schedule = $this->getWorkflowFilmingSchedule($videoIndex, $videoCount, $filmingDate);
                $filmingSchedules[] = [
                    'video' => $video,
                    'schedule' => $schedule
                ];
                $videoIndex++;
            }
        }

        // 2. PHASE MONTAGE : 1-3 jours après le dernier tournage
        $lastFilmingDate = collect($filmingSchedules)->max('schedule.date');
        $editingStartDate = Carbon::parse($lastFilmingDate)->addDays(rand(1, 3));

        // 3. PHASE PUBLICATION : Selon les créneaux optimaux
        $publicationStartDate = $editingStartDate->copy()->addDays(rand(1, 2));

        // Appliquer les plannings
        foreach ($filmingSchedules as $index => $item) {
            $video = $item['video'];
            $filmingSchedule = $item['schedule'];

            // Montage (étalé sur plusieurs jours)
            $editingDate = $editingStartDate->copy()->addDays(floor($index / 3)); // 3 montages max par jour
            $editingSchedule = $this->getWorkflowEditingSchedule($editingDate);

            // Publication selon la plateforme
            $publicationSchedule = $this->getWorkflowPublicationSchedule($video, $publicationStartDate);

            // Sauvegarder
            $video->update([
                'filming_date' => $filmingSchedule['date'],
                'filming_start_time' => $filmingSchedule['start_time'],
                'filming_end_time' => $filmingSchedule['end_time'],
                'filming_status' => 'pending',

                'editing_date' => $editingSchedule['date'],
                'editing_start_time' => $editingSchedule['start_time'],
                'editing_end_time' => $editingSchedule['end_time'],
                'editing_status' => 'pending',

                'publication_date' => $publicationSchedule['date'],
                'publication_time' => $publicationSchedule['time'],
                'scheduled_datetime' => Carbon::parse($publicationSchedule['date'] . ' ' . $publicationSchedule['time']),
                'publication_status' => 'pending'
            ]);
        }

        // Préparer la semaine suivante pour le prochain workflow
        $this->currentWeek = $publicationStartDate->copy()->addWeek()->startOfWeek();

        return $videoCount;
    }

    private function planVideoIdea($videoIdea)
    {
        // 1. TOURNAGE (Lundi prioritaire)
        $filmingSchedule = $this->getFilmingSchedule($videoIdea);

        // 2. MONTAGE (1-2 jours après tournage)
        $editingSchedule = $this->getEditingSchedule($videoIdea, $filmingSchedule['date']);

        // 3. PUBLICATION (selon stratégie plateforme)
        $publicationSchedule = $this->getPublicationSchedule($videoIdea, $editingSchedule['date']);

        // Sauvegarder en base
        $videoIdea->update([
            // Tournage
            'filming_date' => $filmingSchedule['date'],
            'filming_start_time' => $filmingSchedule['start_time'],
            'filming_end_time' => $filmingSchedule['end_time'],
            'filming_status' => 'pending',

            // Montage
            'editing_date' => $editingSchedule['date'],
            'editing_start_time' => $editingSchedule['start_time'],
            'editing_end_time' => $editingSchedule['end_time'],
            'editing_status' => 'pending',

            // Publication
            'publication_date' => $publicationSchedule['date'],
            'publication_time' => $publicationSchedule['time'],
            'scheduled_datetime' => Carbon::parse($publicationSchedule['date'] . ' ' . $publicationSchedule['time']),
            'publication_status' => 'pending'
        ]);
    }

    private function getFilmingSchedule($videoIdea)
    {
        // Priorité au lundi, puis autres jours
        $preferredDays = ['monday', 'tuesday', 'wednesday'];

        foreach ($preferredDays as $day) {
            if (!isset($this->filmingSlots[$day])) continue;

            $dayCarbon = $this->currentWeek->copy()->startOfWeek()->addDays(array_search($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']));

            foreach ($this->filmingSlots[$day] as $slotIndex => $slot) {
                $slotKey = $day . '_' . $slotIndex;

                if (!isset($this->usedFilmingSlots[$slotKey])) {
                    $this->usedFilmingSlots[$slotKey] = 0;
                }

                // Maximum 2 vidéos par créneau
                if ($this->usedFilmingSlots[$slotKey] < 2) {
                    $this->usedFilmingSlots[$slotKey]++;

                    return [
                        'date' => $dayCarbon->format('Y-m-d'),
                        'start_time' => $slot['start'],
                        'end_time' => $slot['end']
                    ];
                }
            }
        }

        // Si aucun créneau disponible, passer à la semaine suivante
        $this->currentWeek->addWeek();
        $this->usedFilmingSlots = [];

        return $this->getFilmingSchedule($videoIdea);
    }

    private function getEditingSchedule($videoIdea, $filmingDate)
    {
        $filmingCarbon = Carbon::parse($filmingDate);

        // Montage 1-2 jours après le tournage
        $editingStartDate = $filmingCarbon->copy()->addDays(rand(1, 2));

        // Trouver le bon jour de la semaine
        $weekDay = strtolower($editingStartDate->format('l'));

        if (!isset($this->editingSlots[$weekDay])) {
            // Si pas de créneau ce jour, prendre le jour suivant
            $editingStartDate->addDay();
            $weekDay = strtolower($editingStartDate->format('l'));
        }

        if (isset($this->editingSlots[$weekDay])) {
            foreach ($this->editingSlots[$weekDay] as $slotIndex => $slot) {
                $slotKey = $weekDay . '_' . $slotIndex . '_' . $editingStartDate->format('Y-m-d');

                if (!isset($this->usedEditingSlots[$slotKey])) {
                    $this->usedEditingSlots[$slotKey] = 0;
                }

                // Maximum 1 vidéo par créneau de montage (plus intensif)
                if ($this->usedEditingSlots[$slotKey] < 1) {
                    $this->usedEditingSlots[$slotKey]++;

                    return [
                        'date' => $editingStartDate->format('Y-m-d'),
                        'start_time' => $slot['start'],
                        'end_time' => $slot['end']
                    ];
                }
            }
        }

        // Créneau par défaut si aucun disponible
        return [
            'date' => $editingStartDate->format('Y-m-d'),
            'start_time' => '14:00',
            'end_time' => '16:00'
        ];
    }

    private function getPublicationSchedule($videoIdea, $editingDate)
    {
        $editingCarbon = Carbon::parse($editingDate);
        $platform = $videoIdea->platform;

        // Publication 1-4 jours après montage selon la plateforme
        $daysDelay = match($platform) {
            'youtube' => rand(2, 4),        // Plus de temps pour YouTube
            'youtube_shorts' => rand(1, 2), // Rapide pour les shorts
            'tiktok' => rand(1, 3),         // Réactif pour TikTok
            'instagram' => rand(1, 3),      // Modéré pour Instagram
            'facebook' => rand(2, 4),       // Plus de temps pour Facebook
            'linkedin' => rand(1, 3),       // Professionnel mais réactif
            default => rand(1, 3)
        };

        $publicationDate = $editingCarbon->copy()->addDays($daysDelay);

        // Éviter les weekends pour certaines plateformes business
        if (in_array($platform, ['linkedin']) && $publicationDate->isWeekend()) {
            $publicationDate->addDays($publicationDate->isSaturday() ? 2 : 1);
        }

        $times = $this->publicationTimes[$platform] ?? ['12:00'];
        $selectedTime = $times[array_rand($times)];

        return [
            'date' => $publicationDate->format('Y-m-d'),
            'time' => $selectedTime
        ];
    }

    private function showStatistics()
    {
        $stats = [
            'total' => VideoIdea::whereNotNull('filming_date')->count(),
            'filming_pending' => VideoIdea::where('filming_status', 'pending')->count(),
            'editing_pending' => VideoIdea::where('editing_status', 'pending')->count(),
            'publication_pending' => VideoIdea::where('publication_status', 'pending')->count(),
        ];

        $this->info("\n📊 STATISTIQUES DE PLANIFICATION :");
        $this->info("🎬 Vidéos planifiées : {$stats['total']}");
        $this->info("🎥 Tournages en attente : {$stats['filming_pending']}");
        $this->info("✂️  Montages en attente : {$stats['editing_pending']}");
        $this->info("📱 Publications en attente : {$stats['publication_pending']}");

        // Prochaines tâches
        $nextFilming = VideoIdea::where('filming_status', 'pending')
                               ->whereDate('filming_date', '>=', today())
                               ->orderBy('filming_date')
                               ->orderBy('filming_start_time')
                               ->first();

        if ($nextFilming) {
            $this->info("\n🎬 PROCHAIN TOURNAGE :");
            $this->info("📅 {$nextFilming->filming_date} de {$nextFilming->filming_start_time} à {$nextFilming->filming_end_time}");
            $this->info("🎥 {$nextFilming->title}");
        }
    }

    /**
     * Planning de tournage groupé pour un workflow complet
     */
    private function getWorkflowFilmingSchedule($videoIndex, $totalVideos, $baseDate)
    {
        // Tous les tournages du workflow sur 1-2 jours consécutifs
        $currentDate = $baseDate->copy();

        // Si c'est un gros workflow (>8 vidéos), étaler sur 2 jours
        if ($totalVideos > 8) {
            $dayOffset = floor($videoIndex / 8); // 8 vidéos max par jour
            $currentDate->addDays($dayOffset);
        }

        // Créneaux de tournage intensifs pour un workflow
        $intensiveSlots = [
            ['start' => '08:00', 'end' => '11:00'],   // Matin
            ['start' => '11:30', 'end' => '14:30'],  // Fin de matinée
            ['start' => '15:00', 'end' => '18:00'],  // Après-midi
            ['start' => '18:30', 'end' => '21:30'],  // Soirée
        ];

        $slotIndex = $videoIndex % 4; // 4 créneaux par jour max
        $slot = $intensiveSlots[$slotIndex];

        return [
            'date' => $currentDate->format('Y-m-d'),
            'start_time' => $slot['start'],
            'end_time' => $slot['end']
        ];
    }

    /**
     * Planning de montage après tournage d'un workflow
     */
    private function getWorkflowEditingSchedule($editingDate)
    {
        // Créneaux de montage intensifs
        $editingSlots = [
            ['start' => '09:00', 'end' => '13:00'],  // Matinée intensive
            ['start' => '14:00', 'end' => '18:00'],  // Après-midi intensive
            ['start' => '19:00', 'end' => '22:00'],  // Soirée
        ];

        $slot = $editingSlots[array_rand($editingSlots)];

        return [
            'date' => $editingDate->format('Y-m-d'),
            'start_time' => $slot['start'],
            'end_time' => $slot['end']
        ];
    }

    /**
     * Planning de publication optimisé par plateforme
     */
    private function getWorkflowPublicationSchedule($video, $basePublicationDate)
    {
        $platform = $video->platform;

        // Délais recommandés selon la plateforme
        $daysDelay = match($platform) {
            'youtube' => rand(3, 7),        // YouTube : plus de temps
            'youtube_shorts' => rand(1, 3), // Shorts : plus rapide
            'tiktok' => rand(1, 4),         // TikTok : réactif
            'instagram' => rand(2, 5),      // Instagram : modéré
            'facebook' => rand(3, 6),       // Facebook : moins urgent
            'linkedin' => rand(2, 5),       // LinkedIn : professionnel
            default => rand(2, 5)
        };

        $publicationDate = $basePublicationDate->copy()->addDays($daysDelay);

        // Éviter les weekends pour LinkedIn
        if ($platform === 'linkedin' && $publicationDate->isWeekend()) {
            $publicationDate->addDays($publicationDate->isSaturday() ? 2 : 1);
        }

        // Heures optimales par plateforme
        $optimalTimes = [
            'youtube' => ['14:00', '17:00', '19:00'],
            'youtube_shorts' => ['12:00', '18:00', '21:00'],
            'tiktok' => ['09:00', '12:00', '19:00', '21:00'],
            'instagram' => ['11:00', '14:00', '17:00'],
            'facebook' => ['13:00', '15:00', '18:00'],
            'linkedin' => ['08:00', '12:00', '17:00']
        ];

        $times = $optimalTimes[$platform] ?? ['12:00'];
        $selectedTime = $times[array_rand($times)];

        return [
            'date' => $publicationDate->format('Y-m-d'),
            'time' => $selectedTime
        ];
    }
}