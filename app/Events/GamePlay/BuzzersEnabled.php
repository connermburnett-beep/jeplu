<?php

namespace App\Events\GamePlay;

use App\Models\GameSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BuzzersEnabled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public GameSession $session,
        public bool $enabled
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('game-session.' . $this->session->id);
    }

    public function broadcastWith(): array
    {
        return [
            'session_id' => $this->session->id,
            'buzzers_enabled' => $this->enabled,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}