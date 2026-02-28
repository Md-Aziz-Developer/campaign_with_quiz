<?php

namespace App\Services;

use App\Models\Response;

class ScoringService
{
    public function scoreResponse(Response $response): float
    {
        $response->load([
            'responseAnswers.question.options',
            'responseAnswers.question.textKeyword',
            'responseAnswers.question.numberRules',
        ]);

        $total = 0.0;
        foreach ($response->responseAnswers as $answer) {
            $total += $this->scoreAnswer($answer);
        }
        $total = round($total, 2);
        $response->update(['total_score' => $total]);
        return $total;
    }

    private function scoreAnswer($responseAnswer): float
    {
        $question = $responseAnswer->question;
        $score = 0.0;

        switch ($question->type) {
            case 'mcq_single':
                $optionIds = $responseAnswer->selected_option_ids;
                if (is_array($optionIds) && count($optionIds) > 0) {
                    $optionId = $optionIds[0];
                    $option = $question->options->firstWhere('id', $optionId);
                    if ($option) {
                        $score = (float) $option->score;
                    }
                }
                break;
            case 'mcq_multi':
                $optionIds = $responseAnswer->selected_option_ids ?? [];
                foreach ((array) $optionIds as $optionId) {
                    $option = $question->options->firstWhere('id', $optionId);
                    if ($option) {
                        $score += (float) $option->score;
                    }
                }
                break;
            case 'text':
                $text = mb_strtolower((string) $responseAnswer->answer_text);
                $keyword = $question->textKeyword;
                if ($keyword && is_array($keyword->rules)) {
                    foreach ($keyword->rules as $rule) {
                        $keywordStr = $rule['keyword'] ?? '';
                        $keywordLower = mb_strtolower($keywordStr);
                        if ($keywordLower !== '' && mb_strpos($text, $keywordLower) !== false) {
                            $score += (float) ($rule['score'] ?? 0);
                        }
                    }
                }
                break;
            case 'number':
                $num = $responseAnswer->answer_number;
                if ($num === null && $responseAnswer->answer_text !== null) {
                    $num = is_numeric(trim($responseAnswer->answer_text)) ? (float) trim($responseAnswer->answer_text) : null;
                }
                if ($num !== null) {
                    $num = (float) $num;
                    foreach ($question->numberRules as $rule) {
                        if ($rule->exact_value !== null) {
                            if ((float) $rule->exact_value === $num) {
                                $score = (float) $rule->score;
                                break;
                            }
                        } elseif ($rule->min_value !== null && $rule->max_value !== null) {
                            if ($num >= (float) $rule->min_value && $num <= (float) $rule->max_value) {
                                $score = (float) $rule->score;
                                break;
                            }
                        }
                    }
                }
                break;
        }

        $score = round($score, 2);
        $responseAnswer->update(['score' => $score]);
        return $score;
    }
}
