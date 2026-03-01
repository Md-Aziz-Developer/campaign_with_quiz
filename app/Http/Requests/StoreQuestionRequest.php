<?php

namespace App\Http\Requests;

use App\Models\Question;
use App\Rules\NoOverlappingNumberRanges;
use App\Rules\NumberRuleExactOrRangeOnly;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $rules = [
            'question_text' => ['required', 'string'],
            'type' => ['required', 'in:mcq_single,mcq_multi,text,number'],
            'is_mandatory' => ['boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];

        if (in_array($this->input('type'), [Question::TYPE_MCQ_SINGLE, Question::TYPE_MCQ_MULTI])) {
            $rules['options'] = ['required', 'array', 'min:1'];
            $rules['options.*.option_text'] = ['required', 'string'];
            $rules['options.*.score'] = ['required', 'numeric'];
            $rules['options.*.is_correct'] = ['nullable', 'boolean'];
        }

        if ($this->input('type') === Question::TYPE_TEXT) {
            $rules['keyword_rules'] = ['required', 'array', 'min:1'];
            $rules['keyword_rules.*.keyword'] = ['required', 'string'];
            $rules['keyword_rules.*.score'] = ['required', 'numeric'];
        }

        if ($this->input('type') === Question::TYPE_NUMBER) {
            $rules['number_rules'] = ['required', 'array', 'min:1', new NumberRuleExactOrRangeOnly, new NoOverlappingNumberRanges];
            $rules['number_rules.*.score'] = ['required', 'numeric'];
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_mandatory' => $this->boolean('is_mandatory'),
            'options' => $this->decodeIfString('options'),
            'keyword_rules' => $this->decodeIfString('keyword_rules'),
            'number_rules' => $this->decodeIfString('number_rules'),
        ]);
    }

    private function decodeIfString(string $key): array|string|null
    {
        $v = $this->input($key);
        if (is_string($v)) {
            $dec = json_decode($v, true);
            return is_array($dec) ? $dec : [];
        }
        return $v;
    }
}
