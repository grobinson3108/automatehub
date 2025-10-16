<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;

class QuizService
{
    /**
     * Questions du quiz n8n avec leurs réponses
     */
    private array $questions = [
        [
            'question' => 'Qu\'est-ce que n8n ?',
            'options' => [
                'A' => 'Un langage de programmation',
                'B' => 'Un outil d\'automatisation workflow',
                'C' => 'Une base de données',
                'D' => 'Un serveur web'
            ],
            'correct' => 'B',
            'level' => 'beginner'
        ],
        [
            'question' => 'Comment appelle-t-on les éléments connectables dans n8n ?',
            'options' => [
                'A' => 'Modules',
                'B' => 'Plugins',
                'C' => 'Nodes',
                'D' => 'Components'
            ],
            'correct' => 'C',
            'level' => 'beginner'
        ],
        [
            'question' => 'Quel est le format de données principal utilisé par n8n ?',
            'options' => [
                'A' => 'XML',
                'B' => 'JSON',
                'C' => 'CSV',
                'D' => 'YAML'
            ],
            'correct' => 'B',
            'level' => 'intermediate'
        ],
        [
            'question' => 'Comment peut-on déclencher un workflow n8n ?',
            'options' => [
                'A' => 'Uniquement manuellement',
                'B' => 'Par webhook, cron ou trigger',
                'C' => 'Seulement par email',
                'D' => 'Uniquement par API'
            ],
            'correct' => 'B',
            'level' => 'intermediate'
        ],
        [
            'question' => 'Que permet de faire l\'expression {{ $json.data }} dans n8n ?',
            'options' => [
                'A' => 'Créer un nouveau JSON',
                'B' => 'Accéder aux données du node précédent',
                'C' => 'Valider le format JSON',
                'D' => 'Convertir en XML'
            ],
            'correct' => 'B',
            'level' => 'expert'
        ]
    ];

    /**
     * Obtenir toutes les questions du quiz
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }

    /**
     * Calculer le niveau n8n basé sur les réponses
     */
    public function calculateLevel(array $answers): string
    {
        $score = 0;
        $beginnerQuestions = 0;
        $intermediateQuestions = 0;
        $expertQuestions = 0;

        foreach ($this->questions as $index => $question) {
            $userAnswer = $answers[$index] ?? null;
            
            if ($userAnswer === $question['correct']) {
                $score++;
                
                switch ($question['level']) {
                    case 'beginner':
                        $beginnerQuestions++;
                        break;
                    case 'intermediate':
                        $intermediateQuestions++;
                        break;
                    case 'expert':
                        $expertQuestions++;
                        break;
                }
            }
        }

        // Logique de détermination du niveau
        if ($score >= 4 && $expertQuestions >= 1) {
            return 'expert';
        } elseif ($score >= 3 && $intermediateQuestions >= 1) {
            return 'intermediate';
        } else {
            return 'beginner';
        }
    }

    /**
     * Attribuer les badges de départ selon le niveau
     */
    public function assignStarterBadges(User $user, string $level): void
    {
        $badgesToAssign = [];

        // Badge de bienvenue pour tous
        $welcomeBadge = Badge::where('slug', 'welcome')->first();
        if ($welcomeBadge) {
            $badgesToAssign[] = $welcomeBadge->id;
        }

        // Badges selon le niveau
        switch ($level) {
            case 'expert':
                $expertBadge = Badge::where('slug', 'n8n-expert')->first();
                if ($expertBadge) {
                    $badgesToAssign[] = $expertBadge->id;
                }
                // Fall through pour inclure aussi les badges inférieurs
            case 'intermediate':
                $intermediateBadge = Badge::where('slug', 'n8n-intermediate')->first();
                if ($intermediateBadge) {
                    $badgesToAssign[] = $intermediateBadge->id;
                }
                // Fall through pour inclure aussi le badge débutant
            case 'beginner':
                $beginnerBadge = Badge::where('slug', 'n8n-beginner')->first();
                if ($beginnerBadge) {
                    $badgesToAssign[] = $beginnerBadge->id;
                }
                break;
        }

        // Badge professionnel si applicable
        if ($user->is_professional) {
            $proBadge = Badge::where('slug', 'professional')->first();
            if ($proBadge) {
                $badgesToAssign[] = $proBadge->id;
            }
        }

        // Attacher les badges à l'utilisateur
        if (!empty($badgesToAssign)) {
            $user->badges()->attach($badgesToAssign, [
                'earned_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Valider les réponses du quiz
     */
    public function validateQuizAnswers(array $answers): bool
    {
        // Vérifier que toutes les questions ont une réponse
        if (count($answers) !== count($this->questions)) {
            return false;
        }

        // Vérifier que chaque réponse est valide (A, B, C, ou D)
        foreach ($answers as $answer) {
            if (!in_array($answer, ['A', 'B', 'C', 'D'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtenir le score détaillé du quiz
     */
    public function getDetailedScore(array $answers): array
    {
        $score = 0;
        $details = [];

        foreach ($this->questions as $index => $question) {
            $userAnswer = $answers[$index] ?? null;
            $isCorrect = $userAnswer === $question['correct'];
            
            if ($isCorrect) {
                $score++;
            }

            $details[] = [
                'question' => $question['question'],
                'user_answer' => $userAnswer,
                'correct_answer' => $question['correct'],
                'is_correct' => $isCorrect,
                'level' => $question['level']
            ];
        }

        return [
            'score' => $score,
            'total' => count($this->questions),
            'percentage' => round(($score / count($this->questions)) * 100, 2),
            'details' => $details
        ];
    }
}
