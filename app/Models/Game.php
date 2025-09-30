<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->orderBy('order');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(GameSession::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(GameSettings::class);
    }

    // Check if user can create more games based on their tier
    public static function canCreateGame(User $user): bool
    {
        $gameCount = $user->games()->count();
        
        return match($user->subscription_tier) {
            'free' => $gameCount < 2,
            'basic' => $gameCount < 10,
            'premium' => true, // unlimited
            default => false,
        };
    }

    // Get max categories allowed for this game's user
    public function getMaxCategoriesAttribute(): int
    {
        return match($this->user->subscription_tier) {
            'free' => 5,
            'basic' => 5,
            'premium' => 10,
            default => 5,
        };
    }

    // Get max questions per category allowed
    public function getMaxQuestionsPerCategoryAttribute(): int
    {
        return match($this->user->subscription_tier) {
            'free' => 3,
            'basic' => 5,
            'premium' => 10,
            default => 3,
        };
    }

    // Get max teams allowed
    public function getMaxTeamsAttribute(): int
    {
        return match($this->user->subscription_tier) {
            'free' => 2,
            'basic' => 5,
            'premium' => 10,
            default => 2,
        };
    }
}
