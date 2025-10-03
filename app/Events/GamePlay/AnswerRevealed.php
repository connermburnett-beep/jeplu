<?php

namespace App\Events\GamePlay;

use App\Models\GameSession;
use App\Models\Question;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnswerRevealed implements ShouldBroadcast
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
            'question_id' => $this->question->id,
            'answer' => $this->question->answer,
            'revealed_at' => now()->toIso8601String(),
        ];
    }
}