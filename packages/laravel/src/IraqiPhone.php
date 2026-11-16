<?php

declare(strict_types=1);

namespace UniverseIT\KurdishSorani;

/**
 * An Iraqi mobile number, parsed from almost any user input:
 * 0750 123 4567, +964 750 123 4567, 00964-750-1234567, ٠٧٥٠١٢٣٤٥٦٧ ...
 */
final class IraqiPhone
{
    private const CARRIERS = [
        '75' => 'Korek',
        '77' => 'Asiacell',
        '78' => 'Zain',
        '79' => 'Zain',
    ];

    private function __construct(private readonly string $digits)
    {
        // $digits is the 10-digit subscriber number without leading 0, e.g. "7501234567"
    }

    /** Parse input; returns null when it is not a valid Iraqi mobile number. */
    public static function parse(?string $input): ?self
    {
        if ($input === null) {
            return null;
        }

        $s = Sorani::toLatinDigits(trim($input));
        $s = (string) preg_replace('/[\s\-().]/u', '', $s);

        if (str_starts_with($s, '+964')) {
            $s = substr($s, 4);
        } elseif (str_starts_with($s, '00964')) {
            $s = substr($s, 5);
        } elseif (str_starts_with($s, '964') && strlen($s) === 13) {
            $s = substr($s, 3);
        }

        if (str_starts_with($s, '0')) {
            $s = substr($s, 1);
        }

        if (preg_match('/^7[3-9]\d{8}$/', $s) !== 1) {
            return null;
        }

        return new self($s);
    }

    public static function isValid(?string $input): bool
    {
        return self::parse($input) !== null;
    }

    /** E.164 format, e.g. +9647501234567 — store this in your database. */
    public function e164(): string
    {
        return '+964' . $this->digits;
    }

    /** Local display format, e.g. 0750 123 4567. */
    public function national(): string
    {
        return '0' . substr($this->digits, 0, 3) . ' ' . substr($this->digits, 3, 3) . ' ' . substr($this->digits, 6);
    }

    /** Korek, Asiacell, Zain, or null when unknown. */
    public function carrier(): ?string
    {
        return self::CARRIERS[substr($this->digits, 0, 2)] ?? null;
    }

    public function __toString(): string
    {
        return $this->e164();
    }
}
