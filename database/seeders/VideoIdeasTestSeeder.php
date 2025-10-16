<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VideoContentPlan;
use App\Models\VideoIdea;
use App\Services\VideoSchedulingService;
use Carbon\Carbon;

class VideoIdeasTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scheduler = new VideoSchedulingService();

        // Récupérer le premier VideoContentPlan (JARVIS)
        $jarvisPlan = VideoContentPlan::where('workflow_name', 'LIKE', '%JARVIS%')->first();

        if (!$jarvisPlan) {
            $this->command->error('Aucun plan JARVIS trouvé. Veuillez d\'abord exécuter VideoContentController::generateFromWorkflows()');
            return;
        }

        $this->command->info("Création des VideoIdeas pour le plan: {$jarvisPlan->workflow_name}");

        // Créer des VideoIdeas pour JARVIS avec des dates de tournage échelonnées
        $baseDate = today()->addDays(3); // Commencer dans 3 jours

        $videoIdeas = [
            // YouTube - Vidéo 1
            [
                'video_content_plan_id' => $jarvisPlan->id,
                'platform' => 'youtube',
                'video_index' => 0,
                'title' => 'J\'ai créé JARVIS comme Iron Man - Assistant IA complet',
                'description' => 'Tutorial complet pour créer son assistant personnel IA avec n8n. Découvrez comment automatiser complètement votre vie quotidienne avec un assistant ultra-performant.',
                'hook' => 'Création JARVIS étape par étape',
                'hashtags' => ['JARVIS', 'assistant IA', 'n8n automation', 'productivity', 'tutorial français', 'Iron Man', 'intelligence artificielle'],
                'thumbnail_concept' => 'JARVIS interface + Iron Man reference + Before/After productivity',
                'duration' => '12-15 min',
                'difficulty' => 'Intermédiaire',
                'video_type' => 'Tutorial long-form',
                'call_to_action' => 'Télécharge le workflow JARVIS complet en description',
                'target_audience' => 'Entrepreneurs, freelancers, tech enthusiasts',
                'estimated_views' => 1500,
                'viral_potential' => 4,
                'filming_date' => $baseDate->copy(),
                'filming_start_time' => '09:00',
                'filming_end_time' => '12:00',
            ],
            // YouTube - Vidéo 2
            [
                'video_content_plan_id' => $jarvisPlan->id,
                'platform' => 'youtube',
                'video_index' => 1,
                'title' => 'JARVIS vs Assistant Google - Lequel est le meilleur ?',
                'description' => 'Comparaison détaillée entre JARVIS personnalisé et les assistants commerciaux. Résultats surprenants !',
                'hook' => 'Battle des assistants IA',
                'hashtags' => ['JARVIS vs Google', 'assistant comparison', 'AI battle', 'productivity test', 'automation'],
                'thumbnail_concept' => 'VS layout with JARVIS vs Google Assistant logos',
                'duration' => '8-10 min',
                'difficulty' => 'Débutant',
                'video_type' => 'Comparison/Review',
                'call_to_action' => 'Dis-moi en commentaire lequel tu préfères !',
                'target_audience' => 'Tech comparateurs, early adopters',
                'estimated_views' => 2000,
                'viral_potential' => 3,
                'filming_date' => $baseDate->copy()->addDays(1),
                'filming_start_time' => '14:00',
                'filming_end_time' => '16:00',
            ],
            // TikTok - Vidéo 1
            [
                'video_content_plan_id' => $jarvisPlan->id,
                'platform' => 'tiktok',
                'video_index' => 0,
                'title' => 'POV: Tu as JARVIS comme assistant personnel',
                'description' => 'Démonstration rapide des capacités de JARVIS avec transitions dynamiques',
                'hook' => 'POV assistant IA futuriste',
                'hashtags' => ['#JARVIS', '#AssistantIA', '#POV', '#TechTok', '#Automation', '#IronMan', '#Productivity'],
                'thumbnail_concept' => 'Phone screen showing JARVIS interface + futuristic effects',
                'duration' => '45s',
                'difficulty' => 'Facile',
                'video_type' => 'POV/Demo',
                'call_to_action' => 'Follow pour plus d\'automations folles',
                'target_audience' => 'Gen Z, tech lovers, productivity enthusiasts',
                'estimated_views' => 5000,
                'viral_potential' => 5,
                'music' => 'Trending tech/futuristic sound',
                'transitions' => 'Quick cuts, zoom effects, text overlays',
                'filming_date' => $baseDate->copy()->addDays(2),
                'filming_start_time' => '10:00',
                'filming_end_time' => '11:00',
            ],
            // TikTok - Vidéo 2
            [
                'video_content_plan_id' => $jarvisPlan->id,
                'platform' => 'tiktok',
                'video_index' => 1,
                'title' => 'Cette IA gère ma vie mieux que moi',
                'description' => 'Avant/après dramatique avec JARVIS',
                'hook' => 'Transformation lifestyle',
                'hashtags' => ['#AvantApres', '#IA', '#ProductiviteHack', '#TechTok', '#LifeChanger', '#Automation'],
                'thumbnail_concept' => 'Split screen chaos vs organized',
                'duration' => '30s',
                'difficulty' => 'Facile',
                'video_type' => 'Before/After transformation',
                'call_to_action' => 'Workflow gratuit en bio 🔗',
                'target_audience' => 'Procrastinators, young professionals',
                'estimated_views' => 8000,
                'viral_potential' => 5,
                'music' => 'Dramatic transformation trending sound',
                'transitions' => 'Before/after split, time-lapse effect',
                'filming_date' => $baseDate->copy()->addDays(3),
                'filming_start_time' => '15:00',
                'filming_end_time' => '16:00',
            ],
            // LinkedIn - Vidéo 1
            [
                'video_content_plan_id' => $jarvisPlan->id,
                'platform' => 'linkedin',
                'video_index' => 0,
                'title' => 'JARVIS en entreprise : ROI de 300% en 3 mois',
                'description' => 'Case study business avec chiffres concrets',
                'hook' => 'ROI et productivité business',
                'hashtags' => ['#automation', '#business', '#entrepreneurship', '#innovation', '#ROI', '#productivity'],
                'thumbnail_concept' => 'Business charts + JARVIS interface + ROI numbers',
                'duration' => '6-8 min',
                'difficulty' => 'Intermédiaire',
                'video_type' => 'Business Case Study',
                'call_to_action' => 'Que pensez-vous de ce type d\'automation ?',
                'target_audience' => 'Business leaders, entrepreneurs, C-level',
                'estimated_views' => 800,
                'viral_potential' => 4,
                'filming_date' => $baseDate->copy()->addDays(4),
                'filming_start_time' => '08:00',
                'filming_end_time' => '10:00',
            ],
        ];

        foreach ($videoIdeas as $ideaData) {
            $this->command->info("Création VideoIdea: {$ideaData['title']} ({$ideaData['platform']})");

            // Calculer automatiquement les dates de montage et publication
            $filmingTime = $ideaData['filming_start_time'] . ' - ' . $ideaData['filming_end_time'];
            $schedule = $scheduler->calculateSchedule(
                $ideaData['filming_date'],
                $filmingTime,
                $ideaData['platform']
            );

            // Ajouter les dates calculées
            $ideaData['editing_date'] = $schedule['editing_date'];
            $ideaData['editing_start_time'] = $schedule['editing_start_time'];
            $ideaData['editing_end_time'] = $schedule['editing_end_time'];
            $ideaData['publication_date'] = $schedule['publication_date'];
            $ideaData['publication_time'] = $schedule['publication_time'];
            $ideaData['scheduled_datetime'] = $schedule['scheduled_datetime'];
            $ideaData['filming_status'] = 'pending';
            $ideaData['editing_status'] = 'pending';
            $ideaData['publication_status'] = 'pending';

            VideoIdea::create($ideaData);

            $this->command->line("  🎬 Tournage: {$ideaData['filming_date']->format('d/m/Y')} {$filmingTime}");
            $this->command->line("  ✂️ Montage: {$schedule['editing_date']->format('d/m/Y')} {$schedule['editing_start_time']}-{$schedule['editing_end_time']}");
            $this->command->line("  📤 Publication: {$schedule['publication_date']->format('d/m/Y')} {$schedule['publication_time']}");
            $this->command->line("");
        }

        $this->command->info("✅ " . count($videoIdeas) . " VideoIdeas créées avec succès !");
        $this->command->info("🔗 Accédez à: /admin/video-content/{$jarvisPlan->id} pour tester l'édition en temps réel");
    }
}