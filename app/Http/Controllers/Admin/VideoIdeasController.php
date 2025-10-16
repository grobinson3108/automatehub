<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoIdea;
use App\Models\VideoContentPlan;
use App\Services\ContentSchedulerService;
use App\Services\VideoSchedulingService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VideoIdeasController extends Controller
{
    protected $scheduler;
    protected $videoScheduler;

    public function __construct(ContentSchedulerService $scheduler, VideoSchedulingService $videoScheduler)
    {
        $this->scheduler = $scheduler;
        $this->videoScheduler = $videoScheduler;
    }

    /**
     * Afficher toutes les idées vidéos par workflow
     */
    public function index(Request $request)
    {
        $workflows = VideoContentPlan::with(['videoIdeas' => function($query) {
            $query->orderBy('platform')->orderBy('video_index');
        }])
        ->where('priority', '<=', 20)
        ->where('viral_potential', '>=', 4)
        ->orderBy('priority')
        ->get();

        // Statistiques globales
        $stats = [
            'total_ideas' => VideoIdea::count(),
            'by_platform' => VideoIdea::selectRaw('platform, COUNT(*) as count')
                ->groupBy('platform')
                ->pluck('count', 'platform')
                ->toArray(),
            'total_workflows' => $workflows->count(),
            'avg_viral_potential' => round(VideoIdea::avg('viral_potential'), 1)
        ];

        return view('admin.video-ideas.index', compact('workflows', 'stats'));
    }

    /**
     * Générer le planning pour un workflow
     */
    public function generateSchedule(Request $request, VideoContentPlan $workflow)
    {
        $startDate = $request->get('start_date', today()->next('monday'));

        try {
            $publications = $this->scheduler->generateOptimalSchedule($workflow, $startDate);

            return response()->json([
                'success' => true,
                'message' => "Planning généré pour {$workflow->workflow_name} : " . count($publications) . " publications créées",
                'publications_count' => count($publications)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer le planning pour tous les workflows
     */
    public function generateAllSchedules(Request $request)
    {
        $startDate = $request->get('start_date', today()->next('monday'));

        $workflows = VideoContentPlan::where('priority', '<=', 20)
            ->where('viral_potential', '>=', 4)
            ->orderBy('priority')
            ->get();

        $totalPublications = 0;
        $errors = [];

        foreach ($workflows as $workflow) {
            try {
                $publications = $this->scheduler->generateOptimalSchedule($workflow, $startDate);
                $totalPublications += count($publications);

                // Décaler la date de début pour le prochain workflow
                $startDate = Carbon::parse($startDate)->addWeeks(2);
            } catch (\Exception $e) {
                $errors[] = "Erreur pour {$workflow->workflow_name}: " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs lors de la génération',
                'errors' => $errors
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Planning généré pour tous les workflows : {$totalPublications} publications créées",
            'publications_count' => $totalPublications
        ]);
    }

    /**
     * Afficher les détails d'une idée vidéo
     */
    public function show(VideoIdea $videoIdea)
    {
        $videoIdea->load(['videoContentPlan', 'publications']);

        return view('admin.video-ideas.show', compact('videoIdea'));
    }

    /**
     * Modifier une idée vidéo
     */
    public function edit(VideoIdea $videoIdea)
    {
        return view('admin.video-ideas.edit', compact('videoIdea'));
    }

    /**
     * Mettre à jour une idée vidéo
     */
    public function update(Request $request, VideoIdea $videoIdea)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'hook' => 'nullable|string',
            'hashtags' => 'nullable|array',
            'thumbnail_concept' => 'nullable|string',
            'duration' => 'nullable|string',
            'difficulty' => 'nullable|string',
            'video_type' => 'nullable|string',
            'call_to_action' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'estimated_views' => 'nullable|integer|min:0',
            'viral_potential' => 'required|integer|min:1|max:10',
            'music' => 'nullable|string',
            'transitions' => 'nullable|string'
        ]);

        $videoIdea->update($validated);

        return redirect()->route('admin.video-ideas.show', $videoIdea)
            ->with('success', 'Idée vidéo mise à jour avec succès');
    }

    /**
     * Planning quotidien optimisé
     */
    public function dailyTasks(Request $request)
    {
        $date = $request->get('date', today());
        $tasks = $this->scheduler->getDailyTasks($date);

        return view('admin.video-ideas.daily-tasks', compact('tasks', 'date'));
    }

    /**
     * AJAX: Mettre à jour la date de tournage
     */
    public function updateFilmingDate(Request $request, VideoIdea $videoIdea)
    {
        $request->validate([
            'filming_date' => 'required|date|after_or_equal:today'
        ]);

        $newDate = Carbon::parse($request->filming_date);

        // Vérifier les conflits potentiels
        $conflicts = $this->videoScheduler->checkConflicts(
            $newDate,
            $videoIdea->filming_start_time ?? '09:00',
            $videoIdea->filming_end_time ?? '11:00',
            $videoIdea->id
        );

        if (!empty($conflicts)) {
            return response()->json([
                'success' => false,
                'message' => 'Conflit détecté avec d\'autres tournages',
                'conflicts' => $conflicts
            ], 409);
        }

        // Mettre à jour la date de tournage
        $videoIdea->update(['filming_date' => $newDate]);

        // Recalculer automatiquement montage et publication
        $schedule = $this->videoScheduler->recalculateVideoIdea($videoIdea);

        if (!empty($schedule)) {
            $videoIdea->update([
                'editing_date' => $schedule['editing_date'],
                'editing_start_time' => $schedule['editing_start_time'],
                'editing_end_time' => $schedule['editing_end_time'],
                'publication_date' => $schedule['publication_date'],
                'scheduled_datetime' => $schedule['scheduled_datetime']
            ]);
        }

        // Recharger pour avoir les données fraîches
        $videoIdea->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Date de tournage mise à jour avec recalcul automatique',
            'video_idea' => [
                'id' => $videoIdea->id,
                'filming_date' => $videoIdea->filming_date->format('Y-m-d'),
                'filming_date_formatted' => $videoIdea->filming_date->format('d/m/Y'),
                'editing_date' => $videoIdea->editing_date?->format('Y-m-d'),
                'editing_date_formatted' => $videoIdea->editing_date?->format('d/m/Y'),
                'publication_date' => $videoIdea->publication_date?->format('Y-m-d'),
                'publication_date_formatted' => $videoIdea->publication_date?->format('d/m/Y'),
                'editing_time' => $videoIdea->editing_start_time . ' - ' . $videoIdea->editing_end_time,
                'publication_time' => $schedule['publication_time'] ?? null
            ]
        ]);
    }

    /**
     * AJAX: Mettre à jour l'heure de tournage
     */
    public function updateFilmingTime(Request $request, VideoIdea $videoIdea)
    {
        $request->validate([
            'filming_start_time' => 'required|date_format:H:i',
            'filming_end_time' => 'required|date_format:H:i|after:filming_start_time'
        ]);

        $startTime = $request->filming_start_time;
        $endTime = $request->filming_end_time;

        // Vérifier les conflits
        $conflicts = $this->videoScheduler->checkConflicts(
            $videoIdea->filming_date ?? today(),
            $startTime,
            $endTime,
            $videoIdea->id
        );

        if (!empty($conflicts)) {
            return response()->json([
                'success' => false,
                'message' => 'Conflit horaire avec d\'autres tournages',
                'conflicts' => $conflicts
            ], 409);
        }

        // Mettre à jour les heures
        $videoIdea->update([
            'filming_start_time' => $startTime,
            'filming_end_time' => $endTime
        ]);

        // Recalculer montage et publication
        $schedule = $this->videoScheduler->recalculateVideoIdea($videoIdea);

        if (!empty($schedule)) {
            $videoIdea->update([
                'editing_date' => $schedule['editing_date'],
                'editing_start_time' => $schedule['editing_start_time'],
                'editing_end_time' => $schedule['editing_end_time'],
                'publication_date' => $schedule['publication_date'],
                'scheduled_datetime' => $schedule['scheduled_datetime']
            ]);
        }

        $videoIdea->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Horaires mis à jour avec recalcul automatique',
            'video_idea' => [
                'id' => $videoIdea->id,
                'filming_time' => $startTime . ' - ' . $endTime,
                'editing_date' => $videoIdea->editing_date?->format('d/m/Y'),
                'editing_time' => $videoIdea->editing_start_time . ' - ' . $videoIdea->editing_end_time,
                'publication_date' => $videoIdea->publication_date?->format('d/m/Y'),
                'publication_time' => $schedule['publication_time'] ?? null
            ]
        ]);
    }

    /**
     * AJAX: Vérifier les conflits
     */
    public function checkConflicts(Request $request, VideoIdea $videoIdea)
    {
        $date = Carbon::parse($request->get('date', $videoIdea->filming_date));
        $startTime = $request->get('start_time', $videoIdea->filming_start_time ?? '09:00');
        $endTime = $request->get('end_time', $videoIdea->filming_end_time ?? '11:00');

        $conflicts = $this->videoScheduler->checkConflicts($date, $startTime, $endTime, $videoIdea->id);

        return response()->json([
            'conflicts' => $conflicts,
            'has_conflicts' => !empty($conflicts)
        ]);
    }

    /**
     * AJAX: Obtenir les créneaux disponibles
     */
    public function getAvailableSlots(Request $request, string $date)
    {
        $targetDate = Carbon::parse($date);
        $duration = (int) $request->get('duration', 2);

        $availableSlots = $this->videoScheduler->suggestAvailableSlots($targetDate, $duration);

        return response()->json([
            'date' => $targetDate->format('Y-m-d'),
            'date_formatted' => $targetDate->format('d/m/Y'),
            'available_slots' => $availableSlots,
            'slots_count' => count($availableSlots)
        ]);
    }
}