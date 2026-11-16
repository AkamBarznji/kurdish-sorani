<?php

declare(strict_types=1);

namespace UniverseIT\KurdishSorani\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use UniverseIT\KurdishSorani\IraqiPhone as Phone;

/**
 * Usage: 'phone' => ['required', new IraqiPhone()]
 * or the string form: 'phone' => 'required|iraqi_phone'
 */
final class IraqiPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! Phone::isValid($value)) {
            $fail('kurdish-sorani::validation.iraqi_phone')->translate();
        }
    }
}
