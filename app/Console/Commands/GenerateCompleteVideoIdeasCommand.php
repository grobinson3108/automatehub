<?php

namespace App\Console\Commands;

use App\Models\VideoContentPlan;
use App\Models\VideoIdea;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateCompleteVideoIdeasCommand extends Command
{
    protected $signature = 'video:generate-complete-ideas {--workflow_id=} {--force} {--batch_size=10}';
    protected $description = 'Génère des idées vidéo complètes pour les 142+ workflows avec métadonnées riches';

    private $platforms = ['youtube', 'youtube_shorts', 'tiktok', 'instagram', 'facebook', 'linkedin'];

    private $optimalTimes = [
        'youtube' => ['14:00', '17:00', '19:00'],
        'youtube_shorts' => ['12:00', '18:00', '21:00'],
        'tiktok' => ['09:00', '12:00', '19:00'],
        'instagram' => ['11:00', '14:00', '17:00'],
        'facebook' => ['13:00', '15:00', '18:00'],
        'linkedin' => ['08:00', '12:00', '17:00']
    ];

    private $publicationFrequencies = [
        'youtube' => 3,           // 3 vidéos par semaine
        'youtube_shorts' => 2,    // 2 shorts par semaine
        'tiktok' => 4,           // 4 vidéos par semaine
        'instagram' => 3,        // 3 posts par semaine
        'facebook' => 2,         // 2 posts par semaine
        'linkedin' => 4          // 4 posts par semaine
    ];

    public function handle()
    {
        $this->info('🚀 Génération des idées vidéo complètes pour 142+ workflows...');

        $workflowId = $this->option('workflow_id');
        $force = $this->option('force');
        $batchSize = $this->option('batch_size') ?? 10;

        if ($workflowId) {
            $workflows = VideoContentPlan::where('id', $workflowId)->get();
        } else {
            $workflows = VideoContentPlan::orderBy('priority')->get();
        }

        if ($workflows->isEmpty()) {
            $this->error('Aucun workflow trouvé.');
            return;
        }

        $this->info("📊 {$workflows->count()} workflows à traiter");

        // Traitement par batch pour optimiser les performances
        $batchIndex = 0;
        foreach ($workflows->chunk($batchSize) as $batch) {
            $batchIndex++;
            $this->info("📦 Traitement batch {$batchIndex}");

            foreach ($batch as $workflow) {
                $this->generateIdeasForWorkflow($workflow, $force);
            }

            // Pause entre les batches pour éviter la surcharge
            sleep(1);
        }

        $this->info('✅ Génération terminée pour tous les workflows !');
        $this->showStatistics();
    }

    private function generateIdeasForWorkflow($workflow, $force)
    {
        $this->line("📹 {$workflow->workflow_name} (ID: {$workflow->id})");

        // Vérifier s'il y a déjà des idées
        if (!$force && $workflow->videoIdeas()->count() > 0) {
            $this->comment("   ⚠️  Idées déjà existantes");
            return;
        }

        // Supprimer les anciennes idées si force
        if ($force) {
            $workflow->videoIdeas()->delete();
        }

        // Déterminer le nombre d'idées selon l'intérêt
        $videoCount = $this->getVideoCountForWorkflow($workflow);

        $totalCreated = 0;
        foreach ($this->platforms as $platform) {
            $created = $this->generateIdeasForPlatform($workflow, $platform, $videoCount);
            $totalCreated += $created;
        }

        $this->info("   ✅ {$totalCreated} idées créées");
    }

    private function getVideoCountForWorkflow($workflow)
    {
        // Workflows les plus intéressants (priority 1-15 ou viral_potential >= 4)
        if ($workflow->priority <= 15 || $workflow->viral_potential >= 4) {
            return 2; // 2 idées par plateforme
        }

        return 1; // 1 idée par plateforme pour les autres
    }

    private function generateIdeasForPlatform($workflow, $platform, $videoCount)
    {
        $createdCount = 0;

        for ($i = 1; $i <= $videoCount; $i++) {
            $ideaData = $this->generateRichVideoIdea($workflow, $platform, $i);

            VideoIdea::create([
                'video_content_plan_id' => $workflow->id,
                'platform' => $platform,
                'video_index' => $i,
                'title' => $ideaData['title'],
                'description' => $ideaData['description'],
                'hook' => $ideaData['hook'],
                'hashtags' => json_encode($ideaData['hashtags']),
                'thumbnail_concept' => $ideaData['thumbnail_concept'],
                'duration' => $ideaData['duration'],
                'difficulty' => $ideaData['difficulty'],
                'video_type' => $ideaData['video_type'],
                'call_to_action' => $ideaData['call_to_action'],
                'target_audience' => $ideaData['target_audience'],
                'estimated_views' => $ideaData['estimated_views'],
                'viral_potential' => $ideaData['viral_potential'],
                'music' => $ideaData['music'] ?? null,
                'transitions' => $ideaData['transitions'] ?? null,
                'source_data' => json_encode($ideaData['source_data']),
                'optimal_publish_time' => $this->getOptimalPublishTime($platform),
                'scheduled_date' => $this->getScheduledDate($workflow, $platform, $i)
            ]);

            $createdCount++;
        }

        return $createdCount;
    }

    private function generateRichVideoIdea($workflow, $platform, $index)
    {
        $workflowName = $workflow->workflow_name;
        $description = $workflow->workflow_description ?? 'Workflow d\'automatisation puissant avec n8n';

        switch ($platform) {
            case 'youtube':
                return $this->generateYouTubeIdea($workflowName, $description, $index, $workflow->viral_potential ?? 3);

            case 'youtube_shorts':
                return $this->generateYouTubeShortsIdea($workflowName, $description, $index, $workflow->viral_potential ?? 3);

            case 'tiktok':
                return $this->generateTikTokIdea($workflowName, $description, $index, $workflow->viral_potential ?? 3);

            case 'instagram':
                return $this->generateInstagramIdea($workflowName, $description, $index, $workflow->viral_potential ?? 3);

            case 'facebook':
                return $this->generateFacebookIdea($workflowName, $description, $index, $workflow->viral_potential ?? 3);

            case 'linkedin':
                return $this->generateLinkedInIdea($workflowName, $description, $index, $workflow->viral_potential ?? 3);
        }
    }

    private function generateYouTubeIdea($workflow, $description, $index, $viralPotential)
    {
        $titles = [
            "TUTO {$workflow} avec n8n (COMPLET 2025)",
            "J'ai automatisé {$workflow} - Résultats FOUS !",
            "{$workflow} : L'automation QUI CHANGE TOUT",
            "Comment créer {$workflow} en 15 min",
            "Cette automation {$workflow} va vous CHOQUER"
        ];

        $hooks = [
            "Si vous passez encore des heures sur les tâches répétitives, cette vidéo va révolutionner votre workflow",
            "Je vais vous montrer comment {$workflow} peut vous faire gagner 15h par semaine minimum",
            "Voici comment j'ai complètement automatisé {$workflow} et pourquoi c'est révolutionnaire",
            "3 mois que j'utilise cette automation {$workflow} - les résultats vont vous surprendre",
            "Cette automation a transformé ma productivité, elle va faire pareil pour vous"
        ];

        $thumbnailConcepts = [
            "Interface n8n avec {$workflow} + flèches colorées + texte 'AUTOMATIQUE' + visage étonné",
            "Split screen chaos vs automation + timer + texte 'RÉVOLUTIONNAIRE'",
            "Écran workflow en action + stats impressionnantes + effet néon",
            "Avant/après productivité + interface n8n + émojis choc 🤯⚡",
            "Setup complet {$workflow} + checkmarks verts + texte 'FONCTIONNE !'"
        ];

        return [
            'title' => $titles[$index - 1] ?? $titles[0],
            'description' => "🚀 RÉVOLUTIONNAIRE ! Automatisez complètement {$workflow} avec n8n en 2025.\n\n{$description}\n\n📋 PROGRAMME COMPLET :\n✅ Setup de A à Z (étape par étape)\n✅ Optimisations PRO pour performances max\n✅ Gestion erreurs + monitoring avancé\n✅ Cas d'usage concrets + exemples\n✅ Template prêt à l'emploi\n\n🎯 RÉSULTATS GARANTIS :\n• 10x plus rapide qu'en manuel\n• 0% d'erreur humaine\n• Économie de 15h/semaine minimum\n• ROI immédiat dès le 1er jour\n\n💾 TÉLÉCHARGEMENT GRATUIT :\n→ Workflow complet n8n\n→ Documentation PDF\n→ Vidéos bonus\n→ Support communauté\n\n🔗 Lien : automatehub.fr\n\n⏰ CHAPTERS :\n00:00 Introduction + démonstration\n02:30 Installation et prérequis\n05:45 Configuration étape par étape\n09:15 Optimisations avancées\n12:30 Tests et debugging\n15:45 Cas d'usage réels\n18:00 Conclusion + ressources\n\n🔥 ABONNEZ-VOUS pour plus d'automations révolutionnaires !\n\n#Automation #n8n #Productivité #NoCode #Workflow #Tutorial #Français #2025",
            'hook' => $hooks[$index - 1] ?? $hooks[0],
            'hashtags' => ['automation', 'n8n', 'productivité', 'nocode', 'workflow', 'tutorial', 'français', '2025'],
            'thumbnail_concept' => $thumbnailConcepts[$index - 1] ?? $thumbnailConcepts[0],
            'duration' => '15-20 min',
            'difficulty' => 'Intermédiaire',
            'video_type' => 'Tutorial complet avancé',
            'call_to_action' => 'TÉLÉCHARGEZ le workflow complet + bonus en description ! Abonnez-vous pour plus d\'automations révolutionnaires 🚀',
            'target_audience' => 'Entrepreneurs, freelancers, responsables IT, passionnés automation, business owners',
            'estimated_views' => rand(8000, 75000) * max(1, $viralPotential / 3),
            'viral_potential' => min(5, $viralPotential + 1),
            'source_data' => [
                'keywords' => ['automation', 'n8n', 'productivity', 'workflow', 'tutorial', 'français'],
                'competition_level' => 'Moyen-Élevé',
                'monetization_potential' => 'Très Élevé',
                'engagement_rate' => '6-12%',
                'retention_target' => '65%+'
            ]
        ];
    }

    private function generateYouTubeShortsIdea($workflow, $description, $index, $viralPotential)
    {
        $concepts = [
            "Cette automation {$workflow} est DINGUE 🤯",
            "POV: Tu découvres {$workflow} automatisé",
            "{$workflow} en 60 secondes chrono ⚡",
            "Regarde cette automation FOLLE",
            "Cette automation va te SAUVER"
        ];

        $hooks = [
            "POV: Tu viens de découvrir l'automation la plus folle de ta vie",
            "Cette automation fait en 1 seconde ce qui te prend 3 heures",
            "Regarde bien, cette automation va te choquer complètement",
            "Tu ne vas jamais croire ce que fait cette automation",
            "Cette automation {$workflow} va changer ta vie pour toujours"
        ];

        return [
            'title' => $concepts[$index - 1] ?? $concepts[0],
            'description' => "🤯 Cette automation {$workflow} va EXPLOSER ton cerveau !\n\n{$description}\n\n⚡ En 60 secondes, découvre comment automatiser complètement {$workflow} avec n8n.\n\n🔥 Cette automation :\n• Fait le travail de 10 personnes\n• Fonctionne 24h/24\n• 0% d'erreur\n• Setup en 5 min\n\n💾 Workflow GRATUIT ici : automatehub.fr\n\n🚨 Follow pour plus d'automations qui changent la vie !\n\n#automation #n8n #productivity #shorts #workflow #viral #tech #trending #nocode #amazing #france",
            'hook' => $hooks[$index - 1] ?? $hooks[0],
            'hashtags' => ['automation', 'n8n', 'productivity', 'shorts', 'workflow', 'viral', 'tech', 'trending', 'nocode', 'amazing', 'france'],
            'thumbnail_concept' => "Interface {$workflow} en action + émojis choc 🤯⚡🔥 + texte 'AUTOMATIQUE' + effets visuels",
            'duration' => '45-60 secondes',
            'difficulty' => 'Débutant',
            'video_type' => 'Demo choc viral',
            'call_to_action' => 'Workflow GRATUIT en bio ! FOLLOW pour plus d\'automations folles 🔥 LIKE si ça t\'a impressionné !',
            'target_audience' => 'Gen Z, millennials, tech enthusiasts, entrepreneurs débutants, étudiants',
            'estimated_views' => rand(25000, 200000) * max(1, $viralPotential / 2),
            'viral_potential' => min(5, $viralPotential + 2),
            'music' => 'Son trending tech/productivity énergique ou audio original percutant',
            'transitions' => 'Cuts ultra-rapides, zooms dynamiques, text overlays animés, effets de vitesse',
            'source_data' => [
                'optimal_posting_time' => '18:00-22:00',
                'trending_elements' => ['Choc visuel', 'Rapidité', 'Transformation'],
                'hook_duration' => '3 secondes max',
                'retention_strategy' => 'Maintenir suspense jusqu\'à la fin'
            ]
        ];
    }

    private function getOptimalPublishTime($platform)
    {
        $times = $this->optimalTimes[$platform] ?? ['12:00'];
        return $times[array_rand($times)];
    }

    private function getScheduledDate($workflow, $platform, $index)
    {
        // Répartition intelligente sur 60 jours pour éviter la surcharge
        $baseDelay = ($workflow->id * 3 + array_search($platform, $this->platforms) * 7 + $index * 2) % 60;
        return Carbon::now()->addDays($baseDelay);
    }

    private function showStatistics()
    {
        $totalIdeas = VideoIdea::count();
        $totalWorkflows = VideoContentPlan::count();
        $platformStats = [];

        foreach ($this->platforms as $platform) {
            $platformStats[$platform] = VideoIdea::where('platform', $platform)->count();
        }

        $this->info("\n📊 STATISTIQUES FINALES :");
        $this->info("🎬 Workflows traités : {$totalWorkflows}");
        $this->info("💡 Idées générées : {$totalIdeas}");
        $this->info("📱 Répartition par plateforme :");

        foreach ($platformStats as $platform => $count) {
            $this->line("   • {$platform}: {$count} idées");
        }
    }

    private function generateTikTokIdea($workflow, $description, $index, $viralPotential)
    {
        $concepts = [
            ['type' => 'POV viral', 'trend' => 'comedy'],
            ['type' => 'Transformation choc', 'trend' => 'inspiring'],
            ['type' => 'Demo rapide', 'trend' => 'educational'],
            ['type' => 'Reaction authentique', 'trend' => 'shock'],
            ['type' => 'Storytime captivant', 'trend' => 'personal']
        ];

        $concept = $concepts[$index - 1] ?? $concepts[0];

        $titles = [
            "POV: Tu découvres {$workflow} automatisé 🤯",
            "Cette automation {$workflow} m'a sauvé la vie",
            "Regarde ce que fait cette automation DINGUE",
            "Ma réaction à {$workflow} automatisé",
            "Comment {$workflow} a changé ma vie"
        ];

        $hooks = [
            "POV: Tu viens de découvrir l'automation la plus folle de ta vie",
            "Cette automation fait en 1 seconde ce qui te prend 5 heures",
            "Regarde bien, cette automation va te choquer à vie",
            "Ma vraie réaction quand j'ai testé cette automation",
            "Laisse-moi te raconter comment cette automation a tout changé"
        ];

        return [
            'title' => $titles[$index - 1] ?? $titles[0],
            'description' => "Cette automation {$workflow} est complètement DINGUE ! 🤯\n\n{$description}\n\nElle fait automatiquement ce qui te prend des heures manuellement.\n\n🔥 Cette automation :\n• Fait le travail de 20 personnes\n• Marche 24h/24, 7j/7\n• 0% d'erreur JAMAIS\n• Setup en 2 minutes chrono\n\nWorkflow GRATUIT ici : automatehub.fr\n\n#automation #n8n #productivity #tech #viral #fyp #trending #workflow #nocode #amazing #france #dingue #revolution",
            'hook' => $hooks[$index - 1] ?? $hooks[0],
            'hashtags' => ['automation', 'n8n', 'productivity', 'tech', 'viral', 'fyp', 'trending', 'workflow', 'nocode', 'amazing', 'france', 'dingue', 'revolution'],
            'thumbnail_concept' => "Visage expressif choqué + interface automation en arrière + texte VIRAL + émojis 🤯⚡🔥",
            'duration' => '15-60 secondes',
            'difficulty' => 'Débutant',
            'video_type' => $concept['type'],
            'call_to_action' => 'FOLLOW pour plus d\'automations qui changent la vie ! Workflow GRATUIT en bio 🔗 LIKE si ça t\'impressionne !',
            'target_audience' => 'Gen Z, tech lovers, étudiants, jeunes entrepreneurs, créateurs de contenu',
            'estimated_views' => rand(75000, 800000) * max(1, $viralPotential / 2),
            'viral_potential' => min(5, $viralPotential + 3),
            'music' => "Son trending {$concept['trend']} du moment ou audio original énergique",
            'transitions' => 'Jump cuts ultra-rapides, effets zoom, overlays texte animés, split screens dynamiques',
            'source_data' => [
                'best_posting_times' => ['09:00', '12:00', '18:00', '21:00'],
                'trending_elements' => ['POV', 'transformation', 'shock factor', 'relatability'],
                'video_style' => $concept['trend'],
                'engagement_tactics' => ['Question hook', 'Suspense', 'Call to action fort']
            ]
        ];
    }

    private function generateInstagramIdea($workflow, $description, $index, $viralPotential)
    {
        $formats = ['Reel viral', 'Carousel éducatif', 'Story highlight', 'Post inspirant', 'Tutorial express'];
        $format = $formats[$index - 1] ?? 'Reel viral';

        $titles = [
            "Transformation {$workflow} COMPLÈTE ✨",
            "Avant/Après {$workflow} automatisé",
            "Cette automation {$workflow} change TOUT",
            "Process {$workflow} révolutionnaire",
            "Setup {$workflow} PARFAIT"
        ];

        $aesthetics = [
            'Clean & minimal avec palette pastel',
            'Bold & vibrant avec contrastes forts',
            'Dark mode élégant avec accents colorés',
            'Gradient moderne avec effets glassmorphism',
            'Style magazine avec typographie impactante'
        ];

        return [
            'title' => $titles[$index - 1] ?? $titles[0],
            'description' => "✨ Transformation RÉVOLUTIONNAIRE de {$workflow} avec l'automation n8n !\n\n{$description}\n\n🚀 Cette automation va révolutionner votre workflow et vous faire gagner des heures précieuses chaque jour.\n\n💫 Résultats FOUS :\n• 15x plus rapide\n• 0 erreur humaine JAMAIS\n• Totalement automatique\n• ROI immédiat dès le jour 1\n• Setup en moins de 10 min\n\n🔥 Bénéfices concrets :\n→ 20h/semaine économisées\n→ Stress divisé par 10\n→ Productivité multipliée\n→ Résultats prévisibles\n\n🔗 Workflow GRATUIT : automatehub.fr\n\n📱 SAVE ce post pour plus tard !\n💬 Dis-moi en commentaire si tu veux le tutorial complet\n\n#automation #productivity #n8n #workflow #entrepreneur #business #tech #nocode #optimization #efficiency #reels #viral #france #transformation",
            'hook' => "Cette transformation {$workflow} va complètement révolutionner votre façon de travailler",
            'hashtags' => ['automation', 'productivity', 'n8n', 'workflow', 'entrepreneur', 'business', 'tech', 'nocode', 'optimization', 'efficiency', 'reels', 'viral', 'france', 'transformation'],
            'thumbnail_concept' => "Split avant/après esthétique + interface workflow + texte impact + palette cohérente + émojis stratégiques",
            'duration' => '60-90 secondes',
            'difficulty' => 'Débutant à Intermédiaire',
            'video_type' => $format,
            'call_to_action' => 'SAVE ce post ! FOLLOW @automatehub pour plus d\'automations révolutionnaires ✨ Partage à quelqu\'un qui en a besoin !',
            'target_audience' => 'Entrepreneurs, créateurs de contenu, freelancers, business owners, influenceurs',
            'estimated_views' => rand(25000, 150000) * max(1, $viralPotential / 2),
            'viral_potential' => min(5, $viralPotential + 1),
            'music' => 'Musique inspirante et moderne, trending business/productivity sounds',
            'transitions' => 'Transitions douces, effets de fade, animations fluides, overlay esthétiques',
            'source_data' => [
                'format_type' => $format,
                'aesthetic' => $aesthetics[$index - 1] ?? $aesthetics[0],
                'color_palette' => ['#6366f1', '#8b5cf6', '#06b6d4', '#f59e0b'],
                'best_hashtag_count' => '10-15 hashtags',
                'engagement_strategy' => 'Save + Share + Comment'
            ]
        ];
    }

    private function generateFacebookIdea($workflow, $description, $index, $viralPotential)
    {
        $angles = [
            'Success story personnel',
            'Analyse business détaillée',
            'Tutorial complet accessible',
            'Case study avec chiffres',
            'Guide pratique étape par étape'
        ];

        $angle = $angles[$index - 1] ?? 'Success story personnel';

        $titles = [
            "Comment {$workflow} a transformé mon business (RÉSULTATS FOUS)",
            "Cette automation {$workflow} va vous surprendre",
            "Pourquoi TOUS les entrepreneurs devraient automatiser {$workflow}",
            "Les résultats INCROYABLES de {$workflow} automatisé",
            "Tutorial COMPLET {$workflow} pour entrepreneurs"
        ];

        return [
            'title' => $titles[$index - 1] ?? $titles[0],
            'description' => "🎯 ENTREPRENEURS : Cette automation {$workflow} va RÉVOLUTIONNER votre business !\n\n{$description}\n\n📈 Après 6 mois d'utilisation, voici mes résultats CONCRETS :\n✅ 92% de temps gagné sur cette tâche\n✅ 0% d'erreur humaine (incroyable !)\n✅ ROI de 450% dès le premier mois\n✅ Équipe 10x plus productive et motivée\n✅ Stress divisé par 20\n\n💡 Cette automation gère AUTOMATIQUEMENT tout le processus {$workflow}, de A à Z, sans AUCUNE intervention humaine.\n\n🔥 Je partage le workflow COMPLET (gratuitement) pour aider la communauté d'entrepreneurs français à exploser leurs résultats.\n\n📊 Idéal pour :\n• Entrepreneurs ambitieux\n• PME et TPE en croissance\n• Freelancers qui veulent scaler\n• Agences qui cherchent l'efficacité\n• E-commerce en développement\n• Consultants qui optimisent\n\n💬 Partagez VOS résultats en commentaire si vous testez ! Je réponds à TOUS les commentaires.\n\n👥 PARTAGEZ ce post avec un entrepreneur qui galère avec {$workflow}\n\n🔗 Accès GRATUIT immédiat : automatehub.fr\n\n#entrepreneuriat #automation #productivité #business #n8n #workflow #startup #PME #freelance #entrepreneur #france #reussite #croissance #efficacite",
            'hook' => "Entrepreneurs : cette automation {$workflow} va transformer votre business (mes vrais résultats après 6 mois)",
            'hashtags' => ['entrepreneuriat', 'automation', 'productivité', 'business', 'n8n', 'workflow', 'startup', 'PME', 'freelance', 'entrepreneur', 'france', 'reussite', 'croissance', 'efficacite'],
            'thumbnail_concept' => "Photo pro avec interface workflow + graphiques résultats + texte impact business + émojis chiffres",
            'duration' => '4-10 minutes de lecture',
            'difficulty' => 'Débutant',
            'video_type' => $angle,
            'call_to_action' => 'PARTAGEZ ce post avec un entrepreneur qui en a besoin ! Commentez vos questions, je réponds à TOUS ⬇️',
            'target_audience' => 'Entrepreneurs, dirigeants PME/TPE, business owners, consultants, freelancers',
            'estimated_views' => rand(8000, 40000) * max(1, $viralPotential / 3),
            'viral_potential' => max(1, $viralPotential - 1),
            'source_data' => [
                'post_type' => $angle,
                'engagement_strategy' => 'Question ouverte + call to share + réponses commentaires',
                'optimal_length' => 'Long-form pour crédibilité et autorité',
                'trust_building' => 'Résultats chiffrés + témoignage personnel + preuve sociale'
            ]
        ];
    }

    private function generateLinkedInIdea($workflow, $description, $index, $viralPotential)
    {
        $approaches = [
            'Case Study ROI détaillé',
            'Industry Insight prospectif',
            'Professional Tutorial avancé',
            'Analyse competitive',
            'Innovation Spotlight'
        ];

        $approach = $approaches[$index - 1] ?? 'Case Study ROI détaillé';

        $titles = [
            "Case Study : {$workflow} automatisé (ROI +450%)",
            "L'avenir du {$workflow} : Automation & IA en 2025",
            "Guide professionnel : Automatiser {$workflow}",
            "Analyse ROI : Automation {$workflow} en entreprise",
            "Innovation : Comment {$workflow} transforme l'industrie"
        ];

        return [
            'title' => $titles[$index - 1] ?? $titles[0],
            'description' => "🎯 ANALYSE PROFESSIONNELLE : Impact de l'automation {$workflow} sur la performance business\n\n{$description}\n\n📊 RÉSULTATS OBSERVÉS (étude sur 100+ entreprises) :\n\n✅ Productivité : +340% en moyenne\n✅ Réduction erreurs : -98%\n✅ Temps économisé : 25h/semaine/employé\n✅ ROI moyen : 450% sur 12 mois\n✅ Satisfaction équipe : +75%\n✅ Réduction coûts opérationnels : -60%\n\n🔍 SECTEURS LES PLUS IMPACTÉS :\n• Services professionnels (+400% efficacité)\n• E-commerce (+350% traitement commandes)\n• Consulting (+300% deliverables)\n• Agences marketing (+450% campagnes)\n• Startups tech (+500% scalabilité)\n\n💡 RECOMMANDATIONS STRATÉGIQUES :\n1. Audit des processus existants (2 jours)\n2. Formation équipe spécialisée (1 semaine)\n3. Déploiement progressif (phase pilote)\n4. Monitoring performance en temps réel\n5. Optimisation continue basée sur la data\n\n🚀 Cette automation représente un avantage concurrentiel MAJEUR dans un marché de plus en plus digitalisé.\n\n📈 Les entreprises qui n'automatisent pas {$workflow} prennent un retard CONSIDÉRABLE et perdent des opportunités de croissance.\n\n💼 Nous accompagnons 200+ entreprises dans cette transformation.\n\n💬 Quelle est votre expérience avec l'automation de processus business ?\n\n📥 DM pour une analyse gratuite de vos processus.\n\n#Innovation #Automation #BusinessOptimization #Productivity #DigitalTransformation #ROI #ProcessOptimization #TechLeadership #BusinessIntelligence #Strategy #Efficiency #Growth #Leadership #France",
            'hook' => "L'automation {$workflow} génère 450% de ROI en moyenne - voici l'analyse complète",
            'hashtags' => ['Innovation', 'Automation', 'BusinessOptimization', 'Productivity', 'DigitalTransformation', 'ROI', 'ProcessOptimization', 'TechLeadership', 'BusinessIntelligence', 'Strategy', 'Efficiency', 'Growth', 'Leadership', 'France'],
            'thumbnail_concept' => "Graphiques professionnels + interface workflow + logo entreprise + stats ROI + design corporate",
            'duration' => '8-15 minutes de lecture',
            'difficulty' => 'Professionnel Expert',
            'video_type' => $approach,
            'call_to_action' => 'Partagez votre expérience en commentaire. Connectons-nous pour échanger sur l\'automation business ! DM pour audit gratuit.',
            'target_audience' => 'Dirigeants, CTO, CIO, responsables IT, consultants, business analysts, décideurs',
            'estimated_views' => rand(5000, 25000) * max(1, $viralPotential / 3),
            'viral_potential' => max(1, $viralPotential),
            'source_data' => [
                'professional_tone' => 'Expertise & autorité thought leadership',
                'content_type' => 'Thought leadership avec data',
                'engagement_style' => 'Discussion professionnelle + networking',
                'networking_potential' => 'Très élevé',
                'lead_generation' => 'Audit gratuit + DM strategy'
            ]
        ];
    }
}
