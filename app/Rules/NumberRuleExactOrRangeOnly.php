<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NumberRuleExactOrRangeOnly implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            return;
        }

        foreach ($value as $index => $rule) {
            $exact = isset($rule['exact_value']) && $rule['exact_value'] !== '' && $rule['exact_value'] !== null;
            $min = isset($rule['min_value']) && $rule['min_value'] !== '' && $rule['min_value'] !== null;
            $max = isset($rule['max_value']) && $rule['max_value'] !== '' && $rule['max_value'] !== null;

            $hasExact = $exact;
            $hasRange = $min && $max;

            if ($hasExact && $hasRange) {
                $fail('Each number rule must be either an exact value or a range (min–max), not both. Rule ' . ($index + 1) . ' has both.');
                return;
            }
            if (! $hasExact && ! $hasRange) {
                if ($exact || $min || $max) {
                    $fail('Number rule ' . ($index + 1) . ': use exact value only, or both min and max for a range.');
                    return;
                }
            }
        }
    }
}
