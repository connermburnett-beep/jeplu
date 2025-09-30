<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use Billable;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'subscription_tier',
        'ai_questions_used',
        'ai_questions_reset_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ai_questions_reset_at' => 'datetime',
        ];
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    public function gameSessions(): HasMany
    {
        return $this->hasMany(GameSession::class);
    }

    // Check if user can use AI features
    public function canUseAI(): bool
    {
        return $this->subscription_tier === 'premium';
    }

    // Check if user has AI questions remaining this month
    public function hasAIQuestionsRemaining(): bool
    {
        if (!$this->canUseAI()) {
            return false;
        }

        // Reset counter if it's a new month
        if ($this->ai_questions_reset_at === null || $this->ai_questions_reset_at->isPast()) {
            $this->update([
                'ai_questions_used' => 0,
                'ai_questions_reset_at' => now()->addMonth(),
            ]);
        }

        return $this->ai_questions_used < 200;
    }

    // Increment AI questions used
    public function incrementAIQuestions(): void
    {
        $this->increment('ai_questions_used');
    }

    // Get remaining AI questions
    public function getRemainingAIQuestionsAttribute(): int
    {
        if (!$this->canUseAI()) {
            return 0;
        }

        return max(0, 200 - $this->ai_questions_used);
    }

    // Check if user is on free tier
    public function isFreeTier(): bool
    {
        return $this->subscription_tier === 'free';
    }

    // Check if user is on basic tier
    public function isBasicTier(): bool
    {
        return $this->subscription_tier === 'basic';
    }

    // Check if user is on premium tier
    public function isPremiumTier(): bool
    {
        return $this->subscription_tier === 'premium';
    }
}
