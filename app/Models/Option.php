<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Option extends Model
{
    protected $fillable = [
        'question_id',
        'option_text',
        'score',
        'is_correct',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'is_correct' => 'boolean',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
