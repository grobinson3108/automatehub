<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VideoContentPlan;
use App\Models\VideoIdea;

class ImportVideoIdeasCommand extends Command
{
    protected $signature = 'video-ideas:import';
    protected $description = 'Import video ideas from existing workflow video_details JSON';

    public function handle()
    {
        $this->info('🎬 Importation des idées vidéos depuis les workflows existants...');

        $workflows = VideoContentPlan::whereNotNull('video_details')->get();
        $totalImported = 0;
        $totalSkipped = 0;

        foreach ($workflows as $workflow) {
            $this->line("📋 Traitement du workflow: {$workflow->workflow_name}");

            $videoDetails = $workflow->video_details;
            if (!is_array($videoDetails)) {
                $this->warn("⚠️  video_details invalide pour le workflow {$workflow->id}");
                continue;
            }

            foreach ($videoDetails as $platform => $platformData) {
                if (!isset($platformData['videos']) || !is_array($platformData['videos'])) {
                    continue;
                }

                $this->line("   🎯 Plateforme: {$platform}");

                foreach ($platformData['videos'] as $index => $video) {
                    // Vérifier si l'idée existe déjà
                    $existingIdea = VideoIdea::where('video_content_plan_id', $workflow->id)
                        ->where('platform', $platform)
                        ->where('video_index', $index)
                        ->first();

                    if ($existingIdea) {
                        $totalSkipped++;
                        continue;
                    }

                    try {
                        $videoIdea = VideoIdea::create([
                            'video_content_plan_id' => $workflow->id,
                            'platform' => $platform,
                            'video_index' => $index,
                            'title' => $video['title'] ?? "Vidéo {$platform} #{$index}",
                            'description' => $video['description'] ?? $video['content'] ?? '',
                            'hook' => $video['hook'] ?? null,
                            'hashtags' => $this->processHashtags($video['hashtags'] ?? []),
                            'thumbnail_concept' => $video['thumbnail_concept'] ?? $video['thumbnail'] ?? null,
                            'duration' => $video['duration'] ?? null,
                            'difficulty' => $video['difficulty'] ?? null,
                            'video_type' => $video['type'] ?? null,
                            'call_to_action' => $video['call_to_action'] ?? $video['cta'] ?? null,
                            'target_audience' => $video['target_audience'] ?? null,
                            'estimated_views' => $video['estimated_views'] ?? null,
                            'viral_potential' => $video['viral_potential'] ?? 5,
                            'music' => $video['music'] ?? null,
                            'transitions' => $video['transitions'] ?? null,
                            'source_data' => $video
                        ]);

                        $totalImported++;
                        $this->line("      ✅ Importé: {$videoIdea->title}");

                    } catch (\Exception $e) {
                        $this->error("      ❌ Erreur lors de l'import de la vidéo {$index}: " . $e->getMessage());
                    }
                }
            }
        }

        $this->newLine();
        $this->info("📊 Résumé de l'importation:");
        $this->line("   ✅ Idées importées: {$totalImported}");
        $this->line("   ⏭️  Idées ignorées (déjà existantes): {$totalSkipped}");

        if ($totalImported > 0) {
            $this->newLine();
            $this->info("🎉 Importation terminée avec succès !");
            $this->line("💡 Vous pouvez maintenant accéder à la gestion des idées vidéos dans l'admin.");
        } else {
            $this->warn("⚠️  Aucune nouvelle idée n'a été importée.");
        }

        return 0;
    }

    private function processHashtags($hashtags)
    {
        if (is_string($hashtags)) {
            // Si c'est une chaîne, la découper par espaces ou virgules
            $tags = preg_split('/[\s,]+/', $hashtags);
            return array_filter(array_map('trim', $tags));
        }

        if (is_array($hashtags)) {
            return array_filter(array_map('trim', $hashtags));
        }

        return [];
    }
}
