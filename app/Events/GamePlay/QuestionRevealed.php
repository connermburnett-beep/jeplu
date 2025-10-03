<?php

namespace App\Events\GamePlay;

use App\Models\GameSession;
use App\Models\Question;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionRevealed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public GameSession $session,
        public Question $question
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('game-session.' . $this->session->id);
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->id,
            'question' => [
                'id' => $this->question->id,
                'question' => $this->question->question,
                'points' => $this->question->points,
                'time_limit' => $this->question->time_limit,
                'category' => $this->question->category->name,
            ],
            'question_started_at' => now()->toIso8601String(),
        ];
    }
}