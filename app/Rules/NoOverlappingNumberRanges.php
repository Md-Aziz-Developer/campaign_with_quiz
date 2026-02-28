<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoOverlappingNumberRanges implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value) || count($value) < 2) {
            return;
        }

        $ranges = [];
        foreach ($value as $rule) {
            $exact = isset($rule['exact_value']) && $rule['exact_value'] !== '' && $rule['exact_value'] !== null
                ? (float) $rule['exact_value'] : null;
            $min = isset($rule['min_value']) && $rule['min_value'] !== '' && $rule['min_value'] !== null
                ? (float) $rule['min_value'] : null;
            $max = isset($rule['max_value']) && $rule['max_value'] !== '' && $rule['max_value'] !== null
                ? (float) $rule['max_value'] : null;
            $ranges[] = ['exact' => $exact, 'min' => $min, 'max' => $max];
        }

        for ($i = 0; $i < count($ranges); $i++) {
            for ($j = $i + 1; $j < count($ranges); $j++) {
                $a = $ranges[$i];
                $b = $ranges[$j];
                if ($this->overlap($a, $b)) {
                    $fail('Number rules contain overlapping ranges.');
                    return;
                }
            }
        }
    }

    private function overlap(array $a, array $b): bool
    {
        $aExact = $a['exact'];
        $aMin = $a['min'];
        $aMax = $a['max'];
        $bExact = $b['exact'];
        $bMin = $b['min'];
        $bMax = $b['max'];

        if ($aExact !== null && $bExact !== null) {
            return $aExact === $bExact;
        }
        if ($aExact !== null) {
            return $bMin !== null && $bMax !== null && $aExact >= $bMin && $aExact <= $bMax;
        }
        if ($bExact !== null) {
            return $aMin !== null && $aMax !== null && $bExact >= $aMin && $bExact <= $aMax;
        }
        if ($aMin !== null && $aMax !== null && $bMin !== null && $bMax !== null) {
            return $aMin <= $bMax && $bMin <= $aMax;
        }
        return false;
    }
}
