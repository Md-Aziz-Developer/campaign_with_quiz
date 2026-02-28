<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TextKeyword extends Model
{
    protected $fillable = [
        'question_id',
        'rules',
    ];

    protected function casts(): array
    {
        return [
            'rules' => 'array', // [["keyword" => "fine", "score" => 0.5], ...]
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
