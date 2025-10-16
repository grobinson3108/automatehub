<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VideoContentPlan;
use App\Models\VideoIdea;

class GenerateCreativeIdeasCommand extends Command
{
    protected $signature = 'video-ideas:generate-creative {--limit=10 : Nombre de workflows à traiter}';
    protected $description = 'Génère des idées créatives pour chaque workflow';

    private $platformTemplates = [
        'youtube' => [
            'titles' => [
                'Comment créer {workflow} avec n8n (Guide Complet)',
                'Tutoriel {workflow} : De 0 à Expert en 10 min',
                '{workflow} : La Méthode Qui Va Révolutionner Votre Travail',
                'Automatisation {workflow} : Économisez 10h par Semaine',
                'Le Secret pour Maîtriser {workflow} (Personne ne vous le dira)'
            ],
            'hooks' => [
                'Si vous perdez du temps avec les tâches répétitives, cette vidéo va changer votre vie',
                'Je vais vous montrer comment automatiser {workflow} en moins de 10 minutes',
                '99% des gens ne connaissent pas cette astuce pour {workflow}',
                'Voici comment j\'ai économisé 40 heures par semaine grâce à cette automation',
                'Cette automation {workflow} m\'a fait gagner 5000€ par mois'
            ]
        ],
        'youtube_shorts' => [
            'titles' => [
                '{workflow} en 60 secondes !',
                'Cette automation va vous choquer',
                'POV: Tu découvres {workflow}',
                'Avant/Après : {workflow}',
                'Le hack {workflow} que tu dois connaître'
            ],
            'hooks' => [
                'Tu fais encore ça à la main ? 😱',
                'Cette automation va te faire économiser des heures',
                'POV: Tu découvres l\'automation {workflow}',
                'Regarde ce qui se passe quand j\'active {workflow}',
                'Cette astuce va changer ta productivité'
            ]
        ],
        'tiktok' => [
            'titles' => [
                'Cette automation va te rendre riche 💰',
                'POV: Tu automatises {workflow}',
                'Millionaire mindset avec {workflow}',
                'Cette astuce {workflow} = 🤯',
                'Automation {workflow} (tu vas kiffer)'
            ],
            'hooks' => [
                'Si tu fais encore ça manuellement, cette vidéo est pour toi',
                'Cette automation {workflow} va exploser ton business',
                'Regarde ce qui arrive quand j\'automatise {workflow}',
                'Tu vas regretter de ne pas avoir connu {workflow} plus tôt',
                'Cette astuce {workflow} va changer ta vie'
            ]
        ],
        'instagram' => [
            'titles' => [
                'Automation {workflow} ✨',
                '{workflow} : Game Changer 🚀',
                'Ma routine {workflow} automatisée',
                'Productivité x10 avec {workflow}',
                'Secret pour automatiser {workflow}'
            ],
            'hooks' => [
                'Swipe pour découvrir comment automatiser {workflow}',
                'Cette automation {workflow} a changé ma vie ✨',
                'Tu veux économiser 10h par semaine ? Voici comment',
                'Ma méthode secrète pour automatiser {workflow}',
                'Comment j\'ai automatisé {workflow} (step by step)'
            ]
        ],
        'facebook' => [
            'titles' => [
                'Comment {workflow} peut transformer votre business',
                'Automation {workflow} : Mon expérience après 6 mois',
                'Pourquoi tout entrepreneur devrait connaître {workflow}',
                '{workflow} : L\'outil qui m\'a fait gagner du temps',
                'Ma transformation grâce à l\'automation {workflow}'
            ],
            'hooks' => [
                'Hier encore, je passais des heures sur les tâches répétitives...',
                'Depuis que j\'ai découvert {workflow}, ma productivité a explosé',
                'Si vous êtes entrepreneur, vous devez absolument connaître {workflow}',
                'Cette automation {workflow} m\'a permis de me concentrer sur l\'essentiel',
                'Voici comment {workflow} a transformé ma façon de travailler'
            ]
        ],
        'linkedin' => [
            'titles' => [
                'Comment {workflow} optimise la productivité en entreprise',
                'ROI de l\'automation {workflow} : Mon retour d\'expérience',
                '{workflow} : L\'avenir de l\'efficacité professionnelle',
                'Transformation digitale avec {workflow} : Cas d\'étude',
                'Leadership et automation : Mon approche avec {workflow}'
            ],
            'hooks' => [
                'En tant que dirigeant, l\'automation {workflow} a révolutionné notre efficacité',
                'Nos équipes ont gagné 30% de productivité grâce à {workflow}',
                'Voici comment {workflow} transforme les processus métier',
                'ROI de 400% avec l\'automation {workflow} : voici notre méthode',
                'L\'automation {workflow} : un levier stratégique pour l\'entreprise'
            ]
        ]
    ];

    public function handle()
    {
        $limit = $this->option('limit');
        $this->info("🎨 Génération d'idées créatives pour les {$limit} premiers workflows...");

        $workflows = VideoContentPlan::whereHas('videoIdeas')
            ->orderBy('priority')
            ->limit($limit)
            ->get();

        $totalGenerated = 0;

        foreach ($workflows as $workflow) {
            $this->line("📋 Traitement : {$workflow->workflow_name}");

            // Nettoyer les anciennes idées générées
            VideoIdea::where('video_content_plan_id', $workflow->id)->delete();

            $generated = $this->generateIdeasForWorkflow($workflow);
            $totalGenerated += $generated;

            $this->line("   ✅ {$generated} idées générées");
        }

        $this->newLine();
        $this->info("🎉 Génération terminée ! {$totalGenerated} idées créatives générées au total.");

        return 0;
    }

    private function generateIdeasForWorkflow($workflow)
    {
        $generated = 0;
        $workflowName = $workflow->workflow_name;

        // YouTube (3 vidéos)
        for ($i = 0; $i < 3; $i++) {
            $this->createVideoIdea($workflow, 'youtube', $i, $workflowName);
            $generated++;
        }

        // YouTube Shorts (2 vidéos)
        for ($i = 0; $i < 2; $i++) {
            $this->createVideoIdea($workflow, 'youtube_shorts', $i, $workflowName);
            $generated++;
        }

        // TikTok (4 vidéos)
        for ($i = 0; $i < 4; $i++) {
            $this->createVideoIdea($workflow, 'tiktok', $i, $workflowName);
            $generated++;
        }

        // Instagram (3 vidéos)
        for ($i = 0; $i < 3; $i++) {
            $this->createVideoIdea($workflow, 'instagram', $i, $workflowName);
            $generated++;
        }

        // Facebook (2 vidéos)
        for ($i = 0; $i < 2; $i++) {
            $this->createVideoIdea($workflow, 'facebook', $i, $workflowName);
            $generated++;
        }

        // LinkedIn (4 vidéos)
        for ($i = 0; $i < 4; $i++) {
            $this->createVideoIdea($workflow, 'linkedin', $i, $workflowName);
            $generated++;
        }

        return $generated;
    }

    private function createVideoIdea($workflow, $platform, $index, $workflowName)
    {
        $templates = $this->platformTemplates[$platform];

        $title = str_replace('{workflow}', $workflowName,
            $templates['titles'][array_rand($templates['titles'])]);

        $hook = str_replace('{workflow}', $workflowName,
            $templates['hooks'][array_rand($templates['hooks'])]);

        $description = $this->generateDescription($workflowName, $platform);
        $hashtags = $this->generateHashtags($platform, $workflowName);

        VideoIdea::create([
            'video_content_plan_id' => $workflow->id,
            'platform' => $platform,
            'video_index' => $index,
            'title' => $title,
            'description' => $description,
            'hook' => $hook,
            'hashtags' => $hashtags,
            'thumbnail_concept' => $this->generateThumbnailConcept($platform, $workflowName),
            'duration' => $this->getOptimalDuration($platform),
            'video_type' => $this->getVideoType($platform),
            'call_to_action' => $this->getCallToAction($platform),
            'target_audience' => 'Entrepreneurs, Freelances, Créateurs de contenu',
            'estimated_views' => $this->estimateViews($platform),
            'viral_potential' => rand(6, 10), // Potentiel élevé
            'music' => $platform === 'tiktok' ? 'Trending upbeat' : null,
            'transitions' => $platform === 'tiktok' ? 'Quick cuts, zoom effects' : 'Smooth transitions'
        ]);
    }

    private function generateDescription($workflowName, $platform)
    {
        $base = "Découvrez comment automatiser {$workflowName} et gagner des heures chaque semaine. ";

        return match($platform) {
            'youtube' => $base . "Dans cette vidéo complète, je vous montre étape par étape comment configurer et optimiser cette automation. Timestamps en description !",
            'youtube_shorts' => $base . "Tutoriel express en moins d'une minute !",
            'tiktok' => $base . "Cette astuce va révolutionner votre productivité ! 🚀",
            'instagram' => $base . "Swipe pour voir le before/after de ma productivité ✨",
            'facebook' => $base . "Retour d'expérience après 6 mois d'utilisation. Les résultats vont vous surprendre !",
            'linkedin' => $base . "Analyse ROI et impact business de cette automation. Étude de cas complète.",
            default => $base
        };
    }

    private function generateHashtags($platform, $workflowName)
    {
        $base = ['automation', 'productivity', 'n8n', 'workflow'];

        $specific = match($platform) {
            'youtube' => ['tutorial', 'howto', 'guide', 'tech'],
            'youtube_shorts' => ['shorts', 'quick', 'tips', 'hack'],
            'tiktok' => ['TechTok', 'ProductivityHack', 'LifeHack', 'BusinessTips'],
            'instagram' => ['entrepreneur', 'hustle', 'productive', 'mindset'],
            'facebook' => ['business', 'entrepreneur', 'success', 'tips'],
            'linkedin' => ['business', 'leadership', 'innovation', 'efficiency'],
            default => []
        };

        return array_merge($base, $specific);
    }

    private function generateThumbnailConcept($platform, $workflowName)
    {
        return match($platform) {
            'youtube' => "Miniature avec texte accrocheur, flèches colorées, before/after, expression de surprise",
            'youtube_shorts' => "Texte large et visible, émojis, contraste élevé",
            'tiktok' => "Visuel impactant, texte overlay, émojis tendance",
            'instagram' => "Esthétique épurée, palette cohérente, texte stylisé",
            'facebook' => "Image engageante, texte informatif, call-to-action visuel",
            'linkedin' => "Professionnel mais accrocheur, données/stats, branding",
            default => "Visuel accrocheur avec le workflow {$workflowName}"
        };
    }

    private function getOptimalDuration($platform)
    {
        return match($platform) {
            'youtube' => '8-12 min',
            'youtube_shorts' => '30-60s',
            'tiktok' => '15-30s',
            'instagram' => '30-60s',
            'facebook' => '1-3 min',
            'linkedin' => '2-5 min',
            default => '5 min'
        };
    }

    private function getVideoType($platform)
    {
        return match($platform) {
            'youtube' => 'Tutoriel',
            'youtube_shorts' => 'Quick Tips',
            'tiktok' => 'Trend/Viral',
            'instagram' => 'Stories/Reels',
            'facebook' => 'Storytelling',
            'linkedin' => 'Éducatif',
            default => 'Démonstration'
        };
    }

    private function getCallToAction($platform)
    {
        return match($platform) {
            'youtube' => 'Abonnez-vous et téléchargez le workflow gratuit en description !',
            'youtube_shorts' => 'Follow pour plus d\'astuces automation !',
            'tiktok' => 'Save ce post et follow pour plus de tips !',
            'instagram' => 'Save + Partage si ça t\'a aidé ! 💙',
            'facebook' => 'Partagez votre expérience en commentaire !',
            'linkedin' => 'Qu\'en pensez-vous ? Partagez votre avis !',
            default => 'Découvrez plus sur AutomateHub.fr'
        };
    }

    private function estimateViews($platform)
    {
        return match($platform) {
            'youtube' => rand(1000, 5000),
            'youtube_shorts' => rand(5000, 50000),
            'tiktok' => rand(10000, 100000),
            'instagram' => rand(2000, 20000),
            'facebook' => rand(500, 5000),
            'linkedin' => rand(1000, 10000),
            default => 1000
        };
    }
}
