<?php

declare(strict_types=1);

namespace UniverseIT\KurdishSorani;

use InvalidArgumentException;

/**
 * Kurdish (Sorani) text utilities: normalization, digits, IQD formatting
 * and number-to-words. Framework-agnostic; usable without Laravel.
 */
final class Sorani
{
    private const ZWNJ = "\u{200C}";

    private const KURDISH_DIGITS = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    private const PERSIAN_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    private const ONES = ['', 'یەک', 'دوو', 'سێ', 'چوار', 'پێنج', 'شەش', 'حەوت', 'هەشت', 'نۆ'];

    private const TEENS = ['دە', 'یازدە', 'دوازدە', 'سیازدە', 'چواردە', 'پازدە', 'شازدە', 'حەڤدە', 'هەژدە', 'نۆزدە'];

    private const TENS = ['', '', 'بیست', 'سی', 'چل', 'پەنجا', 'شەست', 'حەفتا', 'هەشتا', 'نەوەد'];

    private const SCALES = ['', 'هەزار', 'ملیۆن', 'ملیار', 'ترلیۆن'];

    /**
     * Convert Arabic-keyboard characters to their correct Kurdish Unicode forms.
     *
     * ك→ک, ي/ى→ی, ة→ە, ه+ZWNJ→ە, and removes tatweel (ـ).
     * With $finalHeh = true, a word-final ه is also converted to ە.
     */
    public static function normalize(string $text, bool $finalHeh = false): string
    {
        $text = strtr($text, [
            "\u{0640}" => '',          // tatweel
            "\u{0643}" => "\u{06A9}",  // ك → ک
            "\u{064A}" => "\u{06CC}",  // ي → ی
            "\u{0649}" => "\u{06CC}",  // ى → ی
            "\u{0629}" => "\u{06D5}",  // ة → ە
            "\u{0647}" . self::ZWNJ => "\u{06D5}", // ه‌ → ە
        ]);

        if ($finalHeh) {
            $text = (string) preg_replace('/\x{0647}(?!\p{L})/u', "\u{06D5}", $text);
        }

        return $text;
    }

    /** Convert Latin (0-9) and Persian (۰-۹) digits to Kurdish/Arabic-Indic digits (٠-٩). */
    public static function toKurdishDigits(string $text): string
    {
        $map = [];
        foreach (self::KURDISH_DIGITS as $i => $kd) {
            $map[(string) $i] = $kd;
            $map[self::PERSIAN_DIGITS[$i]] = $kd;
        }

        return strtr($text, $map);
    }

    /** Convert Kurdish/Arabic-Indic (٠-٩) and Persian (۰-۹) digits to Latin digits (0-9). */
    public static function toLatinDigits(string $text): string
    {
        $map = [];
        foreach (self::KURDISH_DIGITS as $i => $kd) {
            $map[$kd] = (string) $i;
            $map[self::PERSIAN_DIGITS[$i]] = (string) $i;
        }

        return strtr($text, $map);
    }

    /**
     * Format an amount in Iraqi Dinar, e.g. 25000 → "٢٥٬٠٠٠ د.ع".
     */
    public static function formatIqd(int|float $amount, bool $kurdishDigits = true, string $suffix = 'د.ع'): string
    {
        $value = (int) round($amount);
        $formatted = number_format(abs($value), 0, '.', ',');

        if ($kurdishDigits) {
            $formatted = self::toKurdishDigits(str_replace(',', '٬', $formatted));
        }

        $sign = $value < 0 ? '-' : '';

        return $suffix === '' ? $sign . $formatted : $sign . $formatted . ' ' . $suffix;
    }

    /**
     * Spell out a non-negative integer in Sorani, e.g. 2026 → "دوو هەزار و بیست و شەش".
     */
    public static function numberToWords(int $number): string
    {
        if ($number < 0) {
            throw new InvalidArgumentException('Negative numbers are not supported.');
        }
        if ($number === 0) {
            return 'سفر';
        }

        $groups = [];
        while ($number > 0) {
            $groups[] = $number % 1000;
            $number = intdiv($number, 1000);
        }
        if (count($groups) > count(self::SCALES)) {
            throw new InvalidArgumentException('Number is too large.');
        }

        $parts = [];
        for ($i = count($groups) - 1; $i >= 0; $i--) {
            $g = $groups[$i];
            if ($g === 0) {
                continue;
            }
            if ($i === 0) {
                $parts[] = self::belowThousand($g);
            } elseif ($i === 1 && $g === 1) {
                $parts[] = self::SCALES[1];
            } else {
                $parts[] = self::belowThousand($g) . ' ' . self::SCALES[$i];
            }
        }

        return implode(' و ', $parts);
    }

    private static function belowThousand(int $n): string
    {
        $parts = [];
        $hundreds = intdiv($n, 100);
        $rest = $n % 100;

        if ($hundreds > 0) {
            $parts[] = ($hundreds === 1 ? '' : self::ONES[$hundreds]) . 'سەد';
        }
        if ($rest >= 10 && $rest < 20) {
            $parts[] = self::TEENS[$rest - 10];
        } elseif ($rest > 0) {
            $tens = intdiv($rest, 10);
            $ones = $rest % 10;
            if ($tens > 0) {
                $parts[] = self::TENS[$tens];
            }
            if ($ones > 0) {
                $parts[] = self::ONES[$ones];
            }
        }

        return implode(' و ', $parts);
    }
}
