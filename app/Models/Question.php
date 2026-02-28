<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Question extends Model
{
    public const TYPE_MCQ_SINGLE = 'mcq_single';
    public const TYPE_MCQ_MULTI = 'mcq_multi';
    public const TYPE_TEXT = 'text';
    public const TYPE_NUMBER = 'number';

    protected $fillable = [
        'campaign_id',
        'question_text',
        'type',
        'is_mandatory',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_mandatory' => 'boolean',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class)->orderBy('order');
    }

    public function textKeyword(): HasOne
    {
        return $this->hasOne(TextKeyword::class);
    }

    public function numberRules(): HasMany
    {
        return $this->hasMany(NumberRule::class);
    }

    public function responseAnswers(): HasMany
    {
        return $this->hasMany(ResponseAnswer::class, 'question_id');
    }
}
