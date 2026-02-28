<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponseAnswer extends Model
{
    protected $fillable = [
        'response_id',
        'question_id',
        'answer_text',
        'answer_number',
        'selected_option_ids',
        'score',
    ];

    protected function casts(): array
    {
        return [
            'answer_number' => 'decimal:4',
            'selected_option_ids' => 'array',
            'score' => 'decimal:2',
        ];
    }

    public function response(): BelongsTo
    {
        return $this->belongsTo(Response::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
