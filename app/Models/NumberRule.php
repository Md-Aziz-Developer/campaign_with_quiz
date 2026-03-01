<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NumberRule extends Model
{
    protected $fillable = [
        'question_id',
        'exact_value',
        'min_value',
        'max_value',
        'score',
    ];

    protected function casts(): array
    {
        return [
            'exact_value' => 'decimal:2',
            'min_value' => 'decimal:2',
            'max_value' => 'decimal:2',
            'score' => 'decimal:2',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
