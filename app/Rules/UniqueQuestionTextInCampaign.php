<?php

namespace App\Rules;

use App\Models\Campaign;
use App\Models\Question;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueQuestionTextInCampaign implements ValidationRule
{
    public function __construct(
        protected Campaign $campaign,
        protected ?Question $excludeQuestion = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $normalized = $this->normalize($value);
        if ($normalized === '') {
            return;
        }

        $query = Question::where('campaign_id', $this->campaign->id);
        if ($this->excludeQuestion) {
            $query->where('id', '!=', $this->excludeQuestion->id);
        }

        foreach ($query->get() as $question) {
            if ($this->normalize($question->question_text) === $normalized) {
                $fail('Duplicate question detected. This question text already exists in this campaign.');
                return;
            }
        }
    }

    protected function normalize(string $text): string
    {
        $text = strip_tags($text);
        $text = trim(preg_replace('/\s+/', ' ', $text));
        return mb_strtolower($text);
    }
}
