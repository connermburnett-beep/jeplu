<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Team extends Model
{
    protected $fillable = [
        'game_session_id',
        'name',
        'color',
        'score',
        'buzzer_code',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            if (empty($team->buzzer_code)) {
                $team->buzzer_code = strtoupper(Str::random(6));
            }
        });
    }

    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    public function buzzerEvents(): HasMany
    {
        return $this->hasMany(BuzzerEvent::class);
    }

    public function addPoints(int $points): void
    {
        $this->increment('score', $points);
    }

    public function subtractPoints(int $points): void
    {
        $this->decrement('score', $points);
    }
}
