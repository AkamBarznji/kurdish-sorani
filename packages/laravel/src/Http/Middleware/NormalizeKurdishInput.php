<?php

declare(strict_types=1);

namespace UniverseIT\KurdishSorani\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;
use UniverseIT\KurdishSorani\Sorani;

/**
 * Normalizes Kurdish text in every request input (ك→ک, ي→ی, ة→ە ...),
 * so searching and duplicate checks work no matter which keyboard the user has.
 */
class NormalizeKurdishInput extends TransformsRequest
{
    /** @var array<int, string> */
    protected array $except = [
        'password',
        'password_confirmation',
        'current_password',
    ];

    protected function transform($key, $value)
    {
        if (in_array($key, $this->except, true) || ! is_string($value)) {
            return $value;
        }

        return Sorani::normalize($value);
    }
}
