<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;

class AIQuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Generate questions using AI
     */
    public function generate(Request $request)
    {
        $user = Auth::user();

        // Check if user can use AI
        if (!$user->canUseAI()) {
            return response()->json([
                'error' => 'AI question generation is only available for Premium subscribers.'
            ], 403);
        }

        // Check if user has questions remaining
        if (!$user->hasAIQuestionsRemaining()) {
            return response()->json([
                'error' => 'You have reached your monthly limit of 200 AI-generated questions.'
            ], 429);
        }

        $request->validate([
            'category' => 'required|string|max:255',
            'difficulty' => 'required|in:easy,medium,hard',
            'count' => 'required|integer|min:1|max:10',
        ]);

        try {
            $prompt = $this->buildPrompt(
                $request->category,
                $request->difficulty,
                $request->count
            );

            $response = OpenAI::chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant that generates Jeopardy-style trivia questions and answers. Always respond with valid JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
            ]);

            $content = $response->choices[0]->message->content;
            $questions = json_decode($content, true);

            if (!$questions || !isset($questions['questions'])) {
                throw new \Exception('Invalid response format from AI');
            }

            // Increment AI usage
            $user->incrementAIQuestions();

            return response()->json([
                'questions' => $questions['questions'],
                'remaining' => $user->remaining_ai_questions,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate questions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build the prompt for AI question generation
     */
    private function buildPrompt(string $category, string $difficulty, int $count): string
    {
        $difficultyGuide = match($difficulty) {
            'easy' => 'suitable for elementary school students',
            'medium' => 'suitable for middle school students',
            'hard' => 'suitable for high school students or adults',
        };

        return <<<PROMPT
Generate {$count} Jeopardy-style trivia questions about "{$category}".
The questions should be {$difficulty} difficulty ({$difficultyGuide}).

Format your response as JSON with this structure:
{
    "questions": [
        {
            "question": "The question text (as a statement/clue, not a question)",
            "answer": "The answer (in question form, starting with 'What is' or 'Who is')",
            "points": 100
        }
    ]
}

Make the questions engaging and educational. The question should be a statement or clue, and the answer should be in the form of a question.
PROMPT;
    }

    /**
     * Get remaining AI questions for the user
     */
    public function remaining()
    {
        $user = Auth::user();

        if (!$user->canUseAI()) {
            return response()->json([
                'can_use' => false,
                'remaining' => 0,
            ]);
        }

        return response()->json([
            'can_use' => true,
            'remaining' => $user->remaining_ai_questions,
            'used' => $user->ai_questions_used,
            'reset_at' => $user->ai_questions_reset_at,
        ]);
    }
}