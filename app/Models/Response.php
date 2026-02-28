<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Response extends Model
{
    protected $fillable = [
        'campaign_id',
        'participant_name',
        'participant_email',
        'total_score',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_score' => 'decimal:2',
            'completed_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function responseAnswers(): HasMany
    {
        return $this->hasMany(ResponseAnswer::class);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
