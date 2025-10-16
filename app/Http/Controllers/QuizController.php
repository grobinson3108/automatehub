<?php

namespace App\Http\Controllers;

use App\Services\QuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    protected QuizService $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    /**
     * Get quiz questions
     */
    public function getQuestions(): JsonResponse
    {
        try {
            $questions = $this->quizService->getQuestions();
            return response()->json([
                'success' => true,
                'questions' => $questions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement du quiz'
            ], 500);
        }
    }

    /**
     * Submit quiz answers
     */
    public function submitAnswers(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'answers' => 'required|array|size:5',
                'answers.*' => 'required|in:A,B,C,D',
            ]);

            $answers = $request->input('answers');
            $user = Auth::user();

            // Valider les réponses
            if (!$this->quizService->validateQuizAnswers($answers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Réponses invalides'
                ], 400);
            }

            // Calculer le niveau
            $level = $this->quizService->calculateLevel($answers);
            $score = $this->quizService->getDetailedScore($answers);

            // Mettre à jour l'utilisateur
            $user->update([
                'level_n8n' => $level,
                'quiz_completed_at' => now()
            ]);

            // Attribuer les badges
            $this->quizService->assignStarterBadges($user, $level);

            return response()->json([
                'success' => true,
                'level' => $level,
                'score' => $score,
                'message' => 'Quiz terminé avec succès !'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la soumission du quiz'
            ], 500);
        }
    }
}
