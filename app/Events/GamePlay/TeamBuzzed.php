<?php

namespace App\Events\GamePlay;

use App\Models\GameSession;
use App\Models\Team;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamBuzzed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public GameSession $session,
        public Team $team,
        public int $responseTimeMs
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('game-session.' . $this->session->id);
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->id,
            'team' => [
                'id' => $this->team->id,
                'name' => $this->team->name,
                'color' => $this->team->color,
            ],
            'response_time_ms' => $this->responseTimeMs,
            'buzzed_at' => now()->toIso8601String(),
        ];
    }
}