<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'category_id',
        'question',
        'answer',
        'points',
        'time_limit',
        'order',
        'is_answered',
    ];

    protected $casts = [
        'points' => 'integer',
        'time_limit' => 'integer',
        'order' => 'integer',
        'is_answered' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function buzzerEvents(): HasMany
    {
        return $this->hasMany(BuzzerEvent::class);
    }
}
