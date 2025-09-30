<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuzzerEvent extends Model
{
    protected $fillable = [
        'game_session_id',
        'team_id',
        'question_id',
        'buzzed_at',
        'response_time_ms',
    ];

    protected $casts = [
        'buzzed_at' => 'datetime',
        'response_time_ms' => 'integer',
    ];

    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
