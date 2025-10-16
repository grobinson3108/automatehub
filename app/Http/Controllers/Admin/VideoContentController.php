<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoContentPlan;
use Illuminate\Http\Request;

class VideoContentController extends Controller
{
    /**
     * Display a listing of the video content plans.
     */
    public function index()
    {
        $videoPlans = VideoContentPlan::byPriority()
            ->with([])
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'workflow_name' => $plan->workflow_name,
                    'workflow_description' => $plan->workflow_description,
                    'platforms' => $plan->platforms,
                    'priority' => $plan->priority,
                    'viral_potential' => $plan->viral_potential,
                    'viral_stars' => $plan->viral_stars,
                    'status' => $plan->status,
                    'status_color' => $plan->status_color,
                    'priority_color' => $plan->priority_color,
                    'estimated_videos' => $plan->estimated_videos,
                    'total_videos' => $plan->total_videos,
                    'planned_date' => $plan->planned_date?->format('d/m/Y'),
                    'completed_date' => $plan->completed_date?->format('d/m/Y'),
                    'notes' => $plan->notes,
                ];
            });

        $stats = [
            'total' => $videoPlans->count(),
            'todo' => $videoPlans->where('status', 'todo')->count(),
            'in_progress' => $videoPlans->where('status', 'in_progress')->count(),
            'done' => $videoPlans->where('status', 'done')->count(),
            'total_videos' => $videoPlans->sum('total_videos'),
        ];

        return view('admin.video-content.index', compact('videoPlans', 'stats'));
    }

    /**
     * Show the form for creating a new video content plan.
     */
    public function create()
    {
        $platforms = ['youtube', 'tiktok', 'linkedin', 'instagram', 'facebook'];
        return view('admin.video-content.create', compact('platforms'));
    }

    /**
     * Store a newly created video content plan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'workflow_name' => 'required|string|max:255',
            'workflow_file_path' => 'nullable|string',
            'workflow_description' => 'required|string',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'in:youtube,tiktok,linkedin,instagram,facebook',
            'priority' => 'required|integer|min:1|max:100',
            'viral_potential' => 'required|integer|min:1|max:5',
            'estimated_videos' => 'required|integer|min:1',
            'video_details' => 'nullable|array',
            'planned_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        VideoContentPlan::create($validated);

        return redirect()->route('admin.video-content.index')
            ->with('success', 'Plan de contenu vidéo créé avec succès!');
    }

    /**
     * Display the specified video content plan.
     */
    public function show(VideoContentPlan $videoContentPlan)
    {
        // Utiliser les VideoIdeas comme source unique de vérité - Tri chronologique comme dans publication-calendar/today
        $videoIdeas = $videoContentPlan->videoIdeas()
            ->orderBy('filming_date')
            ->orderBy('filming_start_time')
            ->get();

        // Grouper par plateforme et trier les plateformes par ordre chronologique strict
        $platformsGrouped = $videoIdeas->groupBy('platform');

        // Trier les plateformes par la date/heure de leur première vidéo (même logique que getDailyTasks)
        $platformsSorted = $platformsGrouped->map(function($videos, $platform) {
            // Trier les vidéos dans chaque plateforme par ordre chronologique
            $sortedVideos = $videos->sortBy(function($video) {
                // Clé de tri : date + heure pour ordre chronologique strict
                $date = $video->filming_date ? $video->filming_date->format('Y-m-d') : '9999-12-31';
                $time = $video->filming_start_time ?? '23:59';
                return $date . ' ' . $time;
            });

            $firstVideo = $sortedVideos->first();
            return [
                'platform' => $platform,
                'first_filming' => $firstVideo && $firstVideo->filming_date
                    ? $firstVideo->filming_date->format('Y-m-d') . ' ' . ($firstVideo->filming_start_time ?? '23:59')
                    : '9999-12-31 23:59',
                'videos' => $sortedVideos
            ];
        })->sortBy('first_filming');

        $platformDetails = [];
        foreach ($platformsSorted as $platformData) {
            $platform = $platformData['platform'];
            // Conserver l'ordre chronologique déjà établi
            $platformVideos = $platformData['videos'];

            $videos = $platformVideos->map(function($idea) {
                return [
                    'title' => $idea->title,
                    'description' => $idea->description,
                    'duration' => $idea->duration,
                    'hook' => $idea->hook,
                    'video_type' => $idea->video_type,
                    'difficulty' => $idea->difficulty,
                    'target_audience' => $idea->target_audience,
                    'call_to_action' => $idea->call_to_action,
                    'tags' => $idea->hashtags ?? [],
                    'thumbnail_ideas' => $idea->thumbnail_concept,
                    'music' => $idea->music,
                    'transitions' => $idea->transitions,
                    'filming_date' => $idea->filming_date,
                    'filming_time' => $idea->filming_start_time . ' - ' . $idea->filming_end_time,
                    'editing_date' => $idea->editing_date,
                    'publication_date' => $idea->publication_date,
                    'filming_order' => $idea->filming_date->format('Y-m-d H:i') . ' ' . $idea->filming_start_time,
                ];
            })->values()->toArray();

            $platformDetails[$platform] = [
                'videos' => $videos
            ];
        }

        return view('admin.video-content.show', [
            'videoContentPlan' => $videoContentPlan,
            'platformDetails' => $platformDetails,
        ]);
    }

    /**
     * Show the form for editing the specified video content plan.
     */
    public function edit(VideoContentPlan $videoContentPlan)
    {
        $platforms = ['youtube', 'tiktok', 'linkedin', 'instagram', 'facebook'];
        return view('admin.video-content.edit', compact('videoContentPlan', 'platforms'));
    }

    /**
     * Update the specified video content plan.
     */
    public function update(Request $request, VideoContentPlan $videoContentPlan)
    {
        $validated = $request->validate([
            'workflow_name' => 'required|string|max:255',
            'workflow_file_path' => 'nullable|string',
            'workflow_description' => 'required|string',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'in:youtube,tiktok,linkedin,instagram,facebook',
            'priority' => 'required|integer|min:1|max:100',
            'viral_potential' => 'required|integer|min:1|max:5',
            'estimated_videos' => 'required|integer|min:1',
            'video_details' => 'nullable|array',
            'planned_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $videoContentPlan->update($validated);

        return redirect()->route('admin.video-content.index')
            ->with('success', 'Plan de contenu vidéo mis à jour avec succès!');
    }

    /**
     * Remove the specified video content plan.
     */
    public function destroy(VideoContentPlan $videoContentPlan)
    {
        $videoContentPlan->delete();

        return redirect()->route('admin.video-content.index')
            ->with('success', 'Plan de contenu vidéo supprimé avec succès!');
    }

    /**
     * Mark as done and move to bottom
     */
    public function markAsDone(VideoContentPlan $videoContentPlan)
    {
        $videoContentPlan->markAsDone();

        // Déplacer à la fin en mettant une priorité élevée
        $maxPriority = VideoContentPlan::where('status', 'done')->max('priority') ?? 90;
        $videoContentPlan->update(['priority' => $maxPriority + 1]);

        return redirect()->route('admin.video-content.index')
            ->with('success', 'Workflow marqué comme terminé!');
    }

    /**
     * Mark as in progress
     */
    public function markAsInProgress(VideoContentPlan $videoContentPlan)
    {
        $videoContentPlan->markAsInProgress();

        return redirect()->route('admin.video-content.index')
            ->with('success', 'Workflow marqué comme en cours!');
    }

    /**
     * Update priority (for drag & drop reordering)
     */
    public function updatePriority(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer|exists:video_content_plans,id',
            'items.*.priority' => 'required|integer|min:1',
        ]);

        foreach ($request->items as $item) {
            VideoContentPlan::where('id', $item['id'])
                ->update(['priority' => $item['priority']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Generate video content plans from workflows
     */
    public function generateFromWorkflows()
    {
        // Ici on va analyser les workflows traduits et créer automatiquement les plans
        $this->generateTop10Plans();
        $this->generateAllWorkflowPlans();

        return redirect()->route('admin.video-content.index')
            ->with('success', 'Plans de contenu générés automatiquement!');
    }

    /**
     * Generate plans for TOP 10 workflows
     */
    private function generateTop10Plans()
    {
        $top10Workflows = [
            [
                'name' => 'JARVIS - Assistant Personnel Ultime',
                'description' => 'Assistant IA multi-agents qui gère emails, calendrier, contacts et création de contenu automatiquement.',
                'priority' => 1,
                'viral_potential' => 5,
                'estimated_videos' => 2,
                'platforms' => ['youtube', 'tiktok', 'linkedin', 'instagram', 'facebook'],
                'video_details' => [
                    'youtube' => [
                        'videos' => [
                            [
                                'title' => 'J\'ai créé JARVIS comme Iron Man - Assistant IA complet',
                                'description' => 'Tutorial complet pour créer son assistant personnel IA avec n8n. Découvrez comment automatiser complètement votre vie quotidienne avec un assistant ultra-performant.',
                                'duration' => '12-15 min',
                                'hook' => 'Création JARVIS étape par étape',
                                'tags' => ['JARVIS', 'assistant IA', 'n8n automation', 'productivity', 'tutorial français', 'Iron Man', 'intelligence artificielle'],
                                'thumbnail_ideas' => 'JARVIS interface + Iron Man reference + Before/After productivity',
                                'target_audience' => 'Entrepreneurs, freelancers, tech enthusiasts',
                                'call_to_action' => 'Télécharge le workflow JARVIS complet en description',
                                'video_type' => 'Tutorial long-form',
                                'difficulty' => 'Intermédiaire'
                            ],
                            [
                                'title' => 'JARVIS vs Assistant Google - Lequel est le meilleur ?',
                                'description' => 'Comparaison détaillée entre JARVIS personnalisé et les assistants commerciaux. Résultats surprenants !',
                                'duration' => '8-10 min',
                                'hook' => 'Battle des assistants IA',
                                'tags' => ['JARVIS vs Google', 'assistant comparison', 'AI battle', 'productivity test', 'automation'],
                                'thumbnail_ideas' => 'VS layout with JARVIS vs Google Assistant logos',
                                'target_audience' => 'Tech comparateurs, early adopters',
                                'call_to_action' => 'Dis-moi en commentaire lequel tu préfères !',
                                'video_type' => 'Comparison/Review',
                                'difficulty' => 'Débutant'
                            ]
                        ]
                    ],
                    'tiktok' => [
                        'videos' => [
                            [
                                'title' => 'POV: Tu as JARVIS comme assistant personnel',
                                'description' => 'Démonstration rapide des capacités de JARVIS avec transitions dynamiques',
                                'duration' => '45s',
                                'hook' => 'POV assistant IA futuriste',
                                'tags' => ['#JARVIS', '#AssistantIA', '#POV', '#TechTok', '#Automation', '#IronMan', '#Productivity'],
                                'thumbnail_ideas' => 'Phone screen showing JARVIS interface + futuristic effects',
                                'target_audience' => 'Gen Z, tech lovers, productivity enthusiasts',
                                'call_to_action' => 'Follow pour plus d\'automations folles',
                                'video_type' => 'POV/Demo',
                                'music' => 'Trending tech/futuristic sound',
                                'transitions' => 'Quick cuts, zoom effects, text overlays'
                            ],
                            [
                                'title' => 'Cette IA gère ma vie mieux que moi',
                                'description' => 'Avant/après dramatique avec JARVIS',
                                'duration' => '30s',
                                'hook' => 'Transformation lifestyle',
                                'tags' => ['#AvantApres', '#IA', '#ProductiviteHack', '#TechTok', '#LifeChanger', '#Automation'],
                                'thumbnail_ideas' => 'Split screen chaos vs organized',
                                'target_audience' => 'Procrastinators, young professionals',
                                'call_to_action' => 'Workflow gratuit en bio 🔗',
                                'video_type' => 'Before/After transformation',
                                'music' => 'Dramatic transformation trending sound',
                                'transitions' => 'Before/after split, time-lapse effect'
                            ]
                        ]
                    ],
                    'linkedin' => [
                        'videos' => [
                            [
                                'title' => 'JARVIS en entreprise : ROI de 300% en 3 mois',
                                'description' => 'Case study business avec chiffres concrets',
                                'duration' => '6-8 min',
                                'hook' => 'ROI et productivité business'
                            ]
                        ]
                    ],
                    'instagram' => [
                        'videos' => [
                            [
                                'title' => 'Avant JARVIS vs Après JARVIS',
                                'description' => 'Transformation visuelle du quotidien',
                                'duration' => '60s',
                                'hook' => 'Transformation lifestyle'
                            ]
                        ]
                    ],
                    'facebook' => [
                        'videos' => [
                            [
                                'title' => 'Comment JARVIS a changé ma vie quotidienne',
                                'description' => 'Témoignage accessible et inspirant',
                                'duration' => '4-5 min',
                                'hook' => 'Transformation personnelle'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Clone Vidéo TikTok - Analyse et Repurpose',
                'description' => 'IA qui analyse n\'importe quelle vidéo TikTok et crée des variations inspirées automatiquement.',
                'priority' => 2,
                'viral_potential' => 5,
                'estimated_videos' => 2,
                'platforms' => ['youtube', 'tiktok', 'linkedin', 'instagram'],
                'video_details' => [
                    'youtube' => [
                        'videos' => [
                            [
                                'title' => 'Je clone n\'importe quelle vidéo TikTok avec l\'IA',
                                'description' => 'Démonstration complète du processus de clonage légal',
                                'duration' => '12-15 min',
                                'hook' => 'Clonage vidéo viral en direct'
                            ]
                        ]
                    ],
                    'tiktok' => [
                        'videos' => [
                            [
                                'title' => 'Cette IA copie n\'importe quelle vidéo virale',
                                'description' => 'Demo choc du clonage en action',
                                'duration' => '50s',
                                'hook' => 'Révélation technique viral'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Posts Tous Réseaux - Publication Multi-Plateformes',
                'description' => 'Automatisation qui publie un seul post sur 15 réseaux sociaux différents en même temps.',
                'priority' => 3,
                'viral_potential' => 5,
                'estimated_videos' => 1,
                'platforms' => ['youtube', 'tiktok', 'linkedin', 'instagram', 'facebook'],
                'video_details' => [
                    'youtube' => [
                        'videos' => [
                            [
                                'title' => 'Je publie sur 15 réseaux sociaux en 1 clic',
                                'description' => 'Automation complète multi-plateformes',
                                'duration' => '10-12 min',
                                'hook' => 'Publication massive automatique'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => '30 Jours Contenu - Génération Massive',
                'description' => 'IA qui génère un mois complet de contenu personnalisé en 1 minute chrono.',
                'priority' => 4,
                'viral_potential' => 5,
                'estimated_videos' => 1,
                'platforms' => ['youtube', 'tiktok', 'linkedin', 'instagram'],
                'video_details' => [
                    'youtube' => [
                        'videos' => [
                            [
                                'title' => '30 jours de contenu créés en 1 minute',
                                'description' => 'Solution miracle au blocage créatif',
                                'duration' => '11-13 min',
                                'hook' => 'Génération contenu miracle'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        foreach ($top10Workflows as $workflow) {
            VideoContentPlan::updateOrCreate(
                ['workflow_name' => $workflow['name']],
                [
                    'workflow_name' => $workflow['name'],
                    'workflow_description' => $workflow['description'],
                    'priority' => $workflow['priority'],
                    'viral_potential' => $workflow['viral_potential'],
                    'estimated_videos' => $workflow['estimated_videos'],
                    'platforms' => $workflow['platforms'],
                    'video_details' => $workflow['video_details'] ?? [],
                ]
            );
        }
    }

    /**
     * Generate basic plans for all workflows (à implémenter plus tard)
     */
    private function generateAllWorkflowPlans()
    {
        // TODO: Scanner tous les workflows et créer des plans basiques
        // Pour l'instant on se concentre sur le TOP 10
    }
}