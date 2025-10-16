<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VideoIdea;
use App\Models\VideoPublication;
use App\Models\VideoContentPlan;

class OptimizeVideoSystemCommand extends Command
{
    protected $signature = 'video-system:optimize {--clean-duplicates : Nettoyer les publications en doublon}';
    protected $description = 'Optimise le système de gestion vidéo';

    public function handle()
    {
        $this->info('🚀 Optimisation du système de gestion vidéo...');

        if ($this->option('clean-duplicates')) {
            $this->cleanDuplicatePublications();
        }

        $this->optimizeVideoIdeas();
        $this->generateMissingSchedules();
        $this->updateStatistics();

        $this->newLine();
        $this->info('✅ Optimisation terminée avec succès !');

        return 0;
    }

    private function cleanDuplicatePublications()
    {
        $this->info('🧹 Nettoyage des publications en doublon...');

        $duplicates = VideoPublication::select('video_content_plan_id', 'platform', 'video_index')
            ->groupBy('video_content_plan_id', 'platform', 'video_index')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $cleaned = 0;
        foreach ($duplicates as $duplicate) {
            $publications = VideoPublication::where('video_content_plan_id', $duplicate->video_content_plan_id)
                ->where('platform', $duplicate->platform)
                ->where('video_index', $duplicate->video_index)
                ->orderBy('created_at', 'desc')
                ->get();

            // Garder le plus récent, supprimer les autres
            $publications->skip(1)->each(function($pub) use (&$cleaned) {
                $pub->delete();
                $cleaned++;
            });
        }

        $this->line("   ✅ {$cleaned} publications en doublon supprimées");
    }

    private function optimizeVideoIdeas()
    {
        $this->info('🎯 Optimisation des idées vidéos...');

        $ideas = VideoIdea::whereNull('viral_potential')->orWhere('viral_potential', 0)->get();
        $updated = 0;

        foreach ($ideas as $idea) {
            // Calculer le potentiel viral basé sur les mots-clés
            $viral = $this->calculateViralPotential($idea);
            $idea->update(['viral_potential' => $viral]);
            $updated++;
        }

        $this->line("   ✅ {$updated} idées vidéos optimisées");
    }

    private function generateMissingSchedules()
    {
        $this->info('📅 Génération des plannings manquants...');

        $workflows = VideoContentPlan::whereHas('videoIdeas')
            ->whereDoesntHave('publications', function($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->limit(5) // Limiter pour éviter la surcharge
            ->get();

        $generated = 0;
        foreach ($workflows as $workflow) {
            try {
                $scheduler = new \App\Services\ContentSchedulerService();
                $publications = $scheduler->generateOptimalSchedule($workflow, today()->next('monday'));
                $generated += count($publications);
                $this->line("   📋 Planning généré pour: {$workflow->workflow_name} (" . count($publications) . " publications)");
            } catch (\Exception $e) {
                $this->warn("   ⚠️  Erreur pour {$workflow->workflow_name}: " . $e->getMessage());
            }
        }

        $this->line("   ✅ {$generated} publications planifiées");
    }

    private function updateStatistics()
    {
        $this->info('📊 Mise à jour des statistiques...');

        $stats = [
            'total_ideas' => VideoIdea::count(),
            'total_publications' => VideoPublication::count(),
            'total_workflows' => VideoContentPlan::whereHas('videoIdeas')->count(),
            'planned_publications' => VideoPublication::where('status', 'planned')->count(),
            'published' => VideoPublication::where('status', 'published')->count(),
        ];

        $this->table(['Métrique', 'Valeur'], [
            ['Idées vidéos', $stats['total_ideas']],
            ['Publications totales', $stats['total_publications']],
            ['Workflows actifs', $stats['total_workflows']],
            ['Publications planifiées', $stats['planned_publications']],
            ['Publications réalisées', $stats['published']],
        ]);
    }

    private function calculateViralPotential($idea)
    {
        $score = 5; // Base score

        // Analyser le titre et la description
        $content = strtolower($idea->title . ' ' . $idea->description);

        // Mots-clés viraux
        $viralKeywords = [
            'secret', 'astuce', 'hack', 'incroyable', 'révolutionnaire', 'automatisation',
            'intelligence artificielle', 'ia', 'gratuit', 'facile', 'rapide', 'ultime'
        ];

        foreach ($viralKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                $score += 1;
            }
        }

        // Plateforme bonus
        if ($idea->platform === 'tiktok') {
            $score += 2;
        } elseif ($idea->platform === 'instagram') {
            $score += 1;
        }

        return min(10, max(1, $score));
    }
}
