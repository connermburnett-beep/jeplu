<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameSettings extends Model
{
    protected $fillable = [
        'game_id',
        'background_image',
        'theme',
        'background_music',
        'buzzer_sound',
        'buzzer_timer',
        'custom_colors',
    ];

    protected $casts = [
        'buzzer_timer' => 'integer',
        'custom_colors' => 'array',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    // Check if user can customize settings based on tier
    public function canCustomizeBackground(): bool
    {
        $tier = $this->game->user->subscription_tier;
        return in_array($tier, ['basic', 'premium']);
    }

    public function canCustomizeTheme(): bool
    {
        return $this->game->user->subscription_tier === 'premium';
    }

    public function canCustomizeMusic(): bool
    {
        return $this->game->user->subscription_tier === 'premium';
    }

    public function canCustomizeBuzzerSound(): bool
    {
        return $this->game->user->subscription_tier === 'premium';
    }

    public function canCustomizeTimer(): bool
    {
        $tier = $this->game->user->subscription_tier;
        return in_array($tier, ['basic', 'premium']);
    }
}
