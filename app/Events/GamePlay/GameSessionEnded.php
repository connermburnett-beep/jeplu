<?php

namespace App\Events\GamePlay;

use App\Models\GameSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameSessionEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public GameSession $session
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('game-session.' . $this->session->id);
    }

    public function broadcastWith(): array
    {
        $teams = $this->session->teams()
            ->orderBy('score', 'desc')
            ->get()
            ->map(fn($team) => [
                'id' => $team->id,
                'name' => $team->name,
                'score' => $team->score,
                'color' => $team->color,
            ]);

        return [
            'session_id' => $this->session->id,
            'status' => 'completed',
            'teams' => $teams,
            'completed_at' => $this->session->completed_at?->toIso8601String(),
        ];
    }
}