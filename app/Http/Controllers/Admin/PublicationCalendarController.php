<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoContentPlan;
use App\Models\VideoPublication;
use App\Services\ContentSchedulerService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PublicationCalendarController extends Controller
{
    /**
     * Afficher le calendrier éditorial
     */
    public function index(Request $request)
    {
        // Récupérer toutes les publications
        $publications = VideoPublication::with('videoContentPlan')
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->get();

        // Statistiques
        $stats = [
            'total' => VideoPublication::count(),
            'thisWeek' => VideoPublication::thisWeek()->count(),
            'published' => VideoPublication::where('status', 'published')->count(),
            'scheduled' => VideoPublication::where('status', 'scheduled')->count(),
        ];

        // Workflows pour le filtre
        $workflows = VideoContentPlan::orderBy('workflow_name')->get();

        return view('admin.publication-calendar.index', compact('publications', 'stats', 'workflows'));
    }

    /**
     * Générer le calendrier pour un workflow
     */
    public function generateSchedule(Request $request, VideoContentPlan $videoContentPlan)
    {
        $startDate = $request->get('start_date', today()->next('monday'));

        $scheduler = new ContentSchedulerService();
        $publications = $scheduler->generateOptimalSchedule($videoContentPlan, $startDate);

        return redirect()->back()->with('success',
            'Calendrier généré avec succès pour ' . $videoContentPlan->workflow_name .
            ' (' . count($publications) . ' publications créées)');
    }

    /**
     * Générer le calendrier pour tous les workflows TOP
     */
    public function generateAllSchedules(Request $request)
    {
        $startDate = $request->get('start_date', today()->next('monday'));

        // Prendre les workflows avec des idées vidéos
        $topWorkflows = VideoContentPlan::whereHas('videoIdeas')
            ->where('priority', '<=', 20)
            ->where('viral_potential', '>=', 4)
            ->orderBy('priority')
            ->limit(5)
            ->get();

        $scheduler = new ContentSchedulerService();
        $totalPublications = 0;
        $count = 0;

        foreach ($topWorkflows as $workflow) {
            $workflowStartDate = Carbon::parse($startDate)->addWeeks($count * 2);
            $publications = $scheduler->generateOptimalSchedule($workflow, $workflowStartDate);
            $totalPublications += count($publications);
            $count++;
        }

        return redirect()->back()->with('success',
            "Calendrier généré pour {$count} workflows prioritaires ({$totalPublications} publications créées)");
    }

    /**
     * Planning d'aujourd'hui et des 3 prochains jours
     */
    public function today()
    {
        $scheduler = new ContentSchedulerService();

        // Récupérer les tâches pour les 4 prochains jours (aujourd'hui + 3)
        $dailyTasks = [];
        for ($i = 0; $i < 4; $i++) {
            $date = today()->addDays($i);
            $dailyTasks[$date->format('Y-m-d')] = [
                'date' => $date,
                'tasks' => $scheduler->getDailyTasks($date)
            ];
        }

        // Statistiques globales
        $stats = [
            'total_scheduled' => \App\Models\VideoIdea::whereNotNull('filming_date')->count(),
            'this_week' => \App\Models\VideoIdea::whereBetween('filming_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'in_production' => \App\Models\VideoIdea::whereIn('filming_status', ['pending', 'in_progress'])
                                ->orWhereIn('editing_status', ['pending', 'in_progress'])
                                ->count(),
            'published_this_month' => \App\Models\VideoIdea::where('publication_status', 'published')
                ->whereMonth('publication_date', now()->month)
                ->count(),
        ];

        return view('admin.publication-calendar.today', compact('dailyTasks', 'stats'));
    }

    /**
     * Export des données JSON pour le calendrier
     */
    public function getPublicationsJson(Request $request)
    {
        $videoIdeas = \App\Models\VideoIdea::with('videoContentPlan')
            ->whereNotNull('scheduled_datetime')
            ->get()
            ->map(function ($videoIdea) {
                $isPublished = $videoIdea->publication_status === 'published';
                $isFuture = $videoIdea->scheduled_datetime->isFuture();
                $color = $this->getPlatformColor($videoIdea->platform);

                // Calculer l'opacité selon le statut
                $opacity = match(true) {
                    $isPublished => 1.0,     // Publié = opacité 100%
                    $isFuture => 0.5,        // Futur = opacité 50%
                    default => 0.8           // Autres = opacité 80%
                };

                // Convertir la couleur hexadécimale en rgba
                $rgba = $this->hexToRgba($color, $opacity);

                return [
                    'id' => $videoIdea->id,
                    'title' => $videoIdea->title,
                    'start' => $videoIdea->scheduled_datetime->format('Y-m-d\TH:i:s'),
                    'backgroundColor' => $rgba,
                    'borderColor' => $color,
                    'textColor' => $isPublished ? '#ffffff' : ($isFuture ? '#666666' : '#ffffff'),
                    'classNames' => [$videoIdea->publication_status, $videoIdea->platform],
                    'extendedProps' => [
                        'platform' => $videoIdea->platform,
                        'status' => $videoIdea->publication_status,
                        'workflow' => $videoIdea->videoContentPlan->title ?? 'Workflow inconnu',
                        'isPublished' => $isPublished,
                        'isFuture' => $isFuture,
                        'filming_date' => $videoIdea->filming_date?->format('d/m/Y'),
                        'editing_date' => $videoIdea->editing_date?->format('d/m/Y'),
                        'description' => $videoIdea->description,
                        'hashtags' => $videoIdea->formatted_hashtags
                    ]
                ];
            });

        return response()->json($videoIdeas);
    }

    private function getPlatformColor($platform)
    {
        $colors = [
            'youtube' => '#FF0000',
            'youtube_shorts' => '#FF4444',
            'tiktok' => '#000000',
            'instagram' => '#E4405F',
            'linkedin' => '#0077B5',
            'facebook' => '#1877F2',
        ];

        return $colors[$platform] ?? '#6c757d';
    }

    private function hexToRgba($hex, $opacity)
    {
        // Supprimer le # si présent
        $hex = ltrim($hex, '#');

        // Convertir hex en RGB
        if (strlen($hex) === 6) {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        } else {
            return "rgba(108, 117, 125, $opacity)"; // Couleur par défaut
        }

        return "rgba($r, $g, $b, $opacity)";
    }

    /**
     * Voir les détails d'une publication
     */
    public function show(VideoPublication $publication)
    {
        $publication->load('videoContentPlan');

        return view('admin.publication-calendar.show', compact('publication'));
    }

    /**
     * Modifier une publication
     */
    public function edit(VideoPublication $publication)
    {
        $publication->load('videoContentPlan');

        return view('admin.publication-calendar.edit', compact('publication'));
    }

    /**
     * Mettre à jour une publication
     */
    public function update(Request $request, VideoPublication $publication)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'hashtags' => 'nullable|string',
            'caption' => 'nullable|string',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'target_audience' => 'nullable|string',
            'call_to_action' => 'nullable|string',
            'thumbnail_concept' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $publication->update($validated);

        return redirect()->route('admin.publication-calendar.show', $publication)
            ->with('success', 'Publication mise à jour avec succès');
    }

    /**
     * Changer le statut d'une publication
     */
    public function updateStatus(Request $request, VideoPublication $publication)
    {
        $request->validate([
            'status' => 'required|in:planned,filmed,edited,published,cancelled'
        ]);

        $publication->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Statut mis à jour']);
    }

    /**
     * Marquer comme publié avec URL
     */
    public function markAsPublished(Request $request, VideoPublication $publication)
    {
        $request->validate([
            'published_url' => 'nullable|url',
            'published_date' => 'nullable|date'
        ]);

        $publication->markAsPublished(
            $request->published_url,
            $request->published_date ? Carbon::parse($request->published_date) : null
        );

        return response()->json(['success' => true]);
    }

    /**
     * Mettre à jour les métriques d'une publication
     */
    public function updateMetrics(Request $request, VideoPublication $publication)
    {
        $request->validate([
            'views' => 'required|integer|min:0',
            'likes' => 'nullable|integer|min:0',
            'comments' => 'nullable|integer|min:0',
            'shares' => 'nullable|integer|min:0',
        ]);

        $publication->updateMetrics(
            $request->views,
            $request->likes ?? 0,
            $request->comments ?? 0,
            $request->shares ?? 0
        );

        return response()->json(['success' => true]);
    }


    /**
     * Dupliquer une publication (pour répéter un contenu performant)
     */
    public function duplicate(VideoPublication $publication)
    {
        $newPublication = $publication->replicate();
        $newPublication->scheduled_date = today()->addDays(7); // Dans une semaine
        $newPublication->status = 'planned';
        $newPublication->published_url = null;
        $newPublication->published_date = null;
        $newPublication->actual_views = null;
        $newPublication->likes = null;
        $newPublication->comments = null;
        $newPublication->shares = null;
        $newPublication->actual_engagement_rate = null;
        $newPublication->save();

        return response()->json(['success' => true, 'id' => $newPublication->id]);
    }


    /**
     * Export du calendrier en CSV
     */
    public function exportCalendar(Request $request)
    {
        $start = Carbon::parse($request->get('start', today()->startOfMonth()));
        $end = Carbon::parse($request->get('end', today()->endOfMonth()));

        $publications = VideoPublication::with('videoContentPlan')
            ->whereBetween('scheduled_date', [$start, $end])
            ->orderBy('scheduled_date')
            ->get();

        $filename = 'calendrier-editorial-' . $start->format('Y-m') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($publications) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'Date',
                'Heure',
                'Workflow',
                'Plateforme',
                'Titre',
                'Statut',
                'Vues estimées',
                'URL'
            ], ';');

            // Données
            foreach ($publications as $pub) {
                fputcsv($file, [
                    $pub->scheduled_date->format('d/m/Y'),
                    $pub->scheduled_time ? Carbon::parse($pub->scheduled_time)->format('H:i') : '',
                    $pub->videoContentPlan->workflow_name,
                    ucfirst($pub->platform),
                    $pub->title,
                    $pub->status_text,
                    $pub->estimated_views,
                    $pub->published_url ?? ''
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}